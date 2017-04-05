# 什么是declare结构？

declare结构用来设定一段代码的执行指令。declare 的语法和其它流程控制结构相似： 

```php
declare (directive)
  statement
```
directive部分允许设定declare代码段的行为。目前只认识两个指令：ticks和encoding。

declare结构也可用于全局范围，影响到其后的所有代码（但如果有 declare 结构的文件被其它文件包含，则对包含它的父文件不起作用）。
例：
```php
// you can use this:
declare(ticks=1) {
  // entire script here
}

// or you can use this:
declare(ticks=1);
// entire script here
```
`Tick`（时钟周期）是一个在declare代码段中解释器`每执行N条可计时的低级语句就会发生的事件`。N的值是在declare中的directive部分用ticks=N来指定的。 
不是所有语句都可计时。通常条件表达式和参数表达式都不可计时。 
例：
```php
declare(ticks=1);

// A function called on each tick event
function tick_handler(){
  echo "tick_handler() called<br/>";
}

register_tick_function('tick_handler');
```
输出：
tick_handler() called
tick_handler() called
tick_handler() called

可以用 encoding 指令来对每段脚本指定其编码方式。 
```php
<?php
declare(encoding='ISO-8859-1');
// code here
?> 
```