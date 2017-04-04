# HTTP内容协商

客户端和服务器端就响应的资源内容进行交涉，然后提供给客户端最为合适的资源。内容协商会以响应资源的语言、字符集、编码方式等作为判断的基准，这些判断基准就是请求报文中的某些首部字段：

```
Accept
Accept-Charset
Accept-Encoding
Accept-Language
Content-Language
```

