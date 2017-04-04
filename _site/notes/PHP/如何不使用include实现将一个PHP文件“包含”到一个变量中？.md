# 如何不使用include实现将一个PHP文件“包含”到一个变量中？

用输出控制函数结合 include 来捕获其输出
例：使用`输出缓冲`来将 PHP 文件包含入一个字符串

```php
$string = get_include_contents('somefile.php');

function get_include_contents($filename) {
  if (is_file($filename)) {
    ob_start();
    include $filename;
    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
  }
  return false;
}
```