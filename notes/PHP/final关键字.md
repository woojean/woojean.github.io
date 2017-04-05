# final关键字

## 禁止类被继承
```php
final class A{
...
}
```

### 禁止函数被重载：
```php
class A{
		public $attribute ="default A";
		final function operation(){
			echo "A.attribute:".$this->attribute."<br/>";
		}
	}
```