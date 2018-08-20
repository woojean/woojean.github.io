---
layout: post
title:  "《PHP和MySQL Web开发》读书笔记"
date: 2017-04-05 00:03:00
categories: 编程
tags: PHP
excerpt: ""
---

* content
{:toc}

## 基本语法
### HTML标签的id属性和name属性有什么区别？

### 4种不同风格的php标记
XML风格：<? php echo ”hello world !”; ?>
简短风格：<? php echo ”hello world !”; ?>
SCRIPT风格：<script language=’php’>echo ”hello world !”;</script>
ASP风格：<% echo ”hello world !”; %>

### 2种不同风格的注释：
C/C++风格：//或/* */
Shell脚本风格： #

### 3种风格访问表单数据：
$key 需要打开register_globals开关，这种用法存在安全性问题，可能会被弃用
_POST[“key”]
HTTP_POST_VARS[“key”] 已被弃用

### 3种指定字符串的方法：
单引号
双引号。在双引号中，变量名称将被变量值所替代，而在单引号中，变量名称或者任何其他文本都会不经修改的发送给浏览器。
heredoc语法：
```php
echo <<<theFlag
line 1
line 2
theFlag
```
heredoc字符串和双引号字符串一样具有计算变量值的特性。

### 6种基本数据类型：
Integer：整数
Float：双精度值浮点数，也叫做Double
String：字符串
Boolean：true或false
Array：数组
Object：对象

### 2种特殊的类型：
NULL：空，未被赋值或者被赋值为NULL的变量。
resource：资源，例如数据库连接。

### 6项基本的作用域规则：
超级全局变量（内置）可以在脚本的任何地方使用和可见。
常量一旦被声明，将全局可见，即可以在函数内外使用。
在一个脚本中声明的全局变量在整个脚本中是可见的。
函数内部使用的变量声明为全局变量时，其名称要与全局变量一致。
在函数内部创建的静态变量，在函数外部不可见，但是可以在函数的多次执行中保持值。
在函数内部创建的非静态变量，当函数终止时就不存在了。

### 9个超级全局变量：
$GLOBALS：所有全局变量数组，作用和global关键字一样
$_SERVER：服务器环境变量数组
$_GET：通过get方法传递给该脚本的变量数组
$_POST：通过post方法传递给该脚本的变量数组
$_COOKIE：cookie变量数组
$_FILE：与文件上传相关的变量数组
$_ENV：环境变量数组
$_REQUEST：所有用户输入的变量数组，包括$_GET、$_POST、$_COOKIE所包含的输入内容
$_SESSION：会话变量数组


### 可变变量：
用于动态地改变一个变量的名称，如：
$x = “value”;
$varname = ‘x’; // 将一个变量的名称赋值给一个可变变量
$$varname=5; 等价于 $x =5;

### 常量：
define(“VALUE”,100); 常量一旦设定，在脚本的其他地方就不能更改，此外，不同于变量：当引用一个常量时，不需要在它前面用$进行引用。

### 引用操作符：
```php
$a = 5;
$b = &$a;
$a = 7;
echo $a.' '.$b."<br/>";
$b = 8;
echo $a.' '.$b."<br/>";
unset($a);
echo $a.' '.$b."<br/>";
```
输出：
7 7
8 8
Notice: Undefined variable: a in D:\wamp\www\test.php on line 10
8



### 恒等操作符：
只有当恒等操作符两边的操作数相等并且具有相同的数据类型时，其返回值才为true。例如0==’0’为true，但是0===’0’不为true。

### 错误抑制操作符：
@可以在任何表达式前面使用，抑制可能发生的警告。如果启用了PHP配置文件中的track_errors特性，错误信息将会被保存在全局变量$php_errormsg中。

### 执行操作符：
` `将会把反向单引号之间的命令当做服务器端的命令来执行，表达式的值就是命令执行的结果，如：
$out = `ls -la`;
echo $out;

### 类型操作符：
instanceof，如：
```php
class sampleClass{};
$myObject = new sampleClass();
if( $myObject instanceof sampleClass ){
...
}
```

### 测试变量类型：
is_array()
is_double()、is_float()、is_real()
is_long()、is_int()、is_integer()
is_string()
is_bool()
is_object()
is_resource()
is_null()
is_scalar() 是否是标量，即一个整数、布尔值、字符串、浮点数
is_numeric() 是否是任何类型的数字或数字字符串
is_callable() 检查该变量是否是有效的函数名称

### 测试变量状态：
isset() 如果变量存在，则返回true，否则返回false。对应的unset函数用来销毁一个变量。
empty() 变量存在，且值不为NULL、0、””，则返回true。


PHP引擎和HTML一样会忽略空格字符，包括回车、空格、Tab字符等。
PHP不要求在使用变量之前声明变量，当第一次给一个变量赋值时才创建这个变量。
PHP变量的类型可以动态改变，即可以在任何时间根据保存在变量中的值来确定变量的类型。
同为逻辑操作符，and、or比&&、||的优先级低。
可以调用exit;来终止PHP脚本的执行，从而不会执行剩下的脚本。

## 文件读写操作
3种方法得到Web服务器文档根目录：
$_SERVER[‘DOCUMENT_ROOT’]
$DOCUMENT_ROOT
$HTTP_SERVER_VARS[‘DOCUMENT_ROOT’]

### fopen()
以追加模式、二进制模式来打开一个位于Web服务器文档根目录之外的文件：
$fp = fopen(“$DOCUMENT_ROOT/../orders/order.txt”,’ab’);
在include_path(PHP配置)中搜索并打开指定文件名的文件：
$fp = fopen(“orders.txt”,’ab’,true);
fopen()函数允许文件名称以协议名称开始，并且在一个远程的位置打开文件。如:http://.

可以通过关闭php配置文件中的allow_url_fopen指令来禁用这个功能。

### fputs()和fwrite()
fputs()是fwrite()函数的别名。fwrite($fp,$outputstring);

### fclose()
用来关闭文件指针。fclose($fp);

### feof()
判断文件已经读取结束。while(!feof($fp))

### fgets()、fgetss()、fgetcsv()
每次读取一行数据。
$order = fgets($fp,999);从文件中每次读取一行内容，直到读到一个换行符（\n）、或者文件读取结束、或者已经读取了998B（可读取的最大长度为指定的最大长度减去1B）
fgetss()可以过滤字符串中包含的PHP和HTML标记。假设test.html文件内容为：
<p><b>This is a paragraph.</b></p>
则：
```php
$file = fopen("test.htm","r");
echo fgetss($file);
```
输出：This is a paragraph.
```php
$file = fopen("test.htm","r");
echo fgetss($file,1024,"<p>,<b>");
```
输出：<p><b>This is a paragraph.</b></p>
fgetcsv()用来读取包含定界符的文件，并返回一个数组。

### readfile()、fpassthru()、file()
用于读取整个文件。
```php
readfile(“$DOCUMENT_ROOT/../orders/order.txt”); //不需要文件指针
$fp = fopen(“$DOCUMENT_ROOT/../orders/order.txt”,’rb’);
fpassthru($fp);fpassthru() 函数输出文件指针处的所有剩余数据。

$file = fopen("test.txt","r");
fgets($file); // 读取第一行
echo fpassthru($file); // 把文件的其余部分发送到输出缓存
fclose($file);
```

### file()
可以将文件读取到数组中，文件的每一行作为数组的一个元素。
$filearray = file(“$DOCUMENT_ROOT/../orders/order.txt”);

### fgetc()
读取一个字符。
$char = fgetc($fp);

### fread()
读取任意字节长度。

### file_exists()
判断文件是否存在。

### filesize()
查看文件大小。
```php
echo filesize(“$DOCUMENT_ROOT/../orders/order.txt”);
```

### nl2br()
将字符串中的\n转换成<br/>

### unlink()
删除一个文件。

### rewind()、fseek()、ftell()
在文件中进行定位。
rewind()回到初始位置。
ftell()报告当前位置。
fseek()定位到指定位置。

### flock()
文件锁定。
```php
$fp = fopen(“$DOCUMENT_ROOT/../orders/order.txt”,’ab’);
flock($fp,LOCK_EX);
fwrite($fp,$outputstring);
flock($fp,LOCK_UN);
fclose($fp);
```

## 数组操作
PHP中数组的大小能够根据所增加的元素而动态地变化：
```php
$products = array( ‘a’,’b’,’c’);  // 创建了一个有3个元素的数组
$products[3] = ‘e’; // 增加了一个新的元素到数组的末尾，这时数组长度变为4
```
遍历访问数组：
```php
for($i=0; $i<3; $i++){
  echo $products[$i];
}
```
或者使用foreach：
```php
foreach($products as $current){
  echo $current;
}
```

### 关联数组：
$prices = array(‘a’=>100,’b’=>200,’c’=>300);
关联数组的索引不是数字，遍历时需要使用foreach：
```php
foreach($prices as $key=>$value){
  echo $key.’-’$value;
}
```
或者使用each()函数：
```php
while($element = each($prices)){
  echo $element[‘key’].’-’.$element[‘value’];
}
```
each函数将返回一个带有4个索引的数组（即$element是一个数组），索引key和0包含了当前元素的关键字，索引value和1包含了当前元素的值。更好的用法是结合list来使用：
```php
while( list($product,$price) = each($prices) ){
  echo “$product - $price”;
}
```
其本质就是将上述$element数组的0、1两个元素的值赋给了$product和$price这两个新变量。

`注意`：当使用each()函数时，数组将记录当前元素。要再次使用each()进行循环操作，则需要使用函数reset()将当前元素重新设置到数组的开始处：
```php
while( list($product,$price) = each($prices) ){
  echo “$product - $price”;
}
reset($prices);
while( list($product,$price) = each($prices) ){
  echo “$product - $price”;
}
```

### 数组操作符：
+ 合并（去重）
== 如果两个数组包含相同的元素，则返回true
=== 如果两个数组包含相同顺序和类型的元素，则返回true
!= 如果两个数组不包含相同的元素则返回true
<> 与!=一样
!== 如果两个数组不包含相同顺序和类型的元素，则返回true

### 数组排序：
将数组按字母升序（或按数字升序）进行排序：
sort($products);
第2个可选参数用来指定排序类型，默认为SORT_REGULAR，可取值还有SORT_NUMERIC、SORT_STRING。
将关联数组按值的升序进行排序：
asort($prices);
将关联数组按键的升序进行排序：
ksort($prices);

sort()、asort()、ksort()分别有一个对应的反向排序的函数：rsort()、arsort()、krsort()。

随机排序：shuffle($prices);
反向排序：array_reverse($prices);

### 数组指针操作：
每个数组都有一个内部指针指向数组中的当前元素，如果创建一个新数组，那么当前指针就将被初始化，并指向数组的第一个元素。
current($arr); 返回指针当前所指向的元素
each($arr); 在指针前移一个位置之前返回当前元素
next($arr); 将指针迁移，再返回新的当前元素
reset($arr); 将指针移到数组第一个元素的位置
end($arr); 将指针移到数组的最后一个元素的位置
prev($arr); 将当前指针往前移一个位置，再返回新的当前元素
pos($arr); 为current函数的别名

例：结合end()和prev()来反向显示一个数组的内容：
```php
$value = end( $arr );
while($value){
  echo $value;
  $value = prev($arr);
}
```

array_walk()，对数组的每个元素应用任何函数，如下对数组中的每个元素应用自定义的my_print函数：
```php
function my_print($value){
echo “$value”;
}
array_walk($arr,’my_print’);
```

count()和sizeof()可用来统计数组中元素的个数。

array_count_values()，统计每个特定的值在数组中出现过的次数：
$arr = array(4,5,1,2,3,1,2,1);
$ac = array_count_values($arr);
$ac数组的键值信息如下：
键	值
4	1
5	1
1	3
2	2
3	1

extract()，将关联数组转换成一系列的标量变量。
```php
$arr =array (‘key1’=>’value1’,‘key2’=>’value2’,‘key3’=>’value3’);
ectract($arr);
echo “$key1 $key2 $key3”; //即这里创建了3个新的变量
```
可以指定当已存在同名变量时如何处理冲突，以及为所有的生成变量指定一个前缀（自动添加下划线）：
```php
$arr = array (‘key1’=>’value1’,‘key2’=>’value2’,‘key3’=>’value3’);
extract($arr,EXTR_PREFIX_ALL,”my_prefix”);
echo “$my_prefix_key1 $my_prefix_key2 $my_prefix_key3”; //即这里创建了3个前缀为my_prefix的新的变量
```

## 字符串操作
### trim()函数用于去除字符串两端的空字符（空格、回车、tab等），也可以提供第2个参数来指定要过滤的其他字符。对应的左、右去除函数为：ltrim()、rtrim()
chop()为rtrim()的别名函数。

### 格式化字符串：
nl2br函数将字符串中的\n替换为<br/>
printf()将一个格式化的字符串输出到浏览器中
spritf()返回一个格式化了的字符串
strtoupper() 将字符串转换为大写
strtolower() 将字符串转换为小写
ucfirst() 将字符串的第一个字符转换为大写
ucwords() 将字符串中所有单词的首字母转换为大写

### 魔术引号
magic_quotes_gpc指令表示来自GET、POST、cookie方式的变量将被自动包括在引号内。使用get_magic_quotes_gpc()函数可以判断该指令是否已打开。
对于PHP magic_quotes_gpc=on的情况，我们可以不对输入和输出数据库的字符串数据作addslashes()和stripslashes()的操作，数据也会正常显示。如果此时你对输入的数据作了addslashes()处理，那么在输出的时候就必须使用stripslashes()去掉多余的反斜杠。
对于PHP magic_quotes_gpc=off 的情况，必须使用addslashes()对输入数据进行处理，但并不需要使用stripslashes()格式化输出，因为addslashes()并未将反斜杠一起写入数据库，只是帮助mysql完成了sql语句的执行。
addslashes()函数将单引号转换为\’的转义字符使sql语句成功执行，但\’并未作为数据存入数据库，数据库保存的是snow”’’sun 而并不是我们想象的snow\’\’\’\’sun。
这个特性在PHP5.3.0中已经废弃并且在5.4.0中已经移除了（This feature has been DEPRECATED as of PHP 5.3.0 and REMOVED as of PHP 5.4.0.）。所以没有理由再使用魔术引号，因为它不再是 PHP 支持的一部分。 不过它帮助了新手在不知不觉中写出了更好（更安全）的代码。 但是在处理代码的时候，最好是更改你的代码而不是依赖于魔术引号的开启。

### 字符串分割与连接
$email_array = explode(‘@’,$email);
$new_email = implode(‘@’,$email_array); 
或者：
$new_email = join(‘@’,$email_array); // join的效果和implode一样

strtok() 函数把字符串分割为更小的字符串：
```php
$string = "Hello world. Beautiful day today.";
$token = strtok($string, " ");
while ($token !== false)
  {
  echo "$token<br />";
  $token = strtok(" ");
  }
```
输出：
Hello
world.
Beautiful
day
today.
注意：仅在第一次调用 strtok() 函数时使用了 string 参数。在首次调用后，该函数仅需要 split 参数，这是因为它清楚自己在当前函数中所在的位置。

substr()，返回指定开始位置、指定长度的子字符串。字符串的起点从0开始。如果只用一个负数作为子字符串的起点来调用它，将得到一个源字符串尾部的一个子字符串，子字符串的长度等于给定负数的绝对值。
考虑substr的start、length都为负数的情况：
$s = "123456789";
$ss = substr($s,-5,-3);
echo $ss;
输出为：56


### 字符串比较
strcmp() 字符串比较，区分大小写
strcasecmp() 不区分大小写
strnatcmp()\strnatcasecmp() 将按“自然排序”来比较字符串。自然排序即人们习惯的顺序进行排序，如strcmp()会认为2大于12，而strnatcmp()则相反。

strlen() 字符串长度

### 子字符串
strstr() 函数搜索一个字符串在另一个字符串中的第一次出现。该函数返回字符串的其余部分（从匹配点）。如果未找到所搜索的字符串，则返回 false。
echo strstr("Hello world!","world");
将输出：world!
strchr()和strstr()完全一样。
stristr()不区分字符大小写
strrchr()会从最后出现目标关键字的位置的前面返回被搜索的字符串（从后面开始的最后一次出现的位置）
例：
```php
echo strrchr("Hello world!orld","orld");
echo "<br/>";
echo strstr("Hello world!orld","orld");
```
输出：
orld
orld!orld

strpos()、strrpos()用来返回子字符串在被搜索字符串中位置。
注意：当匹配位置为从0开始的时候，将返回0。而在php中0与false是相等的。因此，这里需要使用===来进行判断：
```php
$test = “Hello World!”;
$result = strpos($test,”H”);
if($result == false){
echo “Not found”;
}
else{
echo “Found”;
}
```

### 字符串替换
str_replace：字符串替换
str_ireplace()：忽略大小写
substr_replace：在给定位置中查找和替换字符串中特定的子字符串，如将$test字符串中的最后一个字符替换为’X’：
$test = substr_replace($test,’X’,-1);

### 正则表达式
PHP支持2种风格的正则表达式语法：POSIX和Perl。POSIX正则表达式更容易掌握，但是它们不是二进制安全的。以下都是POSIX风格的内容。
二进制安全：PHP基于C实现，二进制安全指可以处理字符串中包含特殊字符如’\0’的情况。
.代表单个字符
.at 匹配：cat、sat、mat、#at
.号可以匹配除换行符\n之外的所有单个字符，但是当用在[]中时，则失去通配符的意义，仅代表一个.字符
[]匹配1个字符的集合
[a-z]at 将不会匹配“#cat”
[aeiou]at将限定首字母为元音字母
[a-zA-Z]at 将限定首字母为大小写的字母
[^a-z]at 将限定不以字母开头
*代表重复0次或多次，+代表重复1次或多次
子表达式：用圆括号，如(very )*large，将可以匹配：
large
very large
very very large

子表达式计数：使用{}，{}中的数字表示指定内容允许重复的次数，可以指定一个具体的数字或者一个范围，如{3}、{2,4}、{2，}
(very ){1,3}，将可以匹配：
very
very very
very very very

指定字符串的头尾：
^bob 指定必须以bob开头
com$ 指定必须以com结尾

分支：|，如com|edu|net，表示要匹配com、edu、net的任意一个

匹配特殊字符：要匹配特殊字符需要在前面加一个\
注意：要是一个$能够在模式中匹配，需要使用“\\\$”，因为这个字符串被引用在双引号中，PHP解释器将其解析为\$，而正则表达式解释器将其解析为一个$字符。

ereg()函数用于使用正则表达式进行字符串查找：
if(!ereg( ‘[a-z]’,$str )){
...
}
eregi 不区分大小写
ereg_replace() 用正则表达式替换子字符串
eregi_replace() 不区分大小写
split() 用正则表达式分割字符串：
```php
$address = “username@example.com”;
$arr = split(“\.|@”,$address);
while( list($key,$value) = each($arr) ){
echo “<br/>”.$value;
}
```
输出：
username
@
example
.
com


## 代码重用
### require()、include()
require()和include()只是php中的一种语言特性，而不是函数。用于指定的文件代替语句本身，就象C语言中的include()语句一样。如果php配置文件php.ini中的URL fopen wrappers 是打开的(默认情况下是打开的)，就可以使用URL来指定文件的位置从而实现远程文件的调用。如：
	require("http://some_server/file.php?varfirst=1&varsecond=2"); 
区别：
require()语句会无条件地读取它所包含的文件的内容，而不管这些语句是否执行（比如在判断为false的分支语句中依然会执行）。如果你想按照不同的条件包含不同的文件，就必须使用include()语句。
require一个文件存在错误的话，那么程序就会中断执行了，并显示致命错误。include一个文件存在错误的话，那么程序不会中端，而是继续执行，并显示一个警告错误。
include有返回值，而require没有。
`注`：
（1）require_once()和include_once()语句分别对应于require()和include()语句。require_once()和include_once()语句主要用于需要包含多个文件时，可以有效地避免把同一段代码包含进去而出现函数或变量重复定义的错误。
（2）有一点就是使用require()和include()语句时要特别的注意。那就是在被包含的文件中，处理器是按照html模式来解释其中的内容的（无论被包含文件的扩展名是什么），处理完被包含的内容后又恢复到php模式。所以如果需要在被包含文件中使用php语法，就要使用正确的php开始和结束标记来把这些语句包含进去。 
（3）require()和include()语句中的变量继承require()和include()语句所在位置的变量作用域。所有在require()和include()语句的位置可以访问的变量，在require()和include()语句所包含的文件中都可以访问。如果require()和include()语句位于一个函数内部，那么被包含文件内的语句都相当于定义在函数内部。

### auto_prepend_file、auto_append_file
另一种将页眉和页脚添加到每个页面中的方法是使用php的两个配置auto_prepend_file、auto_append_file
auto_prepend_file = “/home/username/include/header.php”
auto_append_file = “/home/username/include/footer.php”
如果使用的是apache服务器，可以对单个目录进行不同配置选项的修改，即在目录中添加一个.htacess文件，文件内容如下：
php_value auto_prepend_file “/home/username/include/header.php”
php_value auto_append_file “/home/username/include/footer.php”
使用.htacess文件的前提是服务器运行重设其主配置文件，相对于在php.ini或者Web服务器配置文件中进行设置，将更加灵活，可以在一台共享机器上只影响某一个目录，不需要重新启动服务器，而且也不需要管理员权限。缺点在于，目录中每个被读取和解析的文件每次都要进行处理，而不是只在启动时处理一次，所以性能会有所降低。

### PHP中函数调用不区分大小写：
```php
<?php   
	function func(){
		echo "X<br/>";
	}

	func();
	Func();
	fUNc();
?>
```
将输出：
X
X
X

PHP`不支持函数重载`，所以自定义函数不能和内置函数或者用户已定义的函数重名。

### 可变函数：
PHP 支持可变函数的概念。这意味着如果一个变量名后有圆括号，PHP 将寻找与变量的值同名的函数，并且尝试执行它。可变函数可以用来实现包括回调函数，函数表在内的一些用途。
如：$name();
可变函数不能用于例如 echo，print，unset()，isset()，empty()，include，require 以及类似的语言结构。

### 帮助器函数：
func_num_args() 返回传入的参数的个数
func_get_args() 返回参数的数组
func_get_arg() 一次获得一个参数，如获得第2个参数func_get_arg(1)

如果希望一个在函数内部创建的变量具有全局作用域，可以使用global关键字：
```php
function fn(){
global $var;
$var = “XX”;
}
fn();
echo $var;
```
因为这个变量被明确的声明为全局变量，因此在函数调用结束后变量在函数外部也存在。

## 面向对象
### 类定义、构造函数、析构函数
```php
class classname{
public $attribute;
function __construct($param){
...
}

function __destruct(){
...
}
}
```

### 实例化
```php
$a = new classname(“first”);
$a->attribute = “test”;
```

### $this指针
```php
class classname{
public $attribute;
function operation($param){
$this->attribute = $param;
}	
}
```

### __get、__set
```php
class classname{
public $attribute;
function __get($name){
return $this->$name;
}

function __set($name,$value){
$this->$name = $value;
}
}

$a = new classname();
$a->attribute = 5;
```
该语句将调用__set()函数，将其$name参数的值设置为attribute，而$value的值被设置为5。
$var = $a->attribute;
这条语句将调用__get()函数，$name的值为attribute。

private、public、protected
默认为public

### 继承
```php
class B extends A{
...
}
```

### 重载
```php
<?php   
	class A{
		public $attribute ="default A";
		function operation(){
			echo "A.attribute:".$this->attribute."<br/>";
		}
	}

	class B extends A{
		public $attribute = "default B";
		function operation(){
			echo "B.attribute:".$this->attribute."<br/>";
		}
	}

	$a = new A();
	$a->operation();
	$b=new B();
	$b->operation();
?>

$a = new A();
$a->operation();
$b=new B();
$b->operation();
```
将输出：
A.attribute:default A
B.attribute:default B

### parent关键字
修改：
```php
class B extends A{
		public $attribute = "default B";
		function operation(){
			parent::operation();
			echo "B.attribute:".$this->attribute."<br/>";
		}
	}

$b = new B();
$b->attribute = "B";
$b->operation();
```
将输出：
A.attribute:B  
B.attribute:B

### final关键字
禁止类被继承：
```php
final class A{
...
}
```

### 禁止函数被重载：
```php
class A{
		public $attribute ="default A";
		final function operation(){
			echo "A.attribute:".$this->attribute."<br/>";
		}
	}
```

### 接口
```php
interface Displayable{
function display();
}

class webPage implements Displayable{
function display(){
...
}
}
```

### Per-Class常量
```php
class Math{
const pi = 3.14;
}
```
可以在不初始化类的情况下使用该常量：
echo Math::pi;

### 静态方法
```php
class Math{
static function squared($input){
return $input*$input;
}
}
echo Math::squared(8);
```

### 延迟静态绑定
延迟静态绑定，就是把本来在定义阶段固定下来的表达式或变量，改在执行阶段才决定，比如当一个子类继承了父类的静态表达式的时候，它的值并不能被改变，有时不希望看到这种情况。
```php
class A{
		public static function echoClass(){
			echo __CLASS__;
		}

		public static function test(){
			self::echoClass();      
		}
	}

	class B extends A{      
		public static function echoClass(){
			echo __CLASS__;
		}
	}

	B::test(); //输出A
```
延迟静态绑定使用了static关键字，而不是self：
```php
class A{
		public static function echoClass(){
			echo __CLASS__;
		}

		public static function test(){
			static::echoClass();      
		}
	}

	class B extends A{      
		public static function echoClass(){
			echo __CLASS__;
		}
	}

	B::test(); //输出B
```

### 克隆对象
PHP提供了clone关键字：
$b = clone $a; // 将创建与$a具有相同类的副本，而且具有相同的属性值
要想改变默认的克隆行为，需要重定义__clone()方法。

### 抽象类
```php
abstract class A{
abstract function operation($param);
}
```

### 使用__call()实现方法重载
```php
public function __call($method, $p){
if($method == “display”){ // 不用实现任何display()方法
if(is_object($p[0])){
$this->displayObject($p[0]);
}
else if(is_array($p[0]){
$this->displayArray($p[0]);
}
else{
$this->displayScalar($p[0]);
}
}
}
$ov = new overload();
$ov->display(array(1,2,3));
$ov->display(‘cat’);
```
使用__call可以实现调用不存在的方法，如上例中的display方法根本不存在。

### __autoload()
__autoload()不是一个类方法，而是一个单独的函数，可以在任何类声明之外声明这个函数，如果实现了这个函数，它将在实例化一个还没有被声明的类时自动调用。其主要用途是尝试包含或请求任何用来初始化所需要的文件。
function __autoload($name){
include_once $name.’.php’;
}

### 类迭代器*
可以通过foreach来循环得到类的所有属性：
```php
class myClass{
public $a = ‘1’;
public $b = ‘2’;
public $c = ‘3’;
}
$x = new myClass;
foreach( $x as $attribute){
echo $attribute;
}
```
输出：123

可以结合使用Iterator和IteratorAggregate接口自定义更加复杂的类属性遍历行为。

### __tostring()
如果在类定义中实现了__tostring()方法，当尝试打印该类时，将会调用这个方法：
```php
class Printable{
		public $one="1";
		public $two="2";
		public function __toString(){
			//return (var_export($this,true));
			return $this->one.$this->two;
		}
	}
	
	$o = new Printable;
	echo $o;
```
输出：12

### 反射
```php
class A{
		public $attribute ="default A";
		function operation(){
			echo "A.attribute:".$this->attribute."<br/>";
		}
}
	
$class = new ReflectionClass("A");
echo $class;
```
输出：
Class [ class A ] { @@ D:\wamp\www\test.php 3-8 - Constants [0] { } - Static properties [0] { } - Static methods [0] { } - Properties [1] { Property [ public $attribute ] } - Methods [1] { Method [ public method operation ] { @@ D:\wamp\www\test.php 5 - 7 } } }


## 异常处理
在PHP中，异常必须手动抛出。
```php
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
```
输出：
10
An exception
D:\wamp\www\test.php
4
exception 'Exception' with message 'An exception' in D:\wamp\www\test.php:4 Stack trace: #0 {main}


## MySql管理、操作*
（MySql数据库的管理、操作，略过）

## PHP访问MySql
（本节内容参考自网络）
mysql、mysqli
PHP-MySQL（mysql）是PHP操作MySQL数据库最原始的Extension，PHP-MySQLi（mysqli）的i代表 Improvement ，提供了相对进阶的功能，也增加了安全性。而PDO (PHP Data Object) 则是提供了一个 Abstraction Layer 来操作数据库。
 
mysql是非持继连接函数而mysqli是永远连接函数。也就是说mysql每次链接都会打开一个连接的进程而mysqli多次运行mysqli将使用同一连接进程,从而减少了服务器的开销。
如果使用new mysqli('localhost', usenamer', 'password', 'databasename')总是报错，Fatal error: Class 'mysqli' not found in ...那么要检查一下mysqli是不是开启的。mysqli类不是默认开启的，win下要改php.ini,去掉php_mysqli.dll前的;,linux下要把mysqli编译进去。
当然，如果mysql也需要永久连接的话，就可以使用mysql_pconnect()这个函数。

mysqli的用法：
$mysqli = new mysqli('localhost', 'my_user', 'my_password', 'my_db');

mysql的用法：
$link = mysql_connect('example.com:3307', 'mysql_user', 'mysql_password');

例（使用mysql）：
```php
<?php
$con = mysql_connect("localhost","peter","abc123");
if (!$con)
  	{
  		die('Could not connect: ' . mysql_error());
  	}

mysql_select_db("my_db", $con);

$result = mysql_query("SELECT * FROM Persons");

while($row = mysql_fetch_array($result))
  	{
  		echo $row['FirstName'] . " " . $row['LastName'];
  		echo "<br />";
  	}

mysql_close($con);
?>
```

使用mysqli：
```php
$searchtype = $_POST[‘searchtype’];
$searchterm = $_POST[‘searchterm’];
if(!searchterm  || !searchterm’){
echo “Invalid”; //查询条件为空
}

if(!get_magic_quotes_gpc()){
$searchtype = addslashes($searchtype);
$searchterm = addslashes($searchterm );
}
@$db = new mysqli(‘localhost’,'my_user', 'my_password', 'db_bookes');
if(mysqli_connect_errno()){
		echo “Could not connect”; //连接失败
}
$query = “select * from books where ”.$searchtype.” like ‘%”.$searchterm.”%’”;
$result = $db->query($query);
$num_results = $result->num_rows;
echo “Numbers of books:”.$num_results;
for($i=0; $i<$num_results; $i++){
$row = $result->fetch_assoc();
echo htmlspecialchars(stripslashes($row[‘title’]));
...
}
$result->free();
$db->close();
```

### Prepared语句
mysqli支持prepared语句，好处有2：
对于在执行大量具有不同数据的相同查询时，可以提高执行速度；
可以免受SQL注入攻击；
通常的数据库插入操作如下：
```php
$query = “insert into books values(‘”.$isbn.”’,’”.$author.”’,’”.$title.”’,’”.$price.”’)”;
$result = $db->query($query);
if($result){
echo $db->affected_rows;
...
```
Prepared语句的基本思想是向MySql发送一个需要执行的查询模板，然后再单独发送数据。因此可以向相同的Prepared语句发送大量相同的数据，对于批处理的插入操作来说是非常有用的。
```php
$query = “insert into books values(?,?,?,?)”;
$stmt = $db->prepare($query);
$stmt->bind_param(“sssd”,$isbn,$author,$title,$price); //sssd为格式化字符串
$stmt->execute();
echo $smtt->affected_rows;
$stmt->close();
```
对于查询操作，也可以绑定查询结果至变量：
$stmt->bind_result($isbn,$author,$title,$price);

## 安全*
（略）

## 服务器交互
php.ini中用来控制文件上传的指令：
file_uploads：控制是否允许http方式的文件上传，默认为ON
upload_tmp_dir：指定被上传的文件在被处理之前的临时存放目录，如果没有配置，将使用系统默认值
upload_max_filesize：控制允许上传的文件的大小，如果文件大小大于该值，将创建一个文件大小为0的占位符文件，默认为2M
post_max_size：控制可接受的，通过POST方法上传数据的最大值。

上传文件的HTML表单：
<form action="upload_file.php" method="post" enctype="multipart/form-data">
<input type=”hidden” name=”MAX_FILE_SIZE” value=”1000000”>
<label for="file">Filename:</label>
<input type="file" name="userfile" id="userfile" /> 
<br />
<input type="submit" name="submit" value="Submit" />
</form>
设置属性enctype="multipart/form-data"，这样服务器就可以知道上传的文件带有常规的表单信息。
名为MAX_FILE_SIZE的隐藏表单域是可选的，用来控制允许上传的文件的最大长度值（按字节计算）。该值也可以在服务器端设置，但是如果在表单中使用，则名称必须为MAX_FILE_SIZE。

### 处理上传文件：
当文件被上传时，将被保存在临时目录中，脚本执行完成后该临时文件将会被删除。
```php
if($_FILES[“userfile”][“error”] > 0){
switch($_FILES[“userfile”][“error”]){
case 1:
echo “上传文件的大小超过了约定值（upload_max_filesize）”;
break;
case 2:
echo “上传文件大小超过了HTML表单指定的最大值（MAX_FILE_SIZE）”;
break;
case 3:
echo “文件只被部分上传”;
break;
case 4:
echo “没有上传任何文件”;
break;
case 6:
echo “没有指定临时目录”;
break;
case 7:
echo “将文件写入磁盘失败”;
break;
}
exit;
}

if($_FILES[“userfile”][“type”] !=”text/plain”){
echo “文件类型不对”;
exit;	
}

$upfile = “/uploads/”.$_FILES[“userfile”][“name”];

if(is_uploaded_file($_FILES[“userfile”][“tmp_name”])){
if(!move_uploaded_file($_FILES[“userfile”][“tmp_name”]),$upfile)){
echo “不能移动临时文件到指定位置”;
exit;
}
else{
echo “可能存在文件上传攻击”;
exit;
}

echo “文件上传成功”;
```
### 目录函数
列出目录内容：
```php
$current_dir = ".";
	$dir = opendir($current_dir);
	while(false !== ($file = readdir($dir))){
		if($file != '.'  && $file != '..'){ //过滤掉特殊目录
			echo $file."<br/>";
		}
	}
// rewinddir($dir); //恢复到开始处
	closedir($dir);
```
将输出：
a.html
ace
ace - 快捷方式.lnk
adcodecoverage
android代码覆盖率.zip
cc
cc - 快捷方式.lnk
cc-old

可以使用dir类完成同样的功能：
```php
$dir = dir(".");
	while(false !== ($file = $dir->read())){
		if($file != '.'  && $file != '..'){
			echo $file."<br/>";
		}
	}
	$dir->close();
```

如果想要指定目录内容排序输出，可以使用scandir：
```php
$dir = ".";
	$files1 = scandir($dir); // 顺序
	$files2 = scandir($dir,1); // 倒序
	
	foreach($files1 as $file){
		echo $file."<br/>";
	}
	
	foreach($files2 as $file){
		echo $file."<br/>";
	}
```
### 获取目录名和文件名：
```php
$file = "D:/wamp/www/test.htm";
	
echo dirname($file)."<br/>";
echo basename($file)."<br/>";
```
输出：
D:/wamp/www
test.htm

### 获取目录所在磁盘空间：
```php
$dir = "D:/wamp/www";
echo disk_total_space($dir)."<br/>";
echo disk_free_space($dir)."<br/>";
```
输出：
136314875904
70492467200

### 创建和删除目录：
```php
mkdir(“/tmp/testing”,0777);
rmdir(“/tmp/testing”);
```

### 文件函数：
```php
	$file = "test.htm";
	echo date('j F Y H:i',fileatime($file)); //最近一次被访问的时间
	echo date('j F Y H:i',fileatime($file)); //最近一次被修改的时间
	
	echo fileowner($file); //文件拥有者
	echo filegroup($file); //文件用户组
	echo fileperms($file); //文件权限
	echo filetype($file); //文件类型
	echo filesize($file); //文件大小
```
所有文件状态函数的运行都很费时间，因此它们的结果将被缓存起来。如果要在变化之前或者变化之后检查文件信息，需要调用函数：clearstatcache()来清除以前的缓存结果。

此外还有一组测试函数：is_dir()、is_executable()、is_file()、is_readable()、is_writable()等。
更改文件属性的函数：chgrp()、chmod()、chown()。
touch()，用来创建一个文件，或者修改文件上次被修改的时间
unlink()，删除文件
copy()、rename() 复制和重命名文件：
copy($source_path,$destination_path)
rename($oldfile,$newfile)

### 执行命令*：
有4种主要的在Web服务器上执行命令的方法：
1）	exec()函数
没有直接的输出，将返回命令执行结果的最后一行
2）	passthru()函数
直接将输出显示到浏览器，如果输出是二进制，比如图片，这将是非常有用的。
3）	system()函数
将命令的输出回显到浏览器，它将每一行的输出向后对齐。
4）	反引号
``
更复杂的需求可以使用popen()、proc_open()、proc_close()，这些函数可以启动外部进程，并且在这些进程之间传递数据。

如果要把用户提交的数据包含在命令中，考虑到安全性问题，可以使用escapeshellcmd来阻止用户在系统上执行恶意的命令：
system(escapeshellcmd($command));

### 环境变量
运行phpinfo()可以获得PHP所有环境变量的列表。
获取环境变量：getenv(“HTTP_REFERER”);
设置环境变量：
$home = “/home/nobody”;
putenv(“HOME=$home”);
可以设置safe_mode_allowed_env_vars指令来控制当PHP在安全模式下运行时程序员可设置的环境变量集合。

## 网络协议
邮件*

### 使用其他站点的数据
```php
$url = “http://....”;
if(!($contents = file_get_contents($url))){
die(“failed to open url”);
)
...
```

### 检查函数
例：用于检查url的脚本：
```php
$url = "http://www.baodu.com";	
	$url = parse($url);
	$host = $url['host'];
	if(!($ip = gethostbyname($host))){
		echo "这个url的host没有合法的ip";
		exit;
	}
	echo "该url的host的ip为".$ip."<br/>";
```

ftp*


## 时间日期
### date()函数
date()函数有两个参数，一个必选的格式化字符串和一个可选的UNIX时间戳
echo date('Y-m-d H:i:s');
输出：2015-05-09 05:37:12

### 获取UNIX时间戳的方法：
```php
echo mktime(11,45,40,5,9,2015); //mktime()用于将一日期和时间转换成UNIX时间戳
echo '<br/>';

echo time(); //获取当前系统的UNIX时间戳
echo '<br/>';

echo date('U'); //获取当前系统的UNIX时间戳
```
输出：
1431164740
1431143156
1431143156

### getdate()函数
以时间戳作为可选参数，返回一个相关数组，表示日期和时间的各个部分。
$today = getdate();
print_r($today);
输出：
Array ( [seconds] => 48 [minutes] => 50 [hours] => 5 [mday] => 9 [wday] => 6 [mon] => 5 [year] => 2015 [yday] => 128 [weekday] => Saturday [month] => May [0] => 1431143448 )

### checkdate()函数
用于检查日期是否有效，如月份是否介于1-12，闰年等。
checkdate(2,29,2008); //true
checkdate(2,29,2009); //false

### strftime()函数
根据系统的locale来格式化一个时间戳。
echo strftime('%A');
echo strftime('%c');
输出：
Saturday05/09/15 05:59:39

### PHP与MySql时间转换
MySql中的日期和时间是按照ISO标准处理的，即形如2008-03-29或者08-02-29这两种格式。PHP和MySql的通信常常会需要进行日期和时间的转换，这可以在其中任意一端进行：
PHP：可以使用date()函数进行转换。需要注意的是，月份和具体的日期应该使用带有前导0的格式。
MySql：可以使用DATE_FORMATE()或者UNIX_TIMESTAMP()。
SELECT DATE_FORMATE(date_column,’%m %d %y’) FROM tablename;
SELECT UNIX_TIMESTAMP(date_column) FROM tablename;

### 日期计算
可以通过转化为时间戳的方式进行计算，但是这样的方法存在很多缺陷，如1970年以前的日期、闰年等。在以前，涉及日期时间计算的问题必须使用MySql来进行（MySql提供了大量的日期操作函数）。PHP5.3提供了一些新的函数：date_add()、date_sub()、date_diff()等。

### 微秒
echo microtime();
0.37470700 1431145149

echo microtime(true);
1431145269.6923

## 图像*

## 会话控制
PHP的会话是通过唯一的会话ID来驱动的，会话ID是一个加密的随机数字串，它由PHP生成，在会话的生命周期中都会保存在客户端。可以通过cookie或者url来在网络上传递。

### 设置cookie的HTTP标题头：
Set-Cookie:NAME=VALUE;[expires=DATE;][path=PATH;][domain=DOMAIN_NAME;][secure]
expires为该cookie的失效日期，如果不设置，cookie将永远有效，除非手工删除。
path和domain合起来指定与该cookie相关的URL
secure用来指定在普通的HTTP连接中不发送cookie

### 通过PHP设置cookie：
bool setcookie(string name [,string value [,int expire [,string path [,string domain [,int secure]]]]])
例：
```php
setcookie("test","value",time()+3600);
	if(isset($_COOKIE["test"])){
		echo "have cookie:";
		echo $_COOKIE["test"];
	}
```
加载，再刷新一次后输出：
have cookie:value

### 删除cookie：
```php
setcookie("test","value",time()-1);  // 将cookie设为一个已过去的时间
if(isset($_COOKIE["test"])){
	echo "have cookie:";
	echo $_COOKIE["test"];
}
```
加载页面，再刷新，将无输出。

`注意`：
```php
setcookie("test"); //书上及网络上声称可以用来删除cookie的方法
	if(isset($_COOKIE["test"])){
		echo "have cookie:";
		echo $_COOKIE["test"];
	}
```
将输出：
have cookie:
所以这种方式仅仅是将cookie的值设为空，但是cookie还是存在的。

### 注销会话、删除cookie
```php
session_start(); 
$_SESSION = array(); 
if(ini_get('session.use_cookies')) { 
$pms = session_get_cookie_params();  // 获取由会话控制设置的cookie内容
setcookie( 
session_name(), 
        session_id(), 
        time()-1000, 
        $pms['path'], 
        $pms['domain'], 
        $pms['secure'], 
        $pms['httponly']  
}
session_destroy();  
```

### 脱离cookie来使用session：
有时候会遇到因cookie无法使用从而导致session变量不能跨页传递。原因可能有：
客户端禁用了cookie；
浏览器出现问题，暂时无法存取cookie；
php.ini中的session.use_trans_sid = 0，或者编译时没有打开--enable-trans-sid选项。
当代码session_start()时，就在服务器上产生了一个session文件，随之也产生了一个唯一对应的sessionID。跨页后，为了使用session，必须又执行session_start()，将又会产生一个session文件以及新的sessionID，这个新的sessionID无法用来获取前一个sessionID设置的值。除非在session_start()之前加代码session_id($session_id);将不会产生session文件，而是直接读取与这个id对应的session文件。

假设cookie功能不可用，可以通过如下方式实现抛开cookie来使用session：
设置php.ini中的session.use_trans_sid =1，或者编译时打开--enable-trans-sid选项，让PHP自动跨页传递sessionID（基于URL）；
手动通过URL传值、隐藏表单传递sessionID，即约定一个键名，读出后手工赋值给session_id();函数；
用文件、数据库等形式保存sessionID，在跨页中手工调用。

注：
清除所有cookie后，加载本地127.0.0.1/test.php，内容如下：
```php
<?php
	session_start(); 
	$_SESSION['var1']="value"; 
	$url="<a href="."'test2.php'>link</a>"; 
	echo $url; 
?>
```
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

### 开始会话：
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

配置会话控制：
session.auto_start 自动启动会话，默认为0
session.cache_expire 为缓存中的会话页设置当前时间 默认180分钟
session.cookie_domain 指定会话cookie中的域 默认为none
session.cookie_lifetime 指定cookie在用户机器上延续多久，默认值为0，表示延续到浏览器关闭
session.cookie_path 指定会话cookie中要设置的路径
Session.name 会话的名称，在用户系统中用作会话名，默认为PHPSESSID
session.save_handler 定义会话数据保存的地方，可以将其设置为指向一个数据库，但是要编写自己函数，默认值为files
session.save_path 会话数据存储的路径
session.use_cookie 在客户端使用cookie的会话
session.cookie_secure 是否该在安全连接中发送cookie
session.hash_function 指定用来生成会话ID的哈希算法，如MD5，SHA1

## PHP杂项函数*

## 创建实用的PHP和MySql项目*


