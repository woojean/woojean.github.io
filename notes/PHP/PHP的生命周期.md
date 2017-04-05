# PHP的生命周期

两种init和两种shutdown各会执行多少次、各自的执行频率有多少取决于PHP是用什么sapi与宿主通信的。最常见的四种sapi通信方式如下：

 1.直接以CLI/CGI模式调用

 PHP的生命周期完全在一个单独的请求中完成，两种init和两种shutdown仍然都会被执行。

 以`$php -f test.php`为例，执行过程如下：

 （1）调用每个扩展的MINIT；

 （2）请求test.php文件；

 （3）调用每个扩展的RINIT；

 （4）执行test.php；

 （5）调用每个扩展的RSHUTDOWN；

 （6）执行清理操作；

 （7）调用每个扩展的MSHUTDOWN；

 （8）终止php；

 2.多进程模块

 如编译成Apache2的Pre-fork MPM，当Apache启动的时候，会立即把自己fork出好几个子进程，每一个进程都有自己独立的内存空间，在每个进程里的PHP的工作方式如下：

 （1）调用每个扩展的MINIT；

 （2）循环：a.调用每个扩展的RINIT; b.执行脚本；c.调用每个扩展的RSHUTDOWN；

 （3）调用每个扩展的MSHUTDOWN；

 3.多线程模

 如IIS的isapi和Apache MPM worker，只有一个服务器进程在运行着，但会同时运行很多线程，这样可以减少一些资源开销， 像Module init和Module shutdown就只需要运行一次就行了，一些全局变量也只需要初始化一次， 因为线程独具的特质，使得各个请求之间方便的共享一些数据成为可能。

 4.Embedded(嵌入式，在自己的C程序中调用Zend Engine)

 Embed SAPI是一种比较特殊但不常用的sapi，允许在C/C++语言中调用PHP/ZE提供的函数。 这种sapi和上面的三种一样，按Module Init、Request Init、Rshutdown、mshutdown的流程执行着。