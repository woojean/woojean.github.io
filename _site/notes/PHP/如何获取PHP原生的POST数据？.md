# 如何获取PHP原生的POST数据？

$HTTP_RAW_POST_DATA — 原生POST数据
一般而言，使用 `php://input` 代替 $HTTP_RAW_POST_DATA

Traversable、Iterator、IteratorAggregate、ArrayAccess、Serializable等接口的作用是什么？
`Traversable`（遍历）接口：检测一个类是否可以使用foreach进行遍历的接口。 
这是一个无法在PHP脚本中实现的内部引擎接口。这个接口没有任何方法，它的作用仅仅是作为所有可遍历类的基本接口。 IteratorAggregate 或 Iterator 接口可以用来代替它。  

`Iterator`（迭代器）接口：
```php
Iterator  extends Traversable  {
  /* 方法 */
  abstract public mixed current ( void )
  abstract public scalar key ( void )
  abstract public void next ( void )
  abstract public void rewind ( void )
  abstract public boolean valid ( void )
}
```

`IteratorAggregate`（聚合式迭代器）接口：
```php
IteratorAggregate  extends Traversable  {
  /* 方法 */
  abstract public Traversable getIterator ( void )
}
```
例：让一个类拥有数组的操作
```php
class myData implements IteratorAggregate {
    public $property1 = "Public property one";
    public $property2 = "Public property two";
    public $property3 = "Public property three";
    
    public function __construct() {
        $this->property4 = "last property";
    }
    
    public function getIterator() {
        return new ArrayIterator($this);
    }
}

$obj = new myData;

foreach($obj as $key => $value) {
    var_dump($key, $value);
    echo "\n";
}
```

`ArrayAccess`（数组式访问）接口
```php
ArrayAccess  {
  /* 方法 */
   abstract public boolean offsetExists ( mixed $offset )
  abstract public mixed offsetGet ( mixed $offset )
  abstract public void offsetSet ( mixed $offset , mixed $value )
  abstract public void offsetUnset ( mixed $offset )
}
```

`Serializable` 自定义序列化的接口
```php
Serializable  {
  /* 方法 */
  abstract public string serialize ( void )
  abstract public mixed unserialize ( string $serialized )
}
```
实现此接口的类将不再支持 __sleep() 和 __wakeup()。