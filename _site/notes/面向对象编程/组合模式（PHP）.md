# 组合模式（PHP）

整体和局部可以互换，即容器对象和它们包含的对象共享相同的接口。比如一个士兵、一辆坦克有它们的战斗力值，一个由士兵、坦克组成的军队（一个集合）也有它的战斗力值。士兵、坦克、军队，它们都可以定义为单元，拥有共同的接口。

```php
<?php
abstract class Unit {
    abstract function addUnit( Unit $unit );
    abstract function removeUnit( Unit $unit );
    abstract function bombardStrength();
}

class Army extends Unit {
    private $units = array();

    function addUnit( Unit $unit ) {
        if ( in_array( $unit, $this->units, true ) ) {
            return;
        }
        
        $this->units[] = $unit;
    }

    function removeUnit( Unit $unit ) {
        $this->units = array_udiff( $this->units, array( $unit ), 
                      function( $a, $b ) { return ($a === $b)?0:1; } );
    }

// 计算军队的战斗力
    function bombardStrength() {
        $ret = 0;
        foreach( $this->units as $unit ) {
            $ret += $unit->bombardStrength();
        }
        return $ret;
    }
}

class Tank extends Unit { 
function addUnit( Unit $unit ) {}				// 冗余方法，但是为了保证“透明性”必须存在
    function removeUnit( Unit $unit ) {}			// 冗余方法，但是为了保证“透明性”必须存在

    function bombardStrength() {
        return 4;
    }
}

class Soldier extends Unit { 
    function addUnit( Unit $unit ) {}				// 冗余方法，但是为了保证“透明性”必须存在
    function removeUnit( Unit $unit ) {}			// 冗余方法，但是为了保证“透明性”必须存在

    function bombardStrength() {
        return 8;
    }
}

$tank =  new Tank();
$tank2 = new Tank();
$soldier = new Soldier();

$army = new Army();
$army->addUnit( $soldier );
$army->addUnit( $tank );
$army->addUnit( $tank2 );

print_r( $army );

$army->removeUnit( $tank2 );

print_r( $army );
?>
```