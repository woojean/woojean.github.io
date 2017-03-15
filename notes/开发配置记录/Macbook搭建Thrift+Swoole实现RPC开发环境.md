# Macbook搭建Thrift+Swoole实现RPC开发环境

## 安装thrift

```
brew install thrift
ln -s /usr/local/Cellar/thrift/0.10.0/bin/thrift /usr/local/bin/thrift
```



## 安装thrift的PHP扩展

```
brew install homebrew/php/php56-thrift
extension=/usr/local/Cellar/php56-thrift/0.9.2/thrift_protocol.so
/Applications/MAMP/bin/php/php5.6.30/bin/php -m | grep thrift
```



## 新建thrift接口文件

/Users/wujian/learn/demo/thrift-php/hello.thrift

```
service HelloWorld
{
    string sayHello()
}
```

生成文件：

```
cd /Users/wujian/learn/demo/thrift-php
thrift -gen php:server hello.thrift
```



composer require apache/thrift



## 编写Client



## 编写Server



## 测试

```
php -S localhost:9090
/Applications/MAMP/bin/php/php5.6.30/bin/php server.php
/Applications/MAMP/bin/php/php5.6.30/bin/php client.php --http
```







# 安装boost

version: 1.63.0

```
cd extensions/boost_1_63_0
./bootstrap.sh
sudo ./b2 threading=multi address-model=64 variant=release stage install
```



# 安装libevent

version: 2.0.22

```
brew install openssl 
brew link openssl --force

vi ~/.bashrc
export C_INCLUDE_PATH=/usr/local/opt/openssl/include:$C_INCLUDE_PATH
export CPLUS_INCLUDE_PATH=/usr/local/opt/openssl/include:$CPLUS_INCLUDE_PATH
export LIBRARY_PATH=/usr/local/opt/openssl/lib:$LIBRARY_PATH
export LD_LIBRARY_PATH=/usr/local/opt/openssl/lib:$LD_LIBRARY_PATH

cd extensions/libevent-2.0.22-stable
./configure --prefix=/usr/local

make 
sudo make install
```



# 安装bison

version: 2.5.1

```
cd extensions/bison-2.5.1
./configure --prefix=/usr/local/
make
sudo make install
```



# 安装thrift

version: 0.10.0

```
vi ~/.bashrc
PHP_PREFIX=/Applications/MAMP/bin/php/php5.6.30/lib/php

[安装jdk]

cd extensions/thrift-0.10.0
./configure --prefix=/usr/local/ --with-boost=/usr/local --with-libevent=/usr/local --without-perl --without-cpp --without-python
sudo make
sudo make install
```



# 安装swoole

version: 1.9.6

```

```























