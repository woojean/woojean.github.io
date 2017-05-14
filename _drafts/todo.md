

  

## ES6与ES5，this的变化比较（参考typescript文档 类类型）

# js 类静态部分与实例部分的区别（参考typescript文档 类类型）


## 使用Swoole后PHP开发的一些变化理解

使用Swoole扩展执行的PHP脚本需要预先在服务端执行（而不是每次访问时才会执行）。

比如程序中定义了一个类A，在传统模式下每次有用户访问时，类A都需要提前编译到内存中，1万次访问就要编译1万次。而用swoole则只需要在服务端编译一次，只要Swoole的进程存在，类A就会一直存在于内存中。

再比如PHP入门时就必须要掌握的session，对于运用了Swoole扩展的PHP程序而言，完全可以用一个变量来替换。

再比如：平时写PHP代码，完全不必担心内存使用，全局变量/函数/对象等，可以随便使用，因为PHP脚本执行结束后，内存自然会自行释放掉。但用Swoole扩展的PHP程序，则必然要手动注销全局的变量/函数/对象等。

PHP在fork子进程的时候，父进程的资源连接会被子进程获得，父进程本身会断掉。要解决这个问题只能在fork之后重新建连接。

一定不可以多进程或多线程共同一个mysql或redis连接，否则消息会串。每个进程或线程创建一个mysql的连接。连接断掉也就是mysql gone away之后进行重连。子进程退出后php引擎回清理回收所有的内存，关闭所有的连接。然后由于子进程和父进程共享内存，所以父进程里建立的连接等等也会被顺带关闭掉。

fpm本身是leader follower同步阻塞模型，同一时间只能处理一个请求，支持不了异步。


# 如何不直接使用include返回赋值实现将一个PHP文件的执行结果赋值到一个变量中？

用**输出控制函数**结合include来捕获其输出。
例：使用`输出缓冲`来将 PHP 文件包含入一个字符串

```php
<?php

function get_include_contents($filename) {
  if (is_file($filename)) {
    ob_start();
    include $filename;
    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
  }
  return false;
}

$string = get_include_contents('somefile.php');
```


专业书籍可以看看《组织行为学》，人大出版社的， 
以及人大劳人院的系列教材。这些是科普性质 系统化的讲解 
另外推荐彼得德鲁克的《卓有成效的管理者》管理学经典，总会有一个点触动你 
以及google的《work rules重新定义团队》打开管理思路 
  

  焦虑 = 未来的不确定性 x 事情的重要性 x 自己无能为力的程度。