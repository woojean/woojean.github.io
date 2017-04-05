# PHP的启动与终止

无论是web模式还是cli模式运行，PHP的工作原理都是一样的， 都是作为一种SAPI在运行（Server Application Programming Interface： the API used by PHP to interface with Web Servers）。`SAPI就是PHP和外部环境的代理器`。它把外部环境抽象后, 为内部的PHP提供一套固定的，统一的接口，使得PHP自身实现能够不受错综复杂的外部环境影响，保持一定的独立性。

PHP程序的启动可以看作有两个概念上的启动，终止也有两个概念上的终止。 以Apache举例，其中一个是`PHP作为Apache的一个模块的启动与终止`， 这次启动php会初始化一些必要数据，比如与宿主Apache有关的，并且这些数据是常驻内存的！ 终止与之相对。 还有一个概念上的启动就是当Apache分配一个页面请求过来的时候，PHP会有一次启动与终止，这也是我们最常讨论的一种。

在PHP随着Apache的启动而常驻在内存里时， 会把自己所有已加载扩展的`MINIT方法`(全称Module Initialization，是由每个模块自己定义的函数)都执行一遍。 在这个时间里，扩展可以定义一些自己的常量、类、资源等所有会被用户端的PHP脚本用到的东西。 

当一个页面请求到来时候，PHP会迅速的开辟一个新的环境，并重新扫描自己的各个扩展， 遍历执行它们各自的`RINIT方法`(俗称Request Initialization)， 这时候一个扩展可能会初始化在本次请求中会使用到的变量等， 还会初始化稍后用户端（即PHP脚本）中的变量之类的。

当请求完成（或者别die等终结），PHP便会启动回收程序，执行所有已加载扩展的`RSHUTDOWN`（Request Shutdown）方法， 这时候扩展可以抓紧利用内核中的变量表之类的做一些事情， 因为一旦PHP把所有扩展的RSHUTDOWN方法执行完， 便会释放掉这次请求使用过的所有东西， 包括变量表的所有变量、所有在这次请求中申请的内存等等。

当Apache通知PHP自己要Stop的时候，PHP便进入`MSHUTDOWN`（Module Shutdown）阶段。一旦PHP把扩展的MSHUTDOWN执行完，便会进入自毁程序，所以这里一定要把自己擅自申请的内存给释放掉。

一个最简单的例子：

walu.c 
```c
 int time_of_minit;  // 每次请求都不变
 PHP_MINIT_FUNCTION(walu)
 {
     time_of_minit=time(NULL);
     return SUCCESS;
 }

 int time_of_rinit;  // 每次请求都改变
 PHP_RINIT_FUNCTION(walu)
 {
     time_of_rinit=time(NULL);
     return SUCCESS;
 }

 // 每次页面请求都会往time_rshutdown.txt中写入数据
 PHP_RSHUTDOWN_FUNCTION(walu)
 {
     FILE *fp=fopen("/cnan/www/erzha/time_rshutdown.txt","a+");
     fprintf(fp,"%d\n",time(NULL));
     fclose(fp);
     return SUCCESS;
 }

 // 只有在apache结束后time_mshutdown.txt才写入有数据
 PHP_MSHUTDOWN_FUNCTION(walu)
 {
     FILE *fp=fopen("/cnan/www/erzha/time_mshutdown.txt","a+");
     fprintf(fp,"%d\n",time(NULL));
     return SUCCESS;
 }

 PHP_FUNCTION(walu_test)
 {
     php_printf("%d&lt;br /&gt;",time_of_minit);
     php_printf("%d&lt;br /&gt;",time_of_rinit);
     return;
 }
```