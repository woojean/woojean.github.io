# AMD规范、requireJS

因为JavaScript本身的灵活性：框架没办法绝对的约束你的行为，一件事情总可以用多种途径去实现，所以我们只能在方法学上去引导正确的实施方法。
AMD规范：Asynchronous Module Definition，即异步模块加载机制。AMD规范简单到只有一个API，即define函数：
　　define([module-name?], [array-of-dependencies?], [module-factory-or-object]);
module-name: 模块标识，可以省略。
array-of-dependencies: 所依赖的模块，可以省略。
module-factory-or-object: 模块的实现，或者一个JavaScript对象。
当define函数执行时，它首先会异步地去调用第二个参数中列出的依赖模块，当所有的模块被载入完成之后，如果第三个参数是一个回调函数则执行，然后告诉系统模块可用，也就通知了依赖于自己的模块自己已经可用。
实例：

```javascript
	define("alpha", ["require", "exports", "beta"], function (require, exports, beta) {	// 依赖的模块做参数传入
　　	exports.verb = function() {	
　　		return beta.verb();
　　	}
　　});
```
requireJS例一：使用requirejs动态加载jquery
目录结构：
/web
/index.html				# 页面文件
/jquery-1.7.2.js			# jquery模块
/main.js					# js加载主入口，在引用require.js文件时通过data-main属性指定
/require.js				# requireJS文件

index.html文件内容：
```html
<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <script data-main="main" src="require.js"></script>		# “main”指向模块加载入口文件main.js
    </head>
    <body>
  	...
    </body>
</html>
```
main.js文件内容：
```javascript
require.config({
    paths: {
        jquery: 'jquery-1.7.2'
    }
});
 
require(['jquery'], function($) {		
    alert($().jquery);
});
```
引用模块jquery，因为这里配置了jquery的paths参数，所以将使用参数所对应的值'jquery-1.7.2'（js后缀名省略）。
jQuery从1.7以后支持AMD规范，所以当jQuery作为一个AMD模块运行时，它的模块名是jquery（区分大小写）。
如果文件名'jquery-1.7.2'改为jquery，则无需配置path参数。

requireJS例二：使用自定义模块
目录结构：
/web
/js
/cache.js				# 自定义模块
/event.js				# 自定义模块
/main.js			
/selector.js			# 自定义模块
/index.html
/require.js

index.html文件内容：
```html
<html>
    <head>
        <meta charset="utf-8">
        <style type="text/css">
            p {
                width: 200px;
                background: gray;
            }
        </style>
    </head>
    <body>
        <p>p1</p><p>p2</p><p>p3</p><p>p4</p><p>p5</p>
        <script data-main="js/main" src="require.js"></script>
    </body>
</html>
```
cache模块内容：返回一个js对象
```javascript
	define(function() {
...
    return {
        set: function(el, key, val) {
            var c = ...;
            ...
            return c;
        },
        ...
    };
});
```
event模块内容：依赖于cache模块（define第一个参数为依赖的模块列表，第二个参数为一个函数，函数形参直接使用模块）
```javascript
define(['cache'], function(cache) {
    ...
    return {				# 返回一个js对象
        bind : bind,
        unbind : unbind,
        trigger : trigger
    };
});
```
selector模块内容：
```javascript
define(function() {
    function query(selector,context) {
        ...
    }
     
    return query;		# 返回一个js函数
});
```
main.js内容：
```javascript
require.config({
  baseUrl: 'js'
});

require(['selector', 'event'], function($, E) {		# 函数两个形参一一对应所依赖的两个模块
    var els = $('p');
    for (var i=0; i < els.length; i++) {
        E.bind(els[i], 'click', function() {
            alert(this.innerHTML);
        });
    }
});
```