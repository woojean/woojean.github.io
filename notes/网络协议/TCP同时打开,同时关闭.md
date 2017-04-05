# TCP同时打开,同时关闭

两个应用程序同时执行主动打开的情况是可能的，虽然发生的可能性较低。每一端都发送一个SYN,并传递给对方，且每一端都使用对端所知的端口作为本地端口。例如：
主机a中一应用程序使用7777作为本地端口，并连接到主机b 8888端口做主动打开。
主机b中一应用程序使用8888作为本地端口，并连接到主机a 7777端口做主动打开。
`tcp协议在遇到这种情况时，只会打开一条连接`。
这个连接的建立过程需要4次数据交换，而一个典型的连接建立只需要3次交换（即3次握手）
但多数伯克利版的tcp/ip实现并不支持同时打开。
![image](https://github.com/woojean/woojean.github.io/blob/master/images/net_10.png)

如果应用程序同时发送FIN，则在发送后会首先进入FIN_WAIT_1状态。在收到对端的FIN后，回复一个ACK，会进入CLOSING状态。在收到对端的ACK后，进入TIME_WAIT状态。这种情况称为同时关闭。
同时关闭也需要有4次报文交换，与典型的关闭相同。
![image](https://github.com/woojean/woojean.github.io/blob/master/images/net_11.png)