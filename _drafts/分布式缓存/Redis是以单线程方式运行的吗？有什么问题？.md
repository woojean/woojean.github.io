# Redis是以单线程方式运行的吗？有什么问题？

Redis实际上是单线程运行的。虽然Redis是单线程运行的，但是可以同时运行多个Redis客户端进程，常见的并发问题还是会出现。像如下的代码，在get运行之后，set运行之前，powerlevel的值可能会被另一个Redis客户端给改变，从而造成错误：

```
redis.multi()
current = redis.get('powerlevel')
redis.set('powerlevel', current + 1)
redis.exec()
```