# 服务器出现大量TIME_WAIT和CLOSE_WAIT的可能原因是什么？

查看当前服务器网络连接状态：
netstat -n | awk '/^tcp/ {++S[$NF]} END {for(a in S) print a, S[a]}'
输出：
TIME_WAIT 814
CLOSE_WAIT 1
FIN_WAIT1 1
ESTABLISHED 634
SYN_RECV 2
LAST_ACK 1

常用的三个状态是：ESTABLISHED 表示正在通信，TIME_WAIT 表示主动关闭，CLOSE_WAIT 表示被动关闭。
![image](https://github.com/woojean/woojean.github.io/blob/master/images/net_12.png)

如果服务器出了异常，百分之八九十都是下面两种情况：
1.服务器保持了大量TIME_WAIT状态
2.服务器保持了大量CLOSE_WAIT状态
因为linux分配给一个用户的文件句柄是有限的，而TIME_WAIT和CLOSE_WAIT两种状态如果一直被保持，那么意味着对应数目的通道就一直被占着，一旦达到句柄数上限，新的请求就无法被处理了，接着就是大量Too Many Open Files异常。

服务器保持了大量TIME_WAIT状态的原因：
TIME_WAIT是主动关闭连接的一方保持的状态，对于爬虫服务器来说他本身就是“客户端”，在完成一个爬取任务之后，他就 会发起主动关闭连接，从而进入TIME_WAIT的状态，然后在保持这个状态2MSL（max segment lifetime）时间之后，彻底关闭回收资源。
而对于HTTP的交互跟上面画的那个图是不一样的，`关闭连接的不是客户端，而是服务器`，所以web服务器也是会出现大量的TIME_WAIT的情况的。解决思路很简单，就是让服务器能够快速回收和重用那些TIME_WAIT的资源，通过修改`/etc/sysctl.conf`文件实现。
net.ipv4.tcp_tw_reuse = 1   # 表示开启重用。允许将TIME-WAIT sockets重新用于新的TCP连接，默认为0，表示关闭  
net.ipv4.tcp_tw_recycle = 1  # 表示开启TCP连接中TIME-WAIT sockets的快速回收，默认为0，表示关闭  

服务器保持了大量CLOSE_WAIT状态的原因：
TIME_WAIT状态可以通过优化服务器参数得到解决，因为发生TIME_WAIT的情况是服务器自己可控的，要么就是对方连接的异常，要么就是自己没有迅速回收资源，总之不是由于自己程序错误导致的。
如果一直保持在CLOSE_WAIT状态，那么只有一种情况，就是在对方关闭连接之后服务器程序自己没有进一步发出ack信号。换句话说，就是在对方连接关闭之后，程序里没有检测到，或者程序压根就忘记了这个时候需要关闭连接，于是这个资源就一直被程序占着。
如：服 务器A是一台爬虫服务器，它使用简单的HttpClient去请求资源服务器B上面的apache获取文件资源，正常情况下，如果请求成功，那么在抓取完资源后，服务器A会主动发出关闭连接的请求，这个时候就是主动关闭连接，服务器A的连接状态我们可以看到是TIME_WAIT。如果一旦发生异常呢？假设 请求的资源服务器B上并不存在，那么这个时候就会由服务器B发出关闭连接的请求，服务器A就是被动的关闭了连接，如果服务器A被动关闭连接之后程序员忘了 让HttpClient释放连接，那就会造成CLOSE_WAIT的状态了。