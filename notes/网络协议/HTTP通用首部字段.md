# HTTP通用首部字段

## Cache-Control

通过一系列指令来控制缓存，多个指令用逗号分隔，如：
Cache-Control:private,max-age=0,no-cache

**缓存请求指令**
no-cache：强制向原服务器再次验证
no-store：不缓存请求或者响应的任何内容
max-age=[s]：响应的最大Age值（客户端告知缓存维护者，如果缓存过期不超过XX时间，就直接给我吧）
max-stale=[s]：接收已过期的响应
min-fresh=[s]：期望在指定时间内的响应仍有效
no-transform：代理不可更改媒体类型（防止压缩图片等操作）
only-if-cached：从缓存获取资源
cache-extension：新指令标记（用来扩展指令，但是其行为需要事先约定）

**缓存响应指令**
public：（源服务器告诉缓存代理服务器）可向任意方提供响应的缓存
private：（源服务器告诉缓存代理服务器）仅向特定用户返回响应
no-cache：缓存前必须先确认其有效性（区别于请求指令，在响应指令中的no-cache可以带参数Cache-Control:no-cache=Location）
no-store：不缓存请求或者响应的任何内容
no-transform：代理不可更改媒体类型
must-revalidate：可缓存但必须再向源服务器进行确认（强制验证，使用该指令时将会忽略掉max-stale）
proxy-revalidate：要求中间缓存服务器对缓存的响应有效性再进行确认
max-age=[s]：响应的最大Age值（源服务器告知缓存维护者XX时间内不必再确认，可以直接支配缓存）
s-maxage=[s]：公共缓存服务器（通常指代理）响应的最大Age值，当使用该命令时，将直接忽略对Expires首部字段及max-age指令的处理
cache-extension：新指令标记



## Connection

**控制不再转发给代理的首部字段**

Connection:不再转发的首部字段名（都是逐跳首部）
根据是否经过缓存代理，将HTTP首部字段分为两种，即：`端到端首部`（End-to-end Header）和`逐跳首部`（Hop-by-hop Header），逐跳首部有8个（Connection、Keep-Alive、Proxy-Authenticate、Proxy-Authorization、Trailer、TE、Transfer-Encoding、Upgrade），其他的所有首部都是端到端首部。

客户端 -> 代理服务器：

```
GET / HTTP/1.1
Upgrade:HTTP/1.1
Connection:Upgrade
```

代理服务器 -> 源服务器

```
GET / HTTP/1.1  //这里去掉了Upgrade
```

**管理持久链接**

HTTP/1.1默认使用持久链接，当想要明确断开链接时，需要使用Connection:close
HTTP/1.1之前的版本默认都是非持久链接，为此，在旧版本上想要使用持久链接，则必须将Connection设为Keep-Alive，如：

```
GET / HTTP/1.1
Connection：Keep-Alive
```



## Date

用来表明创建HTTP报文的日期和时间



## Pragma

历史遗留字段，被Cache-Control:no-cache取代



## Trailer

用来事先说明在报文主体后记录了哪些首部字段，常用于HTTP分块传输编码的场景。


## Transfer-Encoding

规定了传输报文主体时采用的编码方式，仅对分块传输编码有效。


## Upgrade

用于检测HTTP协议或者其他协议是否可使用更高的版本进行通信，其值可以用来指定一个完全不同的通信协议。其作用仅限于客户端和相邻服务器之间，因此还需要配合使用Connection:Upgrade。对于附有首部字段Upgrade的请求，服务器用101 Switching Protocols状态码作为相应返回。

## Via

在经过代理时附加该首部字段，表名代理服务器的信息（HTTP协议版本、域名、服务器版本等）：

客户端 -> proxy1

```
GET / HTTP/1.1
```

proxy1 -> proxy2

```
GET / HTTP/1.1 
Via:1.0 proxy1.com(squid/3.1)
```

proxy2 -> 源服务器

```
GET / HTTP/1.1 
Via:1.0 proxy1.com(squid/3.1),1.1 proxy2.com(squid/3.2)
```

Via首部是为了追踪传输路径，所以常和TRACE方法一起使用，如代理服务器收到由TRACE方法发送过来的请求，其中Max-Forwards:0，这时代理服务器就不能转发改请求了，代理服务器会将自身的信息附加到Via首部后，返回该请求的响应。



## Warning

用来告知用户一些与缓存相关的问题的警告。定义了7种警告供参考。