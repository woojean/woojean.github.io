# DHCP协议的工作过程

动态主机配置协议 DHCP 提供了即插即用连网(plug-and-play networking)的机制。
这种机制允许一台计算机加入新的网络和获取IP地址而不用手工参与。

## DHCP 使用客户服务器方式
1.需要 IP 地址的主机在启动时就向 DHCP 服务器广播发送发现报文（DHCPDISCOVER），这时该主机就成为 DHCP 客户。
2.本地网络上所有主机都能收到此广播报文，但只有 DHCP 服务器才回答此广播报文。
3.DHCP 服务器先在其数据库中查找该计算机的配置信息。若找到，则返回找到的信息。若找不到，则从服务器的 IP 地址池(address pool)中取一个地址分配给该计算机。DHCP 服务器的回答报文叫做提供报文（DHCPOFFER）。

## DHCP 中继代理(relay agent) 
并不是每个网络上都有 DHCP 服务器，这样会使 DHCP 服务器的数量太多。现在是每一个网络至少有一个 DHCP 中继代理，它配置了 DHCP 服务器的 IP 地址信息。
当 DHCP 中继代理收到主机发送的发现报文后，就以单播方式向 DHCP 服务器转发此报文，并等待其回答。收到 DHCP 服务器回答的提供报文后，DHCP 中继代理再将此提供报文发回给主机。

## 租用期(lease period) 
DHCP 服务器分配给 DHCP 客户的 IP 地址的临时的，因此 DHCP 客户只能在一段有限的时间内使用这个分配到的 IP 地址。DHCP 协议称这段时间为租用期。

## DHCP 协议的工作过程 
![image](https://github.com/woojean/woojean.github.io/blob/master/images/wangluo26.png)

1.DHCP 服务器被动打开 UDP 端口 67，等待客户端发来的报文。
2.DHCP 客户从 UDP 端口 68发送 DHCP 发现报文。
3.凡收到 DHCP 发现报文的 DHCP 服务器都发出 DHCP 提供报文，因此 DHCP 客户可能收到多个 DHCP 提供报文。
4.DHCP 客户从几个 DHCP 服务器中选择其中的一个，并向所选择的 DHCP 服务器发送 DHCP 请求报文。
5.被选择的 DHCP 服务器发送确认报文DHCPACK，进入已绑定状态，并可开始使用得到的临时 IP 地址了。
DHCP 客户现在要根据服务器提供的租用期 T 设置两个计时器 T1 和 T2，它们的超时时间分别是 0.5T 和 0.875T。当超时时间到就要请求更新租用期。
6.租用期过了一半（T1 时间到），DHCP 发送请求报文 DHCPREQUEST 要求更新租用期。
7.DHCP 服务器若同意，则发回确认报文DHCPACK。DHCP 客户得到了新的租用期，重新设置计时器。
8.DHCP 服务器若不同意，则发回否认报文DHCPNACK。这时 DHCP 客户必须立即停止使用原来的 IP 地址，而必须重新申请 IP 地址（回到步骤2）。
若DHCP服务器不响应步骤6的请求报文DHCPREQUEST，则在租用期过了 87.5% 时，DHCP 客户必须重新发送请求报文 DHCPREQUEST（重复步骤6），然后又继续后面的步骤。
9.DHCP 客户可随时提前终止服务器所提供的租用期，这时只需向 DHCP 服务器发送释放报文 DHCPRELEASE 即可。