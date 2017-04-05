# 什么是Redis的管道技术？

Redis是一种基于客户端-服务端模型以及请求/响应协议的TCP服务。这意味着通常情况下一个请求会遵循以下步骤：
（1）客户端向服务端发送一个查询请求，并监听Socket返回，通常是以阻塞模式，等待服务端响应。
（2）服务端处理命令，并将结果返回给客户端。
Redis管道技术可以在服务端未响应时，客户端可以继续向服务端发送请求，并最终一次性读取所有服务端的响应。
如：
$(echo -en "PING\r\n SET w3ckey redis\r\nGET w3ckey\r\nINCR visitor\r\nINCR visitor\r\nINCR visitor\r\n"; sleep 10) | nc localhost 6379

+PONG
+OK
redis
:1
:2
:3
以上实例中通过使用PING命令查看redis服务是否可用，之后设置了w3ckey的值为redis，然后获取w3ckey的值并使得visitor自增3次。在返回的结果中可以看到`这些命令一次性向redis服务提交，并最终一次性读取所有服务端的响应`
管道技术最显著的优势是提高了redis服务的性能。