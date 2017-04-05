# 导入CSS文件的方式

## 在HTML文档中导入CSS文件
```html
<link href=’css/basic.css’ rel=’stylesheet’ type=’text/css’ />
```

## 在CSS文件中导入CSS文件
```html
<style type=’text/css’>
<!--
@import url(‘/css/advanced.css’);
-->
</style>
```

尽量使用单一的CSS文件而不是将其分为多个小文件：因为多个文件会导致多次服务器请求，这将影响下载时间。此外浏览器只能同时从一个域名下载数量有限的文件。