# 在PHP中，如何控制数组json_encode后为json对象或者json数组？

```php
$foo = array(
  "item1" => (object)[],
  "item2" => []
);

echo json_encode($foo);
```
输出：
```php
{"item1":{},"item2":[]}
```