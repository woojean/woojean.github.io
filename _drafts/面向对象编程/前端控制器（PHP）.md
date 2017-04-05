# 前端控制器（PHP）

用一个中心来处理所有到来的请求：单一入口。
所有请求都定向到index.php中，该文件内容如下：

```php
require(“woo/controller/Controller.php”);
\woo\controller\Controller::run();			// 创建并运行一个前端控制器来处理所有的操作
```

前端控制器委托ApplicationHelper对象来初始化执行环境，然后从CommandResolver对象获取一个Command对象，最后调用Command::execute()处理业务逻辑。

```php
<?php
namespace woo\controller;

// 前端控制器类
class Controller {
    private $applicationHelper;				// 辅助类，用于初始化环境变量

    private function __construct() {}

// 控制器执行入口
    static function run() {
        $instance = new Controller();	
        $instance->init();					// 初始化
        $instance->handleRequest();			// 处理请求
    }

    function init() {
        $applicationHelper = ApplicationHelper::instance();
        $applicationHelper->init();
    }

// 实际处理请求的地方
    function handleRequest() {
        $request = new \woo\controller\Request();				// 请求对象
        $cmd_r = new \woo\command\CommandResolver();		// 命令解析对象
        $cmd = $cmd_r->getCommand( $request );				// 获取命令对象
        $cmd->execute( $request );							// 处理请求
    }
}

// 辅助类，用于初始化环境变量。是一个单例，并用注册表模式操作应用程序级对象
class ApplicationHelper {
    private static $instance;
    private $config = "/tmp/data/woo_options.xml";				// 配置文件路径

    private function __construct() {}

    static function instance() {
        if ( ! self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    function init() {
        $dsn = \woo\base\ApplicationRegistry::getDSN( );
        if ( ! is_null( $dsn ) ) {
            return;
        }
        $this->getOptions();
     }

     private function getOptions() {
        $this->ensure( file_exists( $this->config  ),"Could not find options file" );
        $options = SimpleXml_load_file( $this->config );
        print get_class( $options );
        $dsn = (string)$options->dsn;
        $this->ensure( $dsn, "No DSN found" );
        \woo\base\ApplicationRegistry::setDSN( $dsn );
        // set other values
    }

    private function ensure( $expr, $message ) {
        if ( ! $expr ) {
            throw new \woo\base\AppException( $message );
        }
    }
}

// 请求类
class Request {
    private $properties;
    private $feedback = array();

    function __construct() {
        $this->init();
        \woo\base\RequestRegistry::setRequest($this );			// 设置应用程序注册表中的请求对象为自己
    }

    function init() {
        if ( isset( $_SERVER['REQUEST_METHOD'] ) ) {
            $this->properties = $_REQUEST;
            return;
        }
        foreach( $_SERVER['argv'] as $arg ) {
            if ( strpos( $arg, '=' ) ) {
                list( $key, $val )=explode( "=", $arg );
                $this->setProperty( $key, $val );
            }
        }
    }

    function getProperty( $key ) {
        if ( isset( $this->properties[$key] ) ) {
            return $this->properties[$key];
        }
    }

    function setProperty( $key, $val ) {
        $this->properties[$key] = $val;
    }
    
    function addFeedback( $msg ) {
        array_push( $this->feedback, $msg );
    }
 
    function getFeedback( ) {
        return $this->feedback;
    }

    function getFeedbackString( $separator="\n" ) {
        return implode( $separator, $this->feedback );
    }
}



namespace woo\command;
// 命令对象的基类。前端控制器的主要功能就是根据请求参数映射到不同的命令
abstract class Command {
    final function __construct() { }

// 执行命令
    function execute( \woo\controller\Request $request ) {
        $this->doExecute( $request );
    }

    abstract function doExecute( \woo\controller\Request $request );
}

// 默认的命令
class DefaultCommand extends Command {
    function doExecute( \woo\controller\Request $request ) {
        $request->addFeedback( "Welcome to WOO" );
        include( "woo/view/main.php");					// 分配视图
    }
}

// 命令解析器（生成不同命令的工厂）
class CommandResolver {
    private static $base_cmd;
    private static $default_cmd;

    function __construct() {
        if ( ! self::$base_cmd ) {
            self::$base_cmd = new \ReflectionClass( "\woo\command\Command" );		// 使用反射
            self::$default_cmd = new DefaultCommand();
        }
    }

// 获取命令对象
    function getCommand( \woo\controller\Request $request ) {
        $cmd = $request->getProperty( 'cmd' );
        $sep = DIRECTORY_SEPARATOR;
        if ( ! $cmd ) {
            return self::$default_cmd;
        }
        $cmd=str_replace( array('.', $sep), "", $cmd );
        $filepath = "woo{$sep}command{$sep}{$cmd}.php";
        $classname = "woo\\command\\{$cmd}";
        if ( file_exists( $filepath ) ) {
            @require_once( "$filepath" );
            if ( class_exists( $classname) ) {
                $cmd_class = new ReflectionClass($classname);// 使用反射的方式，根据类名返回类实例
                if ( $cmd_class->isSubClassOf( self::$base_cmd ) ) {
                    return $cmd_class->newInstance();
                } else {
                    $request->addFeedback( "command '$cmd' is not a Command" );
                }
            }
        }
        $request->addFeedback( "command '$cmd' not found" );
        return clone self::$default_cmd;
    }
}

?>
```