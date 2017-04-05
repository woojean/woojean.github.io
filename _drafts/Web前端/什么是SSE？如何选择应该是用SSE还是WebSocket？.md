# 什么是SSE？如何选择应该是用SSE还是Web Socket？

SSE（Server-Sent Events，服务器发送事件）是围绕只读 Comet 交互推出的 API 或者模式。SSE API用于创建到服务器的单向连接，服务器通过这个连接可以发送任意数量的数据。服务器响应的 MIME类型必须是 text/event-stream ，而且是浏览器中的 JavaScript API 能解析格式输出。SSE 支持短轮询、长轮询和 HTTP 流，而且能在断开连接时自动确定何时重新连接。
var source = new EventSource("myevents.php");
source.onmessage = function(event){
var data = event.data;
//处理数据
};
source.close();

所谓的服务器事件会通过一个持久的 HTTP 响应发送，这个响应的 MIME 类型为 text/event。
面对某个具体的用例，在考虑是使用 SSE 还是使用 Web Sockets 时，可以考虑如下几个因素：
首先，是否有自由度建立和维护 Web Sockets服务器？因为 Web Socket 协议不同于 HTTP，所以现有服务器
不能用于 Web Socket 通信。SSE 倒是通过常规 HTTP 通信，因此现有服务器就可以满足需求。
到底需不需要双向通信。如果用例只需读取服务器数据（如比赛成绩），那么 SSE 比较容易实现。如果用例必须双向通信（如聊天室），那么 Web Sockets 显然更好。（在不能选择 Web Sockets 的情况下，组合 XHR 和 SSE 也是能实现双向通信的）