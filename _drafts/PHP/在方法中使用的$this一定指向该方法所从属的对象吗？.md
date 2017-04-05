# 在方法中使用的$this一定指向该方法所从属的对象吗？

$this是一个到主叫对象的引用（通常是该方法所从属的对象，但`如果是从第二个对象静态调用时也可能是另一个对象`）。

```php
class A{
  function foo(){
    if (isset($this)) {
      echo '$this is defined (';
      echo get_class($this);
      echo ")\n";
    } else {
      echo "\$this is not defined.\n";
    }
  }
}

class B{
  function bar(){
    // Note: the next line will issue a warning if E_STRICT is enabled.
    A::foo();
  }
}

$b = new B();
$b->bar();
```

输出：$this is defined (B)