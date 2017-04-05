# 如何禁止在PHP中使用危险函数？

Web木马程序通常利用php的特殊函数执行系统命令，查询任意目录文件，增加修改删除文件等。
比如：
<?php eval($_POST[cmd]);?> 
（其实就是使用了一些危险函数使得应用存在漏洞，最好的防范方式就是不允许使用这些函数）
打开php.ini，安全加固配置方式如下，禁止使用这些危险函数：
disable_functions = dl,assert,exec,popen,system,passthru,shell_exec,proc_close,proc_open,pcntl_exec