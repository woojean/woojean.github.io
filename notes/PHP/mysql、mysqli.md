# mysql、mysqli

PHP-MySQL（mysql）是PHP操作MySQL数据库最原始的Extension，PHP-MySQLi（mysqli）的i代表 Improvement ，提供了相对进阶的功能，也增加了安全性。而PDO (PHP Data Object) 则是提供了一个 Abstraction Layer 来操作数据库。

mysql是非持继连接函数而mysqli是永远连接函数。也就是说mysql每次链接都会打开一个连接的进程而mysqli多次运行mysqli将使用同一连接进程,从而减少了服务器的开销。
如果使用new mysqli('localhost', usenamer', 'password', 'databasename')总是报错，Fatal error: Class 'mysqli' not found in ...那么要检查一下mysqli是不是开启的。mysqli类不是默认开启的，win下要改php.ini,去掉php_mysqli.dll前的;,linux下要把mysqli编译进去。
当然，如果mysql也需要永久连接的话，就可以使用mysql_pconnect()这个函数。

mysqli的用法：
$mysqli = new mysqli('localhost', 'my_user', 'my_password', 'my_db');

mysql的用法：
$link = mysql_connect('example.com:3307', 'mysql_user', 'mysql_password');

例（使用mysql）：
```php
<?php
$con = mysql_connect("localhost","peter","abc123");
if (!$con)
  	{
  		die('Could not connect: ' . mysql_error());
  	}

mysql_select_db("my_db", $con);

$result = mysql_query("SELECT * FROM Persons");

while($row = mysql_fetch_array($result))
  	{
  		echo $row['FirstName'] . " " . $row['LastName'];
  		echo "<br />";
  	}

mysql_close($con);
?>
```

使用mysqli：
```php
$searchtype = $_POST[‘searchtype’];
$searchterm = $_POST[‘searchterm’];
if(!searchterm  || !searchterm’){
echo “Invalid”; //查询条件为空
}

if(!get_magic_quotes_gpc()){
$searchtype = addslashes($searchtype);
$searchterm = addslashes($searchterm );
}
@$db = new mysqli(‘localhost’,'my_user', 'my_password', 'db_bookes');
if(mysqli_connect_errno()){
		echo “Could not connect”; //连接失败
}
$query = “select * from books where ”.$searchtype.” like ‘%”.$searchterm.”%’”;
$result = $db->query($query);
$num_results = $result->num_rows;
echo “Numbers of books:”.$num_results;
for($i=0; $i<$num_results; $i++){
$row = $result->fetch_assoc();
echo htmlspecialchars(stripslashes($row[‘title’]));
...
}
$result->free();
$db->close();
```

### Prepared语句
mysqli支持prepared语句，好处有2：
对于在执行大量具有不同数据的相同查询时，可以提高执行速度；
可以免受SQL注入攻击；
通常的数据库插入操作如下：
```php
$query = “insert into books values(‘”.$isbn.”’,’”.$author.”’,’”.$title.”’,’”.$price.”’)”;
$result = $db->query($query);
if($result){
echo $db->affected_rows;
...
```
Prepared语句的基本思想是向MySql发送一个需要执行的查询模板，然后再单独发送数据。因此可以向相同的Prepared语句发送大量相同的数据，对于批处理的插入操作来说是非常有用的。
```php
$query = “insert into books values(?,?,?,?)”;
$stmt = $db->prepare($query);
$stmt->bind_param(“sssd”,$isbn,$author,$title,$price); //sssd为格式化字符串
$stmt->execute();
echo $smtt->affected_rows;
$stmt->close();
```
对于查询操作，也可以绑定查询结果至变量：
$stmt->bind_result($isbn,$author,$title,$price);