# 如何使用list()来解包嵌套的数组？

```php
$array = [
  [1, 2],
  [3, 4],
];

foreach ($array as list($a, $b)) {
  // $a contains the first element of the nested array,
  // and $b contains the second element.
  echo "A: $a; B: $b\n";
}
```
输出：
A: 1; B: 2
A: 3; B: 4

list() 中的单元可以少于嵌套数组（但是不能多于），此时多出来的数组单元将被忽略：
```php
$array = [
  [1, 2],
  [3, 4],
];

foreach ($array as list($a)) {
  // Note that there is no $b here.
  echo "$a\n";
}
```
输出：
1
3