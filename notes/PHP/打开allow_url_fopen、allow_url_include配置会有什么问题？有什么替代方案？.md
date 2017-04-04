# 打开allow_url_fopen、allow_url_include配置会有什么问题？有什么替代方案？

允许访问URL远程资源（就是允许fopen这样的函数打开url）使得PHP应用程序的漏洞变得更加容易被利用，php脚本若存在远程文件包含漏洞可以让攻击者直接获取网站权限及上传web木马，一般会在php配置文件中关闭该功能，若需要访问远程服务器建议采用其他方式如libcurl库。
allow_url_fopen = Off
allow_url_include = Off
比如有这样的代码：

```php
 	if (isset($HTTP_GET_VARS)) {
		reset($HTTP_GET_VARS);
		while ( list($var, $val) = each($HTTP_GET_VARS) ) {
			$$var=$val;
		}
	}
```
（一些较偶然的场景会导致将以http://开头的get参数所表示的远程文件直接包含进来，然后执行）