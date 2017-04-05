# 命令模式（PHP）

以对象来代表实际行动。命令对象可以把行动（action）及其参数封装起来。

```php
<?php

class CommandNotFoundException extends Exception {}

// 通过CommandContext机制，请求数据可被传递给Command对象，同时响应也可以被返回到视图层 
class CommandContext {
    private $params = array();
    private $error = "";

    function __construct() {
        $this->params = $_REQUEST;		// 将请求参数映射到参数列表，请求参数中包含命令名称以及其他的参数
    }

    function addParam( $key, $val ) { 
        $this->params[$key]=$val;
    }

    function get( $key ) { 
        return $this->params[$key];
    }

    function setError( $error ) {
        $this->error = $error;
    }

    function getError() {
        return $this->error;
    }
}

// 生成命令对象的工厂
// 在commnds目录里查找特定的类文件
// 如果文件和类都存在，则返回命令对象给调用者
class CommandFactory {
    private static $dir = 'commands';
    static function getCommand( $action='Default' ) {
        if ( preg_match( '/\W/', $action ) ) {
            throw new Exception("illegal characters in action");
        }
        $class = UCFirst(strtolower($action))."Command";  
        $file = self::$dir.DIRECTORY_SEPARATOR."$class.php";
        if ( ! file_exists( $file ) ) {
            throw new CommandNotFoundException( "could not find '$file'" );
        }
        require_once( $file );
        if ( ! class_exists( $class ) ) {
            throw new CommandNotFoundException( "no '$class' class located" );
        }
        $cmd = new $class();
        return $cmd;
    }
}

// 命令调用者
class Controller {
    private $context;
    function __construct() {
        $this->context = new CommandContext();
    }

    function getContext() {
        return $this->context;
    }

    function process() {
// 在一个Web项目中，选择实例化哪个命令对象的最简单的办法是根据请求本身的参数来决定
        $cmd = CommandFactory::getCommand( $this->context->get('action') );
        if ( ! $cmd->execute( $this->context ) ) {
            // 处理错误
        } else {
            // 成功，返回视图
        }
    } 
}    

// 使用
$controller = new Controller();

// 获取controller的CommandContext，并设置参数
$context = $controller->getContext();	
$context->addParam('action', 'feedback' );  // 指定命令名称，将被用于查找类文件
$context->addParam('email', 'bob@bob.com' );
$context->addParam('topic', 'my brain' );
$context->addParam('msg', 'all about my brain' );

// 执行命令
$controller->process();
print $context->getError();

?>


命令类的具体实现：
​```php
class FeedbackCommand extends Command{
  function execute(CommandContext $context){
    $email = $context->get(‘email’);
    $msg = $context->get(‘msg’);
    ...
    return true;
  }
}

可以再定义其他的命令类，比如：
​```php
class LoginCommand extends Command{
  function execute(CommandContext $context){
    ...
  }
}
```
