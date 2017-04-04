# session的基本使用

## 开始会话
开始一个会话有2种方法：
1）session_start()函数：该函数将检查是否有一个会话ID存在，如果不存在就创建一个，如果已经存在，就将这个已经注册的会话变量载入以便使用。
2）将PHP设置为当有用户访问网站的时候就自动启动一个会话，具体方法是打开php.ini中的session.auto_start选项。这种方法有一个很大的缺点：无法使用对象作为会话变量，因为该对象的类定义必须在创建该对象的会话开始之前载入。

## 创建会话变量
可以通过全局数组$_SESSION来注册新的会话变量，如：
$_SESSION[“new_var”] = “value”;
会话变量创建后，只有在会话结束，或者手动重置时才会失效。此外，php.ini中的gc_maxlifetime指令确定了会话的持续时间，超时后会话将会被垃圾回收。

## 使用会话变量
if(isset($_SESSION[‘myvar’])){
...

## 销毁会话
销毁会话变量：unset($_SESSION[‘myvar’]);
一次销毁所有的会话变量：$_SESSION = array();
清除会话ID：session_destroy();

## 配置会话控制
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