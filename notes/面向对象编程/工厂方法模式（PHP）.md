# 工厂方法模式（PHP）

工厂方法模式把创建者类与要生产的产品类分离开来，创建者是一个工厂类，其中定义了用于生产产品对象的类方法。创建者的每个子类实例化一个相应的产品子类。
例如：有一个编码器类ApptEncoder，用来将数据转换成特定的格式。此外有一个管理类CommsManager，用来获取不同的编码器。
一种比较差的、基于条件语句的实现如下：

```php
<?php
abstract class ApptEncoder {
    abstract function encode();
}

class BloggsApptEncoder extends ApptEncoder {
    function encode() {
        return "Appointment data encoded in BloggsCal format\n";
    }
}

class MegaApptEncoder extends ApptEncoder {
    function encode() {
        return "Appointment data encoded in MegaCal format\n";
    }
}

class CommsManager {
    const BLOGGS = 1;
    const MEGA = 2;
    private $mode ;

    function __construct( $mode ) {
        $this->mode = $mode;
    }

    function getHeaderText() {					// 基于条件语句
        switch ( $this->mode ) {
            case ( self::MEGA ):
                return "MegaCal header\n";
            default:
                return "BloggsCal header\n";
        }
    }
    function getApptEncoder() {					// 基于条件语句（重复判断）
        switch ( $this->mode ) {
            case ( self::MEGA ):
                return new MegaApptEncoder();
            default:
                return new BloggsApptEncoder();
        }
    }

// 如果再加入一个新的getFooterText()方法，则要再多一次条件判断
}

$man = new CommsManager( CommsManager::MEGA );
print ( get_class( $man->getApptEncoder() ) )."\n";
$man = new CommsManager( CommsManager::BLOGGS );
print ( get_class( $man->getApptEncoder() ) )."\n";
?>
```
工厂方法的实现：
```php
<?php
abstract class ApptEncoder {
    abstract function encode();
}

class BloggsApptEncoder extends ApptEncoder {
    function encode() {
        return "Appointment data encode in BloggsCal format\n";
    }
}

abstract class CommsManager {
    abstract function getHeaderText();
    abstract function getApptEncoder();
    abstract function getFooterText();
}

// 产品和工厂都抽象出来，一个工厂的子类对应一个产品子类
class BloggsCommsManager extends CommsManager {
    function getHeaderText() {
        return "BloggsCal header\n";
    }

    function getApptEncoder() {
        return new BloggsApptEncoder();
    }

    function getFooterText() {
        return "BloggsCal footer\n";
    }
}
?>
```