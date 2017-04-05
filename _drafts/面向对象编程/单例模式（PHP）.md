# 单例模式（PHP）

保证某个类在整个应用中仅有一个实例，并提供一个访问它的全局访问点。
单例模式的要点有三个：一是某个类只能有一个实例；二是它必须自行创建这个实例；三是它必须自行向整个系统提供这个实例。

```php
<?php

class Preferences {
    private $props = array();
    private static $instance;

    private function __construct() { }	

    public static function getInstance() {
        if ( empty( self::$instance ) ) {
            self::$instance = new Preferences();
        }
        return self::$instance;
    }

    public function setProperty( $key, $val ) {
        $this->props[$key] = $val;
    }

    public function getProperty( $key ) {
        return $this->props[$key];
    }
}


$pref = Preferences::getInstance();
$pref->setProperty( "name", "matt" );

unset( $pref ); // 删除$pref

$pref2 = Preferences::getInstance();
print $pref2->getProperty( "name" ) ."\n"; 	// 虽然$pref被删除了，但是值被保留了
?>
```