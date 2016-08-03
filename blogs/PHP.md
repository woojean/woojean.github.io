 ## PHP中如何创建、销毁会话？
开始会话：
开始一个会话有2种方法：
1）session_start()函数：该函数将检查是否有一个会话ID存在，如果不存在就创建一个，如果已经存在，就将这个已经注册的会话变量载入以便使用。
2）将PHP设置为当有用户访问网站的时候就自动启动一个会话，具体方法是打开php.ini中的session.auto_start选项。这种方法有一个很大的缺点：无法使用对象作为会话变量，因为该对象的类定义必须在创建该对象的会话开始之前载入。

创建会话变量：
可以通过全局数组$_SESSION来注册新的会话变量，如：
$_SESSION[“new_var”] = “value”;
会话变量创建后，只有在会话结束，或者手动重置时才会失效。此外，php.ini中的gc_maxlifetime指令确定了会话的持续时间，超时后会话将会被垃圾回收。

使用会话变量：
if(isset($_SESSION[‘myvar’])){
...

销毁会话：
销毁会话变量：unset($_SESSION[‘myvar’]);
一次销毁所有的会话变量：$_SESSION = array();
清除会话ID：session_destroy();

foreach循环中使用引用有什么潜在问题？
$array = [1, 2, 3]; 
echo implode(',', $array), "<br/>"; 	// 1,2,3

foreach ($array as &$value) {}    
echo implode(',', $array), "<br/>"; 	// 1,2,3	

foreach ($array as $value) {}     	 
echo implode(',', $array), "<br/>";		// 1,2,2

第一个循环过后，$value是数组中最后一个元素的引用。
第二个循环开始：
第1步：复制$arr[0]到$value（注意此时$value是$arr[2]的引用），这时数组变成[1,2,1]
第2步：复制$arr[1]到$value，这时数组变成[1,2,2]
第3步：复制$arr[2]到$value，这时数组变成[1,2,2]
综上，最终结果就是1,2,2
避免这种错误最好的办法就是在循环后立即用unset函数销毁变量：
$arr = array(1, 2, 3, 4); 
foreach ($arr as &$value) { 
    $value = $value * 2; 
} 
unset($value);

__autoload和spl_autoload_register()函数直接有什么区别和联系？
void __autoload ( string $class )
可以通过定义这个函数来启用类的自动加载，参数为待加载的类名。

例如：
./myClass.php
<?php
class myClass {
    public function __construct() {
        echo "myClass init'ed successfuly!!!";
    }
}
?>

./index.php
<?php
function __autoload($classname) {
    $filename = "./". $classname .".php";			# 拼凑查找类文件的逻辑
    include_once($filename);
}

$obj = new myClass();							# 直接使用类名，会触发对__autoload函数的调用
?>

bool spl_autoload_register ([ callable $autoload_function [, bool $throw = true [, bool $prepend = false ]]] )
将函数注册到SPL __autoload函数队列中。如果该队列中的函数尚未激活，则激活它们。
如果在程序中已经实现了__autoload()函数，它必须显式注册到__autoload()队列中。因为 spl_autoload_register()函数会将Zend Engine中的__autoload()函数取代为spl_autoload()或spl_autoload_call()。

spl_autoload_register 可以很好地处理需要多个加载器的情况，这种情况下spl_autoload_register会按顺序依次调用之前注册过的加载器。作为对比， __autoload 因为是一个函数，所以只能被定义一次。

例如：
function loadprint( $class ) {
 	$file = $class . '.class.php';  
 	if (is_file($file)) {  
  		require_once($file);  
 	} 
} 
 
spl_autoload_register( 'loadprint' ); 
 
$obj = new PRINTIT();
$obj->doPrint();
将__autoload换成loadprint函数。但是loadprint不会像__autoload自动触发，这时spl_autoload_register()就起作用了，它告诉PHP碰到没有定义的类就执行loadprint()。 

例：spl_autoload_register() 调用静态方法 

class test {
 	public static function loadprint( $class ) {
  		$file = $class . '.class.php';  
  		if (is_file($file)) {  
   			require_once($file);  
  		} 
 	}
} 
 
spl_autoload_register(array('test','loadprint'));
// 另一种写法：spl_autoload_register("test::loadprint"); 
 
$obj = new PRINTIT();
$obj->doPrint();


## PHP中删除数组元素后，如何重建索引？
unset()函数允许删除数组中的某个键。但要注意数组将不会重建索引。如果需要删除后重建索引，可以用 array_values() 函数：
$a = array(1 => 'one', 2 => 'two', 3 => 'three');
unset($a[2]);
/* will produce an array that would have been defined as
   $a = array(1 => 'one', 3 => 'three');
   and NOT
   $a = array(1 => 'one', 2 =>'three');
*/

$b = array_values($a);
// Now $b is array(0 => 'one', 1 =>'three')

## 能否在函数内部定义函数或类？
任何有效的PHP代码都有可能出现在函数内部，甚至包括其它函数和类定义。
PHP中的所有函数和类都具有全局作用域，可以定义在一个函数之内而在之外调用，反之亦然。

## 如何对数组元素使用可变变量？
要将可变变量用于数组，必须解决一个模棱两可的问题（借助花括号）。这就是当写下$$a[1]时，解析器需要知道是想要$a[1]作为一个变量呢，还是想要$$a作为一个变量并取出该变量中索引为[1]的值。解决此问题的语法是，对第一种情况用${$a[1]}，对第二种情况用${$a}[1]。

## isset()和empty()有什么区别？
isset() 如果变量存在，则返回true，否则返回false。对应的unset函数用来销毁一个变量。
empty() 变量存在，且值不为NULL、0、””，则返回true。

## 有哪些魔术常量？其行为是什么样的？它们真的是常量吗？
__LINE__ 文件中的当前行号  
__FILE__ 文件的完整路径和文件名
__DIR__ 文件所在的目录 
__FUNCTION__ 函数名称（PHP 4.3.0 新加）
__CLASS__ 类的名称（PHP 4.3.0 新加）  
__TRAIT__ Trait 的名字（PHP 5.4.0 新加）  
__METHOD__ 类的方法名（PHP 5.0.0 新加）
__NAMESPACE__ 当前命名空间的名称（区分大小写）


## 如何实现对象深复制？
当对象作为参数传递，作为结果返回，或者赋值给另外一个变量，另外一个变量跟原来的不是引用的关系，只是他们都保存着同一个标识符的拷贝，这个标识符指向同一个对象的真正内容。

对象复制可以通过clone关键字来完成（如果有，这将调用对象的__clone()方法）。对象中的__clone()方法不能被直接调用。 

当对象被复制时，PHP 5会对对象的所有属性执行一个浅复制（shallow copy）。所有的引用属性仍然会是一个指向原来的变量的引用。当复制完成时，如果定义了__clone()方法，则新创建的对象（复制生成的对象）中的 __clone() 方法会被调用，可用于修改属性的值（如果有必要的话）。


## 有哪些操作数组指针的函数？
每个数组都有一个内部指针指向数组中的当前元素，如果创建一个新数组，那么当前指针就将被初始化，并指向数组的第一个元素。
current($arr); 返回指针当前所指向的元素
each($arr); 在指针前移一个位置之前返回当前元素
next($arr); 将指针迁移，再返回新的当前元素
reset($arr); 将指针移到数组第一个元素的位置
end($arr); 将指针移到数组的最后一个元素的位置
prev($arr); 将当前指针往前移一个位置，再返回新的当前元素
pos($arr); 为current函数的别名

## 常量和变量有哪些不同？作用域是什么样的？
和super globals一样，常量的范围是全局的。不用管作用区域就可以在脚本的任何地方访问常量。

常量只能包含标量数据（boolean、integer、float和string）。可以定义resource常量，但应尽量避免，因为会造成不可预料的结果。

常量和变量有如下不同： 
	1）常量前面没有美元符号（$）；  
	2）常量只能用 define() 函数定义，而不能通过赋值语句；  
	3）常量可以不用理会变量的作用域而在任何地方定义和访问；  
	4）常量一旦定义就不能被重新定义或者取消定义；  
	5）常量的值只能是标量。

如果使用了一个未定义的常量，PHP 假定想要的是该常量本身的名字，如同用字符串调用它一样（CONSTANT 对应 "CONSTANT"）。
define("CONSTANT", "Hello world.");
echo CONSTANT; // outputs "Hello world."
echo Constant; // 输出 "Constant" 并发出一个提示级别错误信息
如果只想检查是否定义了某常量，用 defined() 函数。 

使用关键字 const 定义常量
// 以下代码在 PHP 5.3.0 后可以正常工作
const CONSTANT = 'Hello World';
echo CONSTANT;
和使用define()来定义常量相反的是，使用const关键字定义常量必须处于最顶端的作用区域，因为用此方法是在编译时定义的。这就意味着不能在类定义内、函数内、循环内以及if语句之内用const来定义常量。 

# PHP中那些函数是同步阻塞的
同步阻塞函数

mysql、mysqli、pdo以及其他DB操作函数
sleep、usleep
curl
stream、socket扩展的函数
swoole_client同步模式
memcache、redis扩展函数
file_get_contents/fread等文件读取函数
swoole_server->taskwait
swoole_server->sendwait
swoole_server的PHP代码中有上述函数，Server就是同步服务器
代码中没有上述函数就是异步服务器
异步非阻塞函数

swoole_client异步模式
mysql-async库
redis-async库
swoole_timer_tick/swoole_timer_after
swoole_event系列函数
swoole_table/swoole_atomic/swoole_buffer
swoole_server->task/finish函数

## 为什么在被包含文件中仍然要使用PHP起始标志？
当一个文件被包含时，语法解析器在目标文件的开头脱离 PHP 模式并进入 HTML 模式，到文件结尾处恢复。由于此原因，目标文件中需要作为PHP代码执行的任何代码都必须被包括在有效的PHP起始和结束标记之中。

## PHP中有哪几种表达字符串的方式？区别是什么？
一个字符串可以用4种方式表达： 
	1）单引号  
2）双引号  
3）heredoc 语法结构  
4）nowdoc 语法结构（自 PHP 5.3.0 起） 
不像双引号和heredoc语法结构，在单引号字符串中的变量和特殊字符的转义序列将不会被替换。
要注意的是heredoc结束标识符这行除了可能有一个分号（;）外，绝对不能包含其它字符。这意味着标识符不能缩进，分号的前后也不能有任何空白或制表符。
就象 heredoc 结构类似于双引号字符串，nowdoc 结构是类似于单引号字符串的。nowdoc 结构很象 heredoc 结构，但是 nowdoc 中不进行解析操作。
字符串会被按照该脚本文件相同的编码方式来编码。因此如果一个脚本的编码是 ISO-8859-1，则其中的字符串也会被编码为 ISO-8859-1。

## 在脚本文件中使用return，有哪些不同的场景和行为？
在脚本文件中使用return语言结构：
如果在全局范围中调用，则当前脚本文件中止运行。
如果当前脚本文件是被 include 的或者 require 的，则控制交回调用文件。
此外，如果当前脚本是被 include 的，则 return 的值会被当作 include 调用的返回值。
如果在主脚本文件中调用 return，则脚本中止运行。
如果当前脚本文件是在 php.ini 中的配置选项 auto_prepend_file 或者 auto_append_file 所指定的，则此脚本文件中止运行。 
注意既然 return 是语言结构而不是函数，因此其参数没有必要用括号将其括起来。通常都不用括号，实际上也应该不用，这样可以降低 PHP 的负担。 如果没有提供参数，则一定不能用括号，此时返回 NULL。如果调用 return 时加上了括号却又没有参数会导致解析错误。

## Cookies与Session
Cookie与Session都属于会话跟踪技术。理论上，一个用户的所有请求操作都应该属于同一个会话。HTTP协议是无状态的协议。一旦数据交换完毕，客户端与服务器端的连接就会关闭（HTTP基于TCP），再次交换数据需要建立新的连接。这就意味着服务器无法从连接上跟踪会话。
在Session出现之前，基本上所有的网站都采用Cookie来跟踪会话。目前Cookie已经成为标准，所有的主流浏览器都支持Cookie（需要浏览器的支持：保存、更新、发送、删除。不同的浏览器保存Cookie的方式不同）。
查看网站Cookie的简单方式：直接在浏览器地址栏中输入`javascript:alert(document.cookie)`
Session的使用比Cookie方便，但是过多的Session存储在服务器内存中，会对服务器造成压力。

Cookie具有`不可跨域名性`（这里指全域名，而不是仅仅指根域名）。同一个一级域名下的两个二级域名如www.demo.com和images.demo.com也不能交互使用Cookie，因为二者的域名并不严格相同。如果想所有demo.com名下的二级域名都可以使用该Cookie，需要设置Cookie的domain参数为“`.demo.com`”（以.开头），这样所有以“demo.com”结尾的域名都可以访问该Cookie。
Cookie中使用Unicode字符时需要对Unicode字符进行编码（`Cookie中保存中文只能编码`，推荐使用UTF-8，因为`JavaScript不支持GBK编码`）。
由于浏览器每次请求服务器都会携带Cookie，因此Cookie内容不宜过多，否则影响速度。Cookie的内容应该少而精。

maxAge为负数的Cookie，为`临时性Cookie`，不会被持久化，不会被写到Cookie文件中。Cookie信息保存在浏览器内存中，因此关闭浏览器该Cookie就消失了。Cookie默认的maxAge值为-1。
要想修改Cookie只能使用一个同名的Cookie来覆盖原来的Cookie，达到修改的目的。删除时只需要把maxAge修改为0即可（Cookie并不提供直接的修改、删除操作）。修改、删除Cookie时，新建的Cookie除value、maxAge之外的所有属性，例如name、path、domain等，都要与原Cookie完全一样。否则，浏览器将视为两个不同的Cookie不予覆盖，导致修改、删除失败。
从客户端读取Cookie时，包括maxAge在内的其他属性都是不可读的，也不会被提交。浏览器提交Cookie时只会提交name与value属性。maxAge属性只被浏览器用来判断Cookie是否过期。
Cookie的`Expires`属性标识了Cookie的有效时间，当Cookie的有效时间过了之后，这些数据就被自动删除了。默认情况下coolie是暂时存在的，他们存储的值只在浏览器会话期间存在，当用户退出浏览器后这些值也会丢失，如果想让cookie存在一段时间，就要为expires属性设置为未来的一个过期日期。`expires属性现在已经被max-age属性所取代`，max-age用秒来设置cookie的生存期。
`path属性`决定允许访问Cookie的路径。页面只能获取它属于的Path的Cookie。例如/session/test/a.jsp不能获取到路径为/session/abc/的Cookie。

如果不希望Cookie在HTTP等非安全协议中传输，可以设置Cookie的`secure属性`为true。浏览器只会在HTTPS和SSL等安全协议中传输此类Cookie。secure属性并不能对Cookie内容加密，因而不能保证绝对的安全性。如果需要高安全性，需要在程序中对Cookie内容加密、解密，以防泄密。
W3C标准的浏览器会阻止JavaScript读写任何不属于自己网站的Cookie。

Session在用户第一次访问服务器的时候`自动创建`。Session生成后，只要用户继续访问，服务器就会更新Session的最后访问时间，并维护该Session。为防止内存溢出，服务器会把长时间内没有活跃的Session从内存删除。这个时间就是Session的超时时间。如果超过了超时时间没访问过服务器，Session就自动失效了。
虽然Session保存在服务器，对客户端是透明的，`它的正常运行仍然需要客户端浏览器的支持`（如果使用Cookie来发送SessionID的话）。这是因为Session需要使用Cookie作为识别标志。如果浏览器不支持Cookie，则需要依赖URL重写。
`URL地址重写`是对客户端不支持Cookie的解决方案。URL地址重写的原理是将该用户Session的id信息重写到URL地址中。服务器能够解析重写后的URL获取Session的id。

## require和include的区别是什么？
require和include几乎完全一样，除了处理失败的方式不同。require 在出错时产生E_COMPILE_ERROR 级别的错误。换句话说将导致脚本中止而include只产生警告（E_WARNING），脚本会继续运行。

在失败时include返回FALSE并且发出警告。成功的包含则返回1，除非在包含文件中另外给出了返回值。

因为 include 是一个特殊的语言结构，其参数不需要括号。在比较其返回值时要注意。 
if ((include 'vars.php') == 'OK') {
    echo 'OK';
}

如果在包含文件中定义有函数，这些函数不管是在return之前还是之后定义的，都可以独立在主文件中使用。

require()和include()只是php中的一种语言特性，而不是函数。用于指定的文件代替语句本身，就象C语言中的include()语句一样。如果php配置文件php.ini中的URL fopen wrappers 是打开的(默认情况下是打开的)，就可以使用URL来指定文件的位置从而实现远程文件的调用。如：
	require("http://some_server/file.php?varfirst=1&varsecond=2"); 
区别：
1.require()语句会无条件地读取它所包含的文件的内容，而不管这些语句是否执行（比如在判断为false的分支语句中依然会执行）。如果你想按照不同的条件包含不同的文件，就必须使用include()语句。
2.require一个文件存在错误的话，那么程序就会中断执行了，并显示致命错误。include一个文件存在错误的话，那么程序不会中端，而是继续执行，并显示一个警告错误。
3.include有返回值，而require没有。
注：
（1）require_once()和include_once()语句分别对应于require()和include()语句。require_once()和include_once()语句主要用于需要包含多个文件时，可以有效地避免把同一段代码包含进去而出现函数或变量重复定义的错误。
（2）有一点就是使用require()和include()语句时要特别的注意。那就是在被包含的文件中，处理器是按照html模式来解释其中的内容的（无论被包含文件的扩展名是什么），处理完被包含的内容后又恢复到php模式。所以如果需要在被包含文件中使用php语法，就要使用正确的php开始和结束标记来把这些语句包含进去。 
（3）require()和include()语句中的变量继承require()和include()语句所在位置的变量作用域。所有在require()和include()语句的位置可以访问的变量，在require()和include()语句所包含的文件中都可以访问。如果require()和include()语句位于一个函数内部，那么被包含文件内的语句都相当于定义在函数内部。


## 为什么PHP数组可以同时含有integer和string类型的键名？
PHP 数组可以同时含有integer和string类型的键名，因为PHP实际并不区分索引数组和关联数组。如果对给出的值没有指定键名，则取当前最大的整数索引值，而新的键名将是该值加一。如果指定的键名已经有了值，则该值会被覆盖。 
例：
$array = array(
         "a",
         "b",
    6 => "c",
         "d",
);
var_dump($array);
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
$a = array( 'color' => 'red',
	'taste' => 'sweet',
	'shape' => 'round',
	'name'  => 'apple',
	4        // key will be 0
);

## 在cookie不可用的情况下如何使用session？
有时候会遇到因cookie无法使用从而导致session变量不能跨页传递。原因可能有：
1）客户端禁用了cookie；
2）浏览器出现问题，暂时无法存取cookie；
3）php.ini中的session.use_trans_sid = 0，或者编译时没有打开--enable-trans-sid选项。
当代码session_start()时，就在服务器上产生了一个session文件，随之也产生了一个唯一对应的sessionID。跨页后，为了使用session，必须又执行session_start()，将又会产生一个session文件以及新的sessionID，这个新的sessionID无法用来获取前一个sessionID设置的值。除非在session_start()之前加代码session_id($session_id);将不会产生session文件，而是直接读取与这个id对应的session文件。

假设cookie功能不可用，可以通过如下方式实现抛开cookie来使用session：
1）设置php.ini中的session.use_trans_sid =1，或者编译时打开--enable-trans-sid选项，让PHP自动跨页传递sessionID（基于URL）；
2）手动通过URL传值、隐藏表单传递sessionID，即约定一个键名，读出后手工赋值给session_id();函数；
3）用文件、数据库等形式保存sessionID，在跨页中手工调用。

注：
清除所有cookie后，加载本地127.0.0.1/test.php，内容如下：
<?php
	session_start(); 
	$_SESSION['var1']="value"; 
	$url="<a href="."'test2.php'>link</a>"; 
	echo $url; 
?>
返回HTTP头部如下：
HTTP/1.1 200 OK
Date: Sat, 09 May 2015 09:30:03 GMT
Server: Apache/2.4.9 (Win64) PHP/5.5.12
X-Powered-By: PHP/5.5.12
Set-Cookie: PHPSESSID=4j6ngd4o2vgeq8bj1otluvvih2; path=/
Expires: Thu, 19 Nov 1981 08:52:00 GMT
Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0
Pragma: no-cache
Content-Length: 28
Keep-Alive: timeout=5, max=100
Connection: Keep-Alive
Content-Type: text/html

仅设置session.use_trans_sid =1可能无效，php.ini中还有另外两个选项：
session.use_cookies = 1
session.use_only_cookies = 1
因此如果想要在cookie可用时则使用cookie，不可用时则使用url重写，则应该进行如下配置：
session.use_trans_sid =1
session.use_cookies = 1
session.use_only_cookies = 0
或者在php中：
ini_set(“session.use_trans_sid”,”1”);
ini_set(“session.use_only_cookies”,”0”);
ini_set(“session.use_cookies”,”1”);

## 什么是生成器？有什么优势？
生成器提供了一种更容易的方法来实现简单的对象迭代，相比较定义类实现 Iterator 接口的方式，性能开销和复杂性大大降低。 

生成器允许你在 foreach 代码块中写代码来迭代一组数据而不需要在内存中创建一个数组, 那会使你的内存达到上限，或者会占据可观的处理时间。相反，你可以写一个生成器函数，就像一个普通的自定义函数一样, 和普通函数只返回一次不同的是, 生成器可以根据需要yield多次，以便生成需要迭代的值。


## 哪些变量不能使用可变变量行为？
在 PHP 的函数和类的方法中，超全局变量不能用作可变变量。$this变量也是一个特殊变量，不能被动态引用。 


### 如何使用可变参数？
在5.6+版本中，可以如下使用可变参数：
function sum(...$numbers) {
    $acc = 0;
    foreach ($numbers as $n) {
        $acc += $n;
    }
    return $acc;
}

echo sum(1, 2, 3, 4);
在PHP 5.5及更早版本中，使用函数func_num_args()，func_get_arg()，和func_get_args()来支持可变参数。

## 什么是魔术引号？
magic_quotes_gpc指令表示来自GET、POST、cookie方式的变量将被自动包括在引号内。使用get_magic_quotes_gpc()函数可以判断该指令是否已打开。
对于PHP magic_quotes_gpc=on的情况，我们可以不对输入和输出数据库的字符串数据作addslashes()和stripslashes()的操作，数据也会正常显示。如果此时你对输入的数据作了addslashes()处理，那么在输出的时候就必须使用stripslashes()去掉多余的反斜杠。
对于PHP magic_quotes_gpc=off 的情况，必须使用addslashes()对输入数据进行处理，但并不需要使用stripslashes()格式化输出，因为addslashes()并未将反斜杠一起写入数据库，只是帮助mysql完成了sql语句的执行。
addslashes()函数将单引号转换为\’的转义字符使sql语句成功执行，但\’并未作为数据存入数据库，数据库保存的是snow”’’sun 而并不是我们想象的snow\’\’\’\’sun。
这个特性在PHP5.3.0中已经废弃并且在5.4.0中已经移除了（This feature has been DEPRECATED as of PHP 5.3.0 and REMOVED as of PHP 5.4.0.）。所以没有理由再使用魔术引号，因为它不再是 PHP 支持的一部分。 不过它帮助了新手在不知不觉中写出了更好（更安全）的代码。 但是在处理代码的时候，最好是更改你的代码而不是依赖于魔术引号的开启。

## 如何将全局的非命名空间中的代码与命名空间中的代码组合在一起？
只能使用大括号形式的语法。全局代码必须用一个不带名称的namespace语句加上大括号括起来：
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

如果一个文件中包含命名空间，它必须在其它所有代码之前声明命名空间。 在声明命名空间之前唯一合法的代码是用于定义源文件编码方式的declare语句。另外，所有非PHP代码包括空白符都不能出现在命名空间的声明之前：
<html>
<?php
namespace MyProject; // 致命错误 -　命名空间必须是程序脚本的第一条语句
?> 

同一个命名空间可以定义在多个文件中。

命名空间的名字可以使用分层次的方式定义（使用反斜杠做分隔符），如：
namespace MyProject\Sub\Level;

## PHP中有哪些来自PHP之外的变量？
来自PHP之外的变量：
1）HTML表单（GET和POST），根据特定的设置和个人的喜好，有很多种方法访问 HTML 表单中的数据。
2）HTTP Cookies

对于通过表单或者Cookies传进来的变量，PHP将会自动将变量名中的点（如果有的话）替换成下划线。
如：<input type="image" src="image.gif" name="sub" />，点击后，将会加上两个变量：sub_x和sub_y。它们包含了用户点击图像的坐标。（这里浏览器发出的是sub.x和sub.y）

## 被包含文件的搜索顺序是什么样的？
1）先按参数给出的路径寻找
2）如果没有给出目录（只有文件名）时则按照 include_path 指定的目录寻找
3）如果在include_path下没找到该文件则include最后才在调用脚本文件所在的目录和当前工作目录下寻找
4）如果最后仍未找到文件则include 结构会发出一条警告；这一点和 require 不同，后者会发出一个致命错误。 
5）如果定义了路径——不管是绝对路径还是当前目录的相对路径，include_path 都会被完全忽略。
6）当一个文件被包含时，其中所包含的代码继承了include所在行的变量范围。从该处开始，调用文件在该行处可用的任何变量在被调用的文件中也都可用。不过所有在包含文件中定义的函数和类都具有全局作用域。 
7）如果 include 出现于调用文件中的一个函数里，则被调用的文件中所包含的所有代码将表现得如同它们是在该函数内部定义的一样。所以它将遵循该函数的变量范围。此规则的一个例外是魔术常量，它们是在发生包含之前就已被解析器处理的。

## 未初始化的变量的默认值是什么？如何判读变量是否被初始化？
未初始化的变量具有其类型的默认值-布尔类型的变量默认值是FALSE，整形和浮点型变量默认值是零，字符串型变量（例如用于echo中）默认值是空字符串以及数组变量的默认值是空数组。isset()语言结构可以用来检测一个变量是否已被初始化

## 打开allow_url_fopen、allow_url_include配置会有什么问题？有什么替代方案？
允许访问URL远程资源（就是允许fopen这样的函数打开url）使得PHP应用程序的漏洞变得更加容易被利用，php脚本若存在远程文件包含漏洞可以让攻击者直接获取网站权限及上传web木马，一般会在php配置文件中关闭该功能，若需要访问远程服务器建议采用其他方式如libcurl库。
allow_url_fopen = Off
allow_url_include = Off
比如有这样的代码：
 	if (isset($HTTP_GET_VARS)) {
		reset($HTTP_GET_VARS);
		while ( list($var, $val) = each($HTTP_GET_VARS) ) {
			$$var=$val;
		}
	}
（一些较偶然的场景会导致将以http://开头的get参数所表示的远程文件直接包含进来，然后执行）

## PHP会在输出时自动删除其结束符 ?> 后的一个换行
例1：
<?php   
	echo "XXX";
?>YYY
输出：
XXXYYY

例2：一个换行，会被直接删除
<?php   
	echo "XXX";
?>
YYY
输出：
XXXYYY

例3：多个换行，效果等同于一个空格
<?php   
	echo "XXX";
?>

YYY
输出：
XXX YYY

## 如何定义一个返回引用的函数？
从函数返回一个引用，必须在函数声明和指派返回值给一个变量时都使用引用运算符 &：
function &returns_reference()
{
    return $someref;
}
$newref =&returns_reference();

## new/delete与malloc/free之间的联系和区别
(1) malloc/free和new/delete的联系
	a）存储方式相同。malloc和new动态申请的内存都位于堆中。申请的内存都不能自动被操作系统收回，都需要配套的free和delete来释放。
	b）除了带有构造函数和析构函数的类等数据类型以外，对于一般数据类型，如int、char等等，两组动态申请的方式可以通用，作用效果一样，只是形式不一样。
	c）内存泄漏对于malloc或者new都可以检查出来的，区别在于new可以指明是那个文件的那一行，而malloc没有这些信息。
	d）两组都需要配对使用，malloc配free，new配delete，注意，这不仅仅是习惯问题，如果不配对使用，容易造成内存泄露。同时，在C++中，两组之间不能混着用，虽说有时能编译过，但容易存在较大的隐患。
(2) malloc/free和new/delete的区别
	a）malloc返回void类型指针，free的形参为void指针，new和delete直接带具体类型的指针。
	b）malloc和free属于C语言中的函数，需要库的支持，而new/delete是C++中的运算符，况且可以重载，所以new/delete的执行效率高些。C++中为了兼用C语法，所以保留malloc和free的使用，但建议尽量使用new和delete。
	c）在C++中， new是类型安全的，而malloc不是。例如：
　		int* p = new char[10];  // 编译时指出错误
  		delete [] p;                                     //对数组需要加中括号“[]”
　		int* p = malloc(sizeof(char)*10);    // 编译时无法指出错误
		free (p);   //只需要所释放内存的头指针（free释放malloc分配的数组）。在malloc和free的面前没有对象没有数组，只有“内存块”。一次malloc分配的东西，一次free一定能回收。至于内存块的大小内存管理会进行记录，这应该是库函数的事。free的真正弊端在于它不会调用析构函数。

	d）使用new动态申请类对象的内存空间时，类对象的构建要调用构造函数，相当于对内存空间进行了初始化。而malloc动态申请的类对象的内存空间时，不会初始化，也就是说申请的内存空间无法使用，因为类的初始化是由构造函数完成的。delete和free的意义分别于new和malloc相反。
	e）不能用malloc和free来完成类对象的动态创建和删除。
另：
void *calloc(int n,int size);
函数返回值为void型指针。如果执行成功，函数从堆上获得size X n的字节空间，并返回该空间的首地址。如果执行失败，函数返回NULL。该函数与malloc函数的一个显著不同时是，calloc函数得到的内存空间是经过初始化的，其内容全为0。calloc函数适合为数组申请空间，可以将size设置为数组元素的空间长度，将n设置为数组的容量。

realloc函数的功能比malloc函数和calloc函数的功能更为丰富，可以实现内存分配和内存释放的功能，其函数声明如下：
void * realloc(void * p,int n);
其中，指针p必须为指向堆内存空间的指针，即由malloc函数、calloc函数或realloc函数分配空间的指针。realloc函数将指针p指向的内存块的大小改变为n字节。如果n小于或等于p之前指向的空间大小，那么。保持原有状态不变。如果n大于原来p之前指向的空间大小，那么，系统将重新为p从堆上分配一块大小为n的内存空间，同时，将原来指向空间的内容依次复制到新的内存空间上，p之前指向的空间被释放。relloc函数分配的空间也是未初始化的。
注意：使用malloc函数，calloc函数和realloc函数分配的内存空间都要使用free函数或指针参数为NULL的realloc函数来释放。

## 什么样的变量会被认为是NULL？如何判断？
特殊的NULL值表示一个变量没有值。NULL类型唯一可能的值就是NULL（不区分大小写）。在下列情况下一个变量被认为是 NULL： 
1）被赋值为 NULL。 
2）尚未被赋值。 
3）被unset()。 
判断是否为NULL：is_null()

递减NULL值也没有效果，但是递增NULL的结果是 1。

## 什么是resource变量？
资源resource是一种特殊变量，保存了到外部资源的一个引用。资源是通过专门的函数来建立和使用的。
由于资源类型变量保存有为打开文件、数据库连接、图形画布区域等的特殊句柄，因此将其它类型的值转换为资源没有意义。

## 在方法中使用的$this一定指向该方法所从属的对象吗？
$this是一个到主叫对象的引用（通常是该方法所从属的对象，但如果是从第二个对象静态调用时也可能是另一个对象）。
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

输出：$this is defined (B)

## 在<?php …?>外面的内容是否一定会被原样输出？
当PHP解释器碰到?>结束标记时就简单地将其后内容原样输出，直到碰到下一个开始标记；例外是处于条件语句中间时，此时 PHP 解释器会根据条件判断来决定哪些输出，哪些跳过：
<?php if ($expression == true): ?>
  This will show if the expression is true.
<?php else: ?>
  Otherwise this will show.
<?php endif; ?> 
上例中PHP将跳过条件语句未达成的段落，即使该段落位于 PHP 开始和结束标记之外。

如果文件内容是纯PHP代码，最好在文件末尾删除PHP结束标记
如果文件内容是纯PHP代码，最好在文件末尾删除PHP结束标记。这可以避免在PHP结束标记之后万一意外加入了空格或者换行符，会导致PHP开始输出这些空白，而脚本中此时并无输出的意图。

例：
<?php   
	echo "XXX";
?> 

这里在结尾处多了一个空格和一个换行
将输出：
<body>XXX 
</body>
即，在XXX后面多了一个空格，并换行

而：
<?php   
	echo "XXX";
将输出：
<body>XXX</body>

一段PHP代码中的结束标记隐含表示了一个分号
例：
<?php   
	echo "XXX"
输出：
Parse error: syntax error, unexpected $end, expecting ',' or ';' in D:\wamp\www\t.php on line 2

<?php   
	echo "XXX"
?>
输出：
XXX

## PHP中没有整除的运算符
1/2 产生出float 0.5。值可以舍弃小数部分强制转换为integer，或者使用round()函数可以更好地进行四舍五入。
<?php
var_dump(25/7);         	// float(3.5714285714286) 
var_dump((int) (25/7)); 		// int(3)
var_dump(round(25/7));  	// float(4) 
?> 

## MySQL有哪些数据类型？
串数据类型
CHAR 1~255个字符的定长串，长度必须在创建时指定，否则MySQL假定为CHAR(1)
ENUM 接收最多64K个串组成的一个预定义集合的某个串
LONGTEXT 与TEXT相同，但最大长度为4GB
MEDIUMTEXT 与TEXT相同，但最大长度为16K
SET 接受最多64个串组成的一个预定义集合的零个或多个串
TEXT 最大长度为64K的变长文本
TINYTEXT 与TEXT相同，但最大长度为255字节
VARCHAR 长度可变，最多不超过255字节。如果在创建时指定为VARCHAR(n)，则可存储0~n个字符的变长串

数值数据类型
BIT 					位字段，1~64位
BIGINT 				整数值，8个字节，-2^63到2^63-1的整型数据
BOOLEAN (BOOL) 	布尔值，为0或1
DECIMAL (DEC) 		精度可变的浮点数
DOUBLE 			双精度浮点数
FLOAT 				单精读浮点数
INT (INTEGER) 		整数值，4个字节，-2^31到2^31–1的整型数据
MEDIUMINT 		整数值，3个字节
REAL 				4字节的浮点值
SMALLINT 			整数值，2个字节
TINYINT 			整数值，1个字节

日期和时间数据类型
DATE 				表示1000-01-01~9999-12-31的日期，格式为YYYY-MM-DD
DATETIME 			DATE和TIME的组合
TIMESTAMP 		功能和DATETIME相同，但范围较小
TIME 				格式为HH:MM:SS
YEAR 				1970~2069年

二进制数据类型
BLOB 				最大长度为64K
MEDIUMBLOB 		最大长度为16M
LONGBLOB 		最大长度为4GB
TINYBLOB 			最大长度为255字节


## 在PHP中，如何控制数组json_encode后为json对象或者json数组？
$foo = array(
  "item1" => (object)[],
  "item2" => []
);

echo json_encode($foo);

输出：
{"item1":{},"item2":[]}


## 如何抛出异常并处理？
在PHP中，异常必须手动抛出。
try{
		throw new Exception("An exception",10); // 构造内置Exception对象，参数为异常信息和编号
	}
	catch(Exception $e){
		echo $e->getCode()."<br/>"; 
		echo $e->getMessage()."<br/>";
		echo $e->getFile()."<br/>";
		echo $e->getLine()."<br/>";
		echo $e;
	}
输出：
10
An exception
D:\wamp\www\test.php
4
exception 'Exception' with message 'An exception' in D:\wamp\www\test.php:4 Stack trace: #0 {main}

## 什么是完全限定名称？
访问任意全局类、函数或常量，都可以使用完全限定名称，例如\strlen()或\Exception或\INI_ALL，以及使用类：$a = new \currentnamespace\foo();

## 超全局变量有哪些？
PHP 中的许多预定义变量都是"超全局的"，这意味着它们在一个脚本的全部作用域中都可用。在函数或方法中无需执行global $variable; 就可以访问它们：
$GLOBALS	引用全局作用域中可用的全部变量。与所有其他超全局变量不同，$GLOBALS在PHP中总是可用的
$_SERVER	服务器和执行环境信息
$_GET		HTTP GET 变量，通过URL参数传递给当前脚本的变量的数组
$_POST		HTTP POST 变量，通过 HTTP POST 方法传递给当前脚本的变量的数组
$_FILES		HTTP 文件上传变量
$_COOKIE	HTTP Cookies
$_SESSION	Session 变量
$_REQUEST	HTTP Request 变量，默认情况下包含了 $_GET，$_POST 和 $_COOKIE 的数组
$_ENV		这些变量被从PHP 解析器的运行环境导入到PHP 的全局命名空间。很多是由支持 PHP 运行的 Shell 提供的，并且不同的系统很可能运行着不同种类的 Shell，所以不可能有一份确定的列表。

## 2147483648这个数是integer类型的吗？
如果给定的一个数超出了integer的范围，将会被解释为float。同样如果执行的运算结果超出了integer范围，也会返回float。
<?php
$large_number = 2147483647;
var_dump($large_number);	// int(2147483647)

$large_number = 2147483648;
var_dump($large_number); 	// float(2147483648)

$million = 1000000;
$large_number =  50000 * $million;
var_dump($large_number);	// float(50000000000)
?>

## 什么是declare结构？
declare结构用来设定一段代码的执行指令。declare 的语法和其它流程控制结构相似： 
declare (directive)
    	statement
directive部分允许设定declare代码段的行为。目前只认识两个指令：ticks和encoding。

declare结构也可用于全局范围，影响到其后的所有代码（但如果有 declare 结构的文件被其它文件包含，则对包含它的父文件不起作用）。
例：
// you can use this:
declare(ticks=1) {
    // entire script here
}

// or you can use this:
declare(ticks=1);
// entire script here

Tick（时钟周期）是一个在declare代码段中解释器每执行N条可计时的低级语句就会发生的事件。N的值是在declare中的directive部分用ticks=N来指定的。 
不是所有语句都可计时。通常条件表达式和参数表达式都不可计时。 
例：
declare(ticks=1);

// A function called on each tick event
function tick_handler()
{
    echo "tick_handler() called<br/>";
}

register_tick_function('tick_handler');
输出：
tick_handler() called
tick_handler() called
tick_handler() called

加一个赋值语句：
declare(ticks=1);

// A function called on each tick event
function tick_handler()
{
    echo "tick_handler() called<br/>";
}

register_tick_function('tick_handler');
$var = 1;
输出：
tick_handler() called
tick_handler() called
tick_handler() called
tick_handler() called

可以用 encoding 指令来对每段脚本指定其编码方式。 
<?php
declare(encoding='ISO-8859-1');
// code here
?> 

## 如何在类内部创建自身及父类对象？
在类定义内部，可以用new self和new parent创建新对象。

## 如何获取PHP原生的POST数据？
$HTTP_RAW_POST_DATA — 原生POST数据
一般而言，使用 php://input 代替 $HTTP_RAW_POST_DATA

Traversable、Iterator、IteratorAggregate、ArrayAccess、Serializable等接口的作用是什么？
Traversable（遍历）接口：检测一个类是否可以使用foreach进行遍历的接口。 
这是一个无法在PHP脚本中实现的内部引擎接口。这个接口没有任何方法，它的作用仅仅是作为所有可遍历类的基本接口。 IteratorAggregate 或 Iterator 接口可以用来代替它。  

Iterator（迭代器）接口：
Iterator  extends Traversable  {
/* 方法 */
abstract public mixed current ( void )
abstract public scalar key ( void )
abstract public void next ( void )
abstract public void rewind ( void )
abstract public boolean valid ( void )
}

IteratorAggregate（聚合式迭代器）接口：
IteratorAggregate  extends Traversable  {

/* 方法 */
abstract public Traversable getIterator ( void )
}
例：让一个类拥有数组的操作
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

ArrayAccess（数组式访问）接口
ArrayAccess  {
/* 方法 */
abstract public boolean offsetExists ( mixed $offset )
abstract public mixed offsetGet ( mixed $offset )
abstract public void offsetSet ( mixed $offset , mixed $value )
abstract public void offsetUnset ( mixed $offset )
}

Serializable 自定义序列化的接口
Serializable  {
/* 方法 */
abstract public string serialize ( void )
abstract public mixed unserialize ( string $serialized )
}
实现此接口的类将不再支持 __sleep() 和 __wakeup()。

## 如何不使用include实现将一个PHP文件“包含”到一个变量中？
用输出控制函数结合 include 来捕获其输出
例：使用输出缓冲来将 PHP 文件包含入一个字符串
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

## 在函数内部如何使用外部定义的变量？
在用户自定义函数中，一个局部函数范围将被引入。任何用于函数内部的变量按缺省情况将被限制在局部函数范围内。
如：
$a = 1; /* global scope */

function Test()
{
    echo $a; /* reference to local scope variable */
}

Test();
这个脚本不会有任何输出，因为echo语句引用了一个局部版本的变量$a，而且在这个范围内，它并没有被赋值。
PHP 中全局变量在函数中使用时必须声明为global。

第二个办法，是用特殊的PHP自定义$GLOBALS数组：
$a = 1;
$b = 2;

function Sum()
{
    $GLOBALS['b'] = $GLOBALS['a'] + $GLOBALS['b'];
}

Sum();
echo $b;
因为 $GLOBALS 是一个超全局变量，而超全局变量是不需要global 声明就可以使用的。


### 可变函数如何使用？有什么限制？
PHP支持可变函数的概念。这意味着如果一个变量名后有圆括号，PHP 将寻找与变量的值同名的函数，并且尝试执行它。
可变函数不能用于例如 echo，print，unset()，isset()，empty()，include，require 以及类似的语言结构。
也可以用可变函数的语法来调用一个对象的方法：
$foo = new Foo();
$funcname = "Variable";
$foo->$funcname();   // This calls $foo->Variable()

## 为什么PHP不做编译优化？
C程序通常一次编译，多次运行或长时间运行，因此在编译上多耗些时间、多做些优化被认为是值得的。而解释型语言往往作为胶水语言，也就是完成一项用后即弃的特定任务。在PHP内核开发邮件列表里，一个月经贴是为什么PHP不做编译优化。官方的答复是，PHP程序运行时间往往很短暂，比如10ms；如果花100ms做编译优化，把运行时间压缩到1ms，总的时间消耗是101ms，反而更慢了（不考虑中间代码缓存）。

## 有哪些对数组key的强制转换的情况？
1）包含有合法整型值的字符串会被转换为整型。例如键名 "8" 实际会被储存为 8。但是 "08" 则不会强制转换，因为其不是一个合法的十进制数值。  
2）浮点数也会被转换为整型，意味着其小数部分会被舍去。例如键名 8.7 实际会被储存为 8。  
3）布尔值也会被转换成整型。即键名 true 实际会被储存为 1 而键名 false 会被储存为 0。  
4）Null 会被转换为空字符串，即键名 null 实际会被储存为 ""。  
5）数组和对象不能被用为键名。坚持这么做会导致警告：Illegal offset type。 
例：
$array = array(
    1    => "a",
    "1"  => "b",  
    1.5  => "c",
    true => "d",
);
var_dump($array);
输出：
array(1) {
  [1]=>
  string(1) "d"
}
上例中所有的键名都被强制转换为1，所以最终值为最后一次的赋值

## PHP中在进行逻辑判断时，哪些值会被认为是False？
当转换为boolean时，除以下值外，其他所有的值都会被认为是TRUE。被认为是FALSE的值包括：
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

## 如何禁止在PHP中使用危险函数？
Web木马程序通常利用php的特殊函数执行系统命令，查询任意目录文件，增加修改删除文件等。
比如：
<?php eval($_POST[cmd]);?> 
（其实就是使用了一些危险函数使得应用存在漏洞，最好的防范方式就是不允许使用这些函数）
打开php.ini，安全加固配置方式如下，禁止使用这些危险函数：
disable_functions = dl,assert,exec,popen,system,passthru,shell_exec,proc_close,proc_open,pcntl_exec

## PHP支持哪些数据类型？
PHP共支持8种原始数据类型，其中包括：
	4种标量类型：boolean、integer、float（double）、string
2种复合类型：array、object
2种特殊类型：resource、NULL
 	double 和 float 是相同的，由于一些历史的原因，这两个名称同时存在。
变量的类型通常不是由程序员设定的，确切地说，是由PHP根据该变量使用的上下文在运行时决定的。 

## 能否对类属性应用可变变量？
类的属性也可以通过可变属性名来访问。可变属性名将在该调用所处的范围内被解析。例如，对于$foo->$bar 表达式，则会在本地范围来解析$bar并且其值将被用于$foo的属性名。对于$bar是数组单元时也是一样。 

## mysql与mysqli有什么区别？
PHP-MySQL（mysql）是PHP操作MySQL数据库最原始的Extension，PHP-MySQLi（mysqli）的i代表 Improvement ，提供了相对进阶的功能，也增加了安全性。

mysql是非持继连接函数而mysqli是永远连接函数。也就是说mysql每次链接都会打开一个连接的进程而mysqli多次运行mysqli将使用同一连接进程,从而减少了服务器的开销。
如果使用new mysqli('localhost', usenamer', 'password', 'databasename')总是报错，Fatal error: Class 'mysqli' not found in ...那么要检查一下mysqli是不是开启的。mysqli类不是默认开启的，win下要改php.ini,去掉php_mysqli.dll前的;,linux下要把mysqli编译进去。
当然，如果mysql也需要永久连接的话，就可以使用mysql_pconnect()这个函数。

## 如何收集PHP的错误日志？
php在没有连接到数据库或者其他情况下会有提示错误，一般错误信息中会包含php脚本当前的路径信息或者查询的SQL语句等信息，这类信息提供给黑客后，是不安全的，所以服务器建议禁止错误提示。
display_errors = Off
在关闭display_errors后为了能够把错误信息记录下来，便于查找服务器运行的原因,同时也要设置错误日志存放的目录，建议跟webserver的日志放在一起。
打开php.ini，安全加固配置方式如下，打开错误日志记录并设置错误日志存放路径：
log_errors = On
error_log = /usr/local/apache2/logs/php_error.log # 该文件必须允许webserver的用户和组具有写的权限


## 如何使用OpCache提高PHP应用的性能？
sudo vim /etc/php.ini
加入：
; 开关打开
opcache.enable=1

; 可用内存, 酌情而定, 单位 megabytes
opcache.memory_consumption=256

; 最大缓存的文件数目, 命中率不到 100% 的话, 可以试着提高这个值
opcache.max_accelerated_files=5000

; Opcache 会在一定时间内去检查文件的修改时间, 这里设置检查的时间周期, 默认为 2, 单位为秒
opcache.revalidate_freq=240

; interned string 的内存大小, 也可调
opcache.interned_strings_buffer=8   

; 是否快速关闭, 打开后在PHP Request Shutdown的时候回收内存的速度会提高
opcache.fast_shutdown=1

; 不保存文件/函数的注释
opcache.save_comments=0

检查：
php -v
    PHP 5.5.3-1ubuntu2.2 (cli) (built: Feb 28 2014 20:06:05) 
    Copyright (c) 1997-2013 The PHP Group
    Zend Engine v2.5.0, Copyright (c) 1998-2013 Zend Technologies
        `with Zend OPcache v7.0.3-dev`, Copyright (c) 1999-2013, by Zend Technologies

需要提醒的是，在生产环境中使用上述配置之前，必须经过严格测试。 因为上述配置存在一个已知问题，它会引发一些框架和应用的异常， 尤其是在存在文档使用了备注注解的时候。

重启服务：
sudo /etc/init.d/php-fpm restart
sudo /etc/init.d/nginx restart

如果在更新代码之后，发现没有执行的还是旧代码，可使用函数 opcache_reset() 来清除缓存。

## 字符串中解析变量有哪两种规则？
当字符串用双引号或 heredoc 结构定义时，其中的变量将会被解析。这里有两种变量解析的规则：
1）简单规则：在一个string中嵌入一个变量，一个array的值，或一个object的属性。
2）复杂规则：不是因为其语法复杂而得名，而是因为它可以借助花括号紧接美元符号（{$）来使用复杂的表达式：
例：
//只有通过花括号语法才能正确解析带引号的键名
echo "This works: {$arr['key']}";

//当在字符串中使用多重数组时，一定要用括号将它括起来
echo "This works: {$arr['foo'][3]}";

注意：$必须紧挨着{
例：
$great = 'fantastic';
// {和$之间多了一个空格，无效，输出: This is { fantastic}
echo "This is { $great}";
// 有效，输出： This is fantastic
echo "This is {$great}";
可以把string当成字符组成的array
string中的字符可以通过一个从0开始的下标，用类似array结构中的方括号包含对应的数字来访问和修改，比如$str[42]。可以把string当成字符组成的array。函数substr()和substr_replace()可用于操作多于一个字符的情况
string也可用花括号访问，比如$str{42}。PHP 的字符串在内部是字节组成的数组。因此用花括号访问或修改字符串对多字节字符集很不安全。
如：用超出字符串长度的下标写入将会拉长该字符串并以空格填充。非整数类型下标会被转换成整数。非法下标类型会产生一个 E_NOTICE 级别错误。用负数下标写入字符串时会产生一个 E_NOTICE 级别错误，用负数下标读取字符串时返回空字符串。写入时只用到了赋值字符串的第一个字符。用空字符串赋值则赋给的值是 NULL 字符。 

PHP中的string的实现方式是一个由字节组成的数组再加上一个整数指明缓冲区长度，并无如何将字节转换成字符的信息，由程序员来决定。因此是二进制安全的。

## PHP的变量作用域规则是什么样的？
6项基本的作用域规则：
1）超级全局变量（内置）可以在脚本的任何地方使用和可见。
2）常量一旦被声明，将全局可见，即可以在函数内外使用。
3）在一个脚本中声明的全局变量在整个脚本中是可见的。
4）函数内部使用的变量声明为全局变量时，其名称要与全局变量一致。
5）在函数内部创建的静态变量，在函数外部不可见，但是可以在函数的多次执行中保持值。
6）在函数内部创建的非静态变量，当函数终止时就不存在了。

## php.ini中用来控制文件上传的指令有哪些？
1）file_uploads：控制是否允许http方式的文件上传，默认为ON
2）upload_tmp_dir：指定被上传的文件在被处理之前的临时存放目录，如果没有配置，将使用系统默认值
3）upload_max_filesize：控制允许上传的文件的大小，如果文件大小大于该值，将创建一个文件大小为0的占位符文件，默认为2M
4）post_max_size：控制可接受的，通过POST方法上传数据的最大值。

## Traits是什么？和类有什么区别？
traits 是一种为类似 PHP 的单继承语言而准备的代码复用机制。可以理解为一组能被不同的类都能调用到的方法集合。（Traits不是类，不能被实例化）

## 如何使用list()来解包嵌套的数组？
$array = [
    [1, 2],
    [3, 4],
];

foreach ($array as list($a, $b)) {
    // $a contains the first element of the nested array,
    // and $b contains the second element.
    echo "A: $a; B: $b\n";
}
输出：
A: 1; B: 2
A: 3; B: 4

list() 中的单元可以少于嵌套数组（但是不能多于），此时多出来的数组单元将被忽略：
$array = [
    [1, 2],
    [3, 4],
];

foreach ($array as list($a)) {
    // Note that there is no $b here.
    echo "$a\n";
}
输出：
1
3

## 有哪些通过PHP在Web服务器上执行命令的方式？
有4种主要的在Web服务器上执行命令的方法：
1）exec()函数
没有直接的输出，将返回命令执行结果的最后一行
2）passthru()函数
直接将输出显示到浏览器，如果输出是二进制，比如图片，这将是非常有用的。
3）system()函数
将命令的输出回显到浏览器，它将每一行的输出向后对齐。
4）反引号
``
更复杂的需求可以使用popen()、proc_open()、proc_close()，这些函数可以启动外部进程，并且在这些进程之间传递数据。

如果要把用户提交的数据包含在命令中，考虑到安全性问题，可以使用escapeshellcmd来阻止用户在系统上执行恶意的命令：
system(escapeshellcmd($command));

## 为什么要关闭register_globals配置？
register_globals是php.ini里的一个配置，这个配置影响到php如何接收传递过来的参数。
register_globals的意思就是注册为全局变量，所以当On的时候，传递过来的值会被直接的注册为（与控件的name属性同名的）全局变量直接使用，而Off的时候，我们需要到特定的数组里去得到它。

## PHP中数组拷贝是值拷贝还是引用拷贝？
通过引用来拷贝数组（数组拷贝默认是值拷贝）
$arr1 = array(2, 3);
$arr2 = $arr1;
$arr2[] = 4; // $arr2 is changed,
             // $arr1 is still array(2, 3)
             
$arr3 = &$arr1;
$arr3[] = 4; // now $arr1 and $arr3 are the same

能否将非对象类型转换为对象？如果可以，转换得到的对象有哪些属性？
如果将一个对象转换成对象，它将不会有任何变化。
如果其它任何类型的值被转换成对象，将会创建一个内置类stdClass的实例。
如果该值为 NULL，则新的实例为空。
数组转换成对象将使键名成为属性名并具有相对应的值。
对于任何其它的值，名为scalar的成员变量将包含该值。 
$obj = (object) 'ciao';
echo $obj->scalar;  // outputs 'ciao'

## PHP配置中的open_basedir配置项的作用是什么？

open_basedir是PHP配置中为了防御跨目录进行文件（目录）读写的配置，所有PHP中有关文件读、写的函数都会经过open_basedir的检查。实际上是一些目录的集合，在定义了open_basedir以后，php可以读写的文件、目录都将被限制在这些目录中。在linux下，不同的目录由“:”分割，如“/var/www/:/tmp/”。
注意用open_basedir指定的限制实际上是前缀,而不是目录名。

Apache+PHP配置方法有三种：
方法一：在php.ini里配置
open_basedir = .:/tmp/
方法二：在Apache配置的VirtualHost里设置(httpd-vhosts.conf)
php_admin_value open_basedir .:/tmp/
方法三：在Apache配置的Direcotry里设置
php_admin_value open_basedir .:/tmp/
关于三个配置方法的解释：
a、方法二的优先级高于方法一，也就是说方法二会覆盖方法一；方法三的优先级高于方法二，也就是说方法三会覆盖方法二；
b、配置目录里加了“/tmp/”是因为php默认的临时文件（如上传的文件、session等）会放在该目录，所以一般需要添加该目录，否则部分功能将无法使用；
c、配置目录里加了“.”是指运行php文件的当前目录，这样做可以避免每个站点一个一个设置；
d、如果站点还使用了站点目录外的文件，需要单独在对应VirtualHost设置该目录；

## 如何进行对象相等比较？
当使用比较运算符（==）比较两个对象变量时，比较的原则是：如果两个对象的属性和属性值都相等，而且两个对象是同一个类的实例，那么这两个对象变量相等。 
而如果使用全等运算符（===），这两个对象变量一定要指向某个类的同一个实例（即同一个对象）。 

## 使用swoole时出现mysql server gone away的原因
mysql本身是一个多线程的程序，每个连接过来，会开一个线程去处理相关的query, mysql会定期回收长时间没有任何query的连接(时间周期受wait_timeout配置影响)，所以在swoole中，由于是一个长驻内存的服务，我们建立了一个mysql的连接，不主动关闭 或者是用pconnect的方式，那么这个mysql连接会一直保存着，然后长时间没有和数据库有交互，就主动被mysql server关闭了，之后继续用这个连接，就报mysql server gone away了。

解决方法：
1.修改mysql的wait_timeout值为一个非常大的值，此方法不太可取，可能会产生大量的sleep连接，导致mysql连接上限了， 建议不使用。

2.每次query之前主动进行连接检测
//如果是用mysqli，可用内置的mysqli_ping
if (!$mysqli->ping()) {  
	mysqli->connect(); //重连
}

//如果是pdo，可以检测mysql server的服务器信息来判断
 try {
	$pdo->getAttribute(\PDO::ATTR_SERVER_INFO);
} catch (\Exception $e) {
	if ($e->getCode() == 'HY000') {
		$pdo = new PDO(xxx);  //重连
	} else {
		throw $e;
	}
}
这个方案有个缺点：额外多一次请求，所以改进方法: 用一个全局变量存放最后一次query的时间，下一次query的时候先和现在时间对比一下，超过waite_timeout再重连. 或者也可以用swoole_tick定时检测。

3.被动检测，每次query用try catch包起来，如有mysql gone away异常，则重新连接，再执行一次当前sql.
try {
	query($sql);
} catch (\Exception $e) {
	if ($e->getCode() == 'HY000') {
		reconnect(); //重连
		query($sql)
	} else {
		throw $e;
	}
}

4.用短连接，务必每次操作完之后，手动close

## 如何获取整型数的字长？如何获取整型数的最大值？
整型数的字长和平台有关，字长可以用常量PHP_INT_SIZE来表示，自 PHP 4.4.0 和 PHP 5.0.5后，最大值可以用常量PHP_INT_MAX来表示。 
如：
echo PHP_INT_SIZE;
echo '<br>';
echo PHP_INT_MAX;
将输出：
4
2147483647  // 21亿

## 能否在声明静态变量时使用表达式进行赋值？
静态变量的声明是在编译时解析的，因此在声明静态变量时不能用表达式进行赋值：
function foo(){
    static $int = 0;       	// correct
    static $int = 1+2;    	// wrong  (as it is an expression)
    static $int = sqrt(121);  	// wrong  (as it is an expression too)

    $int++;
    echo $int;
}

## PHP对象注入漏洞的原因是什么？
PHP支持对象的序列化和反序列化操作（serialize、unserialize）。
如：
class User{

  public $age = 0;
  public $name = '';

  public function PrintData(){
    echo 'User ' . $this->name . ' is ' . $this->age . ' years old. <br />';
  }
}

$usr = unserialize('O:4:"User":2:{s:3:"age";i:20;s:4:"name";s:4:"John";}');
$usr->PrintData();

输出：
User John is 20 years old. 

当一个对象进行序列化和反序列化操作时也会自动调用其他相应的魔幻方法：
当对象进行序列化操作时魔幻方法“__sleep”会被自动调用。（必须返回一个包含序列化的类变量名的数组）
当对象进行反序列化操作时魔幻方法“__wakeup”会被自动调用。
反序列化操作自动调用__wakeup和__destruct，攻击者可以操作类变量来攻击web应用，比如：
$usr = unserialize('O:7:"LogFile":1:{s:8:"filename";s:9:".htaccess";}');
$usr->PrintData();
从而意外地执行了LogFile的__construct和__destruct。

在处理由用户提供数据的地方不要使用“unserialize”，可以使用“json_decode”。


## 什么是协程？
协程，又称微线程，纤程。英文名Coroutine。
子程序，或者称为函数，在所有语言中都是层级调用，比如A调用B，B在执行过程中又调用了C，C执行完毕返回，B执行完毕返回，最后是A执行完毕。
所以子程序调用是通过栈实现的，一个线程就是执行一个子程序。子程序调用总是一个入口，一次返回，调用顺序是明确的。而协程的调用和子程序不同。
协程看上去也是子程序，但执行过程中，`在子程序内部可中断`，然后转而执行别的子程序（是中断后执行，而不是函数调用其他的子程序），在适当的时候再返回来接着执行。
协程的特点在于是一个线程执行（所以不是多线程）。优势就是极高的执行效率。因为子程序切换不是线程切换，而是由程序自身控制，因此，没有线程切换的开销，和多线程比，线程数量越多，协程的性能优势就越明显。另一个优势就是不需要多线程的锁机制，因为只有一个线程，也不存在同时写变量冲突，在协程中控制共享资源不加锁，只需要判断状态就好了，所以执行效率比多线程高很多。


缺点：
无法利用多核资源：协程的本质是个单线程,它不能同时将 单个CPU 的多个核用上,协程需要和进程配合才能运行在多CPU上.当然我们日常所编写的绝大部分应用都没有这个必要，除非是cpu密集型应用。
进行阻塞（Blocking）操作（如IO时）会阻塞掉整个程序：这一点和事件驱动一样，可以使用异步IO操作来解决

在PHP中基于yield实现简单的协程通信（`双向`异步信息传递）：
包含yield关键字的函数比较特殊，返回值是一个Generator对象，此时函数内语句尚未真正执行。Generator对象是Iterator接口实例，可以通过rewind()、current()、next()、valid()系列接口进行操纵。`Generator可以视为一种“可中断”的函数，而yield构成了一系列的“中断点”`。Generator类似于车间生产的流水线，每次需要用产品的时候才从那里取一个，然后这个流水线就停在那里等待下一次取操作。


<?php

function gen() {
	for($i=1;$i<=100;$i++) {
		$cmd = (yield $i);  // yield既是语句，又是表达式，既具备类似return语句的功能，同时也有类似表达式的返回值（通过send得到的值）
		if($cmd=='stop') {
			return;
		}
	}s
}

$gen = gen();
$i=0;
foreach($gen as $item) {
	echo $item."\n";
	if($i>=10) {
		$gen->send('stop');
	}
	$i++;
}

## 什么是参数类型约束？
PHP 5 可以使用类型约束。函数的参数可以指定必须为对象（在函数原型里面指定类的名字），接口，数组（PHP 5.1 起）或者 callable（PHP 5.4 起）。不过如果使用 NULL 作为参数的默认值，那么在调用函数的时候依然可以使用 NULL 作为实参。

如果一个类或接口指定了类型约束，则其所有的子类或实现也都如此。

类型约束不能用于标量类型，如int或string。traits 也不允许。

## CGI、FastCGI的理解和区别
CGI：CGI叫“公共网关接口”(Common Gateway Interface), CGI描述了客户端和服务器程序之间传输数据的一种标准,就是规定要传哪些数据、以什么样的格式传递给后方处理这个请求的协议。总之，CGI是一种标准，一种协议。

CGI程序：CGI程序确保CGI协议的顺利执行，并且返回结果，用来沟通程序(如PHP, Python, Java)和Web服务器(Apache2, Nginx), 充当桥梁的作用。即CGI程序是介于Web服务器与Web程序之间的，用来保证CGI协议的程序。如php-fpm。

web server（比如说nginx）只是内容的分发者。比如，如果客户端(比如浏览器)请求/index.html，那么web server会去文件系统中找到这个文件，发送给浏览器，这里分发的是静态数据。如果现在请求的是/index.php，根据配置文件，nginx知道这个不是静态文件，那它就要找到相应的CGI程序来处理了，根据配置，找到了php-cgi这个程序（也就是PHP解析器）,那么他会把这个请求交个php-cgi（PHP解析器）。nginx会启动CGI程序,然后会根据CGI协议，把需要的数据传给全部丢给php-cgi，比如请求的url，查询字符串，POST数据，HTTP header等等。php-cgi(PHP解析器)会解析php.ini文件，初始化执行环境，然后找到index.php，编译，执行,把执行结果返回给客户端。
CGI执行过程的特点是：每次请求过来，再启动CGI程序，去处理请求。所以就会造成，如果50个请求过来，就得启动50个CGI程序的进程来处理，处理完之后，就销毁相应的线程。
但是启动一个进程，肯定是需要时间的，这样大量的启动、销毁，造成了大量的浪费。

FastCGI会启动FastCGI进程管理器（后面简称master），解析配置文件，初始化执行环境，master再启动多个CGI程序(后面简称worker)在那里等候。master的职责是：请求过来，把请求传递给到空闲的worker,然后立即可以接受下一个请求，再传递。worker的职责是：每个worker都一直在等候，接到从master传递过来的请求之后，立即执行并返回，但是执行完毕后，不销毁，而且继续等待下个请求。当然，master还时时刻刻监控着worker的情况，如果worker不够了，master会启动多几个，如果空闲的太多了，停掉一些。这样一种方法，比起老的CGI执行，省去了大量的启动、销毁线程的时间和节约了大量的资源。

修改php.ini后，FastCgi可以平滑的重启,执行php-fpm reload。php-cgi进程是没办法平滑重启的，需要restart。

## CGI与FastCGI有什么区别？
CGI 的工作方式，从 Web 服务器的角度看，是在特定的位置（位置指url，比如：http://www.example.com/wiki.cgi）定义了可以运行的CGI 程序（比如一个脚本，但也可能不是脚本，比如用C实现CGI）。当收到一个匹配URL的请求，相应的程序就会被调用（如果是脚本，则应该调用脚本解释器进行解释），并将客户端发送的数据作为输入。程序的输出会由 Web 服务器收集，并加上合适的档头，再发送回客户端。
一般每次的 CGI 请求都需要新生成一个程序的副本来运行，这样大的工作量会很快将服务器压垮，因此一些更有效的技术可以让脚本解释器直接作为模块集成在 Web 服务器（例如：Apache，mod_php）中，这样就能避免重复载入和初始化解释器。不过这只是就那些需要解释器的高级语言（即解释语言）而言的，使用诸如C一类的编译语言则可以避免这种额外负荷。由于 C 及其他编译语言的程序与解释语言程序相比，前者的运行速度更快、对操作系统的负荷更小，使用编译语言程序是可能达到更高执行效率的，然而因为开发效率等原因，在目前解释性语言还是最合适的。
与为每个请求创建一个新的进程不同，FastCGI使用持续的进程来处理一连串的请求。这些进程由FastCGI服务器管理，而不是web服务器。 当进来一个请求时，web服务器把环境变量和这个页面请求通过一个socket比如FastCGI进程与web服务器(都位于本地）或者一个TCP connection（FastCGI进程在远端的server farm）传递给FastCGI进程。Apache通过mod_fcgid以及较早的第三方mod_fastcgi模块来实现。 

## 用于方法重载的魔术方法有哪些？
在对象中调用一个不可访问方法时，__call() 会被调用。 
用静态方式中调用一个不可访问方法时，__callStatic() 会被调用。

自动加载不可用于 PHP 的 CLI 交互模式。

## 变量的作用域是否可以扩展到后续引入的文件中？
变量的作用域可以包含include或者require引入的文件。
如：
$a = 1;
include 'b.inc';
这里变量$a将会在包含文件b.inc中生效。

## PHP中所有的传值赋值行为都是值传递吗？
在PHP中普通的传值赋值行为有个例外就是碰到对象object时，在PHP 5中是以引用赋值的，除非明确使用了clone关键字来拷贝。

流程控制的替代语法
PHP 提供了一些流程控制的替代语法，包括 if，while，for，foreach 和 switch。替代语法的基本形式是把左花括号（{）换成冒号（:），把右花括号（}）分别换成 endif;，endwhile;，endfor;，endforeach; 以及 endswitch;。
不支持在同一个控制块内混合使用两种语法。 

## $php_errormsg是什么？
前一个错误信息。这个变量只在错误发生的作用域内可用，并且要求 track_errors 配置项是开启的（默认是关闭的）。

## 什么是callable类型？能代表empty()或eval()吗？
自PHP 5.4起可用callable类型指定回调类型callback。一个PHP的函数以string类型传递其名称。可以使用任何内置或用户自定义函数，但除了语言结构，例如：array()，echo，empty()，eval()，exit()，isset()，list()，print 或 unset()。

除了普通的用户自定义函数外，create_function()可以用来创建一个匿名回调函数。
call_user_func有哪些典型的用法？
（32）各种类型回调函数示例：
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

## mysqli的Prepared语句有什么好处？
mysqli支持prepared语句，好处有2：
1）对于在执行大量具有不同数据的相同查询时，可以提高执行速度；
2）可以免受SQL注入攻击；
通常的数据库插入操作如下：
$query = “insert into books values(‘”.$isbn.”’,’”.$author.”’,’”.$title.”’,’”.$price.”’)”;
$result = $db->query($query);
if($result){
echo $db->affected_rows;
...
Prepared语句的基本思想是向MySql发送一个需要执行的查询模板，然后再单独发送数据。因此可以向相同的Prepared语句发送大量相同的数据，对于批处理的插入操作来说是非常有用的。
$query = “insert into books values(?,?,?,?)”;
$stmt = $db->prepare($query);
$stmt->bind_param(“sssd”,$isbn,$author,$title,$price); //sssd为格式化字符串
$stmt->execute();
echo $smtt->affected_rows;
$stmt->close();

对于查询操作，也可以绑定查询结果至变量：
$stmt->bind_result($isbn,$author,$title,$price);

## echo (int) ( (0.1+0.7) * 10 );显示什么内容？
浮点数的精度有限。尽管取决于系统，PHP 通常使用IEEE 754双精度格式，则由于取整而导致的最大相对误差为1.11e-16。非基本数学运算可能会给出更大误差，并且要考虑到进行复合运算时的误差传递。以十进制能够精确表示的有理数如0.1或0.7，无论有多少尾数都不能被内部所使用的二进制精确表示，因此不能在不丢失一点点精度的情况下转换为二进制的格式。
如：
echo (int) ( (0.1+0.7) * 10 ); // 显示 7!
因为该结果内部的表示其实是类似 7.9999999999999991118...
永远不要比较两个浮点数是否相等
要测试浮点数是否相等，要使用一个仅比该数值大一丁点的最小误差值。该值也被称为机器极小值（epsilon）或最小单元取整数，是计算中所能接受的最小的差别值。 
如：$a和$b在小数点后五位精度内都是相等的：
$a = 1.23456789;
$b = 1.23456780;
$epsilon = 0.00001;

if(abs($a-$b) < $epsilon) {
    echo "true";
}

## 用于属性重载的魔术方法有哪些？
在给不可访问属性赋值时，__set() 会被调用。 
读取不可访问属性的值时，__get() 会被调用。 
当对不可访问属性调用 isset() 或 empty() 时，__isset() 会被调用。 
当对不可访问属性调用 unset() 时，__unset() 会被调用。 
属性重载只能在对象中进行。在静态方法中，这些魔术方法将不会被调用。


## SplQueue实现队列
异步并发的服务器里经常使用队列实现生产者消费者模型，解决并发排队问题。PHP的SPL标准库中提供了SplQueue扩展内置的队列数据结构。另外PHP的数组也提供了array_pop和array_shift可以使用数组模拟队列数据结构。
虽然使用Array可以实现队列，但实际上性能会非常差。在一个大并发的服务器程序上，建议使用SplQueue作为队列数据结构。

100万条数据随机入队、出队，使用SplQueue仅用2312.345ms即可完成，而使用Array模拟的队列的程序根本无法完成测试，CPU一直持续在100%，降低到1万条后，也需要260ms才能完成测试。

SplQueue
```
$splq = new SplQueue;
for($i = 0; $i < 1000000; $i++)
{
    $data = "hello $i\n";
    $splq->push($data);

    if ($i % 100 == 99 and count($splq) > 100)
    {
        $popN = rand(10, 99);
        for ($j = 0; $j < $popN; $j++)
        {
            $splq->shift();
        }
    }
}

$popN = count($splq);
for ($j = 0; $j < $popN; $j++)
{
    $splq->pop();
}
```

Array队列
```
$arrq = array();
for($i = 0; $i <1000000; $i++)
{
    $data = "hello $i\n";
    $arrq[] = $data;
    if ($i % 100 == 99 and count($arrq) > 100)
    {
        $popN = rand(10, 99);
        for ($j = 0; $j < $popN; $j++)
        {
            array_shift($arrq);
        }
    }
}
$popN = count($arrq);
for ($j = 0; $j < $popN; $j++)
{
    array_shift($arrq);
}
```



























