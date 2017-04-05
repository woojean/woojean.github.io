# PHP对象注入漏洞的原因是什么？

PHP支持`对象的序列化和反序列化`操作（serialize、unserialize）。
如：

```php
class User{

  public $age = 0;
  public $name = '';

  public function PrintData(){
    echo 'User ' . $this->name . ' is ' . $this->age . ' years old. <br />';
  }
}

$usr = unserialize('O:4:"User":2:{s:3:"age";i:20;s:4:"name";s:4:"John";}');
$usr->PrintData();
```

输出：
User John is 20 years old. 

`当一个对象进行序列化和反序列化操作时也会自动调用其他相应的魔幻方法`：
当对象进行序列化操作时魔幻方法“__sleep”会被自动调用。（必须返回一个包含序列化的类变量名的数组）
当对象进行反序列化操作时魔幻方法“__wakeup”会被自动调用。
反序列化操作自动调用__wakeup和__destruct，攻击者可以操作类变量来攻击web应用，比如：
$usr = unserialize('O:7:"LogFile":1:{s:8:"filename";s:9:".htaccess";}');
$usr->PrintData();
从而`意外地执行了`LogFile的__construct和__destruct。
在处理由用户提供数据的地方不要使用“unserialize”，可以使用“json_decode”。