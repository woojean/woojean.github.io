# SDK版本

SDK最低版本：操作系统会拒绝将应用安装在系统版本低于该标准的设备上。
SDK目标版本：告知系统该应用是给哪个API级别去运行的，高于目标版本的系统功能将被忽略。
SDK编译版本：该设置不会出现在Manifest文件中（不同于最低版本与目标版本），不会通知给操作系统，而是用于编译时指定具体要使用的系统版本。

检查设备的Android系统的编译版本：
if( Build.VERSION.SDK_INT >= Build.VERSION_CODES.HONEYCOMB ){
...
}

禁止Lint提示兼容性问题：
```java
@TargetApi(11)
@Override
protected void onCreate(Bundle savedInstanceState){
...
if( Build.VERSION.SDK_INT >= Build.VERSION_CODES.HONEYCOMB ){
...
}
}
```