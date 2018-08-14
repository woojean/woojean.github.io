---
layout: post
title:  "《AMQP 0-9-1 Model Explained》读书笔记"
date: 2017-04-24 00:00:01
categories: 编程
tags: AMQP 消息队列 网络协议
excerpt: "AMQP协议基本概念理解"
---

* content
{:toc}
本文翻译自：[http://www.rabbitmq.com/tutorials/amqp-concepts.html](http://www.rabbitmq.com/tutorials/amqp-concepts.html)

# AMQP 0-9-1协议及AMQP Model 概览

**AMQP**:Advanced Message Queuing Protocol，是一个用于特定客户端与特定**消息代理**通信的协议。因为**AMQP的“实体”（即Exchange、message、queue等）和路由架构是由应用程序自己定义的**（而不是代理管理员），所以AMQP是一个支持编程扩展的协议。

```
publisher --publish--> exchange --routes--> queue --consumers--> consumer
```



# Exchanges及其类型

## Default Exchange
由消息代理（broker）预先定义的，未命名的exchange。每一个被创建的queue都使用queue的名称自动绑定到该exchange。比如定义一个名为“search-indexing-online”的queue，那么AMQP broker将会使用“search-indexing-online”为routing key将该队列绑定到默认的exchange，当一个带有该routing key的消息被发布时，将会被路由到"search-indexing-online"这个队列。



## Direct Exchange
直接Exchange基于消息的路由键（message routing key）来分发消息。当一个带有routing key的消息到来时，Direct Exchange将会把该消息分发给所有使用该key进行绑定的queue（注意，这里的queue也可能是多个，即它们要处理的routing key相同）。

**当有多个队列匹配时，时轮询还是全部发送？** 全部发送



## Fanout Exchange
Fanout Exchange会把收到的消息分发给所有绑定的queue（即忽略routing key），适用于广播的场景。



## Topic Exchange
基于消息的routing key和匹配模式（pattern）来分发消息。



## Headers Exchange
忽略routing key，而是基于message headers来分发消息。

当其x-match参数设为any时，只要有一个header值满足条件，则消息会被分发。当设为all时，所有headers都必须满足才会被分发。

# Queues
queue用来保存消息，主要有以下这些属性：
* Name
* Durable：broker重启后消息不会丢失
* Exclusive：该queue只能用于一个连接，且当连接关闭时queue将会被删除
* Auto-delete：当最后一个消费者解除订阅时，queue将被删除
* Arguments：一些broker使用它来实现重要的属性，比如message的TTL



## Queue Names
Queue names不能超过255个字符。以"amq."开头的名称被保留为broker内部使用。



## Queue Durability
持久队列会被辞旧化到磁盘上，所以broker重启后，队列不会丢失。非持久化的队列也称为transient。

持久化队列并不保证队列中的消息也被持久化。当broker重启时，持久化的队列会被重新定义，但是只有持久化的消息（persistent messages）才会被恢复。



# Bindings

Binding用来指定exchanges与queue之间的路由关系。

如果一个消息无法被路由（比如消息发布到的exchange没有绑定任何queue），那么这个消息将会被丢弃或者返回给消息的发布者（取决于消息发布者的设置）。



# Consumers
应用程序有2种消费消息的方式：

* push方式：注册一个消费者，并**订阅**一个队里。

* pull方式：主动去队列获取消息。

  ​

## Message Acknowledgements
AMQP定义了2种从队列中确认并删除消息的选择：
* 自动确认：在broker将消息发送到应用程序后，自动删除。
* 明确确认：在应用程序发回确认信息后删除。

当消费者（consumer）崩溃因而未能返回确认信息，broker将会尝试把消息分发到其他的消费者。如果没有任何可用的消费者，broker将会等待直到有一个新的消费者注册。

## Rejecting Messages
应用程序可以拒绝消息以此来通知broker消息处理失败，并且可以告诉broker丢弃该消息或者重新入队。

## Negative Acknowledgements
AMQP没有定义拒绝复合消息（multiple messages）的方法，不过如果使用RabbitMQ，则可以。

## Prefetching Messages
Prefetching Messages是一种负载均衡技术，应用于有多个消费者消费同一个队列的场景。它定义了在消费者返回下一个确认信息之前可以发送的消息的数量。

RabbitMQ只支持渠道级别（channel-level）的prefetch-count，而不是连接级别或者基于传输大小（not connection or size based prefetching）。



# Message Attributes and Payload
Message的一些属性：
* Content type

* Content encoding

* Routing key

* Delivery mode (persistent or not)

* Message priority

* Message publishing timestamp

* Expiration period

* Publisher application id

AMQP messages支持携带负载信息（payload），**AMQP brokers会把这些信息当做字节数组**进行透明传输，而不会去修改它们。

在发布消息时可以指定消息为持久消息，如果这样，队列将会持久化这些消息（影响性能）。

# Message Acknowledgements
AMQP内建了消息确认机制，当应用程序崩溃时（broker通过发现连接断开来判断），对于未收到确认的消息，broker将会被重新入队或者等待有可用的队列后重新入队。



# AMQP 0-9-1 Methods
AMQP定义了一系列的方法（类似HTTP的方法，而不是程序语言的方法），方法使用类（classes）来组织。

## exchange class
一组与exchange操作相关的方法:
* exchange.declare
* exchange.declare-ok
* exchange.delete
* exchange.delete-ok

比如一个客户端请求broker来定义一个新的exchange：
```
Client(Publisher/Consumer) --exchange.declare--> AMQP broker
              [name="xxx",type="direct",durable=true,...]
```
如果创建成功，broker将会通过exchange.declare-ok方法返回信息：
```
Client(Publisher/Consumer) <--exchange.declare-ok-- AMQP broker
```

并非所有的AMQP方法都有对应的response方法，比如basic.publish。



# Connections
AMQP connections are typically long-lived. 
AMQP is an **application level protocol** that uses TCP for reliable delivery. 
AMQP connections use authentication and can be protected using TLS (SSL). 

AMQP连接是典型的长连接。

AMQP是一个应用层协议，使用TCP来实现可靠传输。

AMQP可以使用认证，也可以使用TLS（SSL）来实现安全传输。



# Channels
有些应用程序可能同时需要多个连接连接到AMQP broker，但是同时保持多个打开的TCP连接是不可取的。

AMQP 0-9-1连接使用channel来实现并发连接的功能。多个channel共享同一个TCP连接。常用的场景是在每一个进程或线程中打开一个channel，不同channel之间的数据是不共享的（因而所有的AMQP方法都同时带有一个channel number以便于客户端判断操作对应的channel）。



# Virtual Hosts
AMQP提供类似Web Server的vhosts的概念，用于提供独立的broker运行环境。AMQP客户端在连接阶段可以指定想要连接的vhost。



# AMQP is Extensible

AMQP 0-9-1 has several extension points:略.

# AMQP 0-9-1 Clients Ecosystem
There are many AMQP 0-9-1 clients for many popular programming languages and platforms. 

