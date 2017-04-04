# auto_prepend_file、auto_append_file

另一种将页眉和页脚添加到每个页面中的方法是使用php的两个配置auto_prepend_file、auto_append_file
auto_prepend_file = “/home/username/include/header.php”
auto_append_file = “/home/username/include/footer.php”
如果使用的是apache服务器，可以对单个目录进行不同配置选项的修改，即在目录中添加一个.htacess文件，文件内容如下：
php_value auto_prepend_file “/home/username/include/header.php”
php_value auto_append_file “/home/username/include/footer.php”
使用.htacess文件的前提是服务器运行重设其主配置文件，相对于在php.ini或者Web服务器配置文件中进行设置，将更加灵活，可以在一台共享机器上只影响某一个目录，不需要重新启动服务器，而且也不需要管理员权限。缺点在于，目录中每个被读取和解析的文件每次都要进行处理，而不是只在启动时处理一次，所以性能会有所降低。