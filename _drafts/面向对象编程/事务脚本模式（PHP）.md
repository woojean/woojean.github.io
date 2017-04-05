# 事务脚本模式（PHP）

事务即一个功能，事务脚本提供一个快速而有效的机制来满足系统目标，自己处理请求（不分层，比如把连接数据库和计算业务逻辑的代码耦合在一个类中），而不是委托给特定的对象来完成。好处在于可以很快就得到想要的结果，但一般只应用在小型项目中。

```php
<?php
namespace woo\process;

// 封装不同事务的公共操作
abstract class Base {
    static $DB;
    static $stmts = array();
   
    function __construct() {
        $dsn = \woo\base\ApplicationRegistry::getDSN( );
        if ( is_null( $dsn ) ) {
            throw new \woo\base\AppException( "No DSN" );
        }

        self::$DB = new \PDO( $dsn );
        self::$DB->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    } 

    function prepareStatement( $stmt_s ) {					// 拼装SQL语句
        if ( isset( self::$stmts[$stmt_s] ) ) {
            return self::$stmts[$stmt_s];
        }
        $stmt_handle = self::$DB->prepare($stmt_s);
        self::$stmts[$stmt_s]=$stmt_handle;
        return $stmt_handle;
    } 

    protected function doStatement( $stmt_s, $values_a ) {	// 执行SQL查询
        $sth = $this->prepareStatement( $stmt_s );
        $sth->closeCursor();
        $db_result = $sth->execute( $values_a );
        return $sth;
    }
}

class VenueManager extends Base {
    static $add_venue =  "INSERT INTO venue ( name ) values( ? )";
    static $add_space  = "INSERT INTO space( name, venue ) values( ?, ? )"; 
    static $check_slot = "SELECT id, name FROM event WHERE space = ? AND (start+duration) > ? AND start < ?"; 
    static $add_event =  "INSERT INTO event ( name, space, start, duration ) values( ?, ?, ?, ? )"; 

// 一个事务：添加空间
    function addVenue( $name, $space_array ) {
        $ret = array();
        $ret['venue'] = array( $name ); 
        $this->doStatement( self::$add_venue, $ret['venue']);
        $v_id = self::$DB->lastInsertId();
        $ret['spaces'] = array();
        foreach ( $space_array as $space_name ) {
            $values = array( $space_name, $v_id );
            $this->doStatement( self::$add_space, $values);
            $s_id = self::$DB->lastInsertId();
            array_unshift( $values, $s_id );
            $ret['spaces'][] = $values;
        }
        return $ret;
    }
    
// 一个事务：注册事件
    function bookEvent( $space_id, $name, $time, $duration ) {
        $values = array( $space_id, $time, ($time+$duration) ); 
        $stmt = $this->doStatement( self::$check_slot, $values, false ) ;
        if ( $result = $stmt->fetch() ) {
            throw new \woo\base\AppException( "double booked! try again" );
        }
        $this->doStatement( self::$add_event, 
            array( $name, $space_id, $time, $duration ) );
    } 

// 还可以用同样的形式定义其他的基于数据库操作的事务，快速实现功能。
}
$halfhour = (60*30);
$hour     = (60*60);
$day      = (24*$hour);

$mode="sqlite";
if ( $mode == 'mysql' ) {
    $dsn = "mysql:dbname=test";    
} else {
    $dsn = "sqlite://tmp/data/woo.db";    
}

\woo\base\ApplicationRegistry::setDSN( $dsn ); 
$mgr = new VenueManager();
$ret = $mgr->addVenue( "The Eyeball Inn", array( 'The Room Upstairs', 'Main Bar' ));
$space_id = $ret['spaces'][0][0];
$mgr->bookEvent( $space_id, "Running like the rain", time()+($day), ($hour-5) ); 
$mgr->bookEvent( $space_id, "Running like the trees", time()+($day-$hour), (60*60) ); 
?>
```