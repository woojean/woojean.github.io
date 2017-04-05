# 抽象工厂模式（PHP）

工厂方法解决了同一个编码器添加不同编码格式的问题，当需要添加不同的编码器时，需要使用抽象工厂模式。
例如需要添加一个新的解码器类：TtdEncoder，该类同样支持ApptEncoder所支持的所有编码格式。

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

// TtdEncoder及其子类的实现略

abstract class CommsManager {
    abstract function getHeaderText();
    abstract function getApptEncoder();		// 解码器类型1 
    abstract function getTtdEncoder();			// 解码器类型2
    abstract function getContactEncoder();		// 解码器类型3
    abstract function getFooterText();
}

class BloggsCommsManager extends CommsManager {
    function getHeaderText() {
        return "BloggsCal header\n";
    }

    function getApptEncoder() {
        return new BloggsApptEncoder();
    }

    function getTtdEncoder() {
        return new BloggsTtdEncoder();
    }

    function getContactEncoder() {
        return new BloggsContactEncoder();
    }

    function getFooterText() {
        return "BloggsCal footer\n";
    }
}

class MegaCommsManager extends CommsManager {
    function getHeaderText() {
        return "MegaCal header\n";
    }

    function getApptEncoder() {
        return new MegaApptEncoder();
    }

    function getTtdEncoder() {
        return new MegaTtdEncoder();
    }

    function getContactEncoder() {
        return new MegaContactEncoder();
    }

    function getFooterText() {
        return "MegaCal footer\n";
    }
}

/*
$mgr = new MegaCommsManager();
print $mgr->getHeaderText();
print $mgr->getApptEncoder()->encode();
print $mgr->getFooterText();
*/
?>
```
工厂方法：一个抽象产品类可以派生出多个具体产品类，一个抽象工厂类可以派生出多个具体工厂类，每个具体工厂类只能创建一个具体产品类的实例。
抽象工厂：多个抽象产品类，每个抽象产品类可以派生出多个具体产品类。一个抽象工厂可以派生出多个具体工厂类，每个具体工厂类可以创建多个具体产品类的实例。
如上例，产品有编码器、编码格式两个维度，工厂的不同子类对应不同的编码格式，每个子类提供该格式的不同类型的编码器。