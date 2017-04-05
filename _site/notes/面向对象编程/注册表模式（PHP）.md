# 注册表模式（PHP）

注册表是跳出层约束的主要途径之一，大多数模式只能用在某个层，但注册表是一个例外。
注册表的作用是提供系统级别的对象访问能力（跨层）。

```php
<?php
namespace woo\base;

// 注册表基类，需要支持“应用程序作用域”的数据可以继承该类
abstract class Registry {
    abstract protected function get( $key );
    abstract protected function set( $key, $val );
}

// 请求-提供应用程序级别的、对请求对象的访问接口
class RequestRegistry extends Registry {
    ...
}

// 会话-提供应用程序级别的、对会话对象的访问接口
class SessionRegistry extends Registry {
    private static $instance;
    private function __construct() {
        session_start();
    }

    static function instance() {
        if ( ! isset(self::$instance) ) { self::$instance = new self(); }
        return self::$instance;
    }

    protected function get( $key ) {
        if ( isset( $_SESSION[__CLASS__][$key] ) ) {
            return $_SESSION[__CLASS__][$key];
        }
        return null;
    }

    protected function set( $key, $val ) {
        $_SESSION[__CLASS__][$key] = $val;
    }

    function setComplex( Complex $complex ) {
        self::instance()->set('complex', $complex);
    }

    function getComplex( ) {
        return self::instance()->get('complex');
    }
}

// 应用程序-代表应用程序自身
class ApplicationRegistry extends Registry {
    ...
}

class MemApplicationRegistry extends Registry {
    ...
}

class AppException extends \Exception {}
?>

// 使用
if ( ! isset( $argv[1] ) ) {
    // run script without argument to monitor
    while ( 1 ) {
        sleep(5);
        $thing = \woo\base\ApplicationRegistry::getDSN();

// 初始化各种注册表
        \woo\base\RequestRegistry::instance();
        \woo\base\SessionRegistry::instance();
        \woo\base\MemApplicationRegistry::instance();

        print "dsn is {$thing}\n";
    }
} else {
    // run script with argument in separate window to change value.. see the result in monitor process
    print "setting dsn {$argv[1]}\n"; 
    \woo\base\ApplicationRegistry::setDSN($argv[1]);
}
```