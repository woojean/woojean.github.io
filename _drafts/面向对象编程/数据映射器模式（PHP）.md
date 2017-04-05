# 数据映射器模式（PHP）

数据映射器是一个负责将数据库数据映射到对象的类。
通常习惯为每一个领域类实现一个映射类。

```php
// 数据映射器的基类
abstract class Mapper {
    protected static $PDO; 
    function __construct() {
        if ( ! isset(self::$PDO) ) { 
            $dsn = \woo\base\ApplicationRegistry::getDSN( );
            if ( is_null( $dsn ) ) {
                throw new \woo\base\AppException( "No DSN" );
            }
            self::$PDO = new \PDO( $dsn );
            self::$PDO->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
    }

// 根据ID从数据库中查找记录，并映射为一个对象返回
    function find( $id ) {
        $this->selectstmt()->execute( array( $id ) );
        $array = $this->selectstmt()->fetch( ); 
        $this->selectstmt()->closeCursor( ); 
        if ( ! is_array( $array ) ) { return null; }
        if ( ! isset( $array['id'] ) ) { return null; }
        $object = $this->createObject( $array );			// 重定向到createObject(...)
        return $object; 
    }

// 根据一个数组参数创建一个对象返回
    function createObject( $array ) {
        $obj = $this->doCreateObject( $array );			// 重定向到doCreateObject(...)
        return $obj;
    }

    function insert( \woo\domain\DomainObject $obj ) {	// 重定向到doInsert(...)
        $this->doInsert( $obj );
    }

// 留待子类实现的方法
    abstract function update( \woo\domain\DomainObject $object );
    protected abstract function doCreateObject( array $array );
    protected abstract function doInsert( \woo\domain\DomainObject $object );
    protected abstract function selectStmt();
}

// 对应数据库中Venue表的数据映射器
class VenueMapper extends Mapper {
    function __construct() {
        parent::__construct();
// 在构造方法中预定义了一些sql语句模板，其中就关联了指定的表venue
        $this->selectStmt = self::$PDO->prepare( "SELECT * FROM venue WHERE id=?");
        $this->updateStmt = self::$PDO->prepare("update venue set name=?, id=? where id=?");
        $this->insertStmt = self::$PDO->prepare("insert into venue ( name ) values( ? )");
    } 
    
// 在对象关系层面，一个venue可以包含多个space
    function getCollection( array $raw ) {
        return new SpaceCollection( $raw, $this );
    }

// 创建一个新对象，此时与数据库无关
    protected function doCreateObject( array $array ) {
        $obj = new \woo\domain\Venue( $array['id'] );
        $obj->setname( $array['name'] );
        // $space_mapper = new spacemapper();
        // $space_collection = $space_mapper->findbyvenue( $array['id'] );
        // $obj->setspaces( $space_collection );
        return $obj;
    }

// 插入：将对象映射到数据库
    protected function doInsert( \woo\domain\DomainObject $object ) {
        print "inserting\n";
        debug_print_backtrace();
        $values = array( $object->getName() ); 
        $this->insertStmt->execute( $values );
        $id = self::$PDO->lastInsertId();
        $object->setId( $id );
    }
    
// 更新
    function update( \woo\domain\DomainObject $object ) {
        print "updating\n";
        $values = array( $object->getName(), $object->getId(), $object->getId() ); 
        $this->updateStmt->execute( $values );
    }

    function selectStmt() {
        return $this->selectStmt;
    }
}

// 使用
$mapper = new VenueMapper();
$venue = $mapper->find(2);				// 使用数据映射器来填充一个领域对象模型
print_r( $venue );
```