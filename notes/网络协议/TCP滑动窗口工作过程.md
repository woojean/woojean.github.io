# TCP滑动窗口工作过程

根据 B 给出的窗口值
【1】A 构造出自己的发送窗口 
![image](https://github.com/woojean/woojean.github.io/blob/master/images/wangluo13.png)

【2】A 发送了 11 个字节的数据 
![image](https://github.com/woojean/woojean.github.io/blob/master/images/wangluo14.png)

P3 – P1 = A 的发送窗口（又称为通知窗口）
P2 – P1 = 已发送但尚未收到确认的字节数
P3 – P2 = 允许发送但尚未发送的字节数（又称为可用窗口） 
【3】A 收到新的确认号，发送窗口向前滑动 
![image](https://github.com/woojean/woojean.github.io/blob/master/images/wangluo15.png)

【4】A 的发送窗口内的序号都已用完，但还没有再收到确认，必须停止发送。 
![image](https://github.com/woojean/woojean.github.io/blob/master/images/wangluo16.png)



## 发送缓存与接收缓存的作用

发送缓存用来暂时存放：
 1.发送应用程序传送给发送方 TCP 准备发送的数据；
 2.TCP 已发送出但尚未收到确认的数据。
接收缓存用来暂时存放：
 1.按序到达的、但尚未被接收应用程序读取的数据；
 2.不按序到达的数据。

## 需要强调三点

1.A 的发送窗口并不总是和 B 的接收窗口一样大（因为有一定的时间滞后）。
2.TCP 标准没有规定对不按序到达的数据应如何处理。通常是先临时存放在接收窗口中，等到字节流中所缺少的字节收到后，再按序交付上层的应用进程。
3.TCP 要求接收方必须有累积确认的功能，这样可以减小传输开销。 