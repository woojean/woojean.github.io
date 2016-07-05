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

页面代码：



（3）`密码模式`（resource owner password credentials）
用户向客户端提供自己的用户名和密码。客户端使用这些信息，向"服务商提供商"索要授权。这通常用在用户对客户端高度信任的情况下。

（4）`客户端模式`（client credentials）
指客户端以自己的名义，而不是以用户的名义，向"服务提供商"进行认证。严格地说，客户端模式并不属于OAuth框架所要解决的问题。在这种模式中，用户直接向客户端注册，客户端以自己的名义要求"服务提供商"提供服务，其实不存在授权问题。

# 网络编程基本模型
所有的网络应用都是基于相同的基本编程模型，有着相似的整体逻辑结构，并且依赖相同的编程接口。每个网络应用都是基于客户端-服务器模型的。一个应用是由一个服务器进程和一个或多个客户端进程组成。
	客户端-服务器模型中的基本操作是事务，一个客户端-服务器事务由四步组成：

注意：客户端和服务器是进程，而不是机器或者主机。
客户端和服务器端通过在“连接”上发送和接收字节流来通信。套接字是“连接”的端点。套接字=地址：端口。
	当客户端发起一个连接请求时，客户端套接字地址中的端口是由内核自动分配的，称为临时端口。然而服务器套接字地址中的端口通常是某个知名的端口，是和服务对应的。在Unix机器上，文件etc/services包含一张这台机器提供的服务以及它们的知名端口号的综合列表。
	套接字接口是一组用来结合Unix I/O函数创建网络应用的函数。大多数现代系统上都实现它，包括所有Unix变种、Windows、Macintosh系统。

套接字地址存放在类型为sockaddr_in的16字节结构中。对于因特网应用，sin_family成员是AF_INTE，sin_port成员是一个16位的端口号，而sin_addr成员就是一个32位的IP地址。IP地址和端口号总是以网络字节顺序（大端法）存放的。

客户端和服务器使用socket函数来创建一个套接字描述符：

如：clientfd = Socket( AF_INET, SOCK_STREAM, 0);
Socket返回的clientfd描述符仅是部分打开的，并且不能用于读写。如何完成打开套接字的工作，取决于我们是客户端还是服务器。
客户端通过调用connect函数来建立和服务器的连接：

connect函数试图与套接字地址为serv_addr的服务器建立一个因特网连接，其中addrlen是sizeof(sockaddr_in)。connect函数会阻塞，一直到连接成功建立或是发生错误，如果成功，sockfd描述符现在就准备好读写了，并且得到的连接是由套接字对：
(x:y, serv_addr.sin_addr:serv_addr.sin_port)刻画的。x，y分别表示客户端的IP地址和端口。
bind、listen、accept三个函数用来和客户端建立连接：

bind函数告诉内核将my_addr中的服务器套接字地址和套接字描述符sockfd联系起来。
默认情况下内核会认为socket函数创建的描述符对应于主动套接字，它存在于一个连接的客户端。服务器调用listen函数告诉内核，描述符是被服务器而不是客户端使用的。

listen函数将sockfd从一个主动套接字转化为一个监听套接字，该套接字可以接受来自客户端的连接请求。backlog参数暗示了内核在开始拒绝连接请求前应该放入队列中等待的未完成连接请求的数量，其确切含义要求对TCP/IP协议的理解。通常会被设置为一个较大的值，比如1024.
服务器通过调用accept函数来等待来自客户端的连接请求：

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

（2）多线程的服务器模型（Multi-Thread）
应对多客户机的网络应用，最简单的解决方式是在服务器端使用多线程（或多进程）。多线程（或多进程）的目的是让每个连接都拥有独立的线程（或进程），这样任何一个连接的阻塞都不会影响其他的连接。但是如果要同时响应成千上万路的连接请求，则无论多线程还是多进程都会严重占据系统资源，降低系统对外界响应效率。
在多线程的基础上，可以考虑使用“线程池”或“连接池”，“线程池”旨在减少创建和销毁线程的频率，其维持一定合理数量的线程，并让空闲的线程重新承担新的执行任务。“连接池”维持连接的缓存池，尽量重用已有的连接、减少创建和关闭连接的频率。这两种技术都可以很好的降低系统开销，都被广泛应用很多大型系统。

（3）非阻塞式模型（Non-blocking IO）
相比于阻塞型接口的显著差异在于，在被调用之后立即返回。

需要应用程序调用许多次来等待操作完成。这可能效率不高，因为在很多情况下，当内核执行这个命令时，应用程序必须要进行忙碌等待，直到数据可用为止。
另一个问题，在循环调用非阻塞IO的时候，将大幅度占用CPU，所以一般使用select等来检测”是否可以操作“。

（4）多路复用IO（IO multiplexing）
支持I/O复用的系统调用有select、poll、epoll、kqueue等。使用Select返回后，仍然需要轮训再检测每个socket的状态（读、写），这样的轮训检测在大量连接下也是效率不高的。因为当需要探测的句柄值较大时，select () 接口本身需要消耗大量时间去轮询各个句柄。
很多操作系统提供了更为高效的接口，如 linux 提供 了 epoll，BSD 提供了 kqueue，Solaris 提供了 /dev/poll …。如果需要实现更高效的服务器程序，类似 epoll 这样的接口更被推荐。

（5）使用事件驱动库libevent的服务器模型
libevent是一个事件触发的网络库，适用于windows、linux、bsd等多种平台，内部使用select、epoll、kqueue、IOCP等系统调用管理事件机制。著名分布式缓存软件memcached也是基于libevent，而且libevent在使用上可以做到跨平台。
libevent 库提供一种事件机制，它作为底层网络后端的包装器。事件系统让为连接添加处理函数变得非常简便，同时降低了底层IO复杂性。这是 libevent 系统的核心。
创建 libevent 服务器的基本方法是，注册当发生某一操作（比如接受来自客户端的连接）时应该执行的函数，然后调用主事件循环 event_dispatch()。执行过程的控制现在由 libevent 系统处理。注册事件和将调用的函数之后，事件系统开始自治；在应用程序运行时，可以在事件队列中添加（注册）或 删除（取消注册）事件。事件注册非常方便，可以通过它添加新事件以处理新打开的连接，从而构建灵活的网络处理系统。

（6）信号驱动IO模型（Signal-driven IO）
让内核在描述符就绪时发送SIGIO信号通知应用程序。


（7）异步IO模型（asynchronous IO）
告知内核启动某个操作，并让内核在整个操作（包括将数据从内核复制到我们自己的缓冲区）完成后通知我们。这种模型与信号驱动模型的主要区别在于：信号驱动式I/O是由内核通知我们何时可以启动一个I/O操作，而异步I/O模型是由内核通知我们I/O操作何时完成。

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






















