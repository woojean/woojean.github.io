# nohup与&的区别

& 要是关闭终端那么脚本也停了，
加nohup  既使把终端关了，脚本也会跑，是在服务器那运行的。

nohup 命令运行由 Command 参数和任何相关的 Arg 参数指定的命令，忽略所有挂断（SIGHUP）信号。在注销后使用 nohup 命令运行后台中的程序。

一般结合使用：nohup command & 