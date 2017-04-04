# 浏览器基于HTTP协议的缓存协商机制实现前端资源缓存的工作过程

浏览器会为缓存的每个文件打上一些标记，比如过期时间，上次修改时间、上次检查时间等。

## 缓存协商
缓存协商基于HTTP头信息进行，动态内容本身并不受浏览器缓存机制的排斥，只要HTTP头信息中包含相应的缓存协商信息，动态内容一样可以被浏览器缓存。不过对于POST类型的请求，浏览器一般不启用本地缓存。

## Last-Modified
```
<?php
  header('Last-Modified:' . gmdate('D, d M Y H:i:s') . ' GMT');
  echo time();
?>
```

此时再通过浏览器请求该动态文件，HTTP响应中将会添加一个头信息：
```
Last-Modified:Fri, 20 Mar 2009 07:53:02 GMT
```

对于带有`Last-Modified`的响应，浏览器会对文件进行缓存，并打上一些标记，下次再发出请求时会带上如下的HTTP头信息：
```
If-Modified-Since:Fri, 20 Mar 2009 07:53:02 GMT
```

如果没有修改，服务器会返回304信息：
```
HTTP/1.1 304 Not Modified
...
```
意味着浏览器可以直接使用本地缓存的内容。

使用基于最后修改时间的缓存协商存在一些缺点：
1.很可能文件内容没有变化，而只是时间被更新，此时浏览器仍然会获取全部内容。
2.当使用多台机器实现负载均衡时，用户请求会在多台机器之间轮询，而不同机器上的相同文件最后修改时间很难保持一致，可能导致用户的请求每次切换到新的服务器时就需要重新获取所有内容。

## ETag
比如服务器返回如下带ETag的响应：
```
ETag:"74123-b-938fny4nfi8"
```

浏览器在下次请求该内容时会在HTTP头中添加如下信息：
```
If-None-Match:"74123-b-938fny4nfi8"
```
如果相同的话，服务器返回304。
Web服务器可以自由定义ETag的格式和计算方法。

## Expires
Expires告诉浏览器该内容在何时过期，暗示浏览器在该内容过期之前`不需要再询问服务器`（彻底消灭请求），而是直接使用本地缓存即可。
```
...
header('Last-Modified:' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Expires:' . gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT');
```
## Cache-Control
Expire使用的是绝对过期时间，存在一些不足之处，比如浏览器和服务器的时间不一致。
HTTP/1.1提供Cache-Control，使用相对时间来弥补Expires的不足，格式如下：
```
Cache-Control:max-age=<second>
```
目前主流的浏览器都将HTTP/1.1作为首选，所以当HTTP响应头中同时含有Expires和Cache-Control时，浏览器会优先考虑Cache-Control。