# 因特网网络层安全协议IPsec与SA

## IPsec 与安全关联 SA
IPsec 中最主要的两个部分 
鉴别首部 AH (Authentication Header)： AH鉴别源点和检查数据完整性，但不能保密。
封装安全有效载荷 ESP (Encapsulation Security Payload)：ESP 比 AH 复杂得多，它鉴别源点、检查数据完整性和提供保密。

## 安全关联 SA (Security Association) 
在使用 AH 或 ESP 之前，先要从源主机到目的主机建立一条网络层的逻辑连接。此逻辑连接叫做安全关联 SA。
 IPsec 就把传统的因特网无连接的网络层转换为具有逻辑连接的层。