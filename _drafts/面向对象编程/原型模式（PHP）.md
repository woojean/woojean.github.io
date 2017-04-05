# 原型模式（PHP）

用原型实例指定创建对象的种类，并且通过拷贝这些原型创建新的对象。
抽象工厂模式实现了平行的继承扩展，但当产品类型过多时，往往需要实现一个庞大的继承体系，变得不灵活。这时可以使用抽象工厂的变形：原型模式。

```php
<?php

class Sea {}
class EarthSea extends Sea {}
class MarsSea extends Sea {}

class Plains {}
class EarthPlains extends Plains {}
class MarsPlains extends Plains {}

class Forest {}
class EarthForest extends Forest {}
class MarsForest extends Forest {}

class TerrainFactory {
    private $sea;
    private $forest;
    private $plains;

    function __construct( Sea $sea, Plains $plains, Forest $forest ) {
        $this->sea = $sea;
        $this->plains = $plains;
        $this->forest = $forest;
    }

    function getSea( ) {
        return clone $this->sea;
    }

    function getPlains( ) {
        return clone $this->plains;
    }

    function getForest( ) {
        return clone $this->forest;
    }
}

$factory = new TerrainFactory( new EarthSea(),new EarthPlains(),new EarthForest() );
print_r( $factory->getSea() );
print_r( $factory->getPlains() );
print_r( $factory->getForest() );
?>
```
以上在初始化工厂的时候，传入了不同类的实例用于初始化工厂。当需要新的类型实例时，无须写一个新的创建者类，只需要简单地改变创建工厂时提供的参数，如：
	$factory = new TerrainFactory( new EarthSea(),new MarsPlains(),new MarsForest() );

同样是平行扩展，抽象工厂模式基于继承实现，原型模式基于组合实现。