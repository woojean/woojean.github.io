# Swoole工作原理与优势

swoole的出现使phper可以从web开发跳出，进入了更大的服务器网络编程领域。

swoole运行有个前提条件：必需在cli模式下执行。

cli下执行一个php文件时的关键步骤：

1.调用每个扩展的MINIT；

2.调用每个扩展的RINIT；

3.执行test.php；

4.调用每个扩展的RSHUTDOWN；

5.调用每个扩展的MSHUTDOWN；

fpm每个请求都是在执行2~4步。opcode cache是把第3步的词法分析、语法分析、生成opcode代码这几个操作给缓存起来了，从而达到加速的作用。

Swoole在第3步接管了php，进入swoole的生命周期，以多进程模式为例：

（1）.onStart

在回调此函数之前Swoole Server已进行了如下操作

- 创建了manager进程
- 创建了worker子进程
- 监听所有TCP/UDP端口
- 监听了定时器

`此函数是在主进程回调的`，和worker进程的onWorkStart是并行的没有先后之分，在此回调里强烈要求只做log记录，设置进程名操作，不做业务逻辑,否则业务逻辑代码的错误导致master进程crash,让整个swoole server不对对外提供服务了。

（2）.onWorkStart

每个worker或task进程在启动之后，会回调此函数，由于此回调类似于fpm里的MINIT，所以可以在这里做一个全局的资源加载，框架初始化之类的操作，这样可以对每个请求做全局共享，而达到提升性能的目的。

（3）.onReceive

每个请求（也称数据到达），会回调此函数，然后进行业务逻辑处理，输出结果

（4）.onWorkerStop

worker退出时，会回调此函数。

（5）.onShutDown

swoole服务停止回调此函数,然后继续fpm的第4、5步，进而退出php生命周期。



Apache处理一个请求是**同步阻塞**的模式：每到达一个请求，Apache都会去fork一个子进程去处理这个请求，直到这个请求处理完毕。

epoll代理的原理是这样的：当连接有I/O流事件产生的时候，epoll就会去告诉进程哪个连接有I/O流事件产生，然后进程就去处理这个进程。

有了epoll，理论上1个进程就可以无限数量的连接，而且无需轮询，真正解决了c10k的问题。Nginx是基于epoll的，异步非阻塞的服务器程序。自然，Nginx能够轻松处理百万级的并发连接，也就无可厚非了。