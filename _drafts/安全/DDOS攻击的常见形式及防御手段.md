# DDOS攻击的常见形式及防御手段

DDOS，Distributed Denial of Service,分布式拒绝服务，利用合理的请求造成资源过载，导致服务不可用。

## SYN flood
SYN flood是一种最为经典的DDOS攻击，它利用了TCP协议设计的缺陷，先伪造大量的源IP地址向服务器端发送大量的SYN包，服务器端接收后会返回SYN/ACK包，因为源地址是伪造的，所以伪造的IP并不会应答，服务器端收不到伪造的IP的回应，于是重试3~5次并等待一个SYN Time(30~120s)，超时后丢弃连接。攻击者大量发送这种伪造源地址的SYN请求，服务器端将会消耗非常多的资源来处理这种半连接，进而导致拒绝服务。
主要的对抗方式是SYN Cookie，为每一个IP地址分配一个Cookie，并统计每个IP地址的访问频率，如果在短时间内收到大量来自同一个IP地址的数据包，则认为受到了攻击，之后来自这个IP地址的包将被丢弃。

## 应用层DDOS
发生在应用层（TCP连接已建立），比如对一些资源消耗较大的应用页面不断地发起正常的请求，以达到消耗服务器端资源的目的。是针对服务器性能的一种攻击。

最常见的防御措施是在应用中针对每个客户端（IP+Cookie）做一个请求频率的限制。

## Slowloris攻击
比如以极低的速度往服务器发送HTTP请求，由于Web Server对于并发的连接数都有一定的上限，因此当恶意占用这些连接不释放时，WebServer的所有连接将被占用，从而无法接受新的请求。
比如构造一个畸形的HTTP请求：
```
GET / HTTP/1.1\r\n
....
Content-Lenght:42\r\n
```
在正常的HTTP包头中是以两个CLRF表示HTTP Headers部分结束的：
```
Content-Length:42\r\n\r\n
```
由于Web Server只收到一个\r\n，因此将认为HTTP Headers部分没有结束，并保持此连接不释放，继续等待完整的请求，此时客户端再发送任意HTTP头，保持住连接即可：
```
X-a:b\r\n
```
这种攻击几乎对所有Web Server都有效。

## HTTP POST DOS
在发送HTTP POST包时指定一个非常大的Content-Length值，然后以很低的速度发包（比如10~100s发一个字节），保持这个连接不断开，这样当客户端连接数多了以后占用住Web Server的所有可用连接，从而导致DOS。

## Server Limit DOS
Web Server对HTTP包头都有长度限制，比如Apache默认为8192字节，如果客户端发送的HTTP包头超过这个大小，服务器会返回一个4xx错误。攻击者通过XSS攻击恶意地往客户端写入一个超长的Cookie，则该客户端在清空Cookie之前将无法再访问该Cookie所在域的任何页面。

## ReDOS
正则表达式也能造成拒绝服务：构造恶意的输入，消耗大量的系统资源（比如CPU和内存），从而导致整台服务器的性能下降。
详略。

## 防御措施
1.验证码：CAPTCHA,Complete Automated Public Turing Test to Tell Computers and Humans Apart,全自动区分计算机和人类的图灵测试。

2.让客户端解析一段JavaScript，并给出正确的运行结果；

3.优化Web Server的配置，比如调小Timeout、KeepAliveTimeout的值、增加MaxClients的值等。