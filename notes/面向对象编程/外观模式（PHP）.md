# 外观模式（PHP）

外观模式是一个十分简单的概念，它只是为一个分层或者一个子系统创建一个单一的入口，方便客户端代码的使用，避免客户端代码使用子系统复杂的内部方法。

```php
class ProductFacade {
    private $products = array();

    function __construct( $file ) {
        $this->file = $file;
        $this->compile();
    }

    private function compile() {
        // 复杂的操作
    }

    function getProducts() {
        return $this->products;
    }

    function getProduct( $id ) {
        return $this->products[$id];
    }
}

$facade = new ProductFacade( 'test.txt' );
$object = $facade->getProduct( 234 );
```