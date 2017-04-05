# PHP中删除数组元素后，如何重建索引？

`unset()`函数允许删除数组中的某个键。但要注意数组将不会重建索引。如果需要删除后重建索引，可以用 `array_values()` 函数：

```php
$a = array(1 => 'one', 2 => 'two', 3 => 'three');
unset($a[2]);
/* will produce an array that would have been defined as
   $a = array(1 => 'one', 3 => 'three');
   and NOT
   $a = array(1 => 'one', 2 =>'three');
*/

$b = array_values($a);
// Now $b is array(0 => 'one', 1 =>'three')
```