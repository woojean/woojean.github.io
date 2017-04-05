# android-BroadcastReceiver系统开机广播

1.监听系统开机广播android.intent.action.BOOT_COMPLETED
2.访问系统开机事件的权限android.permission.RECEIVE_BOOT_COMPLETED

```xml
<receiver android:name=".MyBroadcast">  
	<intent-filter >  
		<action android:name="android.intent.action.BOOT_COMPLETED"/>  
</intent-filter>  
</receiver>  
</application>  
<uses-permission android:name="android.permission.RECEIVE_BOOT_COMPLETED"/>  
```
```java
public class MyBroadcast extends BroadcastReceiver{  
		Calendar c;  
		@Override  
		public void onReceive(Context context, Intent intent) {  
			// TODO Auto-generated method stub  
			c=Calendar.getInstance();  
			int hour=c.get(Calendar.HOUR_OF_DAY);  
			if(hour>=0&&hour<=10)  
			{  
				Toast.makeText(context, "上午", Toast.LENGTH_LONG).show();  
			}  
```