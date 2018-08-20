---
layout: post
title:  "更新CentOS的PHP及各种扩展为最新"
date: 2017-04-05 00:02:00
categories: 编程
tags: 运维
excerpt: ""
---

* content
{:toc}

## 安装PHP7

**清理现有安装**

```
yum list installed | grep php

sudo yum remove php56w.x86_64 php56w-bcmath.x86_64 php56w-cli.x86_64 php56w-common.x86_64 php56w-devel.x86_64 php56w-fpm.x86_64 php56w-gd.x86_64 php56w-imap.x86_64 php56w-intl.x86_64 php56w-ldap.x86_64 php56w-mbstring.x86_64 php56w-mcrypt.x86_64 php56w-mysql.x86_64 php56w-odbc.x86_64 php56w-opcache.x86_64 php56w-pdo.x86_64 php56w-pear.noarch php56w-pecl-igbinary.x86_64 php56w-process.x86_64 php56w-xml.x86_64 php56w-xmlrpc.x86_64
```

**安装PHP7**

```
wget https://dl.fedoraproject.org/pub/epel/epel-release-latest-6.noarch.rpm
wget http://rpms.remirepo.net/enterprise/remi-release-6.rpm
rpm -Uvh remi-release-6.rpm epel-release-latest-6.noarch.rpm

yum install php70-php php70-php-pear php70-php-bcmath php70-php-pecl-jsond-devel php70-php-mysqlnd php70-php-gd php70-php-common php70-php-fpm php70-php-intl php70-php-cli php70-php php70-php-xml php70-php-opcache php70-php-pecl-apcu php70-php-pecl-jsond php70-php-pdo php70-php-gmp php70-php-process php70-php-pecl-imagick php70-php-devel php70-php-mbstring php70-php-mcrypt

ln -s /usr/bin/php70 /usr/bin/php
ln -s /opt/remi/php70/root/usr/sbin/php-fpm /usr/sbin/php-fpm
ln -s /opt/remi/php70/root/usr/bin/phpize /usr/sbin/phpize
ln -s /opt/remi/php70/root/usr/bin/php-config /usr/sbin/php-config
ln -s /opt/remi/php70/root/usr/bin/php-cgi /usr/sbin/php-cgi
```

**测试**

```
# php -v
PHP 7.0.17 (cli) (built: Mar 14 2017 14:13:48) ( NTS )
Copyright (c) 1997-2017 The PHP Group
Zend Engine v3.0.0, Copyright (c) 1998-2017 Zend Technologies
    with Zend OPcache v7.0.17, Copyright (c) 1999-2017, by Zend Technologies

php配置文件位置：/etc/opt/remi/php70/php.ini
```



### 安装Phalcon.so

https://github.com/phalcon/cphalcon/archive/v3.1.1.tar.gz

```
cd /data/extend/cphalcon-3.1.1/build/php7/64bits
sudo phpize
sudo ./configure
sudo make

# 拷贝生成的模块
cp /data/extend/cphalcon-3.1.1/build/php7/64bits/modules/phalcon.so /data/extend/php7/phalcon.so
```



## 更新seaslog.so

https://github.com/Neeke/SeasLog/archive/SeasLog-1.6.9.tar.gz

```
cd /data/extend/SeasLog-SeasLog-1.6.9
sudo phpize
sudo ./configure
sudo make

# 拷贝生成的模块
cp /data/extend/SeasLog-SeasLog-1.6.9/modules/seaslog.so /data/extend/php7/seaslog.so
```

### 更新redis.so

https://github.com/phpredis/phpredis/archive/3.1.2.tar.gz

```
cd /data/extend/phpredis-3.1.2
sudo phpize
sudo ./configure
sudo make

# 拷贝生成的模块
cp /data/extend/phpredis-3.1.2/modules/redis.so /data/extend/php7/redis.so
```

### 更新xdebug

https://github.com/xdebug/xdebug/archive/XDEBUG_2_5_1.tar.gz

```
cd /data/extend/xdebug-XDEBUG_2_5_1
sudo phpize
sudo ./configure
sudo make

# 拷贝生成的模块
cp /data/extend/xdebug-XDEBUG_2_5_1/modules/xdebug.so /data/extend/php7/xdebug.so
```

### 更新swoole.so

https://github.com/swoole/swoole-src/archive/v2.0.7.tar.gz

```
cd /Users/wujian/extensions/swoole-src-2.0.7
sudo phpize
sudo ./configure
sudo make

# 拷贝生成的模块
cp /data/extend/swoole-src-2.0.7/modules/swoole.so /data/extend/php7/swoole.so
```

## 更新thrift_protocol.so

https://github.com/apache/thrift/archive/0.10.0.tar.gz

```
cd /data/extend/thrift-0.10.0/lib/php/src/ext/thrift_protocol

sudo phpize
sudo ./configure
sudo make

cp /Users/wujian/extensions/thrift-0.10.0/lib/php/src/ext/thrift_protocol/modules/thrift_protocol.so /Users/wujian/extensions/thrift_protocol.so
```



## 添加PHP扩展配置

```
vi /etc/opt/remi/php70/php.d/phalcon.ini
extension=/data/extend/php7/phalcon.so

vi /etc/opt/remi/php70/php.d/seaslog.ini
extension=/data/extend/php7/seaslog.so

vi /etc/opt/remi/php70/php.d/redis.ini
extension=/data/extend/php7/redis.so

;vi /etc/opt/remi/php70/php.d/xdebug.ini
;extension=/data/extend/php7/xdebug.so

vi /etc/opt/remi/php70/php.d/swoole.ini
extension=/data/extend/php7/swoole.so

;extension=/Users/wujian/extensions/thrift_protocol.so
```



# 重启php-fpm和nginx

```
ps -ef | grep php-fpm
kill -9 **
php-fpm

nginx -s stop
/usr/local/nginx/sbin/nginx -c /usr/local/nginx/conf/nginx.conf
nginx -s reload
```



```
/var/opt/remi/php70/log/php-fpm/error.log
```



```
mv /etc/nginx/conf.d/article.conf /usr/local/nginx/conf/conf.d/article.conf
mv /etc/nginx/conf.d/demo.conf /usr/local/nginx/conf/conf.d/demo.conf
mv /etc/nginx/conf.d/product.conf /usr/local/nginx/conf/conf.d/product.conf
mv /etc/nginx/conf.d/user-center.conf /usr/local/nginx/conf/conf.d/user-center.conf
mv /etc/nginx/conf.d/wiki.conf /usr/local/nginx/conf/conf.d/wiki.conf
mv /etc/nginx/conf.d/cart.conf /usr/local/nginx/conf/conf.d/cart.conf
mv /etc/nginx/conf.d/ds.conf /usr/local/nginx/conf/conf.d/ds.conf
mv /etc/nginx/conf.d/sms.conf /usr/local/nginx/conf/conf.d/sms.conf
mv /etc/nginx/conf.d/virtual.conf /usr/local/nginx/conf/conf.d/virtual.conf
mv /etc/nginx/conf.d/wu.conf /usr/local/nginx/conf/conf.d/wu.conf
mv /etc/nginx/conf.d/default.conf /usr/local/nginx/conf/conf.d/default.conf
mv /etc/nginx/conf.d/order.conf /usr/local/nginx/conf/conf.d/order.conf
mv /etc/nginx/conf.d/ssl.conf /usr/local/nginx/conf/conf.d/ssl.conf
mv /etc/nginx/conf.d/wiki2.conf /usr/local/nginx/conf/conf.d/wiki2.conf

```