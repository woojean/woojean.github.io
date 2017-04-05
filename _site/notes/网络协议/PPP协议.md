# PPP协议

全世界使用得最多的数据链路层协议是点对点协议 PPP (Point-to-Point Protocol)

用户使用拨号电话线接入因特网时，一般都是使用 PPP 协议。  
PPP 协议应满足的需求：简单、封装成帧、透明性、多种网络层协议、多种类型链路、差错检测、检测连接状态、最大传送单元、网络层地址协商、数据压缩协商；
PPP 协议不需要的功能：纠错、流量控制、序号、多点线路、半双工或单工链路；
PPP 协议有三个组成部分：
1.一个将 IP 数据报封装到串行链路的方法。
2.链路控制协议 LCP (Link Control Protocol)。
3.网络控制协议 NCP (Network Control Protocol)。  

## PPP 协议的工作状态

![image](https://github.com/woojean/woojean.github.io/blob/master/images/wangluo2.png)

1.当用户拨号接入 ISP 时，路由器的调制解调器对拨号做出确认，并建立一条物理连接。
2.PC 机向路由器发送一系列的 LCP 分组（封装成多个 PPP 帧）。
3.这些分组及其响应选择一些 PPP 参数，和进行网络层配置，NCP 给新接入的 PC机分配一个临时的 IP 地址，使 PC 机成为因特网上的一个主机。
4.通信完毕时，NCP 释放网络层连接，收回原来分配出去的 IP 地址。接着，LCP 释放数据链路层连接。5.最后释放的是物理层的连接。    

报文格式等，详略。