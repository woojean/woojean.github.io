# 安装Nginx依赖哪些条件？

（1）编译环境gcc g++ 开发库之类的需要提前装好
（2）安装PCRE库，为了重写（rewrite）：PCRE(Perl Compatible Regular Expressions)是一个Perl库，包括perl兼容的正则表达式库。
（3）安装zlib库，为了gzip压缩。
（4）安装ssl

./configure --sbin-path=/usr/local/nginx/nginx 
--conf-path=/usr/local/nginx/nginx.conf 
--pid-path=/usr/local/nginx/nginx.pid 
--with-http_ssl_module
--with-pcre=/usr/local/src/pcre-8.34 			
--with-zlib=/usr/local/src/zlib-1.2.8 			
--with-openssl=/usr/local/src/openssl-1.0.1c