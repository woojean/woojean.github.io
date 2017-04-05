# PHP会在输出时自动删除其结束符?>后的一个换行

例1：

```php
<?php   
	echo "XXX";
?>YYY
```
输出：
XXXYYY

例2：一个换行，会被直接删除
```php
<?php   
	echo "XXX";
?>
YYY
```
输出：
XXXYYY

例3：`多个换行，效果等同于一个空格`
```php
<?php   
	echo "XXX";
?>

YYY
```
输出：
XXX YYY