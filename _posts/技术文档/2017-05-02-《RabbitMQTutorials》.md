---
layout: post
title:  "《RabbitMQ Tutorials》"
date: 2017-05-02 00:00:01
categories: 技术文档
tags: RabbitMQ PHP
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

**a producer that sends a single message, and a consumer that receives messages and prints them out.**

## 生产者：send.php
```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$queueName = 'queue_hello';
$connection = new AMQPStreamConnection('localhost',5672,'guest','guest');

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
The main idea behind Work Queues is to **avoid doing a resource-intensive task immediately and having to wait for it to complete**. Instead schedule the task to be done later. We **encapsulate a task as a message and send it to a queue. A worker process (or many workers) running in the background will pop the tasks and eventually execute the job.** 

One of the advantages of using a Task Queue is the ability to easily parallelise work. If we are building up a backlog of work, we can just add more workers and that way, **scale easily**.By default, RabbitMQ will send each message to the next consumer, in sequence. On average every consumer will get the same number of messages(Round-robin dispatching). 

An ack(nowledgement) is sent back from the consumer to tell RabbitMQ that a particular message has been received, processed and that RabbitMQ is free to delete it.If a consumer dies (its channel is closed, connection is closed, or TCP connection is lost) without sending an ack, RabbitMQ will understand that a message wasn't processed fully and will re-queue it. If there are other consumers online at the same time, it will then quickly redeliver it to another consumer. 

**There aren't any message timeouts**; RabbitMQ will redeliver the message when the consumer dies. It's fine even if processing a message takes a very, very long time.

Message acknowledgments are turned off by default. 

When RabbitMQ quits or crashes it will forget the queues and messages unless you tell it not to. Two things are required to make sure that messages aren't lost: we need to **mark both the queue and messages as durable**.

Marking messages as persistent doesn't fully guarantee that a message won't be lost. Although it tells RabbitMQ to save the message to disk, **there is still a short time window when RabbitMQ has accepted a message and hasn't saved it yet**. Also, RabbitMQ doesn't do fsync(2) for every message -- it may be just saved to cache and not really written to the disk. The persistence guarantees aren't strong, but it's more than enough for our simple task queue. If you need a stronger guarantee then you can use publisher confirms.

RabbitMQ just dispatches a message when the message enters the queue. **It doesn't look at the number of unacknowledged messages for a consumer. It just blindly dispatches every n-th message to the n-th consumer**.


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

// 根据用户输入拼装消息参数
$data = implode(' ',array_slice($argv,1));
if(empty($data)){
    $data = "Hello World!";
}
$msg = new AMQPMessage($data,[
    'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT  // 代表持久化消息
]);

//  发布消息
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
deliver a message to multiple consumers.（we're going to build a simple logging system.）

**The core idea in the messaging model in RabbitMQ is that the producer never sends any messages directly to a queue. **Instead, the producer can only send messages to an exchange. An exchange is a very simple thing. On one side it receives messages from producers and the other side it pushes them to queues. The exchange must know exactly what to do with a message it receives：
* Should it be appended to a particular queue? 
* Should it be appended to many queues?
* Should it get discarded？ 
The rules for that are defined by the **exchange type**：direct, topic, headers , fanout.

The fanout exchange is very simple,it just broadcasts all the messages it receives to all the queues it knows.

**The default exchange**
identify by the empty string ("").

**Temporary queues**
In the php-amqplib client, when we supply queue name as an empty string, we create a **non-durable queue** with a generated name:
```
list($queue_name, ,) = $channel->queue_declare(""); // amq.gen-JzTY20BRgKO-HjmUJj0wLg
```
the $queue_name variable contains a random queue name generated by RabbitMQ.

relationship between exchange and a queue is called a binding.


## log接收者
每次运行动态生成一个queue，并绑定到相应的exchange上
```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// 定义exchange
$channel->exchange_declare('logs','fanout', FALSE, FALSE, FALSE);

// 生成一个临时queue，并保存queue的名字
list($queue_name,,) = $channel->queue_declare('', FALSE, FALSE,TRUE, FALSE);

// 将queue绑定到exchange
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
$data = implode(' ', array_slice($argv, 1));
if (empty($data)) $data = "info: Hello World!";
$msg = new AMQPMessage($data);

// 将消息发布到exchange
$channel->basic_publish($msg,'logs');

echo " [x] Sent ", $data, "\n";
$channel->close();
$connection->close();
```





# 例：Routing
subscribe only to a subset of the messages.（For example, we will be able to direct only critical error messages to the log file (to save disk space), while still being able to print all of the log messages on the console.）

The meaning of a binding key depends on the exchange type. The fanout exchanges simply ignored its value.

The routing algorithm behind a direct exchange is simple - a message goes to the queues whose binding key exactly matches the routing key of the message.

It is perfectly legal to bind multiple queues with the same binding key.

多个queue可以使用同一个key绑定到同一个exchange。
同一个queue和同一个exchange之间可以绑定多个不同的key。

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





# 例：Topics
Using the direct exchange still has limitations - **it can't do routing based on multiple criteria**.
(In our logging system we might want to subscribe to not only logs based on severity, but also based on the source which emitted the log. )

Messages sent to a topic exchange can't have an arbitrary routing_key - it must be a list of words, delimited by dots(up to the limit of 255 bytes).

there are two important special cases for binding keys:
* \* (star) can substitute for exactly one word.
* \# (hash) can substitute for zero or more words.

比如：`<speed>.<colour>.<species>`:
```
Q1: *.orange.*  // all the orange animals
Q2: *.*.rabbit  // everything about rabbits
Q3: lazy.#      // everything about lazy animals
```
"quick.brown.fox" doesn't match any binding so it will be discarded.
"orange" or "quick.orange.male.rabbit"? Well, these messages won't match any bindings and will be lost.
"lazy.orange.male.rabbit", even though it has four words, will match the last binding.

When a queue is bound with "#" (hash) binding key - it will receive all the messages, regardless of the routing key - like in fanout exchange.

When special characters "*" (star) and "#" (hash) aren't used in bindings, the topic exchange will behave just like a direct one.

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
run a function on a remote computer and wait for the result.

A client sends a request message and a server replies with a response message. In order to receive a response we need to **send a 'callback' queue address with the request**. 

The AMQP 0-9-1 protocol predefines a set of 14 properties that go with a message. Most of the properties are rarely used, with the exception of the following:
* delivery_mode: Marks a message as persistent (with a value of 2) or transient (1). You may remember this property from the second tutorial.
* content_type: Used to describe the mime-type of the encoding. For example for the often used JSON encoding it is a good practice to set this property to: application/json.
* reply_to: Commonly used to name a callback queue.
* correlation_id: Useful to correlate RPC responses with requests.

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

