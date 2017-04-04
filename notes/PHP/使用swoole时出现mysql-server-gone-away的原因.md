# 使用swoole时出现mysql-server-gone-away的原因

使用swoole时出现mysql-server-gone-away的原因
`mysql本身是一个多线程的程序`，每个连接过来，会开一个线程去处理相关的query, mysql会定期回收长时间没有任何query的连接(时间周期受wait_timeout配置影响)，所以在swoole中，由于是一个长驻内存的服务，我们建立了一个mysql的连接，不主动关闭 或者是用pconnect的方式，那么这个mysql连接会一直保存着，然后长时间没有和数据库有交互，就主动被mysql server关闭了，之后继续用这个连接，就报mysql server gone away了。

解决方法：
1.修改mysql的`wait_timeout`值为一个非常大的值，此方法不太可取，可能会产生大量的sleep连接，导致mysql连接上限了， 建议不使用。

2.每次query之前主动进行连接检测
//如果是用mysqli，可用内置的mysqli_ping
```php
if (!$mysqli->ping()) {  
	mysqli->connect(); //重连
}
```

//如果是pdo，可以检测mysql server的服务器信息来判断
```php
 try {
	$pdo->getAttribute(\PDO::ATTR_SERVER_INFO);
} catch (\Exception $e) {
	if ($e->getCode() == 'HY000') {
		$pdo = new PDO(xxx);  //重连
	} else {
		throw $e;
	}
}
```
这个方案有个缺点：额外多一次请求，所以改进方法: 用一个全局变量存放最后一次query的时间，下一次query的时候先和现在时间对比一下，超过waite_timeout再重连. 或者也可以用swoole_tick定时检测。

3.被动检测，每次query用try catch包起来，如有mysql gone away异常，则重新连接，再执行一次当前sql.
```php
try {
	query($sql);
} catch (\Exception $e) {
	if ($e->getCode() == 'HY000') {
		reconnect(); //重连
		query($sql)
	} else {
		throw $e;
	}
}
```

4.用`短连接`，务必每次操作完之后，手动close