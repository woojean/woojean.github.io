# android-BroadcastReceiver发送有序广播

普通广播（Normal Broadcast）：
1.优缺点：和有序广播的优缺点相反！
2.发送广播的方法：sendBroadcast()

有序广播（Ordered Broadcast）：
1.优缺点
优点：
(1)按优先级的不同，优先Receiver可对数据进行处理，并传给下一个Receiver
(2)通过abortBroadcast可终止广播的传播  
缺点：效率低  
2.发送广播的方法：sendOrderedBroadcast()   
3.优先接收到Broadcast的Receiver可通过setResultExtras(Bundle)方法将处理结果存入Broadcast中，下一个Receiver 通过 Bundle bundle=getResultExtras(true)方法获取上一个 Receiver传来的数据     
程序效果：点击按钮，两个Receiver接收同一条广播，在logcat中打印出数据（按照Receiver的优先顺序，Receiver2先，Receiver1后）  
```xml
<application  
	android:icon="@drawable/ic_launcher"  
	android:label="@string/app_name" >  
		<activity  
			android:label="@string/app_name"  
			android:name=".C48_BroadcastActivity" >  
			<intent-filter >  
				<action android:name="android.intent.action.MAIN" />  
<category android:name="android.intent.category.LAUNCHER" />  
			</intent-filter>  
			<!--优先级的设定 MyReceiver2大于MyReceiver1 -->  
		</activity>  
<receiver android:name=".MyReceiver1">  
		<intent-filter android:priority="200">  
			<action android:name="com.song.123"/>  
		</intent-filter>  
	</receiver>  
	<receiver android:name=".MyReceiver2">  
		<intent-filter android:priority="1000">  
			<action android:name="com.song.123"/>  
		</intent-filter>  
	</receiver>  
</application>  
```
```java
@Override  
	public void onClick(View v) {  
			// TODO Auto-generated method stub  
			Intent intent=new Intent("com.song.123");  
			Bundle bundle=new Bundle();  
			 bundle.putString("a", "aaa");  
			intent.putExtras(bundle);  
			//有序广播  
			sendOrderedBroadcast(intent, null);  
	}  


@Override  
	public void onReceive(Context context, Intent intent) {  
		// TODO Auto-generated method stub  
		System.out.println("receiver2");  
		Bundle bundle=intent.getExtras();  
		bundle.putString("b", "bbb");  
		System.out.println("a="+bundle.get("a"));  
		setResultExtras(bundle);  
		//切断广播  
		//abortBroadcast();  
	}  


@Override  
	public void onReceive(Context context, Intent intent) {  
		// TODO Auto-generated method stub  
		System.out.println("receiver1");  
		//要不要接受上一个广播接收器receiver2传来的的数据  
		Bundle bundle=getResultExtras(true);  
		System.out.println("a="+bundle.getString("a")+",b="+bundle.getString("b"));  
	}  
```