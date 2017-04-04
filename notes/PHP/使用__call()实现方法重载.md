# 使用__call()实现方法重载

```php
public function __call($method, $p){
if($method == “display”){ // 不用实现任何display()方法
if(is_object($p[0])){
$this->displayObject($p[0]);
}
else if(is_array($p[0]){
$this->displayArray($p[0]);
}
else{
$this->displayScalar($p[0]);
}
}
}
$ov = new overload();
$ov->display(array(1,2,3));
$ov->display(‘cat’);
```
使用__call可以实现调用不存在的方法，如上例中的display方法根本不存在。