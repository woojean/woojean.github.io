# PHP自身缺陷所带来的若干安全问题

## 文件包含漏洞
文件包含漏洞是代码注入的一种，原理就是注入一段用户能够控制的脚本或代码，并让服务器端执行。常见的导致文件包含的函数包括：include()、include_once()、require()、require_once()、fopen（）、readfile()等。

当使用include()、include_once()、require()、require_once()这4个函数包含一个新的文件时，该文件将作为PHP代码执行，PHP内核并不会在意该被包含的文件是什么类型。

## 本地文件包含
即能够打开并包含本地文件的漏洞。
通常服务器端代码可能这样接收用户输入：
```
include ‘/home/wwwrun/’ .$file. '.php'；
```
看起来无论用户输入什么$file，最终只能包含'.php'文件。实际PHP内核由C语言实现，使用了一些C语言的字符串处理函数，在连接字符串时\x00将作为字符串结束符，所以当攻击者如下输入时：
```
../../etc/passwd\0
```
或者在通过Web输入时UrlEncode：
```
../../etc/passwd%00
```
就能截断file变量之后的字符串。

当PHP配置了open_basedir时，将可以使得这种攻击无效。open_basedir的作用是限制在某个特定目录下PHP能打开的文件，需要注意的是其值是目录的前缀，因此如果设置为/home/app/aaa，那么实际上如下目录都将被允许：
```
/home/app/aaa
/home/app/aaabbb
/home/app/aaa123
```
如果要限定一个指定的目录，则需要在最后加上'/'：
```
open_basedir = /home/app/aaa/
```

要解决文件包含漏洞应该尽量避免包含动态的变量，尤其是用户可以控制的变量，一种通用方式是使用枚举判断被包含的文件。

## 远程文件包含
如果PHP设置中allow_url_include为ON，则include、require函数可以加载远程文件，远程文件漏洞可以用来执行任意命令。

## 本地文件包含的利用技巧
1.包含用户上传的文件；
2.包含data://或php://input等伪协议；
3.包含Session文件；（需要攻击者能够控制Session文件的部分内容）
4.包含日志文件，比如Web Server的access log；（攻击者可以拼凑特定请求来将PHP代码写到日志中）
5.包含/proc/self/environ文件；（Web进程运行时的环境变量，其中很多都是用户可以控制的，比如User-Agent）
6.包含上传的临时文件；
7.包含其他应用程序创建的文件，比如数据库文件、缓存文件、应用日志等。

## 变量覆盖漏洞
当register_globals为ON时，PHP全局变量的来源可能是多个地方，包括页面的表单、Cookie等（会自动创建全局变量）。PHP4.2.0之后的版本register_globals默认为OFF。

extract()函数能够将变量从数组导入当前的符号表：
```
int extract( array $var_array [, int $extract_type [, string $prefix]] );
```
$extract_type指定将变量导入符号表时的行为，为EXTR_OVERWRITE时，若发生变量名冲突，将覆盖已有的变量。当extract()函数从用户可以控制的数组中导出变量时，将可能覆盖变量，从而绕过服务器端逻辑。

需要注意类似`$$k`的变量赋值方式可能覆盖已有的变量。

import_request_variables(...)函数将GET、POST、Cookie中的变量导入到全局，因而也可能导致变量覆盖问题。

parse_str()函数往往用于解析URL中的query string，当参数值能被用户控制时，很可能导致变量覆盖。

## 代码执行漏洞
PHP中popen()、system()、passthru()、exec()等函数都可以直接执行系统命令，eval()函数可以执行PHP代码。当攻击者可以控制输入时，将造成代码执行漏洞。

preg_replace()的第一个参数如果存在/e模式修饰符，则允许代码执行。

用户自定义的动态函数也能导致代码执行：
```
$dyn_func = $_GET['dyn_func'];
$argument = $_GET['argument'];
$dyn_func($argument);
```

Curly Syntax也能导致代码执行，它将执行花括号间的代码并将结果替换回去：
```
$var = "I was ...until $(`ls`) appeared here";
```

很多函数都可以执行回调函数，当回调函数可以被用户控制时，将导致代码执行。

unserialize()在执行时如果定义了__destruct()或者__wakeup()函数，则这两个函数将执行。

## 定制安全的PHP环境
1.register_globals = OFF
2.设置open_basedir
3.allow_url_fopen = Off
4.allow_url_include = Off
5.display_errors = Off / log_errors = On
6.magic_quotes_gpc = Off 因为有若干种方法可以绕过它，甚至由于它反而衍生出一些新的安全问题
7.cgi.fix_pathinfo = 0
8.session.cookie_httponly = 1
9.session.cookie_secure = 1 # 当全站HTTPS时，应该开启

## safe_mode
开启时，很多函数的行为将发生变化，略。

## disable_functions
应该禁止使用一些函数，详细列表略。