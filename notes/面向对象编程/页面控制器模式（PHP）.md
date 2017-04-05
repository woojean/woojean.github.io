# 页面控制器模式（PHP）

比前端控制器更简单的实现：控制逻辑被放在视图里面。
最简单的页面控制器代码如下：

```php
 	<?php
require_once("woo/domain/Venue.php");
try {
    $venues = \woo\domain\Venue::findAll();
} catch ( Exception $e ) {
    include( 'error.php' );
    exit(0);
}
// default page follows
?>
<html>
<head>
<title>Venues</title>
</head>
<body>
<h1>Venues</h1>

<?php foreach( $venues as $venue ) { ?>
    <?php print $venue->getName(); ?><br />
<?php } ?>

</body>
</html>

```
模板视图和视图助手
不应该在视图中混入业务逻辑代码，应该把应用处理逻辑放在视图外的地方，只允许视图执行“显示数据”的功能，通常的做法是先得到数据，再将数据传递给视图。当一个视图需要访问系统数据时，可以提供一个视图助手对象来帮助视图达到目的：
```php
<?php
namespace woo\view;

// 定义一个视图助手类
class VH {
    static function getRequest() {
        return \woo\base\RequestRegistry::getRequest();
    }
}

?>
```
在视图中使用视图助手类：
```php
require_once("woo/view/ViewHelper.php");
$request = \woo\view\VH::getRequest();		// 通过视图助手类来获得请求对象
$venue = $request->getObject('venue');			// 获得视图所需的数据
?>

<html>
<head>
<title>Add a Space for venue <?php echo $venue->getName() ?></title>
</head>
<body>
<h1>Add a Space for Venue '<?php print $venue->getName() ?>'</h1>

<table>
<tr>
<td>
<?php print $request->getFeedbackString("</td></tr><tr><td>"); ?>
</td>
</tr>
</table>

<form method="post">
    <input type="text"
     value="<?php echo $request->getProperty( 'space_name' ) ?>" name="space_name"/>
    <input type="hidden" name="venue_id" value="<?php echo $venue->getId() ?>" />
    <input type="submit" value="submit" />
</form>

</body>
</html>
```