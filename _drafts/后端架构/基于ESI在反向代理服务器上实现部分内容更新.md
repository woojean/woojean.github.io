# 基于ESI在反向代理服务器上实现部分内容更新

反向代理服务器可以支持部分内容更新，但是前提是网页必须实现ESI（Edge Side Includes），ESI是W3C指定的标准，语法非常类似SSI，不同的是SSI在Web服务器上组装内容，而ESI在HTTP代理服务器上组装内容：

```
<HTML>
<BODY>
...
新闻内容
...
推荐新闻：<esi:include src="/recommand.php" />
</BODY>
</HTML>
```
（显然ajax更好，详略）