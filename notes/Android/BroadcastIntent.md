# Broadcast Intent

Broadcast Intent可以同时被多个组件接收，Broadcast Receiver负责接收各种Broadcast Intent。
 ![image](https://github.com/woojean/woojean.github.io/blob/master/images/android_6.png)

例：实现随设备重启而重启的定时器
设备重启后，那些持续运行的应用通常也需要重启，通过监听具有BOOT_COMPLETE操作的Broadcast Intent可得知设备是否已完成启动。
BroadcastReceiver与Service、Activity一样，属于可以接收Intent的组件，需要在系统中登记。

修改之前的Service的代码，在设置Service启停的方法中往Preferences中加入一个定时器当前启停状态的标识：
```java
public class PollService extends IntentService{
public static void setServiceAlarm(Context context,boolean isOn){
...
PreferenceManager.getDefaultSharedPreferences(context)
.edit()
.putBoolean(PollService.PREF_IS_ALARM_ON，isOn) // 保存定时器的启停状态
.commit();
}
}
```
创建一个broadcast receiver：
```java
public class StartupReceiver extends BroadcastReceiver{

@Override
public void onReceive(Context context,Intent intent){
SharedPreferences prefs =  PreferencesManager.getDefaultSharedPreferences(context);
boolean isOn = prefs.getBoolean(PollService.PREF_IS_ALARM_ON,false);
PollService.setServiceAlarm(context,isOn);
}
}
```
在Manifest文件中登记broadcast receiver：
在配置文件中完成声明后，即使应用当前并未运行，只要有匹配的broadcast intent发来，broadcast receiver就会接收，并执行onReceive方法，broadcast receiver随即被销毁。因为broadcast receiver的存在非常短暂，因此其作用有限，例如无法使用任何异步API或登记任何监听器，因为onReceive(...)方法刚运行完，receiver就不存在了。此外，onReceive(...)方法运行在主线程上，因此不能在该方法内做一些耗时的任务，如网络连接，数据读写存储等。broadcast receiver适合处理一些轻型的任务，比如这里的重置定时器。
```xml
<manifest ...>
...
<uses-permission android:name=”android.permission.RECEIVE_BOOT_COMPLETED” />
...
<application>
...
<receiver android:name=”.StartupReceiver”>
<intent-filter>
<action android:name=”android.intent.action.BOOT_COMPLETED” />
</intent-filter>
</receiver>
</application>
</manifest>
```
例：动态的Broadcast Receiver
目前的通知消息存在一个问题：应用的通知消息虽然工作良好，但是在打开应用后依然会收到通知消息（即，在使用某个应用的过程中不应该再收到该应用的新通知）。

解决思路：
有两个receiver，一个静态注册，用来实际发送消息，其优先级非常低。另有一个动态注册，即在特定Fragment的onResume()中注册，在onPause()中注销。
因为应用监听了系统重启事件，并开启定时器，所以只要打开设备，检查是否有图片更新的后台服务就会被定时器触发去运行。当发现有新的图片时，会以Activity.RESULT_OK为resultCode来执行sendOrderedBroadcast(...)。而在实际执行发送消息的receiver中会通过getResultCode()获取resultCode并进行判断，如果是Activity.RESULT_OK，则执行消息发送操作，否则直接return。
而动态注册的receiver也接收同样的action，可以处理同样的broadcast intent，且优先级更高，只要当前应用打开，则会创建加载指定Fragment，因此动态receiver得以在Fragment调用onResume()时进行注册，之后只要应用保持活动状态，该动态注册的receiver就会一直保持监听状态。当后台线程再次发出有序broadcast时，会被该动态注册的，具有更高优先级的receiver接收并处理：把resultCode改成Activity.CANCEL。之后，当实际负责发送消息的receiver处理broadcast intent时，便不会再发出消息。


发送broadcast intent：
```java
public class PollService extends IntentService{
...
// 定义一个操作常量
public static final String ACTION_SHOW_NOTIFICATION = “com.xxx.xx...”;
...
@Override
protected void onHandleIntent(Intent intent){
if(!resultId.equals(lastResultId)){
		// 得到一个新的查询结果，发出通知
...
notificationManager.notify(0,notification); 

// 广播我们定义的操作
sendBroadcast(new Intent(ACTION_SHOW_NOTIFICATION));
}
...
}
}
```
接收broadcast intent：
这里静态的receiver行不通，因为需要在PhotoGalleryFragment存在的时候才接收intent，静态的receiver很难做到在不断接收intent的同时还有确定PhotoGalleryFragment的存在状态。

新建一个用于隐藏前台通知的通用fragment：
```java
public abstract class VisibleFragment extends Fragment{
private BroadcastReceiver mOnShowNotification = new BroadcastReceiver(){
@Override
public void onReceive(Context context,Intent intent){
Toast.makeText(
getActivity(),
”获取到一个新的广播信息:”+intent.getAction(),
Toast.LENGTH_LONG).show();
}
};
// 在onResume()中注册receiver
@Override
public void onResume(){
super.onResume();
// 用一个动作名称来顶一个IntentFilter
IntentFilter filter = new IntentFilter(PollService.ACTION_SHOW_NOTIFICATION);
// 用一个BroadcastReceiver + 一个IntentFilter来注册一个register
getActivity().registerReceiver(mOnShowNotification,filter);
}

// 在onPause()中注销receiver
@Override
public void onPause(){
super.onPause();
getActivity().unregisterReceiver(mOnShowNotification);
}
} 
```
因为设备发生旋转时，Fragment的onCreate()和onDestroy()方法中返回的getActivity()不同，因此，如果想在onCreate()和onDestroy()中实现登记或者注销登记，应该使用getActivity().getApplicationContext()方法。

修改PhotoGalleryFragment类，使其继承于VisibleFragment：
public class PhotoGalleryFragment extends VisibleFragment{
...
}

使用私有权限：
目前系统中的任何应用都可以触发上面定义的动态receiver。如果receiver声明在manifest配置文件中，且仅限应用内部使用，则可在receiver标签上添加一个android:exported=”false”属性，这样系统中的其他应用就再也无法接触到该receiver。此外还可以通过创建自己的权限来进行限制。
<manifest
...
// 定义私有权限
<permission 
android:name=”com.xxx...”  // 与发送broadcast intent时定义的操作常量相同
android:protectionLevel=”signature” />
...
// 使用私有权限
<uses-permission
android:name=”com.xxx...” />
...
</manifest>

PollService中发送广播时的代码修改为：
public static final String PERM_PRIVATE = “com.xxx...”;
...
	sendBroadCast(new Intent(ACTION_SHOW_NOTIFICATION),PERM_PRIVATE); // 发送广播时带上权限，任何应用必须使用相同的权限才能接收该Intent。

在VisibleFragment中注册监听器时也指定权限：
getActivity().registerReceiver(mOnShowNotification,filter,PollService.PERM_PRIVATE,null);

权限本身只是一行简单的字符串，它需要出现在三个地方：
1.发送广播的时候
2.定义私有权限的时候
3.使用私有权限的时候

自定义的权限必须指定android:protectionLevel属性值，Android根据该值来确定自定义权限的使用方式，如上面android:protectionLevel=”signature”，则如果其他应用想要使用我们的自定义权限，必须使用和当前应用相同的key做签名认证。protectionLevel的可选值包括：
normal:应用安装前，用户会看到相应的安全级别，但无需主动授权，主要用来告诉用户可能带来的影响。RECEIVE_BOOT_COMPLETED、手机震动等使用该安全级别。
dangerous：normal安全级别控制以外的任何危险操作，如访问个人隐私、通过网络接口收发数据、使用可监视用户的硬件功能等。需要用户的明确授权。网络使用权限、相机使用、联系人信息使用等都属于该级别。
signature：如果应用签署了与声明应用一致的权限证书，则该权限由系统授予，否则系统作相应的拒绝。授予权限时，系统不会通知用户，通常适用于应用内部。
signatureOrSystem：类似于signature，但该授权级别针对Android系统镜像中的所有包授权，用于系统镜像内应用间的通信，用户通常无需关心。

使用ordered broadcast实现双向通信：
理论上一个broadcast intent可被多个receiver同时接收并处理，不能指望它们按照某种顺序依次运行，也无法知道它们什么时候全部结束运行。有序broadcast允许多个broadcast receiver依序处理broadcast intent，此外通过传入一个result receiver，有序broadcast还可以实现让broadcast的发送者接收broadcast接收者的返回值。
修改broadcast receiver的onReceive()方法，将取消通知的信息发送给broadcast的发送者：
```java
public abstract class VisibleFragment extends Fragment{
private BroadcastReceiver mOnShowNotification = new BroadcastReceiver(){
@Override
public void onReceive(Context context,Intent intent){
setResultCode(Activity.RESULT_CANCELED); // 还有其他的可选方法，如setResultData(String)、setResultExtras(Bundle)
}
};
...
```
要使该取消操作有效，broadcast必须有序。在PollService中新增一个可发送有序broadcast的新方法：
```java
public class PollService extends IntentService{
...
void showBackgroundNotification(int requestCode, Notification notification){
Intent i = new Intent(ACTION_SHOW_NOTIFICATION);
i.putExtra(“REQUEST_CODE”,requestCode);
i.putExtra(“NOTIFICATION”，notification);
sendOrderedBroadcast(i,PERM_PRIVATE,null,null,Activity.RESULT_OK,null,null);
}
...
sendOrderedBroadcast方法原型：
public abstract void sendOrderedBroadcast (
Intent intent, 
String receiverPermission, 
BroadcastReceiver resultReceiver,  
Handler scheduler, 
int initialCode, 
String initialData, 
Bundle initialExtras)
```
其中resultReceiver只在所有有序broacast intent的接收者运行结束后才开始运行，scheduler为一个支持resultReceiver运行的Handler。

新建一个处理有序broadcast的receiver：
```java
		public class NotificationReceiver extends BroadcastReceiver{

@Override
public void onReceive(Context c,Intent i){
if(getResultCode()!= Activity.RESULT_OK)
return;
int requestCode = i.getIntExtra(“REQUEST_CODE”,0);
Notification notification = (Notification)i.getParcelableExtra(“NOTIFICATION”);
NotificationManager notificationManager = 
(NotificationManager)c.getSystemService(Context.NOTIFICATION_SERVICE);
notificationManager.notify(requestCode,notification);
}
}
```
登记新建的receiver：
因为NotificationReceiver接收其他receiver返回的结果码，并负责发送消息，它的运行应该总是在最后，这需要将其优先级设置为最低，即设置其优先级为-999（-1000及以下值属于系统保留值）。
<manifest
<application
...
<receiver 
android:name=”.NotificationReceiver”
android:exported=”false”>
<intent-filter android:priority=”-999”>
<action android:name=”com.xxx...SHOW_NOTIFICATION” />
</intent-filter>
</receiver>
</application>
</manifest>

修改PollService中获取到新的图片时发送通知的方式：
```java
	public class PollService extends IntentService{
...
@Override
protected void onHandleIntent(Intent intent){
if(!resultId.equals(lastResultId)){
		// 得到一个新的查询结果，发出通知
...
Notification notification = new NotificationCompat.Builder(this)...build();
showBackgroundNotification(0,notification);
}
```