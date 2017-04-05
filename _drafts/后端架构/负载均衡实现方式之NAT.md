# 负载均衡实现方式之NAT

NAT工作在传输层，可以对数据包中的IP地址和端口信息进行修改，也称四层负载均衡。
Linux内核中的Netfilter模块可以修改IP数据包，它在内核中维护着一些数据包过滤表，这些表包含了用于控制数据包过滤的规则。当网络数据包到达服务器的网卡并且进入某个进程的地址空间之前先要通过内核缓冲区，此时Netfilter便对数据包有着绝对的控制权，可以修改数据包、改变路由规则。
Netfilter位于内核中，Linux提供了命令行工具iptables来对Netfilter的过滤表进行操作。

使用iptables为Web服务器配置防火墙，只允许外部网络通过TCP与当前机器的80端口建立连接：
```
iptables -F INPUT
iptables -A INPUT -i eth0 -p tcp --dport 80 -j ACCEPT
iptables -P INPUT DROP
```

使用iptables实现本机端口重定向，将所有从外网进入80端口的请求转移到8000端口：
```
iptables -t nat -A PREROUTING -i eth0 -p tcp --dport 80 -j REJECT -- to-port 8000
```

使用iptables实现NAT，将外网网卡8001端口接收的所有请求转发给10.0.1.210这台服务器的8000端口：
```
echo 1 > /proc/sys/net/ipv4/ip_forward  # 打开调度器的数据包转发选项
iptables -t nat -A PREROUTING -i eth0 -p tcp --dport 8001 -j DNAT -- to-destination 10.0.1.210:8000
```
注意，还需要同时将实际服务器的默认网关设置为NAT服务器（NAT服务器必须为实际服务器的网关）：
```
route add default gw 10.0.1.50
```