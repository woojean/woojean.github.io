# 策略模式（PHP）

策略模式定义了一系列的算法，并将每一个算法封装起来，而且使它们还可以相互替换。策略模式让算法独立于使用它的客户而独立变化。
例如，有一个测验问题类Question，还有一个mark()方法用来表示回答。当用户回答问题时可以为答案选择不同的标记方式，因此继承Question类来实现针对不同回答方式的子类，如MarkLogicQuestion、MatchQuestion、RegexpQuestion。此时，如果新增需求，要求支持不同类型的问题，如TextQuestion、AVQuestion，而每种问题又同样要支持之前的3种标记语言，如果完全依赖继承，则需要创建6个类。
“组合优于继承”，只要发现正在不断地在继承树的各个分支中重复同一个算法，无论是通过子类还是通过重复条件语句，应该将这些算法抽象成独立的类型。该例中的“算法”，即mark()方法。

```php
<?php
abstract class Question {
    protected $prompt;		// 问题提示
    protected $marker;		// 答案标记对象

    function __construct( $prompt, Marker $marker ) {
        $this->prompt=$prompt;
        $this->marker=$marker;
    }

    function mark( $response ) {		// 算法：标记用户的回答
        return $this->marker->mark( $response );
    }
}

class TextQuestion extends Question {
    // 文本类型的问题
}

class AVQuestion extends Question {
    // AV类型的问题
}

// 答案标记处理类
abstract class Marker {
    protected $test;

    function __construct( $test ) {
        $this->test = $test;
    }

    abstract function mark( $response );
}

class MarkLogicMarker extends Marker {		// mark()算法实现1
    function mark( $response ) {
        ...
return true;
    }
}

class MatchMarker extends Marker {			// mark()算法实现2
    function mark( $response ) {
        return ( $this->test == $response );
    }
}

class RegexpMarker extends Marker {			// mark()算法实现3
    function mark( $response ) {
        return ( preg_match( "$this->test", $response ) );
    }
}

// 使用
// 构造3个策略对象
$markers = array(	
new RegexpMarker( "/f.ve/" ),
 			new MatchMarker( "five" ),
    		new MarkLogicMarker( '$input equals "five"' )
		);

foreach ( $markers as $marker ) {
    print get_class( $marker )."\n";

// 用策略对象来处理问题
    $question = new TextQuestion( "how many beans make five", $marker );
    foreach ( array( "five", "four" ) as $response ) { 
        print "\tresponse: $response: ";
        if ( $question->mark( $response ) ) {
            print "well done\n";
        } else {
            print "never mind\n";
        }
    }
}

?>
```