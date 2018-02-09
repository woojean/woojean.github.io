---
layout: post
title:  "Network Protocol"
date: 2017-04-05 00:00:03
categories: 编程
tags: 网络协议
excerpt: ""
---

* content
{:toc}


## Cookie小结
* **Expires**：默认情况下Cookie是暂时存在的，他们存储的值只在浏览器会话期间存在，当用户退出浏览器后这些值也会丢失，如果想让Cookie存在一段时间，就要为expires属性设置为未来的一个过期日期；
* **max-age**：expires属性现在已经被max-age属性所取代，max-age用秒来设置cookie的生存期；
* **path**：页面只能获取它属于的Path的Cookie。例如/session/test/a.php不能获取到路径为/session/abc/的Cookie；
* **secure**：如果不希望Cookie在HTTP等非安全协议中传输，可以设置Cookie的secure属性为true。浏览器只会在HTTPS和SSL等安全协议中传输此类Cookie；
* **HTTPOnly**：如果在cookie中设置了HttpOnly属性，那么通过js脚本将无法读取到cookie信息，这样能有效的防止XSS攻击；
* Cookie中使用Unicode字符时需要对Unicode字符进行编码（Cookie中保存中文只能编码，推荐使用UTF-8，因为JavaScript不支持GBK编码）；
* 要想修改Cookie只能使用一个同名的Cookie来覆盖原来的Cookie。删除时只需要把maxAge修改为0即可（Cookie并不提供直接的修改、删除操作）。
* Cookie具有不可跨域名性（同一个一级域名下的两个二级域名如www.demo.com和images.demo.com也不能交互使用Cookie，因为二者的域名并不严格相同。如果想所有demo.com名下的二级域名都可以使用该Cookie，需要设置Cookie的domain参数为.demo.com（以.开头），这样所有以demo.com结尾的域名都可以访问该Cookie。



## Session及Url重写

Session在用户第一次访问服务器的时候自动创建。Session生成后，只要用户继续访问，服务器就会更新Session的最后访问时间，并维护该Session。为防止内存溢出，服务器会把长时间内没有活跃的Session从内存删除。这个时间就是**Session的超时时间**。如果超过了超时时间没访问过服务器，Session就自动失效了。
虽然Session保存在服务器，对客户端是透明的，它的正常运行仍然需要客户端浏览器的支持（如果使用Cookie来发送SessionID的话）。这是因为Session需要使用Cookie作为识别标志。如果浏览器不支持Cookie，则需要依赖URL重写。
<u>URL地址重写是对客户端不支持Cookie的解决方案</u>。URL地址重写的原理是将该用户Session的id信息重写到URL地址中。服务器能够解析重写后的URL获取Session的id。




## GET和POST在TCP层的区别
<u>GET产生一个TCP数据包，POST产生两个TCP数据包</u>：
对于GET方式的请求，浏览器会把http header和data一并发送出去，服务器响应200（返回数据）；
而对于POST，浏览器先发送header，服务器响应<u>100 continue</u>，浏览器再发送data，服务器响应200 ok（返回数据）。



## HTTP缓存协商机制

HTTP缓存协商机制基于6个HTTP头信息进行，动态内容本身并不受浏览器缓存机制的排斥，**只要HTTP头信息中包含相应的缓存协商信息，动态内容一样可以被浏览器缓存**。不过对于POST类型的请求，浏览器一般不启用本地缓存。除了浏览器缓存，HTTP缓存协商机制同样适用于HTTP缓存代理服务器。

主要涉及以下6个HTTP Header：
`Expires`
`Cache-Control`
`Last-Modified`、`If-Modified-Since`
`ETag`、`If-None-Match`。

**Expires/Cache-Control**是控制浏览器**<u>是否直接从浏览器缓存取数据还是重新发请求到服务器取数据</u>**。只是Cache-Control比Expires可以控制的多一些，而且**Cache-Control会重写Expires的规则**。Cache-Control常见的取值有private、no-cache、max-age、must-revalidate等。如果指定Cache-Control的值为private、no-cache、must-revalidate，那么打开新窗口访问时都会重新访问服务器。而如果指定了max-age值，那么**在此值内的时间里就不会重新访问服务器**，例如：`Cache-control: max-age=5`表示当访问此网页后的5秒内再次访问不会去服务器。

**Last-Modified/If-Modified-Since**和**ETag/If-None-Match**是**浏览器<u>发送请求到服务器后判断文件是否已经修改过</u>**，如果没有修改过就只发送一个304回给浏览器，告诉浏览器直接从自己本地的缓存取数据；如果修改过那就整个数据重新发给浏览器。

**Expires和Cache-Control max-age的区别与联系**
1. Expires在HTTP/1.0中已经定义，Cache-Control:max-age在HTTP/1.1中才有定义。
2. Expires指定一个**绝对的过期时间**(GMT格式)，这么做会导致至少2个问题：
* 客户端和服务器时间不同步导致Expires的配置出现问题。
* 很容易在配置后忘记具体的过期时间，导致过期来临出现浪涌现象；（而Cache-Control:max-age指定的是从文档被访问后的存活时间，这个时间是个相对值，相对的是文档第一次被请求时服务器记录的请求时间。
3. Expires指定的时间可以是相对文件的最后访问时间或者修改时间，而max-age相对的是文档的请求时间。
4. 在Apache中，max-age是根据Expires的时间来计算出来的max-age = expires - request_time:(mod_expires.c)

目前主流的浏览器都将HTTP/1.1作为首选，所以当HTTP响应头中同时含有Expires和Cache-Control时，浏览器会优先考虑Cache-Control。


**Last-Modified/If-Modified-Since和ETag/If-None-Match工作方式**

1. 浏览器把缓存文件的最后修改时间通过If-Modified-Since来告诉Web服务器（<u>浏览器缓存里存储的不只是网页文件，还有服务器发过来的该文件的最后服务器修改时间</u>）。服务器会把这个时间与服务器上实际文件的最后修改时间进行比较。如果时间一致，那么返回HTTP状态码304（但不返回文件内容），客户端接到之后，就直接把本地缓存文件显示到浏览器中。如果时间不一致，就返回HTTP状态码200和新的文件内容，客户端接到之后，会丢弃旧文件，把新文件缓存起来，并显示到浏览器中（当文件发生改变，或者第一次访问时，服务器返回的HTTP头标签中有Last-Modified，告诉客户端页面的最后修改时间）。

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

对应以上两组缓存控制Header，按F5刷新浏览器和在地址栏里输入网址然后回车。这两个行为是不一样的。**按F5刷新浏览器，浏览器会去Web服务器验证缓存。如果是在地址栏输入网址然后回车，浏览器会直接使用有效的缓存，而不会发http request去服务器验证缓存，这种情况叫做缓存命中**。

Cache-Control: public 指可以公有缓存，可以是数千名用户共享的。
Cache-Control: private 指只支持私有缓存，私有缓存是单个用户专用的。
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

<u>对于带有`Last-Modified`的响应，浏览器会对文件进行缓存，并打上一些标记，下次再发出请求时会带上如下的HTTP头信息</u>：
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
2. <u>当使用多台机器实现负载均衡时，用户请求会在多台机器之间轮询，而不同机器上的相同文件最后修改时间很难保持一致，可能导致用户的请求每次切换到新的服务器时就需要重新获取所有内容</u>。

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
<u>处理粘包的唯一方法就是**制定应用层的数据通讯协议**</u>，通过协议来规范现有接收的数据是否满足消息数据的需要。在应用中处理粘包的基础方法主要有两种分别是**以4节字描述消息大小**或**用结束符**，实际上也有两者相结合的如HTTP，redis的通讯协议等。



## Nagle算法理解
Nagle算法属于**TCP拥塞控制方法**。
从键盘输入的一个字符，占用一个字节，可能在传输上造成41字节的包，其中包括1字节的有用信息和40字节的首部数据。这种情况转变成了4000%的消耗，且<u>这些小包同样都需要经过ACK等</u>。这样的情况对于轻负载的网络来说还是可以接受的，但是重负载的网络就受不了了，**会导致网络由于太多的包而过载**。
事实上，Nagle算法所谓的提高网络利用率只是它的一个副作用，**Nagle算法的主旨在于避免发送大量的小包**。Nagle算法并没有阻止发送小包，它只是阻止了发送大量的小包！
**Nagle算法的基本定义是任意时刻，最多只能有一个未被确认的小段**。 所谓小段，指的是小于MSS尺寸的数据块，所谓未被确认，是指一个数据块发送出去后，没有收到对方发送的ACK确认该数据已收到。Nagle算法会在TCP程序里添加两行代码，在未确认数据发送的时候让发送器把数据送到缓存里。任何数据随后继续**直到得到明确的数据确认或者直到攒到了一定数量的数据了再发包**。
默认情况下，发送数据采用Nagle算法。这样**<u>虽然提高了网络吞吐量，但是实时性却降低了</u>**，在一些交互性很强的应用程序来说是不允许的，使用`TCP_NODELAY`选项可以禁止Nagle 算法。



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



## HTTP Via首部字段

代理服务器的基本行为就是接收客户端发送的请求后转发给其他服务器，代理不改变请求URI，会直接发送给前方持有资源的目标服务器。从源服务器返回的响应经过代理服务器后再传给客户端。转发时，需要附加Via首部字段以标记出经过的主机信息：
请求时：
```
GET HTTP/1.1
Via：proxy1.com(squid/3.1)
```
返回时：
```
HTTP/1.1 200 OK
Via：pproxy1.com(squid/3.1)
```



## 网关与代理的区别

网关的工作机制和代理很相似，但是<u>网关能使通信线路上的服务器提供非HTTP协议的服务</u>，因此可以利用网关由HTTP请求转化为其他协议通信，与其他系统联动：
客户端-------HTTP请求------->网关-------非HTTP通信协议------->非HTTP服务器
客户端<------HTTP响应--------网关<-----非HTTP通信协议---------非HTTP服务器



## 完整的URI格式
```
http://user:pass@www.example.com:80/dir/index.html?param1=value1&param2=value2#flag
```



## HTTP 持久链接（HTTP Persistent Connections、HTTP keep-alive、HTTP connection reuse）与管线化技术（pipelining）：

HTTP协议的初始版本中，每进行一次HTTP通信就要断开一次TCP链接，频繁的TCP链接与断开（如请求一个有很多图片的网页）会造成无谓的流量消耗和低效的性能。
持久链接的特点是：只要任意一端没有明确地提出断开连接，则保持TCP连接状态。
在HTTP/1.1中，所有的连接默认都是持久连接，<u>持久连接的实现需要服务器端和客户端同时支持</u>。

持久连接使得当有多个请求时，可以以管线化方式发出请求：不用等待前一个请求的响应即可以直接发送下一个请求（<u>实现并行请求行为</u>）。



## HTTP请求方法

（方法名区分大小写，一定要用大写字母）
1.0和1.1都支持的方法有5个：
* GET
* POST
* PUT 用来传输文件（写文件），因为PUT方法自身不带验证机制，因此一般的Web网站不使用该方法；
* HEAD` 获取报文首部，与GET方法的主要区别在于其不返回报文主体，主要用于确认URI的有效性及资源更新的日期时间等；
* DELETE`：删除文件，作用于PUT相反；

仅1.1支持的方法有3个：
* OPTIONS 询问指定URI资源支持的方法；
* TRACE 让Web服务器将之前的请求通信环回给客户端的方法，配合Max-Forwards字段，可以用来查询发送出去的请求是怎样被加工修改的；
* CONNECT 要求在与代理服务器通信时建立隧道，实现用隧道协议进行TCP通信。主要使用SSL和TLS协议把通信内容加密后经网络隧道传输。
  （CONNECT这个方法的作用就是把服务器作为跳板，让服务器代替用户去访问其它网页，之后把数据原原本本的返回给用户。这样用户就可以访问到一些只有服务器上才能访问到的网站了，这就是HTTP代理。CONNECT方法是需要使用TCP直接去连接的，要是使用CONNECT方法，首先要让服务器监听一个端口来接收CONNECT方法的请求。Connection验证并建立连接后会返回HTTP/1.1 200 Connection Established，否则返回HTTP/1.1 407 Unauthorized。验证通过之后，我们就可以做普通的HTTP操作了。完全可以把现在的代理服务器看作是请求连接的Internet服务器，也就是说可以像直接访问普通的服务器一样，使用GET、POST等方法来请求Internet服务器上的页面了。）

仅1.0支持，在1.1中已经废弃的方法有2个：
* LINK

* UNLINE

  ​

## HTTP分块传输编码
HTTP可以对传输内容进行编码，内容编码后的实体由客户端接收并负责解码。通过在传输时进行编码，可以提升传输速率，但是因为编码及解码的过程需要由计算机来完成，因此会消耗更多的CPU等资源。常用的内容编码有以下几种：
gzip（GNU zip）
compress（UNIX系统的标准压缩）
deflate（zlib）

一般情况HTTP的Header包含Content-Length域来指明报文体的长度，有时候服务生成HTTP回应是无法确定消息大小的，比如大文件的下载，或者后台需要复杂的逻辑才能全部处理页面的请求，这时用需要实时生成消息长度，服务器一般使用chunked编码。
分块传输编码会将传输内容分成多个部分，每一部分都会用一个十六进制来标记大小，最后一部分会使用CR+LF来标记。使用分块传输编码的实体主体会由接收的客户端负责解码，恢复到编码前的实体主体。
如果一个HTTP消息（请求消息或应答消息）的**Transfer-Encoding**消息头的值为**chunked**，那么，消息体由数量未定的块组成，并以最后一个大小为0的块为结束。
每一个非空的块都以该块包含数据的字节数（字节数以十六进制表示）开始，跟随一个CRLF （回车及换行），然后是数据本身，最后块CRLF结束。在一些实现中，块大小和CRLF之间填充有白空格（0x20）。
最后一块是单行，由块大小（0），一些可选的填充白空格，以及CRLF。最后一块不再包含任何数据，但是可以发送可选的尾部，包括消息头字段。
消息最后以CRLF结尾。
例：
```
HTTP/1.1 200 OK
Content-Type: text/plain
Transfer-Encoding: `chunked`

25
This is the data in the first chunk

1C
and this is the second one

3
con
8
sequence
0
```

**分块传输编码的好处**
* HTTP分块传输编码允许服务器为动态生成的内容维持HTTP持久链接。通常，持久链接需要服务器在开始发送消息体前发送Content-Length消息头字段，但是对于动态生成的内容来说，在内容创建完之前是不可知的。

* 分块传输编码允许服务器在最后发送消息头字段。对于那些头字段值在内容被生成之前无法知道的情形非常重要，例如消息的内容要使用散列进行签名，散列的结果通过HTTP消息头字段进行传输。没有分块传输编码时，服务器必须缓冲内容直到完成后计算头字段的值并在发送内容前发送这些头字段的值。

* HTTP服务器有时使用压缩 （gzip或deflate）以缩短传输花费的时间。分块传输编码可以用来分隔压缩对象的多个部分。在这种情况下，块不是分别压缩的，而是整个负载进行压缩，压缩的输出使用本文描述的方案进行分块传输。在压缩的情形中，分块编码有利于一边进行压缩一边发送数据，而不是先完成压缩过程以得知压缩后数据的大小。

  ​


## HTTP范围请求（断点续传）
一种网络中断可恢复机制，解决下载过程中网络中断后需要重头开始下载的问题。
例如只请求5001~10000字节内的资源：
```
GET /image.jpg HTTP/1.1
Host:www.test.com
Range:bytes = 5001-10000
```
这里用到首部字段Range，其他用法有：
从5001之后的全部字节：
```
Range:bytes=5001-
```
多重范围：从一开始到3000字节以及5000-7000字节
```
Range:bytes=-3001,5000-7000
```
针对范围请求，如果服务器端无法响应范围请求，那么便会返回状态码200 OK，和完整的实体内容。否则，便会返回状态码为**206 Partial Content**的响应报文：
```
HTTP/1.1 206 Partial Content
Date:Fri, 13 Jul 2014 04:39:17 GMT
Content-Range:bytes 5001-10000/10000
Content-Length:5000
Content-Type:image/jpeg
```
另外，对于多重范围的范围请求，响应会在首部字段Content-Type标明multipart/byteranges后返回响应报文。



## HTTP状态码

状态码由3位数字和原因短语组成，如200 OK
第一位数字指定了HTTP Response的类别，主要有5种：
* 1 信息，接收的请求正在处理
* 2 成功，请求正常处理完毕
* 3 重定向，需要进行附加操作以完成请求
* 4 客户端错误，服务器无法处理请求
* 5 服务器端错误，服务器处理请求出错

HTTP状态码的数量在60个以上（RFC2616，4918，5842），但是常用的通常是下面这十几种：
* 200 OK 

* 204 No Content  # 一般用在只需要从客户端往服务器端发送信息，而服务器端不需要发送新信息的场景

* 206 Partial Content 

* 301 Moved Permanently  # 永久性重定向，表示请求的资源已经被分配了新的URI（具体见Location首部字段）

* 302 Found  # 临时性重定向

* 303 See Other  # 表示由于请求的资源存在另一个URI，应使用GET方法定向获取请求的资源

* 304 Not Modified  # 当客户端发送附带条件的请求（If-Match、If-Modified-Since等）时，未满足条件的情况下返回。304的返回将不包含任何响应的主体部分。

* 307 Temporary Redirect  # 临时重定向，该状态码与302有着相同的含义

* 400 Bad Request # 表示请求报文中存在语法错误

* 401 Unauthorized  # 表示发送的请求需要有通过HTTP认证（BASIC或者DIGEST）的认证信息，如果浏览器是第1次接收到401响应，会弹出认证用的对话框。如果之前已进行过1次请求，则表示用户认证失败。

* 403 Forbidden  # 访问被拒绝

* 404 Not Found  # 服务器上没有请求的资源

* 500 Internal Server Error 

* 503 Service Unavailable  # 服务器正忙，一般会返回Retry-After字段

  ​

## HTTP首部字段举例
**通用首部字段**
* Cache-Control 通过一系列指令来控制缓存；
* Connection 控制不再转发给代理的首部字段以及管理持久链接（Connection:close）；
* Date 表明创建HTTP报文的日期和时间；
* Pragma 历史遗留字段，被Cache-Control:no-cache取代
* Trailer 用来事先说明在报文主体后记录了哪些首部字段，常用于HTTP分块传输编码的场景；
* Transfer-Encoding 规定了传输报文主体时采用的编码方式，仅对分块传输编码有效；
* Upgrade 用于检测HTTP协议或者其他协议是否可使用更高的版本进行通信，其值可以用来指定一个完全不同的通信协议。其作用仅限于客户端和相邻服务器之间，因此还需要配合使用Connection:Upgrade。对于附有首部字段Upgrade的请求，服务器用101 Switching Protocols状态码作为相应返回；
* Via 在经过代理时附加该首部字段，表名代理服务器的信息（HTTP协议版本、域名、服务器版本等）；
* Warning 用来告知用户一些与缓存相关的问题的警告，定义了7种警告供参考；

**请求首部字段**
* Accept 用来告知服务器客户端所能够处理的媒体类型及其相对优先级，可以一次指定多种媒体类型及各个类型的权重(Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8)
* Accept-Charset 告知能够处理的字符集及其相对优先级；
* Accept-Encoding 告知能够处理的内容编码及其相对优先级；
* Accept-Language 告知能够处理的自然语言集及其相对优先级；
* Authorization 告知服务器用户代理的认证信息（证书值），通常是在收到401的返回码后，把Authorization字段加入请求中，再次请求；
* Expect 告知所期望的扩展，如果服务器端不能满足期望，则会返回417 Expection Failed；
* From 告知服务器，用户的邮箱地址，如From:winstonwu@tencent.com；
* Host 多个虚拟主机可能运行在同一个IP上，这时候应该使用首部字段Host（域名+端口号）加以区分。若服务器未设Host，那么直接发送一个空值即可Host:。Host是HTTP/1.1规范内唯一一个必须被包含在请求内的首部字段；
* If-Match 只有当If-Match的字段值和ETag值匹配一致时，服务器才会接受请求。否则返回412 Precondition Failed；
* ETag 实体标记，是与特定资源关联的确定值。资源更新后ETag也会随之更新。
  假如用*来指定If-Match的值，服务器将会忽略ETag的值，只要资源存在就处理请求；
* If-Modified-Since 如果在If-Modified-Since字段指定的日期之后资源发生了更新，服务器会接受请求，否则返回304 Not Modified；
* If-None-Match 与If-Match字段的作用相反，只有在If-None-Match的值与ETag值不一致的时候，可以处理该请求；
* If-Range 告知服务器，若指定的If-Range字段值（ETag值或者时间）和所请求的资源的ETag值或时间相一致，则做范围请求，反之则返回整个资源；
* If-Unmodified-Since 和If-Modified-Since的作用相反；
* Max-Forwards 指定可通过的服务器的最大数目。通过TRACE或OPTIONS方法发送包含Max-Forwards首部字段的请求时，服务器在往下一个服务器转发请求之前，会将Max-Forwards的值减1后重新赋值。当服务器接收到Max-Forwards值为0的请求时，则不再进行转发，而是直接返回响应；
* Proxy-Authorization 用于告知代理服务器认证所需要的信息；
* Range 用于范围请求的指定范围。若服务器可以处理范围请求，则返回206，若无法处理，则返回200及整个资源；
* Referer 告知服务器请求的原始资源的URI，当直接在浏览器的地址栏输入URI或者出于安全性考虑时，也可以不发送该首部字段；
* TE 与Accept-Encoding功能类型，但是用于传输编码；
* User-Agent 将创建请求的浏览器和用户代理名称等信息传达给服务器；

**响应首部字段**
* Accept-Ranges 告知客户端，服务器是否能处理范围请求；
* Age 告知客户端，源服务器在多久之前创建了响应，单位为秒；
* ETag 资源实体标识，将资源以字符串形式做唯一标识，算法取决于服务器端；
* Location 提供重定向的URI；
* Proxy-Authenticate 把代理服务器所要求的认证信息发送给客户端；
* Retry-After 告知客户端多久以后再次发出请求，主要配合503 Service Unavailable或者3XX Rediect响应一起使用，字段值可以指定为具体的日期时间，也可以是创建响应后的秒数；
* Server 告知客户端当前服务器上安装的HTTP服务器程序的信息；
* Vary 用于缓存控制；
* WWW-Authenticate 用于HTTP访问认证，告知客户端适用于访问请求URI所指定资源的认证方案和带参数提示的质询。状态码为401的响应中，肯定带有首部字段WWW-Authenticate；

**实体首部字段**
* Allow 用于通知客户端服务器所能接收的HTTP请求方法。当服务器接收到不支持的HTTP方法时，会以状态码405 Method Not Allowed作为响应返回，同时还会把所有能支持的HTTP方法都写入首部字段Allow中返回；
* Content-Encoding 告知客户端，服务器对实体的主体部分所选用的内容编码方式；
* Content-Language 告知客户端，实体主体所使用的自然语言；
* Content-Length 告知客户端，实体主体部分的大小，对实体主体部分进行内容编码传输时，不能再使用Content-Length首部字段。实体主体大小的计算方法略复杂；
* Content-Location 报文主体部分对应的URI，主要用于返回内容和实际请求的对象不同的场景；
* Content-MD5 对报文的主体执行MD5算法后获得128位二进制数，再通过Base64编码后将结果写入Content-MD5字段值；
* Content-Range 用于范围请求的返回；
* Content-Type 实体主体内对象的媒体类型；
* Expires 将资源失效的日期告知客户端，当源服务器不希望缓存服务器对资源进行缓存时，可在Expires字段内写入与首部字段Date相同的时间值。当首部字段Cache-Control有指定max-age指令时，会优先处理max-age而忽略Expires。
* Last-Modified 指明资源最终修改的时间；

**为Cookie服务的首部字段**
* Set-Cookie 响应首部字段，设置Cookie到本地；
* Cookie 请求首部字段，带上本地Cookie；

**其他首部字段**
* X-Frame-Options 用于控制网站内容在其他Web网站的Frame标签内的显示问题；
* X-XSS-Protection 用于控制浏览器XSS防护机制的开关；
* DNT Do not track，是表示拒绝被精准广告追踪的一种方法；
* P3P 让Web网站上的个人隐私变成一种仅供程序可理解的形式；




## HTTPS协议工作过程

HTTP协议使用明文方式发送报文，本身不具备加密功能，不验证通信双方的身份，也无法验证报文的完整性，因此存在窃听、身份伪装和内容篡改等安全问题。
```
HTTPS = HTTP + 加密 + 认证 + 完整性保护
```
用SSL建立安全通信线路后，就可以在这条线路上进行HTTP通信，与SSL（或者TLS）组合使用的HTTP被称为HTTPS。
HTTPS的通信步骤：
* 建立TCP连接
* （1）客户端---Handshake:ClientHello--->服务器，开始SSL通信，报文内容包括支持的SSL版本，加密组件列表等信息；
* （2）客户端<---Handshake:ServerHello---服务器，服务器端应答，报文内容包括支持的SSL版本，加密组件列表等信息；
* （3）客户端<---Handshake:Certificate---服务器，Certificate报文中包含公开密钥证书；
* （4）客户端<---Handshake:ServerHelloDone---服务器，SSL握手协商结束；
* （5）客户端---Handshake:ClientKeyExchange--->服务器，报文内容包含一个随机密码串，且使用第（3）步中的公钥进行加密；
* （6）客户端---ChangeCipherSpec--->服务器，提示服务器，此后的报文通信会采用Pre-master secret密钥加密；
* （7）客户端---Handshake:Finished--->服务器，Finish报文，会包含连接至今全部报文整体校验值；
* （8）客户端<---ChangeCipherSpec---服务器；
* （9）客户端<---Handshake:Finished---服务器；
* SSL链接建立完成，之后进行应用层协议的通信，应用层发送数据时会附加一种叫做MAC（Message Authentication Code）的报文摘要，能够查知报文是否遭到篡改，从而保护报文的完整性；
* （10）客户端---Application Data(HTTP)--->服务器；
* （11）客户端<---Application Data(HTTP)---服务器；
* （12）客户端---Alert:warning,close notify--->服务器，由客户端断开SSL链接；
* 断开TCP连接



## HTTP的扩展协议

* SPDY Google于2010年发布，目标在于解决HTTP的性能瓶颈，缩短Web页面的加载时间（50%）。SPDY以会话层的形式加入，控制对数据的流动，但还是采用HTTP建立通信连接，因此可照常使用HTTP的GET和POST等方法、Cookie以及HTTP报文等：
```
HTTP		应用层
SPDY		会话层
SSL			表示层
TCP			传输层
```
* WebSocket 使用浏览器进行全双工通信。提供API，可供Javascript调用；
* HTTP/2.0 仍在讨论中；
* WebDAV 一个可对Web服务器上的内容直接进行文件复制、编辑等操作的分布式文件系统；



## HTTP长连接的实现条件

持久链接也称长连接，它本身是TCP通信的一种普通方式，即在一次TCP链接中持续发送多份数据而不断开连接，与它相反的方式称为短连接，短连接每次发送数据都需要建立新的TCP连接。
HTTP长连接的实现需要浏览器和Web服务器的共同协作，一方面浏览器需要保持一个TCP连接并重复利用（表现在HTTP头中的Connection:Keep-Alive），不断地发送多个请求，另一方面，服务器不能过早地主动关闭连接。
浏览器和Web服务器各自的长连接超时时间的设置可能不一致，所以在实际运行中是以最短的超时时间为准。



## NFS协议理解

共享文件系统的意义并不是一个磁盘文件系统，并不能用于存储和管理磁盘数据，而只是定义了文件在网络传输过程中的组织格式和传输协议。<u>一个文件从网络一端到达另一端的过程中需要进行两次格式转换，分别发生在进入网络和离开网络的时候。</u>

NFS并没有设计自己的传输协议，而是基于RPC协议，工作在应用层，负责客户端和服务器端之间请求与相应数据的传输控制。NFS服务器端和客户端软件包一般在Linux中默认安装。
```
vi /etc/exports  /data  10.0.1.201(rw,sync)  # 将本机的/data目录共享给10.0.1.201
/etc/init.d/nfsserver startStarting kernel based NFS server done # 启动Server
```
然后需要在目标机器上执行mount操作，将共享的目录绑定到自己的文件系统中：
```
mount -t nfs 10.0.1.200:/data /mnt/data
```

NFS服务器采用多进程模型。
通过nfsstat查看统计信息等，略。
共享文件系统只能同时为很少的服务器提供文件共享服务，其本身就是一个不强调扩展性的概念。



## 网络结构分层及各层关键概念

* **物理层**：数据、信号（模拟、数字）、二元制调制方法（调幅、调频、调相）、信道复用技术（频分、时分、波分、码分）；
* **数据链路层**：三个基本问题（封装成帧、透明传输、差错检测）、帧检验序列FCS、循环冗余检验CRC、点对点协议PPP、CSMA/CD、集线器、MAC帧、交换机；
* **网络层**：IP（三类地址、划分子网）、ARP、RARP、ICMP、IGMP、RIP、OSPF、BGP、VPN、NAT；
* **运输层**：TCP（缓存、流量控制、拥塞避免）、UDP；
* **应用层**：DNS（域名）、FTP、NFS、TELNET、HTTP、CGI、SMTP、POP3、IMAP、DHCP；



## 以太网最短有效帧长

以太网取 51.2 微秒为争用期的长度。
对于 10 Mb/s 以太网，在争用期内可发送512 bit，即 64 字节。
以太网在发送数据时，若前 64 字节没有发生冲突，则后续的数据就不会发生冲突。
<u>如果发生冲突，就一定是在发送的前 64 字节之内</u>。 由于一检测到冲突就立即中止发送，这时已经发送出去的数据一定小于 64 字节。 以太网规定了最短有效帧长为 64 字节，凡长度小于 64 字节的帧都是由于冲突而异常中止的无效帧。




## TCP报文格式
![image](/images/tech/tlpi_29.png)



## TCP状态迁移图

TCP结点以状态机的方式来建模，状态迁移图如下
![image](/images/tech/tlpi_30.png)



## SSL/TLS协议运行机制

目前，应用最广泛的是TLS 1.0，接下来是SSL 3.0。但是，主流浏览器都已经实现了TLS 1.2的支持。
TLS 1.0通常被标示为SSL 3.1，TLS 1.1为SSL 3.2，TLS 1.2为SSL 3.3。
SSL/TLS协议的基本思路是采用公钥加密法，也就是说，<u>客户端先向服务器端索要公钥，然后用公钥加密信息，服务器收到密文后，用自己的私钥解密</u>。主要需要解决两个问题：
* 如何保证公钥不被篡改？
  将公钥放在数字证书中。只要证书是可信的，公钥就是可信的。
* 公钥加密计算量太大，如何减少耗用的时间？
  每一次对话（session），客户端和服务器端都生成一个"对话密钥"（session key），用它来加密信息。由于"对话密钥"是对称加密，所以运算速度非常快，而服务器公钥只用于加密"对话密钥"本身，这样就减少了加密运算的消耗时间。

基本过程：
* 1.客户端向服务器端索要并验证公钥。
* 2.双方协商生成"对话密钥"。
* 3.双方采用"对话密钥"进行加密通信。






