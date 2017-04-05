# WebDAV的大概工作方式

WebDAV是HTTP的扩展协议，它允许基于HTTP/1.1协议来对Web服务器进行远程文件操作，包括文件和目录的创建、修改，以及版本控制等（SVN的HTTP工作方式）。
目前主流的Web服务器软件都支持WebDAV扩展。

创建目录的请求：
```
MKCOL /files/2009/ HTTP/1.1
Host:www.xxx.com
```

创建成功的响应：
```
HTTP/1.1 201 Created
```
