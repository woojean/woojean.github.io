# global关键字

如果希望一个在函数内部创建的变量具有全局作用域，可以使用global关键字：

```php
function fn(){
global $var;
$var = “XX”;
}
fn();
echo $var;
```
因为这个变量被明确的声明为全局变量，因此在函数调用结束后变量在函数外部也存在。