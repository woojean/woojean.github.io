# 网际控制报文协议ICMP

## ICMP 报文的格式 

![image](https://github.com/woojean/woojean.github.io/blob/master/images/wangluo8.png)

ICMP 报文的种类有两种，即 ICMP 差错报告报文和 ICMP 询问报文。
ICMP 差错报告报文共有 5 种：
终点不可达 
源点抑制(Source quench)  
时间超过 
参数问题 
改变路由（重定向）(Redirect)   

## 不应发送 ICMP 差错报告报文的几种情况 

1.对 ICMP 差错报告报文不再发送 ICMP 差错报告报文。
2.对第一个分片的数据报片的所有后续数据报片都不发送 ICMP 差错报告报文。
3.对具有多播地址的数据报都不发送 ICMP 差错报告报文。
4.对具有特殊地址（如127.0.0.0 或 0.0.0.0）的数据报不发送 ICMP 差错报告报文。

## ICMP 询问报文有两种 

回送请求和回答报文
时间戳请求和回答报文

## PING

PING 用来测试两个主机之间的连通性。
PING 使用了 ICMP 回送请求与回送回答报文。
PING 是应用层直接使用网络层 ICMP 的例子，它没有通过运输层的 TCP 或UDP。