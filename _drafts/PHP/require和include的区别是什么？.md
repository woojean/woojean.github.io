# require和include的区别是什么？

`处理失败的方式不同`。require 在出错时产生E_COMPILE_ERROR 级别的错误。换句话说将导致脚本中止而include只产生警告（E_WARNING），脚本会继续运行。

在失败时include返回FALSE并且发出警告。成功的包含则返回1，除非在包含文件中另外给出了返回值。

因为 include 是一个特殊的语言结构，其参数不需要括号。在比较其返回值时要注意。 
```
if ((include 'vars.php') == 'OK') {
    echo 'OK';
}
```
如果在包含文件中定义有函数，这些函数不管是在return之前还是之后定义的，都可以独立在主文件中使用。

require()和include()只是php中的一种语言特性，而不是函数。用于指定的文件代替语句本身，就象C语言中的include()语句一样。如果php配置文件php.ini中的URL fopen wrappers 是打开的(默认情况下是打开的)，就可以`使用URL来指定文件的位置`从而实现远程文件的调用。如：
	require("http://some_server/file.php?varfirst=1&varsecond=2"); 
区别：
1.require()语句会无条件地读取它所包含的文件的内容，而不管这些语句是否执行（`比如在判断为false的分支语句中依然会执行`）。如果你想按照不同的条件包含不同的文件，就必须使用include()语句。
2.require一个文件存在错误的话，那么程序就会中断执行了，并显示致命错误。include一个文件存在错误的话，那么程序不会中端，而是继续执行，并显示一个警告错误。
3.`include有返回值，而require没有`。
注：
（1）require_once()和include_once()语句分别对应于require()和include()语句。require_once()和include_once()语句主要用于需要包含多个文件时，可以有效地避免把同一段代码包含进去而出现函数或变量重复定义的错误。
（2）有一点就是使用require()和include()语句时要特别的注意。那就是在被包含的文件中，处理器是按照html模式来解释其中的内容的（无论被包含文件的扩展名是什么），处理完被包含的内容后又恢复到php模式。所以如果需要在被包含文件中使用php语法，就要使用正确的php开始和结束标记来把这些语句包含进去。 
（3）require()和include()语句中的变量继承require()和include()语句所在位置的变量作用域。所有在require()和include()语句的位置可以访问的变量，在require()和include()语句所包含的文件中都可以访问。如果require()和include()语句位于一个函数内部，那么被包含文件内的语句都相当于定义在函数内部。