# __autoload和spl_autoload_register()函数直接有什么区别和联系？

void __autoload ( string $class )
可以通过定义这个函数来启用类的自动加载，参数为待加载的类名。

例如：
```php
./myClass.php
<?php
class myClass {
    public function __construct() {
        echo "myClass init'ed successfuly!!!";
    }
}
?>

./index.php
<?php
function __autoload($classname) {
    $filename = "./". $classname .".php";			# 拼凑查找类文件的逻辑
    include_once($filename);
}

$obj = new myClass();							# 直接使用类名，会触发对__autoload函数的调用
?>

bool spl_autoload_register ([ callable $autoload_function [, bool $throw = true [, bool $prepend = false ]]] )
```
将函数注册到SPL __autoload函数队列中。如果该队列中的函数尚未激活，则激活它们。
如果在程序中已经实现了__autoload()函数，它必须显式注册到__autoload()队列中。因为 spl_autoload_register()函数会将Zend Engine中的__autoload()函数取代为spl_autoload()或spl_autoload_call()。

spl_autoload_register 可以很好地处理需要`多个加载器`的情况，这种情况下spl_autoload_register会`按顺序依次调用`之前注册过的加载器。作为对比， __autoload 因为是一个函数，所以只能被定义一次。

例如：
```php
function loadprint( $class ) {
 	$file = $class . '.class.php';  
 	if (is_file($file)) {  
  		require_once($file);  
 	} 
} 

spl_autoload_register( 'loadprint' ); 

$obj = new PRINTIT();
$obj->doPrint();
```
将__autoload换成loadprint函数。但是loadprint不会像__autoload自动触发，这时spl_autoload_register()就起作用了，它告诉PHP碰到没有定义的类就执行loadprint()。 

例：spl_autoload_register() 调用静态方法 
```php
class test {
 	public static function loadprint( $class ) {
  		$file = $class . '.class.php';  
  		if (is_file($file)) {  
   			require_once($file);  
  		} 
 	}
} 

spl_autoload_register(array('test','loadprint'));
// 另一种写法：spl_autoload_register("test::loadprint"); 

$obj = new PRINTIT();
$obj->doPrint();
```