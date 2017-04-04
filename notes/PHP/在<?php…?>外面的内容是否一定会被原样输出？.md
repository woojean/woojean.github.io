# 在<?php…?>外面的内容是否一定会被原样输出？

当PHP解释器碰到?>结束标记时就简单地将其后内容原样输出，直到碰到下一个开始标记；例外是处于条件语句中间时，此时 PHP 解释器会根据条件判断来决定哪些输出，哪些跳过：

```php
<?php if ($expression == true): ?>
  This will show if the expression is true.
<?php else: ?>
  Otherwise this will show.
<?php endif; ?> 
```
上例中PHP将跳过条件语句未达成的段落，即使该段落位于 PHP 开始和结束标记之外。

`如果文件内容是纯PHP代码，最好在文件末尾删除PHP结束标记`。这可以避免在PHP结束标记之后万一意外加入了空格或者换行符，会导致PHP开始输出这些空白，而脚本中此时并无输出的意图。

例：
```php
<?php   
	echo "XXX";
?> 
```
这里在结尾处多了一个空格和一个换行
将输出：
```php
<body>XXX 
</body>
```
即，在XXX后面多了一个空格，并换行

而：
```php
<?php   
	echo "XXX";
将输出：
<body>XXX</body>
```

一段PHP代码中的结束标记隐含表示了一个分号
例：
```php
<?php   
	echo "XXX"
```
输出：
```
Parse error: syntax error, unexpected $end, expecting ',' or ';' in D:\wamp\www\t.php on line 2
```
```php
<?php   
	echo "XXX"
?>
```
输出：
```
XXX
```