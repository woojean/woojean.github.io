# HTTP响应首部字段

## Accept-Ranges

告知客户端，服务器是否能处理范围请求，有两种取值：
Accept-Ranges:bytes 能
Accept-Ranges:none 不能

## Age

告知客户端，源服务器在多久之前创建了响应，单位为秒。
若创建该响应的服务器是缓存服务器，Age值是指缓存后的响应再次发起认证到认证完成的时间值。代理创建响应时必须加上首部字段A	ge。

## ETag

资源实体标识，将资源以字符串形式做唯一标识，算法取决于服务器端。（不同的资源可能对应相同的URI，这时候就要使用ETag来分辨）
ETag中有强ETag和弱ETag之分*。

## Location

提供重定向的URI。

## Proxy-Authenticate

把代理服务器所要求的认证信息发送给客户端。

## Retry-After

告知客户端多久以后再次发出请求，主要配合503 Service Unavailable或者3XX Rediect响应一起使用，字段值可以指定为具体的日期时间，也可以是创建响应后的秒数。

## Server

告知客户端当前服务器上安装的HTTP服务器程序的信息。

## Vary

用于缓存控制

## WWW-Authenticate

用于HTTP访问认证，告知客户端适用于访问请求URI所指定资源的认证方案和带参数提示的质询。状态码为401的响应中，肯定带有首部字段WWW-Authenticate。