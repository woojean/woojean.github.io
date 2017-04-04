# OAuth2.0授权方式

OAuth是一个关于授权（authorization）的开放网络标准，目前的版本是2.0版。
OAuth的作用就是让"客户端"（第三方应用）安全可控地获取"用户"的授权，与"服务商提供商"（平台，比如微信）进行互动。
OAuth在"客户端"与"服务提供商"之间，设置了一个授权层（authorization layer）。`"客户端"不能直接登录"服务提供商"，只能登录授权层`，以此将用户与客户端区分开来。"客户端"登录授权层所用的令牌（token），与用户的密码不同。用户可以在登录的时候，指定授权层令牌的权限范围和有效期。"客户端"登录授权层以后，`"服务提供商"根据令牌的权限范围和有效期，向"客户端"开放用户储存的资料`。

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