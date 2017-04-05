# HTTP的扩展协议

## SPDY

Google于2010年发布，目标在于解决HTTP的性能瓶颈，缩短Web页面的加载时间（50%）。SPDY以会话层的形式加入，控制对数据的流动，但还是采用HTTP建立通信连接，因此可照常使用HTTP的GET和POST等方法、Cookie以及HTTP报文等：
HTTP		应用层
SPDY		会话层
SSL			表示层
TCP			传输层

## WebSocket

使用浏览器进行全双工通信。提供API，可供Javascript调用。

## HTTP/2.0

仍在讨论中。

## WebDAV

一个可对Web服务器上的内容直接进行文件复制、编辑等操作的分布式文件系统。