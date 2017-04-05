# 因特网运输层安全协议SSL

## 安全套接层 SSL
SSL 是安全套接层 (Secure Socket Layer)，可对万维网客户与服务器之间传送的数据进行加密和鉴别。
SSL 在双方的联络阶段协商将使用的加密算法和密钥，以及客户与服务器之间的鉴别。在联络阶段完成之后，所有传送的数据都使用在联络阶段商定的会话密钥。SSL 不仅被所有常用的浏览器和万维网服务器所支持，而且也是运输层安全协议 TLS (Transport Layer Security)的基础。

## SSL 的位置 
![image](https://github.com/woojean/woojean.github.io/blob/master/images/wangluo35.png)
1.在发送方，SSL 接收应用层的数据（如 HTTP 或 IMAP 报文），对数据进行加密，然后把加了密的数据送往 TCP 套接字。
2.在接收方，SSL 从 TCP 套接字读取数据，解密后把数据交给应用层。

## SSL 提供以下三个功能 
(1) SSL 服务器鉴别    允许用户证实服务器的身份。具有 SS L 功能的浏览器维持一个表，上面有一些可信赖的认证中心 CA (Certificate Authority)和它们的公钥。
(2) 加密的 SSL 会话    客户和服务器交互的所有数据都在发送方加密，在接收方解密。
(3) SSL 客户鉴别    允许服务器证实客户的身份。

## 安全电子交易 SET (Secure Electronic Transaction)
SET 的主要特点是：
(1) SET 是专为与支付有关的报文进行加密的。
(2) SET 协议涉及到三方，即顾客、商家和商业银行。所有在这三方之间交互的敏感信息都被加密。
(3) SET 要求这三方都有证书。在 SET 交易中，商家看不见顾客传送给商业银行的信用卡号码。  