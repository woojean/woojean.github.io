# 点击劫持（ClickJacking）的攻击与防御

点击劫持是一种视觉上的欺骗手段，比如使用一个透明的iframe覆盖在一个网页上，然后诱使用户在该网页上进行操作，通过调整iframe页面的位置，可以使得用户恰好点击在iframe页面的一些功能性按钮上。

图片覆盖是另一种类似的视觉欺骗的方法。

## 防御ClickJacking
1.frame busting：通过写一段JavaScript代码禁止iframe的嵌套；（由于使用JavaScript，因此控制能力并不是特别强，有很多方法可以绕过它）

2.X-Frame-Options
当值为DENY时浏览器会拒绝当前页面加载任何frame页面，为SAMEORIGIN时可以加载同源下的页面，当值为ALLOW-FROM，可以定义运行的页面。