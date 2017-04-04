# 跨站点请求伪造（CSRF）的攻击与防御

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
