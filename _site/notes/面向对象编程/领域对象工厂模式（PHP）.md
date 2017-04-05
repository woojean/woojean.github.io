# 领域对象工厂模式（PHP）

把映射器类的createObject()方法移出来放到一个独立的类中，就构成了领域对象工厂模式。

```php
// 领域对象工厂基类
abstract class DomainObjectFactory {
    abstract function createObject( array $array );

    protected function getFromMap( $class, $id ) {
        return \woo\domain\ObjectWatcher::exists( $class, $id );
    }

    protected function addToMap( \woo\domain\DomainObject $obj ) {
        return \woo\domain\ObjectWatcher::add( $obj );
    }

}

// Venue类的领域对象工厂类
class VenueObjectFactory extends DomainObjectFactory {
    function createObject( array $array ) {
        $class = '\woo\domain\Venue';
        $old = $this->getFromMap( $class, $array['id'] );
        if ( $old ) { return $old; }
        $obj = new $class( $array['id'] );
        $obj->setname( $array['name'] );
        //$space_mapper = new SpaceMapper();
        //$space_collection = $space_mapper->findByVenue( $array['id'] );
        //$obj->setSpaces( $space_collection );
        $this->addToMap( $obj );
        return $obj;
    }
}

// Space类的领域对象工厂类
class SpaceObjectFactory extends DomainObjectFactory {
    function createObject( array $array ) {
        $class = '\woo\domain\Space';
        $old = $this->getFromMap( $class, $array['id'] );
        if ( $old ) { return $old; }
        $obj = new $class( $array['id'] );
        $obj->setname( $array['name'] );
        $ven_mapper = new VenueMapper();
        $venue = $ven_mapper->find( $array['venue'] );
        $obj->setVenue( $venue );

        $event_mapper = new EventMapper();
        $event_collection = $event_mapper->findBySpaceId( $array['id'] );        
        $obj->setEvents( $event_collection );
        return $obj;
    }
}
```