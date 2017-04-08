---
layout: post
title:  "修复MAMP中mysql.slave_relay_log_info等表丢失的问题"
date: 2017-04-08 00:00:04
categories: 配置记录
tags: Mac
excerpt: ""
---


```
drop table innodb_index_stats;
drop table innodb_table_stats;
drop table slave_master_info;
drop table slave_relay_log_info;
drop table slave_worker_info;

FLUSH TABLES innodb_table_stats;

CREATE TABLE IF NOT EXISTS innodb_table_stats (
	database_name			VARCHAR(64) NOT NULL,
	table_name			VARCHAR(64) NOT NULL,
	last_update			TIMESTAMP NOT NULL NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	n_rows				BIGINT UNSIGNED NOT NULL,
	clustered_index_size		BIGINT UNSIGNED NOT NULL,
	sum_of_other_index_sizes	BIGINT UNSIGNED NOT NULL,
	PRIMARY KEY (database_name, table_name)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_bin STATS_PERSISTENT=0


```

```
cd /Applications/MAMP/db/mysql56/mysql
ls *.ibd

展示：
innodb_index_stats.ibd   slave_master_info.ibd    slave_worker_info.ibd
innodb_table_stats.ibd   slave_relay_log_info.ibd

rm -f *.ibd

rm innodb_index_stats.frm slave_master_info.frm slave_worker_info.frm innodb_table_stats.frm slave_relay_log_info.frm
```

```
cd /Library/Application Support/appsolute/MAMP PRO/db/mysql56/mysql
ls *.ibd
rm -f *.ibd

rm innodb_index_stats.frm slave_master_info.frm slave_worker_info.frm innodb_table_stats.frm slave_relay_log_info.frm
```



重启。

```
/Applications/MAMP/Library/bin/mysql --host=localhost -uroot -p123456 && use mysql;
source /Applications/MAMP/Library/share/mysql_system_tables.sql
```

