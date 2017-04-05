# PHP中函数调用不区分大小写

```php
<?php   
	function func(){
		echo "X<br/>";
	}

	func();
	Func();
	fUNc();
?>
```
将输出：
X
X
X

PHP`不支持函数重载`，所以自定义函数不能和内置函数或者用户已定义的函数重名。