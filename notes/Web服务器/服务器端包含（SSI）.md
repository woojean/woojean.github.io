# 服务器端包含（SSI）

SSI技术实现各个局部页面的独立更新，比如Apache中的mod_include模块：

```
AddType text/html .shtml
AddOutputFilter INCLUDES .shtml
```
一旦网页支持SSI（按如上配置，即后缀为.shtml），那么每次请求的时候服务器必须要通读网页内容查找include标签，这需要大量的CPU开销。

SSI语法略。