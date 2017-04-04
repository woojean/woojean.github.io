# meat标签的http-equiv属性

http-equiv属性可用于模拟一个HTTP响应头。

// 设定网页的到期时间（一旦网页过期，必须到服务器上重新传输）
＜meta http-equiv="expires" content="Wed, 20 Jun 2007 22:33:00 GMT"＞

// 禁止浏览器从本地机的缓存中调阅页面内容（这样设定，访问者将无法脱机浏览）
＜meta http-equiv="Pragma" content="no-cache"＞

// 自动刷新并指向新页面（停留2秒钟后自动刷新到URL网址）
＜meta http-equiv="Refresh" content="2; URL=http://www.net.cn/"＞

// 设置Cookie（如果网页过期，那么存盘的cookie将被删除）
＜meta http-equiv="Set-Cookie" content="cookievalue=xxx;expires=Wednesday, 20-Jun-2007 22:33:00 GMT;path=/"＞ 

// 显示窗口的设定（强制页面在当前窗口以独立页面显示，防止别人在框架里调用自己的页面）
＜meta http-equiv="Window-target" content="_top"＞

// 设定页面使用的字符集
＜meta http-equiv="Content-Type" content="text/html; charset=gb2312"＞

// 网页等级评定
<meta http-equiv="Pics-label" contect="">
在IE的internet选项中有一项内容设置，可以防止浏览一些受限制的网站，而网站的限制级别就是通过meta属性来设置的。

// 设定进入页面时的特殊效果
<meta http-equiv="Page-Enter" contect="revealTrans(duration=1.0,transtion=12)">

// 设定离开页面时的特殊效果
<meta http-equiv="Page-Exit" contect="revealTrans(duration=1.0,transtion=12)">

// 清除缓存
<meta http-equiv="cache-control" content="no-cache">

// 关键字，给搜索引擎用的
<meta http-equiv="keywords" content="keyword1,keyword2,keyword3">

// 页面描述，给搜索引擎用的
<meta http-equiv="description" content="This is my page">
