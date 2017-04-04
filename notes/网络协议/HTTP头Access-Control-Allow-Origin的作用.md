# HTTP头Access-Control-Allow-Origin的作用

在某域名下使用Ajax向另一个域名下的页面请求数据，会遇到跨域问题。另一个域名必须在response中添加 Access-Control-Allow-Origin 的header，才能让前者成功拿到数据。
只有当目标页面的response中，包含了 Access-Control-Allow-Origin 这个header，并且它的值里有我们自己的域名时，浏览器才允许我们拿到它页面的数据进行下一步处理。
如果它的值设为 * ，则表示谁都可以用。