# 负载均衡实现方式之IPVS

IP Virtual Server，类似于Netfilter，也工作于Linux内核中，但是更专注于实现IP负载均衡。不仅可以实现NAT的负载均衡，还可以实现直接路由、IP隧道等负载均衡。
Linux提供ipvsadm工具来管理IPVS，可以用来快速实现负载均衡系统，也称为LVS（Linux Virtual Server）：

```
echo 1 > /proc/sys/net/ipv4/ip_forward  # 打开调度器的数据包转发选项
route add default gw 10.0.1.50  # 将实际服务器的默认网关设置为NAT服务器

ipvsadm -A -t 125.12.12.12:80 -s rr  # 添加一台虚拟服务器（负载均衡调度器）
ipvsadm -a -t 125.12.12.12:80 -r 10.0.1.210:8000 -m  # 实际服务器
ipvsadm -a -t 125.12.12.12:80 -r 10.0.1.211:8000 -m  # 实际服务器
```
LVS还提供一系列的动态调度策略，如LC（最小连接）、WLC（带权重的最小连接）、SED（最短期望时间延迟）等。

瓶颈：NAT负载均衡服务器的转发能力主要取决于NAT服务器的网络带宽，包括内部网络和外部网络。