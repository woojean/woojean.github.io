# android-BroadcastReceiver通过终止广播阻止用户收到短信

1.系统收到短信，发出的广播属于有序广播。
如果想阻止用户收到短信，可自定义Receiver，设置高优先级，率先获得接收短信的广播，并终止广播。
2.接收短信的广播名android.provider.Telephony.SMS_RECEIVED
3.注意：程序一旦在某个模拟器运行，将一直阻止短信，只有注释掉abortBroadcast()，重新运行，方可正常。

```xml
<receiver android:name=".MySmsResevicer">  
			<intent-filter android:priority="1000">  
				<action android:name="android.provider.Telephony.SMS_RECEIVED"/>  
			</intent-filter>  
		</receiver>  
	</application>  
	<uses-permission android:name="android.permission.RECEIVE_SMS"/>  
```
```java
public class MySmsResevicer extends BroadcastReceiver{  
@Override  
public void onReceive(Context context, Intent intent) {  
		// TODO Auto-generated method stub  
			System.out.println("on receive");  
			abortBroadcast();  
		}    
}  
```
（官方文档给出的android:priority的最大值为1000，实际可以设置的最大值为2147483647，即Integer.MAX_VALUE。
此外动态注册要比静态注册的优先级高，因此可以结合开机自启、动态注册等方法来做到高于360、飞信等的短信拦截功能：
Service中动态注册：
```java
IntentFilter filter = new IntentFilter();
filter.addAction("android.provider.Telephony.SMS_RECEIVED");
filter.setPriority(Integer.MAX_VALUE);
registerReceiver(new SmsReceiver(), filter);
）
```