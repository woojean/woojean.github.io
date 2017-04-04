# JSONP的原理

JSONP是一种解决跨域传输JSON数据的问题的解决方案，是一种非官方跨域数据交互协议。
Ajax（或者说js）直接请求普通文件存在跨域无权限访问的问题，但是`Web页面上凡是拥有"src"属性的标签引用文件时则不受是否跨域的影响`。如果想通过纯web端（ActiveX控件、服务端代理、Websocket等方式不算）跨域访问数据就只有一种可能：在远程服务器上设法把数据装进js格式的文件里，供客户端调用和进一步处理。
为了便于客户端使用数据，逐渐形成了一种非正式传输协议:JSONP，该协议的一个要点就是`允许用户传递一个callback参数给服务端，然后服务端返回数据时会将这个callback参数作为函数名来包裹住JSON数据`，这样客户端就可以随意定制自己的函数来自动处理返回数据了。

例：使用Javascript实现JSONP
```html
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
<script type="text/javascript">
// 回调函数
  var flightHandler = function(data){
    alert('你查询的航班结果是：票价 ' + data.price + ' 元，' + '余票 ' + data.tickets + ' 张。');
  };

// 拼凑url
  var url = "http://flightQuery.com/jsonp/flightResult.aspx?code=CA1998&callback=flightHandler";

// 拼凑<script>标签，用于发出JSONP请求
  var script = document.createElement('script');
  script.setAttribute('src', url);
  document.getElementsByTagName('head')[0].appendChild(script);
</script>
</head>
<body>
</body>
</html>
```

服务器端返回格式：
```json
  flightHandler({
    "code": "CA1998",
    "price": 1780,
    "tickets": 5
 });
```

例：使用jQuery实现JSONP
```html
<html xmlns="http://www.w3.org/1999/xhtml" >
 <head>
     <title>Untitled Page</title>
      <script type="text/javascript" src=jquery.min.js"></script>
      <script type="text/javascript">
     		jQuery(document).ready(function(){
        		$.ajax({
             		type: "get",
             		async: false,
             		url: "http://flightQuery.com/jsonp/flightResult.aspx?code=CA1998",
             		dataType: "jsonp",
             		jsonp: "callback",		// 传递给请求处理程序或页面的，用以获得jsonp回调函数名的参数名(一般默认为:callback)
             		jsonpCallback:"flightHandler",		// 自定义的jsonp回调函数名称，没有定义的话会jQuery会自动生成以jQuery开头的函数
             		success: function(json){
                 		alert('您查询到航班信息：票价： ' + json.price + ' 元，余票： ' + json.tickets + ' 张。');
             		},
             		error: function(){
                 		alert('fail');
             		}
         		});
    		 });
     </script>
     </head>
  <body>
  </body>
</html>
```
jquery在处理jsonp类型的ajax时自动生成回调函数并把数据（即不含函数名的纯json格式的数据）取出来供success属性方法来调用。