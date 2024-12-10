---
layout: post
title:  "Mac系统安装部署Nessus记录"
date: 2024-12-10 00:00:01
categories: 编程
tags: 后端安全
excerpt: "Web安全渗透测试"
---

* content
{:toc}


## 下载
https://www.tenable.com/downloads/nessus?loginAttempted=true

## 本地安装路径
/Library/Nessus/run/sbin

## 获取本地激活码
```
nessuscli fetch --challenge
```

72ce13e04d6d6da02c51aa5c9b0c3128695ee56a

## 注册
https://zh-cn.tenable.com/products/nessus/nessus-essentials


## 邮件激活码
QSRD-MXR2-U68L-9GMM-9FDL

## 访问
https://plugins.nessus.org/v2/offline.php

点击链接下载文件（压缩包+license共2个文件），放到目录：
```
/Library/Nessus/run/sbin
```

license
```
-----BEGIN TENABLE LICENSE-----
TjVveTl3MjZwakpPMWxPRnFHRUk2TnBaK2lvT1h0SHVraXQzcStQN1ZFN2t5L1JZMmR4NWFBL3li
djVDMzlQYTRIQmxmbFdmNStMLzliYWxkMEJ3YWNRQk9rUFdldTV2d1pMcWpySG9QNEN5ZDZBVFhN
NzE1MkpuQldUU3lEd0xMTkl6aXU5dGxYUkJFRXNaQnZOTlI5MURJNGtvdmZiVW42QTY1azBJZFF0
V1Fqb0tiVVZDWTVTOHlwT0k1enN3bzR3dDdWcjBVQ0dVZSt3RkI2VDY5aUF1NnIzOU10U3VCL0hj
K2hOcExVVmZieHJDdUxodW1NZnpPZml2T1Bma2RTWjdpK3B5WTB5Y1RmL3JjR0paWGg5cWhhVHEx
Nkordk9tRGZ6eFpoWDN2MVY3eCtFTDlyaVRqVUY0eTdLd0lEL3o1bzV4QjdWWjlxWVBOWThyY2pV
V0JSeTR6eTRYVE5lVWpGOE1CbE9LTDlGNGhTc0lTWEl1dnYySnJHclVyOWlMNGRJUE1vKytmWHEw
T1BrdW5oTnZhNGduSmk4bnp5cWh5a3BEUTVTNExlTHI3ZWdGZVVITG9LRU1mVTdwS1dlNHlKMWZi
U2UvOW9oWU1DbHpKWFpmb01mS0FCOS9iZXNjNEt3UHh6TDMyU24xTnZlZUZUZXRmNXhsSUlPSlNr
amlXb05tR2hPSkY1QUsxM2lJTXRqNmVpRHZIMXIzcm5ubGtuTWxsaGU3eTdCNGNzZEdWbXNzRU9Q
Z0VuRUNnUnVydjM0dTFpNDVpUmZ1bmNGVEZtc1VkUWFCb3JYeFVndXVLL0tpMlhHcitPZFhFUGhp
QTZESElhcEdaZ0Ric2VHTnFXbXR1TW0xYWxKblQ3Y3RXSGg5QXVCUGlHUkpIQTZhRVdRYkswejg9
DQp7ImFjdGl2YXRpb25fY29kZSI6IlFTUkQtTVhSMi1VNjhMLTlHTU0tOUZETCIsInVwZGF0ZV9w
YXNzd29yZCI6IjlmNmI4MDg5MzNjMmQ0ZmI1MTBmZWM5ZWYyYjE3OTRlIiwicGVuZG9fYXBpX2tl
eSI6ImE3YzhkNWM0LWI2YWUtNDg2Ny03ZGFiLTY5YTkyYTA4NDdlMSIsIm5hbWUiOiJOZXNzdXMg
SG9tZSIsInR5cGUiOiJob21lIiwiZXhwaXJhdGlvbl9kYXRlIjoxODkxNTA2ODEwLCJjdXN0b21l
cl9pZCI6MCwiaXBzIjoxNiwidXBkYXRlX2xvZ2luIjoiMjEyYmJiMmQ0ZjcyM2FhMjc1OTI0M2Jm
ZTIxZDExMjkiLCJkcm0iOiIxMWNhNjhjM2QxYjQ0ZDRlNDgzNTU0MDRmODFjZjE4NiJ9
-----END TENABLE LICENSE-----
```


## 安装插件
```
cd /Library/Nessus/run/sbin
sudo ./nessuscli update all-2.0.tar.gz
```

输出：
```
[info] Copying templates version 202412031651 to /Library/Nessus/run/var/nessus/templates/tmp
[info] Finished copying templates.
[info] Moved new templates with version 202412031651 from plugins dir.
[info] Moved new pendo client with version 2.169.1
 from plugins dir.
 * Update successful.  The changes will be automatically processed by Nessus.
 ```


## 停止服务
sudo ./nessusmgt stop "Tenable Nessus"

## 启动服务
sudo ./nessus-service


## 本地访问服务
[https://localhost:8834/#/scans/folders/my-scans](https://localhost:8834/#/scans/folders/my-scans)


## 修改plugin_feed_info.inc文件，一共2处：
```
sudo vi /Library/Nessus/run/var/nessus/plugin_feed_info.inc
sudo vi /Library/Nessus/run/lib/nessus/plugins/plugin_feed_info.inc
```

内容：
```
PLUGIN_SET = "202412031651"
PLUGIN_FEED = "ProfessionalFeed(Direct)"
PLUGIN_FEED_TRANSPORT = "TenableNetwork Security Lightning";
```

修改文件权限，防止重启服务后被覆盖（会被清空内容）：
```
sudo chmod 444 /Library/Nessus/run/var/nessus/plugin_feed_info.inc
sudo chmod 444 /Library/Nessus/run/lib/nessus/plugins/plugin_feed_info.inc
```

## 更新
```
sudo ./nessuscli update --plugins-only
```











