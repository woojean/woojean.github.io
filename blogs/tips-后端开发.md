# 后端开发知识点总结

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

## 跨域登录同步
用户在bbb.com上已经登陆，现在要去aaa.com上玩，但在aaa.com域名下暂未登录。需要访问的aaa.com/resource.html资源需要登录才能访问。两个网站是同一套会员体系，同一个公司的。这是要让用户体验上做到用户在aaa.com上玩也能识别出登录状态（而不是要登录2次）。
第一步：用户向aaa.com发起get请求，获取resource.html资源，aaa.com发现用户未登录，返回302状态和外部重定向url:j.bbb.com?target=www.aaa.com/resource.html，注意j.bbb.com子域名上部署的应用可以认为是专门用于跨域同步。
第二步：用户根据重定向url，访问j.bbb.com?target=www.aaa.com/resource.html，由于在bbb.com上已经登录，所以bbb.com上能拿到从client端传递过来cookie信息。子域j.bbb.com上的应用负责将cookie读取出来，并作为参数再次重定向到
p.aaa.com?tartet=www.aaa.com/resource.html&sessionid=xxx&loginId=xxx&……  
 	第三步：用户根据第二步重定向url，访问p.aaa.com。p.aaa.com子域名（可以理解为该子域名用来处理aaa.com域名下的登录逻辑）上的应用专门负责根据请求参数里的参数对，往aaa.com域写入cookie（改变登录状态），并重定向到用户第一步请求的url。
第四步：经过前三步，已经完成了在aaa.com域名下同步bbb.com的登录状态，用户再次请求aaa.com/resource.html，这时就能成功访问了。

## HTTP缓存控制
HTTP所控制的缓存主要基于浏览器缓存，以及缓存代理服务器来实现。主要涉及以下`6个HTTP Header`：
`Expires`、`Cache-Control Header`、`Last-Modified`、`If-Modified-Since`、`ETag`、`If-None-Match`。

Expires/Cache-Control Header是控制浏览器是否直接从浏览器缓存取数据还是重新发请求到服务器取数据。只是Cache-Control比Expires可以控制的多一些，而且`Cache-Control会重写Expires的规则`。“Cache-control”常见的取值有private、no-cache、max-age、must-revalidate等。如果指定cache-control的值为private、no-cache、must-revalidate，那么打开新窗口访问时都会重新访问服务器。而如果指定了max-age值，那么在此值内的时间里就不会重新访问服务器，例如：Cache-control: max-age=5表示当访问此网页后的5秒内再次访问不会去服务器

Last-Modified/If-Modified-Since和ETag/If-None-Match是浏览器`发送请求到服务器后`判断文件是否已经修改过，如果没有修改过就只发送一个304回给浏览器，告诉浏览器直接从自己本地的缓存取数据；如果修改过那就整个数据重新发给浏览器。

Expires和Cache-Control max-age的区别与联系：
（1）Expires在HTTP/1.0中已经定义，Cache-Control:max-age在HTTP/1.1中才有定义。
（2）Expires指定一个绝对的过期时间(GMT格式)，这么做会导致至少2个问题：1）客户端和服务器时间不同步导致Expires的配置出现问题。2）很容易在配置后忘记具体的过期时间，导致过期来临出现浪涌现象；max-age 指定的是从文档被访问后的存活时间，这个时间是个相对值，相对的是文档第一次被请求时服务器记录的Request_time(请求时间)
（3）Expires指定的时间可以是相对文件的最后访问时间(Atime)或者修改时间(MTime)，而max-age相对对的是文档的请求时间(Atime)
（4）在Apache中，max-age是根据Expires的时间来计算出来的max-age = expires- request_time:(mod_expires.c)

Last-Modified/If-Modified-Since和ETag/If-None-Match工作方式
第一种，浏览器把缓存文件的最后修改时间通过If-Modified-Since来告诉Web服务器（初次请求时不带这个头）。（其实浏览器缓存里存储的不只是网页文件，还有服务器发过来的该文件的最后服务器修改时间）。服务器会把这个时间与服务器上实际文件的最后修改时间进行比较。如果时间一致，那么返回HTTP状态码304（不返回文件内容），客户端接到之后，就直接把本地缓存文件显示到浏览器中。如果时间不一致，就返回HTTP状态码200和新的文件内容，客户端接到之后，会丢弃旧文件，把新文件缓存起来，并显示到浏览器中（当文件发生改变，或者第一次访问时，服务器返回的HTTP头标签中有Last-Modified，告诉客户端页面的最后修改时间）。
第二种，浏览器把缓存文件的ETag，通过If-None-Match，来告诉Web服务器。思路与第一种类似。

一个例子：
Request Headers
Host localhost
User-Agent Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.8.1.16) Gecko/20080702 Firefox/2.0.0.16
Accept text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5
...
`If-Modified-Since` Tue, 19 Aug 2008 06:49:35GMT
`If-None-Match` 7936caeeaf6aee6ff8834b381618b513
`Cache-Control` max-age=0

Response Headers
Date Tue, 19 Aug 2008 06:50:19 GMT
...
`Expires` Tue, 19 Aug 2008 07:00:19 GMT
`Last-Modified` Tue, 19 Aug 2008 06:49:35GMT
`Etag` 7936caeeaf6aee6ff8834b381618b513

对应以上两组缓存控制Header，按F5刷新浏览器和在地址栏里输入网址然后回车。这两个行为是不一样的。按F5刷新浏览器，浏览器会去Web服务器验证缓存。如果是在地址栏输入网址然后回车，浏览器会直接使用有效的缓存，而不会发http request去服务器验证缓存，这种情况叫做`缓存命中`。
Cache-Control: public 指可以`公有缓存`，可以是数千名用户共享的。
Cache-Control: private 指只支持`私有缓存`，私有缓存是单个用户专用的。
此外，针对不同的Cache-Control值，对浏览器执行不同的操作，其缓存访问行为也不一样，这些操作包括：打开新窗口、在地址栏回车、按后退按钮、按刷新按钮。


## HTTP Referer头的安全问题
Referer是HTTP协议中的一个请求报头，`用于告知服务器用户的来源页面`。比如说从Google搜索结果中点击进入了某个页面，那么该次HTTP请求中的Referer就是Google搜索结果页面的地址。如果某篇博客中引用了其他地方的一张图片，那么对该图片的HTTP请求中的Referer就是你那篇博客的地址。
一般Referer主要用于统计，像CNZZ、百度统计等可以通过Referer统计访问流量的来源和搜索的关键词（包含在URL中）等等，方便站长们有针性对的进行推广和SEO。

Referer另一个用处就是`防盗链`。可以用referrer-killer（一个js库）来实现反反盗链。

Referer是由浏览器自动加上的，`以下情况是不带Referer的`
（1）直接输入网址或通过浏览器书签访问
（2）使用JavaScript的Location.href或者是Location.replace()
（3）HTTPS等加密协议

Referer的安全问题：以新浪微博曾经的一个漏洞（新浪微博`gsid劫持`）为例
gsid是一些网站移动版的认证方式，移动互联网之前较老的手机浏览器不支持cookie，为了能够识别用户身份（实现类似cookie的作用），就在用户的请求中加入了一个类似“sessionid”的字符串，通过GET方式传递，带有这个id的请求，就代表你的帐号发起的操作。后来又因用户多次认证体验不好，gsid的失效期是很长甚至永久有效的（即使改了密码也无用哦，这个问题在很多成熟的web产品上仍在发生）。也就是说，一旦攻击者获取到了这个gsid，就等同于长期拥有了你的身份权限，对你的帐号做任意操作。
gsid这个非常重要的参数竟然就在URL里，只要攻击者在微博上给你发一个链接（指向攻击者的服务器），你通过手机点击进入之后，手机当前页面的URL就通过Referer主动送到了攻击者的服务器上，攻击者自然就可以轻松拿到你的gsid进而控制你的账号。

## OAuth 2.0授权方式
OAuth是一个关于授权（authorization）的开放网络标准，目前的版本是2.0版。
OAuth的作用就是让"客户端"（第三方应用）安全可控地获取"用户"的授权，与"服务商提供商"（平台，比如微信）进行互动。
OAuth在"客户端"与"服务提供商"之间，设置了一个授权层（authorization layer）。"客户端"不能直接登录"服务提供商"，只能登录授权层，以此将用户与客户端区分开来。"客户端"登录授权层所用的令牌（token），与用户的密码不同。用户可以在登录的时候，指定授权层令牌的权限范围和有效期。"客户端"登录授权层以后，"服务提供商"根据令牌的权限范围和有效期，向"客户端"开放用户储存的资料。

OAuth 2.0的运行流程如下图：
![image](https://github.com/woojean/woojean.github.io/blob/master/images/net_7.png)	

客户端的授权模式（步骤B）
OAuth 2.0定义了四种授权方式：
（1）`授权码模式`（authorization code） 适用于有server端的应用授权
是功能最完整、流程最严密的授权模式。它的特点就是通过客户端的后台服务器，与"服务提供商"的认证服务器进行互动。
（A）用户访问客户端，后者将前者导向认证服务器。
（B）用户选择是否给予客户端授权。
（C）假设用户给予授权，认证服务器将用户导向客户端事先指定的"重定向URI"（redirection URI），同时附上一个授权码。
（D）客户端收到授权码，附上早先的"重定向URI"，向认证服务器申请令牌。这一步是在客户端的后台的服务器上完成的，对用户不可见。
（E）认证服务器核对了授权码和重定向URI，确认无误后，向客户端发送访问令牌（access token）和更新令牌（refresh token）。
即：用一个URI去申请，获得用户授权后得到一个对应该URI的授权码。之后就可以用该URI+对应的授权码来获取一个令牌，之后就可以使用该令牌来通过授权层。

（2）`隐式授权`（implicit）	适用于通过客户端访问的应用授权
不通过第三方应用程序的服务器，直接在浏览器中向认证服务器申请令牌，跳过了"授权码"这个步骤，因此得名。所有步骤在浏览器中完成，令牌对访问者是可见的，且客户端不需要认证。
（A）客户端将用户导向认证服务器。
（B）用户决定是否给于客户端授权。
（C）假设用户给予授权，认证服务器将用户导向客户端指定的"重定向URI"，并在URI的Hash部分包含了访问令牌。
（D）浏览器向资源服务器发出请求，其中不包括上一步收到的Hash值。
（E）资源服务器返回一个网页(typically an HTML document with an embedded script)，其中包含的代码可以获取Hash值中的令牌。
（F）浏览器执行上一步获得的脚本，提取出令牌。
（G）浏览器将令牌发给客户端（客户端就可以凭借此令牌来获取数据）。
实例：

其中短暂停留的那个页面的url为：
https://www.zhihu.com/oauth/callback/login/qqconn?code=680726D150FF0B9DF2EBBE2EFEEEC0D4&state=7f13b99dc94e506e69ecb9ec83296eec
页面效果：
![image](https://github.com/woojean/woojean.github.io/blob/master/images/net_8.png)
页面代码：
![image](https://github.com/woojean/woojean.github.io/blob/master/images/net_9.png)


（3）`密码模式`（resource owner password credentials）
用户向客户端提供自己的用户名和密码。客户端使用这些信息，向"服务商提供商"索要授权。这通常用在用户对客户端高度信任的情况下。

（4）`客户端模式`（client credentials）
指客户端以自己的名义，而不是以用户的名义，向"服务提供商"进行认证。严格地说，客户端模式并不属于OAuth框架所要解决的问题。在这种模式中，用户直接向客户端注册，客户端以自己的名义要求"服务提供商"提供服务，其实不存在授权问题。

# 网络编程基本模型
所有的网络应用都是基于相同的基本编程模型，有着相似的整体逻辑结构，并且依赖相同的编程接口。每个网络应用都是基于客户端-服务器模型的。一个应用是由一个服务器进程和一个或多个客户端进程组成。
	客户端-服务器模型中的基本操作是事务，一个客户端-服务器事务由四步组成：
![image](https://github.com/woojean/woojean.github.io/blob/master/images/basic_1.png)
注意：客户端和服务器是进程，而不是机器或者主机。
客户端和服务器端通过在“连接”上发送和接收字节流来通信。套接字是“连接”的端点。套接字=地址：端口。
	当客户端发起一个连接请求时，客户端套接字地址中的端口是由内核自动分配的，称为临时端口。然而服务器套接字地址中的端口通常是某个知名的端口，是和服务对应的。在Unix机器上，文件etc/services包含一张这台机器提供的服务以及它们的知名端口号的综合列表。
	套接字接口是一组用来结合Unix I/O函数创建网络应用的函数。大多数现代系统上都实现它，包括所有Unix变种、Windows、Macintosh系统。
![image](https://github.com/woojean/woojean.github.io/blob/master/images/basic_2.png)
套接字地址存放在类型为sockaddr_in的16字节结构中。对于因特网应用，sin_family成员是AF_INTE，sin_port成员是一个16位的端口号，而sin_addr成员就是一个32位的IP地址。IP地址和端口号总是以网络字节顺序（大端法）存放的。
![image](https://github.com/woojean/woojean.github.io/blob/master/images/basic_3.png)
客户端和服务器使用socket函数来创建一个套接字描述符：
![image](https://github.com/woojean/woojean.github.io/blob/master/images/basic_4.png)
如：clientfd = Socket( AF_INET, SOCK_STREAM, 0);
Socket返回的clientfd描述符仅是部分打开的，并且不能用于读写。如何完成打开套接字的工作，取决于我们是客户端还是服务器。
客户端通过调用connect函数来建立和服务器的连接：
![image](https://github.com/woojean/woojean.github.io/blob/master/images/basic_5.png)
connect函数试图与套接字地址为serv_addr的服务器建立一个因特网连接，其中addrlen是sizeof(sockaddr_in)。connect函数会阻塞，一直到连接成功建立或是发生错误，如果成功，sockfd描述符现在就准备好读写了，并且得到的连接是由套接字对：
(x:y, serv_addr.sin_addr:serv_addr.sin_port)刻画的。x，y分别表示客户端的IP地址和端口。
bind、listen、accept三个函数用来和客户端建立连接：
![image](https://github.com/woojean/woojean.github.io/blob/master/images/basic_6.png)
bind函数告诉内核将my_addr中的服务器套接字地址和套接字描述符sockfd联系起来。
默认情况下内核会认为socket函数创建的描述符对应于主动套接字，它存在于一个连接的客户端。服务器调用listen函数告诉内核，描述符是被服务器而不是客户端使用的。
![image](https://github.com/woojean/woojean.github.io/blob/master/images/basic_7.png)
listen函数将sockfd从一个主动套接字转化为一个监听套接字，该套接字可以接受来自客户端的连接请求。backlog参数暗示了内核在开始拒绝连接请求前应该放入队列中等待的未完成连接请求的数量，其确切含义要求对TCP/IP协议的理解。通常会被设置为一个较大的值，比如1024.
服务器通过调用accept函数来等待来自客户端的连接请求：
![image](https://github.com/woojean/woojean.github.io/blob/master/images/basic_8.png)
accept函数等待来自客户端的连接请求，默认会阻塞进程，直到有一个客户连接建立后返回，它返回的是一个新的可用套接字，即“连接套接字”。参数中的listenfd是“监听套接字”，addr是一个结果参数，它用来接受一个返回值，指定客户端的地址。addrlen也是结果参数。
如果accept成功返回，则服务器与客户端已经正确建立连接了，此时服务器通过accept返回的套接字来完成与客户的通信。
“监听套接字”是作为客户端连接请求的一个端点，它只被创建一次，并存在于服务器的整个生命周期。“连接套接字”是客户端和服务器之间已经建立起来的连接的一个端点，服务器每次接受请求时，都会创建一次，只存在于服务器为一个客户端服务的过程。


## SOCKET编程流程
基于TCP（面向连接）的socket编程流程：
	服务器端：
	①创建套接字；
	②将套接字绑定到一个本地地址和端口上；
	③将套接字设为监听模式，准备接收客户请求；
	④等待客户请求到来；当请求到来后接受连接请求，返回一个新的对应于此次连接的套接字；
	⑤用返回的套接字和客户端进行通信；
	⑥返回，等待另一客户请求；
	⑦关闭套接字；
	客户端：
	①创建套接字；
	②向服务器发出连接请求；
	③和服务器端进行通信；
	④关闭套接字；
基于UDP（面向无连接）的socket编程流程：
	服务器端：
	①创建套接字；
	②将套接字绑定到一个本地地址和端口上；
	③等待接收数据
	④关闭套接字；
	客户端：
	①创建套接字；
	②向服务器发送数据；
	③关闭套接字；


## select、poll、epoll
文件描述符（fd）：文件描述符是一个简单的整数，用以标明每一个被进程所打开的文件和socket的索引。第一个打开的文件是0，第二个是1，依此类推。最前面的三个文件描述符（0，1，2）分别与标准输入（stdin），标准输出（stdout）和标准错误（stderr）对应。Unix 操作系统通常给每个进程能打开的文件数量强加一个限制。当用完所有的文件描述符后，将不能接收用户新的连接，直到一部分当前请求完成，相应的文件和socket被关闭。

select，poll，epoll都是IO多路复用的机制。I/O多路复用通过一种机制，可以监视多个文件描述符，一旦某个描述符就绪（一般是读就绪或者写就绪），能够通知程序进行相应的读写操作。select，poll，epoll本质上都是同步I/O，因为他们都需要在读写事件就绪后自己负责进行读写，也就是说这个读写过程是阻塞的，而异步I/O则无需自己负责进行读写，异步I/O的实现会负责把数据从内核拷贝到用户空间。

epoll的改进：
（1）select，poll实现需要自己不断轮询所有fd集合，直到设备就绪，期间可能要睡眠和唤醒多次交替。而epoll其实也需要调用epoll_wait不断轮询就绪链表，期间也可能多次睡眠和唤醒交替，但是它是设备就绪时，调用回调函数，把就绪fd放入就绪链表中，并唤醒在epoll_wait中进入睡眠的进程。虽然都要睡眠和交替，但是select和poll在“醒着”的时候要遍历整个fd集合，而epoll在“醒着”的时候只要判断一下就绪链表是否为空就行了，这节省了大量的CPU时间。这就是回调机制带来的性能提升（本质的改进在于epoll采用基于事件的就绪通知方式）。
（2）select，poll每次调用都要把fd集合从用户态往内核态拷贝一次，并且要把current往设备等待队列中挂一次，而epoll只要一次拷贝，而且把current往等待队列上挂也只挂一次（在epoll_wait的开始，注意这里的等待队列并不是设备等待队列，只是一个epoll内部定义的等待队列）。（另一个本质的改进就是使用了内存映射（mmap）技术）

epoll被公认为Linux2.6下性能最好的多路I/O就绪通知方法，实现高效处理百万句柄。


## IO阻塞、非阻塞、同步、异步
同步和异步
同步和异步是针对应用程序和内核的交互而言的，同步指的是用户进程触发I/O操作并等待或者轮询的去查看I/O操作是否就绪，而异步是指用户进程触发I/O操作以后便开始做自己的事情，而当I/O操作已经完成的时候会得到I/O完成的通知。

阻塞和非阻塞
阻塞和非阻塞是针对于进程在访问数据的时候，根据I/O操作的就绪状态来采取的不同方式，是一种读取或者写入函数的实现方式，阻塞方式下读取或者写入函数将一直等待，而非阻塞方式下，读取或者写入函数会立即返回一个状态值。

服务器端有以下几种IO模型：
（1）阻塞式模型（blocking IO）
大部分的socket接口都是阻塞型的（ listen()、accpet()、send()、recv() 等）。阻塞型接口是指系统调用（一般是 IO 接口）不返回调用结果并让当前线程一直阻塞，只有当该系统调用获得结果或者超时出错时才返回。在线程被阻塞期间，线程将无法执行任何运算或响应任何的网络请求，这给多客户机、多业务逻辑的网络编程带来了挑战。
![image](https://github.com/woojean/woojean.github.io/blob/master/images/basic_9.png)
（2）多线程的服务器模型（Multi-Thread）
应对多客户机的网络应用，最简单的解决方式是在服务器端使用多线程（或多进程）。多线程（或多进程）的目的是让每个连接都拥有独立的线程（或进程），这样任何一个连接的阻塞都不会影响其他的连接。但是如果要同时响应成千上万路的连接请求，则无论多线程还是多进程都会严重占据系统资源，降低系统对外界响应效率。
在多线程的基础上，可以考虑使用“线程池”或“连接池”，“线程池”旨在减少创建和销毁线程的频率，其维持一定合理数量的线程，并让空闲的线程重新承担新的执行任务。“连接池”维持连接的缓存池，尽量重用已有的连接、减少创建和关闭连接的频率。这两种技术都可以很好的降低系统开销，都被广泛应用很多大型系统。

（3）非阻塞式模型（Non-blocking IO）
相比于阻塞型接口的显著差异在于，在被调用之后立即返回。
![image](https://github.com/woojean/woojean.github.io/blob/master/images/basic_10.png)
需要应用程序调用许多次来等待操作完成。这可能效率不高，因为在很多情况下，当内核执行这个命令时，应用程序必须要进行忙碌等待，直到数据可用为止。
另一个问题，在循环调用非阻塞IO的时候，将大幅度占用CPU，所以一般使用select等来检测”是否可以操作“。

（4）多路复用IO（IO multiplexing）
支持I/O复用的系统调用有select、poll、epoll、kqueue等。使用Select返回后，仍然需要轮训再检测每个socket的状态（读、写），这样的轮训检测在大量连接下也是效率不高的。因为当需要探测的句柄值较大时，select () 接口本身需要消耗大量时间去轮询各个句柄。
很多操作系统提供了更为高效的接口，如 linux 提供 了 epoll，BSD 提供了 kqueue，Solaris 提供了 /dev/poll …。如果需要实现更高效的服务器程序，类似 epoll 这样的接口更被推荐。
![image](https://github.com/woojean/woojean.github.io/blob/master/images/basic_11.png)

（5）使用事件驱动库libevent的服务器模型
libevent是一个事件触发的网络库，适用于windows、linux、bsd等多种平台，内部使用select、epoll、kqueue、IOCP等系统调用管理事件机制。著名分布式缓存软件memcached也是基于libevent，而且libevent在使用上可以做到跨平台。
libevent 库提供一种事件机制，它作为底层网络后端的包装器。事件系统让为连接添加处理函数变得非常简便，同时降低了底层IO复杂性。这是 libevent 系统的核心。
创建 libevent 服务器的基本方法是，注册当发生某一操作（比如接受来自客户端的连接）时应该执行的函数，然后调用主事件循环 event_dispatch()。执行过程的控制现在由 libevent 系统处理。注册事件和将调用的函数之后，事件系统开始自治；在应用程序运行时，可以在事件队列中添加（注册）或 删除（取消注册）事件。事件注册非常方便，可以通过它添加新事件以处理新打开的连接，从而构建灵活的网络处理系统。

（6）信号驱动IO模型（Signal-driven IO）
让内核在描述符就绪时发送SIGIO信号通知应用程序。
![image](https://github.com/woojean/woojean.github.io/blob/master/images/basic_12.png)

（7）异步IO模型（asynchronous IO）
告知内核启动某个操作，并让内核在整个操作（包括将数据从内核复制到我们自己的缓冲区）完成后通知我们。这种模型与信号驱动模型的主要区别在于：信号驱动式I/O是由内核通知我们何时可以启动一个I/O操作，而异步I/O模型是由内核通知我们I/O操作何时完成。
![image](https://github.com/woojean/woojean.github.io/blob/master/images/basic_13.png)

同步和异步IO的区别：
A synchronous I/O operation causes the requesting process to be blocked until that I/O operation completes;
An asynchronous I/O operation does not cause the requesting process to be blocked; 
两者的区别就在于synchronous IO做”IO operation”的时候会将process阻塞。按照这个定义阻塞、非阻塞、IO多路复用其实都属于同步IO。

非阻塞与异步IO的区别：在non-blocking IO中，虽然进程大部分时间都不会被block，但是它仍然要求进程去主动的check，并且当数据准备完成以后，也需要进程主动的再次调用recvfrom来将数据拷贝到用户内存。而asynchronous IO则完全不同。它就像是用户进程将整个IO操作（分为两步：准备数据、将数据从内核复制到用户空间）交给了他人（kernel）完成，然后他人做完后发信号通知。在此期间，用户进程不需要去检查IO操作的状态，也不需要主动的去拷贝数据。


## 套接字的类型
①流式套接字（SOCK_STREAM）：提供面向连接、可靠的数据传输服务，数据无差错、无重复的发送，且按发送顺序接收。流式套接字实际上是基于TCP协议实现的；
	②数据报式套接字（SOCK_DGRAM）：提供无连接服务。数据包以独立包形式发送，不提供无错保证，数据可能丢失或重复，并且接收顺序混乱。数据报式套接字实际上是基于UDP协议实现的。
	③原始套接字（SOCK_RAW）


## SOCKET函数
socket()
我们使用系统调用socket()来获得文件描述符：
int socket(int domain,int type,int protocol);
第一个参数domain设置为“AF_INET”。
第二个参数是套接口的类型：SOCK_STREAM或SOCK_DGRAM。
第三个参数设置为0。
系统调用socket()只返回一个套接口描述符，如果出错，则返回-1。 

bind()
一旦你有了一个套接口以后，下一步就是把套接口绑定到本地计算机的某一个端口上。但如果你只想使用connect()则无此必要。下面是系统调用bind()的使用方法：
int bind(int sockfd,struct sockaddr*my_addr,int addrlen);
第一个参数sockfd是由socket()调用返回的套接口文件描述符。
第二个参数my_addr是指向数据结构sockaddr的指针。数据结构sockaddr中包括了关于你的地址、端口和IP地址的信息。
第三个参数addrlen可以设置成sizeof(structsockaddr)。
下面是一个例子：
#define MYPORT 3490
main()
{
	int sockfd;
	struct sockaddr_in my_addr;
	sockfd=socket(AF_INET,SOCK_STREAM,0);	/*do someerror checking!*/
	my_addr.sin_family=AF_INET;	/*hostbyteorder*/
	my_addr.sin_port=htons(MYPORT);	/*short,network byte order*/
	my_addr.sin_addr.s_addr=inet_addr('132.241.5.10');
	bzero(&(my_addr.sin_zero),8);	/*zero the rest of the struct*/
	/*don't forget your error checking for bind():*/
	bind(sockfd,(struct sockaddr*)&my_addr,sizeof(struct sockaddr));
	...
	如果出错，bind()也返回-1。
	如果你使用connect()系统调用，那么你不必知道你使用的端口号。当你调用connect()时，它检查套接口是否已经绑定，如果没有，它将会分配一个空闲的端口。 
 
connect()
系统调用connect()的用法如下：
int connect(int sockfd,struct sockaddr* serv_addr,int addrlen);
第一个参数还是套接口文件描述符，它是由系统调用socket()返回的。
第二个参数是serv_addr是指向数据结构sockaddr的指针，其中包括目的端口和IP地址。
第三个参数可以使用sizeof(structsockaddr)而获得。
下面是一个例子：
#define DEST_IP '132.241.5.10'
#define DEST_PORT 23
main()
{
	intsockfd;
	struct sockaddr_in dest_addr;	/*will hold the destination addr*/
	sockfd=socket(AF_INET,SOCK_STREAM,0);	/*do some error checking!*/
	dest_addr.sin_family=AF_INET;	/*hostbyteorder*/
	dest_addr.sin_port=htons(DEST_PORT);/*short,network byte order*/
	dest_addr.sin_addr.s_addr=inet_addr(DEST_IP);
	bzero(&(dest_addr.sin_zero),8);/*zero the rest of the struct*/
		/*don'tforgettoerrorchecktheconnect()!*/
	connect(sockfd,(structsockaddr*)&dest_addr,sizeof(struct sockaddr));
	...
	同样，如果出错，connect()将会返回-1。 
 	
listen()
如果你希望不连接到远程的主机，也就是说你希望等待一个进入的连接请求，然后再处理它们。这样，你通过首先调用listen()，然后再调用accept()来实现。
系统调用listen()的形式如下：
int listen(int sockfd,int backlog);
第一个参数是系统调用socket()返回的套接口文件描述符。
第二个参数是进入队列中允许的连接的个数。进入的连接请求在使用系统调用accept()应答之前要在进入队列中等待。这个值是队列中最多可以拥有的请求的个数。大多数系统的缺省设置为20。你可以设置为5或者10。当出错时，listen()将会返回-1值。
当然，在使用系统调用listen()之前，我们需要调用bind()绑定到需要的端口，否则系统内核将会让我们监听一个随机的端口。
所以，如果你希望监听一个端口，下面是应该使用的系统调用的顺序：
	socket();
	bind();
	listen();
		/*accept()goes here*/ 
 
accept()
系统调用accept()比较起来有点复杂。在远程的主机可能试图使用connect()连接你使用listen()正在监听的端口。但此连接将会在队列中等待，直到使用accept()处理它。调用accept()之后，将会返回一个全新的套接口文件描述符来处理这个单个的连接。这样，对于同一个连接来说，你就有了两个文件描述符。原先的一个文件描述符正在监听你指定的端口，新的文件描述符可以用来调用send()和recv()。
调用的例子如下：
int accept(intsockfd,void*addr,int*addrlen);
第一个参数是正在监听端口的套接口文件描述符。
第二个参数addr是指向本地的数据结构
sockaddr_in的指针。调用connect()中的信息将存储在这里。通过它你可以了解哪个主机在哪个端口呼叫你。
第三个参数同样可以使用sizeof(structsockaddr_in)来获得。
如果出错，accept()也将返回-1。下面是一个简单的例子：
#define MYPORT 3490	/*theportuserswillbeconnectingto*/
#define BACKLOG 10/*howmanypendingconnectionsqueuewillhold*/
main()
{
	intsockfd,new_fd;/*listenonsock_fd,newconnectiononnew_fd*/
	struct sockaddr_in my_addr;/*myaddressinformation*/
	struct sockaddr_in their_addr;/*connector'saddressinformation*/
	int sin_size;
 	sockfd=socket(AF_INET,SOCK_STREAM,0);	/*dosomeerrorchecking!*/
	my_addr.sin_family=AF_INET;	/*hostbyteorder*/
	my_addr.sin_port=htons(MYPORT);	/*short,networkbyteorder*/
	my_addr.sin_addr.s_addr=INADDR_ANY;/*auto-fillwithmyIP*/
	bzero(&(my_addr.sin_zero),8);/*zerotherestofthestruct*/
		/*don'tforgetyourerrorcheckingforthesecalls:*/
	bind(sockfd,(structsockaddr*)&my_addr,sizeof(structsockaddr));
	listen(sockfd,BACKLOG);
	sin_size=sizeof(structsockaddr_in);
	new_fd=accept(sockfd,&their_addr,&sin_size);
	...
下面，我们将可以使用新创建的套接口文件描述符new_fd来调用send()和recv()。 
 
send() 和recv()
系统调用send()的用法如下：
int send(int sockfd,const void* msg,int len,int flags);
第一个参数是你希望给发送数据的套接口文件描述符。它可以是你通过socket()系统调用返回的，也可以是通过accept()系统调用得到的。
第二个参数是指向你希望发送的数据的指针。
第三个参数是数据的字节长度。第四个参数标志设置为0。
下面是一个简单的例子：
char* msg='Beejwashere!';
int len,bytes_sent;
..
len=strlen(msg);
bytes_sent=send(sockfd,msg,len,0);
...
系统调用send()返回实际发送的字节数，这可能比你实际想要发送的字节数少。如果返回的字节数比要发送的字节数少，你在以后必须发送剩下的数据。当send()出错时，将返回-1。
系统调用recv()的使用方法和send()类似：
int recv(int sockfd,void* buf,int len,unsigned int flags);
第一个参数是要读取的套接口文件描述符。
第二个参数是保存读入信息的地址。
第三个参数是缓冲区的最大长度。第四个参数设置为0。
系统调用recv()返回实际读取到缓冲区的字节数，如果出错则返回-1。
这样使用上面的系统调用，你可以通过数据流套接口来发送和接受信息。 
 
sendto() 和recvfrom()
因为数据报套接口并不连接到远程的主机上，所以在发送数据包之前，我们必须首先给出目的地址，请看：
int sendto(int sockfd,const void* msg,int len,unsigned int flags,
								conststruct sockaddr*to,inttolen);
除了两个参数以外，其他的参数和系统调用send()时相同。
参数to是指向包含目的IP地址和端口号的数据结构sockaddr的指针。
参数tolen可以设置为sizeof(structsockaddr)。
系统调用sendto()返回实际发送的字节数，如果出错则返回-1。
系统调用recvfrom()的使用方法也和recv()的十分近似：
int recvfrom(int sockfd,void* buf,int len,unsigned int flags
						struct sockaddr* from,int* fromlen);
参数from是指向本地计算机中包含源IP地址和端口号的数据结构sockaddr的指针。
参数fromlen设置为sizeof(struct sockaddr)。
系统调用recvfrom()返回接收到的字节数，如果出错则返回-1。 
 
close() 和shutdown()
你可以使用close()调用关闭连接的套接口文件描述符：
close(sockfd);
这样就不能再对此套接口做任何的读写操作了。
使用系统调用shutdown()，可有更多的控制权。它允许你在某一个方向切断通信，或者切断双方的通信：
int shutdown(int sockfd,int how);
第一个参数是你希望切断通信的套接口文件描述符。第二个参数how值如下：
0—Furtherreceivesaredisallowed
1—Furthersendsaredisallowed
2—Furthersendsandreceivesaredisallowed(likeclose())
shutdown()如果成功则返回0，如果失败则返回-1。 
 
getpeername()
这个系统的调用十分简单。它将告诉你是谁在连接的另一端：
int getpeername(int sockfd,struct sockaddr* addr,int* addrlen);
第一个参数是连接的数据流套接口文件描述符。
第二个参数是指向包含另一端的信息的数据结构sockaddr的指针。
第三个参数可以设置为sizeof(structsockaddr)。
如果出错，系统调用将返回-1。
一旦你获得了它们的地址，你可以使用inet_ntoa()或者gethostbyaddr()来得到更多的信息。
 
gethostname()
系统调用gethostname()比系统调用getpeername()还简单。它返回程序正在运行的计算机的名字。系统调用gethostbyname()可以使用这个名字来决定你的机器的IP地址。
下面是一个例子：
int gethostname(char*hostname,size_tsize);
如果成功，gethostname将返回0。如果失败，它将返回-1。 
•htonl()：把32位值从主机字节序转换成网络字节序 
•htons()：把16位值从主机字节序转换成网络字节序 
•ntohl()：把32位值从网络字节序转换成主机字节序 
•ntohs()：把16位值从网络字节序转换成主机字节序 
  
设置Socket缓冲区 
int zero = 0;
setsockopt( ov->m_Socket, SOL_SOCKET, SO_SNDBUF, (char *) &zero, sizeof zero );
setsockopt( ov->m_Socket, SOL_SOCKET, SO_RCVBUF, (char *) &zero, sizeof zero );


# 基础原理知识点总结

## 计算机网络的层级划分及每层的工作内容
OSI的七层协议：应用层、表示层、会话层、运输层、网络层、数据链路层、物理层；
TCP/IP 是四层的体系结构：应用层、运输层、网际层和网络接口层。但最下面的网络接口层并没有具体内容。
因此往往采取折中的办法，即综合 OSI 和 TCP/IP 的优点，采用一种只有五层协议的体系结构：
  应用层 ： FTP、TELNET、HTTP、SMTP、 POP3、DHCP、DNS
  运输层 ： TCP、UDP
  网络层 ： IP、ARP、ICMP、RIP、OSPF、路由器	# 虚拟互联网络
  数据链路层 ： PPP、CRC、FSC、CSMA/CD、交换机	# 封装成帧、 透明传输、差错控制
  物理层 ： ADSL、CDMA、集线器		# 物理信号传输（数、模）、信道复用（频分、时分、码分）



## TCP的Nagle算法
事实上，Nagle算法所谓的“提高网络利用率”只是它的一个副作用，`Nagle算法的主旨在于“避免发送‘大量’的小包”`。Nagle算法并没有阻止发送小包，它只是阻止了发送大量的小包！
TCP/IP协议中，无论发送多少数据，总是要在数据前面加上协议头，同时，对方接收到数据，也需要发送ACK表示确认。为了尽可能的利用网络带宽，TCP总是希望尽可能的发送足够大的数据。Nagle算法就是为了尽可能发送大块数据，避免网络中充斥着许多小数据块。
`Nagle算法的基本定义是任意时刻，最多只能有一个未被确认的小段`。 所谓“小段”，指的是小于MSS尺寸的数据块，所谓“未被确认”，是指一个数据块发送出去后，没有收到对方发送的ACK确认该数据已收到。Nagle的算法通常会在TCP程序里添加两行代码，在未确认数据发送的时候让发送器把数据送到缓存里。任何数据随后继续直到得到明显的数据确认或者直到攒到了一定数量的数据了再发包。
默认情况下，发送数据采用Nagle 算法。这样虽然提高了网络吞吐量，但是实时性却降低了，在一些交互性很强的应用程序来说是不允许的，使用TCP_NODELAY选项可以禁止Nagle 算法。

## Tcp连接的建立和断开的过程
TCP用三次握手建立连接：
![image](https://github.com/woojean/woojean.github.io/blob/master/images/net_1.png)
1.A的TCP向B发出连接请求报文段，其首部中的`同步位SYN = 1`，并选择`序号seq = x`，表明传送数据时的第一个数据字节的序号是 x。
2.B的TCP收到连接请求报文段后，如同意，则发回确认。B 在确认报文段中应使`SYN = 1`，使`ACK = 1`，其确认号`ack = x+1`，自己选择的序号`seq = y`。
3.A收到此报文段后向B给出确认，其`ACK = 1`，确认号`ack = y+1`。A 的 TCP 通知上层应用进程，连接已经建立。B 的 TCP 收到主机 A 的确认后，也通知其上层应用进程：TCP 连接已经建立。 

TCP连接的断开：
![image](https://github.com/woojean/woojean.github.io/blob/master/images/net_2.png)
1.数据传输结束后，通信的双方都可释放连接。现在 A 的应用进程先向其 TCP 发出连接释放报文段，并停止再发送数据，主动关闭 TCP 连接。A 把连接释放报文段首部的`FIN = 1`，其序号`seq = u`，等待 B 的确认。
2.B发出确认，确认号`ack = u+1`，而这个报文段自己的序号`seq = v`。TCP 服务器进程通知高层应用进程。从 A 到 B 这个方向的连接就释放了，TCP 连接处于`半关闭状态`。B 若发送数据，A 仍要接收。
3.若B已经没有要向 A 发送的数据，其应用进程就通知TCP释放连接。
4.A收到连接释放报文段后，必须发出确认。在确认报文段中`ACK = 1`，确认号`ack = w+1`，自己的序号`seq = u + 1`。

`注意`
### A必须等待2MSL的时间
第一，为了保证A发送的最后一个ACK报文段能够到达B。
第二，防止 “已失效的连接请求报文段”出现在本连接中。A在发送完最后一个ACK报文段后，再经过时间 2MSL，就可以使本连接持续的时间内所产生的所有报文段，都从网络中消失。这样就可以使下一个新的连接中不会出现这种旧的连接请求报文段。

### 双方同时启动关闭的情况
连接双方的应用进程同时发关闭命令，则双方TCP在发送完尚未处理的报文段后，发送FIN报文。各方TCP在FIN前所发报文都得到确认后，发 ACK确认它收到的FIN。各方在收到对方对FIN的确认后，同样等待一段时间再关闭连接。这称之为`同时关闭`（ simultaneous close ）。


## TCP的拥塞控制
`慢开始`：在主机刚刚开始发送报文段时可先将拥塞窗口 cwnd 设置为一个最大报文段 MSS 的数值。在每收到一个对新的报文段的确认后，将拥塞窗口增加至多一个 MSS 的数值。用这样的方法逐步增大发送端的拥塞窗口 cwnd，可以使分组注入到网络的速率更加合理。 

`拥塞避免`：当拥塞窗口值大于慢开始门限时，停止使用慢开始算法而改用拥塞避免算法。拥塞避免算法使发送端的拥塞窗口每经过一个往返时延RTT就增加一个MSS的大小。

`快重传`算法规定，发送端只要一连收到三个重复的 ACK 即可断定有分组丢失了，就应立即重传丢失的报文段而不必继续等待为该报文段设置的重传计时器的超时。

`快恢复`算法：
(1) 当发送端收到连续三个重复的 ACK 时，就重新设置慢开始门限 ssthresh。
(2) 与慢开始不同之处是拥塞窗口 cwnd 不是设置为 1，而是设置为 ssthresh + 3 *MSS。 
(3) 若收到的重复的 ACK 为 n 个（n > 3），则将 cwnd 设置为 ssthresh + n * MSS。
(4) 若发送窗口值还容许发送报文段，就按拥塞避免算法继续发送报文段。
(5) 若收到了确认新的报文段的 ACK，就将 cwnd 缩小到 ssthresh。

`“乘法减小“`是指不论在慢开始阶段还是拥塞避免阶段，只要出现一次超时（即出现一次网络拥塞），就把慢开始门限值 ssthresh 设置为当前的拥塞窗口值乘以 0.5。当网络频繁出现拥塞时，ssthresh 值就下降得很快，以大大减少注入到网络中的分组数。

`“加法增大”`是指执行拥塞避免算法后，当收到对所有报文段的确认就将拥塞窗口 cwnd增加一个 MSS 大小，使拥塞窗口缓慢增大，以防止网络过早出现拥塞。 

### 拥塞控制与流量控制的区别 
1.拥塞控制所要做的都有一个前提，就是网络能够承受现有的网络负荷。
2.拥塞控制是一个全局性的过程，涉及到所有的主机、所有的路由器，以及与降低网络传输性能有关的所有因素。 
3.流量控制往往指在给定的发送端和接收端之间的点对点通信量的控制。 
4.流量控制所要做的就是抑制发送端发送数据的速率，以便使接收端来得及接收。
流量控制(flow control)就是让发送方的发送速率不要太快，既要让接收方来得及接收，也不要使网络发生拥塞。利用滑动窗口机制可以很方便地在 TCP 连接上实现流量控制。



## TCP可靠通信的实现原理
1.TCP 连接的每一端都必须设有两个窗口——一个发送窗口和一个接收窗口。
2.TCP 的可靠传输机制用字节的序号进行控制。`TCP所有的确认都是基于序号`而不是基于报文段。
3.TCP 两端的四个窗口经常处于动态变化之中。
4.TCP连接的往返时间 RTT 也不是固定不变的。需要使用特定的算法估算较为合理的重传时间。


## 域名的解析过程
主机向本地域名服务器的查询一般都是采用递归查询。如果主机所询问的本地域名服务器不知道被查询域名的 IP 地址，那么本地域名服务器就以 DNS 客户的身份，向其他根域名服务器继续发出查询请求报文。
本地域名服务器向根域名服务器的查询通常是采用迭代查询。当根域名服务器收到本地域名服务器的迭代查询请求报文时，要么给出所要查询的 IP 地址，要么告诉本地域名服务器：“你下一步应当向哪一个域名服务器进行查询”。然后让本地域名服务器进行后续的查询。

## TCP端口
端口用一个 16 位端口号进行标志。`端口号只具有本地意义`，即端口号只是为了标志本计算机应用层中的各进程。
三类端口:
1.`熟知端口`，数值一般为 0~1023。
2.`登记端口号`，数值为1024~49151，为没有熟知端口号的应用程序使用的。使用这个范围的端口号必须在 IANA 登记，以防止重复。
3.`客户端口号或短暂端口号`，数值为49152~65535，留给客户进程选择暂时使用。当服务器进程收到客户进程的报文时，就知道了客户进程所使用的动态端口号。通信结束后，这个端口号可供其他客户进程以后使用。

## UDP的主要特点
UDP 只在 IP 的数据报服务之上增加了很少一点的功能，即`端口的功能和差错检测的功能`。虽然 UDP 用户数据报只能提供不可靠的交付，但 UDP 在某些方面有其特殊的优点。
1.UDP 是`无连接`的，即发送数据之前不需要建立连接。
2.UDP 使用`尽最大努力交付`，即不保证可靠交付，同时也不使用拥塞控制。
3.UDP 是`面向报文`的。UDP 没有拥塞控制，很适合多媒体通信的要求。 
4.UDP 支持`一对一、一对多、多对一和多对多`的交互通信。
5.UDP 的`首部开销小`，只有 8 个字节。

## TCP 最主要的特点
1.TCP 是`面向连接`的运输层协议。
2.每一条 TCP 连接`只能有两个端点`(endpoint)，每一条 TCP 连接只能是点对点的（一对一）。 
3.TCP 提供`可靠交付`的服务。
4.TCP 提供`全双工`通信。
5.面向字节流。

`注意`
1.TCP 连接是一条`虚连接`而不是一条真正的物理连接。
2.TCP 对应用进程一次把多长的报文发送到TCP 的缓存中是不关心的。
3.TCP 根据对方给出的窗口值和当前网络拥塞的程度来决定一个报文段应包含多少个字节（UDP 发送的报文长度是应用进程给出的）。
4.TCP 可把太长的数据块划分短一些再传送。TCP 也可等待积累有足够多的字节后再构成报文段发送出去。

网络通信中，write返回成功后，是否确保数据发送成功或是被对端服务收到？
不是，只是表明待发送数据已被写入系统缓存；


## TCP滑动窗口
TCP 把连接作为最基本的抽象。每一条 TCP 连接有两个端点。TCP 连接的端点不是主机，不是主机的IP 地址，不是应用进程，也不是运输层的协议端口。TCP 连接的端点叫做`套接字(socket)`或插口。端口号拼接到(contatenated with) IP 地址即构成了套接字。每一条 TCP 连接唯一地被通信两端的两个端点（即两个套接字）所确定。

以字节为单位的滑动窗口,根据 B 给出的窗口值
1.A 构造出自己的发送窗口
![image](https://github.com/woojean/woojean.github.io/blob/master/images/net_3.png)
2.A 发送了 11 个字节的数据
  P3 – P1 = A 的`发送窗口`（又称为通知窗口）
  P2 – P1 = 已发送但尚未收到确认的字节数
  P3 – P2 = 允许发送但尚未发送的字节数（又称为`可用窗口`） 
![image](https://github.com/woojean/woojean.github.io/blob/master/images/net_4.png)
3.A 收到新的确认号，发送窗口向前滑动
![image](https://github.com/woojean/woojean.github.io/blob/master/images/net_5.png)
4.A 的发送窗口内的序号都已用完，但还没有再收到确认，必须停止发送。
![image](https://github.com/woojean/woojean.github.io/blob/master/images/net_6.png)


### TCP发送缓存与接收缓存
发送缓存用来暂时存放：
 1.发送应用程序传送给发送方 TCP 准备发送的数据；
 2.TCP 已发送出但尚未收到确认的数据。

接收缓存用来暂时存放：
 1.按序到达的、但尚未被接收应用程序读取的数据；
 2.不按序到达的数据。

`注意`：
1.A 的发送窗口并不总是和 B 的接收窗口一样大（因为有一定的时间滞后）。
2.TCP 标准没有规定对不按序到达的数据应如何处理。通常是先临时存放在接收窗口中，等到字节流中所缺少的字节收到后，再按序交付上层的应用进程。
3.TCP 要求接收方必须有累积确认的功能，这样可以减小传输开销。

## 划分子网与构造超网
划分子网纯属一个单位内部的事情。单位对外仍然表现为没有划分子网的网络。凡是从其他网络发送给本单位某个主机的 IP 数据报，仍然是根据 IP 数据报的目的网络号 net-id，先找到连接在本单位网络上的路由器。然后此路由器在收到 IP 数据报后，再按目的网络号 net-id 和子网号 subnet-id 找到目的子网。

`子网掩码`是一个网络或一个子网的重要属性。
路由器在和相邻路由器交换路由信息时，必须把自己所在网络（或子网）的子网掩码告诉相邻路由器。
路由器的路由表中的每一个项目，除了要给出目的网络地址外，还必须同时给出该网络的子网掩码。
若一个路由器连接在两个子网上就拥有两个网络地址和两个子网掩码。
不同的子网掩码可能得出相同的网络地址。但不同的掩码的效果是不同的。 

在划分子网的情况下路由器转发分组的算法 
(1) 从收到的分组的首部提取目的 IP 地址 D。
(2) 先用各网络的子网掩码和 D 逐位相“与”，看是否和相应的网络地址匹配。若匹配，则将分组直接交付。否则就是间接交付，执行(3)。
(3) 若路由表中有目的地址为 D 的特定主机路由，则将分组传送给指明的下一跳路由器；否则，执行(4)。
(4) 对路由表中的每一行的子网掩码和 D 逐位相“与”，若其结果与该行的目的网络地址匹配，则将分组传送给该行指明的下一跳路由器；否则，执行(5)。
(5) 若路由表中有一个默认路由，则将分组传送给路由表中所指明的默认路由器；否则，执行(6)。
(6) 报告转发分组出错。

## RESTful架构风格理解
REST并不是一种具体的技术，也不是一种具体的规范，REST其实是一种内涵非常丰富的架构风格。它是为运行在互联网环境的分布式超媒体系统量身定制的。互联网环境与企业内网环境有非常大的差别，最主要的差别是两个方面：
（1）可伸缩性需求无法控制：并发访问量可能会暴涨，也可能会暴跌。
（2）安全性需求无法控制：无法控制客户端发来的请求的格式，很可能会是恶意的请求。

从架构风格的抽象高度来看，常见的分布式应用架构风格有三种：
（1）分布式对象（Distributed Objects，简称DO），架构实例有CORBA/RMI/EJB/DCOM/.NET Remoting等等；
（2）远程过程调用（Remote Procedure Call，简称RPC），架构实例有SOAP/XML-RPC/Hessian/Flash AMF/DWR等等；
（3）表述性状态转移（Representational State Transfer，简称REST），架构实例有HTTP/WebDAV；

REST是HTTP/1.1协议等Web规范的设计指导原则，HTTP/1.1协议正是为实现REST风格的架构而设计的。REST是所有Web应用都应该遵守的架构设计指导原则。当然，REST并不是法律，违反了REST的指导原则，仍然能够实现应用的功能。但是违反了REST的指导原则，会付出很多代价，特别是对于大流量的网站而言。
要深入理解REST，需要理解REST的五个关键词：
（1）资源（Resource）
资源是一种看待服务器的方式，即，将服务器看作是由很多离散的资源组成。每个资源是服务器上一个可命名的抽象概念。因为资源是一个抽象的概念，所以它不仅仅能代表服务器文件系统中的一个文件、数据库中的一张表等等具体的东西，可以将资源设计的要多抽象有多抽象，只要想象力允许而且客户端应用开发者能够理解。与面向对象设计类似，资源是以名词为核心来组织的，首先关注的是名词。一个资源可以由一个或多个URI来标识。URI既是资源的名称，也是资源在Web上的地址。对某个资源感兴趣的客户端应用，可以通过资源的URI与其进行交互。
（2）资源的表述（Representation）
资源的表述是一段对于资源在某个特定时刻的状态的描述。可以在客户端-服务器端之间转移（交换）。资源的表述可以有多种格式，例如HTML/XML/JSON/纯文本/图片/视频/音频等等。资源的表述格式可以通过协商机制来确定。请求-响应方向的表述通常使用不同的格式。
（3）状态转移（State Transfer）
状态转移（state transfer）与状态机中的状态迁移（state transition）的含义是不同的。状态转移说的是：在客户端和服务器端之间转移（transfer）代表资源状态的表述。通过转移和操作资源的表述，来间接实现操作资源的目的。
（4）统一接口（Uniform Interface）
REST要求，必须通过统一的接口来对资源执行各种操作。对于每个资源只能执行一组有限的操作。以HTTP/1.1协议为例，HTTP/1.1协议定义了一个操作资源的统一接口，主要包括以下内容：
1）7个HTTP方法：GET/POST/PUT/DELETE/PATCH/HEAD/OPTIONS
2）HTTP头信息（可自定义）
3）HTTP响应状态代码（可自定义）
4）一套标准的内容协商机制
5）一套标准的缓存机制
6）一套标准的客户端身份认证机制
（5）超文本驱动（Hypertext Driven）
“超文本驱动”又名“将超媒体作为应用状态的引擎”（Hypermedia As The Engine Of Application State，来自Fielding博士论文中的一句话，缩写为HATEOAS）。将Web应用看作是一个由很多状态（应用状态）组成的有限状态机。资源之间通过超链接相互关联，超链接既代表资源之间的关系，也代表可执行的状态迁移。在超媒体之中不仅仅包含数据，还包含了状态迁移的语义。以超媒体作为引擎，驱动Web应用的状态迁移。通过超媒体暴露出服务器所提供的资源，服务器提供了哪些资源是在运行时通过解析超媒体发现的，而不是事先定义的。从面向服务的角度看，超媒体定义了服务器所提供服务的协议。客户端应该依赖的是超媒体的状态迁移语义，而不应该对于是否存在某个URI或URI的某种特殊构造方式作出假设。一切都有可能变化，只有超媒体的状态迁移语义能够长期保持稳定。

REST风格的架构所具有的6个主要特征：
（1）面向资源（Resource Oriented）
（2）可寻址（Addressability）
（3）连通性（Connectedness）
（4）无状态（Statelessness）
（5）统一接口（Uniform Interface）
（6）超文本驱动（Hypertext Driven）
这6个特征是REST架构设计优秀程度的判断标准。其中，面向资源是REST最明显的特征，即，REST架构设计是以资源抽象为核心展开的。可寻址说的是：每一个资源在Web之上都有自己的地址。连通性说的是：应该尽量避免设计孤立的资源，除了设计资源本身，还需要设计资源之间的关联关系，并且通过超链接将资源关联起来。无状态、统一接口是REST的两种架构约束，超文本驱动是REST的一个关键词。

## 什么是依赖注入？和控制反转是什么关系？
IoC（Inversion of Control）控制反转
DI（Dependency Injection）依赖注入

DI是IoC的一种具体实现，另一种主要的实现方式是服务定位器（Service Locator）。

没有IoC的时候，常规的A类使用C类的示意图：
 ![image](https://github.com/woojean/woojean.github.io/blob/master/images/img_1.png)

有IoC的时候，A类不再主动去创建C，而是被动等待，等待IoC的容器获取一个C的实例，然后反向地注入到A类中。
 ![image](https://github.com/woojean/woojean.github.io/blob/master/images/img_2.png)
 

## Linux中有哪些设备类型？
在Linux中，设备被分为以下三种类型：
1.块设备：可寻址，寻址以块为单位，块大小取决于设备。通常支持对数据的随机访问，如硬盘、蓝光光碟、闪存等。通过称为“块设备节点”的特殊文件来访问，通常被挂载为文件系统。
2.字符设备：不可寻址，仅提供数据的流式访问，即一个个字符或一个个字节，如键盘、鼠标、打印机等。通过称为“字符设备节点”的特殊文件来访问，与块设备不同，应用程序通过直接访问设备节点与字符设备交互。
3.网络设备：通过一个物理适配器和一种特定的网络协议提供了对网络的访问，打破了Unix所有东西都是文件的设计原则，不是通过设备节点来访问，而是通过套接字API这样的特殊接口来访问。

伪设备
并不是所有设备驱动都表示物理设备，有些设备驱动是虚拟的，仅提供访问内核功能而已，被称为“伪设备”，如内核随机数发生器（/dev/random）、空设备（/dev/null）、零设备（/dev/zero）等等。

后台进程与守护进程有什么区别？
（1）最直观的区别：守护进程没有控制终端，而后台进程还有。如通过命令firefox &在后台运行firefox，此时firefox虽然在后台运行，但是并没有脱离终端的控制，如果把终端关掉则firefox也会一起关闭。
（2）后台进程的文件描述符继承自父进程，例如shell，所以它也可以在当前终端下显示输出数据。但是守护进程自己变成进程组长，其文件描述符号和控制终端没有关联，是控制台无关的。
（3）守护进程肯定是后台进程，但后台进程不一定是守护进程。基本上任何一个程序都可以后台运行，但守护进程是具有特殊要求的程序，比如它能够脱离自己的父进程，成为自己的会话组长等（这些需要在程序代码中显式地写出来）。

## 简述ext2和ext3的区别？
Linux ext2/ext3文件系统使用索引节点来记录文件信息，作用像windows的文件分配表。索引节点是一个结构，它包含了一个文件的长度、创建及修改时间、权限、所属关系、磁盘中的位置等信息。一个文件系统维护了一个索引节点的数组，每个文件或目录都与索引节点数组中的唯一一个元素对应。系统给每个索引节点分配了一个号码，也就是该节点在数组中的索引号，称为索引节点号。 linux文件系统将文件索引节点号和文件名同时保存在目录中。所以，目录只是将文件的名称和它的索引节点号结合在一起的一张表，目录中每一对文件名称和索引节点号称为一个连接。 对于一个文件来说有唯一的索引节点号与之对应，对于一个索引节点号，却可以有多个文件名与之对应。因此，在磁盘上的同一个文件可以通过不同的路径去访问它。
Linux缺省情况下使用的文件系统为Ext2，ext2文件系统的确高效稳定。但是，随着Linux系统在关键业务中的应用，Linux文件系统的弱点也渐渐显露出来了:其中系统缺省使用的ext2文件系统是非日志文件系统。这在关键行业的应用是一个致命的弱点。本文向各位介绍Linux下使用ext3日志文件系统应用。
Ext3文件系统是直接从Ext2文件系统发展而来，目前ext3文件系统已经非常稳定可靠。它完全兼容ext2文件系统。用户可以平滑地过渡到一个日志功能健全的文件系统中来。这实际上了也是ext3日志文件系统初始设计的初衷。
Ext3日志文件系统的特点
1、高可用性
系统使用了ext3文件系统后，即使在非正常关机后，系统也不需要检查文件系统。宕机发生后，恢复ext3文件系统的时间只要数十秒钟。
2、数据的完整性:
ext3文件系统能够极大地提高文件系统的完整性，避免了意外宕机对文件系统的破坏。在保证数据完整性方面，ext3文件系统有2种模式可供选择。其中之一就是“同时保持文件系统及数据的一致性”模式。采用这种方式，你永远不再会看到由于非正常关机而存储在磁盘上的垃圾文件。
3、文件系统的速度:
尽管使用ext3文件系统时，有时在存储数据时可能要多次写数据，但是，从总体上看来，ext3比ext2的性能还要好一些。这是因为ext3的日志功能对磁盘的驱动器读写头进行了优化。所以，文件系统的读写性能较之Ext2文件系统并来说，性能并没有降低。
4、数据转换
  由ext2文件系统转换成ext3文件系统非常容易，只要简单地键入两条命令即可完成整个转换过程，用户不用花时间备份、恢复、格式化分区等。用一个ext3文件系统提供的小工具tune2fs，它可以将ext2文件系统轻松转换为ext3日志文件系统。另外，ext3文件系统可以不经任何更改，而直接加载成为ext2文件系统。
5、多种日志模式
  Ext3有多种日志模式，一种工作模式是对所有的文件数据及metadata（定义文件系统中数据的数据,即数据的数据）进行日志记录（data=journal模式）；另一种工作模式则是只对metadata记录日志，而不对数据进行日志记录，也即所谓data=ordered或者data=writeback模式。系统管理人员可以根据系统的实际工作要求，在系统的工作速度与文件数据的一致性之间作出选择。


## Linux中通过编译安装的方式安装程序，各步骤操作分别做什么工作？
源码要运行，必须先转成二进制的机器码。这是编译器的任务。
对于简单的代码，可以直接调用编译器生成二进制文件后运行，如：
	$ gcc test.c
$ ./a.out
对于复杂的项目，编译过程通常分成3个部分：
$ ./configure
$ make  
$ make install

整个编译安装过程分为以下步骤：
（1）配置
配置信息保存在一个配置文件之中，约定俗成是一个叫做configure的脚本文件。通常它是由autoconf工具生成的。编译器通过运行这个脚本，获知编译参数。如果用户的系统环境比较特别，或者有一些特定的需求，就需要手动向configure脚本提供编译参数，如：
$ ./configure --prefix=/www --with-mysql	# 指定安装后的文件保存在www目录，并且编译时加入mysql模块的支持

（2）确定标准库和头文件的位置
从配置文件中知道标准库和头文件的位置。

（3）确定依赖关系
源码文件之间往往存在依赖关系，编译器需要确定编译的先后顺序。假定A文件依赖于B文件，编译器应该保证做到下面两点。
1）只有在B文件编译完成后，才开始编译A文件。
2）当B文件发生变化时，A文件会被重新编译。
编译顺序保存在一个叫做makefile的文件中，里面列出哪个文件先编译，哪个文件后编译。而makefile文件由configure脚本运行生成，这就是为什么编译时configure必须首先运行的原因。

（4）预编译头文件
不同的源码文件，可能引用同一个头文件（比如stdio.h）。编译的时候，头文件也必须一起编译。为了节省时间，编译器会在编译源码之前，先编译头文件。这保证了头文件只需编译一次，不必每次用到的时候，都重新编译了。不过，并不是头文件的所有内容都会被预编译。用来声明宏的#define命令，就不会被预编译。

（5）预处理
编译器就开始替换掉源码中的头文件和宏以及移除注释。

（6）编译
编译器就开始生成机器码。对于某些编译器来说，还存在一个中间步骤，会先把源码转为汇编码（assembly），然后再把汇编码转为机器码。这种转码后的文件称为对象文件（object file）。

（7）链接
把外部函数的代码（通常是后缀名为.lib和.a的文件）添加到可执行文件中。这就叫做连接（linking）。这种通过拷贝，将外部函数库添加到可执行文件的方式，叫做静态连接（static linking）
make命令的作用，就是从第（4）步头文件预编译开始，一直到做完这一步。

（8）安装
将可执行文件保存到用户事先指定的安装目录。这一步还必须完成创建目录、保存文件、设置权限等步骤。这整个的保存过程就称为"安装"（Installation）。

（9）操作系统链接
以某种方式通知操作系统，让其知道可以使用这个程序了。这就要求在操作系统中，登记这个程序的元数据：文件名、文件描述、关联后缀名等等。Linux系统中，这些信息通常保存在/usr/share/applications目录下的.desktop文件中。
make install命令，就用来完成"安装"和"操作系统连接"这两步。

（10）生成安装包
将上一步生成的可执行文件，做成可以分发的安装包。通常是将可执行文件（连带相关的数据文件），以某种目录结构，保存成压缩文件包，交给用户。

（11）动态链接
开发者可以在编译阶段选择可执行文件连接外部函数库的方式，到底是静态连接（编译时连接），还是动态连接（运行时连接）。
静态连接就是把外部函数库，拷贝到可执行文件中。这样做的好处是，适用范围比较广，不用担心用户机器缺少某个库文件；缺点是安装包会比较大，而且多个应用程序之间，无法共享库文件。
动态连接的做法正好相反，外部函数库不进入安装包，只在运行时动态引用。好处是安装包会比较小，多个应用程序可以共享库文件；缺点是用户必须事先安装好库文件，而且版本和安装位置都必须符合要求，否则就不能正常运行。
现实中，大部分软件采用动态连接，共享库文件。这种动态共享的库文件，Linux平台是后缀名为.so的文件，Windows平台是.dll文件，Mac平台是.dylib文件。

## 如何使用P、V操作来结局各种生产者-消费者问题？
PV操作由P操作原语和V操作原语组成（原语是不可中断的过程），对信号量进行操作，具体定义如下：
    P（S）：①将信号量S的值减1，即S=S-1；
           ②如果S0，则该进程继续执行；否则该进程置为等待状态，排入等待队列。
    V（S）：①将信号量S的值加1，即S=S+1；
           ②如果S>0，则该进程继续执行；否则释放队列中第一个等待信号量的进程。
PV操作的意义：我们用信号量及PV操作来实现进程的同步和互斥。PV操作属于进程的低级通信。
信号量（semaphore）的数据结构为一个值和一个指针，指针指向等待该信号量的下一个进程。信号量的值与相应资源的使用情况有关。当它的值大于0时，表示当前可用资源的数量；当它的值小于0时，其绝对值表示等待使用该资源的进程个数。注意，信号量的值仅能由PV操作来改变。
     一般来说，信号量S0时，S表示可用资源的数量。执行一次P操作意味着请求分配一个单位资源，因此S的值减1；当S<0时，表示已经没有可用资源，请求者必须等待别的进程释放该类资源，它才能运行下去。而执行一个V操作意味着释放一个单位资源，因此S的值加1；若S0，表示有某些进程正在等待该资源，因此要唤醒一个等待状态的进程，使之运行下去。
利用信号量和PV操作实现进程互斥：
	进程P1              进程P2           ……          进程Pn
	……                  ……                           ……
	P（S）；              P（S）；                         P（S）；
	临界区；             临界区；                        临界区；
	V（S）；              V（S）；                        V（S）；
	……                  ……            ……           ……

    其中信号量S用于互斥，初值为1。
    使用PV操作实现进程互斥时应该注意的是：
    	（1）每个程序中用户实现互斥的P、V操作必须成对出现，先做P操作，进临界区，后做V操作，出临界区。若有多个分支，要认真检查其成对性。
    	（2）P、V操作应分别紧靠临界区的头尾部，临界区的代码应尽可能短，不能有死循环。
   	（3）互斥信号量的初值一般为1。
利用信号量和PV操作实现进程同步
PV操作是典型的同步机制之一。用一个信号量与一个消息联系起来，当信号量的值为0时，表示期望的消息尚未产生；当信号量的值非0时，表示期望的消息已经存在。用PV操作实现进程同步时，调用P操作测试消息是否到达，调用V操作发送消息。
    使用PV操作实现进程同步时应该注意的是：
    	（1）分析进程间的制约关系，确定信号量种类。在保持进程间有正确的同步关系情况下，哪个进程先执行，哪些进程后执行，彼此间通过什么资源（信号量）进行协调，从而明确要设置哪些信号量。
    	（2）信号量的初值与相应资源的数量有关，也与P、V操作在程序代码中出现的位置有关。
    	（3）同一信号量的P、V操作要成对出现，但它们分别在不同的进程代码中。
【例1】生产者-消费者问题
在多道程序环境下，进程同步是一个十分重要又令人感兴趣的问题，而生产者-消费者问题是其中一个有代表性的进程同步问题。下面我们给出了各种情况下的生产者-消费者问题，深入地分析和透彻地理解这个例子，对于全面解决操作系统内的同步、互斥问题将有很大帮助。
（1）一个生产者，一个消费者，公用一个缓冲区。
定义两个同步信号量：
	empty——表示缓冲区是否为空，初值为1。
   	full——表示缓冲区中是否为满，初值为0。
生产者进程
	while(TRUE){
		生产一个产品;
     	P(empty);
     	产品送往Buffer;
     	V(full);
	}
消费者进程
	while(True){
		P(full);
   		从Buffer取出一个产品;
   		V(empty);
   		消费该产品;
   	}
（2）一个生产者，一个消费者，公用n个环形缓冲区。
定义两个同步信号量：
	empty——表示缓冲区是否为空，初值为n。
	full——表示缓冲区中是否为满，初值为0。
	设缓冲区的编号为1～n-1，定义两个指针in和out，分别是生产者进程和消费者进程使用的指针，指向下一个可用的缓冲区。
生产者进程
	while(TRUE){
     	生产一个产品;
     	P(empty);
     	产品送往buffer（in）；
     	in=(in+1)mod n；
     	V(full);
	}
消费者进程
	while(TRUE){
 		P(full);
   		从buffer（out）中取出产品；
   		out=(out+1)mod n；
   		V(empty);
   		消费该产品;
   	}
（3）一组生产者，一组消费者，公用n个环形缓冲区
在这个问题中，不仅生产者与消费者之间要同步，而且各个生产者之间、各个消费者之间还必须互斥地访问缓冲区。
定义四个信号量：
empty——表示缓冲区是否为空，初值为n。
full——表示缓冲区中是否为满，初值为0。
mutex1——生产者之间的互斥信号量，初值为1。
mutex2——消费者之间的互斥信号量，初值为1。
	设缓冲区的编号为1～n-1，定义两个指针in和out，分别是生产者进程和消费者进程使用的指针，指向下一个可用的缓冲区。
生产者进程
while(TRUE){
     生产一个产品;
     P(empty);
     P(mutex1)；
     产品送往buffer（in）；
     in=(in+1)mod n；
     V(mutex1);
     V(full);
}
消费者进程
while(TRUE){
 P(full)
   P(mutex2)；
   从buffer（out）中取出产品；
   out=(out+1)mod n；
   V（mutex2）；
   V(empty);
   消费该产品;
   }
  需要注意的是无论在生产者进程中还是在消费者进程中，两个P操作的次序不能颠倒。应先执行同步信号量的P操作，然后再执行互斥信号量的P操作，否则可能造成进程死锁。

【例2】桌上有一空盘，允许存放一只水果。爸爸可向盘中放苹果，也可向盘中放桔子，儿子专等吃盘中的桔子，女儿专等吃盘中的苹果。规定当盘空时一次只能放一只水果供吃者取用，请用P、V原语实现爸爸、儿子、女儿三个并发进程的同步。
分析 在本题中，爸爸、儿子、女儿共用一个盘子，盘中一次只能放一个水果。当盘子为空时，爸爸可将一个水果放入果盘中。若放入果盘中的是桔子，则允许儿子吃，女儿必须等待；若放入果盘中的是苹果，则允许女儿吃，儿子必须等待。本题实际上是生产者-消费者问题的一种变形。这里，生产者放入缓冲区的产品有两类，消费者也有两类，每类消费者只消费其中固定的一类产品。
    解：在本题中，应设置三个信号量S、So、Sa，信号量S表示盘子是否为空，其初值为l；信号量So表示盘中是否有桔子，其初值为0；信号量Sa表示盘中是否有苹果，其初值为0。同步描述如下：
	int S＝1;
	int Sa＝0;
	int So＝0;
      main()
      {
        cobegin
            father();      /*父亲进程*/
            son();        /*儿子进程*/
            daughter();    /*女儿进程*/
        coend
    ｝
    father()
    {
        while(1)
          {
            P(S);
            将水果放入盘中;
            if（放入的是桔子）V(So);
            else  V(Sa);
           }
     }
    son()
    {
        while(1)
          {
             P(So);
             从盘中取出桔子;
             V(S);
             吃桔子;
            ｝
    }
    daughter()
    {
         while(1)
            {
              P(Sa);
              从盘中取出苹果;
              V(S);
              吃苹果;
            ｝
｝

## 结构体大小如何判断？
偏移量：偏移量指的是结构体变量中成员的地址和结构体变量地址的差。结构体大小等于最后一个成员的偏移量加上最后一个成员的大小。
由于存储变量时地址对齐的要求，编译器在编译程序时会遵循两条原则：
一、结构体变量中成员的偏移量必须是成员大小的整数倍（0被认为是任何数的整数倍）
二、结构体大小必须是所有成员大小的整数倍。
此外：结构体变量的首地址能够被其最宽基本类型成员的大小所整除；
因此不同的定义顺序会影响到结构体的大小：
struct s{
	char c;
	int i;
	char cc;
};  // 大小为12
struct s{
	char c;
	char cc;
int i;
};  // 大小为8
当结构体中的成员又是另外一种结构体类型时，只需要把其展开，展开后的结构体的第一个成员的偏移量应当是被展开的结构体中最大的成员的整数倍。
附：基本数据类型所占字节数如下
类型                  字节
char                  	1
short int             		2
int                   	2(16bit)/4(32bit)/4(64bit)
long                  	4(16bit)/4(32bit)/8(64bit)
指针变量              	4
float                 	4
double                	8
long long             	8
long double           	10
各种数据类型所占字节长度，主要是int型,long型和指针数据类型的差异。
int型数据，如果是16bit平台,则是2个字节，如果是32bit的，则占4个字节,64bit仍然是4字节。
long型数据，如果是16bit平台,则是4个字节，如果是32bit的，则占4个字节,64bit仍然是8字节。
指针型数据，比较特殊，大多是4个字节，只有在16bit平台，并且指针式段内寻址时才是2个字节。
另外注意：sizeof(表达式)这样的使用，sizeof是给出其操作数所需要占用的内存大小，在编译时就可以确定。因此不需要去计算表达式的值；
因此有：
int i = 3;
   	cout<<sizeof(i++)<<endl;
   	cout<<i<<endl;
	输出4,3。i++根本没有执行。
  ![image](https://github.com/woojean/woojean.github.io/blob/master/images/img_3.png)

## 什么是字节对齐？
在结构中，编译器为结构的每个成员按其自然边界（alignment）分配空间。各个成员按照它们被声明的顺序在内存中顺序存储，第一个成员的地址和整个结构的地址相同。
为了使CPU能够对变量进行快速的访问,变量的起始地址应该具有某些特性,即所谓的”对齐”. 比如4字节的int型,其起始地址应该位于4字节的边界上,即起始地址能够被4整除.
对于标准数据类型，它的地址只要是它的长度的整数倍就行了，而非标准数据类型按下面的原则对齐：
　　数组 ：按照基本数据类型对齐，第一个对齐了后面的自然也就对齐了。 
　　联合 ：按其包含的长度最大的数据类型对齐。 
　　结构体： 结构体中每个数据类型都要对齐。
　　比如有如下一个结构体：
　　struct stu{
　　 char sex;
　　 int length;
　　 char name[10];
　　};
　　struct stu my_stu;
由于在x86下，GCC默认按4字节对齐，它会在sex后面跟name后面分别填充三个和两个字节使length和整个结构体对齐。于是我们sizeof(my_stu)会得到长度为20，而不是15.
 ![image](https://github.com/woojean/woojean.github.io/blob/master/images/img_4.png)

需要字节对齐的根本原因在于CPU访问数据的效率问题。假设上面整型变量的地址不是自然对齐，比如为0x00000002，则CPU如果取它的值的话需要访问两次内存，第一次取从0x00000002-0x00000003的一个short，第二次取从0x00000004-0x00000005的一个short然后组合得到所要的数据，如果变量在0x00000003地址上的话则要访问三次内存，第一次为char，第二次为short，第三次为char，然后组合得到整型数据。而如果变量在自然对齐位置上，则只要一次就可以取出数据。

## 什么是大尾表示法？什么是小尾表示法？
Little-Endian:低位字节放在内存的低地址段；
Big-Endian：高位字节放在内存的低地址段；
问题1
unsigned char endian[2] = {1, 0}; 
short x; 
x = *(short *) endian; 
代码运行后，x的值是多少？
 ![image](https://github.com/woojean/woojean.github.io/blob/master/images/img_5.png)
 
问题2
int i = 1; 
 if (*(char*)&i == 0) 
     return true 
 else 
     return false
答案：
1.	是big endian，x = 256；是little endian，x = 1
2.	是big endian，true；是little endian，false
考察点：
1.如果应试者直接回答，不太妥当，应该问面试官系统是高字节还是低字节
2.如何判断系统是高字节还是低字节


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


## 一条MySQL语句的执行顺序是什么样的？
MySQL的语句一共分为11步，如下图所标注的那样，最先执行的总是FROM操作，最后执行的是LIMIT操作。其中每一个操作都会产生一张虚拟的表，这个虚拟的表作为一个处理的输入，只是这些虚拟的表对用户来说是透明的，但是只有最后一个虚拟的表才会被作为结果返回。如果没有在语句中指定某一个子句，那么将会跳过相应的步骤。
1.	FORM: 对FROM的左边的表和右边的表计算笛卡尔积。产生虚表VT1
2.	ON: 对虚表VT1进行ON筛选，只有那些符合<join-condition>的行才会被记录在虚表VT2中。
3.	JOIN： 如果指定了OUTER JOIN（比如left join、 right join），那么保留表中未匹配的行就会作为外部行添加到虚拟表VT2中，产生虚拟表VT3, rug from子句中包含两个以上的表的话，那么就会对上一个join连接产生的结果VT3和下一个表重复执行步骤1~3这三个步骤，一直到处理完所有的表为止。
4.	WHERE： 对虚拟表VT3进行WHERE条件过滤。只有符合<where-condition>的记录才会被插入到虚拟表VT4中。
5.	GROUP BY: 根据group by子句中的列，对VT4中的记录进行分组操作，产生VT5.
6.	CUBE | ROLLUP: 对表VT5进行cube或者rollup操作，产生表VT6.
7.	HAVING： 对虚拟表VT6应用having过滤，只有符合<having-condition>的记录才会被 插入到虚拟表VT7中。
8.	SELECT： 执行select操作，选择指定的列，插入到虚拟表VT8中。
9.	DISTINCT： 对VT8中的记录进行去重。产生虚拟表VT9.
10.	ORDER BY: 将虚拟表VT9中的记录按照<order_by_list>进行排序操作，产生虚拟表VT10.
11.	LIMIT：取出指定行的记录，产生虚拟表VT11, 并将结果返回。

## LIMIT语句起始行号如何计算？有无替代方法？
返回不多于5行（小于等于）
SELECT prod_name
FROM products
LIMIT 5;

返回从第6行开始的5行（行号从0开始）
SELECT prod_name
FROM products
LIMIT 5,5;

返回从第6行开始的5行（LIMIT的一种替代语法）
SELECT prod_name
FROM products
LIMIT 5 OFFSET 5;

## 使用NULL值进行条件判断存在什么问题？
空值检查
SELECT prod_name
FROM products
WHERE prod_price IS NULL;
无法通过过滤条件“选择出不具有特定值的行”来返回具有NULL值的行，因为“未知”具有特殊的含义，数据库不知道它们是否匹配，所以在匹配过滤或不匹配过滤时，不返回它们。即NULL值既非等于，也非不等于。


## 使用%进行模糊查询有什么限制？
搜索以指定字符串开始、以指定字符串结束的记录
SELECT prod_name
FROM products
WHERE prod_name LIKE 's%e';
%表示任何字符出现任意次数（0、1或多个），是否区分大小写取决于MySQL的配置。
尾空格可能会干扰通配符匹配，例如“anvil ”（最后有一个空格），则LIKE “%anvil”将不会匹配，因为在最后有一个多余的字符。
通配符不能匹配NULL值，即使使用‘%’也不行。

注意：把通配符置于搜索模式的开始处，搜索起来是最慢的。


## 冷备份和热备份有什么不同？各自的优缺点是什么？
冷备份
发生在数据库已经正常关闭的情况下，当正常关闭时会提供给我们一个完整的数据库。冷备份是将关键性文件拷贝到另外位置的一种说法。对于备份数据库信息而言，冷备份是最快和最安全的方法。
优点： 
1．是非常快速的备份方法（只需拷贝文件） 
2．容易归档（简单拷贝即可） 
3．容易恢复到某个时间点上（只需将文件再拷贝回去） 
4．能与归档方法相结合，做数据库“最新状态”的恢复。 
5．低度维护，高度安全。 

缺点： 
1．单独使用时，只能提供到“某一时间点上”的恢复。 
2．在实施备份的全过程中，数据库必须要作备份而不能作其它工作。也就是说，在冷备份过程中，数据库必须是关闭状态。 
3．若磁盘空间有限，只能拷贝到磁带等其它外部存储设备上，速度会很慢。 
4．不能按表或按用户恢复。 
值得注意的是冷备份必须在数据库关闭的情况下进行，当数据库处于打开状态时，执行数据库文件系统备份是无效的 。而且在恢复后一定要把数据库文件的属组和属主改为mysql。

热备份
在数据库运行的情况下，备份数据库操作的sql语句，当数据库发生问题时，可以重新执行一遍备份的sql语句。

优点： 
1．可在表空间或数据文件级备份，备份时间短。 
2．备份时数据库仍可使用。 
3．可达到秒级恢复（恢复到某一时间点上）。 
4．可对几乎所有数据库实体作恢复。 
5．恢复是快速的，在大多数情况下在数据库仍工作时恢复。 

缺点： 
1．不能出错，否则后果严重。 
2．若热备份不成功，所得结果不可用于时间点的恢复。 
3．因难于维护，所以要特别仔细小心，不允许“以失败而告终”。

MySQL原生支持多机热备。


## 如何理解数据库设计的三个范式？
通俗地理解三个范式，对于数据库设计大有好处。在数据库设计中，为了更好地应用三个范式，就必须通俗地理解 三个范式(通俗地理解是够用的理解，并不是最科学最准确的理解)： 
第一范式：1NF是对属性的原子性约束，要求属性具有原子性，不可再分解； 
第二范式：2NF是对记录的惟一性约束，要求记录有惟一标识，即实体的惟一性； 
第三范式：3NF是对字段冗余性的约束，即任何字段不能由其他字段派生出来，它要求字段没有冗余。 

没有冗余的数据库设计可以做到。但是，没有冗余的数据库未必是最好的数据库，有时为了提高运行效率，就必须降低范式标准，适当保留冗余数据。具体做法是：在概念数据模型设计时遵守第三范式，降低范式标准的工作放到物理数据模型设计时考虑。降低范式就是增加字段，允许冗余。

范式是符合某一种级别的关系模式的集合。关系数据库中的关系必须满足一定的要求，即满足不同的范式。目前关系数据库有六种范式。一般说来，数据库只需满足第三范式（3NF）就行了。
第一范式（1NF）：指数据库表的每一列都是不可分割的基本数据项，同一列中不能有多个值，即实体中的某个属性不能有多个值或者不能有重复的属性。例如，不能将员工信息都放在一列中显示，也不能将其中的两列或多列在一列中显示；员工信息表的每一行只表示一个员工的信息，一个员工的信息在表中只出现一次。简而言之，第一范式就是无重复的列。
第二范式（2NF）：第二范式（2NF）要求数据库表中的每个实例或行必须可以被惟一地区分。为实现区分通常需要为表加上一个列，以存储各个实例的惟一标识。简而言之，第二范式就是非主属性的部分依赖于主关键字。
第三范式（3NF）：第三范式（3NF）要求一个数据库表中不包含已在其它表中已包含的非主关键字信息。例如，存在一个部门信息表，其中每个部门有部门编号（dept_id）、部门名称、部门简介等信息。那么在员工信息表中列出部门编号后就不能再将部门名称、部门简介等与部门有关的信息再加入员工信息表中。如果不存在部门信息表，则根据第三范式（3NF）也应该构建它，否则就会有大量的数据冗余。简而言之，第三范式就是属性不依赖于其它非主属性。


## 有哪些MySQL性能优化的技巧？
（1）优化MySQL查询语句，使其使用查询缓存
对于相同的查询，MySQL引擎会使用缓存，但是如果在SQL语句中使用函数，如NOW()、RAND()、 CURDATE()等等，则拼凑出的查询不会被认为是相同的查询。
// 查询缓存不开启
$r = mysql_query("SELECT username FROM user WHERE signup_date >= CURDATE()");

// 开启查询缓存
$today = date("Y-m-d");
$r = mysql_query("SELECT username FROM user WHERE signup_date >= '$today'");

（2）当只要一行数据时使用LIMIT 1
	这样MySQL数据库引擎会在找到一条数据后停止搜索，而不是继续往后查少下一条符合记录的数据。

（3）为搜索字段建索引
索引并不一定就是给主键或是唯一的字段。如果在表中有某个字段总要会经常用来做搜索，那么请为其建立索引。

（4）在Join表的时候使用相当类型的列，并将其索引
对于那些STRING类型，还需要有相同的字符集才行（两个表的字符集有可能不一样）

（5）千万不要ORDER BY RAND()
You cannot use a column with RAND() values in an ORDER BY clause, because ORDER BY 
would evaluate the column multiple times. 
当记录数据过多时，会非常慢。

（6）避免SELECT *
应该养成一个需要什么就取什么的好的习惯。

（7）使用ENUM而不是VARCHAR
ENUM 实际保存的是TINYINT，但其外表上显示为字符串。如果有一个字段的取值是有限而且固定的，那么，应该使用ENUM而不是VARCHAR。

（8）尽可能的使用NOT NULL
“NULL columns require additional space in the row to record whether their values are NULL. For MyISAM tables, each NULL column takes one bit extra, rounded up to the nearest byte.”
NULL值需要额外的存储空间，而且在比较时也需要额外的逻辑。

（9）把IP地址存成 UNSIGNED INT，而不是VARCHAR(15)

（10）固定长度的表会更快
如果表中的所有字段都是“固定长度”的，整个表会被认为是 “static” 或 “fixed-length”。

（11）垂直分割
“垂直分割”是一种把数据库中的表按列变成几张表的方法，这样可以降低表的复杂度和字段的数目，从而达到优化的目的。
示例一：在Users表中有一个字段是家庭地址，这个字段是可选字段，相比起，而且你在数据库操作的时候除了个人信息外，你并不需要经常读取或是改写这个字段。那么，为什么不把他放到另外一张表中呢？ 这样会让你的表有更好的性能，只有用户ID，用户名，口令，用户角色等会被经常使用。小一点的表总是会有好的性能。
示例二： 你有一个叫 “last_login” 的字段，它会在每次用户登录时被更新。但是，每次更新时会导致该表的查询缓存被清空。所以，你可以把这个字段放到另一个表中，这样就不会影响你对用户ID，用户名，用户角色的不停地读取了，因为查询缓存会帮你增加很多性能。
另外，你需要注意的是，这些被分出去的字段所形成的表，你不会经常性地去Join他们，不然的话，这样的性能会比不分割时还要差，而且，会是极数级的下降。
总结：降低表的规模、方便使用缓存、被分出去的字段应该是不会经常要join的字段。

（12）拆分大的 DELETE 或 INSERT 语句
因为这两个操作是会锁表的，表一锁住了，别的操作都进不来了。
如果有一个大的处理，一定把其拆分，使用 LIMIT 条件是一个好的方法。下面是一个示例：
while (1) {
//每次只做1000条
mysql_query("DELETE FROM logs WHERE log_date <= '2009-11-01' LIMIT 1000");
if (mysql_affected_rows() == 0) {
    // 没得可删了，退出！
    break;
}
// 每次都要休息一会儿
usleep(50000);
}

（13）越小的列会越快
如使用TINYINT而不是INT，使用DATE而不是DATETIME。

（14）选择一个正确的存储引擎
MyISAM 适合于一些需要大量查询的应用，但其对于有大量写操作并不是很好。甚至你只是需要update一个字段，整个表都会被锁起来，而别的进程，就算是读进程都无法操作直到读操作完成。另外，MyISAM 对于 SELECT COUNT(*) 这类的计算是超快无比的。
InnoDB 的趋势会是一个非常复杂的存储引擎，对于一些小的应用，它会比 MyISAM 还慢。他是它支持“行锁” ，于是在写操作比较多的时候，会更优秀。并且，他还支持更多的高级应用，比如：事务。

（15）小心“永久链接”
“永久链接”的目的是用来减少重新创建MySQL链接的次数。当一个链接被创建了，它会永远处在连接的状态，就算是数据库操作已经结束了。而且，自从Apache开始重用它的子进程后——也就是说，下一次的HTTP请求会重用Apache的子进程，并重用相同的 MySQL 链接。
但是从个人经验上来说，这个功能制造出来的麻烦事更多。因为，你只有有限的链接数，内存问题，文件句柄数，等等。

（1）对接触的项目进行慢查询分析，发现TOP10的基本都是忘了加索引或者索引使用不当，如索引字段上加函数导致索引失效等(如where UNIX_TIMESTAMP(gre_updatetime)>123456789)

（2）另外很多同学在拉取全表数据时，喜欢用select xx from xx limit 5000,1000这种形式批量拉取，其实这个SQL每次都是全表扫描，建议添加1个自增id做索引，将SQL改为select xx from xx where id>5000 and id<6000;

只select出需要的字段，避免select *

尽量早做过滤，使Join或者Union等后续操作的数据量尽量小

（5）把能在逻辑层算的提到逻辑层来处理，如一些数据排序、时间函数计算等


## MySQL的索引有哪些类型？
索引是一种特殊的文件(InnoDB数据表上的索引是表空间的一个组成部分)，它们包含着对数据表里所有记录的引用指针。有了相应的索引之后，数据库会直接在索引中查找符合条件的选项。有以下索引类型：
（1）普通索引
这是最基本的索引，它没有任何限制，MyIASM中默认的BTREE类型的索引，也是大多数情况下用到的索引。
// 直接创建索引
CREATE INDEX index_name ON table(column(length))

// 修改表结构的方式添加索引
ALTER TABLE table_name ADD INDEX index_name ON (column(length))

// 创建表的时候同时创建索引
CREATE TABLE `table` (
`id` int(11) NOT NULL AUTO_INCREMENT ,
`title` char(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`time` int(10) NULL DEFAULT NULL ,
PRIMARY KEY (`id`),
INDEX index_name (title(length))
)
// 删除索引
DROP INDEX index_name ON table

（2）唯一索引
索引列的值必须唯一，但允许有空值（注意和主键不同）。如果是组合索引，则列值的组合必须唯一，创建方法和普通索引类似。
// 创建唯一索引
CREATE UNIQUE INDEX indexName ON table(column(length))

// 修改表结构
ALTER TABLE table_name ADD UNIQUE indexName ON (column(length))

// 创建表的时候直接指定
CREATE TABLE `table` (
`id` int(11) NOT NULL AUTO_INCREMENT ,
`title` char(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`time` int(10) NULL DEFAULT NULL ,
PRIMARY KEY (`id`),
UNIQUE indexName (title(length))
);

（3）全文索引
仅可用于MyISAM表。可以从CHAR、VARCHAR或TEXT列中作为CREATE TABLE语句的一部分被创建，或是随后使用ALTER TABLE或CREATE INDEX被添加。对于较大的数据集，将资料输入一个没有FULLTEXT索引的表中，然后创建索引，其速度比把资料输入现有FULLTEXT索引的速度更为快。不过切记对于大容量的数据表，生成全文索引是一个非常消耗时间非常消耗硬盘空间的做法。
// 创建表的适合添加全文索引
CREATE TABLE `table` (
`id` int(11) NOT NULL AUTO_INCREMENT ,
`title` char(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`time` int(10) NULL DEFAULT NULL ,
PRIMARY KEY (`id`),
FULLTEXT (content)
);

// 修改表结构添加全文索引
ALTER TABLE article ADD FULLTEXT index_content(content)

// 直接创建索引
CREATE FULLTEXT INDEX index_content ON article(content)

（4）单列索引、多列索引
多个单列索引与单个多列索引的查询效果不同，因为执行查询时，MySQL只能使用一个索引，会从多个索引中选择一个限制最为严格的索引。

（5）组合索引
例如上表中针对title和time建立一个组合索引：
ALTER TABLE article ADD INDEX index_titme_time (title(50),time(10));
建立这样的组合索引，其实是相当于分别建立了下面两组组合索引：
–title,time
–title
为什么没有time这样的组合索引呢？这是因为MySQL组合索引“最左前缀”的结果。简单的理解就是只从最左面的开始组合。如下面的几个SQL所示：
// 使用到上面的索引
SELECT * FROM article WHREE title='测试' AND time=1234567890;		# 使用title,time索引
SELECT * FROM article WHREE utitle='测试';							# 使用title索引

// 不使用上面的索引
SELECT * FROM article WHREE time=1234567890;						

MySQL只对以下操作符才使用索引：<,<=,=,>,>=,between,in,以及某些时候的like(不以通配符%或_开头的情形)。而理论上每张表里面最多可创建16个索引。

## 不使用COUNT(*)加LIMIT，如何获取分页数据及总记录数？
在很多分页的程序中都这样写:
SELECT COUNT(*) from ‘table’ WHERE ......;  	# 查出符合条件的记录总数
SELECT * FROM ‘table’ WHERE ...... limit M,N; 	# 查询当页要显示的数据
这样的语句可以改成:
SELECT SQL_CALC_FOUND_ROWS * FROM ‘table’ WHERE ......  limit M, N;
SELECT FOUND_ROWS();
这样只要执行一次较耗时的复杂查询可以同时得到与不带limit同样的记录条数。
第二个SELECT返回一个数字，指示了在没有LIMIT子句的情况下，第一个SELECT返回了多少行。


## 什么是Covering Index？如何使用？如何判断索引是否生效？
对于SELECT a FROM … WHERE b = …这种查询，通常的做法是在b字段上建立索引，执行查询时系统会查询b索引进行定位，然后再利用此定位去表里查询需要的数据a。即该过程存在两次查询，一次是查询索引，一次是查询表。
使用Covering Index可以只查询一次索引就完成。建立一个组合索引’b,a’，当查询时，通过组合索引的b部分去定位，至于需要的数据a，立刻就可以在索引里得到，从而省略了表查询的过程。
如果使用Covering Index，要注意SELECT的方式，只SELECT必要的字段，而不能SELECT *，因为不太可能把所有的字段一起做索引。
可以使用EXPLAIN命令来确认是否使用了组合索引：如果在Extra里出现Using Index，就说明使用的是Covering Index。
实例1：
SELECT COUNT(*) FROM articles WHERE category_id = …
当在category_id建立索引后，这个查询使用的就是Covering Index。

实例2：
比如说在文章系统里分页显示的时候，一般的查询是这样的：
SELECT id, title, content FROM article ORDER BY created DESC LIMIT 10000, 10;
通常这样的查询会把索引建在created字段（其中id是主键），不过当LIMIT偏移很大时，查询效率仍然很低，改变一下查询：
SELECT id, title, content FROM article
INNER JOIN (
SELECT id FROM article ORDER BY created DESC LIMIT 10000, 10
) AS page USING(id)
此时，建立复合索引”created, id”就可以在子查询里利用上Covering Index，快速定位id。

## innodb_buffer_pool_size设置什么？
innodb_buffer_pool_size这个参数主要作用是缓存innodb表的索引、数据、插入数据时的缓冲。
默认值：128M
专用mysql服务器设置的大小： 操作系统内存的70%-80%最佳。
设置方法：
my.cnf文件
innodb_buffer_pool_size = 6G
此外，这个参数是非动态的，要修改这个值，需要重启mysqld服务。

如果因为内存不够，MySQL无法启动，就会在错误日志中出现如下报错：
InnoDB: mmap(137363456 bytes) failed; errno 12


## MySQL性能相关的配置参数有哪些？
max_connecttions：最大连接数
table_cache：缓存打开表的数量
key_buffer_size：索引缓存大小
query_cache_size：查询缓存大小
sort_buffer_size：排序缓存大小(会将排序完的数据缓存起来)
read_buffer_size：顺序读缓存大小
read_rnd_buffer_size：某种特定顺序读缓存大小(如order by子句的查询)

查看配置方法：show variables like '%max_connecttions%';

## MySQL如何进行慢查询日志分析？
慢查询相关的配置参数：
log_slow_queries					# 是否打开慢查询日志，得先确保=ON后面才有得分析
long_query_time					# 查询时间大于多少秒的SQL被当做是慢查询，一般设为1S
log_queries_not_using_indexes		# 是否将没有使用索引的记录写入慢查询日志
slow_query_log_file				# 慢查询日志存放路径


## 慢查询记录格式：
可以查看每个慢查询SQL的耗时
 User@Host: edu_online[edu_online] @  [10.139.10.167]
 Query_time: 1.958000  Lock_time: 0.000021 Rows_sent: 254786  Rows_examined: 254786
SET timestamp=1410883292;
select * from t_online_group_records;
日志显示该查询用了1.958秒，返回254786行记录，一共遍历了254786行记录。及具体的时间戳和SQL语句。

使用mysqldumpslow进行慢查询日志分析：
输入：
mysqldumpslow -s t -t 5 slow_log_20140819.txt 
-s：排序方法，t表示按时间（此外，c为按次数，r为按返回记录数等）
-t：取Top多少条，-t 5表示取前5条

输出：
Count: 1076100  Time=0.09s (99065s)  Lock=0.00s (76s)  Rows=408.9 (440058825), edu_online[edu_online]@28hosts
  select * from t_online_group_records where UNIX_TIMESTAMP(gre_updatetime) > N
Count: 1076099  Time=0.05s (52340s)  Lock=0.00s (91s)  Rows=62.6 (67324907), edu_online[edu_online]@28hosts
  select * from t_online_course where UNIX_TIMESTAMP(c_updatetime) > N
Count: 63889  Time=0.78s (49607s)  Lock=0.00s (3s)  Rows=0.0 (18), edu_online[edu_online]@[10.213.170.137]
  select f_uin from t_online_student_contact where f_modify_time > N
...
以第1条为例，表示这类SQL（N可以取很多值，这里mysqldumpslow会归并起来）在8月19号的慢查询日志内出现了1076100次，总耗时99065秒，总返回440058825行记录，有28个客户端IP用到。
通过慢查询日志分析，就可以找到最耗时的SQL，然后进行具体的SQL分析了


## 如何分析MySQL语句的执行情况？
mysql> explain select * from t_online_group_records where UNIX_TIMESTAMP(gre_updatetime) > 123456789;
+----+-------------+------------------------+------+---------------+------+---------+------+------+-------------+
| id | select_type | table                  | type | possible_keys | key  | key_len | ref  | rows | Extra       |
+----+-------------+------------------------+------+---------------+------+---------+------+------+-------------+
|  1 | SIMPLE      | t_online_group_records | ALL  | NULL          | NULL | NULL    | NULL |   47 | Using where |
+----+-------------+------------------------+------+---------------+------+---------+------+------+-------------+
1 row in set (0.00 sec)
如上面例子所示，重点关注下type，rows和Extra：
type：连接操作的类型，可以用来判断有无使用到索引。结果值从好到坏：... > range(使用到索引) > index > ALL(全表扫描)，一般查询应达到range级别，具体可能值如下：
SYSTEM	# CONST的特例，当表上只有一条记录匹配
CONST	# WHERE条件筛选后表上至多有一条记录匹配时，比如WHERE ID = 2 （ID是主键，值为2的要么有一条要么没有）
EQ_REF	# 参与连接运算的表是内表（在代码实现的算法中，两表连接时作为循环中的内循环遍历的对象，这样的表称为内表）。
基于索引（连接字段上存在唯一索引或者主键索引，且操作符必须是“=”谓词，索引值不能为NULL）做扫描，使得对外表的一条元组，内表只有唯一一条元组与之对应。
REF		# 可以用于单表扫描或者连接。参与连接运算的表，是内表。
基于索引（连接字段上的索引是非唯一索引，操作符必须是“=”谓词，连接字段值不可为NULL）做扫描，使得对外表的一条元组，内表可有若干条元组与之对应。
REF_OR_NULL		# 类似REF，只是搜索条件包括：连接字段的值可以为NULL的情况，比如 where col = 2 or col is null
RANGE			# 范围扫描，基于索引做范围扫描，为诸如BETWEEN，IN，>=，LIKE类操作提供支持
INDEX_SCAN		# 索引做扫描，是基于索引在索引的叶子节点上找满足条件的数据（不需要访问数据文件）
ALL				# 全表扫描或者范围扫描：不使用索引，顺序扫描，直接读取表上的数据（访问数据文件）
UNIQUE_SUBQUERY	# 在子查询中，基于唯一索引进行扫描，类似于EQ_REF
INDEX_SUBQUERY		# 在子查询中，基于除唯一索引之外的索引进行扫描
INDEX_MERGE		# 多重范围扫描。两表连接的每个表的连接字段上均有索引存在且索引有序，结果合并在一起。适用于作集合的并、交操作。
FT					# FULL TEXT，全文检索
rows：SQL执行检查的记录数
Extra：SQL执行的附加信息，如"Using index"表示查询只用到索引列，不需要去读表等

## 如何分析MySQL语句执行时间和消耗资源？
SET profiling=1; 						# 启动profiles，默认是没开启的
SELECT * FROM customers;			# 执行要分析的SQL语句

SHOW profiles;						# 查看SQL语句具体执行步骤及耗时

SHOW profile cpu,block io FOR QUERY 41;		#	查看ID为41的查询在各个环节的耗时和资源消耗


## MySQL如何进行主从数据同步？
复制机制（Replication）
master通过复制机制，将master的写操作通过binlog传到slave生成中继日志(relaylog)，slave再将中继日志redo，使得主库和从库的数据保持同步

复制相关的3个Mysql线程（属于slave主动请求拉取的模式）
（1）slave上的I/O线程：向master请求数据
（2）master上的Binlog Dump线程：读取binlog事件并把数据发送给slave的I/O线程
（3）slave上的SQL线程：读取中继日志并执行，更新数据库

相关监控命令
show processlist		# 查看MySQL进程信息，包括3个同步线程的当前状态
show master status		# 查看master配置及当前复制信息
show slave status		# 查看slave配置及当前复制信息

## 如何进行MySQL数据备份与恢复操作？
（1）备份：使用mysqldump导出数据
mysqldump -u 用户名 -p 数据库名 [表名] > 导出的文件名
mysqldump -uroot -p test mytable > mytable.20140921.bak.sql

（2）恢复：导入备份数据
mysql -uroot -p test < mytable.20140921.bak.sql

（3）恢复：导入备份数据之后发送的写操作。先使用mysqlbinlog导出这部分写操作SQL(基于时间点或位置)
// 导出2014-09-21 09:59:59之后的binlog：
mysqlbinlog --database="test" --start-date="2014-09-21 09:59:59" /var/lib/mysql/mybinlog.000001 > binlog.data.sql

// 导出起始id为123456之后的binlog：
mysqlbinlog --database="test" --start-position="123456" /var/lib/mysql/mybinlog.000001 > binlog.data.sql

// 把要恢复的binlog导入db
mysql -uroot -p test < binlog.data.sql


## MySQL一行记录的最大长度是多少？
MySQL表中一行的长度不能超过65535字节，VARCHAR(N)使用额外的1到2字节来存储值的长度，如果N<=255，则使用一个字节，否则使用两个字节；如果表格的编码为UTF8（一个字符占3个字节），那么VARCHAR(255)占用的字节数为255 * 3 + 2 = 767，这样，一行就最多只能有65535 / 765 = 85个VARCHAR(255)类型的列。

MySQL中有哪几种事务隔离级别？
1）READ UNCOMMITTED		# 可读取其他事务未提交的数据（脏读）
2）READ COMMITTED		# 只能读取已提交的数据，但是不可重复读（避免脏读）
3）REPEATABLE READ		# 可重复读
// 用户A查询完之后，用户B将无法更新用户A所查询到的数据集中的任何数据（但是可以更新、插入和删除用户A查询到的数据集之外的数据），直到用户A事务结束才可以进行更新，这样就有效的防止了用户在同一个事务中读取到不一致的数据。
	4）SERIALIZABLE			# 事务串行化，必须等待当前事务执行完，其他事务才可以执行写操作，有多个事务同时设置SERIALIZABLE时会产生死锁：
ERROR 1213 (40001): Deadlock found when trying to get lock; try restarting transaction
这是四个隔离级别中限制最大的级别。因为并发级别较低，所以应只在必要时才使用该选项。

使用事务时设置级别：
	START TRANSACTION
SET [SESSION | GLOBAL] TRANSACTION ISOLATION LEVEL {READ UNCOMMITTED | READ COMMITTED | REPEATABLE READ | SERIALIZABLE}
COMMIT
ROLLBACK

## 什么是MySQL的行级锁？
如果是InnoDB引擎，就可以在事务里使用行锁，格式为：
SELECT xx FROM xx [FORCE INDEX(PRIMARY)] WHERE xx FOR UPDATE 
被加锁的行，其他事务也能读取但如果想写的话就必须等待锁的释放。
只有查询用到的是主键索引或满足最左前缀的主键索引的一部分，并且具有明确的值，如：
索引列=值、索引列=值1 or 索引列=值2、索引列 IN(值1,值2)
才能实现行锁定，否则就会锁表。
注：非主键索引会锁表，如果有多个索引可指定用主键索引（FORCE INDEX(PRIMARY)），以免锁表。


## ON DUPLICATE KEY UPDATE语句的作用是什么？
INSERT INTO ... ON DUPLICATE KEY UPDATE col=VALUES(col)		# VALUES用来取插入的值，存在主键冲突时就更新，没有删除操作

例：更新统计表
老做法是写三条sql语句:
select * from player_count where player_id = 1;					# 查询统计表中是否有记录
insert into player_count(player_id,count) value(1,1);				# 没有记录就执行insert 操作
update player_count set count = count+1 where player_id = 1;		# 有记录就执行update操作
这种写法比较麻烦，用on duplicate key update 的做法如下：
insert into player_count(player_id,count) value(1,1) on duplicate key update count=count+1;


## ON与WHERE有什么区别？
执行连接操作时，可先用ON先进行过滤，减少连接操作的中间结果，然后用WHERE对连接产生的结果再一次过滤。但是，如果是左/右连接，在ON条件里对主表的过滤是无效的，仍然会用到主表的所有记录，连接产生的记录如果不满足主表的过滤条件那么从表部分的数据会置为NULL。

## MySQL如何创建分区表？
分区是一种粗粒度，简易的索引策略，适用于大数据的过滤场景。对于大数据(如10TB)而言，索引起到的作用相对小，因为索引的空间与维护成本很高，另外如果不是索引覆盖查询，将导致回表，造成大量磁盘IO。
分区表分为RANGE、LIST、HASH、KEY四种类型,并且分区表的索引是可以局部针对分区表建立的。
用RANGE创建分区表：
CREATE TABLE sales (
    id INT AUTO_INCREMENT,
    amount DOUBLE NOT NULL,
    order_day DATETIME NOT NULL,
    PRIMARY KEY(id, order_day)
) ENGINE=Innodb PARTITION BY RANGE(YEAR(order_day)) (
    PARTITION p_2010 VALUES LESS THAN (2010),
    PARTITION p_2011 VALUES LESS THAN (2011),
    PARTITION p_2012 VALUES LESS THAN (2012),
    PARTITION p_catchall VALUES LESS THAN MAXVALUE);
如果这么做，则order_day必须包含在主键中，且会产生一个问题：当年份超过阈值，到了2013，2014时需要手动创建这些分区，更好的方法是使用HASH：
CREATE TABLE sales (
    id INT PRIMARY KEY AUTO_INCREMENT,
    amount DOUBLE NOT NULL,
    order_day DATETIME NOT NULL
) ENGINE=Innodb PARTITION BY HASH(id DIV 1000000);
这种分区表示每100W条数据建立一个分区，且没有阈值范围的影响。

如果想为一个表创建分区，这个表最多只能有一个唯一索引（主键也是唯一索引）。如果没有唯一索引，可指定任何一列为分区列；否则就只能指定唯一索引中的任何一列为分区列。查询时需用到分区的列，不然会遍历所有的分区，比不分区的查询效率还低，MySQL支持子分区。

在表建立后也可以新增、删除、合并分区。


## 如何在查询时重新定义数值类型？
使用CASE来重新定义数值类型
SELECT id,title,(CASE date WHEN '0000-00-00' THEN '' ELSE date END) AS date
FROM your_table
  
SELECT id,title,
(CASE status WHEN 0 THEN 'open' WHEN 1 THEN 'close' ELSE 'standby' END) AS status
FROM your_table

## 如何为SELECT语句添加一个自动增加的列？
set @N = 0;
SELECT @N := @N +1 AS number, name, surname FROM gbtags_users;


## 什么是B树？为什么可以使用B数来做数据库的索引？
B树是对二叉查找树的改进。它的设计思想是，将相关数据尽量集中在一起，以便一次读取多个数据，减少硬盘操作次数。

特点如下：
（1）一个节点可以容纳多个值。比如上图中，最多的一个节点容纳了4个值。
（2）除非数据已经填满，否则不会增加新的层。也就是说，B树追求"层"越少越好。
（3）子节点中的值，与父节点中的值，有严格的大小对应关系。一般来说，如果父节点有a个值，那么就有a+1个子节点。比如上图中，父节点有两个值（7和16），就对应三个子节点，第一个子节点都是小于7的值，最后一个子节点都是大于16的值，中间的子节点就是7和16之间的值。
这种数据结构，非常有利于减少读取硬盘的次数。假定一个节点可以容纳100个值，那么3层的B树可以容纳100万个数据，如果换成二叉查找树，则需要20层！假定操作系统一次读取一个节点，并且根节点保留在内存中，那么B树在100万个数据中查找目标值，只需要读取两次硬盘。
数据库以B树格式储存，只解决了按照"主键"查找数据的问题。如果想查找其他字段，就需要建立索引（index）。所谓索引，就是以某个字段为关键字的B树文件（索引的一种）。

## 如何判断一个链表是否存在回路?
给指针加一个标志域，如访问过则置1.当遍历到标志为1的项说明有了回路。
定义2个指针，一快(fast)一慢(slow)，即：从头向后遍历过程中，每循环一次，快指针一次向后移动2个元素，慢指针移动一个元素，每次判断(   fast==slow   ||   slow==fast->nest   ),如果成立，说明慢指针赶上了快指针，则为循环链表，否则，如果有一个指针到达NULL，则为单链表。

## 给你两个有序链表，编写一个函数，把两个链表合并成一个新的有序链表，返回头指针。（要求一分钟内给出两个算法）
递归算法所体现的“重复”一般有三个要求： 
　　一是每次调用在规模上都有所缩小(通常是减半)； 
　　二是相邻两次重复之间有紧密的联系，前一次要为后一次做准备(通常前一次的输出就作为后一次的输入)； 
三是在问题的规模极小时必须用直接给出解答而不再进行递归调用，因而每次递归调用都是有条件的(以规模未达到直接解答的大小为条件)，无条件递归调用将会成为死循环而不能正常结束。
如何设计递归算法
　　1.确定递归公式 
　　2.确定边界(终了)条件递归实现：
①算法思想：
	递归终止条件：若head1为空，返回head2指针（head）；若head2为空，返回head1指针（head）
	递归过程：
	（1）若head1->data > head2->data; 
		head 指针应该指向head2所指向的节点，而且head->next应该指向head1和head2->next两个链表的合成序列的头指针；
	（2）否则head 指针应该指向head1所指向的节点，而且head->next应该指向head->next和head2两个链表的合成序列的头指针；
②实现代码（C++）：  
```c++  
	#include <iostream>
	using namespace std;
	
	/*节点的类定义*/
	class Node
	{
		public:
 			int data;
 			Node * next;
 			Node(int data)
 			{
  				this->data=data;
 			}
	};

 	/*链表的类定义*/
	class LinkedList
	{
		public:
 			Node * head;
		/*用一个整形数组作为参数的构造函数*/
 		LinkedList(int array[])
 		{
  			head=new Node(array[0]);
 	 		Node * temp=head;
  			int i;
  			for(i=1;i<3;i++)
  			{
   				temp->next=new Node(array[i]);
   				temp=temp->next;
  			}
  			temp->next=NULL;
 		}
	};

	/*递归的合并两个有序链表*/
	Node * mergeLinkedList(Node * head1,Node * head2)   
	{   
   		Node *p=NULL;   
   		if(head1==NULL && head2==NULL)   
       		return p;   
   		else if(head1==NULL)   
       		return head2;   
   		else if(head2==NULL)   
       		return head1;   
   		else  
   		{   
       		if(head1->data < head2->data)   
       		{   
          	 	p = head1;   
           		p->next = mergeLinkedList(head1->next,head2);   
       		}   
       		else  
    			{
          		p = head2;   
    				p->next = mergeLinkedList(head1,head2->next);   
    			}   
       		return p;   
   		}   
	} 

	/*打印链表的所有元素*/
	void printList(Node * head)
	{
 		Node * temp=head;
 		while(temp!=NULL)
 		{
  			cout<<temp->data<<"  ";
  			temp=temp->next;
 		}
	}

	int main()
	{
 		int array1[3]={2,5,8};
 		int array2[3]={1,6,7};

  		/*构造两个有序链表--list1和list2*/
 		LinkedList list1(array1);
		LinkedList list2(array2);

		/*递归的将这两个有序链表合并成一个有序链表*/
 		Node * new_head=mergeLinkedList(list1.head,list2.head);
	
		/*打印有序链表*/
 		printList(new_head);
		return 0;
	}
```

## 写出斐波那契数列的递归与迭代代码，并分析时间和空间复杂度。
斐波那契数列指的是这样一个数列：1、1、2、3、5、8、13、21、……     
用数学公式表示出来就是：
          F（1）= 1，F（2）=1     (n=1,2)
          F(n)=F(n-1)+ F(n-2)      (n>2)
有三种比较常用的求解第n项斐波那契数列的方法：递归法、迭代法、通项公式法。
	①递归法：
		Fib(1) = 1 [基本情况]  
		Fib(2) = 1 [基本情况] 
		对所有n > 1的整数：Fib(n) = (Fib(n-1) + Fib(n-2)) [递归定义]
	关键代码：
```c++
		if(n == 1|| n== 2)
		{
    			return 1;
		}
		else
		{
    			return fib(n - 1) + fib(n - 2);
		}
```
	③迭代法：这种方法相对于递归法来说在时间复杂度上减小了不少，但代码相对就要复杂些了。
```c++
		#include<stdio.h>
		int f(int n);
		int main()
		{
 			int n;
    			scanf("%d",&n);
 			f(n);
		}

		int f(int n)
		{
 			int i,f1=1,f2=1,f3;

 			if(n<=0)
 			{
  				printf("输入错误.\n");
    			}
 			else if(n==1||n==2)
 			{
  				printf("1");
 			}
 			else
 			{
  				for(i=0;i<n-2;i++)
  				{
            			f3=f1+f2;           //f1表示当前的值
   					f2=f1;
   					f1=f3;
  				}	
  				printf("%d\n",f1);
 			}
		}
```

## 如何对递归程序进行时间复杂度分析？
例子：求N!。 这是一个简单的"累乘"问题，用递归算法也能解决。 
    n! = n * (n - 1)!   n > 1 
    0! = 1, 1! = 1      n = 0,1 
    因此，递归算法如下： 
   	fact(int n) {  
    		if(n == 0 || n == 1)   
         		return 1;  
        	else   
             	return n * fact(n - 1);  
    }  
    以n=3为例，看运行过程如下： 
    fact(3) ----- fact(2) ----- fact(1) ------ fact(2) -----fact(3) 
    ------------------------------>  ------------------------------> 
                递归                            回溯 
  递归算法在运行中不断调用自身降低规模的过程，当规模降为1，即递归到fact(1)时，满足停止条件停止递归，开始回溯(返回调用算法)并计算，从fact(1)=1计算返回到fact(2);计算2*fact(1)=2返回到fact(3)；计算3*fact(2)=6，结束递归。
递归算法的分析方法比较多，最常用的便是迭代法。 
  	迭代法的基本步骤是先将递归算法简化为对应的递归方程，然后通过反复迭代，将递归方程的右端变换成一个级数，最后求级数的和，再估计和的渐进阶。 
  	<1> 例：n! 
       算法的递归方程为： T(n) = T(n - 1) + O(1); 
       迭代展开： T(n) = T(n - 1) + O(1) 
                       = T(n - 2) + O(1) + O(1) 
                       = T(n - 3) + O(1) + O(1) + O(1) 
                       = ...... 
                       = O(1) + ... + O(1) + O(1) + O(1) 
                       = n * O(1) 
                       = O(n) 
      这个例子的时间复杂性是线性的。 
	<2> 例：如下递归方程： 
      T(n) = 2T(n/2) + 2, 且假设n=2的k次方。 
      T(n) = 2T(n/2) + 2 
           = 2(2T(n/2*2) + 2) + 2 
           = 4T(n/2*2) + 4 + 2 
           = 4(2T(n/2*2*2) + 2) + 4 + 2 
           = 2*2*2T(n/2*2*2) + 8 + 4 + 2 
           = ... 
           = 2的(k-1)次方 * T(n/2的(i-1)次方) + $(i:1~(k-1))2的i次方 
           = 2的(k-1)次方 + (2的k次方)  - 2 
           = (3/2) * (2的k次方) - 2 
           = (3/2) * n - 2 
           = O(n) 
      这个例子的时间复杂性也是线性的。 
	<3> 例：如下递归方程： 
      T(n) = 2T(n/2) + O(n), 且假设n=2的k次方。 
      T(n) = 2T(n/2) + O(n) 
           = 2T(n/4) + 2O(n/2) + O(n) 
           = ... 
           = O(n) + O(n) + ... + O(n) + O(n) + O(n) 
           = k * O(n) 
           = O(k*n) 
           = O(nlog2n) //以2为底 
     
      一般地，当递归方程为T(n) = aT(n/c) + O(n), T(n)的解为： 
      O(n)          (a<c && c>1) 
      O(nlog2n)     (a=c && c>1) //以2为底 
      O(nlogca)     (a>c && c>1) //n的(logca)次方，以c为底 
   上面介绍的3种递归调用形式，比较常用的是第一种情况，第二种形式也有时出现，而第三种形式(间接递归调用)使用的较少，且算法分析 比较复杂。下面举个第二种形式的递归调用例子。 
  	<4> 递归方程为：T(n) = T(n/3) + T(2n/3) + n 
     为了更好的理解，先画出递归过程相应的递归树： 
                            n                        --------> n 
                    n/3            2n/3              --------> n 
              n/9       2n/9   2n/9     4n/9         --------> n 
           ......     ......  ......  .......        ...... 
                                                     -------- 
                                                     总共O(nlogn) 
     累计递归树各层的非递归项的值，每一层和都等于n，从根到叶的最长路径是： 
      n --> (2/3)n --> (4/9)n --> (12/27)n --> ... --> 1 
     设最长路径为k，则应该有：(2/3)的k次方 * n = 1 
     得到 k = log(2/3)n  // 以(2/3)为底 
     于是 T(n) <= (K + 1) * n = n (log(2/3)n + 1) 
     即 T(n) = O(nlogn) 
   由此例子表明，对于第二种递归形式调用，借助于递归树，用迭代法进行算法分析是简单易行的。

## 说明链表和数组作为数据的不同组织形式，各自的优缺点。
　数组，在内存上给出了连续的空间。链表，内存地址上可以是不连续的，每个链表的节点包括原来的内存和下一个节点的信息(单向的一个，双向链表的话，会有两个)。
　　数组优于链表的:
　　A. 内存空间占用的少，因为链表节点会附加上一块或两块下一个节点的信息。
　　但是数组在建立时就固定了。所以也有可能会因为建立的数组过大或不足引起内存上的问题。
　　B. 数组内的数据可随机访问，但链表不具备随机访问性。这个很容易理解，数组在内存里是连续的空间，比如如果一个数组地址从100到200，且每个元素占用两个字节，那么100-200之间的任何一个偶数都是数组元素的地址，可以直接访问。
　　链表在内存地址可能是分散的。所以必须通过上一节点中的信息找能找到下一个节点。
　　C. 查找速度上。这个也是因为内存地址的连续性的问题，不罗索了。
　　链表优于数组的:
　　A. 插入与删除的操作。如果数组的中间插入一个元素，那么这个元素后的所有元素的内存地址都要往后移动。删除的话同理。只有对数据的最后一个元素进行插入删除操作时，才比较快。链表只需要更改有必要更改的节点内的节点信息就够了。并不需要更改节点的内存地址。
　　B. 内存地址的利用率方面。不管你内存里还有多少空间，如果没办法一次性给出数组所需的要空间，那就会提示内存不足，磁盘空间整理的原因之一在这里。而链表可以是分散的空间地址。
　　C. 链表的扩展性比数组好。因为一个数组建立后所占用的空间大小就是固定的，如果满了就没法扩展，只能新建一个更大空间的数组;而链表不是固定的，可以很方便的扩展。


## 统计海量数据中的前10个热门数据
搜索引擎会通过日志文件把用户每次检索使用的所有检索串都记录下来，每个查询串的长度为1-255字节。假设目前有一千万个记录（这些查询串的重复度比较高，虽然总数是1千万，但如果除去重复后，不超过3百万个。一个查询串的重复度越高，说明查询它的用户越多，也就是越热门。），请你统计最热门的10个查询串，要求使用的内存不能超过1G。
	外排序（External sorting）是指能够处理极大量数据的排序算法。通常来说，外排序处理的数据不能一次装入内存，只能放在读写较慢的外存储器（通常是硬盘）上。外排序通常采用的是一种“排序-归并”的策略。在排序阶段，先读入能放在内存中的数据量，将其排序输出到一个临时文件，依此进行，将待排序数据组织为多个有序的临时文件。尔后在归并段阶将这些临时文件组合为一个大的有序文件，也即排序结果。
	外归并排序：外排序的一个例子是外归并排序（External merge sort），它读入一些能放在内存内的数据量，在内存中排序后输出为一个顺串（即是内部数据有序的临时文件），处理完所有的数据后再进行归并。比如，要对 900 MB 的数据进行排序，但机器上只有 100 MB 的可用内存时，外归并排序按如下方法操作：
	1.读入 100 MB 的数据至内存中，用某种常规方式（如快速排序、堆排序、归并排序等方法）在内存中完成排序。 
	2.将排序完成的数据写入磁盘。 
	3.重复步骤 1 和 2 直到所有的数据都存入了不同的 100 MB 的块（临时文件）中。在这个例子中，有 900 MB 数据，单个临时文件大小为 100 MB，所以会产生 9 个临时文件。 
	4.读入每个临时文件（顺串）的前 10 MB （ = 100 MB / (9 块 + 1)）的数据放入内存中的输入缓冲区，最后的 10 MB 作为输出缓冲区。（实践中，将输入缓冲适当调小，而适当增大输出缓冲区能获得更好的效果。） 
	5.执行九路归并算法，将结果输出到输出缓冲区。一旦输出缓冲区满，将缓冲区中的数据写出至目标文件，清空缓冲区。直至所有数据归并完成。

要统计最热门查询，首先就是要统计每个Query出现的次数，然后根据统计结果，找出Top 10。所以我们可以基于这个思路分两步来设计该算法。
    即，此问题的解决分为以下俩个步骤：
第一步：Query统计
    Query统计有以下俩个方法，可供选择：
    1、直接排序法
    首先我们最先想到的的算法就是排序了，首先对这个日志里面的所有Query都进行排序，然后再遍历排好序的Query，统计每个Query出现的次数了。但是题目中有明确要求，那就是内存不能超过1G，一千万条记录，每条记录是255Byte，很显然要占据2.375G内存，这个条件就不满足要求了。
	让我们回忆一下数据结构课程上的内容，当数据量比较大而且内存无法装下的时候，我们可以采用外排序的方法来进行排序，这里我们可以采用归并排序，因为归并排序有一个比较好的时间复杂度O(NlgN)。排完序之后我们再对已经有序的Query文件进行遍历，统计每个Query出现的次数，再次写入文件中。综合分析一下，排序的时间复杂度是O(NlgN)，而遍历的时间复杂度是O(N)，因此该算法的总体时间复杂度就是O(N+NlgN)=O（NlgN）。
    2、Hash Table法
    在第1个方法中，我们采用了排序的办法来统计每个Query出现的次数，时间复杂度是NlgN，那么能不能有更好的方法来存储，而时间复杂度更低呢？
	题目中说明了，虽然有一千万个Query，但是由于重复度比较高，因此事实上只有300万的Query，每个Query255Byte，因此我们可以考虑把他们都放进内存中去，而现在只是需要一个合适的数据结构，在这里，Hash Table绝对是我们优先的选择，因为Hash Table的查询速度非常的快，几乎是O(1)的时间复杂度。
	那么，我们的算法就有了：维护一个Key为Query字串，Value为该Query出现次数的HashTable，每次读取一个Query，如果该字串不在Table中，那么加入该字串，并且将Value值设为1；如果该字串在Table中，那么将该字串的计数加一即可。最终我们在O(N)的时间复杂度内完成了对该海量数据的处理。
	本方法相比算法1：在时间复杂度上提高了一个数量级，为O（N），但不仅仅是时间复杂度上的优化，该方法只需要IO数据文件一次，而算法1的IO次数较多的，因此该算法2比算法1在工程上有更好的可操作性。

第二步：找出Top 10
    算法一：普通排序
    我想对于排序算法大家都已经不陌生了，这里不在赘述，我们要注意的是排序算法的时间复杂度是NlgN，在本题目中，三百万条记录，用1G内存是可以存下的。
	算法二：部分排序
    题目要求是求出Top 10，因此我们没有必要对所有的Query都进行排序，我们只需要维护一个10个大小的数组，初始化放入10个Query，按照每个Query的统计次数由大到小排序，然后遍历这300万条记录，每读一条记录就和数组最后一个Query对比，如果小于这个Query，那么继续遍历，否则，将数组中最后一条数据淘汰，加入当前的Query。最后当所有的数据都遍历完毕之后，那么这个数组中的10个Query便是我们要找的Top10了。
		不难分析出，这样，算法的最坏时间复杂度是N*K， 其中K是指top多少。
	算法三：堆
    在算法二中，我们已经将时间复杂度由NlogN优化到NK，不得不说这是一个比较大的改进了，可是有没有更好的办法呢？
		分析一下，在算法二中，每次比较完成之后，需要的操作复杂度都是K，因为要把元素插入到一个线性表之中，而且采用的是顺序比较。这里我们注意一下，该数组是有序的，一次我们每次查找的时候可以采用二分的方法查找，这样操作的复杂度就降到了logK，可是，随之而来的问题就是数据移动，因为移动数据次数增多了。不过，这个算法还是比算法二有了改进。
	基于以上的分析，我们想想，有没有一种既能快速查找，又能快速移动元素的数据结构呢？回答是肯定的，那就是堆。
    借助堆结构，我们可以在log量级的时间内查找和调整/移动。因此到这里，我们的算法可以改进为这样，维护一个K(该题目中是10)大小的小根堆，然后遍历300万的Query，分别和根元素进行对比。
	思想与上述算法二一致，只是算法在算法三，我们采用了最小堆这种数据结构代替数组，把查找目标元素的时间复杂度有O（K）降到了O（logK）。
    那么这样，采用堆数据结构，算法三，最终的时间复杂度就降到了N‘logK，和算法二相比，又有了比较大的改进。
总结：
    至此，算法就完全结束了，经过上述第一步、先用Hash表统计每个Query出现的次数，O（N）；然后第二步、采用堆数据结构找出Top 10，N*O（logK）。所以，我们最终的时间复杂度是：O（N） + N'*O（logK）。（N为1000万，N’为300万）。


## 海量日志数据，提取出某日访问百度次数最多的那个IP。
      首先是这一天，并且是访问百度的日志中的IP取出来，逐个写入到一个大文件中。注意到IP是32位的，最多有个2^32个IP。同样可以采用映射的方法，比如模1000，把整个大文件映射为1000个小文件，再找出每个小文中出现频率最大的IP（可以采用hash_map进行频率统计，然后再找出频率最大的几个）及相应的频率。然后再在这1000个最大的IP中，找出那个频率最大的IP，即为所求。
或者如下阐述：
算法思想：分而治之+Hash
1.IP地址最多有2^32=4G种取值情况，所以不能完全加载到内存中处理； 
2.可以考虑采用“分而治之”的思想，按照IP地址的Hash(IP)%1024值，把海量IP日志分别存储到1024个小文件中。这样，每个小文件最多包含4MB个IP地址； 
3.对于每一个小文件，可以构建一个IP为key，出现次数为value的Hash map，同时记录当前出现次数最多的那个IP地址；
4.可以得到1024个小文件中的出现次数最多的IP，再依据常规的排序算法得到总体上出现次数最多的IP；


## 有一个庞大的字符串数组，然后给你一个单独的字符串，让你从这个数组中查找是否有这个字符串并找到它，你会怎么做？
	有一个方法最简单，老老实实从头查到尾，一个一个比较，直到找到为止，我想只要学过程序设计的人都能把这样一个程序作出来，但要是有程序员把这样的程序交给用户，我只能用无语来评价，或许它真的能工作，但...也只能如此了。
所谓Hash，一般是一个整数，通过某种算法，可以把一个字符串"压缩" 成一个整数。当然，无论如何，一个32位整数是无法对应回一个字符串的，但在程序中，两个字符串计算出的Hash值相等的可能非常小。
是不是把第一个算法改进一下，改成逐个比较字符串的Hash值就可以了呢，答案是，远远不够，要想得到最快的算法，就不能进行逐个的比较，通常是构造一个哈希表(Hash Table)来解决问题，哈希表是一个大数组，这个数组的容量根据程序的要求来定义，例如1024，每一个Hash值通过取模运算 (mod) 对应到数组中的一个位置，这样，只要比较这个字符串的哈希值对应的位置有没有被占用，就可以得到最后的结果了，想想这是什么速度？是的，是最快的O(1)。
冲突解决：分离链接法，用链表解决冲突。
一个好的hash函数：
/*key为一个字符串，nTableLength为哈希表的长度
*该函数得到的hash值分布比较均匀*/
unsigned long getHashIndex( const char *key, int nTableLength )
{
    unsigned long nHash = 0;
    while (*key)
    {
        nHash = (nHash<<5) + *key++;  // nHash = nHash*32 + *key;  key++
    }
    return ( nHash % nTableLength );
} 

## 有一个1G大小的一个文件，里面每一行是一个词，词的大小不超过16字节，内存限制大小是1M。返回频数最高的100个词。
    方案：顺序读文件中，对于每个词x，取hash(x)%5000，然后按照该值存到5000个小文件（记为x0,x1,...x4999）中。这样每个文件大概是200k左右。
    如果其中的有的文件超过了1M大小，还可以按照类似的方法继续往下分，直到分解得到的小文件的大小都不超过1M。
对每个小文件，统计每个文件中出现的词以及相应的频率（可以采用trie树/hash_map等），并取出出现频率最大的100个词（可以用含100个结点的最小堆），并把100个词及相应的频率存入文件，这样又得到了5000个文件。下一步就是把这5000个文件进行归并（类似与归并排序）的过程了。


## 有10个文件，每个文件1G，每个文件的每一行存放的都是用户的query，每个文件的query都可能重复。要求你按照query的频度排序。
    还是典型的TOP K算法，解决方案如下：
    方案1：
    顺序读取10个文件，按照hash(query)%10的结果将query写入到另外10个文件（记为）中。这样新生成的文件每个的大小大约也1G（假设hash函数是随机的）。
    找一台内存在2G左右的机器，依次对用hash_map(query, query_count)来统计每个query出现的次数。利用快速/堆/归并排序按照出现次数进行排序。将排序好的query和对应的query_cout输出到文件中。这样得到了10个排好序的文件（记为）。
    对这10个文件进行归并排序（内排序与外排序相结合）。
    方案2：
     一般query的总量是有限的，只是重复的次数比较多而已，可能对于所有的query，一次性就可以加入到内存了。这样，我们就可以采用trie树/hash_map等直接来统计每个query出现的次数，然后按出现次数做快速/堆/归并排序就可以了。
    方案3：
与方案1类似，但在做完hash，分成多个文件后，可以交给多个文件来处理，采用分布式的架构来处理（比如MapReduce），最后再进行合并。


## 给定a、b两个文件，各存放50亿个url，每个url各占64字节，内存限制是4G，让你找出a、b文件共同的url？
    方案1：可以估计每个文件的大小为5G×64=320G，远远大于内存限制的4G。所以不可能将其完全加载到内存中处理。考虑采取分而治之的方法。
    遍历文件a，对每个url求取hash(url)%1000，然后根据所取得的值将url分别存储到1000个小文件（记为a0,a1,...,a999）中。这样每个小文件的大约为300M。
    遍历文件b，采取和a相同的方式将url分别存储到1000小文件（记为b0,b1,...,b999）。这样处理后，所有可能相同的url都在对应的小文件（a0vsb0,a1vsb1,...,a999vsb999）中，不对应的小文件不可能有相同的url。然后我们只要求出1000对小文件中相同的url即可。
    求每对小文件中相同的url时，可以把其中一个小文件的url存储到hash_set（STL）中。然后遍历另一个小文件的每个url，看其是否在刚才构建的hash_set中，如果是，那么就是共同的url，存到文件里面就可以了。
    方案2：如果允许有一定的错误率，可以使用Bloom filter，4G内存大概可以表示340亿bit。将其中一个文件中的url使用Bloom filter映射为这340亿bit，然后挨个读取另外一个文件的url，检查是否与Bloom filter，如果是，那么该url应该是共同的url（注意会有一定的错误率）。
Bloom filter日后会在本BLOG内详细阐述。
注：hash_set类是外部STL中的类，用于向收集器中存储或快速检索数据。收集器中的这些数据唯一，且数值充当关键字。
Bloom filter：Bloom Filter是一种空间效率很高的随机数据结构，它利用位数组很简洁地表示一个集合，并能判断一个元素是否属于这个集合。Bloom Filter的这种高效是有一定代价的：在判断一个元素是否属于某个集合时，有可能会把不属于这个集合的元素误认为属于这个集合（false positive）。因此，Bloom Filter不适合那些“零错误”的应用场合。而在能容忍低错误率的应用场合下，Bloom Filter通过极少的错误换取了存储空间的极大节省。Bloom filter 采用的是哈希函数的方法，将一个元素映射到一个 m 长度的阵列上的一个点，当这个点是 1 时，那么这个元素在集合内，反之则不在集合内。这个方法的缺点就是当检测的元素很多的时候可能有冲突，解决方法就是使用 k 个哈希 函数对应 k 个点，如果所有点都是 1 的话，那么元素在集合内，如果有 0 的话，元素则不再集合内。随着元素的插入，Bloom filter 中修改的值变多，出现误判的几率也随之变大，当新来一个元素时，满足其在集合内的条件，即所有对应位都是 1 ，这样就可能有两种情况，一是这个元素就在集合内，没有发生误判；还有一种情况就是发生误判，出现了哈希碰撞，这个元素本不在集合内。


## 在2.5亿个整数中找出不重复的整数，注，内存不足以容纳这2.5亿个整数。
    方案1：采用2-Bitmap（每个数分配2bit，00表示不存在，01表示出现一次，10表示多次，11无意义）进行，共需内存2^32 * 2 bit=1 GB内存，还可以接受。然后扫描这2.5亿个整数，查看Bitmap中相对应位，如果是00变01，01变10，10保持不变。所描完事后，查看bitmap，把对应位是01的整数输出即可。
    方案2：也可采用与第1题类似的方法，进行划分小文件的方法。然后在小文件中找出不重复的整数，并排序。然后再进行归并，注意去除重复的元素。
BitMap算法：来自于《编程珠玑》。所谓的Bit-map就是用一个bit位来标记某个元素对应的Value， 而Key即是该元素。由于采用了Bit为单位来存储数据，因此在存储空间方面，可以大大节省。 
　　如果说了这么多还没明白什么是Bit-map，那么我们来看一个具体的例子，假设我们要对0-7内的5个元素(4,7,2,5,3)排序（这里假设这些元素没有重复）。那么我们就可以采用Bit-map的方法来达到排序的目的。要表示8个数，我们就只需要8个Bit（1Bytes），首先我们开辟1Byte的空间，将这些空间的所有Bit位都置为0 
　　然后遍历这5个元素，首先第一个元素是4，那么就把4对应的位置为1（可以这样操作 p+(i/8)|(0×01<<(i%8)) 当然了这里的操作涉及到Big-ending和Little-ending的情况，这里默认为Big-ending）,因为是从零开始的，所以要把第五位置为1。 
　　然后再处理第二个元素7，将第八位置为1,，接着再处理第三个元素，一直到最后处理完所有的元素，将相应的位置为1。 
然后我们现在遍历一遍Bit区域，将该位是一的位的编号输出（2，3，4，5，7），这样就达到了排序的目的。


## 100w个数中找出最大的100个数。
    方案1：在前面的题中，我们已经提到了，用一个含100个元素的最小堆完成。复杂度为O(100w*lg100)。
    方案2：采用快速排序的思想，每次分割之后只考虑比轴大的一部分，知道比轴大的一部分在比100多的时候，采用传统排序算法排序，取前100个。复杂度为O(100w*100)。
方案3：采用局部淘汰法。选取前100个元素，并排序，记为序列L。然后一次扫描剩余的元素x，与排好序的100个元素中最小的元素比，如果比这个最小的要大，那么把这个最小的元素删除，并把x利用插入排序的思想，插入到序列L中。依次循环，知道扫描了所有的元素。复杂度为O(100w*100)。

## 在一个有序数组中，有些元素重复出现。输入一个数值，求此值在数组中重复的次数
思路有两种:
1.	upperbound() – lowerbound()
2.	使用类似线段树的思想直接统计
iterator lower_bound( const key_type &key ): 返回一个迭代器，指向键值>= key的第一个元素。
iterator upper_bound( const key_type &key ):返回一个迭代器，指向键值> key的第一个元素。
例如：map中已经插入了1，2，3，4的话，如果lower_bound(2)的话，返回的2，而upper_bound（2）的话，返回的就是3

## 输入一个英文句子，翻转句子中单词的顺序，但单词内字符的顺序不变。
句子中单词以空格符隔开。为简单起见，标点符号和普通字母一样处理。
例如输入“I am a student.”，则输出“student. a am I”。
给出思路，并写出程序代码。
解答：
先颠倒句子中的所有字符，再颠倒每个单词内的字符。
如例句中，“I am a student.”第一次整体翻转后得到“.tneduts a ma I”；
第二次在每个单词中内部翻转得到“students. a am I”，即为题解。

## 输入一个整数数组，调整数组中数组的顺序，使得所有奇数位于数组的前半部分，所有偶数位于数组的后半部分，要求时间复杂度为O(n)
维护两个指针，第一个指针指向数组的第一个数字，只向后移动，第二个指针指向最后一个数字，只向前移动。当第一个指针指向数字为偶数，且第二个指针指向数字为奇数时，交换数字，并移动两个指针。


## 一个数组A[N]，包含取值为[1,N]的元素，请判断是否有重复元素
解法：
1、Sum(1…N)!=sum(A[0],A[N-1])则重复
2、hash记数法
3、排序后再判重

## 安装Nginx依赖哪些条件？
（1）编译环境gcc g++ 开发库之类的需要提前装好
（2）安装PCRE库，为了重写（rewrite）：PCRE(Perl Compatible Regular Expressions)是一个Perl库，包括perl兼容的正则表达式库。
（3）安装zlib库，为了gzip压缩。
（4）安装ssl

./configure --sbin-path=/usr/local/nginx/nginx 
--conf-path=/usr/local/nginx/nginx.conf 
--pid-path=/usr/local/nginx/nginx.pid 
--with-http_ssl_module
--with-pcre=/usr/local/src/pcre-8.34 			
--with-zlib=/usr/local/src/zlib-1.2.8 			
--with-openssl=/usr/local/src/openssl-1.0.1c


## Nginx的配置
nginx配置文件主要分为六个区域：
main
控制子进程的所属用户/用户组、派生子进程数、错误日志位置/级别、pid位置、子进程优先级、进程对应cpu、进程能够打开的文件描述符数目等
events
控制nginx处理连接的方式
（3）http
（4）sever
（5）location
（6）upstream

实例：

user www-data;    								# 运行用户
worker_processes  1;							# 启动进程数,通常设置成和cpu的数量相等
error_log  /var/log/nginx/error.log;			# 全局错误日志
pid        /var/run/nginx.pid;					# PID文件

// 工作模式及连接数上限
events {
use   epoll;             					# 使用epoll多路复用模式
worker_connections  1024;					# 单个后台worker process进程的最大并发链接数
    # multi_accept on; 
}

// 设定http服务器，利用它的反向代理功能提供负载均衡支持
http {
    # 设定mime类型,类型由mime.type文件定义
    include       /etc/nginx/mime.types;
default_type  application/octet-stream; 	# 1 octet = 8 bit

    # 设定访问日志
    access_log    /var/log/nginx/access.log;

    # sendfile指令指定nginx是否调用sendfile函数（zero copy方式）来输出文件，对于普通应用，必须设为on,如果用来进行下载等应用磁盘IO重负载应用，可设置为off，以平衡磁盘与网络I/O处理速度，降低系统的uptime.
    sendfile        on;
    #tcp_nopush     on;					# 在一个数据包里发送所有头文件，而不一个接一个的发送

    
    keepalive_timeout  65;				# 连接超时时间
    tcp_nodelay        on;				# 作用于socket参数TCP_NODELAY，禁用nagle算法，也即不缓存数据
    
    # 开启gzip压缩
    gzip  on;
    gzip_disable "MSIE [1-6]\.(?!.*SV1)";

    # 设定请求缓冲
    client_header_buffer_size    1k;
    large_client_header_buffers  44k;

    include /etc/nginx/conf.d/*.conf;
    include /etc/nginx/sites-enabled/*;

    # 设定负载均衡的服务器列表
    upstream mysvr {
    	# weigth参数表示权值，权值越高被分配到的几率越大
    	# 本机上的Squid开启3128端口
    	server 192.168.8.1:3128 	weight=5;
    	server 192.168.8.2:80  		weight=1;
    	server 192.168.8.3:80  		weight=6;
    }

   server {
        listen	80;						# 侦听80端口
        server_name  www.xx.com;		# 定义使用www.xx.com访问
        access_log  logs/www.xx.com.access.log  main;		# 设定本虚拟主机的访问日志

    # 默认请求
    location / {
		root   /root;      						# 定义服务器的默认网站根目录位置
		index index.php index.html index.htm;   # 定义首页索引文件的名称
fastcgi_pass  localhost:9000;				
    	fastcgi_param  SCRIPT_FILENAME  $document_root/$fastcgi_script_name; 
		include /etc/nginx/fastcgi_params;
	}

    # 定义错误提示页面
    error_page   500 502 503 504 /50x.html;  
        location = /50x.html {
        root   /root;
    }

    # 静态文件，nginx自己处理
    location ~ ^/(images|javascript|js|css|flash|media|static)/ {
        root /var/www/virtual/htdocs;
        # 过期时间30天
        expires 30d;
}

    # PHP脚本请求全部转发到FastCGI处理，使用FastCGI默认配置
    location ~ \.php$ {
        root /root;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME /home/www/www$fastcgi_script_name;
        include fastcgi_params;
}

    # 设定查看Nginx状态的地址
    location /NginxStatus {
        stub_status 			on;
        access_log              on;
        auth_basic              "NginxStatus";
        auth_basic_user_file  	conf/htpasswd;
}

    # 禁止访问 .htxxx 文件
    location ~ /\.ht {
        deny all;
    }
    }
}

## Nginx有哪些内置的全局变量？
$args						请求中的参数;
$content_length				HTTP请求信息里的"Content-Length";
$content_type				请求信息里的"Content-Type";
$document_root				针对当前请求的根路径设置值;
$document_uri				与$uri相同;
$host						http请求的域名
$http_user_agent			客户端agent信息;
$http_cookie				客户端cookie信息;
$limit_rate					对连接速率的限制;
$request_body_file			客户端请求主体信息的临时文件名;
$request_method				请求的方法，比如"GET"、"POST"等;
$remote_addr				客户端地址;
$remote_port				客户端端口号;
$remote_user				客户端用户名，认证用;
$request_filename			当前请求的文件路径名;
$request_body_file			客户端请求主体的临时文件名;
$request_uri				包含请求参数的原始URI，不包含主机名，如："/foo/bar.php?arg=baz";
$query_string				与$args相同;
$scheme						所用的协议，比如http或者是https;
$server_addr				服务器地址，如果没有用listen指明服务器地址，使用这个变量将发起一次系统调用以取得地址(造成资源浪费);
$server_name				请求到达的服务器名;
$server_port				请求到达的服务器端口号;
$uri						不带请求参数的当前URI，$uri不包含主机名，如"/foo/bar.html";

$fastcgi_script_name		这个变量等于一个以斜线结尾的请求URI加上fastcgi_index给定的参数。可以用这个变量代替SCRIPT_FILENAME 和PATH_TRANSLATED，以确定php脚本的名称。
如请求"/info/": 
 	fastcgi_index		index.php;  
fastcgi_param  		SCRIPT_FILENAME  	/home/www/scripts/php$fastcgi_script_name;
SCRIPT_FILENAME等于"/home/www/scripts/php/info/index.php"

## fastcgi_index配置的作用是什么？
语法：fastcgi_index file 
默认值：none 
使用字段：http, server, location 
如果URI以斜线结尾，文件名将追加到URI后面，这个值将存储在变量$fastcgi_script_name中。
例如：
fastcgi_index  index.php;
fastcgi_param  SCRIPT_FILENAME  /home/www/scripts/php$fastcgi_script_name;
请求"/page.php"的参数SCRIPT_FILENAME将被设置为"/home/www/scripts/php/page.php"，但是请求"/"则为"/home/www/scripts/php/index.php"。

## fastcgi_param配置的作用是什么？
语法：fastcgi_param parameter value 
默认值：none 
使用字段：http, server, location 
指定一些传递到FastCGI服务器的参数。可以使用字符串，变量，或者其组合，这里的设置不会继承到其他的字段，设置在当前字段会清除掉任何之前的定义。
下面是一个PHP需要使用的最少参数：
  	fastcgi_param  SCRIPT_FILENAME  	/home/www/scripts/php$fastcgi_script_name;  
fastcgi_param  QUERY_STRING     	$query_string;
PHP使用SCRIPT_FILENAME参数决定需要执行哪个脚本，QUERY_STRING包含请求中的某些参数。

如果要处理POST请求，则需要另外增加三个参数：
  	fastcgi_param  REQUEST_METHOD   $request_method;  
fastcgi_param  CONTENT_TYPE     $content_type;  
fastcgi_param  CONTENT_LENGTH   $content_length;

如果PHP在编译时带有--enable-force-cgi-redirect，则必须传递值为200的REDIRECT_STATUS参数：
fastcgi_param  REDIRECT_STATUS  200;

## fastcgi_pass配置的作用是什么？
语法：fastcgi_pass fastcgi-server 
默认值：none 
使用字段：http, server, location 
指定FastCGI服务器监听端口与地址。
可以是本机或者其它：
fastcgi_pass   localhost:9000;

使用Unix socket:
fastcgi_pass   unix:/tmp/fastcgi.socket;

同样可以使用一个upstream字段名称：
upstream backend  {  
server   localhost:1234;
} 
fastcgi_pass   backend;

## fastcgi_read_timeout配置的作用是什么？
语法：fastcgi_read_timeout time 
默认值：fastcgi_read_timeout 60 
使用字段：http, server, location 
前端FastCGI服务器的响应超时时间，如果有一些直到它们运行完才有输出的长时间运行的FastCGI进程，或者在错误日志中出现前端服务器响应超时错误，可能需要调整这个值。


## fastcgi_param的内容是什么？
即为fastcgi模块设置一些服务器环境变量：
fastcgi_param  SCRIPT_FILENAME    $document_root$fastcgi_script_name;			#脚本文件请求的路径
fastcgi_param  QUERY_STRING       $query_string; 					#请求的参数;如?app=123
fastcgi_param  REQUEST_METHOD     $request_method; 					#请求的动作(GET,POST)
fastcgi_param  CONTENT_TYPE       $content_type; 					#请求头中的Content-Type字段
fastcgi_param  CONTENT_LENGTH     $content_length; 					#请求头中的Content-length字段

fastcgi_param  SCRIPT_NAME        $fastcgi_script_name; 			#脚本名称 
fastcgi_param  REQUEST_URI        $request_uri; 					#请求的地址不带参数
fastcgi_param  DOCUMENT_URI       $document_uri; 					#与$uri相同。 
fastcgi_param  DOCUMENT_ROOT      $document_root; #网站的根目录。在server配置中root指令中指定的值 
fastcgi_param  SERVER_PROTOCOL    $server_protocol; 	#请求使用的协议，通常是HTTP/1.0或HTTP/1.1 

fastcgi_param  GATEWAY_INTERFACE  CGI/1.1;							#cgi 版本
fastcgi_param  SERVER_SOFTWARE    nginx/$nginx_version;				#nginx 版本号，可修改、隐藏

fastcgi_param  REMOTE_ADDR        $remote_addr; 					#客户端IP
fastcgi_param  REMOTE_PORT        $remote_port; 					#客户端端口
fastcgi_param  SERVER_ADDR        $server_addr; 					#服务器IP地址
fastcgi_param  SERVER_PORT        $server_port; 					#服务器端口
fastcgi_param  SERVER_NAME        $server_name; 	#服务器名，域名在server配置中指定的server_name

//fastcgi_param  PATH_INFO           $path_info;						#可自定义变量

// PHP only, required if PHP was built with --enable-force-cgi-redirect
// fastcgi_param  REDIRECT_STATUS    200;

在php可打印出上面的服务环境变量：
如：echo $_SERVER['REMOTE_ADDR']


## 如何使用Nginx实现负载均衡和反向代理？
设定http服务器，利用它的反向代理功能提供负载均衡支持
http {
     #设定mime类型,类型由mime.type文件定义
    include       /etc/nginx/mime.types;
    default_type  application/octet-stream;

    #设定日志格式
    access_log    /var/log/nginx/access.log;

    #其他配置，略

    #设定负载均衡的服务器列表
    upstream mysvr {
    	#weigth参数表示权值，权值越高被分配到的几率越大
    	server 192.168.8.1x:3128 weight=5;#本机上的Squid开启3128端口
    	server 192.168.8.2x:80  weight=1;
    	server 192.168.8.3x:80  weight=6;
    }

   	upstream mysvr2 {
    	#weigth参数表示权值，权值越高被分配到的几率越大
    	server 192.168.8.x:80  weight=1;
    	server 192.168.8.x:80  weight=6;
    }

   #第一个虚拟服务器
   server {
    	#侦听192.168.8.x的80端口
        listen       80;
        server_name  192.168.8.x;

      	#对aspx后缀的进行负载均衡请求
    	location ~ .*\.aspx$ {
         	root   /root;      						#定义服务器的默认网站根目录位置
          	index index.php index.html index.htm;   #定义首页索引文件的名称
          	proxy_pass  http://mysvr ;				#请求转向mysvr定义的服务器列表

          	# 反向代理的配置
          	proxy_redirect off;

          	#后端的Web服务器可以通过X-Forwarded-For获取用户真实IP
          	proxy_set_header Host $host;
          	proxy_set_header X-Real-IP $remote_addr;
          	proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
          	client_max_body_size 10m;    		#允许客户端请求的最大单文件字节数
          	client_body_buffer_size 128k;  		#缓冲区代理缓冲用户端请求的最大字节数
          	proxy_connect_timeout 90;  			#nginx跟后端服务器连接超时时间(代理连接超时)
          	proxy_send_timeout 90;        		#后端服务器数据回传时间(代理发送超时)
          	proxy_read_timeout 90;         		#连接成功后，后端服务器响应时间(代理接收超时)
          	proxy_buffer_size 4k;             #设置代理服务器（nginx）保存用户头信息的缓冲区大小
          	proxy_buffers 4 32k;         #proxy_buffers缓冲区，网页平均在32k以下的话，这样设置
          	proxy_busy_buffers_size 64k;    #高负荷下缓冲大小（proxy_buffers*2）
          	proxy_temp_file_write_size 64k;  #设定缓存文件夹大小，大于这个值，将从upstream服务器传
       }
	}
}


## 什么是Nginx重写？与Location有什么区别？Location的匹配规则是什么样的？
rewrite功能就是，使用nginx提供的全局变量或自己设置的变量，结合正则表达式和标志位实现url重写以及重定向。rewrite只能放在server{},location{},if{}中，并且只能对域名后边的除去传递的参数外的字符串起作用。如，http://seanlook.com/a/we/index.php?id=1&u=str 只对/a/we/index.php重写。
如果想对域名或参数字符串起作用，可以使用全局变量匹配，也可以使用proxy_pass反向代理。

Rewrite标志位：
last 			相当于Apache的[L]标记，表示完成rewrite
break			停止执行当前虚拟主机的后续rewrite指令集
redirect		返回302临时重定向，地址栏会显示跳转后的地址
permanent		返回301永久重定向，地址栏会显示跳转后的地址

Rewrite实例：
// 应用于Server
server {
listen 80;
server_name start.igrow.cn;
index index.html index.php;
root html;
if ($http_host !~ “^star\.igrow\.cn$&quot {
rewrite ^(.*) http://star.igrow.cn$1 redirect;
}
}

// 防盗链
location ~* \.(gif|jpg|swf)$ {
valid_referers none blocked start.igrow.cn sta.igrow.cn;
if ($invalid_referer) {
rewrite ^/ http://$host/logo.png;
}
}

// 根据文件类型设置过期时间
location ~* \.(js|css|jpg|jpeg|gif|png|swf)$ {
if (-f $request_filename) {
expires 1h;
break;
}
}

// 禁止访问某个目录
location ~* \.(txt|doc)${
root /data/www/wwwroot/linuxtone/test;
deny all;
}

rewrite和location：
rewrite和location都能实现跳转，主要区别在于rewrite是在同一域名内更改获取资源的路径，而location是对一类路径做控制访问或反向代理，可以proxy_pass到其他机器。很多情况下rewrite也会写在location里，它们的执行顺序是：
（1）执行server块的rewrite指令
（2）执行location匹配
（3）执行选定的location中的rewrite指令
如果其中某步URI被重写，则重新循环执行1-3，直到找到真实存在的文件；循环超过10次，则返回500 Internal Server Error错误。

正则匹配会覆盖普通匹配，location的执行逻辑跟location的编辑顺序无关。
语法格式：location [=|~|~*|^~|@] /uri/ { … } 

=		表示精确匹配
~ 		区分大小写匹配
~* 		不区分大小写匹配
!~		区分大小写不匹配
!~* 	不区分大小写不匹配
^ 		以什么开头的匹配
$ 		以什么结尾的匹配
^~ 		表示uri以某个常规字符串开头，不是正则匹配，优先级高于正则
/ 		通用匹配,如果没有其它匹配,任何请求都会匹配到
* 		代表任意字符

. 		匹配除换行符以外的任意字符
?		重复0次或1次
+		重复1次或更多次
*		重复0次或更多次
\d		匹配数字
{n}		重复n次
{n,}	重复n次或更多次
[c]		匹配单个字符c
[a-z]	匹配a-z小写字母的任意一个
\		转义字符

-f和!-f		判断是否存在文件
-d和!-d		判断是否存在目录
-e和!-e		判断是否存在文件或目录
-x和!-x		判断文件是否可执行


例：
实际使用中一般至少有三个匹配规则定义，如下：
/* 
直接匹配网站根，通过域名访问网站首页比较频繁，使用这个会加速处理，官网如是说。
 这里是直接转发给后端应用服务器了，也可以是一个静态首页
 第一个必选规则
*/
location = / {
    proxy_pass http://tomcat:8080/index
}

/*
 第二个必选规则是处理静态文件请求，这是nginx作为http服务器的强项
 有两种配置模式，目录匹配或后缀匹配,任选其一或搭配使用
*/
location ^~ /static/ {
    root /webroot/static/;
}
location ~* \.(gif|jpg|jpeg|png|css|js|ico)$ {
    root /webroot/res/;
}

/*
 第三个规则就是通用规则，用来转发动态请求到后端应用服务器
 非静态文件请求就默认是动态请求，自己根据实际把握
 毕竟目前的一些框架的流行，带.php,.jsp后缀的情况很少了
*/
location / {
    proxy_pass http://tomcat:8080/
}

## 为什么Nginx反向代理能够提高性能？
对于后端是动态服务来说，比如Java和PHP。这类服务器（如JBoss和PHP-FPM）的IO处理能力往往不高。Nginx有个好处是它会把Request在读取完整之前buffer住，这样交给后端的就是一个完整的HTTP请求，从而提高后端的效率，而不是断断续续的传递（互联网上连接速度一般比较慢）。同样，Nginx也可以把response给buffer住，同样也是减轻后端的压力。


## Nginx的模块及工作原理是怎样的？是如何有FastCGI配合的？
Nginx由内核和模块组成，内核的设计非常简洁，仅仅通过查找配置文件将客户端请求映射到一个location block（location是Nginx配置中的一个指令，用于URL匹配），而在这个location中所配置的每个指令将会启动不同的模块去完成相应的工作。

Nginx的模块直接被编译进Nginx，因此属于静态编译方式。启动Nginx后，Nginx的模块被自动加载，不像Apache首先将模块编译为一个so文件，然后在配置文件中指定是否进行加载。在解析配置文件时，Nginx的每个模块都有可能去处理某个请求，但是同一个处理请求只能由一个模块来完成。 

在工作方式上，Nginx分为单工作进程和多工作进程两种模式。在单工作进程模式下，除主进程外，还有一个工作进程，工作进程是单线程的；在多工作进程模式下，每个工作进程包含多个线程。Nginx默认为单工作进程模式。

Nginx不支持对外部程序的直接调用或者解析，所有的外部程序（包括PHP）必须通过FastCGI接口来调用。FastCGI接口在Linux下是socket（这个socket可以是文件socket，也可以是ip socket）。

为了调用CGI程序，还需要一个FastCGI的wrapper（wrapper可以理解为用于启动另一个程序的程序），这个wrapper绑定在某个固定socket上，如端口或者文件socket。当Nginx将CGI请求发送给这个socket的时候，通过FastCGI接口，wrapper接收到请求，然后Fork(派生）出一个新的线程，这个线程调用解释器或者外部程序处理脚本并读取返回数据；接着，wrapper再将返回的数据通过FastCGI接口，沿着固定的socket传递给Nginx；最后，Nginx将返回的数据（html页面或者图片）发送给客户端。这就是Nginx+FastCGI的整个运作过程

FastCGI接口方式在脚本解析服务器上启动一个或者多个守护进程对动态脚本进行解析，这些进程就是FastCGI进程管理器，或者称为FastCGI引擎，如PHP-FPM。因此HTTPServer完全解放出来，可以更好地进行响应和并发处理。
其实，Nginx就是一个反向代理服务器。Nginx通过反向代理功能将动态请求转向后端php-fpm（wrapper），从而实现对PHP的解析支持，这就是Nginx实现PHP动态解析的原理。

其整体工作流程：
1) FastCGI进程管理器php-fpm自身初始化，启动主进程php-fpm和启动start_servers个CGI 子进程。
主进程php-fpm主要是管理fastcgi子进程，监听9000端口。
fastcgi子进程等待来自Web Server的连接。

当客户端请求到达Web Server Nginx时，Nginx通过location指令，将所有以php为后缀的文件都交给127.0.0.1:9000来处理，即Nginx通过location指令，将所有以php为后缀的文件都交给127.0.0.1:9000来处理。

FastCGI进程管理器PHP-FPM选择并连接到一个子进程CGI解释器。Web server将CGI环境变量和标准输入发送到FastCGI子进程。

FastCGI子进程完成处理后将标准输出和错误信息从同一连接返回Web Server。当FastCGI子进程关闭连接时，请求便告处理完成。

5) FastCGI子进程接着等待并处理来自FastCGI进程管理器（运行在 WebServer中）的下一个连接。


## Nginx支持的IO模型有哪些？
Nginx支持如下处理连接的方法（I/O复用方法），这些方法可以通过use指令指定：
（1）select 如果当前平台没有更有效的方法，它是编译时默认的方法。可以使用配置参数–with-select_module 和 –without-select_module来启用或禁用这个模块。
（2）poll 如果当前平台没有更有效的方法，它是编译时默认的方法。可以使用配置参数–with-poll_module和–without-poll_module来启用或禁用这个模块。
（3）kqueue 高效的方法，使用于FreeBSD 4.1+、 OpenBSD 2.9+、NetBSD 2.0和MacOS X.。使用双处理器的MacOS X系统使用kqueue可能会造成内核崩溃。
（4）epoll 高效的方法，使用于Linux内核2.6版本及以后的系统。在某些发行版本中，如SuSE 8.2, 有让2.4版本的内核支持epoll的补丁。
（5）rtsig 可执行的实时信号，使用于Linux内核版本2.2.19以后的系统。默认情况下整个系统中不能出现大于1024个POSIX实时(排队)信号。这种情况对于高负载的服务器来说是低效的；所以有必要通过调节内核参数 /proc/sys/kernel/rtsig-max来增加队列的大小。可是从Linux内核版本2.6.6-mm2开始， 这个参数就不再使用了，并且对于每个进程有一个独立的信号队列，这个队列的大小可以用 RLIMIT_SIGPENDING 参数调节。当这个队列过于拥塞，nginx就放弃它并且开始使用poll方法来处理连接直到恢复正常。
（6）/dev/poll 高效的方法，使用于 Solaris 7 11/99+, HP/UX 11.22+ (eventport), IRIX 6.5.15+ 和 Tru64 UNIX 5.1A+.
（7）eventport 高效的方法，使用于 Solaris 10。
在linux下面，只有epoll是高效的方法。


## Redis有哪些基本的特点及优势？
Redis（REmote DIctionary Server），特点及优势：
（1）开源。
（2）Redis数据库完全在内存中，使用磁盘仅用于持久性。可以将内存中的数据保持在磁盘中，重启的时候可以再次加载进行使用。
（3）相比许多键值数据存储，Redis拥有一套较为丰富的数据类型。
（4）Redis可以将数据复制到任意数量的从服务器。
（5）异常快速：Redis的速度非常快，每秒能读约11万集合，写约81000条记录。
（6）操作都是原子性：所有Redis操作是原子的，这保证了如果两个客户端同时访问的Redis服务器将获得更新后的值。同时Redis还支持对几个操作全并后的原子性执行。
（7）多功能实用工具：Redis是一个多功能的实用工具，可以在多个用例如缓存、消息、队列使用（Redis原生支持发布/订阅）。

## Redis中如何切换数据库？
在Redis里，数据库简单的使用一个数字编号来进行辨认，默认数据库的数字编号是0。如果想切换到一个不同的数据库，可以使用select命令来实现。在命令行界面里键入select 1，Redis应该会回复一条OK的信息，然后命令行界面里的提示符会变成类似redis 127.0.0.1:6379[1]>这样。如果想切换回默认数据库，只要在命令行界面键入select 0即可。

## Redis是以单线程方式运行的吗？有什么问题？
Redis实际上是单线程运行的。虽然Redis是单线程运行的，但是可以同时运行多个Redis客户端进程，常见的并发问题还是会出现。像如下的代码，在get运行之后，set运行之前，powerlevel的值可能会被另一个Redis客户端给改变，从而造成错误：
redis.multi()
current = redis.get('powerlevel')
redis.set('powerlevel', current + 1)
redis.exec()

## 如何对Redis进行配置？有哪些常见的配置选项？
Redis的配置文件位于Redis安装目录下，文件名为redis.conf。
$ ./redis-server redis.conf				# 指定配置文件启动redis

redis 127.0.0.1:6379> CONFIG GET loglevel				# 查看loglevel配置项
1) "loglevel"
2) "notice"

redis 127.0.0.1:6379> CONFIG GET *						# 查看所有配置项
1) "dbfilename"
2) "dump.rdb"
3) "requirepass"
4) ""
5) "masterauth"
6) ""
7) "unixsocket"
8) ""

redis 127.0.0.1:6379> CONFIG SET loglevel "notice"		# 设置loglevel项
OK

常见配置参数：
（1）daemonize no						# 启用守护进程，默认不启用

（2）pidfile /var/run/redis.pid			# 指定pid文件，当以守护进程方式运行时用到

（3）port 6379							# 指定Redis监听端口

（4）bind 127.0.0.1						# 绑定的主机地址

（5）timeout 300					# 当客户端闲置指定时间后关闭连接，如果指定为0，表示关闭该功能

（6）loglevel verbose					# 指定日志记录级别：debug、verbose、notice、warning

（7）logfile stdout  					# 日志记录方式，默认为标准输出，如果配置Redis为守护进程方式运行，而这里又配置为日志记录方式为标准输出，则日志将会发送给/dev/null

（8）databases 16						# 设置数据库的数量，默认数据库为0，可以使用SELECT <dbid>命令在连接上指定数据库id

（9）指定在多长时间内，有多少次更新操作，就将数据同步到数据文件，可以多个条件配合
    save <seconds> <changes>
    Redis默认配置文件中提供了三个条件：
    save 900 1		# 900秒（15分钟）内有1个更改
    save 300 10		# 300秒（5分钟）内有10个更改
    save 60 10000	# 60秒内有10000个更改
 
（10）rdbcompression yes				# 指定存储至本地数据库时是否压缩数据，默认为yes，Redis采用LZF压缩，如果为了节省CPU时间，可以关闭该选项，但会导致数据库文件变的巨大

（11）dbfilename dump.rdb				# 指定本地数据库文件名，默认值为dump.rdb

（12）dir ./							# 指定本地数据库存放目录

（13）slaveof <masterip> <masterport>		# 设置当本机为slav服务时，设置master服务的IP地址及端口，在Redis启动时，它会自动从master进行数据同步

（14）masterauth <master-password> 		# 当master服务设置了密码保护时，slav服务连接master的密码

（15）requirepass foobared				# 设置Redis连接密码，如果配置了连接密码，客户端在连接Redis时需要通过AUTH <password>命令提供密码，默认关闭

（16）maxclients 128					# 设置同一时间最大客户端连接数，默认无限制，Redis可以同时打开的客户端连接数为Redis进程可以打开的最大文件描述符数，如果设置 maxclients 0，表示不作限制。当客户端连接数到达限制时，Redis会关闭新的连接并向客户端返回max number of clients reached错误信息

（17）maxmemory <bytes>					# 指定Redis最大内存限制，Redis在启动时会把数据加载到内存中，达到最大内存后，Redis会先尝试清除已到期或即将到期的Key，当此方法处理后，仍然到达最大内存设置，将无法再进行写入操作，但仍然可以进行读取操作。Redis新的vm机制，会把Key存放内存，Value会存放在swap区。

（18）appendonly no						# 指定是否在每次更新操作后进行日志记录，Redis在默认情况下是异步的把数据写入磁盘，如果不开启，可能会在断电时导致一段时间内的数据丢失。因为 redis本身同步数据文件是按上面save条件来同步的，所以有的数据会在一段时间内只存在于内存中。默认为no

（19）appendfilename appendonly.aof		# 指定更新日志文件名，默认为appendonly.aof

（20）appendfsync everysec  			#指定更新日志条件，共有3个可选值： 
    	no			# 等操作系统进行数据缓存同步到磁盘（快） 
    	always		# 每次更新操作后手动调用fsync()将数据写到磁盘（慢，安全） 
    	everysec	# 每秒同步一次（折衷，默认值）
    
（21）vm-enabled no						# 指定是否启用虚拟内存机制，默认值为no，简单的介绍一下，VM机制将数据分页存放，由Redis将访问量较少的页即冷数据swap到磁盘上，访问多的页面由磁盘自动换出到内存中

（22）vm-swap-file /tmp/redis.swap		# 虚拟内存文件路径，默认值为/tmp/redis.swap，不可多个Redis实例共享

（23）vm-max-memory 0 					# 将所有大于vm-max-memory的数据存入虚拟内存,无论vm-max-memory设置多小,所有索引数据都是内存存储的(Redis的索引数据 就是keys),也就是说,当vm-max-memory设置为0的时候,其实是所有value都存在于磁盘。默认值为0

（24）vm-page-size 32 					# Redis swap文件分成了很多的page，一个对象可以保存在多个page上面，但一个page上不能被多个对象共享，vm-page-size是要根据存储的 数据大小来设定的，作者建议如果存储很多小对象，page大小最好设置为32或者64bytes；如果存储很大大对象，则可以使用更大的page，如果不确定，就使用默认值

（25）vm-pages 134217728				# 设置swap文件中的page数量，由于页表（一种表示页面空闲或使用的bitmap）是在放在内存中的，在磁盘上每8个pages将消耗1byte的内存。

（26）vm-max-threads 4					# 设置访问swap文件的线程数,最好不要超过机器的核数,如果设置为0,那么所有对swap文件的操作都是串行的，可能会造成比较长时间的延迟。默认值为4

（27）glueoutputbuf yes					# 设置在向客户端应答时，是否把较小的包合并为一个包发送，默认为开启

（28）指定在超过一定的数量或者最大的元素超过某一临界值时，采用一种特殊的哈希算法
    hash-max-zipmap-entries 64
    hash-max-zipmap-value 512

（29）activerehashing yes				# 指定是否激活重置哈希，默认为开启（后面在介绍Redis的哈希算法时具体介绍）

（30）include /path/to/local.conf		# 指定包含其它的配置文件，可以在同一主机上多个Redis实例之间使用同一份配置文件，而同时各个实例又拥有自己的特定配置文件

## Redis有哪些数据类型？每种数据类型的存储极限是什么？
五种数据类型：string（字符串）、hash（哈希）、list（列表）、set（集合）及zset(sorted set：有序集合)。
String
string类型是二进制安全的，所以redis的string可以包含任何数据。比如jpg图片或者序列化的对象。一个string最大能存储512MB。
redis 127.0.0.1:6379> SET name "w3cschool.cc"
OK
redis 127.0.0.1:6379> GET name
"w3cschool.cc"

Hash
Redis hash是一个string类型的field和value的映射表，特别适合用于存储对象。每个 hash 可以存储2^32-1键值对（40多亿）。
redis 127.0.0.1:6379> HMSET user:1 username w3cschool.cc password w3cschool.cc points 200
OK
redis 127.0.0.1:6379> HGETALL user:1		# user:1 为键值
1) "username"
2) "w3cschool.cc"
3) "password"
4) "w3cschool.cc"
5) "points"
6) "200"

List
Redis列表是简单的字符串列表，按照插入顺序排序。可以添加一个元素到列表的头部（左边）或者尾部（右边）。
列表最多可存储2^32-1元素 (4294967295, 每个列表可存储40多亿)。
redis 127.0.0.1:6379> lpush w3cschool.cc redis
(integer) 1
redis 127.0.0.1:6379> lpush w3cschool.cc mongodb
(integer) 2
redis 127.0.0.1:6379> lpush w3cschool.cc rabitmq
(integer) 3
redis 127.0.0.1:6379> lrange w3cschool.cc 0 10
1) "rabitmq"
2) "mongodb"
3) "redis"

Set
Redis的Set是string类型的无序集合。集合是通过哈希表实现的，所以添加，删除，查找的复杂度都是O(1)。集合中最大的成员数为2^32-1(4294967295, 每个集合可存储40多亿个成员)。
redis 127.0.0.1:6379> sadd w3cschool.cc redis
(integer) 1			# 成功，返回1
redis 127.0.0.1:6379> sadd w3cschool.cc mongodb
(integer) 1
redis 127.0.0.1:6379> sadd w3cschool.cc rabitmq
(integer) 1
redis 127.0.0.1:6379> sadd w3cschool.cc rabitmq
(integer) 0			# 元素已经在集合中，返回0，第二次插入的元素将被忽略
redis 127.0.0.1:6379> smembers w3cschool.cc
1) "rabitmq"
2) "mongodb"
3) "redis"

zset
Redis zset和set一样也是string类型元素的集合,且不允许重复的成员。不同的是每个元素都会关联一个double类型的分数（score）。redis正是通过分数来为集合中的成员进行从小到大的排序。zset的成员是唯一的,但分数却可以重复。
redis 127.0.0.1:6379> zadd w3cschool.cc 0 redis
(integer) 1
redis 127.0.0.1:6379> zadd w3cschool.cc 0 mongodb
(integer) 1
redis 127.0.0.1:6379> zadd w3cschool.cc 0 rabitmq
(integer) 1
redis 127.0.0.1:6379> zadd w3cschool.cc 0 rabitmq
(integer) 0			# 元素在集合中存在则更新对应score
redis 127.0.0.1:6379> ZRANGEBYSCORE w3cschool.cc 0 1000
1) "redis"
2) "mongodb"
3) "rabitmq"

## 什么是Redis HyperLogLog ？
基数：如数据集 {1, 3, 5, 7, 5, 7, 8}，那么这个数据集的基数集为 {1, 3, 5 ,7, 8}, 基数(不重复元素)为5。 基数估计就是在误差可接受的范围内，快速计算基数。
Redis HyperLogLog是用来做基数统计的算法，HyperLogLog的优点是，在输入元素的数量或者体积非常非常大时，计算基数所需的空间总是固定的、并且是很小的。因为HyperLogLog只会根据输入元素来计算基数，而不会储存输入元素本身，所以HyperLogLog不能像集合那样，返回输入的各个元素。
例如：
redis 127.0.0.1:6379> PFADD w3ckey "redis"
1) (integer) 1
redis 127.0.0.1:6379> PFADD w3ckey "mongodb"
1) (integer) 1
redis 127.0.0.1:6379> PFADD w3ckey "mysql"
1) (integer) 1
redis 127.0.0.1:6379> PFCOUNT w3ckey
(integer) 3

基本命令：
（1）PFADD key element [element ...] 		# 添加指定元素到HyperLogLog中
（2）PFCOUNT key [key ...] 					# 返回给定 HyperLogLog 的基数估算值
（3）PFMERGE destkey sourcekey [sourcekey ...]  	# 将多个HyperLogLog合并为一个HyperLogLog

## Redis的发布订阅系统如何使用？
Redis发布订阅(pub/sub)是一种消息通信模式：发送者(pub)发送消息，订阅者(sub)接收消息。Redis客户端可以订阅任意数量的频道。

订阅：
redis 127.0.0.1:6379> SUBSCRIBE redisChat
Reading messages... (press Ctrl-C to quit)
1) "subscribe"
2) "redisChat"
3) (integer) 1

发布：（另一个客户端）
redis 127.0.0.1:6379> PUBLISH redisChat "Redis is a great caching technique"
(integer) 1
redis 127.0.0.1:6379> PUBLISH redisChat "Learn redis by w3cschool.cc"
(integer) 1

订阅者客户端显示：
 订阅者的客户端会显示如下消息
1) "message"
2) "redisChat"
3) "Redis is a great caching technique"
1) "message"
2) "redisChat"
3) "Learn redis by w3cschool.cc"

常用命令：
（1）PSUBSCRIBE pattern [pattern ...] 			# 订阅一个或多个符合给定模式的频道
（2）PUBSUB subcommand [argument [argument ...]] 	# 查看订阅与发布系统状态
（3）PUBLISH channel message 					# 将信息发送到指定的频道
（4）PUNSUBSCRIBE [pattern [pattern ...]] 		# 退订所有给定模式的频道
（5）SUBSCRIBE channel [channel ...] 			# 订阅给定的一个或多个频道的信息
（6）UNSUBSCRIBE [channel [channel ...]] 		# 指退订给定的频道

## 如何使用Redis的事务功能？
redis 127.0.0.1:6379> MULTI					# 开始一个事务
OK
redis 127.0.0.1:6379> SET book-name "Mastering C++ in 21 days"		
QUEUED										# 事务入队
redis 127.0.0.1:6379> GET book-name
QUEUED										# 事务入队
redis 127.0.0.1:6379> SADD tag "C++" "Programming" "Mastering Series"
QUEUED										# 事务入队
redis 127.0.0.1:6379> SMEMBERS tag
QUEUED										# 事务入队
redis 127.0.0.1:6379> EXEC					# 执行所有事务
1) OK
2) "Mastering C++ in 21 days"
3) (integer) 3
4) 1) "Mastering Series"
   2) "C++"
   3) "Programming"

常用命令：
（1）DISCARD 								# 取消事务，放弃执行事务块内的所有命令
（2）EXEC 									# 执行所有事务块内的命令
（3）MULTI 									# 标记一个事务块的开始
（4）UNWATCH 								# 取消WATCH命令对所有key的监视
（5）WATCH key [key ...] 					# 监视一个（或多个）key ，如果在事务执行之前这个（或这些） key被其他命令所改动，那么事务将被打断

## 如何对Redis数据进行备份和恢复？
创建当前数据库的备份，将在redis安装目录中创建dump.rdb文件
redis 127.0.0.1:6379> SAVE 					
OK

恢复数据：将备份文件 (dump.rdb) 移动到redis安装目录并启动服务即可
redis 127.0.0.1:6379> CONFIG GET dir		# 获取redis目录
1) "dir"
2) "/usr/local/redis/bin"

在后台备份数据
127.0.0.1:6379> BGSAVE
Background saving started

## 当客户端连接后，Redis会执行什么操作？
Redis通过监听一个TCP端口或者Unix socket的方式来接收来自客户端的连接，当一个连接建立后，Redis内部会进行以下一些操作：
（1）首先，客户端socket会被设置为非阻塞模式，因为Redis在网络事件处理上采用的是非阻塞多路复用模型
（2）然后为这个socket设置TCP_NODELAY属性，禁用Nagle算法
（3）然后创建一个可读的文件事件用于监听这个客户端socket的数据发送

## 什么是Redis的管道技术？
Redis是一种基于客户端-服务端模型以及请求/响应协议的TCP服务。这意味着通常情况下一个请求会遵循以下步骤：
（1）客户端向服务端发送一个查询请求，并监听Socket返回，通常是以阻塞模式，等待服务端响应。
（2）服务端处理命令，并将结果返回给客户端。
Redis管道技术可以在服务端未响应时，客户端可以继续向服务端发送请求，并最终一次性读取所有服务端的响应。
如：
$(echo -en "PING\r\n SET w3ckey redis\r\nGET w3ckey\r\nINCR visitor\r\nINCR visitor\r\nINCR visitor\r\n"; sleep 10) | nc localhost 6379

+PONG
+OK
redis
:1
:2
:3
以上实例中通过使用PING命令查看redis服务是否可用，之后设置了w3ckey的值为redis，然后获取w3ckey的值并使得visitor自增3次。在返回的结果中可以看到这些命令一次性向redis服务提交，并最终一次性读取所有服务端的响应
管道技术最显著的优势是提高了redis服务的性能。

## Redis分区有什么优势和不足？有哪些类型？ 
分区是分割数据到多个Redis实例的处理过程，因此每个实例只保存key的一个子集。

分区的优势：
（1）通过利用多台计算机内存的和值，允许构造更大的数据库。
（2）通过多核和多台计算机，允许扩展计算能力；通过多台计算机和网络适配器，允许扩展网络带宽。

分区的不足：
（1）涉及多个key的操作通常是不被支持的。举例来说，当两个set映射到不同的redis实例上时，就不能对这两个set执行交集操作。
（2）涉及多个key的redis事务不能使用。
（3）当使用分区时，数据处理较为复杂，比如需要处理多个rdb/aof文件，并且从多个实例和主机备份持久化文件。
（4）增加或删除容量也比较复杂。redis集群大多数支持在运行时增加、删除节点的透明数据平衡的能力，但是类似于客户端分区、代理等其他系统则不支持这项特性。然而，一种叫做presharding的技术对此是有帮助的。

分区类型：
Redis有两种类型分区。假设有4个Redis实例R0，R1，R2，R3，和类似user:1，user:2这样的表示用户的多个key，对既定的key有多种不同方式来选择这个key存放在哪个实例中。也就是说，有不同的系统来映射某个key到某个Redis服务。
（1）范围分区
最简单的分区方式是按范围分区，就是映射一定范围的对象到特定的Redis实例。
比如，ID从0到10000的用户会保存到实例R0，ID从10001到 20000的用户会保存到R1，以此类推。
这种方式是可行的，并且在实际中使用，不足就是要有一个区间范围到实例的映射表。这个表要被管理，同时还需要各种对象的映射表，通常对Redis来说并非是好的方法。
（2）哈希分区
另外一种分区方法是hash分区。这对任何key都适用，也无需是object_name:这种形式，像下面描述的一样简单：
用一个hash函数将key转换为一个数字，比如使用crc32 hash函数。对key foobar执行crc32(foobar)会输出类似93024922的整数。
对这个整数取模，将其转化为0-3之间的数字，就可以将这个整数映射到4个Redis实例中的一个了。93024922 % 4 = 2，就是说key foobar应该被存到R2实例中。注意：取模操作是取除的余数，通常在多种编程语言中用%操作符实现。



























