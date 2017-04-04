# 脱离cookie来使用session

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