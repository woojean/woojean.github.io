# 观察者模式（PHP）

观察者模式的核心是把客户元素（观察者）从一个中心类（主体，被观察者）中分离出来。当主体知道事件发生时，观察者需要被通知到，同时不能将主体与观察者之间的关系进行硬编码。
为达到以上目的，需要强制主体实现Observable接口，强制观察者实现Observer接口，并允许观察者在主体上进行注册。

```php
<?php

// 被观察的主体需要实现该接口
interface Observable {
    function attach( Observer $observer );		// 注册观察者
    function detach( Observer $observer );		// 删除观察者
    function notify();						// 通知变化
}

// 观察者需要实现该接口
interface Observer {
    function update( Observable $observable );	// 当关注的事件发生时，触发的方法
}

class Login implements Observable {
    private $observers;						// 观察者列表
    private $storage;
    const LOGIN_USER_UNKNOWN = 1;
    const LOGIN_WRONG_PASS    = 2;
    const LOGIN_ACCESS          = 3;

    function __construct() {
        $this->observers = array();
    }

    function attach( Observer $observer ) {
        $this->observers[] = $observer;
    }

    function detach( Observer $observer ) {
        $this->observers = array_udiff( $this->observers, array( $observer ), 
                        function( $a, $b ) { return ($a === $b)?0:1; } );
    }

// 当事件发生时，逐一通知观察者
    function notify() {
        foreach ( $this->observers as $obs ) {
            $obs->update( $this );
        }
    }

    function handleLogin( $user, $pass, $ip ) {
        switch ( rand(1,3) ) {
            case 1: 
                $this->setStatus( self::LOGIN_ACCESS, $user, $ip );
                $ret = true; break;
            case 2:
                $this->setStatus( self::LOGIN_WRONG_PASS, $user, $ip );
                $ret = false; break;
            case 3:
                $this->setStatus( self::LOGIN_USER_UNKNOWN, $user, $ip );
                $ret = false; break;
        }
        $this->notify();		// 执行事件通知
        return $ret;
    }

    private function setStatus( $status, $user, $ip ) {
        $this->status = array( $status, $user, $ip ); 
    }

    function getStatus() {
        return $this->status;
    }
}

// 所有需要关注Login事件的类需要继承此类
abstract class LoginObserver implements Observer {
    private $login;		
    function __construct( Login $login ) {
        $this->login = $login; 
        $login->attach( $this );
    }

    function update( Observable $observable) {
// 判断使用的是自己被注册的Login对象，而不是任意的Observable对象
        if ( $observable === $this->login ) {
            $this->doUpdate( $observable );
        }
    }

    abstract function doUpdate( Login $login );
} 

// Login事件发生后需要执行的逻辑1
class SecurityMonitor extends LoginObserver {
    function doUpdate( Login $login ) {
        $status = $login->getStatus(); 
        if ( $status[0] == Login::LOGIN_WRONG_PASS ) {
            // send mail to sysadmin 
            print __CLASS__.":\tsending mail to sysadmin\n"; 
        }
    }
}

// Login事件发生后需要执行的逻辑2
class GeneralLogger  extends LoginObserver {
    function doUpdate( Login $login ) {
        $status = $login->getStatus(); 
        // add login data to log
        print __CLASS__.":\tadd login data to log\n"; 
    }
}

// Login事件发生后需要执行的逻辑3
class PartnershipTool extends LoginObserver {
    function doUpdate( Login $login ) {
        $status = $login->getStatus(); 
        // check $ip address 
        // set cookie if it matches a list
        print __CLASS__.":\tset cookie if it matches a list\n"; 
    }
}

// 使用
$login = new Login();
new SecurityMonitor( $login );  	// 注册1
new GeneralLogger( $login );		// 注册2
$pt = new PartnershipTool( $login );
$login->detach( $pt );
for ( $x=0; $x<10; $x++ ) {
    $login->handleLogin( "bob","mypass", '158.152.55.35' );
    print "\n";
}

?>
```