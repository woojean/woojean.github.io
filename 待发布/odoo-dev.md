# config解析
/tools/config.py
_parse_config函数，opt变量：
```
{'config': None, 'save': False, 'init': None, 'update': None, 'without_demo': None, 'import_partial': None, 'pidfile': None, 'addons_path': None, 'upgrade_path': None, 'server_wide_modules': None, 'data_dir': None, 'http_interface': None, 'http_port': None, 'longpolling_port': None, 'http_enable': None, 'proxy_mode': None, 'dbfilter': None, 'test_file': None, 'test_enable': None, 'test_tags': None, 'screencasts': None, 'screenshots': None, 'logfile': None, 'syslog': None, 'log_handler': [], 'log_db': None, 'log_db_level': None, 'log_level': None, 'email_from': None, 'smtp_server': None, 'smtp_port': None, 'smtp_ssl': None, 'smtp_user': None, 'smtp_password': None, 'db_name': None, 'db_user': None, 'db_password': None, 'pg_path': None, 'db_host': None, 'db_port': None, 'db_sslmode': None, 'db_maxconn': None, 'db_template': None, 'load_language': None, 'language': None, 'translate_out': None, 'translate_in': None, 'overwrite_existing_translations': None, 'translate_modules': None, 'list_db': None, 'dev_mode': None, 'shell_interface': None, 'stop_after_init': None, 'osv_memory_count_limit': None, 'osv_memory_age_limit': None, 'max_cron_threads': None, 'unaccent': None, 'geoip_database': None, 'workers': None, 'limit_memory_soft': None, 'limit_memory_hard': None, 'limit_time_cpu': None, 'limit_time_real': None, 'limit_time_real_cron': None, 'limit_request': None}
```



# orm缓存
/tools/cache.py

LRU cache decorator for model methods.
The parameters are strings that represent expressions referring to the signature of the decorated method, and are used to compute a cache key.

Demo:
```python
@ormcache('model_name', 'mode')
	def _compute_domain(self, model_name, mode="read"):
		...
```
Methods implementing this decorator should never return a Recordset,because the underlying cursor will eventually be closed and raise a `psycopg2.OperationalError`.

**总结:**
* 内存缓存；
* LRU算法；
* 基于方法装饰器生成key，方法执行结果为value，所以应该在纯函数中使用；



# TransientModel
```python
class TransientModel(Model):
    """ Model super-class for transient records, meant to be temporarily
    persistent, and regularly vacuum-cleaned.

    A TransientModel has a simplified access rights management, all users can
    create new records, and may only access the records they created. The
    superuser has unrestricted access to all TransientModel records.
    """
    _auto = True                # automatically create database backend
    _register = False           # not visible in ORM registry, meant to be python-inherited only
    _abstract = False           # not abstract
    _transient = True           # transient
```
TransientModel是一种特殊的Model，TransientModel对应的数据表中的数据系统会定时的清理；TransientModel的数据只能做临时数据使用，一般向导对象模型会声明成TransientModel。




# 如果在数据库已有记录的情况下执行-i base会重置数据库吗？（高危操作！！！）
执行：
```
update account_account set name = 'winston' where id =1;
```

再执行：
```
./odoo-bin --config=config/odoo.conf -i base
```

查询：
```
select * from account_account where id =1;
```
发现id为1的记录，name仍然是winston，但是之前安装的sale模块的相关表都不见了（即sale_前缀的表），同时在网站可以看到sals模块是已安装状态。
即模块已安装，但是数据表没有了，于是会有如下各种报错：
```
2020-05-08 06:06:43,866 19496 ERROR odoo_db odoo.modules.loading: Some modules have inconsistent states, some dependencies may be missing: ['sale_crm', 'sale_management']
2020-05-08 06:06:43,867 19496 ERROR odoo_db odoo.modules.loading: Some modules are not loaded, some dependencies or manifest may be missing: ['crm', 'crm_sms', 'procurement_jit', 'sale', 'sale_stock', 'sales_team', 'website_crm', 'website_crm_sms']
```


# PHP调用
使用了Ripcord库，它提供一种简单的XML-RPC API。 Ripcord要求在PHP安装中启用对XML-RPC支持。
因为调用通过HTTPS进行执行，还要求启用OpenSSL插件。

报错：
```
PHP XMLRPC library is not installed
```

安装php xmlrpc扩展：

https://github.com/php/php-src
```
cd /Users/wujian/extensions/php-src-master/ext/xmlrpc
sudo phpize
sudo ./configure
sudo make & make install 


```


附：
RPM包：
https://www.rpmfind.net/linux/rpm2html/search.php?query=xmlrpc.so()(64bit)

MAMP的默认php扩展位置：
/Applications/MAMP/bin/php/php7.1.1/lib/php/extensions/no-debug-non-zts-20160303



CentOS上编译的so文件，拿到Mac上照样使用，可能是因为这个库年代久远的缘故。



# 看不见sale模块
/Users/wujian/Library/Application Support/Odoo/addons/13.0























