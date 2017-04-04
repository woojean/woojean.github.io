# 如何使用OpCache提高PHP应用的性能？

sudo vim /etc/php.ini
加入：
; 开关打开
opcache.enable=1

; 可用内存, 酌情而定, 单位 megabytes
opcache.memory_consumption=256

; 最大缓存的文件数目, 命中率不到 100% 的话, 可以试着提高这个值
opcache.max_accelerated_files=5000

; Opcache 会在一定时间内去检查文件的修改时间, 这里设置检查的时间周期, 默认为 2, 单位为秒
opcache.revalidate_freq=240

; interned string 的内存大小, 也可调
opcache.interned_strings_buffer=8   

; 是否快速关闭, 打开后在PHP Request Shutdown的时候回收内存的速度会提高
opcache.fast_shutdown=1

; 不保存文件/函数的注释
opcache.save_comments=0

检查：
php -v
    PHP 5.5.3-1ubuntu2.2 (cli) (built: Feb 28 2014 20:06:05) 
    Copyright (c) 1997-2013 The PHP Group
    Zend Engine v2.5.0, Copyright (c) 1998-2013 Zend Technologies
        `with Zend OPcache v7.0.3-dev`, Copyright (c) 1999-2013, by Zend Technologies

需要提醒的是，在生产环境中使用上述配置之前，必须经过严格测试。 因为`上述配置存在一个已知问题，它会引发一些框架和应用的异常， 尤其是在存在文档使用了备注注解的时候`。

重启服务：
sudo /etc/init.d/php-fpm restart
sudo /etc/init.d/nginx restart

如果在更新代码之后，发现没有执行的还是旧代码，可使用函数 opcache_reset() 来清除缓存。