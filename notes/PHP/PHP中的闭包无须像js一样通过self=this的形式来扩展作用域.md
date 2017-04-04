# PHP中的闭包无须像js一样通过self=this的形式来扩展作用域

```php
class Demo{
  private $list = [1,2,3,4,5,6,7,8,9,10];
  public $delList = [];
  
  public function test(){
    $arr = array_filter($this->list,function($n){
      if($n % 2 == 0){
        return true;
      }
      else{
        $this->delList[] = $n;
        return false;
      }
    });
    
    return $arr;
  }
}

$demo = new Demo();
$arr = $demo->test();
var_dump($arr);   // 1 3 5 7 9
var_dump($demo->delList); // 2 4 6 8 10
```