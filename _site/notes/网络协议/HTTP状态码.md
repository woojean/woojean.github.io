# HTTP状态码

状态码由3位数字和原因短语组成，如200 OK
第一位数字指定了HTTP Response的类别，主要有5种：
1.信息，接收的请求正在处理
2.成功，请求正常处理完毕
3.重定向，需要进行附加操作以完成请求
4.客户端错误，服务器无法处理请求
5.服务器端错误，服务器处理请求出错

HTTP状态码的数量在60个以上（RFC2616，4918，5842），但是常用的通常是下面这十几种：
`200` OK 
`204` No Content  # 一般用在只需要从客户端往服务器端发送信息，而服务器端不需要发送新信息的场景
`206` Partial Content 
`301` Moved Permanently  # 永久性重定向，表示请求的资源已经被分配了新的URI（具体见Location首部字段）
`302` Found  # 临时性重定向
`303` See Other  # 表示由于请求的资源存在另一个URI，应使用GET方法定向获取请求的资源
`304` Not Modified  # 当客户端发送附带条件的请求（If-Match、If-Modified-Since等）时，未满足条件的情况下返回。304的返回将不包含任何响应的主体部分。
`307` Temporary Redirect  # 临时重定向，该状态码与302有着相同的含义
`400` Bad Request 	# 表示请求报文中存在语法错误
`401` Unauthorized  # 表示发送的请求需要有通过HTTP认证（BASIC或者DIGEST）的认证信息，如果浏览器是第1次接收到401响应，会弹出认证用的对话框。如果之前已进行过1次请求，则表示用户认证失败。
`403` Forbidden  # 访问被拒绝
`404` Not Found  # 服务器上没有请求的资源
`500` Internal Server Error 
`503` Service Unavailable  # 服务器正忙，一般会返回Retry-After字段