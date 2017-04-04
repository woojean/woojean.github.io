# Activity通信

activity调用startActivity(...)方法时，调用请求实际发给了ActivityManager，ActivityManager负责创建Activity实例，并调用其onCreate()方法。

Intent对象是Android Component（Activity、Service、Broadcast Receiver、Content Provider）之间用来通信的一种媒介工具。如果通过指定Context与Class对象的方式来创建Intent，则创建的是显式Intent，否则就是隐式Intent（指定动作和标志）。显式Intent通常用于同一个应用内的通信，隐式Intent通常用于不同应用间的通信。

启动Activity-不带参数：
```java
Intent i = new Intent(XxxActivity.this,YyyActivity.class);
startActivity(i);
```
启动Activity-带参数：
```java
Intent i = new Intent(XxxActivity.this,YyyActivity.class);
boolean param = true;
i.putExtra(“PARAM_NAME”,param);
startActivity(i);
```
被启动Activity获取参数：
```java
@Override
protected void onCreate(Bundle savedInstanceState){
super.onCreate(savedInstanceState);
mParam = getIntent().getBooleanExtra(“PARAM_NAME”);
```
启动Activity后，获取被启动Activity的返回值：
启动：
```java
Intent i = new Intent(XxxActivity.this,YyyActivity.class);
boolean param = true;
i.putExtra(“PARAM_NAME”,param);
startActivityForResult(i,REQUEST_CODE);
```
被启动Activity返回：
```java
Intent i= new Intent();
i.putExtra(“RETURN_VALUE”,mReturn);
setResult(RESULT_OK,i);  // 另一个可取标记为Activity.RESULT_CANCELED
```
接收返回值：
```java
@Override
protected void onActivityResult(int requestCode, int resultCode, Intent data){
if(data == null){
return;
}
mParam = data.getBooleanExtra(“PARAM_NAME”,false);
}
```
注意这里onActivityResult(...)方法的签名中既需要启动时的requestCode，也需要返回时的resultCode。

ActivityManager维护着一个非特定应用独享的回退栈，所有应用的activity都共享该回退栈。即ActivityManager被设计成操作系统级的activity管理器来负责启动activity，不局限于单个应用，回退栈作为一个整体共享给操作系统及设备使用。当用户点击一个应用时，实际是启动了该应用的launcher activity，并将它加入到activity栈中。