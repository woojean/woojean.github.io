# HTTP请求首部字段

## Accept

用来告知服务器客户端所能够处理的媒体类型及其相对优先级，可以一次指定多种媒体类型及各个类型的权重（用;进行分割）：

```
Accept:text/html,application/xhtml+xml,application/xml;q=0.9,/;q=0.8
```

类型之间用,分隔，未指定q参数，则默认为1.0，因此上例应该理解为：

```
Accept:text/html,application/xhtml+xml,application/xml;q=0.9,/;q=0.8
text/html（q=1.0）
application/xhtml+xml（q=1.0）
application/xml（q=0.9）
/（q=0.8）
```

## Accept-Charset

告知能够处理的字符集及其相对优先级

## Accept-Encoding

告知能够处理的内容编码及其相对优先级，如gzip、compress、deflate、identity（不执行压缩、默认的编码格式）

## Accept-Language

告知能够处理的自然语言集及其相对优先级

## Authorization

告知服务器用户代理的认证信息（证书值），通常是在收到401的返回码后，把Authorization字段加入请求中，再次请求

## Expect

告知所期望的扩展，如果服务器端不能满足期望，则会返回417 Expection Failed

## From

告知服务器，用户的邮箱地址，如From:winstonwu@tencent.com

## Host

多个虚拟主机可能运行在同一个IP上，这时候应该使用首部字段Host（域名+端口号）加以区分。若服务器未设Host，那么直接发送一个空值即可Host:。Host是HTTP/1.1规范内唯一一个必须被包含在请求内的首部字段。

## If-Match

只有当If-Match的字段值和ETag值匹配一致时，服务器才会接受请求。否则返回412 Precondition Failed

## ETag

实体标记，是与特定资源关联的确定值。资源更新后ETag也会随之更新。
假如用*来指定If-Match的值，服务器将会忽略ETag的值，只要资源存在就处理请求。

## If-Modified-Since

如果在If-Modified-Since字段指定的日期之后资源发生了更新，服务器会接受请求，否则返回304 Not Modified

## If-None-Match

与If-Match字段的作用相反，只有在If-None-Match的值与ETag值不一致的时候，可以处理该请求

## If-Range

告知服务器，若指定的If-Range字段值（ETag值或者时间）和所请求的资源的ETag值或时间相一致，则做范围请求，反之则返回整个资源。

```
GET /index.html
If-Range:”123456”
Range:bytes=5001-10000
```

若If-Range字段值（ETag值或者时间）和所请求的资源的ETag值或时间相一致，则返回：

	206 Partial Content
	Content-Range:bytes 5001-10000/10000
	Content-Length:5000
若不一致，则忽略范围请求，返回全部资源：

```
200 OK
ETag:”56789”
```

若不使用If-Range，则需要两次请求：

```
GET /
If-Match:”123456”
Range:5001-10000
```

这时服务器端返回412 Precondition Failed，于是还要再次发出请求全部资源：

```
GET /
```

## If-Unmodified-Since

和If-Modified-Since的作用相反。

## Max-Forwards

指定可通过的服务器的最大数目。通过TRACE或OPTIONS方法发送包含Max-Forwards首部字段的请求时，服务器在往下一个服务器转发请求之前，会将Max-Forwards的值减1后重新赋值。当服务器接收到Max-Forwards值为0的请求时，则不再进行转发，而是直接返回响应。

## Proxy-Authorization

用于告知代理服务器认证所需要的信息

## Range

用于范围请求的指定范围。若服务器可以处理范围请求，则返回206，若无法处理，则返回200及整个资源。

## Referer

告知服务器请求的原始资源的URI，当直接在浏览器的地址栏输入URI或者出于安全性考虑时，也可以不发送该首部字段。

## TE

与Accept-Encoding功能类型，但是用于传输编码。

## User-Agent

将创建请求的浏览器和用户代理名称等信息传达给服务器。