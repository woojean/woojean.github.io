---
layout: post
title:  "《RabbitMQ Tutorials》读书笔记"
date: 2017-05-02 00:00:01
categories: 编程技术
tags: RabbitMQ
excerpt: "RabbitMQ官方入门教程（PHP版）阅读笔记"
---

* content
{:toc}

[下载代码:https://github.com/woojean/demos/tree/master/rabbitmq-tutorials](https://github.com/woojean/demos/tree/master/rabbitmq-tutorials)


# 准备
## 安装rabbitmq server
略。

## 安装php-amqplib包
```
cd /Users/wujian/projects/demo/rabbitmq
composer require php-amqplib/php-amqplib
```



# 例：Hello World !

一个生产者发送一个消息，一个消费者收到该消息并打印。

## 生产者：send.php

```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$queueName = 'queue_hello';
$connection = new AMQPStreamConnection('localhost',5672,'guest','guest');  // vhost

// AMQP 0-9-1 connections are multiplexed with channels that can be thought of as “lightweight connections that share a single TCP connection”.
$channel = $connection->channel();

// declare a queue for us to send to
$channel->queue_declare($queueName, FALSE, FALSE, FALSE, FALSE);

// publish a message to the queue
$msg = new AMQPMessage('Hello World!');
$channel->basic_publish($msg,'', $queueName);

echo " [x] Sent 'Hello World!'\n";

// close the channel and the connection
$channel->close();
$connection->close();
```

## 消费者：receive.php
```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$queueName = 'queue_hello';
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// Note that we declare the queue here, as well. Because we might start the consumer before the publisher, we want to make sure the queue exists before we try to consume messages from it.
$channel->queue_declare($queueName, FALSE, FALSE, FALSE, FALSE);
echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

// define a PHP callable that will receive the messages sent by the server
$callback = function($msg){
    echo " [x] Received ", $msg->body, "\n";
};
$channel->basic_consume($queueName,'',false,true,false,false,$callback);

/*
basic_consume(...)的第2个参数是$consumer_tag，在该方法中会对当前$channel对象执行：
$this->callbacks[$consumer_tag] = $callback;
所以，当$channel对象的callbacks列表不为空时，表明存在监听该channel的消费者
*/

// keep it running to listen for messages and print them out .
while(count($channel->callbacks)){
    $channel->wait();
}
```

## 测试
先执行消费者，再执行生产者：
```
php receive.php
php send.php
```




# 例：Work Queues
轮询地分发消息。<u>启动2个worker，之后执行多次new_task，消息将会在2个worker之间交替进行处理。</u>

工作队列的方式非常便于并发扩展（通过添加更多的队列）。RabbitMQ会按序发送每一个消息至下一个消费者，所以从平均来讲，每个消费者将会收到相同数量的消息。

消息没有超时的概念，所以处理消息的时间可以很长。RabbitMQ通过判断连接是否断开来判断消费者是否崩溃。

消息确认（Message acknowledgments）默认是关闭的。

为了确保消息不会丢失，要将队列和消息都设置为持久化（durable）。

Marking messages as persistent doesn't fully guarantee that a message won't be lost. Although it tells RabbitMQ to save the message to disk, **there is still a short time window when RabbitMQ has accepted a message and hasn't saved it yet**. Also, RabbitMQ doesn't do fsync(2) for every message -- it may be just saved to cache and not really written to the disk. The persistence guarantees aren't strong, but it's more than enough for our simple task queue. If you need a stronger guarantee then you can use publisher confirms.

RabbitMQ在收到消息时就直接分发，而不会去检查当前某个消费者未确认的消息的数量，它只是盲目地分发下一个消息去往下一个消费者。


## 添加新任务：new_task.php
```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// 第3个参数为true，代表队列的durable为true，此处要与worker中的定义一致
$channel->queue_declare('task_queue',false,true,false,false);

// 拼装消息参数
$data = "Hello World!";
$msg = new AMQPMessage($data,[
    'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT  // 代表持久化消息
]);

// 发布消息
// exchange为空字符串，即使用default exchange，那么队列名就是routing key
$channel->basic_publish($msg,'','task_queue');
echo " [x] Sent ", $data, "\n";

$channel->close();
$connection->close();
```

## 任务处理器：worker.php
```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// 第3个参数为true，代表队列的durable为true
$channel->queue_declare('task_queue', FALSE, TRUE, FALSE, FALSE);
echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

// 定义任务处理函数
$callback = function ($msg){
    echo " [x] Received ", $msg->body, "\n";  // 总之，消息体的形式肯定是一个字符串（byte string）
    sleep(substr_count($msg->body, '.'));  // 模拟任务执行时间
    echo " [x] Done", "\n";

    // message acknowledgment
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};

// prefetch_count = 1,This tells RabbitMQ not to give more than one message to a worker at a time.
// in other words, don't dispatch a new message to a worker until it has processed and acknowledged the previous one. Instead, it will dispatch it to the next worker that is not still busy.
$channel->basic_qos(null,1,null);
$channel->basic_consume('task_queue', '', FALSE, FALSE, FALSE, FALSE, $callback);

while (count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();
```






# 例：Publish/Subscribe
分发一个消息给多个消费者。

RabbitMQ的核心思想是：生产者从不直接将消息发送给队列，而是将消息发送给exchange，exchange在接收到消息后需要知道如何进行分发：分发给特定的某个队列、分发给多个队列、丢弃消息等，取决于exchange的类型：direct, topic, headers , fanout。

如果使用空字符串定义队列，将会创一个非持久化的队列，该队列的名字自动生成（以amq.开头）。

```
list($queue_name, ,) = $channel->queue_declare(""); // amq.gen-JzTY20BRgKO-HjmUJj0wLg
```
exchange和队列之间的关系称为binding.


## log接收者
每次运行动态生成一个queue，并绑定到相应的exchange上
```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// 定义fanout exchange
$channel->exchange_declare('logs','fanout', FALSE, FALSE, FALSE);

// 生成一个临时queue，并保存queue的名字
list($queue_name,,) = $channel->queue_declare('', FALSE, FALSE,TRUE, FALSE);

// 将queue绑定到exchange，因为该exchange是一个fanout类型的，所以绑定的每个queue将被广播
$channel->queue_bind($queue_name,'logs');  // queue -> exchange
echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";

$callback = function ($msg) {
    echo ' [x] ', $msg->body, "\n";
};

$channel->basic_consume($queue_name,'',FALSE,true,false,false,$callback);

while (count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();
```

## log发送者
```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// 定义一个fanout类型的exchange
$channel->exchange_declare('logs','fanout',false,FALSE,FALSE);

// 拼装消息
$data = "info: Hello World!";
$msg = new AMQPMessage($data);

// 将消息发布到exchange
$channel->basic_publish($msg,'logs');

echo " [x] Sent ", $data, "\n";
$channel->close();
$connection->close();
```





# 例：Routing
只处理全部消息的子集。比如在能够打印所有日志的同时，将错误日志保存到磁盘。

多个queue可以使用同一个routing key绑定到同一个exchange。
同一个queue和同一个exchange之间也可以绑定多个不同的key。

## 发出日志：emit_log_direct.php
```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// 定义一个direct类型的exchange
$channel->exchange_declare('direct_logs', 'direct', FALSE, FALSE, FALSE);

// 获取用户输入
$severity = isset($argv[1]) && !empty($argv[1]) ? $argv[1] : 'info';
$data = implode(' ', array_slice($argv, 2));
if (empty($data)) {
    $data = "Hello World!";
}
$msg = new AMQPMessage($data);

// 发出信息，其中$severity为route key
// 即发布一个routing key为$severity的消息到direct_logs这个exchange中
$channel->basic_publish($msg, 'direct_logs', $severity);

echo " [x] Sent ", $severity, ':', $data, " \n";

$channel->close();
$connection->close();
```

## 处理日志：receive_logs_direct.php
```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// 定义一个direct类型的exchange
$channel->exchange_declare('direct_logs', 'direct', FALSE, FALSE, FALSE);

// 动态生成一个queue
list($queue_name, ,) = $channel->queue_declare("", FALSE, FALSE, TRUE, FALSE);

$severities = array_slice($argv, 1);
if (empty($severities)) {
    file_put_contents('php://stderr', "Usage: $argv[0] [info] [warning] [error]\n");
    exit(1);
}

// 每一种级别的日志都进行一次绑定
foreach ($severities as $severity) {
    // 将$queue_name这个queue使用routing key $severity绑定到exchange direct_logs
    $channel->queue_bind($queue_name, 'direct_logs', $severity);
}

echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";

$callback = function ($msg) {
    echo ' [x] ', $msg->delivery_info['routing_key'], ':', $msg->body, "\n";
};

$channel->basic_consume($queue_name, '', FALSE, TRUE, FALSE, FALSE, $callback);

while (count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();
```

## 测试
启2个receiver
```
php receive_logs_direct.php error
php receive_logs_direct.php info error
```
发出一个error级别的log：
```
php emit_log_direct.php error
```

<u>如果发布error消息，2个receiver都会收到，如果发布info消息，则只有第2个receiver会收到消息。</u>



# 例：Topics
direct exchange的不足在于它不能基于多个纬度来分发消息。比如要同时基于日志的错误级别和来源进行处理。

发送往topic exchange的消息需要带有一个由.号分隔的多个单词组成的binding key，且有2个字符比较特殊：

* \* (star) 代表1个单词；
* \# (hash) 代表0或多个单词；

比如：`<speed>.<colour>.<species>`，声明如下的绑定：
```
Q1: *.orange.*  // all the orange animals
Q2: *.*.rabbit  // everything about rabbits
Q3: lazy.#      // everything about lazy animals
```
"quick.brown.fox"不能匹配任何绑定，所以该消息将被丢弃。
"orange"或"quick.orange.male.rabbit"也不能匹配任何绑定。
"lazy.orange.male.rabbit", 将匹配最后一个绑定。

如果仅使用"#"作为binding key，那么将会匹配所有接收到的消息而忽略routing key，就像fanout exchange一样。对于topic exchange，如果没有使用 "*" 和"#"，那么其行为将和direct exchange一样。

样例代码几乎和Routing部分的一样。


## 发出日志：emit_log_direct.php
```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->exchange_declare('topic_logs', 'topic', FALSE, FALSE, FALSE);

$routing_key = isset($argv[1]) && !empty($argv[1]) ? $argv[1] : 'anonymous.info';
$data = implode(' ', array_slice($argv, 2));
if (empty($data)) $data = "Hello World!";

$msg = new AMQPMessage($data);

$channel->basic_publish($msg, 'topic_logs', $routing_key);

echo " [x] Sent ", $routing_key, ':', $data, " \n";

$channel->close();
$connection->close();
```

## 处理日志：receive_logs_direct.php
```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->exchange_declare('topic_logs', 'topic', FALSE, FALSE, FALSE);

list($queue_name, ,) = $channel->queue_declare("", FALSE, FALSE, TRUE, FALSE);

$binding_keys = array_slice($argv, 1);
if (empty($binding_keys)) {
    file_put_contents('php://stderr', "Usage: $argv[0] [binding_key]\n");
    exit(1);
}

foreach ($binding_keys as $binding_key) {
    $channel->queue_bind($queue_name, 'topic_logs', $binding_key);
}

echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";

$callback = function ($msg) {
    echo ' [x] ', $msg->delivery_info['routing_key'], ':', $msg->body, "\n";
};

$channel->basic_consume($queue_name, '', FALSE, TRUE, FALSE, FALSE, $callback);

while (count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();
```

## 测试
```
php receive_logs_topic.php "#"   // To receive all the logs
php receive_logs_topic.php "kern.*" // To receive all logs from the facility "kern"
php receive_logs_topic.php "kern.*" "*.critical"  // create multiple bindings
php emit_log_topic.php "kern.critical" "A critical kernel error" // emit a log with a routing key "kern.critical" type
```






# 例：Remote procedure call (RPC)

基于回调方式的RPC调用。

涉及的Message属性：
* delivery_mode: 指定消息为是否为持久化的；
* content_type: 比如application/json；
* reply_to: 用于指定回调队列；
* correlation_id: 用于关联RPC Response和请求；

## rpc_server.php 
```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('rpc_queue', FALSE, FALSE, FALSE, FALSE);

function fib($n)
{
    if ($n == 0)
        return 0;
    if ($n == 1)
        return 1;
    return fib($n - 1) + fib($n - 2);
}

echo " [x] Awaiting RPC requests\n";
$callback = function ($req) {
    $n = intval($req->body);
    echo " [.] fib(", $n, ")\n";

    $msg = new AMQPMessage(
        (string)fib($n),
        ['correlation_id' => $req->get('correlation_id')]
    );

    $req->delivery_info['channel']->basic_publish(
        $msg, '', $req->get('reply_to'));
    $req->delivery_info['channel']->basic_ack(
        $req->delivery_info['delivery_tag']);
};

$channel->basic_qos(NULL, 1, NULL);
$channel->basic_consume('rpc_queue', '', FALSE, FALSE, FALSE, FALSE, $callback);

while (count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();
```

## rpc_client.php
```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class FibonacciRpcClient
{
    private $connection;
    private $channel;
    private $callback_queue;
    private $response;
    private $corr_id;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            'localhost', 5672, 'guest', 'guest');
        $this->channel = $this->connection->channel();
        list($this->callback_queue, ,) = $this->channel->queue_declare(
            "", FALSE, FALSE, TRUE, FALSE);
        $this->channel->basic_consume(
            $this->callback_queue, '', FALSE, FALSE, FALSE, FALSE,
            [$this, 'on_response']);
    }

    public function on_response($rep)
    {
        if ($rep->get('correlation_id') == $this->corr_id) {
            $this->response = $rep->body;
        }

        /*
         If we see an unknown correlation_id value, we may safely discard the message - it doesn't belong to our requests.
        You may ask, why should we ignore unknown messages in the callback queue, rather than failing with an error?
         It's due to a possibility of a race condition on the server side.
        Although unlikely, it is possible that the RPC server will die just after sending us the answer, but before sending an acknowledgment message for the request.
        If that happens, the restarted RPC server will process the request again.
        That's why on the client we must handle the duplicate responses gracefully, and the RPC should ideally be idempotent.*/
    }

    public function call($n)
    {
        $this->response = NULL;
        $this->corr_id = uniqid();  // set it to a unique value for every request

        $msg = new AMQPMessage(
            (string)$n,
            ['correlation_id' => $this->corr_id,
             'reply_to'       => $this->callback_queue]
        );
        $this->channel->basic_publish($msg, '', 'rpc_queue');
        while (!$this->response) {
            $this->channel->wait();
        }
        return intval($this->response);
    }
}

;

$fibonacci_rpc = new FibonacciRpcClient();
$response = $fibonacci_rpc->call(30);
echo " [.] Got ", $response, "\n";
```

