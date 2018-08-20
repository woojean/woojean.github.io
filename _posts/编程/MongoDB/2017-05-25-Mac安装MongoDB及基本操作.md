---
layout: post
title:  "Mac安装MongoDB及基本操作"
date: 2017-05-25 00:00:01
categories: 编程
tags: MongoDB
excerpt: ""
---

* content
{:toc}

# 安装
```
brew update
brew install mongodb  // brew install mongodb --with-openssl
```

# 准备数据目录
```
mkdir -p ~/data/mongodb  // ensure that the user account running mongod has read and write permissions for the directory.
```
默认目录为/data/db。


# 导入数据库
数据库文件：https://raw.githubusercontent.com/mongodb/docs-assets/primer-dataset/primer-dataset.json
```
mongoimport --db test --collection restaurants --drop --file ~/Downloads/primer-dataset.json
```
如果MongoDB中已存在同名的Collection，则会先删除已有的Collection后再导入。


# 运行Server端
```
mongod --dbpath ~/data/mongodb
```

# 运行Shell
```
mongo
```

# 查看帮助
```
> help
	db.help()                    help on db methods
	db.mycoll.help()             help on collection methods
	sh.help()                    sharding helpers
	rs.help()                    replica set helpers
	help admin                   administrative help
	help connect                 connecting to a db help
	help keys                    key shortcuts
	help misc                    misc things to know
	help mr                      mapreduce

	show dbs                     show database names
	show collections             show collections in current database
	show users                   show users in current database
	show profile                 show most recent system.profile entries with time >= 1ms
	show logs                    show the accessible logger names
	show log [name]              prints out the last segment of log in memory, 'global' is default
	use <db_name>                set current database
	db.foo.find()                list objects in collection foo
	db.foo.find( { a : 1 } )     list objects in foo where a == 1
	it                           result of the last line evaluated; use to further iterate
	DBQuery.shellBatchSize = x   set default number of items to display on shell
	exit                         quit the mongo shell
```

比如：
```
> show dbs
admin  0.000GB
local  0.000GB
test   0.005GB
```



