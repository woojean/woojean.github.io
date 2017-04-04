# PHP对正则表达式的支持

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