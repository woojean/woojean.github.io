---
layout: post
title:  "《AMQP 0-9-1 Model Explained》读书笔记"
date: 2017-04-24 00:00:01
categories: 技术书籍文档
tags: AMQP 网络协议
excerpt: "AMQP协议基本概念理解"
---

* content
{:toc}

# High-level Overview of AMQP 0-9-1 and the AMQP Model
AMQP:Advanced Message Queuing Protocol,a messaging protocol that enables conforming client applications to communicate with conforming **messaging middleware brokers**.

```
publisher --publish--> exchange --routes--> queue --consumers--> consumer
```

AMQP 0-9-1 is a programmable protocol in the sense that AMQP entities and routing schemes are defined by applications themselves, not a broker administrator. 


# Exchanges and Exchange Types
## Default Exchange
a direct exchange with no name (empty string) pre-declared by the broker.every queue that is created is automatically bound to it with a routing key which is **the same as the queue name**:when you declare a queue with the name of "search-indexing-online", the AMQP broker will bind it to the default exchange using "search-indexing-online" as the routing key. Therefore, a message published to the default exchange with the routing key "search-indexing-online" will be routed to the queue "search-indexing-online".

## Direct Exchange
delivers messages to queues based on the **message routing key**. 
When a new message with routing key R arrives at the direct exchange, the exchange routes it to the queue if K = R.(注意，这里的queue也可能是多个，即它们要处理的routing key相同)
Direct exchanges are often used to distribute tasks between multiple workers (instances of the same application) in a round robin manner. in AMQP 0-9-1, messages are load balanced between consumers and not between queues.

## Fanout Exchange
routes messages to all of the queues that are bound to it and the routing key is ignored. ideal for the broadcast routing of messages.

## Topic Exchange
route messages to one or many queues based on matching between a **message routing key and the pattern** that was used to bind a queue to an exchange. often used to implement various publish/subscribe pattern variations. 

Whenever a problem involves multiple consumers/applications that **selectively choose which type of messages they want to receive**, the use of topic exchanges should be considered.

## Headers Exchange
designed for routing on **multiple attributes** that are more easily expressed as message headers than a routing key. Headers exchanges ignore the routing key attribute. 
When the "x-match" argument is set to "any", just one matching header value is sufficient. Alternatively, setting "x-match" to "all" mandates that all the values must match.

# Queues
store messages that are consumed by applications. Queues share some properties with exchanges(but also have some additional properties):
* Name
* Durable (the queue will survive a broker restart)
* Exclusive (used by only one connection and the queue will be deleted when that connection closes)
* Auto-delete (queue is deleted when last consumer unsubscribes)
* Arguments (some brokers use it to implement additional features like message TTL)

Before a queue can be used it has to be declared. Declaring a queue will cause it to be created if it does not already exist. The declaration will have no effect if the queue does already exist and its attributes are the same as those in the declaration. When the existing queue attributes are not the same as those in the declaration a channel-level exception with code 406 (PRECONDITION_FAILED) will be raised.

## Queue Names
Queue names may be up to 255 bytes of UTF-8 characters. 
Queue names starting with "amq." are reserved for internal use by the broker. Attempts to declare a queue with a name that violates this rule will result in a channel-level exception with reply code 403 (ACCESS_REFUSED).

## Queue Durability
Durable queues are **persisted to disk** and thus survive broker restarts. Queues that are not durable are called **transient**. 

**Durability of a queue does not make messages that are routed to that queue durable**. If broker is taken down and then brought back up, durable queue will be re-declared during broker startup, however, **only persistent messages will be recovered.**

# Bindings
rules that exchanges use to route messages to queues. 

If AMQP message cannot be routed to any queue (for example, because there are no bindings for the exchange it was published to) it is either dropped or returned to the publisher, depending on message attributes the publisher has set.

# Consumers
In the AMQP 0-9-1 Model, there are two ways for applications to do consume:
* Have messages delivered to them ("push API")(register a consumer or, simply put, subscribe to a queue.)
* Fetch messages as needed ("pull API")

## Message Acknowledgements
when should the AMQP broker remove messages from queues? The AMQP 0-9-1 specification proposes two choices:
* the automatic acknowledgement mode:After broker sends a message to an application (using either basic.deliver or basic.get-ok AMQP methods).
* the explicit acknowledgement model:After the application sends back an acknowledgement (using basic.ack AMQP method).

If a consumer dies without sending an acknowledgement the AMQP broker will redeliver it to another consumer or, if none are available at the time, the broker will wait until at least one consumer is registered for the same queue before attempting redelivery.

## Rejecting Messages
An application can indicate to the broker that message processing has failed (or cannot be accomplished at the time) by rejecting a message. When rejecting a message, an application can ask the broker to discard or requeue it.

## Negative Acknowledgements
there is no way to reject multiple messages as you can do with acknowledgements. However, if you are using RabbitMQ, then there is a solution.

## Prefetching Messages
For cases when multiple consumers share a queue, it is useful to be able to specify how many messages each consumer can be sent at once before sending the next acknowledgement. This can be used as **a simple load balancing technique** or to improve throughput if messages tend to be published in batches. 

**RabbitMQ only supports channel-level prefetch-count, not connection or size based prefetching.**

# Message Attributes and Payload
Messages in the AMQP model have attributes,Some examples are:
* Content type
* Content encoding
* Routing key
* Delivery mode (persistent or not)
* Message priority
* Message publishing timestamp
* Expiration period
* Publisher application id
Some attributes are used by AMQP brokers, but most are open to interpretation by applications that receive them. 
Message attributes are set when a message is published.
AMQP messages also have a payload (the data that they carry), which **AMQP brokers treat as an opaque byte array**(will not inspect or modify the payload). 

Messages may be published as persistent, which makes the AMQP broker persist them to disk. (affects performance)

# Message Acknowledgements
AMQP 0-9-1 has a built-in feature called message acknowledgements (sometimes referred to as acks) that consumers use to confirm message delivery and/or processing. If an application crashes (the AMQP broker notices this when the connection is closed), if an acknowledgement for a message was expected but not received by the AMQP broker, the message is re-queued (and possibly immediately delivered to another consumer, if any exists).

# AMQP 0-9-1 Methods
AMQP 0-9-1 is **structured as a number of methods**. Methods are operations (like HTTP methods) and have nothing in common with methods in object-oriented programming languages. **AMQP methods are grouped into classes**. 

## exchange class
a group of methods related to operations on exchanges. It includes the following operations:
* exchange.declare
* exchange.declare-ok
* exchange.delete
* exchange.delete-ok
These operations are "requests" (sent by clients) and "responses" (sent by brokers in response to the aforementioned "requests").

the client asks the broker to declare a new exchange:
```
Client(Publisher/Consumer) --exchange.declare--> AMQP broker
              [name="xxx",type="direct",durable=true,...]
```
If the operation succeeds, the broker responds with the exchange.declare-ok method:
```
Client(Publisher/Consumer) <--exchange.declare-ok-- AMQP broker
```

Not all AMQP methods have counterparts. Some (basic.publish being the most widely used one) do not have corresponding "response" methods and some others (basic.get, for example) have more than one possible "response".

# Connections
AMQP connections are typically long-lived. 
AMQP is an **application level protocol** that uses TCP for reliable delivery. 
AMQP connections use authentication and can be protected using TLS (SSL). 

# Channels
Some applications need multiple connections to an AMQP broker. However, it is undesirable to keep many TCP connections open at the same time.

AMQP 0-9-1 connections are multiplexed with channels that can be thought of as "lightweight connections that share a single TCP connection".

it is very common to open a new channel per thread/process and not share channels between them.

Communication on a particular channel is completely separate from communication on another channel, therefore **every AMQP method also carries a channel number** that clients use to figure out which channel the method is for.

# Virtual Hosts
To make it possible for a single broker to host multiple isolated "environments" (groups of users, exchanges, queues and so on), AMQP includes the concept of virtual hosts (vhosts). They are similar to virtual hosts used by many popular Web servers and provide completely isolated environments in which AMQP entities live. **AMQP clients specify what vhosts they want to use during AMQP connection negotiation.**

# AMQP is Extensible
AMQP 0-9-1 has several extension points:略.

# AMQP 0-9-1 Clients Ecosystem
There are many AMQP 0-9-1 clients for many popular programming languages and platforms. 

