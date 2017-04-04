# mysqli的Prepared语句有什么好处？

mysqli支持prepared语句，好处有2：
1）对于在执行大量具有不同数据的相同查询时，可以提高执行速度；
2）可以免受SQL注入攻击；
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