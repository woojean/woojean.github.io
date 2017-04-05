# Nginx支持的IO模型有哪些？

Nginx支持如下处理连接的方法（I/O复用方法），这些方法可以通过use指令指定：
（1）select 如果当前平台没有更有效的方法，它是编译时默认的方法。可以使用配置参数–with-select_module 和 –without-select_module来启用或禁用这个模块。
（2）poll 如果当前平台没有更有效的方法，它是编译时默认的方法。可以使用配置参数–with-poll_module和–without-poll_module来启用或禁用这个模块。
（3）kqueue 高效的方法，使用于FreeBSD 4.1+、 OpenBSD 2.9+、NetBSD 2.0和MacOS X.。使用双处理器的MacOS X系统使用kqueue可能会造成内核崩溃。
（4）epoll 高效的方法，使用于Linux内核2.6版本及以后的系统。在某些发行版本中，如SuSE 8.2, 有让2.4版本的内核支持epoll的补丁。
（5）rtsig 可执行的实时信号，使用于Linux内核版本2.2.19以后的系统。默认情况下整个系统中不能出现大于1024个POSIX实时(排队)信号。这种情况对于高负载的服务器来说是低效的；所以有必要通过调节内核参数 /proc/sys/kernel/rtsig-max来增加队列的大小。可是从Linux内核版本2.6.6-mm2开始， 这个参数就不再使用了，并且对于每个进程有一个独立的信号队列，这个队列的大小可以用 RLIMIT_SIGPENDING 参数调节。当这个队列过于拥塞，nginx就放弃它并且开始使用poll方法来处理连接直到恢复正常。
（6）/dev/poll 高效的方法，使用于 Solaris 7 11/99+, HP/UX 11.22+ (eventport), IRIX 6.5.15+ 和 Tru64 UNIX 5.1A+.
（7）eventport 高效的方法，使用于 Solaris 10。
在linux下面，只有epoll是高效的方法。