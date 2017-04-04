# Apache基于HTTP协议的缓存协商机制实现响应内容缓存的工作过程

## 缓存响应内容
一个URL在一段较长的时间内对应一个唯一的响应内容，主流的Web服务器软件都提供对URL映射内容的缓存，比如Apache的mod_cache，在URL映射开始时检查缓存，如果缓存有效就直接取出作为响应内容返回给浏览器。
Web服务器的缓存机制实质上是以URL为主键的key-value结构缓存。
```
LoadModule cache_module modules/mod_cache.so
LoadModule disk_cache_module modules/mod_disk_cache.so
CacheRoot /data/apache_cache
CacheEnable disk /
CacheDirLevels 5
CacheDirLength 3

CacheMaxExpire 3600
CacheIgnoreHeaders Set-Cookie
```
Apache将缓存的HTTP头信息和正文信息独立存储，这样为缓存过期检查提供了方便：只要检查HTTP头信息的文件即可。

Web服务器的缓存仍然基于HTTP的缓存协商进行控制。

## 缓存文件描述符
Apache提供了mod_file_cache模块来将已打开的文件描述符缓存在Web服务器内存中。需要指定希望缓存的文件列表：
```
CacheFile /data/www/htdocs/test.htm
```
Apache在启动时会打开这些文件，并持续到Apache关闭为止。
缓存文件描述符只适合小的静态文件，因为处理较大的静态文件所花费的主要时间在传送数据上，而不是打开文件。