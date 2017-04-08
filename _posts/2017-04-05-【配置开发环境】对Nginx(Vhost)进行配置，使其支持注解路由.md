---
layout: post
title:  "对Nginx Vhost进行配置，使其支持Phalcon注解路由"
date: 2017-04-05 00:00:01
categories: 配置开发环境
tags: Nginx Phalcon
excerpt: ""
---


```
server {
    listen  80;
    server_name product.wsn.troublehub.com;
    root  /data/www/product/public;
    index  index.php index.html;
    charset utf-8;
    client_max_body_size 50m;
    error_log  /data/log/product/error.log;
    access_log /data/log/product/access.log main;
    add_header X-Frame-Options "SAMEORIGIN";

    #if  ( !-f $request_filename ) {
    #    rewrite /(.*) /index.php last;
    #}

    location / {
        try_files $uri $uri/ /index.php?_url=$uri&$args;
    }

    location ~ \.php {
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  /index.php;
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

    location ~ /\.ht {
        deny all;
    }
}
```

 

重点就是：

```
location / {
        try_files $uri $uri/ /index.php?_url=$uri&$args;
    }
```

