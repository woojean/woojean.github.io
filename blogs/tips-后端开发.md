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































