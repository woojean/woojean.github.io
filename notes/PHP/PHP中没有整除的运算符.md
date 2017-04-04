# PHP中没有整除的运算符

1/2 产生出float 0.5。值可以舍弃小数部分强制转换为integer，或者使用round()函数可以更好地进行四舍五入。

```php
<?php
var_dump(25/7);         	// float(3.5714285714286) 
var_dump((int) (25/7)); 	// int(3)
var_dump(round(25/7));  	// float(4) 
?> 
```