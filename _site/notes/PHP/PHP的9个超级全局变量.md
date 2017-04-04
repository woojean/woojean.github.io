# PHP的9个超级全局变量

$GLOBALS：所有全局变量数组，作用和global关键字一样
$_SERVER：服务器环境变量数组
$_GET：通过get方法传递给该脚本的变量数组
$_POST：通过post方法传递给该脚本的变量数组
$_COOKIE：cookie变量数组
$_FILE：与文件上传相关的变量数组
$_ENV：环境变量数组
$_REQUEST：所有用户输入的变量数组，包括$_GET、$_POST、$_COOKIE所包含的输入内容
$_SESSION：会话变量数组