# swoole如何处理高并发

Reactor模型：IO多路复用异步非阻塞程序使用经典的Reactor模型，它本身不处理任何数据收发，只是可以监视一个socket(也可以是管道、eventfd、信号)句柄的事件变化比如：

1.Add:添加一个Socket到Reactor；

2.Set:修改Socket对应的事件，如可读可写；

3.Del：从Reactor中移除；

4.Callback：事件发生后，回调的函数；

Reactor只是一个事件发生器，实际对socket句柄的操作，如connect/accept、send/recv、close是在callback中完成的。



swoole采用`多线程Reactor+多进程Worker`：

1.Master进程启动一个Main Reactor线程和多个普通Reactor线程；

2.请求到达 Main Reactor；

3.Main Reactor根据Reactor的情况，将请求注册给对应的Reactor(每个Reactor都有epoll。用来监听客户端的变化)

4.客户端有变化时，交给worker来处理；

5.worker处理完毕，通过进程间通信(比如管道、共享内存、消息队列)发给对应的reactor。

6.reactor将响应结果发给相应的连接。

7.请求处理完成；

因为reactor基于epoll，所以每个reactor可以处理无数个连接请求。 如此，swoole就轻松的处理了高并发。



swoole的worker进程有2种类型：
**一种是 普通的worker进程，一种是 task worker进程。**worker进程是用来处理普通的耗时不是太长的请求；task worker进程用来处理耗时较长的请求，比如数据库的I/O操作。以异步Mysql举例：

1.耗时较长的Mysql查询进入worker；
2.worker通过管道将这个请求交给task worker来处理；
3.worker再去处理其他请求；
4.task worker处理完毕后，处理结果通过管道返回给worker；
5.worker 将结果返回给reactor；
6.reactor将结果返回给请求方；

如此，通过worker、task worker结合的方式，我们就实现了异步I/O。