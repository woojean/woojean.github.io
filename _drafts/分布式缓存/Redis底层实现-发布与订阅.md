# Redis底层实现-发布与订阅

通过执行SUBSCRIBE命令，客户端可以订阅一个或多个频道：

```
SUBSCRIBE "news.it"
```
另一个客户端执行发布命令：
```
PUBLISH "news.it" "hello"
```

还可以通过执行PSUBSCRIBE命令订阅一个或多个模式（一个模式可以匹配多个频道）。


## 频道的订阅与退订
Redis将所有频道的订阅关系都保存在服务器状态的pubsub_channels字典里面，这个字典的键是某个被订阅的频道，而键的值则是一个链表，链表里面记录了所有订阅这个频道的客户端。
SUBSCRIBE和UNSUBSCRIBE实际就是对这个链表的操作。
详略。

## 模式的订阅与退订
服务器也将所有模式的订阅关系都保存在服务器状态的pubsub_patterns属性里面。pubsub_patterns属性是一个链表，链表中的每个节点都包含着一个pubsub Pattern结构，这个结构的pattern属性记录了被订阅的模式，而client属性则记录了订阅模式的客户端。
详略。

## 发送消息
当一个Redis客户端执行PUBLISH <channel> <message>命令将消息message发送给频道channel的时候，服务器需要执行以下两个动作：
1.将消息message发送给channel频道的所有订阅者。
2.如果有一个或多个模式pattern与频道channel相匹配，那么将消息message发送给pattern模式的订阅者。

## 查看订阅信息
PUBSUB命令是Redis 2.8新增加的命令之一，客户端可以通过这个命令来查看频道或者模式的相关信息，比如某个频道目前有多少订阅者，又或者某个模式目前有多少订阅者，诸如此类。
详略。