# 部署Phalcon+Laravel+React混合项目执行环境

项目代码执行所必须的初始化工作.

# 配置：
```
[root@iZuf6cbroi7rj1zjydjruoZ ~]# lsb_release -a
LSB Version:    :base-4.0-amd64:base-4.0-noarch:core-4.0-amd64:core-4.0-noarch
Distributor ID: CentOS
Description:    CentOS release 6.8 (Final)
Release:    6.8
Codename:   Final
```

[VPC](#VPC)
[SLB](#SLB)
[ECS](#ECS)
[PHP](#PHP)
[Tengine](#Tengine)
[MySQL](#MySQL)
[PHP-FPM](#PHP-FPM)
[Redis](#Redis)
[OSS](#OSS)
[PHP-Extention](#PHP-Extention)
[node](#node)
[webpack](#webpack)
[bower](#bower)
[composer](#composer)
[git](#git)
[vhost](#vhost)
[domain](#domain)
[常用操作](#operations)


# VPC
<span id="VPC">==================================================================</span>
## 创建
华东2；
deploy_2017
192.168.0.0/16

## 创建交换机
deploy_switch_online_01
192.168.1.0/24 => 192.168.1.1 ~ 192.168.1.254 / 255.255.255.0


# SLB
<span id="SLB">==================================================================</span>
## 配置
@todo


# ECS
<span id="ECS">==================================================================</span>
## 申请
地域：华东2；
<del>网络：专有网络；deploy_2017；deploy_switch_online_01；</del>
网络：经典网络，默认安全组
实例：略
带宽：略
镜像：CentOS 6.8，64位
存储：40G系统盘，无数据盘；
用户数据：略；
安全设置：略；

## 安全组设置出入规则限制
略。

## 输出
106.14.26.249 root ******


## 初始化
1.准备目录
```
mkdir -p /data/www /data/log /data/extend
```

2.安装Git
```
yum install git
```


# PHP
<span id="PHP">==================================================================</span>
## 清理现有安装
```
yum list installed | grep php
```
如果有则删除：
```
sudo yum remove php55w.x86_64 php55w-bcmath.x86_64 php55w-cli.x86_64 php55w-common.x86_64 php55w-devel.x86_64 php55w-fpm.x86_64 php55w-gd.x86_64 php55w-imap.x86_64 php55w-intl.x86_64 php55w-ldap.x86_64 php55w-mbstring.x86_64 php55w-mcrypt.x86_64 php55w-mysql.x86_64 php55w-odbc.x86_64 php55w-opcache.x86_64 php55w-pdo.x86_64 php55w-pear.noarch php55w-pecl-igbinary.x86_64 php55w-pecl-memcache.x86_64 php55w-process.x86_64 php55w-xml.x86_64 php55w-xmlrpc.x86_64
```

## 安装PHP5.6及常用扩展

添加解析路径：vi /etc/resolv.conf，添加：
nameserver 8.8.8.8

```
rpm -Uvh http://mirror.webtatic.com/yum/el6/latest.rpm

sudo yum install -y php56w  php56w-bcmath php56w-cli php56w-common  php56w-devel php56w-fpm    php56w-gd php56w-imap  php56w-ldap php56w-mbstring php56w-mcrypt php56w-mysql   php56w-odbc   php56w-pdo   php56w-pear  php56w-pecl-igbinary  php56w-xml php56w-xmlrpc php56w-opcache php56w-intl
```

如果yum变成僵尸进程，kill -9杀不掉，则需要找到并杀死它的父进程：
```
ps -ef | grep defunct_process_pid
```

## 输出
```
[root@iZuf6cbroi7rj1zjydjruoZ data]# php -v
PHP 5.6.29 (cli) (built: Dec 10 2016 13:02:08)
Copyright (c) 1997-2016 The PHP Group
Zend Engine v2.6.0, Copyright (c) 1998-2016 Zend Technologies
    with Zend OPcache v7.0.6-dev, Copyright (c) 1999-2016, by Zend Technologies
```

# Tengine
<span id="Tengine">==================================================================</span>
## 先安装OpenSSL
```
yum -y install openssl openssl-devel
```

再安装tengine：
```
cd /data/extend
wget https://github.com/alibaba/tengine/archive/tengine-2.2.0.tar.gz
tar -xvf tengine-2.2.0.tar.gz
mv tengine-tengine-2.2.0/ tengine
cd tengine
./configure
make & make install
```

## 输出
```
[root@iZuf6cbroi7rj1zjydjruoZ tengine]# /usr/local/nginx/sbin/nginx -v
Tengine version: Tengine/2.2.0 (nginx/1.8.1)
```

建立软链接：
```
ln -s /usr/local/nginx/sbin/nginx /usr/bin/nginx
```


# MySQL
<span id="MySQL">==================================================================</span>

# 设置白名单
略。

# 创建高权限账号
略。


@temp
## 输出
host = rm-bp12bu4qv8v6x3k5k.mysql.rds.aliyuncs.com
username = deploy2017
password = ******
dbname = ***
port = 3306



# PHP-FPM
<span id="PHP-FPM">==================================================================</span>
在安装PHP的时候已安装php56w-fpm。

## 输出
```
[root@iZuf6cbroi7rj1zjydjruoZ ~]# whereis php-fpm
php-fpm: /usr/sbin/php-fpm /etc/php-fpm.d /etc/php-fpm.conf /usr/share/man/man8/php-fpm.8.gz
```


# Redis
<span id="Redis">==================================================================</span>

## 安装
@temp 实际应该使用集群
```
cd /data/extend/
wget http://download.redis.io/releases/redis-3.2.8.tar.gz
tar -xvf redis-3.2.8.tar.gz
mv redis-3.2.8 redis
cd redis
make MALLOC=libc  # 否则默认使用jemalloc，因为没有安装，会报错
make install
```

## 输出
```
[root@iZuf6cbroi7rj1zjydjruoZ redis]# redis-server
9023:C 25 Jan 16:57:10.613 # Warning: no config file specified, using the default config. In order to specify a config file use redis-server /path/to/redis.conf

[root@iZuf6cbroi7rj1zjydjruoZ redis]# whereis redis-cli
redis-cli: /usr/local/bin/redis-cli
```

## 配置
```
cp /data/extend/redis/redis.conf /etc/redis.conf
vi /etc/redis.conf
```

1.开启redis密码
```
requirepass ******
```

2.远程访问
```
bind 0.0.0.0
```
注意,另有一处bind 127.0.0.1需要注释

3.关闭保护模式
```
protected-mode no
```

## 输出
```
[root@iZuf6cbroi7rj1zjydjruoZ redis]# nohup redis-server /etc/redis.conf &
[root@iZuf6cbroi7rj1zjydjruoZ redis]# netstat -lntp | grep 6379
tcp        0      0 127.0.0.1:6379              0.0.0.0:*                   LISTEN      9053/redis-server 1
[root@iZuf6cbroi7rj1zjydjruoZ redis]# redis-cli -h 127.0.0.1 -p 6379 -a ******
127.0.0.1:6379> keys *
(empty list or set)
127.0.0.1:6379> set k v
OK
127.0.0.1:6379> keys *
1) "k"
```

# OSS
<span id="OSS">==================================================================</span>
华东2,公共读

## 绑定域名
1.在bucket下添加域名 oss.woojean.com
2.到域名解析后台,添加CNAME解析:oss => deploy2017.oss-cn-shanghai.aliyuncs.com

## 输出
server            = http://oss-cn-shanghai.aliyuncs.com
server_internal   = http://oss-cn-shanghai-internal.aliyuncs.com
access_key_id     = ***
access_key_secret = ***
bucket            = deploy2017
cdn               = http://oss.woojean.com/


# PHP-Extention
<span id="PHP-Extention">==================================================================</span>
## phalcon
```
cd /data/extend/
wget https://github.com/phalcon/cphalcon/archive/v3.0.0.tar.gz
tar -xvf v3.0.0.tar.gz
cd /data/extend/cphalcon-3.0.0/build/php5/64bits
sudo phpize
sudo ./configure
sudo make & make install
```

编译生成的phalcon.so文件位置：/usr/lib64/php/modules
```
vi /etc/php.d/phalcon.ini
extension=phalcon.so
php -i | grep phalcon
```

## seaslog
```
cd /data/extend/
wget https://github.com/Neeke/SeasLog/archive/SeasLog-1.6.8.tar.gz
tar -xvf SeasLog-1.6.8.tar.gz
cd SeasLog-SeasLog-1.6.8/
sudo phpize
sudo ./configure
sudo make & make install

vi /etc/php.d/seaslog.ini
extension=seaslog.so
php -i | grep seaslog
```

## phpredis
```
cd /data/extend/
wget https://github.com/phpredis/phpredis/archive/3.1.0.tar.gz
tar -xvf 3.1.0.tar.gz
cd phpredis-3.1.0/
sudo phpize
sudo ./configure
sudo make && make install

vi /etc/php.d/redis.ini
extension=redis.so
```

# node
<span id="node">==================================================================</span>
## 安装
node的编译安装依赖巨多,甚至依赖python版本,所以直接使用二进制文件.
```
cd /data/extend/
wget https://nodejs.org/dist/v4.2.2/node-v4.2.2-linux-x64.tar.gz
tar -xvf node-v4.2.2-linux-x64.tar.gz

ln -s /data/extend/node-v4.2.2-linux-x64/bin/node /usr/bin/node
ln -s /data/extend/node-v4.2.2-linux-x64/bin/npm /usr/bin/npm
npm config set registry https://registry.npm.taobao.org  
```

## 输出
```
[root@iZuf6cbroi7rj1zjydjruoZ extend]# node -v
v4.2.2
[root@iZuf6cbroi7rj1zjydjruoZ extend]# npm -v
2.14.7
```


# webpack
<span id="webpack">==================================================================</span>
## 安装
```
npm install webpack -g
ln -s /data/extend/node-v4.2.2-linux-x64/lib/node_modules/webpack/bin/webpack.js /usr/bin/webpack
```
## 输出
```
[root@iZuf6cbroi7rj1zjydjruoZ extend]# webpack -v
webpack 1.14.0
...
```

# bower
<span id="bower">==================================================================</span>
```
npm install -g bower
ln -s /data/extend/node-v4.2.2-linux-x64/lib/node_modules/bower/bin/bower /usr/bin/bower
```

## 输出
```
[root@iZuf6cbroi7rj1zjydjruoZ deploy]# bower -v
1.8.0
```


# composer
<span id="composer">==================================================================</span>
## 安装
```
cd /data/extend/
wget https://getcomposer.org/download/1.3.1/composer.phar
cp /data/extend/composer.phar /usr/bin/composer
chmod u+x /usr/bin/composer

composer config -g repo.packagist composer https://packagist.phpcomposer.com
```

## 输出
```
composer -v
Composer version 1.3.1 2017-01-07 18:08:51
```


# git
<span id="git">==================================================================</span>

## 将部署账号添加到git项目
Settings/Collaborators/Add

## 配置公钥
```
root@iZuf6a6ydemt9fvgy22rcqZ ~]# ssh-keygen -t rsa -C "floodwu@qq.com"
Generating public/private rsa key pair.
Enter file in which to save the key (/root/.ssh/id_rsa): 【回车】
Created directory '/root/.ssh'.
Enter passphrase (empty for no passphrase): 【回车】
Enter same passphrase again: 【回车】
Your identification has been saved in /root/.ssh/id_rsa.
Your public key has been saved in /root/.ssh/id_rsa.pub.
The key fingerprint is:
16:d2:ed:a3:3e:65:48:fe:cc:dc:1e:e9:3f:92:3e:47 floodwu@qq.com
```

拷贝公钥内容,添加到git部署账号的ssh key设置中:
```
cat ~/.ssh/id_rsa.pub
ssh-rsa AAAAB3NzaC1yc2EAAAABIwAAAQEAplElkn+tOaO4rVYq7UhnL4Hx9x+G/M5PJX6DLM6f5J/zpJizcEC3KMcJPX8AjcjSrciNYg1DCHucqgxyvAGCOS8THIJQfhrvyG+Xx4935OIrXU5oSPaxW65vKGtFRHgq8AA/wTxim3av+db/uV/WRDV+hbZ1wui4IWfrcp1rfTUszMM3uB1qLCJMOMLYIAK3Sod7QPjGCTob9mn1i7N5cBQEdIDNcI/yIjJEt/cqSWMgF4LSDn3rn8+YnZPkAo9j8D3f8F1AxzYTwbJ4JXg+xLWCSD4hvNGhNus8N/5kBoe8oKzdCBbNw3DqY7aeYxLLIb+zw8nUlQrm3Mia9qHKgQ== floodwu@qq.com

测试：
[root@iZuf6cbroi7rj1zjydjruoZ ~]# ssh -T git@github.com
Hi deploy2017! You've successfully authenticated, but GitHub does not provide shell access.
```

## 设置git用户和token
```
git config --global user.name "deploy2017"
git config --global user.email "floodwu@qq.com"
git config --global github.user deploy2017
git config --global github.token ******  # Personal access tokens
```

## 拉代码
```
cd /data/www
git clone git@github.com:woojean/deploy.git
```

## 初始化数据库
Create_db_deploy.sql

## 初始化代码配置
### laravel-react:
```
cp .env.example .env
cp composer.json.example composer.json
cp package.json.example package.json
cp webpack.config.js.example webpack.config.js

composer install
sudo bower install --allow-root  # npm run build时会copy

npm install --verbose # 不能用sudo,否则不会安装在当前目录下
npm install --verbose --dev # 否则babel不可用
```

vi .env
```
APP_ENV=prod
APP_DEBUG=false
...
KD_HOST=http://phalcon.woojean.com
JAVASCRIPT_BUNDLE=http://laravel-react.woojean.com/build/js/
```

初始化数据库,并填充管理员信息(需要事先修改.env中的数据库配置):
```
CREATE DATABASE IF NOT EXISTS db_laravel_react
  DEFAULT CHARSET utf8mb4
  COLLATE utf8mb4_unicode_ci;

php artisan migrate
php artisan db:seed

php artisan migrate:reset       // 回滚所有
php artisan migrate:rollback    // 回滚最近一次
```


### phalcon:
```
cp config.ini.example config.ini
cp composer.json.example composer.json

composer install
```
vi config.ini
```
mode = production
```


### react:
```
cp package.json.example package.json
cp webpack.config.js.example webpack.config.js

npm cache clean
npm install --verbose
npm update
npm install --cache-min Infinity  # 从cache安装 cache位置: ~/.npm

npm install --dev --verbose # 否则babel不可用
```
@todo 修改webpack生成asset.ini的路径


## 修改前端环境变量
```
vi ~/.bashrc
export NODE_ENV=production
source ~/.bashrc
```
当NODE_ENV被设置为production后,npm install将不会安装devDependencies中的内容.如果需要更新devDependencies中的包,可以临时指定环境变量并执行:
```
export NODE_ENV=production
npm install --save
```


# vhost
<span id="vhost">==================================================================</span>

## 创建www用户
```
groupadd www
useradd -g www www
```

## 配置tengine

vi /usr/local/nginx/conf/nginx.conf
```
user  www www;
worker_processes  auto;
error_log  /data/log/error.log  notice;

pid /var/run/nginx.pid;  # write

events {
    use epoll;
    worker_connections 65535;
}

http {
    include       mime.types;
    default_type  application/octet-stream;
    log_format  main  '$remote_addr - $remote_user [$time_local] "$request" '
                      '$status $body_bytes_sent "$http_referer" '
                      '"$http_user_agent" "$http_x_forwarded_for"'
                      '$connection $upstream_addr '
                      'upstream_response_time $upstream_response_time request_time $request_time ';
    access_log  /data/log/access.log  main;

    server_names_hash_bucket_size 128;
    client_header_buffer_size 32k;
    large_client_header_buffers 4 32k;
    client_max_body_size 50m;

    charset utf-8;

    sendfile    on;
    tcp_nopush  on;
    tcp_nodelay on;

    keepalive_timeout  65;

    #php config
    fastcgi_connect_timeout 300;
    fastcgi_read_timeout    300;
    fastcgi_send_timeout    300;
    fastcgi_buffer_size 256k;
    fastcgi_buffers 6 256k;
    fastcgi_busy_buffers_size 256k;
    fastcgi_temp_file_write_size 256k;
    fastcgi_intercept_errors on;

    gzip on;
    gzip_min_length   1k;
    gzip_buffers      4 16k;
    gzip_http_version 1.0;
    gzip_comp_level   2;
    gzip_types        text/plain application/x-javascript text/css application/xml;
    gzip_vary on;

    include conf.d/*.conf;
}
```

## 配置php-fpm
vi /usr/local/nginx/conf/fastcgi.conf
```
fastcgi_param  SCRIPT_FILENAME    $document_root$fastcgi_script_name;
fastcgi_param  QUERY_STRING       $query_string;
fastcgi_param  REQUEST_METHOD     $request_method;
fastcgi_param  CONTENT_TYPE       $content_type;
fastcgi_param  CONTENT_LENGTH     $content_length;

fastcgi_param  SCRIPT_NAME        $fastcgi_script_name;
fastcgi_param  REQUEST_URI        $request_uri;
fastcgi_param  DOCUMENT_URI       $document_uri;
fastcgi_param  DOCUMENT_ROOT      $document_root;
fastcgi_param  SERVER_PROTOCOL    $server_protocol;
fastcgi_param  HTTPS              $https if_not_empty;

fastcgi_param  GATEWAY_INTERFACE  CGI/1.1;
fastcgi_param  SERVER_SOFTWARE    nginx/$nginx_version;

fastcgi_param  REMOTE_ADDR        $remote_addr;
fastcgi_param  REMOTE_PORT        $remote_port;
fastcgi_param  SERVER_ADDR        $server_addr;
fastcgi_param  SERVER_PORT        $server_port;
fastcgi_param  SERVER_NAME        $server_name;

 # PHP only, required if PHP was built with --enable-force-cgi-redirect
fastcgi_param  REDIRECT_STATUS    200;
```

vi /usr/local/nginx/conf/fastcgi_params
```
fastcgi_param  QUERY_STRING       $query_string;
fastcgi_param  REQUEST_METHOD     $request_method;
fastcgi_param  CONTENT_TYPE       $content_type;
fastcgi_param  CONTENT_LENGTH     $content_length;

fastcgi_param  SCRIPT_NAME        $fastcgi_script_name;
fastcgi_param  REQUEST_URI        $request_uri;
fastcgi_param  DOCUMENT_URI       $document_uri;
fastcgi_param  DOCUMENT_ROOT      $document_root;
fastcgi_param  SERVER_PROTOCOL    $server_protocol;
fastcgi_param  HTTPS              $https if_not_empty;

fastcgi_param  GATEWAY_INTERFACE  CGI/1.1;
fastcgi_param  SERVER_SOFTWARE    nginx/$nginx_version;

fastcgi_param  REMOTE_ADDR        $remote_addr;
fastcgi_param  REMOTE_PORT        $remote_port;
fastcgi_param  SERVER_ADDR        $server_addr;
fastcgi_param  SERVER_PORT        $server_port;
fastcgi_param  SERVER_NAME        $server_name;

 # PHP only, required if PHP was built with --enable-force-cgi-redirect
fastcgi_param  REDIRECT_STATUS    200;
```


## 配置项目虚拟主机
```
mkdir /usr/local/nginx/conf/conf.d
```

### laravel-react:
```
mkdir /data/log/laravel-react
```

vi /usr/local/nginx/conf/conf.d/laravel-react.conf
```
server {
    listen  80;
    server_name laravel-react.woojean.com;
    root  /data/www/deploy/DemoProjects/laravel-react/public/;
    index  index.php index.html;
    charset utf-8;
    client_max_body_size 50m;
    error_log  /data/log/laravel-react/error.log;
    access_log /data/log/laravel-react/access.log main;
    add_header X-Frame-Options "SAMEORIGIN";

    if  ( !-f $request_filename ) {
        rewrite /(.*) /index.php last;
    }

    location ~ \.php {
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_split_path_info ^(.+\.php)(.*)$;
        fastcgi_param PATH_INFO $fastcgi_path_info;
        fastcgi_param PATH_TRANSLATED $document_root$fastcgi_path_info;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ ^/status$ {
        include fastcgi_params;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

### phalcon:
```
mkdir /data/log/phalcon
```

vi /usr/local/nginx/conf/conf.d/phalcon.conf
```
server {
    listen  80;
    server_name phalcon.woojean.com;
    root  /data/www/deploy/DemoProjects/phalcon/public/;
    index  index.php index.html;
    charset utf-8;
    client_max_body_size 50m;
    error_log  /data/log/phalcon/error.log;
    access_log /data/log/phalcon/access.log main;
    add_header X-Frame-Options "SAMEORIGIN";

    if  ( !-f $request_filename ) {
        rewrite /(.*) /index.php last;
    }

    location ~ \.php {
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_split_path_info ^(.+\.php)(.*)$;
        fastcgi_param PATH_INFO $fastcgi_path_info;
        fastcgi_param PATH_TRANSLATED $document_root$fastcgi_path_info;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ ^/status$ {
        include fastcgi_params;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

初始化一个asset.ini文件，否则di注册时会报异常，网站无法访问。
```
touch /data/www/deploy/DemoProjects/phalcon/assets.ini
```

### react: 
```
mkdir /data/log/react
```

vi /usr/local/nginx/conf/conf.d/react.conf
```
server {
    listen  80;
    server_name react.woojean.com;
    root  /data/www/deploy/DemoProjects/phalcon/public/;
    index  index.php index.html;
    charset utf-8;
    client_max_body_size 50m;
    error_log  /data/log/react/error.log;
    access_log /data/log/react/access.log main;
    add_header X-Frame-Options "SAMEORIGIN";

    if  ( !-f $request_filename ) {
        rewrite /(.*) /index.php last;
    }

    location ~ \.php {
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_split_path_info ^(.+\.php)(.*)$;
        fastcgi_param PATH_INFO $fastcgi_path_info;
        fastcgi_param PATH_TRANSLATED $document_root$fastcgi_path_info;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ ^/status$ {
        include fastcgi_params;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

## host
vi /etc/hosts
```
0.0.0.0 laravel-react.woojean.com
0.0.0.0 phalcon.woojean.com
0.0.0.0 react.woojean.com
```


# domain
<span id="domain">==================================================================</span>
A记录,略.

## 测试
phalcon.woojean.com/test/ok


# 常用操作
<span id="operations">==================================================================</span>

106.14.26.249 Deploy@2017

netstat -ntlp

/etc/init.d/php-fpm start
kill -9 **
/etc/init.d/php-fpm reload

nginx -s stop
/usr/local/nginx/sbin/nginx -c /usr/local/nginx/conf/nginx.conf
nginx -s reload

nohup redis-server /etc/redis.conf &

redis-cli -h 127.0.0.1 -p 6379 -a ******
flushdb

/var/log/php-fpm/www-error.log

chmod -R 777 /data
chown -R www:www /data

chmod -R 777 /var/log/www
chown -R www:www /var/log/www

echo 1 > /proc/sys/vm/drop_caches















