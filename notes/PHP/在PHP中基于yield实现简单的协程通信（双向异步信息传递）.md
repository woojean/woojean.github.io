# 在PHP中基于yield实现简单的协程通信（`双向`异步信息传递）

包含yield关键字的函数比较特殊，返回值是一个`Generator对象`，此时函数内语句尚未真正执行。Generator对象是Iterator接口实例，可以通过rewind()、current()、next()、valid()系列接口进行操纵。`Generator可以视为一种“可中断”的函数，而yield构成了一系列的“中断点”`。Generator类似于车间生产的流水线，每次需要用产品的时候才从那里取一个，然后这个流水线就停在那里等待下一次取操作。

```php
<?php

function gen() {
	for($i=1;$i<=100;$i++) {
		$cmd = (yield $i);  // `yield既是语句，又是表达式`，既具备类似return语句的功能，同时也有类似表达式的返回值（通过send得到的值）
		if($cmd=='stop') {
			return;
		}
	}s
}

$gen = gen();
$i=0;
foreach($gen as $item) {
	echo $item."\n";
	if($i>=10) {
		$gen->send('stop');
	}
	$i++;
}
```