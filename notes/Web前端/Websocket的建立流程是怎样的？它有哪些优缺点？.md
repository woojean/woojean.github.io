# Websocket的建立流程是怎样的？它有哪些优缺点？

Web Sockets的目标是在一个`单独的持久连接`上提供`全双工、双向`通信。

要创建 Web Socket，先实例一个 WebSocket 对象并传入要连接的 URL：
```
var socket = new WebSocket("ws://www.example.com/server.php");
socket.send("Hello world!");
socket.close();
```
注意，必须给 WebSocket 构造函数传入绝对 URL。`同源策略对 Web Sockets 不适用`，因此可以通过它打开到任何站点的连接。
实例化了 WebSocket 对象后，浏览器就会马上尝试创建连接。
因为 Web Sockets只能通过连接发送纯文本数据，所以对于复杂的数据结构，在通过连接发送之前，必须进行序列化。

在 JavaScript 中创建了 Web Socket 之后，会有一个 HTTP 请求发送到浏览器以发起连接。在取得服务器响应后，建立的连接会使用 HTTP 升级从 HTTP 协议交换为 WebSocket 协议。也就是说，使用标准的 HTTP 服务器无法实现 Web Sockets，只有支持这种协议的专门服务器才能正常工作。
由于 Web Sockets使用了自定义的协议，所以 URL 模式也略有不同。未加密的连接不再是 http:// ，而是 ws:// ；加密的连接也不是 https:// ，而是 wss:// 。
使用自定义协议而非 HTTP 协议的好处是，能够在客户端和服务器之间发送非常少量的数据，而不必担心 HTTP 那样字节级的开销。由于传递的数据包很小，因此 Web Sockets非常适合移动应用。使用自定义协议的缺点在于，制定协议的时间比制定JavaScript API 的时间还要长。