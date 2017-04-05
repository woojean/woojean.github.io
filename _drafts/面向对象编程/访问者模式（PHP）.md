# 访问者模式（PHP）

访问者表示一个作用于某对象结构中的各元素的操作。它使得可以在不改变各元素类的前提下定义作用于这些元素的新操作。把数据结构和作用于结构上的操作解耦合，使得操作集合可相对自由地演化。适用于数据结构相对稳定算法又易变化的系统。
如下在Unit类族的基础上添加新的功能。

```php
<?php
/* 
应用组合模式实现的Unit类族
 */
abstract class Unit {
    protected $depth = 0;						// 节点深度

    function getComposite() {
        return null;
    }
    
    protected function setDepth( $depth ) {
        $this->depth=$depth;
    }

    function getDepth() {
        return $this->depth;
    }

    abstract function bombardStrength();			// 组合模式的统一接口

    function accept( ArmyVisitor $visitor ) {		// 接收一个访问者对象
        $method = "visit".get_class( $this );		// 拼凑访问者对象的方法，如当前对象是一个名为Archer的Unit的子类的对象，则拼凑出来的方法名为：visitArcher()
        $visitor->$method( $this );
    }
}

// 一种单节点单元
class Archer extends Unit {
    function bombardStrength() {
        return 4;
    }

}

// 一种单节点单元
class Cavalry extends Unit {
    function bombardStrength() {
        return 2;
    }
}

// 一种单节点单元
class LaserCanonUnit extends Unit {
    function bombardStrength() {
        return 44;
    }
}

// 多节点单元的基类
abstract class CompositeUnit extends Unit {
    private $units = array();

    function getComposite() {
        return $this;
    }

    function units() {
        return $this->units;
    }

    function removeUnit( Unit $unit ) {
        $units = array();
        foreach ( $this->units as $thisunit ) {
            if ( $unit !== $thisunit ) {
                $units[] = $thisunit;
            }
        }
        $this->units = $units;
    }

// 多节点对象在接受访问者对象时行为不同于单节点对象
    function accept( ArmyVisitor $visitor ) {
        parent::accept( $visitor );
        foreach ( $this->units as $thisunit ) {  // 所有子节点也应该可以被该访问者对象所访问
            $thisunit->accept( $visitor );
        }
    }

    function addUnit( Unit $unit ) {
        foreach ( $this->units as $thisunit ) {
            if ( $unit === $thisunit ) {
                return;
            }
        }
        $unit->setDepth($this->depth+1);
        $this->units[] = $unit;
    }
}

// 一种多节点单元
class TroopCarrier extends CompositeUnit {
    function addUnit( Unit $unit ) {
        if ( $unit instanceof Cavalry ) {
            throw new UnitException("Can't get a horse on the vehicle");
        }
        parent::addUnit( $unit );
    }

    function bombardStrength() {
        return 0;
    }
}

// 一种多节点单元
class Army extends CompositeUnit {

    function bombardStrength() {
        $ret = 0;
        foreach( $this->units() as $unit ) {
            $ret += $unit->bombardStrength();
        }
        return $ret;
    }
}

// ========================================================================
// 访问者的基类
abstract class ArmyVisitor  {
    abstract function visit( Unit $node );

/*
定义访问每种可访问对象的接口
实际都是重定向到visit(...)方法
*/
    function visitArcher( Archer $node ) {
        $this->visit( $node );
    }
    function visitCavalry( Cavalry $node ) {
        $this->visit( $node );
    }

    function visitLaserCanonUnit( LaserCanonUnit $node ) {
        $this->visit( $node );
    }

    function visitTroopCarrierUnit( TroopCarrierUnit $node ) {
        $this->visit( $node );
    }

    function visitArmy( Army $node ) {
        $this->visit( $node );
    }
}

// 每一个访问者子类即相当于提供了一组新的功能
// 一种访问者
class TextDumpArmyVisitor extends ArmyVisitor {
    private $text="";
// 重写visit方法定义特定功能
    function visit( Unit $node ) {
        $ret = "";
        $pad = 4*$node->getDepth();			// 使用了节点的方法
        $ret .= sprintf( "%{$pad}s", "" );
        $ret .= get_class($node).": ";
        $ret .= "bombard: ".$node->bombardStrength()."\n";
        $this->text .= $ret;
    }

// 该访问器的对外接口：触发访问的入口
    function getText() {
        return $this->text;
    }
}

// 一种访问者
class TaxCollectionVisitor extends ArmyVisitor {
    private $due=0;
    private $report="";

    function visit( Unit $node ) {
        $this->levy( $node, 1 );
    }

    function visitArcher( Archer $node ) {
        $this->levy( $node, 2 );
    }

    function visitCavalry( Cavalry $node ) {
        $this->levy( $node, 3 );
    }

    function visitTroopCarrierUnit( TroopCarrierUnit $node ) {
        $this->levy( $node, 5 );
    }

    private function levy( Unit $unit, $amount ) {
        $this->report .= "Tax levied for ".get_class( $unit );
        $this->report .= ": $amount\n";
        $this->due += $amount;
    }

// 该访问器的对外接口：触发访问的入口1
    function getReport() {
        return $this->report;
    }

// 该访问器的对外接口：触发访问的入口2
    function getTax() {
        return $this->due;
    }
}

// 使用
// 构建一个军队（一组节点，一棵树）
$main_army = new Army();
$main_army->addUnit( new Archer() );
$main_army->addUnit( new LaserCanonUnit() );
$main_army->addUnit( new Cavalry() );

$textdump = new TextDumpArmyVisitor();			// 实例化一个访问器
$main_army->accept( $textdump  );				// 节点数接受这个访问者对象
print $textdump->getText();						// 访问！

$taxcollector = new TaxCollectionVisitor();
$main_army->accept( $taxcollector );
print $taxcollector->getReport();
print "TOTAL: ";
print $taxcollector->getTax()."\n";
?>
```