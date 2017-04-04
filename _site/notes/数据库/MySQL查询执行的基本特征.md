# MySQL查询执行的基本特征

MySQL客户端和服务器端的通信协议是半双工的，所以无法将一个消息分成小块来独立发送。一旦一端开始发送消息，另一端要接收完整消息才能响应它。
客户端使用一个单独的数据包将查询传给服务器，数据包的大小限制取决于max_allowed_packet配置。
服务器响应的数据一般由多个数据包组成，当服务器开始响应客户端请求时，客户端必须完整地接收整个返回结果，而不能简单地只取前面几条需要的结果。
大部分连接MySQL的库函数都可以获得全部结果集并缓存到内存里，也可以逐行获取需要的数据，MySQL通常要等所有数据都已经发送给客户端后才能释放连接。
如下PHP代码：

```
$link = mysql_connect('localhost','user','password');
$result = mysql_query('SELECT * FROM HUGE_TABLE',$link);
while($row = mysql_fetch_array($result)){
  // ...
}
```
看起来好像是只有当需要获取数据时才通过循环从服务器取出数据，实际上在调用mysql_query时PHP就已经将整个查询结果缓存到内存中，while循环只是从缓存中逐行取出数据。用mysql_unbuffered_query代替mysql_query则不会缓存结果。

最终交给引擎的执行计划是一个数据结构，而不是像其他DB那样的字节码。