# HTTP报文结构

HTTP报文本身其实是`由CR+LF作为换行符的一个多行字符串文本`，大致可以分成`报文首部`和`报文主体`两部分，这两部分`由第一个出现的空行来分隔`。用于请求的报文通常称为`请求报文`，用于响应的报文通常称为`响应报文`。



## 请求报文实例

GET http://www.google.cn/ HTTP/1.1`CR+LF`
Host: www.google.cn`CR+LF`
Proxy-Connection: keep-alive`CR+LF`
Accept: text/html,appl.../webp,*/*;q=0.8`CR+LF`
User-Agent: Mozi...36`CR+LF`
Accept-Language: zh-CN,zh;q=0.8`CR+LF`
Cookie: NID=67=i9vxrEuDQ7tNijPKYNau5...`CR+LF`



## 响应报文实例

HTTP/1.1 200 OK`CR+LF`
Vary: Accept-Encoding`CR+LF`
Content-Type: text/html`CR+LF`
Last-Modified:...T`CR+LF`
Date: Tue, 30 Dec 2014 08:49:48 GMT`CR+LF`
Cache-Control: private, max-age=0`CR+LF`
Server: sffe`CR+LF`
`CR+LF`
<!DOCTYPE html>
<html lang="zh">
  <meta charset="utf-8">
  <title>Google</title>
  <style>
...