# __tostring()

如果在类定义中实现了__tostring()方法，当尝试打印该类时，将会调用这个方法：

```php
class Printable{
		public $one="1";
		public $two="2";
		public function __toString(){
			//return (var_export($this,true));
			return $this->one.$this->two;
		}
	}
	
	$o = new Printable;
	echo $o;
```
输出：12