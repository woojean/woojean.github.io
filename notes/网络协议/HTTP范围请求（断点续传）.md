# HTTP范围请求（断点续传）

一种`网络中断可恢复机制`，`解决下载过程中网络中断后需要重头开始下载的问题`。

例如只请求5001~10000字节内的资源：
GET /image.jpg HTTP/1.1
Host:www.test.com
`Range:bytes = 5001-10000`

这里用到首部字段Range，其他用法有：
从5001之后的全部字节：
Range:bytes=5001-
多重范围：从一开始到3000字节以及5000-7000字节
Range:bytes=-3001,5000-7000

针对范围请求，如果服务器端无法响应范围请求，那么便会返回状态码200 OK，和完整的实体内容。否则，便会返回状态码为206 Partial Content的响应报文：
HTTP/1.1 206 `Partial Content`
Date:Fri, 13 Jul 2014 04:39:17 GMT
`Content-Range:bytes 5001-10000/10000`
Content-Length:5000
Content-Type:image/jpeg

另外，对于多重范围的范围请求，响应会在首部字段Content-Type标明multipart/byteranges后返回响应报文。