# 如何收集PHP的错误日志？

php在没有连接到数据库或者其他情况下会有提示错误，一般错误信息中会包含php脚本当前的路径信息或者查询的SQL语句等信息，这类信息提供给黑客后，是不安全的，所以服务器建议禁止错误提示。
`display_errors = Off`
在关闭display_errors后为了能够把错误信息记录下来，便于查找服务器运行的原因,同时也要设置错误日志存放的目录，建议跟webserver的日志放在一起。
打开php.ini，安全加固配置方式如下，打开错误日志记录并设置错误日志存放路径：
log_errors = On
error_log = /usr/local/apache2/logs/php_error.log # 该文件必须允许webserver的用户和组具有写的权限