# foreach循环中使用引用有什么潜在问题？

```php
$array = [1, 2, 3]; 
echo implode(',', $array), "<br/>"; 	// 1,2,3

foreach ($array as &$value) {}    
echo implode(',', $array), "<br/>"; 	// 1,2,3	

foreach ($array as $value) {}     	 
echo implode(',', $array), "<br/>";		// 1,2,2
```

第一个循环过后，$value是数组中最后一个元素的引用。
第二个循环开始：
第1步：复制$arr[0]到$value（注意此时$value是$arr[2]的引用），这时数组变成[1,2,1]
第2步：复制$arr[1]到$value，这时数组变成[1,2,2]
第3步：复制$arr[2]到$value，这时数组变成[1,2,2]
综上，最终结果就是1,2,2
避免这种错误最好的办法就是在循环后立即用unset函数销毁变量：
```php
$arr = array(1, 2, 3, 4); 
foreach ($arr as &$value) { 
    $value = $value * 2; 
} 
unset($value);
```