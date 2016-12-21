# PHP手册“语言参考”部分阅读摘要

## 01 入门指引
略

### PHP的基本信息:
（1）全称：`Hypertext Preprocessor` 
（2）可嵌入到HTML中（用特殊起始和结束符号包含），因此尤其适合Web开发 
（3）运行在服务端 

### PHP能做的事：
（1）服务端脚本：需要Web服务器支持 
（2）命令行脚本：仅需要PHP解释器来执行 
（3）桌面应用程序：使用`PHP-GTK`（通常发布的PHP包中并不包含） 

### PHP的其他特性：
（1）能够在所有主流操作系统上使用 
（2）支持大多数的Web服务器 
（3）能够作为CGI处理器来工作 
（4）可以使用面向过程、面向对象，或者混合两种模式进行开发 
（5）能够用来动态输出图像、PDF、Flash等 
（6）支持很大范围的数据库，具体支持方式包括： 
    1）针对特定数据库的扩展 
    2）使用PDO抽象层 
    3）使用ODBC扩展 
    4）其他的一些特殊访问方式，如cURL或者socket 
（7）支持利用诸如LDAP、IMAP、SNMP、NNTP、POP3、HTTP、COM（Windows 环境）等等协议的服务 
（8）可以开放原始网络端口，使得任何其它的协议能够协同工作 
（9）支持和所有web开发语言之间的WDDX复杂数据交换 
（10）支持对Java对象的即时连接，并且可以透明地将其用作PHP对象 
（11）支持与Perl兼容的正则表达式（PCRE） 
（12）支持XML解析和访问 
总之，可以自由搭配操作系统和Web服务器，并且其自身功能强大、扩展丰富、使用起来非常灵活。 

### 简明教程
一个简单的实例，展示了PHP处理Web页面的最基本用法。几个关键点：
（1）PHP 会在输出时自动删除其结束符 ?> 后的一个换行。 
（2）外部变量不再被默认注册为全局变量。也就是说，从PHP4.2.0版开始，php.ini中的设置选项 register_globals 默认值变成了 off。建议用超全局数组变量来访问传递到脚本的值。 
没有其他值得note down的内容。

### 扩展补充
（1）关于PHP的标记  参考自:《PHP与MySql Web开发》 
PHP常用的起始、结束符号为：<? ... ?>。另有3种其他形式：
  1）XML风格 <?php ... ?> 
  2）js风格 <script language=’php’> ...</script> 
  3）ASP风格 <% ... %>

（2）关于CGI以及FastCGI  参考自:WIKI：通用网关接口、FastCGI
CGI 的工作方式，从 Web 服务器的角度看，是在特定的位置（位置指url，比如：http://www.example.com/wiki.cgi）定义了可以运行的CGI 程序（比如一个脚本，但也可能不是脚本，比如用C实现CGI）。当收到一个匹配URL的请求，相应的程序就会被调用（如果是脚本，则应该调用脚本解释器进行解释），并将客户端发送的数据作为输入。程序的输出会由 Web 服务器收集，并加上合适的档头，再发送回客户端。
一般每次的 CGI请求都需要新生成一个程序的副本来运行，这样大的工作量会很快将服务器压垮，因此一些更有效的技术可以让脚本解释器直接作为模块集成在 Web 服务器（例如：Apache，mod_php）中，这样就能避免重复载入和初始化解释器。不过这只是就那些需要解释器的高级语言（即解释语言）而言的，使用诸如C一类的编译语言则可以避免这种额外负荷。由于 C 及其他编译语言的程序与解释语言程序相比，前者的运行速度更快、对操作系统的负荷更小，使用编译语言程序是可能达到更高执行效率的，然而因为开发效率等原因，在目前解释性语言还是最合适的。
与为每个请求创建一个新的进程不同，FastCGI使用持续的进程来处理一连串的请求。这些进程由FastCGI服务器管理，而不是web服务器。 当进来一个请求时，web服务器把环境变量和这个页面请求通过一个socket比如FastCGI进程与web服务器(都位于本地）或者一个TCP connection（FastCGI进程在远端的server farm）传递给FastCGI进程。Apache通过mod_fcgid以及较早的第三方mod_fastcgi模块来实现。 

（3）PHP 会在输出时自动删除其结束符 ?> 后的一个换行
例1：
```php
<?php   
	echo "XXX";
?>YYY
```
输出：
XXXYYY

例2：一个换行，会被直接删除
```php
<?php   
	echo "XXX";
?>
YYY
```
输出：
XXXYYY

例3：多个换行，效果等同于一个空格
```php
<?php   
	echo "XXX";
?>

YYY
```
输出：
XXX YYY



## 02 语言参考-基本语法
（1）PHP也允许使用短标记<? 和 ?>，但不鼓励使用。只有通过激活php.ini中的short_open_tag配置指令或者在编译PHP时使用了配置选项--enable-short-tags 时才能使用短标记。ASP 风格标记仅在通过php.ini 配置文件中的指令asp_tags打开后才可用。 

（2）如果文件内容是纯PHP代码，最好在文件末尾删除PHP结束标记。这可以避免在PHP结束标记之后万一意外加入了空格或者换行符，会导致PHP开始输出这些空白，而脚本中此时并无输出的意图。 

（3）当PHP解释器碰到?>结束标记时就简单地将其后内容原样输出，直到碰到下一个开始标记；例外是处于条件语句中间时，此时 PHP 解释器会根据条件判断来决定哪些输出，哪些跳过：
```php
<?php if ($expression == true): ?>
  This will show if the expression is true.
<?php else: ?>
  Otherwise this will show.
<?php endif; ?> 
```
上例中PHP将跳过条件语句未达成的段落，即使该段落位于 PHP 开始和结束标记之外。

（4）如果将PHP嵌入到XML或XHTML中则需要使用<?php ?>标记以保持符合标准。 

（5）一段PHP代码中的结束标记隐含表示了一个分号；在一个PHP代码段中的最后一行可以不用分号结束。


### 扩展补充
（1）如果文件内容是纯PHP代码，最好在文件末尾删除PHP结束标记
例：
```php
<?php   
	echo "XXX";
?> 
```
这里在结尾处多了一个空格和一个换行
将输出：
<body>XXX 
</body>
即，在XXX后面多了一个空格，并换行

而：
```php
<?php   
	echo "XXX";
```
将输出：
<body>XXX</body>

（2）一段PHP代码中的结束标记隐含表示了一个分号
例：
```php
<?php   
	echo "XXX"
```
输出：
Parse error: syntax error, unexpected $end, expecting ',' or ';' in D:\wamp\www\t.php on line 2

```php
<?php   
	echo "XXX"
?>
```
输出：
XXX



## 03 语言参考-类型
（1）PHP共支持8种原始数据类型，其中包括：
	4种标量类型：boolean、integer、float（double）、string
2种复合类型：array、object
2种特殊类型：resource、NULL
 	double 和 float 是相同的，由于一些历史的原因，这两个名称同时存在。

（2）变量的类型通常不是由程序员设定的，确切地说，是由PHP根据该变量使用的上下文在运行时决定的。 

（3）有关类型的几个函数：
var_dump() 查看某个变量的值和类型
gettype() 查看某个变量的类型
settype() 将一个变量强制转换为某类型
is_int() 判断变量是否为整数
is_string() 判断变量是否为字符串
	
（4）要指定一个布尔值，使用关键字TRUE或FALSE，不区分大小写。
$foo = True;

（5）可以用(bool)或者(boolean)来强制将一个值转换成boolean，但是通常并不需要这么做，因为当运算符、函数或者流程控制结构需要一个boolean参数时，该值会被自动转换。

（6）当转换为boolean时，除以下值外，其他所有的值都会被认为是TRUE。被认为是FALSE的值包括：
1）布尔值 FALSE 本身  
2）整型值 0（零）  
3）浮点型值 0.0（零）  
4）空字符串，以及字符串 "0"   
5）不包括任何元素的数组  
6）不包括任何成员变量的对象（仅 PHP 4.0 适用）  
7）特殊类型 NULL（包括尚未赋值的变量）  
8）从空标记生成的SimpleXML对象 
需要注意的有两个：
1）字符串“0”是FALSE
2）整数值-1是TRUE

（7）整型数的字长和平台有关，字长可以用常量PHP_INT_SIZE来表示，自 PHP 4.4.0 和 PHP 5.0.5后，最大值可以用常量PHP_INT_MAX来表示。 
如：
```php
echo PHP_INT_SIZE;
echo '<br>';
echo PHP_INT_MAX;
```
将输出：
4
2147483647  // 21亿

（8）如果给定的一个数超出了integer的范围，将会被解释为float。同样如果执行的运算结果超出了integer范围，也会返回float。
```php
<?php
$large_number = 2147483647;
var_dump($large_number);	// int(2147483647)

$large_number = 2147483648;
var_dump($large_number); 	// float(2147483648)

$million = 1000000;
$large_number =  50000 * $million;
var_dump($large_number);	// float(50000000000)
?> 
```
（9）PHP中没有整除的运算符。1/2 产生出float 0.5。值可以舍弃小数部分强制转换为integer，或者使用round()函数可以更好地进行四舍五入。
```php
<?php
var_dump(25/7);         	// float(3.5714285714286) 
var_dump((int) (25/7)); 		// int(3)
var_dump(round(25/7));  	// float(4) 
?> 
```
（10）浮点数的精度有限。尽管取决于系统，PHP 通常使用IEEE 754双精度格式，则由于取整而导致的最大相对误差为1.11e-16。非基本数学运算可能会给出更大误差，并且要考虑到进行复合运算时的误差传递。以十进制能够精确表示的有理数如0.1或0.7，无论有多少尾数都不能被内部所使用的二进制精确表示，因此不能在不丢失一点点精度的情况下转换为二进制的格式。
如：
echo (int) ( (0.1+0.7) * 10 ); // 显示 7!
因为该结果内部的表示其实是类似 7.9999999999999991118...

（11）永远不要比较两个浮点数是否相等
要测试浮点数是否相等，要使用一个仅比该数值大一丁点的最小误差值。该值也被称为机器极小值（epsilon）或最小单元取整数，是计算中所能接受的最小的差别值。 
如：$a和$b在小数点后五位精度内都是相等的：
```php
$a = 1.23456789;
$b = 1.23456780;
$epsilon = 0.00001;

if(abs($a-$b) < $epsilon) {
    echo "true";
}
```
（12）常量 NAN
某些数学运算会产生一个由常量NAN所代表的结果。此结果代表着一个在浮点数运算中未定义或不可表述的值。任何拿此值与其它任何值进行的松散或严格比较的结果都是 FALSE。 
由于NAN代表着任何不同值，不应拿NAN去和其它值进行比较，包括其自身，应该用is_nan()来检查。 

（13）一个字符串可以用4种方式表达： 
	1）单引号  
2）双引号  
3）heredoc 语法结构  
4）nowdoc 语法结构（自 PHP 5.3.0 起） 

（14）不像双引号和heredoc语法结构，在单引号字符串中的变量和特殊字符的转义序列将不会被替换。

（15）要注意的是heredoc结束标识符这行除了可能有一个分号（;）外，绝对不能包含其它字符。这意味着标识符不能缩进，分号的前后也不能有任何空白或制表符。

（16）就象 heredoc 结构类似于双引号字符串，nowdoc 结构是类似于单引号字符串的。nowdoc 结构很象 heredoc 结构，但是 nowdoc 中不进行解析操作。

（17）当字符串用双引号或 heredoc 结构定义时，其中的变量将会被解析。这里有两种变量解析的规则：
1）简单规则：在一个string中嵌入一个变量，一个array的值，或一个object的属性。
2）复杂规则：不是因为其语法复杂而得名，而是因为它可以借助花括号紧接美元符号（{$）来使用复杂的表达式：
例：
//只有通过花括号语法才能正确解析带引号的键名
echo "This works: {$arr['key']}";

//当在字符串中使用多重数组时，一定要用括号将它括起来
echo "This works: {$arr['foo'][3]}";

注意：$必须紧挨着{
例：
```php
$great = 'fantastic';
// {和$之间多了一个空格，无效，输出: This is { fantastic}
echo "This is { $great}";
// 有效，输出： This is fantastic
echo "This is {$great}";
```

（18）string中的字符可以通过一个从0开始的下标，用类似array结构中的方括号包含对应的数字来访问和修改，比如$str[42]。可以把string当成字符组成的array。函数substr()和substr_replace()可用于操作多于一个字符的情况
string也可用花括号访问，比如$str{42}。PHP 的字符串在内部是字节组成的数组。因此用花括号访问或修改字符串对多字节字符集很不安全。
如：用超出字符串长度的下标写入将会拉长该字符串并以空格填充。非整数类型下标会被转换成整数。非法下标类型会产生一个 E_NOTICE 级别错误。用负数下标写入字符串时会产生一个 E_NOTICE 级别错误，用负数下标读取字符串时返回空字符串。写入时只用到了赋值字符串的第一个字符。用空字符串赋值则赋给的值是 NULL 字符。 

（19）PHP中的string的实现方式是一个由字节组成的数组再加上一个整数指明缓冲区长度，并无如何将字节转换成字符的信息，由程序员来决定。因此是二进制安全的。

（20）字符串会被按照该脚本文件相同的编码方式来编码。因此如果一个脚本的编码是 ISO-8859-1，则其中的字符串也会被编码为 ISO-8859-1。

（21）PHP 中的数组实际上是一个有序映射。注意顺序与索引，尤其是数字索引的区别。

（22）自 5.4 起可以使用短数组定义语法，用 [] 替代 array()。 
如：
// 自 PHP 5.4 起
$array = [
    "foo" => "bar",
    "bar" => "foo",
];

（23）数组key会有如下的强制转换：
	1）包含有合法整型值的字符串会被转换为整型。例如键名 "8" 实际会被储存为 8。但是 "08" 则不会强制转换，因为其不是一个合法的十进制数值。  
2）浮点数也会被转换为整型，意味着其小数部分会被舍去。例如键名 8.7 实际会被储存为 8。  
3）布尔值也会被转换成整型。即键名 true 实际会被储存为 1 而键名 false 会被储存为 0。  
4）Null 会被转换为空字符串，即键名 null 实际会被储存为 ""。  
5）数组和对象不能被用为键名。坚持这么做会导致警告：Illegal offset type。 
例：
```php
$array = array(
    1    => "a",
    "1"  => "b",  
    1.5  => "c",
    true => "d",
);
var_dump($array);
```
输出：
array(1) {
  [1]=>
  string(1) "d"
}
上例中所有的键名都被强制转换为1，所以最终值为最后一次的赋值

（24）PHP 数组可以同时含有integer和string类型的键名，因为PHP实际并不区分索引数组和关联数组。如果对给出的值没有指定键名，则取当前最大的整数索引值，而新的键名将是该值加一。如果指定的键名已经有了值，则该值会被覆盖。 
例：
```php
$array = array(
         "a",
         "b",
    6 => "c",
         "d",
);
var_dump($array);
```
将输出：
array(4) {
  [0]=>
  string(1) "a"
  [1]=>
  string(1) "b"
  [6]=>
  string(1) "c"
  [7]=>
  string(1) "d"
}
再比如：
```php
$a = array( 'color' => 'red',
	'taste' => 'sweet',
	'shape' => 'round',
	'name'  => 'apple',
	4        // key will be 0
);
```
（25）添加、删除数组值：
```php
	$arr = array(5 => 1, 12 => 2);
$arr[] = 56;    // This is the same as $arr[13] = 56;
                // at this point of the script
$arr["x"] = 42; // This adds a new element to
                // the array with key "x"          
unset($arr[5]); // This removes the element from the array
unset($arr);    // This deletes the whole array
```

（26）unset()函数允许删除数组中的某个键。但要注意数组将不会重建索引。如果需要删除后重建索引，可以用 array_values() 函数：
```php
$a = array(1 => 'one', 2 => 'two', 3 => 'three');
unset($a[2]);
/* will produce an array that would have been defined as
   $a = array(1 => 'one', 3 => 'three');
   and NOT
   $a = array(1 => 'one', 2 =>'three');
*/

$b = array_values($a);
// Now $b is array(0 => 'one', 1 =>'three')
```
（26）通过引用来拷贝数组（数组拷贝默认是值拷贝）
```php
$arr1 = array(2, 3);
$arr2 = $arr1;
$arr2[] = 4; // $arr2 is changed,
             // $arr1 is still array(2, 3)
             
$arr3 = &$arr1;
$arr3[] = 4; // now $arr1 and $arr3 are the same
```
（27）其他类型值转换为对象：
如果将一个对象转换成对象，它将不会有任何变化。
如果其它任何类型的值被转换成对象，将会创建一个内置类stdClass的实例。
如果该值为 NULL，则新的实例为空。
数组转换成对象将使键名成为属性名并具有相对应的值。
对于任何其它的值，名为scalar的成员变量将包含该值。 
$obj = (object) 'ciao';
echo $obj->scalar;  // outputs 'ciao'

（28）资源resource是一种特殊变量，保存了到外部资源的一个引用。资源是通过专门的函数来建立和使用的。
由于资源类型变量保存有为打开文件、数据库连接、图形画布区域等的特殊句柄，因此将其它类型的值转换为资源没有意义。

（29）特殊的NULL值表示一个变量没有值。NULL类型唯一可能的值就是NULL（不区分大小写）。在下列情况下一个变量被认为是 NULL： 
1）被赋值为 NULL。 
2）尚未被赋值。 
3）被unset()。 
判断是否为NULL：is_null()

（30）自PHP 5.4起可用callable类型指定回调类型callback。一个PHP的函数以string类型传递其名称。可以使用任何内置或用户自定义函数，但除了语言结构，例如：array()，echo，empty()，eval()，exit()，isset()，list()，print 或 unset()。 

（31）除了普通的用户自定义函数外，create_function()可以用来创建一个匿名回调函数。

（32）各种类型回调函数示例：
```php
// An example callback function
function my_callback_function() {
    echo 'hello world!';
}

// An example callback method
class MyClass {
    static function myCallbackMethod() {
        echo 'Hello World!';
    }
}

// Type 1: Simple callback 最简单的回调
call_user_func('my_callback_function'); 

// Type 2: Static class method call 回调类的静态方法
call_user_func(array('MyClass', 'myCallbackMethod')); 

// Type 3: Object method call  回调对象的方法
$obj = new MyClass();
call_user_func(array($obj, 'myCallbackMethod'));

// Type 4: Static class method call (As of PHP 5.2.3)  新的回调静态方法的形式
call_user_func('MyClass::myCallbackMethod');

// Type 5: Relative static class method call (As of PHP 5.3.0) 回调父类静态方法
class A {
    public static function who() {
        echo "A\n";
    }
}

class B extends A {
    public static function who() {
        echo "B\n";
    }
}
call_user_func(array('B', 'parent::who')); // A
```



### 补充扩展

PHP 在变量定义中不需要（或不支持）明确的类型定义；变量类型是根据使用该变量的上下文所决定的。
从手册上来看，主动的进行强制类型转换常常伴随很多未定义的风险。有关类型转换的内容没有过多的note down。




## 04 语言参考-变量、常量、表达式

（1）PHP的变量名是区分大小写的，可以用中文。

（2）$this是一个特殊变量，它不能被赋值。 

（3）只有有名字的变量才可以引用赋值：
```php
$foo = 25;
$bar = &$foo;      // 合法的赋值
$bar = &(24 * 7);  // 非法; 引用没有名字的表达式

function test()
{
   return 25;
}
$bar = &test();    // 非法
```
（4）未初始化的变量具有其类型的默认值-布尔类型的变量默认值是FALSE，整形和浮点型变量默认值是零，字符串型变量（例如用于echo中）默认值是空字符串以及数组变量的默认值是空数组。isset()语言结构可以用来检测一个变量是否已被初始化

（5）变量的作用域可以包含include或者require引入的文件。
如：
$a = 1;
include 'b.inc';
这里变量$a将会在包含文件b.inc中生效。

（6）在用户自定义函数中，一个局部函数范围将被引入。任何用于函数内部的变量按缺省情况将被限制在局部函数范围内。
如：
```php
$a = 1; /* global scope */

function Test()
{
    echo $a; /* reference to local scope variable */
}

Test();
```
这个脚本不会有任何输出，因为echo语句引用了一个局部版本的变量$a，而且在这个范围内，它并没有被赋值。
PHP 中全局变量在函数中使用时必须声明为global。

（7）在全局范围内访问变量的第二个办法，是用特殊的PHP自定义$GLOBALS数组：
```php
$a = 1;
$b = 2;

function Sum()
{
    $GLOBALS['b'] = $GLOBALS['a'] + $GLOBALS['b'];
}

Sum();
echo $b;
```
因为 $GLOBALS 是一个超全局变量，而超全局变量是不需要global 声明就可以使用的。

（8）静态变量仅在局部函数域中存在，但当程序执行离开此作用域时，其值并不丢失。即，其与普通局部变量的区别不在于作用域，而在于生命周期。

（9）静态变量的声明是在编译时解析的，因此在声明静态变量时不能用表达式进行赋值：
```php
function foo(){
    static $int = 0;       	// correct
    static $int = 1+2;    	// wrong  (as it is an expression)
    static $int = sqrt(121);  	// wrong  (as it is an expression too)

    $int++;
    echo $int;
}
```
（10）要将可变变量用于数组，必须解决一个模棱两可的问题（借助花括号）。这就是当写下$$a[1]时，解析器需要知道是想要$a[1]作为一个变量呢，还是想要$$a作为一个变量并取出该变量中索引为[1]的值。解决此问题的语法是，对第一种情况用${$a[1]}，对第二种情况用${$a}[1]。 

（11）类的属性也可以通过可变属性名来访问。可变属性名将在该调用所处的范围内被解析。例如，对于$foo->$bar 表达式，则会在本地范围来解析$bar并且其值将被用于$foo的属性名。对于$bar是数组单元时也是一样。 

（12）在 PHP 的函数和类的方法中，超全局变量不能用作可变变量。$this变量也是一个特殊变量，不能被动态引用。 

（13）来自PHP之外的变量：
1）HTML表单（GET和POST），根据特定的设置和个人的喜好，有很多种方法访问 HTML 表单中的数据。
2）HTTP Cookies

（14）对于通过表单或者Cookies传进来的变量，PHP将会自动将变量名中的点（如果有的话）替换成下划线。
如：<input type="image" src="image.gif" name="sub" />，点击后，将会加上两个变量：sub_x和sub_y。它们包含了用户点击图像的坐标。（这里浏览器发出的是sub.x和sub.y）

（15）和superglobals一样，常量的范围是全局的。不用管作用区域就可以在脚本的任何地方访问常量。

（16）常量只能包含标量数据（boolean、integer、float和string）。可以定义resource常量，但应尽量避免，因为会造成不可预料的结果。

（17）常量和变量有如下不同： 
	1）常量前面没有美元符号（$）；  
	2）常量只能用 define() 函数定义，而不能通过赋值语句；  
	3）常量可以不用理会变量的作用域而在任何地方定义和访问；  
	4）常量一旦定义就不能被重新定义或者取消定义；  
	5）常量的值只能是标量。 

（18）如果使用了一个未定义的常量，PHP 假定想要的是该常量本身的名字，如同用字符串调用它一样（CONSTANT 对应 "CONSTANT"）。
define("CONSTANT", "Hello world.");
echo CONSTANT; // outputs "Hello world."
echo Constant; // 输出 "Constant" 并发出一个提示级别错误信息
如果只想检查是否定义了某常量，用 defined() 函数。 

（19）使用关键字 const 定义常量
// 以下代码在 PHP 5.3.0 后可以正常工作
const CONSTANT = 'Hello World';
echo CONSTANT;
和使用define()来定义常量相反的是，使用const关键字定义常量必须处于最顶端的作用区域，因为用此方法是在编译时定义的。这就意味着不能在类定义内、函数内、循环内以及if语句之内用const来定义常量。 

（20）有八个魔术常量它们的值随着它们在代码中的位置改变而改变：（所谓的魔术常量，它们其实不是常量）
__LINE__ 文件中的当前行号   
__FILE__ 文件的完整路径和文件名 
__DIR__ 文件所在的目录 
__FUNCTION__ 函数名称（PHP 4.3.0 新加） 
__CLASS__ 类的名称（PHP 4.3.0 新加）   
__TRAIT__ Trait 的名字（PHP 5.4.0 新加）   
__METHOD__ 类的方法名（PHP 5.0.0 新加） 
__NAMESPACE__ 当前命名空间的名称（区分大小写）   



## 05 运算符

（1）名称比较特殊的运算符：
clone
new
(int) (float) (string) (array) (object) (bool)
instanceof
and xor or
````

（2）除法运算符总是返回浮点数。只有在下列情况例外：两个操作数都是整数（或字符串转换成的整数）并且正好能整除，这时它返回一个整数。 

（3）取模运算符的操作数在运算之前都会转换成整数（除去小数部分）。取模运算符%的结果和被除数的符号（正负号）相同。即$a % $b的结果和$a的符号相同。

（4）在PHP中普通的传值赋值行为有个例外就是碰到对象object时，在PHP 5中是以引用赋值的，除非明确使用了clone关键字来拷贝。 

（5）PHP 支持一个错误控制运算符：@。当将其放置在一个 PHP 表达式之前，该表达式可能产生的任何错误信息都被忽略掉。 
例：
$v = 10/0;
输出：
Warning: Division by zero in D:\wamp\www\t.php on line 3

而：
@$v = 10/0;
无任何输出
如果用set_error_handler()设定了自定义的错误处理函数，仍然会被调用，但是此错误处理函数可以（并且也应该）调用error_reporting()，而该函数在出错语句前有 @ 时将返回 0。 

（6）如果激活了track_errors特性，表达式所产生的任何错误信息都被存放在变量$php_errormsg中。此变量在每次出错时都会被覆盖，所以如果想用它的话就要尽早检查。 
（详细的错误处理控制见后续手册）

（7）PHP支持一个执行运算符：反引号（``）。PHP 将尝试将反引号中的内容作为外壳命令来执行，并将其输出信息返回（即，可以赋给一个变量而不是简单地丢弃到标准输出）。使用反引号运算符的效果与函数shell_exec()相同。 
反引号运算符在激活了安全模式或者关闭了shell_exec()时是无效的。
与其它某些语言不同，反引号不能在双引号字符串中使用。

（8）关于递增、递减运算符，有两个特别之处：
1）对布尔变量执行递增／递减运算符不影响布尔值。
2）递减NULL值也没有效果，但是递增NULL的结果是 1。

（9）"与"和"或"有两种不同形式运算符的原因是它们运算的优先级不同，优先级顺序：
1）! 
2）&& 
3）|| 
4）and 
5）xor 
6）or 
没有no或not运算符。

（10）数组运算符：
1）$a + $b 联合 $a和$b的联合。 
+运算符把右边的数组元素附加到左边的数组后面，两个数组中都有的键名，则只用左边数组中的，右边的被忽略。
2）$a == $b 相等 如果$a和$b具有相同的键／值对则为 TRUE。 
3）$a === $b 全等 如果$a和$b具有相同的键／值对并且顺序和类型都相同则为 TRUE。 
例：
$a = array("apple", "banana");
$b = array(1 => "banana", "0" => "apple");
var_dump($a == $b); // bool(true)
var_dump($a === $b); // bool(false) 顺序不相同

4）$a != $b 不等 如果$a不等于$b则为 TRUE。 
5）$a <> $b 不等 如果$a不等于$b则为 TRUE。 
6）$a !== $b 不全等 如果$a不全等于$b则为TRUE。 
 
（11）instanceof 运算符是 PHP 5 引进的。在此之前用is_a()，但是后来is_a()被废弃而用instanceof 替代了。注意自PHP 5.3.0 起，又恢复使用is_a()了。 


运算符部分值得note down的内容大概就是这些了，其他过于细节的内容不如直接看手册原文。




## 06 流程控制
（1）在 PHP 中，elseif也可以写成"else if"（两个单词），它和"elseif"（一个单词）的行为完全一样。
注意：如果用冒号（而不是花括号）来定义if/elseif条件，那就不能用两个单词的else if，否则PHP会产生解析错误。

（2）流程控制的替代语法：PHP 提供了一些流程控制的替代语法，包括 if，while，for，foreach 和 switch。替代语法的基本形式是把左花括号（{）换成冒号（:），把右花括号（}）分别换成 endif;，endwhile;，endfor;，endforeach; 以及 endswitch;。
不支持在同一个控制块内混合使用两种语法。 

（3）foreach有两种语法： 
```php
foreach (array_expression as $value)
    	statement
```

```php
foreach (array_expression as $key => $value)
    	statement
```
当 foreach 开始执行时，数组内部的指针会自动指向第一个单元。这意味着不需要在 foreach 循环之前调用 reset()。 

可以很容易地通过在 $value 之前加上 & 来修改数组的元素。此方法将以引用赋值而不是拷贝一个值。
```php
$arr = array(1, 2, 3, 4);
foreach ($arr as &$value) {
    $value = $value * 2;
}
// $arr is now array(2, 4, 6, 8)
echo $value; //输出8
unset($value); // 最后取消掉引用
```
注意：数组最后一个元素的 $value 引用在 foreach 循环之后仍会保留。建议使用 unset() 来将其销毁。 

$value 的引用仅在被遍历的数组可以被引用时才可用（例如是个变量）。以下代码则无法运行：
```php
foreach (array(1, 2, 3, 4) as &$value) {
    $value = $value * 2;
}
```

（4）用 list() 给嵌套的数组解包
```php
$array = [
    [1, 2],
    [3, 4],
];

foreach ($array as list($a, $b)) {
    // $a contains the first element of the nested array,
    // and $b contains the second element.
    echo "A: $a; B: $b\n";
}
```
输出：
A: 1; B: 2
A: 3; B: 4

list() 中的单元可以少于嵌套数组（但是不能多于），此时多出来的数组单元将被忽略：
```php
$array = [
    [1, 2],
    [3, 4],
];

foreach ($array as list($a)) {
    // Note that there is no $b here.
    echo "$a\n";
}
```
输出：
1
3

（5）break 可以接受一个可选的数字参数来决定跳出层数。
```php
$i = 0;
while (++$i) {
    switch ($i) {
    case 5:
        echo "At 5<br />\n";
        break 1;  /* 只退出 switch. */
    case 10:
        echo "At 10; quitting<br />\n";
        break 2;  /* 退出 switch 和 while 循环 */
    default:
        break;
    }
}
```
（6）注意和其它语言不同，continue语句可以作用到switch上，continue语句作用到switch上的作用类似于break。如果在循环中有一个switch并希望 continue 到外层循环中的下一轮循环，用 continue 2。

（7）case表达式可以是任何求值为简单类型的表达式，即整型或浮点数以及字符串。不能用数组或对象，除非它们被解除引用成为简单类型。 

（8）允许使用分号代替 case 语句后的冒号，例如：
```php
switch($beer)
{
    case 'tuborg';
    case 'carlsberg';
    case 'heineken';
        echo 'Good choice';
    break;
    default;
        echo 'Please make a new selection...';
    break;
}
```
（9）declare结构用来设定一段代码的执行指令。declare 的语法和其它流程控制结构相似： 
declare (directive)
    	statement
directive部分允许设定declare代码段的行为。目前只认识两个指令：ticks和encoding。 

（10）declare结构也可用于全局范围，影响到其后的所有代码（但如果有 declare 结构的文件被其它文件包含，则对包含它的父文件不起作用）。
例：
```php
// you can use this:
declare(ticks=1) {
    // entire script here
}

// or you can use this:
declare(ticks=1);
// entire script here
```
（11）Tick（时钟周期）是一个在declare代码段中解释器每执行N条可计时的低级语句就会发生的事件。N的值是在declare中的directive部分用ticks=N来指定的。 
不是所有语句都可计时。通常条件表达式和参数表达式都不可计时。 
例：
```php
declare(ticks=1);

// A function called on each tick event
function tick_handler()
{
    echo "tick_handler() called<br/>";
}

register_tick_function('tick_handler');
```
输出：
tick_handler() called
tick_handler() called
tick_handler() called

加一个赋值语句：
```php
declare(ticks=1);

// A function called on each tick event
function tick_handler()
{
    echo "tick_handler() called<br/>";
}

register_tick_function('tick_handler');
$var = 1;
```
输出：
tick_handler() called
tick_handler() called
tick_handler() called
tick_handler() called

（12）可以用 encoding 指令来对每段脚本指定其编码方式。 
```php
<?php
declare(encoding='ISO-8859-1');
// code here
?> 
```
	在PHP 5.3中除非在编译时指定了--enable-zend-multibyte，否则declare中的encoding值会被忽略。 

（13）在脚本文件中使用return语言结构：
如果在全局范围中调用，则当前脚本文件中止运行。
如果当前脚本文件是被 include 的或者 require 的，则控制交回调用文件。
此外，如果当前脚本是被 include 的，则 return 的值会被当作 include 调用的返回值。
如果在主脚本文件中调用 return，则脚本中止运行。
如果当前脚本文件是在 php.ini 中的配置选项 auto_prepend_file 或者 auto_append_file 所指定的，则此脚本文件中止运行。 
注意既然 return 是语言结构而不是函数，因此其参数没有必要用括号将其括起来。通常都不用括号，实际上也应该不用，这样可以降低 PHP 的负担。 如果没有提供参数，则一定不能用括号，此时返回 NULL。如果调用 return 时加上了括号却又没有参数会导致解析错误。 

（14）require和include几乎完全一样，除了处理失败的方式不同。require 在出错时产生E_COMPILE_ERROR 级别的错误。换句话说将导致脚本中止而include只产生警告（E_WARNING），脚本会继续运行。 

（15）被包含文件：
1）先按参数给出的路径寻找 
2）如果没有给出目录（只有文件名）时则按照 include_path 指定的目录寻找 
3）如果在include_path下没找到该文件则include最后才在调用脚本文件所在的目录和当前工作目录下寻找 
4）如果最后仍未找到文件则include 结构会发出一条警告；这一点和 require 不同，后者会发出一个致命错误。  
5）如果定义了路径——不管是绝对路径还是当前目录的相对路径，include_path 都会被完全忽略。 
6）当一个文件被包含时，其中所包含的代码继承了include所在行的变量范围。从该处开始，调用文件在该行处可用的任何变量在被调用的文件中也都可用。不过所有在包含文件中定义的函数和类都具有全局作用域。  
7）如果 include 出现于调用文件中的一个函数里，则被调用的文件中所包含的所有代码将表现得如同它们是在该函数内部定义的一样。所以它将遵循该函数的变量范围。此规则的一个例外是魔术常量，它们是在发生包含之前就已被解析器处理的。 

（16）当一个文件被包含时，语法解析器在目标文件的开头脱离 PHP 模式并进入 HTML 模式，到文件结尾处恢复。由于此原因，目标文件中需要作为PHP代码执行的任何代码都必须被包括在有效的PHP起始和结束标记之中。 

（17）如果allow_url_fopen被打开，可以用 URL而不是本地文件来指定要被包含的文件：
include 'http://www.example.com/file.txt?foo=1&bar=2'; 

（18）在失败时include返回FALSE并且发出警告。成功的包含则返回1，除非在包含文件中另外给出了返回值。 

（19）因为 include 是一个特殊的语言结构，其参数不需要括号。在比较其返回值时要注意。 
```php 
if ((include 'vars.php') == 'OK') {
    echo 'OK';
}
```
（20）如果在包含文件中定义有函数，这些函数不管是在return之前还是之后定义的，都可以独立在主文件中使用。 

（21）另一个将 PHP 文件"包含"到一个变量中的方法是用输出控制函数结合 include 来捕获其输出
例：使用输出缓冲来将 PHP 文件包含入一个字符串
```php
$string = get_include_contents('somefile.php');

function get_include_contents($filename) {
    if (is_file($filename)) {
        ob_start();
        include $filename;
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }
    return false;
}
```
（22）PHP支持goto语句，但仅在PHP 5.3及以上版本有效。





## 07 函数

（1）任何有效的PHP代码都有可能出现在函数内部，甚至包括其它函数和类定义。 

（2）函数无需在调用之前被定义，除非函数是有条件被定义时。 当一个函数是有条件被定义时，其定义必须在调用之前先处理。 
例：有条件的函数
```php
if ($makefoo) {
  function foo()
  {
    echo "I don't exist until program execution reaches me.\n";
  }
}
```
（3）PHP中的所有函数和类都具有全局作用域，可以定义在一个函数之内而在之外调用，反之亦然。 

（4）定义及调用时，函数名是大小写无关的。 

（5）PHP不支持函数重载，也不可能取消定义或者重定义已声明的函数。  

（6）PHP的函数支持可变数量的参数和默认参数。 

（7）在PHP中可以调用递归函数。但是要避免递归函数／方法调用超过100-200层，因为可能会使堆栈崩溃从而使当前脚本终止。 

（8）在5.6+版本中，可以如下使用可变参数：
```php
function sum(...$numbers) {
    $acc = 0;
    foreach ($numbers as $n) {
        $acc += $n;
    }
    return $acc;
}

echo sum(1, 2, 3, 4);
```
在PHP 5.5及更早版本中，使用函数func_num_args()，func_get_arg()，和func_get_args()来支持可变参数。

（9）从函数返回一个引用，必须在函数声明和指派返回值给一个变量时都使用引用运算符 &：
```php
function &returns_reference()
{
    return $someref;
}
$newref =&returns_reference();
```
（10）PHP支持可变函数的概念。这意味着如果一个变量名后有圆括号，PHP 将寻找与变量的值同名的函数，并且尝试执行它。 

（11）可变函数不能用于例如 echo，print，unset()，isset()，empty()，include，require 以及类似的语言结构。 

（12）也可以用可变函数的语法来调用一个对象的方法：
```php
$foo = new Foo();
$funcname = "Variable";
$foo->$funcname();   // This calls $foo->Variable()
```
（13）匿名函数（Anonymous functions），也叫闭包函数（closures），允许临时创建一个没有指定名称的函数。最经常用作回调函数（callback）参数的值。 

（14）闭包函数也可以作为变量的值来使用。PHP 会自动把此种表达式转换成内置类Closure的对象实例（匿名函数目前是通过Closure类来实现的）：
```php
$greet = function($name)
{
    printf("Hello %s\r\n", $name);
};

$greet('World');
```
（15）闭包可以从父作用域中继承变量。 任何此类变量都应该用use语言结构传递进去。 
```php
$message = 'hello';
// 没有 "use"
$example = function () {
    var_dump($message);
};
echo $example();

// 继承 $message
$example = function () use ($message) {
    var_dump($message);
};
echo $example();
```
输出：
	Notice: Undefined variable: message in D:\wamp\www\t.php on line 6
NULL 
string(5) "hello"

（16）从父作用域中继承变量与使用全局变量是不同的。全局变量存在于一个全局的范围，无论当前在执行的是哪个函数。而 闭包的父作用域是定义该闭包的函数（不一定是调用它的函数）。




## 08 类与对象

（1）PHP 对待对象的方式与引用和句柄相同，即每个变量都持有对象的引用，而不是整个对象的拷贝。 

（2）$this是一个到主叫对象的引用（通常是该方法所从属的对象，但如果是从第二个对象静态调用时也可能是另一个对象）。 
```php
class A
{
    function foo()
    {
        if (isset($this)) {
            echo '$this is defined (';
            echo get_class($this);
            echo ")\n";
        } else {
            echo "\$this is not defined.\n";
        }
    }
}

class B
{
    function bar()
    {
        // Note: the next line will issue a warning if E_STRICT is enabled.
        A::foo();
    }
}

$b = new B();
$b->bar();
```
输出：$this is defined (B)

（3）在类定义内部，可以用new self和new parent创建新对象。  

（4）在类的成员方法里面，可以用 ->（对象运算符）$this->property来访问非静态属性。静态属性则是用 ::（双冒号）self::$property 来访问。

（5）spl_autoload_register() 提供了一种更加灵活的方式来实现类的自动加载。因此，不再建议使用 __autoload() 函数，在以后的版本中它可能被弃用。

（6）自动加载不可用于 PHP 的 CLI 交互模式。 

（7）析构函数即使在使用 exit() 终止脚本运行时也会被调用。在析构函数中调用 exit() 将会中止其余关闭操作的运行。  

（8）试图在析构函数（在脚本终止时被调用）中抛出一个异常会导致致命错误。  

（9）同一个类的对象即使不是同一个实例也可以互相访问对方的私有与受保护成员。这是由于在这些对象的内部具体实现的细节都是已知的。  

（10）范围解析操作符（也可称作 Paamayim Nekudotayim）或者更简单地说是一对冒号，可以用于访问静态成员，类常量，还可以用于覆盖类中的属性和方法。 

（11）traits 是一种为类似 PHP 的单继承语言而准备的代码复用机制。可以理解为一组能被不同的类都能调用到的方法集合。（Traits不是类，不能被实例化）

（12）属性重载：
在给不可访问属性赋值时，__set() 会被调用。  
读取不可访问属性的值时，__get() 会被调用。 
当对不可访问属性调用 isset() 或 empty() 时，__isset() 会被调用。 
当对不可访问属性调用 unset() 时，__unset() 会被调用。 
属性重载只能在对象中进行。在静态方法中，这些魔术方法将不会被调用。

（13）方法重载：
在对象中调用一个不可访问方法时，__call() 会被调用。 
用静态方式中调用一个不可访问方法时，__callStatic() 会被调用。 

（14）属性不能被定义为 final，只有类和方法才能被定义为final。 

（15）对象复制可以通过clone关键字来完成（如果有，这将调用对象的__clone()方法）。对象中的__clone()方法不能被直接调用。 

（16）当对象被复制时，PHP 5会对对象的所有属性执行一个浅复制（shallow copy）。所有的引用属性仍然会是一个指向原来的变量的引用。当复制完成时，如果定义了__clone()方法，则新创建的对象（复制生成的对象）中的 __clone() 方法会被调用，可用于修改属性的值（如果有必要的话）。 

（17）对象比较：
当使用比较运算符（==）比较两个对象变量时，比较的原则是：如果两个对象的属性和属性值 都相等，而且两个对象是同一个类的实例，那么这两个对象变量相等。 
而如果使用全等运算符（===），这两个对象变量一定要指向某个类的同一个实例（即同一个对象）。 

（18）PHP 5 可以使用类型约束。函数的参数可以指定必须为对象（在函数原型里面指定类的名字），接口，数组（PHP 5.1 起）或者 callable（PHP 5.4 起）。不过如果使用 NULL 作为参数的默认值，那么在调用函数的时候依然可以使用 NULL 作为实参。

（19）如果一个类或接口指定了类型约束，则其所有的子类或实现也都如此。 

（20）类型约束不能用于标量类型，如int或string。traits 也不允许。 

（21）后期静态绑定：用于在继承范围内引用静态调用的类。"后期绑定"的意思是说，static:: 不再被解析为定义当前方法所在的类，而是在实际运行时计算的。
例：使用 self:: 或者 __CLASS__ 对当前类的静态引用，取决于定义当前方法所在的类： 
```php
class A {
    public static function who() {
        echo __CLASS__;
    }
    public static function test() {
        self::who();
    }
}

class B extends A {
    public static function who() {
        echo __CLASS__;
    }
}

B::test();
```
输出：A

如果换成static关键字：
```php
class A {
    public static function who() {
        echo __CLASS__;
    }
    public static function test() {
        static::who(); // 后期静态绑定从这里开始
    }
}

class B extends A {
    public static function who() {
        echo __CLASS__;
    }
}

B::test();
```
输出：B

准确说，后期静态绑定工作原理是存储了在上一个"非转发调用"（non-forwarding call）的类名。当进行静态方法调用时，该类名即为明确指定的那个（通常在 :: 运算符左侧部分）；当进行非静态方法调用时，即为该对象所属的类。
所谓的"转发调用"（forwarding call）指的是通过以下几种方式进行的静态调用：self::，parent::，static:: 以及 forward_static_call()。可用 get_called_class() 函数来得到被调用的方法所在的类名，static:: 则指出了其范围。 

（22）当对象作为参数传递，作为结果返回，或者赋值给另外一个变量，另外一个变量跟原来的不是引用的关系，只是他们都保存着同一个标识符的拷贝，这个标识符指向同一个对象的真正内容。 

（23）在应用程序中序列化对象以便在之后使用，强烈推荐在整个应用程序都包含对象的类的定义。





## 09 命名空间、异常处理、生成器

（1）命名空间通过关键字namespace来声明。如果一个文件中包含命名空间，它必须在其它所有代码之前声明命名空间。 在声明命名空间之前唯一合法的代码是用于定义源文件编码方式的declare语句。另外，所有非PHP代码包括空白符都不能出现在命名空间的声明之前： 
<html>
<?php
namespace MyProject; // 致命错误 -　命名空间必须是程序脚本的第一条语句
?> 

（2）与PHP其它的语言特征不同，同一个命名空间可以定义在多个文件中，即允许将同一个命名空间的内容分割存放在不同的文件中。 

（3）命名空间的名字可以使用分层次的方式定义（使用反斜杠做分隔符）
```php
namespace MyProject\Sub\Level;

const CONNECT_OK = 1;
class Connection { /* ... */ }
function connect() { /* ... */  }
```
（4）也可以在同一个文件中定义多个命名空间。在实际的编程实践中，非常不提倡在同一个文件中定义多个命名空间。这种方式的主要用于将多个 PHP 脚本合并在同一个文件中。 

（5）将全局的非命名空间中的代码与命名空间中的代码组合在一起，只能使用大括号形式的语法。全局代码必须用一个不带名称的namespace语句加上大括号括起来：
```php
<?php
declare(encoding='UTF-8');
namespace MyProject {

const CONNECT_OK = 1;
class Connection { /* ... */ }
function connect() { /* ... */  }
}

namespace { // global code
session_start();
$a = MyProject\connect();
echo MyProject\Connection::start();
}
?> 
```
除了开始的declare语句外，命名空间的括号外不得有任何PHP代码。 

（6）与在文件系统中访问一个文件类似，类名同样可以使用三种方式引用：
1）非限定名称，或不包含前缀的类名称，例如 $a=new foo(); 或 foo::staticmethod();如果当前命名空间是 currentnamespace，foo 将被解析为 currentnamespace\foo。如果使用 foo 的代码是全局的，不包含在任何命名空间中的代码，则 foo 会被解析为foo。
2）限定名称,或包含前缀的名称，例如 $a = new subnamespace\foo(); 或 subnamespace\foo::staticmethod();。如果当前的命名空间是 currentnamespace，则 foo 会被解析为 currentnamespace\subnamespace\foo。如果使用 foo 的代码是全局的，不包含在任何命名空间中的代码，foo 会被解析为subnamespace\foo。 
3）完全限定名称，或包含了全局前缀操作符的名称，例如， $a = new \currentnamespace\foo(); 或 \currentnamespace\foo::staticmethod();。在这种情况下，foo 总是被解析为代码中的文字名(literal name)currentnamespace\foo。 

（7）访问任意全局类、函数或常量，都可以使用完全限定名称，例如\strlen()或\Exception或\INI_ALL。 

（8）PHP支持两种抽象的访问当前命名空间内部元素的方法，__NAMESPACE__ 魔术常量和namespace关键字。 
常量__NAMESPACE__的值是包含当前命名空间名称的字符串。在全局的，不包括在任何命名空间中的代码，它包含一个空的字符串。 
关键字namespace可用来显式访问当前命名空间或子命名空间中的元素。它等价于类中的self操作符。

（9）PHP 命名空间支持 有两种使用别名或导入方式：为类名称使用别名，或为命名空间名称使用别名。在PHP中，别名是通过操作符 use 来实现的。

（10）在一个命名空间中，当 PHP 遇到一个非限定的类、函数或常量名称时，它使用不同的优先策略来解析该名称。
（具体规则略过）

（11）生成器提供了一种更容易的方法来实现简单的对象迭代，相比较定义类实现 Iterator 接口的方式，性能开销和复杂性大大降低。 

（12）生成器允许你在 foreach 代码块中写代码来迭代一组数据而不需要在内存中创建一个数组, 那会使你的内存达到上限，或者会占据可观的处理时间。相反，你可以写一个生成器函数，就像一个普通的自定义函数一样, 和普通函数只返回一次不同的是, 生成器可以根据需要yield多次，以便生成需要迭代的值。 





## 10 预定义变量、预定义异常、预定义接口

（1）PHP 中的许多预定义变量都是"超全局的"，这意味着它们在一个脚本的全部作用域中都可用。在函数或方法中无需执行global $variable; 就可以访问它们：
$GLOBALS  
$_SERVER 
$_GET 
$_POST 
$_FILES 
$_COOKIE 
$_SESSION 
$_REQUEST 
$_ENV 

（2）$GLOBALS — 引用全局作用域中可用的全部变量。与所有其他超全局变量不同，$GLOBALS在PHP中总是可用的。 

（3）$_SERVER -- $HTTP_SERVER_VARS [已弃用] — 服务器和执行环境信息
$_SERVER 是一个包含了诸如头信息(header)、路径(path)、以及脚本位置(script locations)等等信息的数组。这个数组中的项目由 Web 服务器创建。不能保证每个服务器都提供全部项目。如果以命令行方式运行 PHP，下面列出的元素几乎没有有效的(或是没有任何实际意义的)。 
    1）'PHP_SELF' 当前执行脚本的文件名，与 document root 有关。 
    2）'argv' 传递给该脚本的参数的数组。当通过 GET 方式调用时，该变量包含query string。 
    3）'argc' 包含命令行模式下传递给该脚本的参数的数目(如果运行在命令行模式下)。  
    4）'GATEWAY_INTERFACE' 服务器使用的 CGI 规范的版本；例如，"CGI/1.1"。  
    5）'SERVER_ADDR' 当前运行脚本所在的服务器的 IP 地址。 
    6）'SERVER_NAME' 当前运行脚本所在的服务器的主机名。 
    7）'SERVER_SOFTWARE' 服务器标识字符串，在响应请求时的头信息中给出。  
    8）'SERVER_PROTOCOL' 请求页面时通信协议的名称和版本。例如，"HTTP/1.0"。  
    9）'REQUEST_METHOD' 访问页面使用的请求方法；例如，"GET", "HEAD"，"POST"，"PUT"。  
    10）'REQUEST_TIME' 请求开始时的时间戳。从 PHP 5.1.0 起可用。  
    11）'REQUEST_TIME_FLOAT' 请求开始时的时间戳，微秒级别的精准度。 自 PHP 5.4.0 开始生效。  
    12）'QUERY_STRING' query string（查询字符串），如果有的话，通过它进行页面访问。  
    13）'DOCUMENT_ROOT' 当前运行脚本所在的文档根目录。在服务器配置文件中定义。  
    14）'HTTP_ACCEPT' 当前请求头中 Accept: 项的内容，如果存在的话。  
    15）'HTTP_ACCEPT_CHARSET' 当前请求头中 Accept-Charset: 项的内容，如果存在的话。例如："iso-8859-1,*,utf-8"。  
    16）'HTTP_ACCEPT_ENCODING' 当前请求头中 Accept-Encoding: 项的内容，如果存在的话。例如："gzip"。  
    17）'HTTP_ACCEPT_LANGUAGE' 当前请求头中 Accept-Language: 项的内容，如果存在的话。例如："en"。  
    18）'HTTP_CONNECTION' 当前请求头中 Connection: 项的内容，如果存在的话。例如："Keep-Alive"。  
    19）'HTTP_HOST' 当前请求头中 Host: 项的内容，如果存在的话。  
    20）'HTTP_REFERER' 引导用户代理到当前页的前一页的地址（如果存在）。由 user agent 设置决定。并不是所有的用户代理都会设置该项，有的还提供了修改 HTTP_REFERER 的功能。简言之，该值并不可信。  
    21）'HTTP_USER_AGENT' 当前请求头中 User-Agent: 项的内容，如果存在的话。该字符串表明了访问该页面的用户代理的信息。一个典型的例子是：Mozilla/4.5 [en] (X11; U; Linux 2.2.9 i586)。除此之外，你可以通过 get_browser() 来使用该值，从而定制页面输出以便适应用户代理的性能。  
    22）'HTTPS' 如果脚本是通过 HTTPS 协议被访问，则被设为一个非空的值。  
    23）'REMOTE_ADDR' 浏览当前页面的用户的 IP 地址。  
    24）'REMOTE_HOST' 浏览当前页面的用户的主机名。DNS 反向解析不依赖于用户的 REMOTE_ADDR。  
    25）'REMOTE_PORT' 用户机器上连接到 Web 服务器所使用的端口号。  
    26）'REMOTE_USER' 经验证的用户  
    27）'REDIRECT_REMOTE_USER' 验证的用户，如果请求已在内部重定向。  
    28）'SCRIPT_FILENAME' 当前执行脚本的绝对路径。  
    29）'SERVER_ADMIN' 该值指明了 Apache 服务器配置文件中的 SERVER_ADMIN 参数。 
    30）'SERVER_PORT' Web 服务器使用的端口。默认值为 "80"。如果使用 SSL 安全连接，则这个值为用户设置的 HTTP 端口。  
    31）'SERVER_SIGNATURE' 包含了服务器版本和虚拟主机名的字符串。  
    32）'PATH_TRANSLATED' 当前脚本所在文件系统（非文档根目录）的基本路径。这是在服务器进行虚拟到真实路径的映像后的结果。  
    33）'SCRIPT_NAME' 包含当前脚本的路径。这在页面需要指向自己时非常有用。__FILE__ 常量包含当前脚本(例如包含文件)的完整路径和文件名。  
    34）'REQUEST_URI'  URI用来指定要访问的页面。例如 "/index.html"。  
    35）'PHP_AUTH_DIGEST' 当作为 Apache 模块运行时，进行 HTTP Digest 认证的过程中，此变量被设置成客户端发送的"Authorization" HTTP 头内容（以便作进一步的认证操作）。  
    36）'PHP_AUTH_USER' 当 PHP 运行在 Apache 或 IIS（PHP 5 是 ISAPI）模块方式下，并且正在使用 HTTP 认证功能，这个变量便是用户输入的用户名。  
    37）'PHP_AUTH_PW' 当 PHP 运行在 Apache 或 IIS（PHP 5 是 ISAPI）模块方式下，并且正在使用 HTTP 认证功能，这个变量便是用户输入的密码。  
    38）'AUTH_TYPE' 当 PHP 运行在 Apache 模块方式下，并且正在使用 HTTP 认证功能，这个变量便是认证的类型。  
    39）'PATH_INFO' 包含由客户端提供的、跟在真实脚本名称之后并且在查询语句（query string）之前的路径信息。 
    40）'ORIG_PATH_INFO' 在被PHP处理之前，"PATH_INFO" 的原始版本。  

（4）$_GET -- $HTTP_GET_VARS [已弃用] — HTTP GET 变量，通过URL参数传递给当前脚本的变量的数组。 

（5）$_POST -- $HTTP_POST_VARS [已弃用] — HTTP POST 变量，通过 HTTP POST 方法传递给当前脚本的变量的数组。 

（6）$_FILES -- $HTTP_POST_FILES [已弃用] — HTTP 文件上传变量 

（7）$_REQUEST — HTTP Request 变量，默认情况下包含了 $_GET，$_POST 和 $_COOKIE 的数组。  
由于$_REQUEST中的变量通过GET，POST和COOKIE 输入机制传递给脚本文件，因此可以被远程用户篡改而并不可信。这个数组的项目及其顺序依赖于 PHP 的 variables_order 指令的配置。 

（8）$_SESSION -- $HTTP_SESSION_VARS [已弃用] — Session 变量 

（9）$_ENV -- $HTTP_ENV_VARS [已弃用] — 环境变量 
这些变量被从PHP 解析器的运行环境导入到PHP 的全局命名空间。很多是由支持 PHP 运行的 Shell 提供的，并且不同的系统很可能运行着不同种类的 Shell，所以不可能有一份确定的列表。 

（10）$_COOKIE -- $HTTP_COOKIE_VARS [已弃用] — HTTP Cookies 

（11）$php_errormsg — 前一个错误信息。这个变量只在错误发生的作用域内可用，并且要求 track_errors 配置项是开启的（默认是关闭的）。 

（12）$HTTP_RAW_POST_DATA — 原生POST数据
一般而言，使用 php://input 代替 $HTTP_RAW_POST_DATA。 

（13）$http_response_header — HTTP 响应头

（14）Exception是所有异常的基类，类摘要如下：
```php
Exception  {
/* 属性 */
protected string $message ;
protected int $code ;
protected string $file ;
protected int $line ;

/* 方法 */
public __construct ([ string $message = "" [, int $code = 0 [, Exception $previous = NULL ]]] )
final public string getMessage ( void )
final public Exception getPrevious ( void )
final public int getCode ( void )
final public string getFile ( void )
final public int getLine ( void )
final public array getTrace ( void )
final public string getTraceAsString ( void )
public string __toString ( void )
final private void __clone ( void )
}
```
（15）Traversable（遍历）接口：检测一个类是否可以使用foreach进行遍历的接口。 
这是一个无法在PHP脚本中实现的内部引擎接口。这个接口没有任何方法，它的作用仅仅是作为所有可遍历类的基本接口。 IteratorAggregate 或 Iterator 接口可以用来代替它。  

（16）Iterator（迭代器）接口：
```php
Iterator  extends Traversable  {
/* 方法 */
abstract public mixed current ( void )
abstract public scalar key ( void )
abstract public void next ( void )
abstract public void rewind ( void )
abstract public boolean valid ( void )
}
```
（17）IteratorAggregate（聚合式迭代器）接口：
IteratorAggregate  extends Traversable  {

/* 方法 */
abstract public Traversable getIterator ( void )
}
例：让一个类拥有数组的操作
```php
class myData implements IteratorAggregate {
    public $property1 = "Public property one";
    public $property2 = "Public property two";
    public $property3 = "Public property three";

    public function __construct() {
        $this->property4 = "last property";
    }

    public function getIterator() {
        return new ArrayIterator($this);
    }
}

$obj = new myData;

foreach($obj as $key => $value) {
    var_dump($key, $value);
    echo "\n";
}
```
（18）ArrayAccess（数组式访问）接口
```php
ArrayAccess  {
/* 方法 */
abstract public boolean offsetExists ( mixed $offset )
abstract public mixed offsetGet ( mixed $offset )
abstract public void offsetSet ( mixed $offset , mixed $value )
abstract public void offsetUnset ( mixed $offset )
}
```
（19）Serializable 自定义序列化的接口
```php
Serializable  {
/* 方法 */
abstract public string serialize ( void )
abstract public mixed unserialize ( string $serialized )
}
```
实现此接口的类将不再支持 __sleep() 和 __wakeup()。

（20）Closure 类：用于代表匿名函数的类。


（完）
