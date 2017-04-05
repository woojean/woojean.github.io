# 装饰模式（PHP）

功能定义完全依赖于继承体系会导致类的数量过多，而且代码会产生重复。
例如，定义一个Tile类表示一个作战区域，它有一个方法getWealthFactor()用于计算某个特定区域被一个玩家所占有后的收益：

```php
<?php
abstract class Tile {
    abstract function getWealthFactor();
}

// 平原
class Plains extends Tile {
    private $wealthfactor = 2;
    function getWealthFactor() {
        return $this->wealthfactor;
    }
}

// 带钻石的平原
class DiamondPlains extends Plains {
    function getWealthFactor() {
        return parent::getWealthFactor() + 2;
    }
}

// 被污染的平原
class PollutedPlains extends Plains {
    function getWealthFactor() {
        return parent::getWealthFactor() - 4;
    }
}

$tile = new PollutedPlains();
print $tile->getWealthFactor();
?>
```
此时，如果想要获得一个既带钻石，又被污染的平原，如果基于集成来扩展功能，就只能创建一个形如PollutedDiamondPlains的新类。

装饰模式使用组合和委托，而不是只使用继承来解决功能变化问题。
```php
<?php

abstract class Tile {
    abstract function getWealthFactor();
}

class Plains extends Tile {
    private $wealthfactor = 2;
    function getWealthFactor() {
        return $this->wealthfactor;
    }
}

// 引入一个委托类
// 委托类继承自Tile，因此与Tile的对象有相同的操作接口
// 委托类还包含一个指向Tile的引用，用于实现链式操作，在运行时轻松合并对象
abstract class TileDecorator extends Tile {
    protected $tile;
    function __construct( Tile $tile ) {
        $this->tile = $tile;
    }
}

class DiamondDecorator extends TileDecorator {
    // 在装饰器类中扩展功能
function getWealthFactor() {
        return $this->tile->getWealthFactor()+2;
    }
}

class PollutionDecorator extends TileDecorator {
    function getWealthFactor() {
        return $this->tile->getWealthFactor()-4;
    }
}

$tile = new Plains();
print $tile->getWealthFactor(); 			// 2

$tile = new DiamondDecorator( new Plains() );
print $tile->getWealthFactor();			 // 4

$tile = new PollutionDecorator( new DiamondDecorator( new Plains() ) );
print $tile->getWealthFactor(); 			// 0
?>
```