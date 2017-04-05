# HTTP实体首部字段

## Allow

用于通知客户端服务器所能接收的HTTP请求方法。当服务器接收到不支持的HTTP方法时，会以状态码405 Method Not Allowed作为响应返回，同时还会把所有能支持的HTTP方法都写入首部字段Allow中返回。

## Content-Encoding

告知客户端，服务器对实体的主体部分所选用的内容编码方式。

## Content-Language

告知客户端，实体主体所使用的自然语言。

## Content-Length

告知客户端，实体主体部分的大小，对实体主体部分进行内容编码传输时，不能再使用Content-Length首部字段。实体主体大小的计算方法略复杂*

## Content-Location

报文主体部分对应的URI，主要用于返回内容和实际请求的对象不同的场景。

## Content-MD5

对报文的主体执行MD5算法后获得128位二进制数，再通过Base64编码后将结果写入Content-MD5字段值。

## Content-Range

用于范围请求的返回。

## Content-Type

实体主体内对象的媒体类型。

## Expires

将资源失效的日期告知客户端。
当源服务器不希望缓存服务器对资源进行缓存时，可在Expires字段内写入与首部字段Date相同的时间值。
当首部字段Cache-Control有指定max-age指令时，会优先处理max-age而忽略Expires。

## Last-Modified

指明资源最终修改的时间。

## Set-Cookie

响应首部字段，设置Cookie到本地
Set-Cookie字段属性：
`NAME`=VALUE  # 键值对，可自定义
`expires`=DATE  # 指定可发送Cookie的有效期，若未指明，则默认为浏览器关闭前为止
`path`=PATH  # 将服务器上的文件目录作为Cookie的适用对象，若不指定，则默认为文档所在的文件目录（即限定发送Cookie的目录）
`domain`=域名  # 作为Cookie适用对象的域名，若不指定，则默认为创建Cookie的服务器的域名（即限定发送Cookie的域名）
`Secure`：仅在HTTPS通信时才会发送Cookie，例：
`Set-Cookies`: name=value;secure
`HttpOnly`：使Cookie不能被JavaScript脚本访问，即不能通过JS的document.cookie来读取附加了HttpOnly的Cookie，进而防止跨站脚本攻击。

## Cookie

请求首部字段，带上本地Cookie
其他首部字段*

## X-Frame-Options

用于控制网站内容在其他Web网站的Frame标签内的显示问题

## X-XSS-Protection

用于控制浏览器XSS防护机制的开关

## DNT

Do not track，是表示拒绝被精准广告追踪的一种方法

## P3P

让Web网站上的个人隐私变成一种仅供程序可理解的形式