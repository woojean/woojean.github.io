---
layout: post
title:  "WebServer"
date: 2017-04-05 00:00:01
categories: 技术问题分类总结
tags: Nginx
excerpt: ""
---

* content
{:toc}

## CGI、CGI程序、Fast-CGI、php-fpm、Web Server等概念理解
* **CGI**：即公共网关接口(Common Gateway Interface)，<u>描述了客户端和服务器程序之间传输数据的一种协议</u>，就是规定要传哪些数据、以什么样的格式传递给后方处理这个请求的协议。总之，<u>CGI是一种标准，一种协议</u>。
* **CGI程序**：确保CGI协议的顺利执行，并且返回结果，用来沟通程序（如PHP、Python、 Java）和Web服务器（Apache2、Nginx），充当桥梁的作用。即<u>CGI程序是介于Web服务器与Web程序之间的用来保证CGI协议执行的程序</u>。比如php-cgi（安装php后会自动安装）。
* **Web Server**：内容的分发者，比如Nginx，通过配置Web Server知道如何处理不同类型的文件，比如请求.html文件，Web Server能够判断出是静态文件；请求.php文件，Web Server知道如何找到并执行对应的CGI程序（<u>比如php-cgi</u>）来处理，根据CGI协议把需要的数据传给CGI程序，对于php-cgi来说，会解析php.ini文件，初始化执行环境，然后找到index.php并编译执行。CGI程序的输出会被Web Server收集并加上合适的HTTP Header并返回给客户端。
* CGI执行的特点是每次请求过来后再启动CGI程序去处理请求，一次请求对应一个新的CGI进程。一些更有效的技术可以<u>让脚本解释器直接作为模块集成在 Web 服务器</u>（例如：Apache，mod_php）<u>中</u>，这样就能避免重复载入和初始化解释器（即无需再额外执行CGI程序）。不过这只是就那些需要解释器的高级语言（即解释型语言）而言的，使用诸如C一类的编译语言则可以避免这种额外负荷。
* **FastCGI程序**：如Apache的mod_fcgid，php-fpm等。<u>使用持续的进程来处理多个请求</u>。FastCGI会启动FastCGI进程管理器（简称master），解析配置文件，初始化执行环境，master再启动多个CGI程序（简称worker）在那里等候。当进来一个请求时，Web Server把环境变量和这个页面请求通过一个Unix Socket或者一个TCP connection传递给FastCGI进程。master的执行特点是请求过来，把请求传递给到空闲的worker，然后立即可以接受下一个请求，再传递。每个worker都一直在等候，接到从master传递过来的请求之后，立即执行并返回，但是<u>执行完毕后，不销毁，而且继续等待下个请求。</u>
* 修改php.ini文件后，php-fpm可以平滑重启（php-fpm reload），php-cgi进程无法平滑重启只能restart。


## Nginx fastcgi_index配置的作用
**语法：**
```
fastcgi_index file 
```
如果URI以斜线结尾，文件名将追加到URI后面，这个值将存储在Nginx内置变量$fastcgi_script_name中。

**例如：**
```
fastcgi_index  index.php;
fastcgi_param  SCRIPT_FILENAME  /home/www/scripts/php$fastcgi_script_name;
```
请求/page.php时，SCRIPT_FILENAME将被设置为/home/www/scripts/php/page.php，但是请求`/`则为/home/www/scripts/php/index.php。


## Nginx fastcgi_pass的不同配置
该配置用于指定FastCGI服务器监听端口与地址。
* 直接使用IP地址和端口号
```
fastcgi_pass localhost:9000;
```

* 使用Unix Socket
```
fastcgi_pass unix:/tmp/fastcgi.socket;
```

* 使用upstream
```
upstream backend  {  
  server   localhost:1234;
} 
fastcgi_pass backend;
```

## Nginx与php-fpm配合工作的流程
* （1）FastCGI进程管理器php-fpm自身初始化，启动主进程php-fpm和启动start_servers个CGI子进程。主进程php-fpm主要是管理fastcgi子进程，监听9000端口。fastcgi子进程等待来自Web Server的连接。
* （2）当客户端请求到达Nginx时，Nginx通过location指令，将所有以php为后缀的文件都交给127.0.0.1:9000来处理。
* （3）FastCGI进程管理器PHP-FPM选择并连接到一个子进程CGI解释器。Web server将CGI环境变量和标准输入发送到FastCGI子进程。
* （4）FastCGI子进程完成处理后将标准输出和错误信息从同一连接返回Web Server。当FastCGI子进程关闭连接时，请求便告处理完成。
* （5）FastCGI子进程接着等待并处理来自FastCGI进程管理器（运行在 WebServer中）的下一个连接。


## Nginx反向代理提高性能的理解
对于后端是动态服务来说，比如Java和PHP。这类服务器（如JBoss和PHP-FPM）的IO处理能力往往不高。Nginx可以把Request在读取完整之前buffer住，这样交给后端的就是一个完整的HTTP请求，从而提高后端的效率，而不是断断续续的传递。同样，Nginx也可以把response给buffer住，同样也是减轻后端的压力。





# 网络协议
----------------------------------------------------------------------------------------------------
## Cookie小结
* Expires：默认情况下Cookie是暂时存在的，他们存储的值只在浏览器会话期间存在，当用户退出浏览器后这些值也会丢失，如果想让Cookie存在一段时间，就要为expires属性设置为未来的一个过期日期；
* max-age：expires属性现在已经被max-age属性所取代，max-age用秒来设置cookie的生存期；
* path：页面只能获取它属于的Path的Cookie。例如/session/test/a.php不能获取到路径为/session/abc/的Cookie；
* secure：如果不希望Cookie在HTTP等非安全协议中传输，可以设置Cookie的secure属性为true。浏览器只会在HTTPS和SSL等安全协议中传输此类Cookie；
* HTTPOnly：如果在cookie中设置了HttpOnly属性，那么通过js脚本将无法读取到cookie信息，这样能有效的防止XSS攻击；
* Cookie中使用Unicode字符时需要对Unicode字符进行编码（Cookie中保存中文只能编码，推荐使用UTF-8，因为JavaScript不支持GBK编码）；
* 要想修改Cookie只能使用一个同名的Cookie来覆盖原来的Cookie。删除时只需要把maxAge修改为0即可（Cookie并不提供直接的修改、删除操作）。
* Cookie具有不可跨域名性（同一个一级域名下的两个二级域名如www.demo.com和images.demo.com也不能交互使用Cookie，因为二者的域名并不严格相同。如果想所有demo.com名下的二级域名都可以使用该Cookie，需要设置Cookie的domain参数为.demo.com（以.开头），这样所有以demo.com结尾的域名都可以访问该Cookie。

## Session及Url重写
Session在用户第一次访问服务器的时候自动创建。Session生成后，只要用户继续访问，服务器就会更新Session的最后访问时间，并维护该Session。为防止内存溢出，服务器会把长时间内没有活跃的Session从内存删除。这个时间就是**Session的超时时间**。如果超过了超时时间没访问过服务器，Session就自动失效了。
虽然Session保存在服务器，对客户端是透明的，它的正常运行仍然需要客户端浏览器的支持（如果使用Cookie来发送SessionID的话）。这是因为Session需要使用Cookie作为识别标志。如果浏览器不支持Cookie，则需要依赖URL重写。
URL地址重写是对客户端不支持Cookie的解决方案。URL地址重写的原理是将该用户Session的id信息重写到URL地址中。服务器能够解析重写后的URL获取Session的id。


## GET和POST在TCP层的区别
GET产生一个TCP数据包，POST产生两个TCP数据包：
对于GET方式的请求，浏览器会把http header和data一并发送出去，服务器响应200（返回数据）；
而对于POST，浏览器先发送header，服务器响应100 continue，浏览器再发送data，服务器响应200 ok（返回数据）。

## 协议缓存协商机制相关的6个HTTP头
HTTP缓存协商机制基于6个HTTP头信息进行，动态内容本身并不受浏览器缓存机制的排斥，**只要HTTP头信息中包含相应的缓存协商信息，动态内容一样可以被浏览器缓存**。不过对于POST类型的请求，浏览器一般不启用本地缓存。除了浏览器缓存，HTTP缓存协商机制同样适用于HTTP缓存代理服务器。

主要涉及以下6个HTTP Header：
`Expires`
`Cache-Control`
`Last-Modified`、`If-Modified-Since`
`ETag`、`If-None-Match`。

**Expires/Cache-Control**是控制浏览器**是否直接从浏览器缓存取数据还是重新发请求到服务器取数据**。只是Cache-Control比Expires可以控制的多一些，而且**Cache-Control会重写Expires的规则**。Cache-Control常见的取值有private、no-cache、max-age、must-revalidate等。如果指定Cache-Control的值为private、no-cache、must-revalidate，那么打开新窗口访问时都会重新访问服务器。而如果指定了max-age值，那么**在此值内的时间里就不会重新访问服务器**，例如：`Cache-control: max-age=5`表示当访问此网页后的5秒内再次访问不会去服务器。

**Last-Modified/If-Modified-Since**和**ETag/If-None-Match**是**浏览器发送请求到服务器后判断文件是否已经修改过**，如果没有修改过就只发送一个304回给浏览器，告诉浏览器直接从自己本地的缓存取数据；如果修改过那就整个数据重新发给浏览器。

**Expires和Cache-Control max-age的区别与联系**
1. Expires在HTTP/1.0中已经定义，Cache-Control:max-age在HTTP/1.1中才有定义。
2. Expires指定一个**绝对的过期时间**(GMT格式)，这么做会导致至少2个问题：
* 客户端和服务器时间不同步导致Expires的配置出现问题。
* 很容易在配置后忘记具体的过期时间，导致过期来临出现浪涌现象；（而Cache-Control:max-age指定的是从文档被访问后的存活时间，这个时间是个相对值，相对的是文档第一次被请求时服务器记录的请求时间。
3. Expires指定的时间可以是相对文件的最后访问时间或者修改时间，而max-age相对对的是文档的请求时间。
4. 在Apache中，max-age是根据Expires的时间来计算出来的max-age = expires- request_time:(mod_expires.c)

目前主流的浏览器都将HTTP/1.1作为首选，所以当HTTP响应头中同时含有Expires和Cache-Control时，浏览器会优先考虑Cache-Control。


**Last-Modified/If-Modified-Since和ETag/If-None-Match工作方式**

1. 浏览器把缓存文件的最后修改时间通过If-Modified-Since来告诉Web服务器（浏览器缓存里存储的不只是网页文件，还有服务器发过来的该文件的最后服务器修改时间）。服务器会把这个时间与服务器上实际文件的最后修改时间进行比较。如果时间一致，那么返回HTTP状态码304（但不返回文件内容），客户端接到之后，就直接把本地缓存文件显示到浏览器中。如果时间不一致，就返回HTTP状态码200和新的文件内容，客户端接到之后，会丢弃旧文件，把新文件缓存起来，并显示到浏览器中（当文件发生改变，或者第一次访问时，服务器返回的HTTP头标签中有Last-Modified，告诉客户端页面的最后修改时间）。

2. 浏览器把缓存文件的ETag，通过If-None-Match，来告诉Web服务器。思路与第一种类似。

**一个例子**
Request Headers
```
Host localhost
User-Agent Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.8.1.16) Gecko/20080702 Firefox/2.0.0.16
Accept text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5
...
If-Modified-Since Tue, 19 Aug 2008 06:49:35GMT
If-None-Match 7936caeeaf6aee6ff8834b381618b513
Cache-Control max-age=0
```

Response Headers
```
Date Tue, 19 Aug 2008 06:50:19 GMT
...
Expires Tue, 19 Aug 2008 07:00:19 GMT
Last-Modified Tue, 19 Aug 2008 06:49:35GMT
Etag 7936caeeaf6aee6ff8834b381618b513
```

对应以上两组缓存控制Header，按F5刷新浏览器和在地址栏里输入网址然后回车。这两个行为是不一样的。**按F5刷新浏览器，浏览器会去Web服务器验证缓存。如果是在地址栏输入网址然后回车，浏览器会直接使用有效的缓存，而不会发http request去服务器验证缓存，这种情况叫做`缓存命中`**。

Cache-Control: public 指可以`公有缓存`，可以是数千名用户共享的。
Cache-Control: private 指只支持`私有缓存`，私有缓存是单个用户专用的。
此外，针对不同的Cache-Control值，对浏览器执行不同的操作，其缓存访问行为也不一样，这些操作包括：打开新窗口、在地址栏回车、按后退按钮、按刷新按钮。

**Last-Modified/If-Modified-Since和ETag/If-None-Match工作方式的区别**
```
<?php
  header('Last-Modified:' . gmdate('D, d M Y H:i:s') . ' GMT');
  echo time();
?>
```

此时再通过浏览器请求该动态文件，HTTP响应中将会添加一个头信息：
```
Last-Modified:Fri, 20 Mar 2009 07:53:02 GMT
```

对于带有`Last-Modified`的响应，浏览器会对文件进行缓存，并打上一些标记，下次再发出请求时会带上如下的HTTP头信息：
```
If-Modified-Since:Fri, 20 Mar 2009 07:53:02 GMT
```

如果没有修改，服务器会返回304信息：
```
HTTP/1.1 304 Not Modified
...
```
意味着浏览器可以直接使用本地缓存的内容。

**使用基于最后修改时间的缓存协商存在一些缺点**：
1. 很可能文件内容没有变化，而只是时间被更新，此时浏览器仍然会获取全部内容。
2. 当使用多台机器实现负载均衡时，用户请求会在多台机器之间轮询，而不同机器上的相同文件最后修改时间很难保持一致，可能导致用户的请求每次切换到新的服务器时就需要重新获取所有内容。

比如服务器返回如下带ETag的响应：
```
ETag:"74123-b-938fny4nfi8"
```

浏览器在下次请求该内容时会在HTTP头中添加如下信息：
```
If-None-Match:"74123-b-938fny4nfi8"
```
如果相同的话，服务器返回304。
Web服务器可以自由定义ETag的格式和计算方法。


## Referer头的安全问题
**Referer的作用**
Referer是HTTP协议中的一个请求报头，用于**告知服务器用户的来源页面**。比如说从Google搜索结果中点击进入了某个页面，那么该次HTTP请求中的Referer就是Google搜索结果页面的地址。如果某篇博客中引用了其他地方的一张图片，那么对该图片的HTTP请求中的Referer就是那篇博客的地址。
一般**Referer主要用于统计**，像CNZZ、百度统计等可以通过Referer统计访问流量的来源和搜索的关键词（包含在URL中）等等，方便站长们有针性对的进行推广和SEO。

Referer另一个用处就是**防盗链**。可以用referrer-killer（一个js库）来实现反反盗链。

Referer是由浏览器自动加上的，**以下情况是不带Referer的**：

* （1）直接输入网址或通过浏览器书签访问
* （2）使用JavaScript的Location.href或者是Location.replace()
* （3）HTTPS等加密协议

**Referer的安全问题**
以新浪微博曾经的一个漏洞（新浪微博gsid劫持）为例。
gsid是一些网站移动版的认证方式，移动互联网之前较老的手机浏览器不支持cookie，为了能够识别用户身份（实现类似cookie的作用），就在用户的请求中加入了一个类似sessionid的字符串，通过GET方式传递，带有这个id的请求，就代表用户的帐号发起的操作。后来又因用户多次认证体验不好，gsid的失效期是很长甚至永久有效的（即使改了密码也无用，这个问题在很多成熟的web产品上仍在发生）。也就是说，一旦攻击者获取到了这个gsid，就等同于长期拥有了用户的身份权限。
只要攻击者在微博上给用户发一个链接（指向攻击者的服务器），用户通过手机点击进入之后，手机当前页面的URL就通过Referer主动送到了攻击者的服务器上，攻击者自然就可以轻松拿到用户的gsid进而控制账号。

## TCP粘包理解
TCP是**面向字节**的，即以流式传送，也就是连接建立后可以一直不停的发送，并**没有明确的边界定义**。而UDP是**面向报文**的，发送的时候是可以按照一个一个数据包去发送的，一个数据包就是一个明确的边界。因为TCP是流式传送，所以会开辟一个缓冲区，发送端往其中写入数据，每过一段时间就发送出去，因此有可能后续发送的数据（属于另一个包）和之前发送的数据同时存在缓冲区中并一起发送，造成粘包。接收端也有缓存，因此也存在粘包。
处理粘包的唯一方法就是**制定应用层的数据通讯协议**，通过协议来规范现有接收的数据是否满足消息数据的需要。在应用中处理粘包的基础方法主要有两种分别是**以4节字描述消息大小**或**用结束符**，实际上也有两者相结合的如HTTP，redis的通讯协议等。

## Nagle算法理解
Nagle算法为福特航空和通信公司1984年定义的**TCP拥塞控制方法**。
从键盘输入的一个字符，占用一个字节，可能在传输上造成41字节的包，其中包括1字节的有用信息和40字节的首部数据。这种情况转变成了4000%的消耗，且这些小包同样都需要经过ACK等。这样的情况对于轻负载的网络来说还是可以接受的，但是重负载的网络就受不了了，**会导致网络由于太多的包而过载**。
事实上，Nagle算法所谓的提高网络利用率只是它的一个副作用，**Nagle算法的主旨在于避免发送大量的小包**。Nagle算法并没有阻止发送小包，它只是阻止了发送大量的小包！
**Nagle算法的基本定义是任意时刻，最多只能有一个未被确认的小段**。 所谓`小段`，指的是小于MSS尺寸的数据块，所谓`未被确认`，是指一个数据块发送出去后，没有收到对方发送的ACK确认该数据已收到。Nagle算法会在TCP程序里添加两行代码，在未确认数据发送的时候让发送器把数据送到缓存里。任何数据随后继续**直到得到明确的数据确认或者直到攒到了一定数量的数据了再发包**。
默认情况下，发送数据采用Nagle算法。这样**虽然提高了网络吞吐量，但是实时性却降低了**，在一些交互性很强的应用程序来说是不允许的，使用`TCP_NODELAY`选项可以禁止Nagle 算法。

## TCP同时打开，同时关闭
**同时打开**
两个应用程序同时执行主动打开。每一端都发送一个SYN，并传递给对方，且每一端都使用对端所知的端口作为本地端口。例如：
主机a中一应用程序使用7777作为本地端口，并连接到主机b 8888端口做主动打开。
主机b中一应用程序使用8888作为本地端口，并连接到主机a 7777端口做主动打开。
**tcp协议在遇到这种情况时，只会打开一条连接**。
这个连接的建立过程需要4次数据交换，而一个典型的连接建立只需要3次交换（即3次握手）
但多数伯克利版的tcp/ip实现并不支持同时打开。
![image](/images/tech/net_10.png)

**同时关闭**
如果应用程序同时发送FIN，则在发送后会首先进入FIN_WAIT_1状态。在收到对端的FIN后，回复一个ACK，会进入CLOSING状态。在收到对端的ACK后，进入TIME_WAIT状态。这种情况称为同时关闭。
同时关闭也需要有4次报文交换，与典型的关闭相同。
![image](/images/tech/net_11.png)


## OAuth2.0工作过程理解
OAuth是一个关于授权（authorization）的开放网络标准，目前的版本是2.0版。其作用就是让"客户端"（第三方应用）安全可控地获取"用户"的授权，与"服务提供商"（平台，比如微信）进行互动。
OAuth**在"客户端"与"服务提供商"之间，设置了一个授权层**（authorization layer）。"客户端"不能直接登录"服务提供商"，只能登录授权层，以此将用户与客户端区分开来。"客户端"登录授权层所用的令牌（token），与用户的密码不同。**用户可以在登录的时候，指定授权层令牌的权限范围和有效期**。"客户端"登录授权层以后，"服务提供商"根据令牌的权限范围和有效期，向"客户端"开放用户储存的资料。

**OAuth 2.0的运行流程**

![image](/images/tech/net_7.png) 

客户端的授权模式（步骤B）

**OAuth 2.0的四种授权方式**

**1.授权码模式（authorization code） **
适用于有server端的应用授权，是功能最完整、流程最严密的授权模式。它的特点就是**通过客户端的后台服务器，与"服务提供商"的认证服务器进行互动**。

* （A）用户访问客户端，后者将前者导向认证服务器。
* （B）用户选择是否给予客户端授权。
* （C）假设用户给予授权，认证服务器将用户导向客户端事先指定的"重定向URI"（redirection URI），同时附上一个授权码。
* （D）客户端收到授权码，附上早先的"重定向URI"，向认证服务器申请令牌。这一步是在客户端的后台的服务器上完成的，对用户不可见。
* （E）认证服务器核对了授权码和重定向URI，确认无误后，向客户端发送访问令牌（access token）和更新令牌（refresh token）。

即：用一个URI去申请，获得用户授权后得到一个对应该URI的授权码。之后就可以用该URI+对应的授权码来获取一个令牌，之后就可以使用该令牌来通过授权层。

**2.隐式授权（implicit）**

适用于通过客户端访问的应用授权，不通过第三方应用程序的服务器，直接在浏览器中向认证服务器申请令牌，跳过了"授权码"这个步骤，因此得名。**所有步骤在浏览器中完成**，令牌对访问者是可见的，且客户端不需要认证。

* （A）客户端将用户导向认证服务器。
* （B）用户决定是否给于客户端授权。
* （C）假设用户给予授权，认证服务器将用户导向客户端指定的"重定向URI"，并在URI的Hash部分包含了访问令牌。
* （D）浏览器向资源服务器发出请求，其中不包括上一步收到的Hash值。
* （E）资源服务器返回一个网页(typically an HTML document with an embedded script)，其中包含的代码可以获取Hash值中的令牌。
* （F）浏览器执行上一步获得的脚本，提取出令牌。
* （G）浏览器将令牌发给客户端（客户端就可以凭借此令牌来获取数据）。

**实例：**

其中短暂停留的那个页面的url为：
https://www.zhihu.com/oauth/callback/login/qqconn?code=680726D150FF0B9DF2EBBE2EFEEEC0D4&state=7f13b99dc94e506e69ecb9ec83296eec

页面效果：
![image](/images/tech/net_8.png)

页面代码：
![image](/images/tech/net_9.png)

**3.密码模式（resource owner password credentials）**

用户向客户端提供自己的用户名和密码。客户端使用这些信息，向"服务商提供商"索要授权。这通常用在用户对客户端高度信任的情况下。

**4.客户端模式（client credentials）**

指客户端以自己的名义，而不是以用户的名义，向"服务提供商"进行认证。**严格地说，客户端模式并不属于OAuth框架所要解决的问题**。在这种模式中，用户直接向客户端注册，客户端以自己的名义要求"服务提供商"提供服务，其实不存在授权问题。