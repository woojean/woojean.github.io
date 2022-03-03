---
layout: post
title:  "MySQL与PostgreSQL的区别"
date: 2022-03-02 00:00:01
categories: 编程
tags: Odoo MySQL PostgreSQL
excerpt: ""
---

* content
{:toc}


* PostgreSQL不属于任何公司，这是相对于MySQL最大的优势（MySQL属于Oracle）。

* MySQL是C/C++混合开发，PostgreSQL则是完全的C语言开发。

* MySQL是单存储引擎，PostgreSQL是多存储引擎，包括InnoDB、MyISAM等。

* 删除临时表的时候，PostgreSQL语句没有TEMP、TEMPORARY关键字，DROP TABLE通过数据库连接的排列被删除。MySQL支持TEMP、TEMPORARY关键字，DROP TABLE语句只允许删除临时表，要手动删除。PostgreSQL支持CASCADE选择删除表的依赖对象，PostgreSQL的TRUNCATE TABLE支持功能更多。MySQL TRUNCATE TABLE不支持CASCADE事物安全，数据删除之后就没办法回滚了。

* PostgreSQL支持多种高级数据类型，比如array，用户也可以定义类型，MySQL只支持标准类型。PostgreSQL支持布尔型，支持IP地址数据类型，支持常量和函数调用。PostgreSQL支持JSON和其他NoSQL功能，本机支持XML，允许索引JSON数据，MySQL支持JSON，不过不支持其他的NoSQL功能。


* **PostgreSQL是多进程、MySQL是多线程**。PostgreSQL支持大多数命令类型上触发的触发器。MySQL是异步复制，PostgreSQL支持同步、异步、半同步复制。PostgreSQL要求所有数据必须完全满足需求，只要出一个错误整个数据入库过程都要失败，不过MySQL没这样的问题。

* MySQL 里需要 utf8mb4 才能显示 emoji ；


* 

















