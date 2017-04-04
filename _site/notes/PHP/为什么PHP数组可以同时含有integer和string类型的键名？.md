# 为什么PHP数组可以同时含有integer和string类型的键名？

PHP 数组可以同时含有integer和string类型的键名，因为`PHP实际并不区分索引数组和关联数组`。如果对给出的值没有指定键名，则取当前最大的整数索引值，而新的键名将是该值加一。如果指定的键名已经有了值，则该值会被覆盖。 
例：

```php
$array = array(
         "a",
         "b",
    6 => "c",
         "d",
);
var_dump($array);
```
将输出：
array(4) {
  [0]=>
  string(1) "a"
  [1]=>
  string(1) "b"
  [6]=>
  string(1) "c"
  [7]=>
  string(1) "d"
}
再比如：
```php
$a = array( 'color' => 'red',
	'taste' => 'sweet',
	'shape' => 'round',
	'name'  => 'apple',
	4        // key will be 0
);
```