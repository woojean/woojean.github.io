# 标识映射模式（PHP）

保存每个已加载过的对象，确保每个对象只加载一次（相当于在数据映射器与数据库之间又加了一层逻辑）当要访问他们的时候，通过映射来查找它们。标识映射的主要目的是保持一致性，而不是提高性能。：
（1）当要访问对象时，首先检查标识映射，看需要的对象是否已经存在其中。
（2）使用Identify来确保不重复加载相同的数据，不仅有助于保证正确性（不会将同一数据加载到两个不同的对象上），还能提升性能。

```php
// 一个标识映射类（一个对象池）
// 同时实现了标识映射模式和工作单元模式
class ObjectWatcher {
    private $all = array();			// 已加载的所有对象
    private $dirty = array();
    private $new = array();
    private $delete = array();
    private static $instance;

    private function __construct() { }

// 单例
    static function instance() {
        if ( ! self::$instance ) {
            self::$instance = new ObjectWatcher();
        }
        return self::$instance;
    }
 
// 构造唯一标识，确保不存在重复对象
    function globalKey( DomainObject $obj ) {
        $key = get_class( $obj ).".".$obj->getId();
        return $key;
    }
  
// 添加新的加载对象，如果已存在则覆盖
    static function add( DomainObject $obj ) {
        $inst = self::instance();
        $inst->all[$inst->globalKey( $obj )] = $obj;
    }

// 判断某个类的某个对象是否已被加载
    static function exists( $classname, $id ) {
        $inst = self::instance();
        $key = "$classname.$id";
        if ( isset( $inst->all[$key] ) ) {
            return $inst->all[$key];
        }
        return null;
    }
 
/***
以下是工作单元模式的实现
***/
    static function addDelete( DomainObject $obj ) {				// 删除
        $self = self::instance();
        $self->delete[$self->globalKey( $obj )] = $obj;
    }

    static function addDirty( DomainObject $obj ) {				// 添加脏对象
        $inst = self::instance();
        if ( ! in_array( $obj, $inst->new, true ) ) {
            $inst->dirty[$inst->globalKey( $obj )] = $obj;
        }
    }

    static function addNew( DomainObject $obj ) {				// 新对象
        $inst = self::instance();
        // we don't yet have an id
        $inst->new[] = $obj;
    }

    static function addClean(DomainObject $obj ) {				// 将脏对象列表中的某个对象标识为干净的
        $self = self::instance();
        unset( $self->delete[$self->globalKey( $obj )] );
        unset( $self->dirty[$self->globalKey( $obj )] );

        $self->new = array_filter( $self->new, 
                function( $a ) use ( $obj ) { return !( $a === $obj ); } 
                );
    }

    function performOperations() {							// 提交所有操作
        foreach ( $this->dirty as $key=>$obj ) {
            $obj->finder()->insert( $obj );
        }
        foreach ( $this->new as $key=>$obj ) {
            $obj->finder()->insert( $obj );
        }
        $this->dirty = array();
        $this->new = array();
    } 
}
```