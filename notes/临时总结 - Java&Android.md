## android-BroadcastReceiver 系统开机广播
1.监听系统开机广播android.intent.action.BOOT_COMPLETED
2.访问系统开机事件的权限android.permission.RECEIVE_BOOT_COMPLETED
```
<receiver android:name=".MyBroadcast">  
	<intent-filter >  
		<action android:name="android.intent.action.BOOT_COMPLETED"/>  
</intent-filter>  
</receiver>  
</application>  
<uses-permission android:name="android.permission.RECEIVE_BOOT_COMPLETED"/>  
```
```
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
## android-BroadcastReceiver 通过终止广播阻止用户收到短信
1.系统收到短信，发出的广播属于有序广播。
如果想阻止用户收到短信，可自定义Receiver，设置高优先级，率先获得接收短信的广播，并终止广播。
2.接收短信的广播名android.provider.Telephony.SMS_RECEIVED
3.注意：程序一旦在某个模拟器运行，将一直阻止短信，只有注释掉abortBroadcast()，重新运行，方可正常。
```
<receiver android:name=".MySmsResevicer">  
			<intent-filter android:priority="1000">  
				<action android:name="android.provider.Telephony.SMS_RECEIVED"/>  
			</intent-filter>  
		</receiver>  
	</application>  
	<uses-permission android:name="android.permission.RECEIVE_SMS"/>  
```
```
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
```
IntentFilter filter = new IntentFilter();
filter.addAction("android.provider.Telephony.SMS_RECEIVED");
filter.setPriority(Integer.MAX_VALUE);
registerReceiver(new SmsReceiver(), filter);
）
```

## android模拟器号码
安卓模拟器号码15555215554，15555215556
如启动多个模拟器，号码后四位每次增2

## android-BroadcastReceiver 发送有序广播
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
```    
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
```
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

      
## Handler和ProgressBar实现进度条的开始，暂停，停止，后退和循环 
一.涉及的handler类方法
1.post(Runnable r)
Causes the Runnable r to be added to the message queue.
将要执行的线程对象加到队列当中 
2.removeCallbacks(Runnable r) 
Remove any pending posts of Runnable r that are in the message queue.
移除队列当中未执行的线程对象
3.postDelayed(Runnable r, long delayMillis) 
Causes the Runnable r to be added to the message queue, to be run after the specified amount of time elapses.
将要执行的线程对象放入到队列当中，待时间结束后，运行制定的线程对象

二.编写程序

程序效果：实现进度条的开始，暂停，停止，后退和循环

主activity
```
package com.song;

import android.app.Activity;
import android.os.Bundle;
import android.os.Handler;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.ProgressBar;

public class C93_Handler3Activity extends Activity {
    /** Called when the activity is first created. */
	ProgressBar bar;
	Button start,pause,back,stop;
	Handler handler;
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.main);
        handler=new Handler();
        bar=(ProgressBar)findViewById(R.id.bar);
        start=(Button)findViewById(R.id.start);
        pause=(Button)findViewById(R.id.pause);
        back=(Button)findViewById(R.id.back);
        stop=(Button)findViewById(R.id.stop);
        start.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				//开始按钮，将要执行的线程对象放到队列当中 
				handler.post(runnable);
			}
		});

        pause.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				//暂停按钮，删除队列当中未执行的线程对象
				handler.removeCallbacks(runnable);
			}
		});

        back.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				if(prolength!=0)
				{
					prolength=bar.getProgress()-1;
					bar.setProgress(prolength);
					setTitle(String.valueOf(prolength));
				}
			}
		});

        stop.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				handler.removeCallbacks(runnable);
				bar.setProgress(0);
				setTitle(String.valueOf(0));
			}
		});
        
}

    int prolength=0;//定义进程度
    //定义线程
    Runnable runnable=new Runnable() {
		@Override
		public void run() {
			// TODO Auto-generated method stub
			prolength=bar.getProgress()+1;
			bar.setProgress(prolength);
			setTitle(String.valueOf(prolength));
			//如果进度小于100,则延迟1000毫秒之后重复执行runnable
			if(prolength<100)
			{
				handler.postDelayed(runnable, 1000);
			}
			//否则，都置零，线程重新执行
			else 
			{
				bar.setProgress(0);
				setTitle(String.valueOf(0));
				handler.post(runnable);
			}
		}
	};
}
```

## handler和message传值
```
package com.song;

import java.text.SimpleDateFormat;
import java.util.Date;

import android.app.Activity;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.TextView;

public class C91_HandlerActivity extends Activity {
    /** Called when the activity is first created. */
	TextView textview;
	Button button;
	MyThread mythread;
	Thread thread;
	MyHandler handler;
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.main);
        textview=(TextView)findViewById(R.id.textview);
        button=(Button)findViewById(R.id.button);
        handler=new MyHandler();
        button.setOnClickListener(new OnClickListener() {
			
			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				mythread=new MyThread();
				thread=new Thread(mythread);
				thread.start();
			}
		});
    }
    class MyHandler extends Handler
    {
    	//接受message的信息
    	@Override
    	public void handleMessage(Message msg) {
    		// TODO Auto-generated method stub
    		super.handleMessage(msg);
    		if(msg.what==1)
    		{
    			textview.setText(msg.getData().getString("time"));
    		}
    		
    	}
    }
    class MyThread implements Runnable
    {

		@Override
		public void run() {
			// TODO Auto-generated method stub
			while(true)
			{
				try {
					Thread.sleep(1000);
					String time=new SimpleDateFormat("yyyy/MM/dd HH:mm:ss").format(new Date());
					System.out.println(time);
					Message message=new Message();
					Bundle bundle=new Bundle();
					bundle.putString("time", time);
					message.setData(bundle);//bundle传值，耗时，效率低
					handler.sendMessage(message);//发送message信息
					message.what=1;//标志是哪个线程传数据
					//message有四个传值方法，
					//两个传int整型数据的方法message.arg1，message.arg2
                 //一个传对象数据的方法message.obj
					//一个bandle传值方法

				} catch (InterruptedException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				}
			}
		}
    	
    }
}
```
Message (extends Object implements Parcelable)
Fields:
public static final Creator<Message>	CREATOR	
public int	arg1	 
arg1 and arg2 are lower-cost alternatives to using setData() if you only need to store a few integer values.
public int	arg2	 
arg1 and arg2 are lower-cost alternatives to using setData() if you only need to store a few integer values.
public Object	obj	 
An arbitrary object to send to the recipient.
public Messenger	replyTo	 
Optional Messenger where replies to this message can be sent.
public int	sendingUid	 
Optional field indicating the uid that sent the message.
public int	what	 
User-defined message code so that the recipient can identify what this message is about.

 


## Android中如何实现循环更新UI 
```
Handler mhandler = new Handler() {
 	@Override
 	public void handleMessage(Message msg) {
  		switch(msg.what){
   			case 0:
				//更新你相应的UI
    			mhandler.sendEmptyMessageDelayed(0, 1000);
    			break;
   			case 1:
    			break;
  		}
 	}
};
```
在你想要启动的位置，调用mhandler.sendEmptyMessageDelayed(0, 1000);
若想要停止，则调用mhandler.removeMessages(0);即可；
注意，mhandler必须是在主线程中创建，也就是常用说的UI线程。
public final boolean sendEmptyMessageDelayed (int what, long delayMillis)
Sends a Message containing only the what value, to be delivered after the specified amount of time elapses.


## Android Handler Message总结
当应用程序启动时，会开启一个主线程（也就是UI线程），由她来管理UI，监听用户点击，来响应用户并分发事件等。所以一般在主线程中不要执行比较耗时的操作，如联网下载数据等，否则出现ANR错误。所以就将这些操作放在子线程中，但是由于Android UI线程是不安全的，所以只能在主线程中更新UI。Handler就是用于子线程和创建Handler的线程进行通信的。（线程安全就是多线程访问时，采用了加锁机制，当一个线程访问该类的某个数据时，进行保护，其他线程不能进行访问直到该线程读取完，其他线程才可使用。不会出现数据不一致或者数据污染。
        线程不安全就是不提供数据访问保护，有可能出现多个线程先后更改数据造成所得到的数据是脏数据）
Handler的使用分为两部分：一部分是创建Handler实例，重载handleMessage方法，来处理消息。
```
mProgressHandler = new Handler()
        {
            public void handleMessage(Message msg)
            {
                super.handleMessage(msg);
            }
        };
```
也可继承自Handler，同样要实现handleMessage(Message msg)方法：
```
class MyHandler extends Handler {
        public MyHandler() {
        }

        // 子类必须重写此方法,接受数据
        @Override
        public void handleMessage(Message msg) {
            // TODO Auto-generated method stub
            Log.d("MyHandler", "handleMessage......");
            super.handleMessage(msg);
        }
    }
```
另一部分是分发Message或者Runable对象到Handler所在的线程中，一般Handler在主线程中。
Handler中分发消息的一些方法
          post(Runnable)
          postAtTime(Runnable,long)
          postDelayed(Runnable long)
          sendEmptyMessage(int what)
          sendMessage(Message)
          sendMessageAtTime(Message,long)
          sendMessageDelayed(Message,long)
handler本身不仅可以发送消息，还可以用post的方式添加一个实现Runnable接口的匿名对象到消息队列中，在目标收到消息后就可以回调的方式在自己的线程中执行run的方法体。
子线程可通过两种方式的Handler与主线程通信：message和Runnable对象

实质都是将在Handler的队列中放入内容，message是放置信息，可以传递一些参数，Handler获取这些信息并将判度如何处理，而Runnable则是直接给出处理的方法。
队列就是依次执行，Handler会处理完一个消息或者执行完某个处理再进行下一步，这样不会出现多个线程同时要求进行UI处理而引发的混乱现象。
这些函数包括有:sendMessage(), sendMessageAtFrontOfQueue(), sendMessageAtTime(), sendMessageDelayed()
以及用于在队列中加入Runnable的post(), postAtFrontOfQueue(), postAtTime(),postDelay()。
一般而言，推荐Messge方式，这样程序设计得可以更为灵活，而Runnable在某些简单明确的方式中使用。


## Android布局之屏幕自适应
Android系统中，默认的单位是像素(px)。也就是说，在没有明确说明的情况下，所有的大小设置都是以像素为单位。
如果以像素设置大小，会导致不同分辨率下出现不同的效果。
Android布局之屏幕自适应
字体的自适应
这里又有关于Android下表示大小的单位的相关知识。
下面列出几种表示单位：
dp（密度无关的尺寸单位）：一种基于屏幕密度的抽象单位，在不同屏幕密度的设备上获得同样大小的尺寸，Android能够自动将这种单位转换成像素单位。在每英寸160点的显示器上，1dp = 1px。
dip: device independent pixels(设备独立像素，即：是和像素密度无关的). 与dp相同。不同设备有不同的显示效果,这个和设备硬件有关，一般我们为了支持WVGA、HVGA和QVGA （WVGA=800x480，HVGA=480x320, QVGA=320x240）推荐使用这个，不依赖像素。密度可以理解为每英寸包含的像素个数（单位是dpi）,1dp实际上相当于密度为160dpi的屏上的一个点(可否理解为物理尺寸？)。也就是说，如果屏幕物理密度是160dpi时，dp和px是等效的。
 举例说明： 
        一块拥有320*480分辨率的手机屏幕，如果宽度是2英寸，高度是3英寸，这块屏幕的密度就是160dpi。 
        一块拥有480*800分辨率的手机屏幕，如果宽度是2英寸，高度是3英寸，这块屏幕的密度就不是160dpi了。这时屏幕的物理密度就变大了（大于160dpi）。这就意味着屏幕每英寸可以显示更多的像素点，屏幕的显示效果就更细腻了。
假设一个按钮的宽度使用dp作为单位，在160dpi时设为160，而在更高的dpi下（如320dpi)，按钮的宽度看上去和160dpi时的屏幕一样。这是由于系统在发现屏幕的密度不是160dpi时，会计算一个转换比例，然后用这个比例与实际尺寸相乘就得出新的尺寸。计算比例的方法是目标屏幕的密度除以160.如果目标屏幕的密度是320dpi,那么这个比例就是2。如果按钮的宽度是160dp，那么在320dpi的屏幕上的宽度就是320个像素点（dp是抽象单位，在实际的屏幕上应转换成像素点）。从这一点可以看出，dp可以自适应屏幕的密度。不管屏幕密度怎样变化，只要屏幕的物理尺寸不变，实际显示的尺寸就不会变化。如果将按钮的宽度设成160px，那么在320dpi的屏幕上仍然会是160个像素点，看上去按钮的宽度只是160dpi屏幕的一半。
公式：px = dip * density / 160，则当屏幕密度为160时，px = dip。
dpi是归一化后的dpi，可能值只有120(low)、160(medium)、240(high)、 320(xhigh)四种。
例：G7的真实dpi是252，根据我以前的理解，310dp换算成px应该是：310 * (252 / 160) = 488像素，而G7水平方向是480px，310dp在这上面绝对满屏都不止了，事实上Android系统并没有拿252作为dpi来计算，而是将G7视 作hdpi设备，然后使用240dpi来计算最终像素，所以在G7上320dp刚好是：320 * (240 / 160) = 480像素，刚好满屏了，310dp确实要差一点点。 
 
px: pixels(像素). 屏幕上的点。
pt: point，是一个标准的长度单位，1pt＝1/72英寸，用于印刷业；
sp: scaled pixels(放大像素). 主要用于字体显示best for textsize。可以根据用户的字体大小首选项进行缩放，即其实际大小取决于当前设置的字体大小。
根据上面对单位的分析，使用sp为单位就可以实现自适应字体大小。
Sp仅用于表示文字大小。
手机上的文字大小，取决于sp的值和用户在系统设置中设置的字体大小。当字体大小设置为普通（默认如此）时，sp=dp。
查看TextView的源码可知Android默认使用sp作为字号单位。将dip作为其他元素的单位。  

在你的res文件夹中创建一个文件夹，叫做values-320x240。其中320x240是你手机屏幕的分辨率，根据你手机屏幕的情况做不同的命名，例如values-800x480。在该文件夹下创建一个dimens.xml文件，定义各种字体的大小。那么系统就会自动根据你手机屏幕的分辨率去调用响应的文件夹。
   另外，值得提醒的是，记得在你默认的values文件下的dimens.xml文件中也要写上相应的字体大小哦，因为当系统无法认识你手机屏幕大小的时候，它会自动去找你默认文件中的东西，没有写的话程序会崩溃。
 
布局自适应
       Android手机屏幕大小不一，有480x320,640x360, 800x480,854x480.怎样才能让App自动适应不同的屏幕呢？ 其实很简单，只需要在res目录下创建不同的layout文件夹，比如layout-640x360,layout-800x480,所有的layout文件在编译之后都会写入R.java里，而系统会根据屏幕的大小自己选择合适的layout进行使用。 但是需要注意的是根据分辨率添加layout文件时，layout这个原来的文件夹及资源一定要存在，否则会出现错误。同时在命名layout文件夹时,必须遵守这样的规则.layout-640x360 大数放在小数的前面,否则会报错。下图为需要定义的文件夹和文件：
drawable-hdpi、drawable-mdpi、drawable-ldpi的区别： 
(1)drawable-hdpi里面存放高分辨率的图片,如WVGA (480x800),FWVGA (480x854) 
(2)drawable-mdpi里面存放中等分辨率的图片,如HVGA (320x480) 
(3)drawable-ldpi里面存放低分辨率的图片,如QVGA (240x320) 
备注：三者的解析度不一样，就像你把电脑的分辨率调低，图片会变大一样，反之分辨率高，图片缩小。
利用weight属性
保证页面的布局随着屏幕的大小变化而变化，最好使用相对布局方式，少使用绝对赋值。
将你控件的layout中的width、height设置为fill-parent，不要使用wrap——content。因为wrap-content的大小是不固定的。而weight（权重）这个属性很好的解决了这个问题。当包裹在控件外面的Layout的width、height属性都设置为fill-parent时，可以利用weight的反比特性。即如果控件A设置weight为9，控件B设置weight为20，那么A所占的空间为20/（9+20），B所占的空间为9/（9+20）。这样的反比属性对任何分辨率下的手机都是合适的。
这种方法虽然解决了自适应问题，但是在我使用的时候发现我有需要使用wrap_content属性的时候，就会出现layout无法填满的现象。（也有可能是我的布局有问题）。
2.3         在程序中制定
获得屏幕的大小，在程序中设置height和width属性。
//得到屏幕的高度
Int heigh = (Activity)m_context).getWindowManager().getDefaultDisplay().getHeight();
textview.setHeight((int)( heigh /14);
这种方法感觉不太适合复杂布局，但是我正在编写的应用时够了~
Android 中的排布也类似于普通的盒式模型，用margin来规定 View 和父 ViewGroup 以及兄弟 View 之间的间距，用padding来表述内容或者子 View 的位置：
 
![image](https://github.com/woojean/woojean.github.io/blob/master/images/java_1.png)

## ListView 美化的一些属性
用心的朋友应该会发现，listview中在设置了背景之后。会有些问题。

1）listview在拖动的时候背景图片消失变成黑色背景。等到拖动完毕我们自己的背景图片才显示出来。

2）listview的上边和下边有黑色的阴影。

3）lsitview的每一项之间需要设置一个图片做为间隔。

针对以上问题 在listview的xml文件中设置以下语句。

问题1 有如下代码结解决 android:scrollingCache="false" 
android:scrollingCache		 When set to true, the list uses a drawing cache during scrolling. 

问题2 用如下代码解决：android:fadingEdge="none"  
问题3  用如下代码解决：  android:divider="@drawable/list_driver"  其中  @drawable/list_driver 是一个图片资源

android:cacheColorHint="#00000000" 隐藏的颜色提示设为透明
android:listSelector="@android:color/transparent" 列表选择器设为颜色透明：当你不使用android:listSelector属性，默认会显示选中的item为橙黄底色，有时候我们需要去掉这种效果
6，设置分割线Divider样式
给ListView设置分割线，只需设置如下两个属性：
android:divider="#000" //设置分割线显示颜色
android:dividerHeight="1px" //此处非0，否则无效
    <ListView android:id="@+id/listView"          android:layout_width="fill_parent"         android:layout_height="fill_parent"          android:divider="#FFF"         android:dividerHeight="1px"         android:layout_margin="10dip"/>



## Shape详解与背景渐变色
res/drawable/background_login.xml
<?xml version="1.0" encoding="utf-8"?>
<shape xmlns:android="http://schemas.android.com/apk/res/android">
 	<gradient 
  		android:startColor="#FFF"
  		android:endColor="#000"
  		android:angle="45"
 	/>
</shape>
shape是用来定义形状的，gradient定义该形状里面为渐变色填充，startColor起始颜色，endColor结束颜色，angle表示方向角度。当angle=0时，渐变色是从左向右。 然后逆时针方向转，当angle=90时为从下往上。
res/layout/login.xml
<?xml version="1.0" encoding="utf-8"?>
< LinearLayout
  	xmlns:android="http://schemas.android.com/apk/res/android"
  	android:orientation="vertical"
  	android:layout_width="fill_parent"
  	android:layout_height="fill_parent"
  	android:background="@drawable/background_login">
< /LinearLayout>
```
import android.app.Activity;
import android.os.Bundle;
public class LoginActivity extends Activity {
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.login);
    }
}
```
效果图：
  
![image](https://github.com/woojean/woojean.github.io/blob/master/images/java_2.png)

在Android开发过程中，经常需要改变控件的默认样式， 那么通常会使用多个图片来解决。不过这种方式可能需要多个图片，比如一个按钮，需要点击时的式样图片，默认的式样图片。 这样就容易使apk变大。
那么除了使用drawable这样的图片外，还有其他方法吗？
本次就谈一下自定义图形shape，Android上支持以下几种属性gradient、stroke、corners、padding、solid等。
简单说明一下：
gradient：对应颜色渐变。 startcolor、endcolor就不多说了。 android:angle 是指从哪个角度开始变。
solid：填充。
stroke：描边。
corners：圆角。
padding：定义内容离边界的距离。与android:padding_left、android:padding_right这些是一个道理。


## Android中的Selector 背景选择器 
drawable/list_item_bg.xml
<?xml version="1.0" encoding="utf-8" ?>   
<selector xmlns:android="http://schemas.android.com/apk/res/android">   
<item 
android:drawable="@drawable/pic1" />
<item 
android:state_window_focused="false"   
android:drawable="@drawable/pic1" />   

<!-- 非触摸模式下获得焦点并单击时的背景图片 -->   
<item 
android:state_focused="true" 
android:state_pressed="true"   
android:drawable= "@drawable/pic2" />   

<!-- 触摸模式下单击时的背景图片 -->   
<item 
android:state_focused="false" 
android:state_pressed="true"   
android:drawable="@drawable/pic3" /> 
  
<item 
android:state_selected="true"   
android:drawable="@drawable/pic4" />  
 
<item 
android:state_focused="true"   
android:drawable="@drawable/pic5" />   
</selector>  
在listview中配置android:listSelector="@drawable/list_item_bg，
或者在listview的item中添加属性android：background="@drawable/list_item_bg"即可实现，
或者在Java代码中使用：Drawable drawable = getResources().getDrawable(R.drawable.list_item_bg);  
ListView.setSelector(drawable);同样的效果。
但是这样会出现列表有时候为黑的情况，需要加上：android:cacheColorHint="@android:color/transparent"
使其透明。

再来看看Button的一些背景效果：
android:state_selected是选中
android:state_focused是获得焦点
android:state_pressed是点击
android:state_enabled是设置是否响应事件,指所有事件
根据这些状态同样可以设置button的selector效果。也可以设置selector改变button中的文字状态。以下就是配置button中的文字效果：drawable/button_font.xml
<?xml version="1.0" encoding="utf-8"?>   
<selector xmlns:android="http://schemas.android.com/apk/res/android">   
<item android:state_selected="true" android:color="#FFF" />   
<item android:state_focused="true" android:color="#FFF" />   
<item android:state_pressed="true" android:color="#FFF" />   
<item android:color="#000" />   
</selector>  
Button还可以实现更复杂的效果，如渐变：
drawable/button_color.xml   
<?xml version="1.0" encoding="utf-8"?>   
<selector xmlns:android="http://schemas.android.com/apk/res/android"> /   
<item android:state_pressed="true">//定义当button 处于pressed 状态时的形态。   
<shape>   
<gradient android:startColor="#8600ff" />   
<stroke android:width="2dp" android:color="#000000" />   
<corners android:radius="5dp" />   
<padding android:left="10dp" android:top="10dp"   
android:bottom="10dp" android:right="10dp"/>   
</shape>   
</item>   
<item android:state_focused="true">//定义当button获得 focus时的形态   
<shape>   
<gradient android:startColor="#eac100"/>   
<stroke android:width="2dp" android:color="#333333" color="#ffffff"/>   
<corners android:radius="8dp" />   
<padding android:left="10dp" android:top="10dp"   
android:bottom="10dp" android:right="10dp"/>   
</shape>   
</item>   
</selector> 
最后，需要在包含button的xml文件里添加两项。假如是main.xml文件，我们需要在<Button/>里加两项。
android：focusable="true" 
android:backgroud="@drawable/button_color" 
这样当你使用Button的时候就可以甩掉系统自带的那黄颜色的背景了，实现个性化的背景。

##用layer-list一次加载多个层次图片
layer.xml
<?xml version="1.0" encoding="UTF-8"?>
<layer-list xmlns:android="http://schemas.android.com/apk/res/android">
	<item android:id="@+id/user_faceback_drawable" android:drawable="@drawable/faceback" />
	<item android:id="@+id/user_face_drawable" android:drawable="@drawable/h001"
		android:left="10.0dip" android:top="18.0dip" android:right="25.0dip"
		android:bottom="35.0dip" />
</layer-list>
就是把drawable文件夹下的image图片faceback和h001多层叠加来组成新图片。
上面的xml可以这样引用：
（1）在其它xml布局里引用：
<ImageView 
android:id="@+id/faceImg" 
android:background="@drawable/layer" (即layer.xml的文件名)
android:layout_width="wrap_content" 
android:layout_height="wrap_content" />
（2）在代码里引用：
((ImageView) findViewById(R.id.imageview)).setImageDrawable(getResources().getDrawable(R.drawable.layer));
或者：
Resources r = getResources(); 
    Drawable[] layers = new Drawable[2]; 
    layers[0] = r.getDrawable(R.drawable.cor_info); 
    layers[1] = r.getDrawable(R.drawable.icon); 
    LayerDrawable layerDrawable = new LayerDrawable(layers); 
    ((ImageView) findViewById(R.id.imageview)).setImageDrawable(layerDrawable);



Vector、ArrayList和List的异同

线性表，链表，哈希表是常用的数据结构，在进行Java开发时，JDK已经为我们提供了一系列相应的类来实现基本的数据结构。这些类均在java.util包中。本文试图通过简单的描述，向读者阐述各个类的作用以及如何正确使用这些类。 

Collection
├List
│├LinkedList
│├ArrayList
│└Vector
│　└Stack
└Set
Map
├Hashtable
├HashMap
└WeakHashMap

Collection接口
　　Collection是最基本的集合接口，一个Collection代表一组Object，即Collection的元素（Elements）。一些Collection允许相同的元素而另一些不行。一些能排序而另一些不行。Java SDK不提供直接继承自Collection的类，Java SDK提供的类都是继承自Collection的“子接口”如List和Set。
　　所有实现Collection接口的类都必须提供两个标准的构造函数：无参数的构造函数用于创建一个空的Collection，有一个Collection参数的构造函数用于创建一个新的Collection，这个新的Collection与传入的Collection有相同的元素。后一个构造函数允许用户复制一个Collection。
　　如何遍历Collection中的每一个元素？不论Collection的实际类型如何，它都支持一个iterator()的方法，该方法返回一个迭代子，使用该迭代子即可逐一访问Collection中每一个元素。典型的用法如下：

Iterator it = collection.iterator(); // 获得一个迭代子
		while(it.hasNext()) {
			Object obj = it.next(); // 得到下一个元素
		}
 
由Collection接口派生的两个接口是List和Set。

List接口
　　List是有序的Collection，使用此接口能够精确的控制每个元素插入的位置。用户能够使用索引（元素在List中的位置，类似于数组下标）来访问List中的元素，这类似于Java的数组。
和下面要提到的Set不同，List允许有相同的元素。
　　除了具有Collection接口必备的iterator()方法外，List还提供一个listIterator()方法，返回一个ListIterator接口，和标准的Iterator接口相比，ListIterator多了一些add()之类的方法，允许添加，删除，设定元素，还能向前或向后遍历。
　　实现List接口的常用类有LinkedList，ArrayList，Vector和Stack。

LinkedList类
　　LinkedList实现了List接口，允许null元素。此外LinkedList提供额外的get，remove，insert方法在LinkedList的首部或尾部。这些操作使LinkedList可被用作堆栈（stack），队列（queue）或双向队列（deque）。（双端队列中的元素可以从两端弹出，其限定插入和删除操作在表的两端进行。）
　　注意LinkedList没有同步方法。如果多个线程同时访问一个List，则必须自己实现访问同步。一种解决方法是在创建List时构造一个同步的List：
　　　　List list = Collections.synchronizedList(new LinkedList(...));

ArrayList类
　　ArrayList实现了可变大小的数组。它允许所有元素，包括null。ArrayList没有同步。
size，isEmpty，get，set方法运行时间为常数。但是add方法开销为分摊的常数，添加n个元素需要O(n)的时间。其他的方法运行时间为线性。
　　每个ArrayList实例都有一个容量（Capacity），即用于存储元素的数组的大小。这个容量可随着不断添加新元素而自动增加，但是增长算法并没有定义。当需要插入大量元素时，在插入前可以调用ensureCapacity方法来增加ArrayList的容量以提高插入效率。

Vector类
　　Vector非常类似ArrayList，但是Vector是同步的。由Vector创建的Iterator，虽然和ArrayList创建的Iterator是同一接口，但是，因为Vector是同步的，当一个Iterator被创建而且正在被使用，另一个线程改变了Vector的状态（例如，添加或删除了一些元素），这时调用Iterator的方法时将抛出ConcurrentModificationException，因此必须捕获该异常。

Stack类
　　Stack继承自Vector，实现一个后进先出的堆栈。Stack提供5个额外的方法使得Vector得以被当作堆栈使用。基本的push和pop方法，还有peek方法得到栈顶的元素，empty方法测试堆栈是否为空，search方法检测一个元素在堆栈中的位置。Stack刚创建后是空栈。

Set接口
　　Set是一种不包含重复的元素的Collection，即任意的两个元素e1和e2都有e1.equals(e2)=false，Set最多有一个null元素。
　　很明显，Set的构造函数有一个约束条件，传入的Collection参数不能包含重复的元素。
　　请注意：必须小心操作可变对象（Mutable Object）。如果一个Set中的可变元素改变了自身状态导致Object.equals(Object)=true将导致一些问题。

Map接口
　　请注意，Map没有继承Collection接口，Map提供key到value的映射。一个Map中不能包含相同的key，每个key只能映射一个value。Map接口提供3种集合的视图，Map的内容可以被当作一组key集合，一组value集合，或者一组key-value映射。

Hashtable类
　　Hashtable继承Map接口，实现一个key-value映射的哈希表。任何非空（non-null）的对象都可作为key或者value。
　　添加数据使用put(key, value)，取出数据使用get(key)，这两个基本操作的时间开销为常数。
Hashtable通过initial capacity和load factor两个参数调整性能。通常缺省的load factor 0.75较好地实现了时间和空间的均衡。增大load factor可以节省空间但相应的查找时间将增大，这会影响像get和put这样的操作。
使用Hashtable的简单示例如下，将1，2，3放到Hashtable中，他们的key分别是”one”，”two”，”three”：
　　　　Hashtable numbers = new Hashtable();
　　　　numbers.put(“one”, new Integer(1));
　　　　numbers.put(“two”, new Integer(2));
　　　　numbers.put(“three”, new Integer(3));
　　要取出一个数，比如2，用相应的key：
　　　　Integer n = (Integer)numbers.get(“two”);
　　　　System.out.println(“two = ” + n);
　　由于作为key的对象将通过计算其散列函数来确定与之对应的value的位置，因此任何作为key的对象都必须实现hashCode和equals方法。hashCode和equals方法继承自根类Object，如果你用自定义的类当作key的话，要相当小心，按照散列函数的定义，如果两个对象相同，即obj1.equals(obj2)=true，则它们的hashCode必须相同，但如果两个对象不同，则它们的hashCode不一定不同，如果两个不同对象的hashCode相同，这种现象称为冲突，冲突会导致操作哈希表的时间开销增大，所以尽量定义好的hashCode()方法，能加快哈希表的操作。
　　如果相同的对象有不同的hashCode，对哈希表的操作会出现意想不到的结果（期待的get方法返回null），要避免这种问题，只需要牢记一条：要同时复写equals方法和hashCode方法，而不要只写其中一个。
（Java语言对equals()的要求如下，这些要求是必须遵循的： 
对称性：如果x.equals(y)返回是“true”，那么y.equals(x)也应该返回是“true”。 
反射性：x.equals(x)必须返回是“true”。 
类推性：如果x.equals(y)返回是“true”，而且y.equals(z)返回是“true”，那么z.equals(x)也应该返回是“true”。 
还有一致性：如果x.equals(y)返回是“true”，只要x和y内容一直不变，不管你重复x.equals(y)多少次，返回都是“true”。 
任何情况下，x.equals(null)，永远返回是“false”；x.equals(和x不同类型的对象)永远返回是“false”。 
以上这五点是重写equals()方法时，必须遵守的准则，如果违反会出现意想不到的结果，请大家一定要遵守。 ）
　　Hashtable是同步的。
（HashMap主要是用数组来存储数据的，它会对key进行哈希运算，哈系运算会有重复的哈希值，对于哈希值的冲突，HashMap采用链表来解决。在HashMap里有这样的一句属性声明：
transient Entry[] table;
Entry就是HashMap存储数据所用的类，它拥有的属性如下
final K key;
V value;
final int hash;
Entry next;
其中next就是为了哈希冲突而存在的。
其他几个关键属性：
存储数据的数组
transient Entry[] table; 这个上面已经讲到了
默认容量
static final int DEFAULT_INITIAL_CAPACITY = 16;
通过阅读源码可以发现，当构造函数中传入的初始容量大小小于16时，实际分配的容量为16，即如果执行new HashMap(9,0.75)；那么HashMap的初始容量是16，而不是9。
最大容量
static final int MAXIMUM_CAPACITY = 1 << 30;
默认加载因子，加载因子是一个比例，当HashMap的数据大小>=容量*加载因子时，HashMap会将容量扩容
static final float DEFAULT_LOAD_FACTOR = 0.75f;
当实际数据大小超过threshold时，HashMap会将容量扩容，threshold＝容量*加载因子
int threshold;
加载因子
final float loadFactor;）

HashMap类
　　HashMap和Hashtable类似，不同之处在于HashMap是非同步的，并且允许null，即null value和null key。但是将HashMap视为Collection时（values()方法可返回Collection），其迭代子操作时间开销和HashMap的容量成比例。因此，如果迭代操作的性能相当重要的话，不要将HashMap的初始化容量设得过高，或者load factor过低。

WeakHashMap类
　　WeakHashMap是一种改进的HashMap，它对key实行“弱引用”，当一个键对象被垃圾回收器回收时，那么相应的值对象的引用会从Map中删除。WeakHashMap能够节省存储空间，可用来缓存那些非必须存在的数据。 

总结
　　如果涉及到堆栈，队列等操作，应该考虑用List，对于需要快速插入，删除元素，应该使用LinkedList，如果需要快速随机访问元素，应该使用ArrayList。
　　如果程序在单线程环境中，或者访问仅仅在一个线程中进行，考虑非同步的类，其效率较高，如果多个线程可能同时操作一个类，应该使用同步的类。
　　要特别注意对哈希表的操作，作为key的对象要正确复写equals和hashCode方法。
　　尽量返回接口而非实际的类型，如返回List而非ArrayList，这样如果以后需要将ArrayList换成LinkedList时，客户端代码不用改变。这就是针对抽象编程。 


## Math.round(11.5)等于多少？ Math.round(-11.5)等于多少？ 
12 ,-11
四舍五入 四和五是指正的4，5
-11.5 这么看 -11.5 = -12 +0.5   ，0.5按四舍五入为1 ，-12+1 = -11，所以Math.round(-11.5)==-11
           -0.5 = -1 + 0.5   0.5按四舍五入为1 ，-1+1 = 0，所以Math.round(-0.5)==0

            11.5 四舍五入 显然 Math.round(11.5)==12
round方法返回与参数最接近的长整数，参数加0.5后求其floor（小于等于该数的最大整数）

SurfaceView和View最本质的区别
(SurfaceView是View的子类)
SurfaceView和View最本质的区别在于，surfaceView是在一个新起的单独线程中可以重新绘制画面而View必须在UI的主线程中更新画面。
那么在UI的主线程中更新画面可能会引发问题，比如你更新画面的时间过长，那么你的主UI线程会被你正在画的函数阻塞。那么将无法响应按键，触屏等消息。
当使用surfaceView 由于是在新的线程中更新画面所以不会阻塞你的UI主线程。但这也带来了另外一个问题，就是事件同步。比如你触屏了一下，你需要surfaceView中thread处理，一般就需要有一个event queue的设计来保存touch event，这会稍稍复杂一点，因为涉及到线程同步。
所以基于以上，根据游戏特点，一般分成两类。
1 被动更新画面的。比如棋类，这种用view就好了。因为画面的更新是依赖于 onTouch 来更新，可以直接使用 invalidate。 因为这种情况下，这一次Touch和下一次的Touch需要的时间比较长些，不会产生影响。
2 主动更新。比如一个人在一直跑动。这就需要一个单独的thread不停的重绘人的状态，避免阻塞main UI thread。所以显然view不合适，需要surfaceView来控制。

##横竖屏切换时候activity的生命周期
（1）不设置Activity的android:configChanges时,切屏会重新调用各个生命周期,切横屏时会执行一次,切竖屏时会执行两次. 
（2）设置Activity的android:configChanges="orientation"时,切屏还是会重新调用各个生命周期,切横、竖屏时只会执行一次. 
（3）设置Activity的android:configChanges="orientation|keyboardHidden"时,切屏不会重新调用各个生命周期,只会执行onConfigurationChanged方法.
但是，自从Android 3.2（API 13），在设置Activity的android:configChanges="orientation|keyboardHidden"后，还是一样会重新调用各个生命周期的。因为screen size也开始跟着设备的横竖切换而改变。所以，在AndroidManifest.xml里设置的MiniSdkVersion和 TargetSdkVersion属性大于等于13的情况下，如果你想阻止程序在运行时重新加载Activity，除了设置"orientation"，你还必须设置"ScreenSize"。
（Lists configuration changes that the activity will handle itself. When a configuration change occurs at runtime, the activity is shut down and restarted by default, but declaring a configuration with this attribute will prevent the activity from being restarted. Instead, the activity remains running and its onConfigurationChanged() method is called.
Note: Using this attribute should be avoided and used only as a last resort. Please read Handling Runtime Changes for more information about how to properly handle a restart due to a configuration change.）

## android中的动画有哪几类，它们的特点和区别是什么?
Android提供两种创建简单动画的机制：tweened animation（补间动画） 和 frame-by-frame animation（帧动画）.
•	tweened animation：通过对场景里的对象不断做图像变换(平移、缩放、旋转)产生动画效果
•	frame-by-frame animation：顺序播放事先做好的图像，跟电影类似
这两种动画类型都能在任何View对象中使用，用来提供简单的旋转计时器，activity图标及其他有用的UI元素。Tweened animation被andorid.view.animation包所操作；frame-by-frame animation被android.graphics.drawable.AnimationDrawable类所操作。
想了解更多关于创建tweened和frame-by-frame动画的信息，读一下Dev Guide-Graphics-2D Graphics里面相关部分的讨论。Animation 是以 XML格式定义的，定义好的XML文件存放在res/anim中。

##一条最长的短信息约占多少byte?
 　　中文70(包括标点)，英文160个字节。

## Handler机制的原理
　　andriod提供了Handler 和Looper 来满足线程间的通信。Handler 先进先出原则。Looper类用来管理特定线程内对象之间的消息交换(Message Exchange)。
　	1)Looper: 一个线程可以产生一个Looper对象，由它来管理此线程里的Message Queue(消息队列)。
 	2)Handler: 你可以构造Handler对象来与Looper沟通，以便push新消息到Message Queue里;或者接收Looper从Message Queue取出)所送来的消息。
　　3)Message Queue(消息队列):用来存放线程放入的消息。
4)线程：UI thread 通常就是main thread，而Android启动程序时会替它建立一个Message Queue。
Android系统的消息队列和消息循环都是针对具体线程的，一个线程可以存在（当然也可以不存在）一个消息队列和一个消息循环（Looper），特定线程的消息只能分发给本线程，不能进行跨线程，跨进程通讯。但是创建的工作线程默认是没有消息循环和消息队列的，如果想让该 线程具有消息队列和消息循环，需要在线程中首先调用Looper.prepare()来创建消息队列，然后调用Looper.loop()进入消息循环。
```
class LooperThread extends Thread {
      public Handler mHandler;
      public void run() {
          Looper.prepare();
          mHandler = new Handler() {
              public void handleMessage(Message msg) {
                  // process incoming messages here
              }
          };
          Looper.loop();
      }
  }
```
这样你的线程就具有了消息处理机制了，在Handler中进行消息处理。
Android系统在启动的时候会为Activity创建一个消息队列和消息循环（Looper）。详细实现请参考ActivityThread.java文件
Android应用程序进程在启动的时候，会在进程中加载ActivityThread类，并且执行这个类的main函数，应用程序的消息循环过程就是在这个main函数里面实现的
```
public final class ActivityThread {
	......
其实android上一个应用的入口应该是ActivityThread。和普通的java类一样，入口是一个main方法。
	public static final void main(String[] args) {
		......
		Looper.prepareMainLooper();
		......
		ActivityThread thread = new ActivityThread();
		thread.attach(false);
		......
		Looper.loop();
		......
		thread.detach();
		......
	}
}
```
这个函数做了两件事情，一是在主线程中创建了一个ActivityThread实例，二是通过Looper类使主线程进入消息循环中。

## android中线程与线程，进程与进程之间如何通信
1.一个Android 程序开始运行时，会单独启动一个Process。
   默认情况下，所有这个程序中的Activity或者Service都会跑在这个Process。
   默认情况下，一个Android程序也只有一个Process，但一个Process下却可以有许多个Thread。
2.一个Android 程序开始运行时，就有一个主线程Main Thread被创建。该线程主要负责UI界面的显示、更新和控件交互，所以又叫UI Thread。
   一个Android程序创建之初，一个Process呈现的是单线程模型--即Main Thread，所有的任务都在一个线程中运行。所以，Main Thread所调用的每一个函数，其耗时应该越短越好。而对于比较费时的工作，应该设法交给子线程去做，以避免阻塞主线程（主线程被阻塞，会导致程序假死现象）。 
3.Android单线程模型：Android UI操作并不是线程安全的并且这些操作必须在UI线程中执行。如果在子线程中直接修改UI，会导致异常。

## Android dvm的进程和Linux的进程, 应用程序的进程是否为同一个概念 
　　DVM指dalivk的虚拟机。每一个Android应用程序都在它自己的进程中运行，都拥有一个独立的Dalvik虚拟机实例。而每一个DVM都是在Linux 中的一个进程，所以说可以认为是同一个概念。 

##让Activity变成一个窗口：Activity属性设定 
　　android :theme="@android:style/Theme.Dialog" 
这就使你的应用程序变成对话框的形式弹出来了
（4.0上如果还用Theme.Dialog，只能说很土，跟整体UI风格差别很大
请使用android:theme="@android:style/Theme.Holo.DialogWhenLarge"
上效果对比图:
@android:style/Theme.Holo.DialogWhenLarge效果
 
![image](https://github.com/woojean/woojean.github.io/blob/master/images/java_3.png)
@android:style/Theme.Dialog效果
 
![image](https://github.com/woojean/woojean.github.io/blob/master/images/java_4.png)
　　android:theme="@android:style/Theme.Translucent" 
就变成半透明的。
类似的这种activity的属性可以在android.R.styleable 类的AndroidManifestActivity 方法中看到，AndroidManifest.xml中所有元素的属性的介绍都可以参考这个类android.R.styleable 

##如何将SQLite数据库(dictionary.db文件)与apk文件一起发布、使用？ 
　　可以将dictionary.db文件复制到Eclipse Android工程中的res raw目录中。所有在res raw目录中的文件不会被压缩，这样可以直接提取该目录中的文件。可以将dictionary.db文件复制到res raw目录中 
在Android中不能直接打开res raw目录中的数据库文件，而需要在程序第一次启动时将该文件复制到手机内存或SD卡的某个目录中，然后再打开该数据库文件。复制的基本方法是使用getResources().openRawResource方法获得res raw目录中资源的 InputStream对象，然后将该InputStream对象中的数据写入其他的目录中相应文件中。在Android SDK中可以使用SQLiteDatabase.openOrCreateDatabase方法来打开任意目录中的SQLite数据库文件。 

## Android系统的架构
android的系统架构和其操作系统一样，采用了分层的架构。从架构图看，android分为四个层，从高层到低层分别是应用程序层、应用程序框架层、系统运行库层和linux核心层。
 
![image](https://github.com/woojean/woojean.github.io/blob/master/images/java_5.png)
android的系统架构和其操作系统一样，采用了分层的架构。从架构图看，android分为四个层，从高层到低层分别是应用程序层、应用程序框架层、系统运行库层和linux核心层。蓝色的代表java程序，黄色的代码为运行JAVA程序而实现的虚拟机，绿色部分为C/C++语言编写的程序库，红色的代码内核(linux内核+drvier)
　　1.应用程序
　　Android会同一系列核心应用程序包一起发布，该应用程序包包括email客户端，SMS短消息程序，日历，地图，浏览器，联系人管理程序等。所有的应用程序都是使用JAVA语言编写的。
　　2.应用程序框架
　　开发人员也可以完全访问核心应用程序所使用的API框架。该应用程序的架构设计简化了组件的重用;任何一个应用程序都可以发布它的功能块并且任何其它的应用程序都可以使用其所发布的功能块(不过得遵循框架的安全性限制)。同样，该应用程序重用机制也使用户可以方便的替换程序组件。
　　隐藏在每个应用后面的是一系列的服务和系统, 其中包括：
　　* 丰富而又可扩展的视图(Views)，可以用来构建应用程序，它包括列表(lists)，网格(grids)，文本框(text boxes)，按钮(buttons)，甚至可嵌入的web浏览器。
　　* 内容提供器(Content Providers)使得应用程序可以访问另一个应用程序的数据(如联系人数据库)， 或者共享它们自己的数据
　　* 资源管理器(Resource Manager)提供非代码资源的访问，如本地字符串，图形，和布局文件( layout files )。
　　* 通知管理器 (Notification Manager) 使得应用程序可以在状态栏中显示自定义的提示信息。
　　* 活动管理器( Activity Manager) 用来管理应用程序生命周期并提供常用的导航回退功能。
　　有关更多的细节和怎样从头写一个应用程序，请参考 如何编写一个 Android 应用程序.
　　3.系统运行库
　　1)程序库
　　Android 包含一些C/C++库，这些库能被Android系统中不同的组件使用。它们通过 Android 应用程序框架为开发者提供服务。以下是一些核心库：
　　* 系统C 库- 一个从BSD 继承来的标准C 系统函数库( libc )，它是专门为基于embedded linux 的设备定制的。
　　* 媒体库- 基于PacketVideo OpenCORE;该库支持多种常用的音频、视频格式回放和录制，同时支持静态图像文件。编码格式包括MPEG4, H.264, MP3, AAC, AMR, JPG, PNG 。
　　* Surface Manager - 对显示子系统的管理，并且为多个应用程序提供了2D和3D图层的无缝融合。
　　* LibWebCore - 一个最新的web浏览器引擎，支持Android浏览器和一个可嵌入的web视图。
　　* SGL - 底层的2D图形引擎
　　* 3D libraries - 基于OpenGL ES 1.0 APIs实现;该库可以使用硬件 3D加速(如果可用)或者使用高度优化的3D软加速。
　　* FreeType -位图(bitmap)和矢量(vector)字体显示。
　　* SQLite - 一个对于所有应用程序可用，功能强劲的轻型关系型数据库引擎。
　　2)Android 运行库
　　Android 包括了一个核心库，该核心库提供了JAVA编程语言核心库的大多数功能。
　　每一个Android应用程序都在它自己的进程中运行，都拥有一个独立的Dalvik虚拟机实例。Dalvik被设计成一个设备可以同时高效地运行多个虚拟系统。Dalvik虚拟机执行(.dex)的Dalvik可执行文件，该格式文件针对小内存使用做了优化。同时虚拟机是基于寄存器的，所有的类都经由JAVA编译器编译，然后通过SDK中的“dx”工具转化成.dex格式由虚拟机执行。
　　Dalvik虚拟机依赖于linux内核的一些功能，比如线程机制和底层内存管理机制。
　　4.Linux 内核
Android 的核心系统服务依赖于Linux 2.6 内核，如安全性，内存管理，进程管理，网络协议栈和驱动模型。Linux 内核也同时作为硬件和软件栈之间的抽象层。
（最新的安卓4.4系统中引入了全新的ART模式吗，相比之前流行已久的Dalvik模式有了很大的改变。dalvik是执行的时候编译+运行，安装比较快，开启应用比较慢，应用占用空间小ART是安装的时候就编译好了，执行的时候直接就可以运行的，安装慢，开启应用快，占用空间大）


## Android常用控件的信息
单选框(RadioButton与RadioGroup)：
RadioGroup用于对单选框进行分组，相同组内的单选框只有一个单选框被选中。
事件：setOnCheckedChangeListener()，处理单选框被选择事件。把RadioGroup.OnCheckedChangeListener实例作为参数传入。
多选框(CheckBox):
每个多选框都是独立的，可以通过迭代所有的多选框，然后根据其状态是否被选中在获取其值。
事件：setOnCheckedChangeListener()，处理多选框被选择事件。把CheckBox.OnCheckedChangeListener()实例作为参数传入。
下拉列表框(Spinner)：
Spinner.getItemAtPosition(Spinner.getSelectedItemPosition());获取下拉列表框的值。
事件：setOnItemSelectedListener(),处理下拉列表框被选择事件把Spinner.OnItemSelectedListener()实例作为参数传入。
拖动条(SeekBar)：
SeekBar.getProgress()获取拖动条当前值
事件:setOnSeekBarChangeListener()，处理拖动条值变化事件，把SeekBar.OnSeekBarChangeListener实例作为参数传入。
菜单(Menu):
重写Activity的onCreatOptionMenu(Menu menu)方法，该方法用于创建选项菜单，当用户按下手机的"Menu"按钮时就会显示创建好的菜单，在onCreatOptionMenu(Menu Menu)方法内部可以调用Menu.add()方法实现菜单的添加。
重写Activity的onMenuItemSelected()方法，该方法用于处理菜单被选择事件。
进度对话框(ProgressDialog)：
创建并显示一个进度对话框：ProgressDialog.show(ProgressDialogActivity.this,"请稍等"，"数据正在加载中...."，true)；
设置对话框的风格：setProgressStyle()
ProgressDialog.STYLE_SPINNER  旋转进度条风格(为默认风格)
ProgressDialog.STYLE_HORIZONTAL 横向进度条风格
下面是各种常用控件的事件监听的使用
①EditText（编辑框）的事件监听---OnKeyListener
②RadioGroup、RadioButton（单选按钮）的事件监听---OnCheckedChangeListener
③CheckBox（多选按钮）的事件监听---OnCheckedChangeListener
④Spinner（下拉列表）的事件监听---OnItemSelectedListener
⑤Menu（菜单）的事件处理---onMenuItemSelected
⑥Dialog（对话框）的事件监听---DialogInterface.OnClickListener()


##请介绍下Android中常用的五种布局
Android布局是应用界面开发的重要一环，在Android中，共有五种布局方式，分别是：FrameLayout，LinearLayout，AbsoluteLayout，RelativeLayout，TableLayout。 
  1.FrameLayout    
这个布局可以看成是墙脚堆东西，有一个四方的矩形的左上角墙脚，我们放了第一个东西，要再放一个，那就在放在原来放的位置的上面，这样依次的放，会盖住原来的东西。这个布局比较简单，也只能放一点比较简单的东西。    
  2.LinearLayout    
 	线性布局，这个东西，从外框上可以理解为一个div，他首先是一个一个从上往下罗列在屏幕上。每一个LinearLayout里面又可分为垂直布局 （android:orientation="vertical"）和水平布局（android:orientation="horizontal" ）。当垂直布局时，每一行就只有一个元素，多个元素依次垂直往下；水平布局时，只有一行，每一个元素依次向右排列。    
LinearLayout中有一个重要的属性 android:layout_weight="1"，这个weight在垂直布局时，代表行距；水平的时候代表列宽；weight值越大就越大。    
3.AbsoluteLayout
绝对布局犹如div指定了absolute属性，用X,Y坐标来指定元素的位置android:layout_x="20px" android:layout_y="12px" 这种布局方式也比较简单，但是在垂直随便切换时，往往会出问题，而且多个元素的时候，计算比较麻烦。    
4.RelativeLayout
    相对布局可以理解为某一个元素为参照物，来定位的布局方式。主要属性有：    
    相对于某一个元素    
    android:layout_below="@id/aaa" 该元素在 id为aaa的下面    
    android:layout_toLeftOf="@id/bbb" 该元素在 id为bbb的左边     
    相对于父元素的地方    
    android:layout_alignParentLeft="true"  与父元素左对齐    
    android:layout_alignParentRight="true" 与父元素右对齐    
    还可以指定边距等，具体详见API        
5.TableLayout 
 	表格布局类似Html里面的Table。每一个TableLayout里面有表格行TableRow，TableRow里面可以具体定义每一个元素，设定他的对齐方式 android:gravity="" 。    
 	每一个布局都有自己适合的方式，另外，这五个布局元素可以相互嵌套应用，做出美观的界面。

##如何启用Service，如何停用Service 
Android中的服务和windows中的服务是类似的东西，服务一般没有用户操作界面，它运行于系统中不容易被用户发觉，可以使用它开发如监控之类的程序。服务的开发比较简单，如下：
第一步：继承Service类
public class SMSService extends Service {
}
第二步：在AndroidManifest.xml文件中的<application>节点里对服务进行配置:
<service android:name=".SMSService" />
服务不能自己运行，需要通过调用Context.startService()或Context.bindService()方法启动服务。这两个方法都可以启动Service，但是它们的使用场合有所不同。使用startService()方法启用服务，调用者与服务之间没有关连，即使调用者退出了，服务仍然运行。使用bindService()方法启用服务，调用者与服务绑定在了一起，调用者一旦退出，服务也就终止，大有“不求同时生，必须同时死”的特点。
如果打算采用Context.startService()方法启动服务，在服务未被创建时，系统会先调用服务的onCreate()方法，接着调用onStart()方法。如果调用startService()方法前服务已经被创建，多次调用startService()方法并不会导致多次创建服务，但会导致多次调用onStart()方法。采用startService()方法启动的服务，只能调用Context.stopService()方法结束服务，服务结束时会调用onDestroy()方法。
如果打算采用Context.bindService()方法启动服务，在服务未被创建时，系统会先调用服务的onCreate()方法，接着调用onBind()方法。这个时候调用者和服务绑定在一起，调用者退出了，系统就会先调用服务的onUnbind()方法，接着调用onDestroy()方法。如果调用bindService()方法前服务已经被绑定，多次调用bindService()方法并不会导致多次创建服务及绑定(也就是说onCreate()和onBind()方法并不会被多次调用)。如果调用者希望与正在绑定的服务解除绑定，可以调用unbindService()方法，调用该方法也会导致系统调用服务的onUnbind()-->onDestroy()方法。
服务常用生命周期回调方法如下：
onCreate() 该方法在服务被创建时调用，该方法只会被调用一次，无论调用多少次startService()或bindService()方法，服务也只被创建一次。
onDestroy()该方法在服务被终止时调用。
 
与采用Context.startService()方法启动服务有关的生命周期方法
onStart()只有采用Context.startService()方法启动服务时才会回调该方法。该方法在服务开始运行时被调用。多次调用startService()方法尽管不会多次创建服务，但onStart()方法会被多次调用。
 
与采用Context.bindService()方法启动服务有关的生命周期方法
onBind()只有采用Context.bindService()方法启动服务时才会回调该方法。该方法在调用者与服务绑定时被调用，当调用者与服务已经绑定，多次调用Context.bindService()方法并不会导致该方法onBind()被多次调用。
onUnbind()只有采用Context.bindService()方法启动服务时才会回调该方法。该方法在调用者与服务解除绑定时被调用

## ListView的优化方案
1.如果自定义适配器，那么在getView方法中要考虑方法传进来的参数contentView是否为null，如果为null就创建contentView并返回，如果不为null则直接使用(即重新赋值后返回)。在这个方法中，尽可能少创建view。
2.给contentView设置tag(setTag()),传入一个viewHolder对象，用于缓存要显示的数据，可以达到图像数据异步加载的效果
3.如果listview需要显示的item很多，就要考虑分页加载。比如一共要显示100条或者更多的时候，我们可以考虑先加载20条，等用户拉到列表底部的时候，再去加载接下来的20条。


##广播接收者生命周期
广播接收器只有一个回调方法：
void onReceive(Context curContext, Intent broadcastMsg)
当广播消息抵达接收器时，Android调用它的onReceive()方法并将包含消息的Intent对象传递给它。广播接收器仅在它执行这个方法时处于活跃状态。当onReceive()返回后，它即为失活状态。
拥有一个活跃状态的广播接收器的进程被保护起来而不会被杀死。但仅拥有失活状态组件的进程则会在其它进程需要它所占有的内存的时候随时被杀掉。
这种方式引出了一个问题：如果响应一个广播信息需要很长的一段时间，我们一般会将其纳入一个衍生的线程中去完成，而不是在主线程内完成它，从而保证用户交互过程的流畅。如果onReceive()衍生了一个线程并且返回，则包涵新线程在内的整个进程都被会判为失活状态（除非进程内的其它应用程序组件仍处于活跃状态），于是它就有可能被杀掉。这个问题的解决方法是令onReceive()启动一个新服务，并用其完成任务，于是系统就会知道进程中仍然在处理着工作。


##设计模式和IoC(Inversion of Control 控制反转)
Android框架魅力的源泉在于IoC，在开发Android的过程中你会时刻感受到IoC带来的巨大方便，就拿Activity 来说，下面的函数是框架调用自动调用的：protected void onCreate(Bundle savedInstanceState);
不是程序编写者主动去调用，反而是用户写的代码被框架调用，这也就反转
了！当然IoC 本身的内涵远远不止这些，但是从这个例子中也可以窥视出IoC带来的巨大好处。此类的例子在Android 随处可见，例如说数据库的管理类，例如说Android 中SAX 的Handler 的调用等。有时候，您甚至需要自己编写简单的IoC实现。
（应用控制反转，对象在被创建的时候，由一个调控系统内所有对象的外界实体将其所依赖的对象的引用传递给它。也可以说，依赖被注入到对象中。所以，控制反转是，关于一个对象如何获取他所依赖的对象的引用，这个责任的反转。
IoC是一个很大的概念,可以用不同的方式实现。其主要形式有两种：
◇依赖查找：容器提供回调接口和上下文条件给组件。EJB和Apache Avalon 都使用这种方式。这样一来，组件就必须使用容器提供的API来查找资源和协作对象，仅有的控制反转只体现在那些回调方法上（也就是上面所说的 类型1）：容器将调用这些回调方法，从而让应用代码获得相关资源。
◇依赖注入：组件不做定位查询，只提供普通的Java方法让容器去决定依赖关系。容器全权负责的组件的装配，它会把符合依赖关系的对象通过JavaBean属性或者构造函数传递给需要的对象。通过JavaBean属性注射依赖关系的做法称为设值方法注入(Setter Injection)；将依赖关系作为构造函数参数传入的做法称为构造器注入（Constructor Injection））

## 4种Activity的启动模式
Activity的启动模式有4种，分别为standard、singleTop、singleTask、singleInstance，在Manifest中定义：
        <activity
            android:name=".A1"
            android:launchMode="standard" />
(1)standard：每次激活Activity时(startActivity)，都创建Activity实例，并放入任务栈；
 
![image](https://github.com/woojean/woojean.github.io/blob/master/images/java_6.png)
(2)singleTop：如果某个Activity自己激活自己，即任务栈栈顶就是该Activity，则不需要创建，其余情况都要创建Activity实例；
 
![image](https://github.com/woojean/woojean.github.io/blob/master/images/java_7.png)
(3)singleTask：如果要激活的那个Activity在任务栈中存在该实例，则不需要创建，只需要把此Activity放入栈顶，并把该Activity以上的Activity实例都pop；（可以用来退出整个应用：将主Activity设为SingTask模式，然后在要退出的Activity中转到主Activity，然后重写主Activity的onNewIntent函数，并在函数中加上一句finish）
（launchMode为singleTask的时候，通过Intent启到一个Activity,如果系统已经存在一个实例，系统就会将请求发送到这个实例上，但这个时候，系统就不会再调用通常情况下我们处理请求数据的onCreate方法，而是调用onNewIntent方法。
但是系统可能会随时杀掉后台运行的Activity，如果这一切发生，那么系统就会调用onCreate方法，而不调用onNewIntent方法，一个好的解决方法就是在onCreate和onNewIntent方法中调用同一个处理数据的方法）
 
![image](https://github.com/woojean/woojean.github.io/blob/master/images/java_8.png)
(4)singleInstance：如果应用1的任务栈中创建了MainActivity实例，如果应用2也要激活MainActivity，则不需要创建，两应用共享该Activity实例；（跨应用的场景）
 
![image](https://github.com/woojean/woojean.github.io/blob/master/images/java_9.png)

## Activity.finish()、onDestroy() 、System.exit()、Process.killProcess
Activity.finish()：在你的activity动作完成的时候，或者Activity需要关闭的时候，调用此方法。当你调用此方法的时候，系统只是将最上面的Activity移出了栈，并没有及时的调用onDestory（）方法，其占用的资源也没有被及时释放。因为移出了栈，所以当你点击手机上面的“back”按键的时候，也不会再找到这个。调用finish()会执行onDestory()；（如果想要主动销毁当前Activity，可以再onPause中调用this.finish()，而不是onDestory()，不能直接调用onDestroy()来结束你的Activity,一般做法是finish()， 在onDestroy()中可以做一些清理操作。）
（A->B(FINISH)->C(BACK)?）S
Activity.onDestory():系统销毁了这个Activity的实例在内存中占据的空间。
在Activity的生命周期中，onDestory()方法是他生命的最后一步，资源空间等就被回收了。当重新进入此Activity的时候，必须重新创建，执行onCreate()方法。
System.exit(0):退出整个应用程序，是针对整个Application的。将整个进程直接KO掉（释放掉所有资源，当然包括Activity占用的资源）0表示正常退出，1表示异常退出。
（其实Process.killProcess（杀死进程）或System.exit(0)（终止当前虚拟机）都不应该直接调用，进程是由os底层进行管理的，android系统会自己进行处理回收进程。退出应用你就直接finish掉activity就行了。正常情况下back键退出应用以后os就会回收app进程，但当app中有推送服务等需要长时间运行的服务时os就不会kill掉进程，也就是说应用将一直在线。即使你手动kill掉进程，进程也会自动重启（估计android os认为app是被意外终止的（如内存不足），os底层有监听服务，app被意外终止会自动重启。））

##设置Activity永不过期，即不执行onDestroy()
/** 重写finish()方法 */
@Override
public void finish() {
    //super.finish(); //记住不要执行此句
    moveTaskToBack(true); //设置该activity永不过期，即不执行onDestroy()
}    
注意：不要调用super.finish()，只需调用moveTaskToBack(true)就可以，这样只有在第一次启动的时候会执行onCreate()。以后(只要进程不死掉)都不会执行onCreate()
方法：public boolean moveTaskToBack(boolean nonRoot)
activity里有这个方法，参数说明如下：
nonRoot=false→ 仅当activity为task根（即首个activity例如启动activity之类的）时才生效
nonRoot=true→ 忽略上面的限制
这个方法不会改变task中的activity中的顺序，效果基本等同于home键
应用场景：
比如有些activity诸如引导图之类的，用户在按返回键的时候你并不希望退出（默认就finish了），而是只希望置后台，就可以调这个方法。

##什么是ANR，如何避免它?
ANR：Application Not Responding。
在Android中，活动管理器和窗口管理器这两个系统服务负责监视应用程序的响应。当出现下列情况时，Android就会显示ANR对话框了： 
　　（1）用户对应用程序的操作(如输入事件，按键、触摸屏事件)在5秒内无响应
　　（2）广播接受器(BroadcastReceiver)在10秒内仍未执行完毕 
　　Android应用程序完全运行在一个独立的线程中(例如main)。这就意味着，任何在主线程中运行的，需要消耗大量时间的操作都会引发ANR。因为此时，你的应用程序已经没有机会去响应输入事件和意向广播(Intent broadcast)。 
避免方法：Activity应该在它的关键生命周期方法（如 onCreate()和onResume()）里尽可能少的去做创建操作，
潜在的耗时操作。例如网络或数据库操作，或者高耗时的计算如改变位图尺寸，应该在子线程里（或者异步方式）来完成。
主线程应该为子线程提供一个Handler，以便完成时能够提交给主线程。

## Android Intent的使用
在一个Android应用中，主要是由一些组件组成，（Activity,Service,ContentProvider,etc.)在这些组件之间的通讯中，由Intent协助完成。
正如网上一些人解析所说，Intent负责对应用中一次操作的动作、动作涉及数据、附加数据进行描述，Android则根据此Intent的描述，负责找到对应的组件，将Intent传递给调用的组件，并完成组件的调用。Intent在这里起着实现调用者与被调用者之间的解耦作用。
Intent传递过程中，要找到目标消费者（另一个Activity,IntentReceiver或Service），也就是Intent的响应者，有两种方法来匹配：
1.显式匹配（Explicit)： 
```
	public TestB extents Activity  
	{  
		.........  
	};  
	public class Test extends Activity  
	{  
			......  
			public void switchActivity()  
			{  
				Intent i = new Intent(Test.this, TestB.class);  
				this.startActivity(i);  
			}  
}  
```
代码简洁明了，执行了switchActivity()函数，就会马上跳转到名为TestB的Activity中。 

2.隐式匹配(Implicit):
  隐式匹配，首先要匹配Intent的几项值：Action, Category, Data/Type,Component
如果填写了Componet就是上例中的Test.class)这就形成了显示匹配。所以此部分只讲前几种匹配。匹配规则为最大匹配规则，
	（1）如果你填写了Action，如果有一个程序的Manifest.xml中的某一个Activity的IntentFilter段中定义了包含了相同的Action那么这个Intent就与这个目标Action匹配，如果这个Filter段中没有定义Type,Category，那么这个Activity就匹配了。但是如果手机中有两个以上的程序匹配，那么就会弹出一个对话可框来提示说明。
Action的值在Android中有很多预定义，如果你想直接转到你自己定义的Intent接收者，你可以在接收者的IntentFilter中加入一个自定义的Action值（同时要设定Category值为"android.intent.category.DEFAULT"），在你的Intent中设定该值为Intent的Action,就直接能跳转到你自己的Intent接收者中。因为这个Action在系统中是唯一的。
	（2）data/type，你可以用Uri来做为data,比如Uri uri = Uri.parse(http://www.google.com );
Intent i = new Intent(Intent.ACTION_VIEW,uri);手机的Intent分发过程中，会根据http://www.google.com 的scheme判断出数据类型type
手机的Brower则能匹配它，在Brower的Manifest.xml中的IntenFilter中首先有ACTION_VIEW Action,也能处理http:的type，
	（3）至于分类Category，一般不要去在Intent中设置它，如果你写Intent的接收者，就在Manifest.xml的Activity的IntentFilter中包含android.category.DEFAULT,这样所有不设置Category（Intent.addCategory(String c);）的Intent都会与这个Category匹配。
	（4）extras（附加信息），是其它所有附加信息的集合。使用extras可以为组件提供扩展信息，比如，如果要执行“发送电子邮件”这个动作，可以将电子邮件的标题、正文等保存在extras里，传给电子邮件发送组件。

##如果后台的Activity由于某原因被系统回收了，如何在被系统回收之前保存当前状态？
当你的程序中某一个Activity A 在运行时中，主动或被动地运行另一个新的Activity B 
这个时候A会执行
public void onSaveInstanceState(Bundle outState) {   
      super.onSaveInstanceState(outState);    
      outState.putLong("id", 1234567890);
}  
B完成以后又会来找A,这个时候就有两种情况，一种是A被回收，一种是没有被回收，被回收的A就要重新调用onCreate()方法，不同于直接启动的是这回onCreate()里是带上参数savedInstanceState，没被收回的就还是onResume就好了。
savedInstanceState是一个Bundle对象，你基本上可以把他理解为系统帮你维护的一个Map对象。在onCreate()里你可能会用到它，如果正常启动onCreate就不会有它，所以用的时候要判断一下是否为空。
if(savedInstanceState != null){  
     long id = savedInstanceState.getLong("id");  
}  
就像官方的Notepad教程里的情况，你正在编辑某一个note，突然被中断，那么就把这个note的id记住，再起来的时候就可以根据这个id去把那个note取出来，程序就完整一些。

##请解释下在单线程模型中Message、Handler、Message Queue、Looper之间的关系。
答：简单的说，Handler获取当前线程中的Looper对象，looper用来从存放Message的MessageQueue中取出Message，再由Handler进行Message的分发和处理.
Message Queue(消息队列)：用来存放通过Handler发布的消息，通常附属于某一个创建它的线程，可以通过Looper.myQueue()得到当前线程的消息队列
Handler：可以发布或者处理一个消息或者操作一个Runnable，通过Handler发布消息，消息将只会发送到与它关联的消息队列，也只能处理该消息队列中的消息。
Looper：是Handler和消息队列之间通讯桥梁，程序组件首先通过Handler把消息传递给Looper，Looper把消息放入队列。Looper也把消息队列里的消息广播给所有的Handler。Handler接受到消息后调用handleMessage进行处理。
Message：消息的类型，在Handler类中的handleMessage方法中得到单个的消息进行处理。在单线程模型下，为了线程通信问题，Android设计了一个Message Queue(消息队列)， 线程间可以通过该Message Queue并结合Handler和Looper组件进行信息交换。下面将对它们进行分别介绍：
（1）Message
    Message消息，理解为线程间交流的信息，处理数据后台线程需要更新UI，则发送Message内含一些数据给UI线程。
	（2）Handler
    Handler处理者，是Message的主要处理者，负责Message的发送，Message内容的执行处理。后台线程就是通过传进来的 Handler对象引用来sendMessage(Message)。而使用Handler，需要implement 该类的 handleMessage(Message)方法，它是处理这些Message的操作内容，例如Update UI。通常需要子类化Handler来实现handleMessage方法。
	（3）Message Queue
    Message Queue消息队列，用来存放通过Handler发布的消息，按照先进先出执行。每个message queue都会有一个对应的Handler。Handler会向messagequeue通过两种方法发送消息：sendMessage或post。这两种消息都会插在message queue队尾并按先进先出执行。但通过这两种方法发送的消息执行的方式略有不同：通过sendMessage发送的是一个message对象,会被 Handler的handleMessage()函数处理；而通过post方法发送的是一个runnable对象，则会自己执行。
	（4）Looper
Looper是每条线程里的Message Queue的管家。Android没有Global的MessageQueue，而Android会自动替主线程(UI线程)建立Message Queue，但在子线程里并没有建立Message Queue。所以调用Looper.getMainLooper()得到的主线程的Looper不为NULL，但调用Looper.myLooper()得到当前线程的Looper就有可能为NULL。对于子线程使用Looper，API Doc提供了正确的使用方法：
1）.在Looper.loop()方法运行开始后，循环地按照接收顺序取出Message Queue里面的非NULL的Message。
   	2）.一开始Message Queue里面的Message都是NULL的。当Handler.sendMessage(Message)到Message Queue，该函数里面设置了那个Message对象的target属性是当前的Handler对象。随后Looper取出了那个Message，则调用 该Message的target指向的Hander的dispatchMessage函数对Message进行处理。在dispatchMessage方法里，如何处理Message则由用户指定，三个判断，优先级从高到低：
    1> Message里面的Callback，一个实现了Runnable接口的对象，其中run函数做处理工作；
    2> Handler里面的mCallback指向的一个实现了Callback接口的对象，由其handleMessage进行处理；
    3> 处理消息Handler对象对应的类继承并实现了其中handleMessage函数，通过这个实现的handleMessage函数处理消息。
    由此可见，我们实现的handleMessage方法是优先级最低的！
    3).Handler处理完该Message (updateUI) 后，Looper则设置该Message为NULL，以便回收！
    在网上有很多文章讲述主线程和其他子线程如何交互，传送信息，最终谁来执行处理信息之类的，个人理解是最简单的方法——判断Handler对象里面的Looper对象是属于哪条线程的，则由该线程来执行！
    	1.当Handler对象的构造函数的参数为空，则为当前所在线程的Looper；
    	2.Looper.getMainLooper()得到的是主线程的Looper对象，Looper.myLooper()得到的是当前线程的Looper对象。

##谈谈android数据存储方式。
Android提供了5种方式存储数据：
（1）使用SharedPreferences存储数据；它是Android提供的用来存储一些简单配置信息的一种机制，采用了XML格式将数据存储到设备中。只能在同一个包内使用，不能在不同的包之间使用。
（2）文件存储数据；文件存储方式是一种较常用的方法，在Android中读取/写入文件的方法，与Java中实现I/O的程序是完全一样的，提供了openFileInput()和openFileOutput()方法来读取设备上的文件。
（3）SQLite数据库存储数据；SQLite是Android所带的一个标准的数据库，它支持SQL语句，它是一个轻量级的嵌入式数据库。
（4）使用ContentProvider存储数据；主要用于应用程序之间进行数据交换，从而能够让其他的应用保存或读取此Content Provider的各种数据类型。
（5）网络存储数据；通过网络上提供给我们的存储空间来上传(存储)和下载(获取)我们存储在网络空间中的数据信息。

## View, surfaceView, GLSurfaceView有什么区别。
View是最基础的，必须在UI主线程内更新画面，速度较慢。
SurfaceView 是view的子类，类似使用双缓机制，在新的线程中更新画面所以刷新界面速度比view快
GLSurfaceView 是SurfaceView的子类，openGL 专用的

## Manifest.xml文件中主要包括哪些信息？
manifest：根节点，描述了package中所有的内容。
uses-permission：为了保证application的正常运行，需要系统授予app的权限声明，如android.permission.CAMERA。
permission：通常情况下我们不需要为自己的应用程序声明某个权限，除非你提供了供其他应用程序调用的代码或者数据。这个时候你才需要使用<permission> 这个标签。很显然这个标签可以让我们声明自己的权限。当然你千万不要以为你自己声明的permission你就可以随意使用，你还是需要使用<uses-permission>来声明你需要该权限，这时你的应用才能正常工作。
instrumentation：声明了用来测试此package或其他package指令组件的代码。
application：包含package中application级别组件声明的根节点。
activity：Activity是用来与用户交互的主要工具。
receiver：IntentReceiver能使的application获得数据的改变或者发生的操作，即使它当前不在运行。
service：Service是能在后台运行任意时间的组件。
provider：ContentProvider是用来管理持久化数据并发布给其他应用程序使用的组件。

## 根据自己的理解描述下Android数字签名。
(1)所有的应用程序都必须有数字证书，Android系统不会安装一个没有数字证书的应用程序。
(2)Android程序包使用的数字证书可以是自签名的，不需要一个权威的数字证书机构签名认证。
(3)如果要正式发布一个Android程序，必须使用一个合适的私钥生成的数字证书来给程序签名，而不能使用adt插件或者ant工具生成的调试证书来发布。
(4)数字证书都是有有效期的，Android只是在应用程序安装的时候才会检查证书的有效期。如果程序已经安装在系统中，即使证书过期也不会影响程序的正常功能。

## android:gravity与android:layout_gravity的区别
LinearLayout有两个非常相似的属性：android:gravity与android:layout_gravity。他们的区别在于：android:gravity用于设置View组件的对齐方式，而android:layout_gravity用于设置Container组件的对齐方式。
举个例子，我们可以通过设置android:gravity="center"来让EditText中的文字在EditText组件中居中显示；同时我们设置EditText的android:layout_gravity="right"来让EditText组件在LinearLayout中居右显示。

## 注册广播接收者两种方式的区别，及优缺点
首先写一个类要继承BroadcastReceiver
第一种:在清单文件中声明,添加
<receiveandroid:name=".IncomingSMSReceiver " >
<intent-filter>
   <actionandroid:name="android.provider.Telephony.SMS_RECEIVED")
<intent-filter>
<receiver>
第二种使用代码进行注册如:
IntentFilterfilter =  newIntentFilter("android.provider.Telephony.SMS_RECEIVED");
IncomingSMSReceiverreceiver = new IncomgSMSReceiver();
registerReceiver(receiver.filter);
两种注册类型的区别是：
1)第一种是常驻型（静态注册），也就是说当应用程序关闭后，如果有信息广播来，程序也会被系统调用自动运行。
2)第二种不是常驻型广播（动态注册），也就是说广播跟随程序的生命周期。
动态注册的优先级是要高于静态注册优先级的。

## Android中有哪几种解析xml的类,官方推荐哪种？以及它们的原理和区别.
DOM解析
优点:
1.XML树在内存中完整存储,因此可以直接修改其数据和结构. 
2.可以通过该解析器随时访问XML树中的任何一个节点. 
3.DOM解析器的API在使用上也相对比较简单.
缺点:
如果XML文档体积比较大时,将文档读入内存是非常消耗系统资源的.
使用场景:DOM是用与平台和语言无关的方式表示XML文档的官方W3C标准。DOM是以层次结构组织的节点的集合.这个层次结构允许开发人员在树中寻找特定信息.分析该结构通常需要加载整个文档和构造层次结构,然后才能进行任何工作.DOM是基于对象层次结构的.

SAX解析
优点:
SAX对内存的要求比较低,因为它让开发人员自己来决定所要处理的标签.特别是当开发人员只需要处理文档中所包含的部分数据时,SAX这种扩展能力得到了更好的体现.
缺点:
用SAX方式进行XML解析时,需要顺序执行,所以很难访问到同一文档中的不同数据.此外,在基于该方式的解析编码过程也相对复杂.
使用场景:
对于含有数据量十分巨大,而又不用对文档的所有数据进行遍历或者分析的时候,使用该方法十分有效.该方法不用将整个文档读入内存,而只需读取到程序所需的文档标签处即可.

Xmlpull解析
android SDK提供了xmlpull api,xmlpull和sax类似,是基于流（stream）操作文件,然后根据节点事件回调开发者编写的处理程序.因为是基于流的处理,因此xmlpull和sax都比较节约内存资源,不会象dom那样要把所有节点以对橡树的形式展现在内存中.xmlpull比sax更简明,而且不需要扫描完整个流.

## AsyncTask的用法 
在开发Android应用时必须遵守单线程模型的原则：Android UI操作并不是线程安全的并且这些操作必须在UI线程中执行。在单线程模型中始终要记住两条法则： 
1. 不要阻塞UI线程 
2. 确保只在UI线程中访问Android UI工具包 
	当一个程序第一次启动时，Android会同时启动一个对应的主线程(Main Thread)，主线程主要负责处理与UI相关的事件，如：用户的按键事件，用户接触屏幕的事件以及屏幕绘图事件，并把相关的事件分发到对应的组件进行处理。所以主线程通常又被叫做UI线程。比如说从网上获取一个网页，在一个TextView中将其源代码显示出来，这种涉及到网络操作的程序一般都是需要开一个线程完成网络访问，但是在获得页面源码后，是不能直接在网络操作线程中调用TextView.setText()的.因为其他线程中是不能直接访问主UI线程成员 。
android提供了几种在其他线程中访问UI线程的方法。 
Activity.runOnUiThread( Runnable ) 
View.post( Runnable ) 
View.postDelayed( Runnable, long ) 
Handler 
这些类或方法同样会使你的代码很复杂很难理解。然而当你需要实现一些很复杂的操作并需要频繁地更新UI时这会变得更糟糕。为了解决这个问题，Android 1.5提供了一个工具类：AsyncTask，它使创建需要与用户界面交互的长时间运行的任务变得更简单。相对来说AsyncTask更轻量级一些，适用于简单的异步处理，不需要借助线程和Handler即可实现。 
AsyncTask是抽象类.AsyncTask定义了三种泛型类型 Params，Progress和Result。 
　　Params 启动任务执行的输入参数，比如HTTP请求的URL。 
　　Progress 后台任务执行的百分比。 
　　Result 后台执行任务最终返回的结果，比如String。 
 	AsyncTask的执行分为四个步骤，每一步都对应一个回调方法，这些方法不应该由应用程序调用，开发者需要做的就是实现这些方法。 
　　1) 子类化AsyncTask 
　　2) 实现AsyncTask中定义的下面一个或几个方法 
　　	onPreExecute(), 该方法将在执行实际的后台操作前被UI thread调用。可以在该方法中做一些准备工作，如在界面上显示一个进度条。 
　　  doInBackground(Params...), 将在onPreExecute 方法执行后马上执行，该方法运行在后台线程中。这里将主要负责执行那些很耗时的后台计算工作。可以调用 publishProgress方法来更新实时的任务进度。该方法是抽象方法，子类必须实现。 
　　  onProgressUpdate(Progress...),在publishProgress方法被调用后，UI thread将调用这个方法从而在界面上展示任务的进展情况，例如通过一个进度条进行展示。 
　　  onPostExecute(Result), 在doInBackground 执行完成后，onPostExecute 方法将被UI thread调用，后台的计算结果将通过该方法传递到UI thread. 

为了正确的使用AsyncTask类，以下是几条必须遵守的准则： 
　　1) Task的实例必须在UI thread中创建 
　　2) execute方法必须在UI thread中调用 
　　3) 不要手动的调用onPreExecute(), onPostExecute(Result)，doInBackground(Params...), onProgressUpdate(Progress...)这几个方法 
　　4) 该task只能被执行一次，否则多次调用时将会出现异常 
 	doInBackground方法和onPostExecute的参数必须对应，这两个参数在AsyncTask声明的泛型参数列表中指定，第一个为doInBackground接受的参数，第二个为显示进度的参数，第三个为doInBackground返回和onPostExecute传入的参数。
最后需要说明AsyncTask不能完全取代线程，在一些逻辑较为复杂或者需要在后台反复执行的逻辑就可能需要线程来实现了。

## AsyncTask和Handler的优缺点比较 
Android之所以有Handler和AsyncTask，都是为了不阻塞主线程（UI线程），且UI的更新只能在主线程中完成，因此异步处理是不可避免的。Android为了降低这个开发难度，提供了AsyncTask。AsyncTask就是一个封装过的后台任务类，顾名思义就是异步任务。
AsyncTask实现的原理和适用的优缺点：AsyncTask,是android提供的轻量级的异步类,可以直接继承AsyncTask,在类中实现异步操作,并提供接口反馈当前异步执行的程度(可以通过接口实现UI进度更新),最后反馈执行的结果给UI主线程.
使用的优点:简单,快捷、过程可控      
使用的缺点:在使用多个异步操作和并需要进行Ui变更时,就变得复杂起来。

Handler异步实现的原理和适用的优缺点：在Handler 异步实现时,涉及到 Handler, Looper, Message,Thread四个对象，实现异步的流程是主线程启动Thread（子线程）运行并生成Message-Looper获取Message并传递给HandlerHandler逐个获取Looper中的Message，并进行UI变更。
使用的优点：结构清晰，功能定义明确；对于多个后台任务时，简单，清晰
使用的缺点：在单个后台异步处理时，显得代码过多，结构过于复杂（相对性）
Android的AsyncTask比Handler更轻量级一些（只是代码上轻量一些，而实际上要比handler更耗资源），适用于简单的异步处理。

## Handler sendMessage与obtainMessage (sendToTarget)比较
//Message msg = new Message()
Message msg = handler.obtainMessage(); msg.what = xxx; msg.arg1  = xxx; msg.arg2  = xxx; msg.obj    = xxx;
通过obtainMessage最终得到的Message 已经不是自己创建的了,而是从MessagePool拿的,省去了创建对象申请内存的开销。尽量使用 Message msg = handler.obtainMessage();的形式创建Message，不要自己New Message 。至于message产生之后你使用obtainMessage 或者是 sendMessage 效率影响并不大。同时我们也要注意以后谈论性能的时候要找准位置,譬如这里性能的问题不是在调用 obtainMessage 和 sendMessage 的方法上,而是调用他们之前对象的创建问题上。

## Java中的字符串分割 
java中的split函数和js中的split函数不一样。 
Java中的我们可以利用split把字符串按照指定的分割符进行分割，然后返回字符串数组，下面是string.split的用法实例及注意事项： 
java.lang.string.split 
split 方法 
将一个字符串分割为子字符串，然后将结果作为字符串数组返回。 
stringObj.split([separator，[limit]])  
stringObj 
必选项。要被分解的 String 对象或文字,该对象不会被split方法修改。 
separator 
可选项。字符串或正则表达式对象，它标识了分隔字符串时使用的是一个还是多个字符。如果忽略该选项，返回包含整个字符串的单一元素数组。 
limit 
可选项。该值用来限制返回数组中的元素个数(也就是最多分割成几个数组元素,只有为正数时有影响) 
split 方法的结果是一个字符串数组，在 stingObj 中每个出现 separator 的位置都要进行分解。separator不作为任何数组元素的部分返回。 
示例1： 
```
      String str="Java string split test"; 
      String[] strarray=str.split(" "); 
      for (int i = 0; i < strarray.length; i++) 
          System.out.println(strarray[i]); 
```
将输出： 
Java 
string 
split 
test 

示例2： 
```
      String str="Java string split test"; 
      String[] strarray=str.split(" ",2);//使用limit，最多分割成2个字符串 
      for (int i = 0; i < strarray.length; i++) 
          System.out.println(strarray[i]); 
```
将输出： 
Java 
string split test 

示例3： 
```
      String str="192.168.0.1"; 
      String[] strarray=str.split("."); 
      for (int i = 0; i < strarray.length; i++) 
          System.out.println(strarray[i]); 
```
结果是什么也没输出,将split(".")改为split("\\."),将输出正确结果： 
192 
168 
0 
1 

经验分享： 
1、分隔符为“.”(无输出),“|”(不能得到正确结果)转义字符时,“*”,“+”时出错抛出异常,都必须在前面加必须得加"\\",如split(\\|); 
2、如果用"\"作为分隔,就得写成这样：String.split("\\\\"),因为在Java中是用"\\"来表示"\"的,字符串得写成这样：String Str="a\\b\\c"; 
转义字符,必须得加"\\"; 
3、如果在一个字符串中有多个分隔符,可以用"|"作为连字符,比如：String str="Java string-split#test",可以用Str.split(" |-|#")把每个字符串分开; 
 
## String,StringBuffer与StringBuilder的区别
String 字符串常量
StringBuffer 字符串变量（线程安全）
StringBuilder 字符串变量（非线程安全）
 	简要的说，String 类型和 StringBuffer 类型的主要性能区别其实在于 String 是不可变的对象, 因此在每次对 String 类型进行改变的时候其实都等同于生成了一个新的 String 对象，然后将指针指向新的 String 对象，所以经常改变内容的字符串最好不要用 String ，因为每次生成对象都会对系统性能产生影响，特别当内存中无引用对象多了以后， JVM 的 GC 就会开始工作，那速度是一定会相当慢的。
 	而如果是使用 StringBuffer 类则结果就不一样了，每次结果都会对 StringBuffer 对象本身进行操作，而不是生成新的对象，再改变对象引用。所以在一般情况下我们推荐使用 StringBuffer ，特别是字符串对象经常改变的情况下。而在某些特别情况下， String 对象的字符串拼接其实是被 JVM 解释成了 StringBuffer 对象的拼接，所以这些时候 String 对象的速度并不会比 StringBuffer 对象慢，而特别是以下的字符串对象生成中， String 效率是远要比 StringBuffer 快的：
 String S1 = “This is only a” + “ simple” + “ test”;
 StringBuffer Sb = new StringBuilder(“This is only a”).append(“ simple”).append(“ test”);
 你会很惊讶的发现，生成 String S1 对象的速度简直太快了，而这个时候 StringBuffer 居然速度上根本一点都不占优势。其实这是 JVM 的一个把戏，在 JVM 眼里，这个
 String S1 = “This is only a” + “ simple” + “test”; 其实就是：
 String S1 = “This is only a simple test”; （编译器已确定值，未进入运行时）所以当然不需要太多的时间了。但大家这里要注意的是，如果你的字符串是来自另外的 String 对象的话，速度就没那么快了，譬如：
String S2 = “This is only a”;
String S3 = “ simple”;
String S4 = “ test”;
String S1 = S2 +S3 + S4;
这时候 JVM 会规规矩矩的按照原来的方式去做
Java.lang.StringBuffer是线程安全的可变字符序列。可将字符串缓冲区安全地用于多个线程。
StringBuffer 上的主要操作是 append 和 insert 方法，可重载这些方法，以接受任意类型的数据。每个方法都能有效地将给定的数据转换成字符串，然后将该字符串的字符追加或插入到字符串缓冲区中。append 方法始终将这些字符添加到缓冲区的末端；而 insert 方法则在指定的点添加字符。
例如，如果 z 引用一个当前内容是“start”的字符串缓冲区对象，则此方法调用 z.append("le") 会使字符串缓冲区包含“startle”，而 z.insert(4, "le") 将更改字符串缓冲区，使之包含“starlet”。
java.lang.StringBuilder
java.lang.StringBuilder一个可变的字符序列是5.0新增的。此类提供一个与 StringBuffer 兼容的 API，但不保证同步。该类被设计用作 StringBuffer 的一个简易替换，用在字符串缓冲区被单个线程使用的时候（这种情况很普遍）。如果可能，建议优先采用该类，因为在大多数实现中，它比 StringBuffer 要快。两者的方法基本相同。

## TextView滚动功能的实现 
滚动条大家肯定不陌生的，当然这里说的不是ScrollView，在很多的时候需要一个TextView来显示很多内容，但是内容太多了超过了TextView的显示范围，这个时候就需要TextView里面的内容滚动起来。首先看下布局文件：
	<TextView  
		android:id="@+id/reportContent"  
		android:layout_width="fill_parent"  
		android:layout_height="wrap_content"  
		android:maxLines="20"  
		android:scrollbars="vertical"  
		android:singleLine="false" /> 
当内容超过了20行的时候就会出现滚动条了，这样才符合上面的描述，但是即便布局文件这样设置了相关属性，在显示的时候仍然达不到我们的要求，因为我们肯定需要把没有显示出来的内容通过滚动能够显示出来，这就需要在代码里面设置TextView的相关属性来，代码如下：
	reportContent = (TextView)findViewById(R.id.reportContent);  
	//如果reportContent内容太多了的话，我们需要让其滚动起来，  
	//具体可以查看SDK中android.text.method了解更多，代码如下：  
reportContent.setMovementMethod(ScrollingMovementMethod.getInstance()) ;
  
## Java中的流(概念和示例) 
流是个抽象的概念，是对输入输出设备的抽象，Java程序中，对于数据的输入/输出操作都是以“流”的方式进行。设备可以是文件，网络，内存等。流具有方向性，至于是输入流还是输出流则是一个相对的概念，一般以程序为参考，如果数据的流向是程序至设备，我们成为输出流，反之我们称为输入流。
当程序需要从某个数据源读入数据的时候，就会开启一个输入流，数据源可以是文件、内存或网络等等。相反地，需要写出数据到某个数据源目的地的时候，也会开启一个输出流，这个数据源目的地也可以是文件、内存或网络等等。
可以从不同的角度对流进行分类：
1.处理的数据单位不同，可分为：字符流，字节流
2.数据流方向不同，可分为：输入流，输出流
3.功能不同，可分为：节点流，处理流
1.和 2.都比较好理解，对于根据功能分类的，可以这么理解：
节点流：节点流从一个特定的数据源读写数据。即节点流是直接操作文件，网络等的流，例如FileInputStream和FileOutputStream，他们直接从文件中读取或往文件中写入字节流。
 
![image](https://github.com/woojean/woojean.github.io/blob/master/images/java_10.png)
处理流：“连接”在已存在的流（节点流或处理流）之上通过对数据的处理为程序提供更为强大的读写功能。过滤流是使用一个已经存在的输入流或输出流连接创建的，过滤流就是对节点流进行一系列的包装。例如BufferedInputStream和BufferedOutputStream，使用已经存在的节点流来构造，提供带缓冲的读写，提高了读写的效率，以及DataInputStream和DataOutputStream，使用已经存在的节点流来构造，提供了读写Java中的基本数据类型的功能。他们都属于过滤流。
 
![image](https://github.com/woojean/woojean.github.io/blob/master/images/java_11.png)


流结构介绍：
Java所有的流类位于java.io包中，都分别继承自以下四种抽象流类型。
 	字节流	字符流
输入流	InputStream	Reader
输出流	OutputStream	Writer
1.继承自InputStream/OutputStream的流都是用于向程序中输入/输出数据，且数据的单位都是字节(byte=8bit)，如图，深色的为节点流，浅色的为处理流。
  
![image](https://github.com/woojean/woojean.github.io/blob/master/images/java_12.png)
 
![image](https://github.com/woojean/woojean.github.io/blob/master/images/java_13.png)

2.继承自Reader/Writer的流都是用于向程序中输入/输出数据，且数据的单位都是字符(2byte=16bit)，如图，深色的为节点流，浅色的为处理流。
 
![image](https://github.com/woojean/woojean.github.io/blob/master/images/java_14.png)
 
![image](https://github.com/woojean/woojean.github.io/blob/master/images/java_15.png)

常见流类介绍：
节点流类型常见的有：
对文件操作的字符流有FileReader/FileWriter，字节流有FileInputStream/FileOutputStream。
处理流类型常见的有：
缓冲流：缓冲流要“套接”在相应的节点流之上，对读写的数据提供了缓冲的功能，提高了读写效率，同事增加了一些新的方法。
　　字节缓冲流有BufferedInputStream/BufferedOutputStream，字符缓冲流有BufferedReader/BufferedWriter，字符缓冲流分别提供了读取和写入一行的方法ReadLine和NewLine方法。
　　对于输出的缓冲流，写出的数据，会先写入到内存中，再使用flush方法将内存中的数据刷到硬盘。所以，在使用字符缓冲流的时候，一定要先flush，然后再close，避免数据丢失。
转换流：用于字节数据到字符数据之间的转换。
　　仅有字符流InputStreamReader/OutputStreamWriter。其中，InputStreamReader需要与InputStream“套接”，OutputStreamWriter需要与OutputStream“套接”。
数据流：提供了读写Java中的基本数据类型的功能。
　　DataInputStream和DataOutputStream分别继承自InputStream和OutputStream，需要“套接”在InputStream和OutputStream类型的节点流之上。
对象流：用于直接将对象写入写出。
　　流类有ObjectInputStream和ObjectOutputStream，本身这两个方法没什么，但是其要写出的对象有要求，该对象必须实现Serializable接口，来声明其是可以序列化的。否则，不能用对象流读写。
还有一个关键字比较重要，transient，由于修饰实现了Serializable接口的类内的属性，被该修饰符修饰的属性，在以对象流的方式输出的时候，该字段会被忽略。（当持久化对象时，可能有一个特殊的对象数据成员，我们不想用serialization机制来保存它。为了在一个特定对象的一个域上关闭serialization，可以在这个域前加上关键字transient。当一个对象被序列化的时候，transient型变量的值不包括在序列化的表示中，然而非transient型的变量是被包括进去的。
使用场景举例：如果一个用户有一些敏感信息（譬如密码，银行卡号等），为了安全起见，不希望在网络操作（主要涉及到序列化操作，本地序列化缓存也适用）中被传输。这些信息对应的变量就可以被定义为transient类型。换句话说，这个字段的生命周期仅存于调用者的内存中。 ）


## java 字节流与字符流的区别

实际上字节流在操作时本身不会用到缓冲区（内存），是文件本身直接操作的，而字符流在操作时使用了缓冲区，通过缓冲区再操作文件。

可以简单地把缓冲区理解为一段特殊的内存。
某些情况下，如果一个程序频繁地操作一个资源（如文件或数据库），则性能会很低，此时为了提升性能，就可以将一部分数据暂时读入到内存的一块区域之中，以后直接从此区域中读取数据即可，因为读取内存速度会比较快，这样可以提升程序的性能。在字符流的操作中，所有的字符都是在内存中形成的，在输出前会将所有的内容暂时保存在内存之中，所以使用了缓冲区暂存数据。
如果想在不关闭时也可以将字符流的内容全部输出，则可以使用Writer类中的flush()方法完成。


## Content Provider数据存储实例
一、Content Provider基本概念
1.ContentProvider为存储和获取数据提供了统一的接口。ContentProvide对数据进行封装，不用关心数据存储的细节。使用表的形式来组织数据。（当应用继承ContentProvider类，并重写该类用于提供数据和存储数据的方法，就可以向其他应用共享其数据。虽然使用其他方法也可以对外共享数据，但数据访问方式会因数据存储的方式而不同，如：采用文件方式对外共享数据，需要进行文件操作读写数据；采用sharedpreferences共享数据，需要使用sharedpreferences API读写数据。而使用ContentProvider共享数据的好处是统一了数据访问方式。）
2.使用ContentProvider可以在不同的应用程序之间共享数据。 
3.Android为常见的一些数据提供了默认的ContentProvider（包括音频、视频、图片和通讯录等）。   
ContentProvider所提供的函数：
query(),insert(),update(),delete(),getType(),onCreate()等。

二、URI（统一资源标识符）的使用方法
为系统的每一个资源给其一个名字，比方说通话记录。
1.每一个ContentProvider都拥有一个公共的URI，这个URI用于表示这个ContentProvider所提供的数据。 
2.Android所提供的ContentProvider都存放在android.provider包中。将其分为A，B，C，D 4个部分：
	A：标准前缀，用来说明一个Content Provider控制这些数据，无法改变的；"content://"
	B：URI 的标识，它定义了是哪个Content Provider提供这些数据。对于第三方应用程序，为了保证URI标识的唯一性，它必须是一个完整的、小写的类名。这个标识在元素的 authorities属性中说明：一般是定义该ContentProvider的包.类的名称:"content://hx.android.text.myprovider"
	C：路径，不知道是不是路径，通俗的讲就是你要操作的数据库中表的名字，或者你也可以自己定义，记得在使用的时候保持一致就ok了；"content://hx.android.text.myprovider/tablename"
D：如果URI中包含表示需要获取的记录的ID；则就返回该id对应的数据，如果没有ID，就表示返回全部； "content://hx.android.text.myprovider/tablename/#" #表示数据id
（尽管这种查询字符串格式很常见，但是它看起来还是有点令人迷惑。为此，Android提供一系列的帮助类（在android.provider包下），里面包含了很多以类变量形式给出的查询字符串，这种方式更容易让我们理解一点，因此，如上面content://contacts/people/45这个URI就可以写成如下形式：
　　Uri person = ContentUris.withAppendedId(People.CONTENT_URI,  45);
然后执行数据查询:
Cursor cur = managedQuery(person, null, null, null);
这个查询返回一个包含所有数据字段的游标，我们可以通过迭代这个游标来获取所有的数据）

## 创建和使用ContentProvider
要创建我们自己的Content Provider的话，我们需要遵循以下几步：
a.创建一个继承了ContentProvider父类的类
b.定义一个名为CONTENT_URI，并且是public static final的Uri类型的类变量，你必须为其指定一个唯一的字符串值，最好的方案是以类的全名称， 如:
public static final Uri CONTENT_URI = Uri.parse(“content://com.google.android.MyContentProvider”);
c. 定义你要返回给客户端的数据列名。如果你正在使用Android数据库，必须为其定义一个叫_id的列，它用来表示每条记录的唯一性。
d. 创建你的数据存储系统。大多数Content Provider使用Android文件系统或SQLite数据库来保持数据，但是你也可以以任何你想要的方式来存储。
e. 如果你要存储字节型数据，比如位图文件等，数据列其实是一个表示实际保存文件的URI字符串，通过它来读取对应的文件数据。处理这种数据类型的Content Provider需要实现一个名为_data的字段，_data字段列出了该文件在Android文件系统上的精确路径。这个字段不仅是供客户端使用，而且也可以供ContentResolver使用。客户端可以调用ContentResolver.openOutputStream()方法来处理该URI指向的文件资源；如果是ContentResolver本身的话，由于其持有的权限比客户端要高，所以它能直接访问该数据文件。
f.声明public static String型的变量，用于指定要从游标处返回的数据列。
g.查询返回一个Cursor类型的对象。所有执行写操作的方法如insert(), update() 以及delete()都将被监听。我们可以通过使用ContentResover().notifyChange()方法来通知监听器关于数据更新的信息。
h. 在AndroidMenifest.xml中使用<provider>标签来设置Content Provider。
i. 如果你要处理的数据类型是一种比较新的类型，你就必须先定义一个新的MIME类型，以供ContentProvider.geType(url)来返回。
MIME类型有两种形式:一种是为指定的单个记录的，还有一种是为多条记录的。（结尾方式不同）这里给出一种常用的格式：
　　vnd.android.cursor.item/vnd.yourcompanyname.contenttype （单个记录的MIME类型）
 　比如, 一个请求列车信息的URI如content://com.example.transportationprovider/trains/122 可能就会返回typevnd.android.cursor.item/vnd.example.rail这样一个MIME类型。
　　vnd.android.cursor.dir/vnd.yourcompanyname.contenttype （多个记录的MIME类型）
 　　比如, 一个请求所有列车信息的URI如content://com.example.transportationprovider/trains 可能就会返回vnd.android.cursor.dir/vnd.example.rail这样一个MIME 类型。
Content Provider的入口需要在AndroidManifest.xml中配置:
<provider 
android:name=”MyContentProvider” 
android:authorities=”com.wissen.MyContentProvider” />
android:authorities的值一定要与定义的常量字符串AUTHORITY的值相同，否则会报错。


## 横竖屏切换
Android中当屏幕横竖屏切换时，Activity的生命周期是重新加载（说明当前的Activity给销毁了，但又重新执行加载）
在AndroidManifest.xml中为Activity设置configChanges属性:
<activity android:name=".MainActivity"
	android:label="@string/app_name" android:configChanges="orientation|keyboardHidden">
configChanges有如下选项：  
	1. orientation ：屏幕在纵向和横向间旋转
	2. keyboardHidden：键盘显示或隐藏 
	3.fontScale：用户变更了首选的字体大小   
	4.locale ： 用户选择了不同的语言设定，
	5.keyboard ：键盘类型变更，例如手机从12键盘切换到全键盘   
	6.touchscreen或navigation：键盘或导航方式变化
如果缺少了keyboardHidden选项不能防止Activity的销毁，并且在之后提到的onConfigurationChanged事件中只能捕获竖屏变横屏的事件不能捕获横屏变竖屏。
注：如果项目不需要屏幕切换时可以设置为
	android:screenOrientation="portrait" 始终以竖屏显示 
	android:screenOrientation="landscape" 始终以横屏显示
也可以通过代码来控制横竖屏：                MainActivity.this.setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_LANDSCAPE);
MainActivity.this.setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_PORTRAIT);

## Back键与Home键区别
back键默认行为是finish处于前台的Activity的即Activity的状态为Destroy状态为止，再次启动该Activity是从onCreate开始的(不会调用onSaveInstanceState方法)。Home键默认是stop前台的Activity即状态为onStop为止而不是destroy,若再次启动它，会调用onSaveInstanceState方法，保持上次Activity的状态则是从OnRestart开始的---->onStart()--->onResume()。
  
## Android task和back stack
一个任务是用户在执行某种工作时所交互的Activity的集合．Activity们放置在一个栈("后退栈")中，按照打开的顺序排列．（这里的Activity不一定属于同一个应用，可以来自于不同的应用。Android可以把不同应用的Activity衔接在一起来达到无缝的用户体验）
当用户触摸在应用启动台中的图标(或一个home屏上的快捷方式)时，应用的任务就来到了前台．如果没有这个应用的已存在的任务(这个应用最近没有被使用)，那么一个新的任务被创建并且这个应用的"main"activity被作为栈的根activity打开．当当前的activity启动了另一个activity，新的activity被放置在栈顶并拥有焦点．先前的activity依然保存在栈中，但是停止了．当一个activity停止时，系统保存了它的用户界的当前状态．当用户后退按钮时，当前的activity被从栈顶弹出(activity被销毁了)并且先前的activity被恢复了．栈中的Activities永不会被重新排列，只是入栈或出栈—当被当前activity启动时就入栈，当用户使用后退按钮离开它时就出栈。如果用户继续后退，那么栈中的各activity被弹出来展示上一个，直到用户退到Home屏(或到达任务开始时运行的那个activity)．当所有的activitie都从棧种移除，任务就不再存在．
一个任务是一个有聚合力的单元，它可以在用户启动一个新的任务或回到home屏时被整体地移到后台．当位于后台时，任务中的所有的activitie都处于停止，但是任务的后退栈却保存完整—当任务被另一个任务取代时，仅仅是失去了焦点。两个任务：任务B到了前台，任务A于是被打入后台，伺机恢复：
 
![image](https://github.com/woojean/woojean.github.io/blob/master/images/java_16.png)

（多个任务可以同时存在于后台．然而，如果用户在同一时刻运行多个后台任务，系统可能会销毁后台activitie来釋放内存，从而导致activity状态的丢失．）
Android管理任务和后退栈的方式，如前面文章所述—通过把所有接连启动的activity放在同一个任务中并且是同一个后进先出的栈中—在大多数应用中工作得很好并且你无需关心你的activity如何与任务相关连或如何在后退栈中存在．然而，你可能决定要打破这种正常的行为．可能你想在你应用的activity启动时开始一个新的任务(而不是放置到当前栈中)；或者，当你启动一个activity，你想把已经运行的它的一个实例提到前台来(而不是创建一个新的实例放在后退栈的顶端)；或者，你希望当用户离开任务时，你的后退栈清除除了根activity以外所有的activity．
可以做这些事情，甚至更多事情，通过设置manifest中<activity>的属性和传到startActivity()的intent的flag．
在这一点上，你可以设置的最重要的<activity>属性有：
taskAffinity
launchMode
allowTaskReparenting
clearTaskOnLaunch
alwaysRetainTaskState
finishOnTaskLaunch

可以使用的最重要的intent　flag：
FLAG_ACTIVITY_NEW_TASK
FLAG_ACTIVITY_CLEAR_TOP
FLAG_ACTIVITY_SINGLE_TOP

## 定义Activity启动模式
启动模式使你可以定义新的activity如何与当前的任务相关联．有两种方法来定义不同的启动模式：
1）使用manifest文件：当你在你的manifest文件中声明一个activity时，你可以指定activity在启动时如何与任务相关联．
2）使用Intent的flag：当你调用startActivity()时，你可以在Intent中包含指明新的activity如何（或是否）与当前栈关联的flag．
同样的，如果ActivityA 启动ActivityB，ActivityB可以在它的manifest中定义如何与当前的任务关联(如果真的存在)并且ActivityA 也可以请求让ActivityB如何与当前的任务关联．如果两个activity都定义了ActivityB如何与任务关联，那么ActivityA的请求(在intent中定义)优先于ActivityB的请求(在它的manifest中定义)．
一些启动模式可以用在manifest中但不能用在intent的flag上，同样的，一些启动模式可以用在intent的flag上但不能用在manifest中．
使用manifest文件：当在你的manifest文件中声明一个activity时，你可以使用<activity>元素的launchMode属性指定activity如何与一个任务关联。launchMode属性指明了activity如何启动到一个任务中去．有四种不同的启动模式你可以用于指定给launchMode属性：
（1）"standard"(默认模式)
默认．系统创建一个新的activity的实例到启动它的任务中．activity可以被多次实例化，每个实例可以属于不同的任务，也可以属于同一个任务．
（2）"singleTop"
如果一个activity的实例已经存在于当前任务的栈的顶端，系统通过调用它的onNewIntent()方法把intent路由到这个实例，而不是创建一个新的实例．activity可以被多次实例化，每个实例可以属于不同的任务，并且一个任务可以具有多个实例(但只是当位于后退栈的顶端的activity不存在时才会出现这种现像)．
例如，假设一个任务的后退栈中有根ActivityA和activityB,C,Ｄ(A-B-C-D;D位于顶端)．一个intent到达了D类型的activity(不是指这里的acitivityD)．如果D具有默认的"standard"启动模式，一个新的类的实例被启动并且栈变为A-B-C-D-D．然而，如果D的启动模式是"singleTop"，那么这个已存在的ActivityD就通过onNewIntent()接收到intent，因为它在栈的顶端—栈于是依然保持A-B-C-D．又然而，如果一个intent到达了B类型的activity(不是此处的activityB)，那么一个新的B实例被添加到栈中，即使它的启动模式是"singleTop"．
注：当一个新的activity的实例被创建，用户可以按下后退键回到上一个activity．但当一个已存在的activity实例处理了一个新intent，用户就不能按下后退键回到当前的activity在intent来之前的状态．
（3）"singleTask"
系统创建一个新的任务并且实例化activity为新任务的根．然而，如果一个activity的实例已存在于另一个任务，系统就会通过调用这个activity的onNewIntent()把intent路由给它，而不是创建一个新的实例．某个时刻只有一个activity的实例可以存在．
　　注：尽管activity在一个新任务中启动，后退键依然可以返回到上一个activity．
（4）"singleInstance".
　跟"singleTask"一样．除了系统不能再启动其它activity到拥有这个activity实例的任务中．activity永远是任务的唯一；任何由这个activity启动的其它activity都在另一个任务中打开．

##  intent中定义启动模式
Android浏览器应用声明网页浏览activity必须在它自己的任务中打开—通过在<activity>元素中指定singleTask启动模式．这表示如果你的应用发出一个intent来打开Android浏览器，它的activity不会放到你的应用所在的任务中．代替的是，可能一个新的任务为浏览器启动，或者，如果浏览器已经运行于后台，它所在的任务就被弄到前台并接受这个intent．
　不论一个从一个新任务启动activity还是在一个已存在这种activity的任务中启动，后退键总是能后退到前一个activity．然而，如果你在任务Ａ中启动一个声明为singleTask模式的activity，而这个activity可能在后台已有一个属于一个任务（任务Ｂ）的实例．任务Ｂ于是被弄到前台并处理这个新的intent．那么此时后退键会先一层层返回任务ＢActivity，然后再返回到任务Ａ的顶端activity．图 4演示了这种情形．
 
![image](https://github.com/woojean/woojean.github.io/blob/master/images/java_17.png)


上图演示一个"singleTask"启动模式的acitvity如何被添加到一个后退栈中．如果这个activity已经是一个后台任务(任务B)自己的栈的一部分，那么整个后退栈被弄到前台，位于当前任务 (任务A)的上面．
注：你使用launchMode属性的指定的actvitiy的行为可以被intent的flag覆盖．
 
使用 Intentflags：当启动一个activity时，你可以在给startActivity()的intent中包含flag以改变activity与任务的默认关联方式．你可以用来改变默认行为的flag有：
•FLAG_ACTIVITY_NEW_TASK
在新的任务中启动activity－即不在本任务中启动．如果一个包含这个activity的任务已经在运行，那个任务就被弄到前台并恢复其UI状态，然后这个已存在的activity在onNewIntent()中接收新的intent．这个标志产生与"singleTask"相同的行为．
•FLAG_ACTIVITY_SINGLE_TOP
如果正启动的activity就是当前的activity(位于后退栈的顶端)，那么这个已存在的实例就接收到onNewIntent()的调用，而不是创建一个新的实例．这产生与"singleTop"模式相同的行为．
•FLAG_ACTIVITY_CLEAR_TOP
如果要启动的activity已经在当前任务中运行，那么在它之上的所有其它的activity都被销毁掉，然后这个activity被恢复，而且通过onNewIntent()，initent被发送到这个activity（现在位于顶部了）
没有launchMode属性值对应这种行为．
FLAG_ACTIVITY_CLEAR_TOP多数时候与FLAG_ACTIVITY_NEW_TASK联用．当一起使用时，会在其它任务中寻找一个已存在的activity实例并其把它放到一个可以响应intent的位置．
注：如果Activity的启动模式是"standard"，FLAG_ACTIVITY_CLEAR_TOP会导致已存在的activity被从栈中移除然后在这个位置创建一个新的实例来处理到来的intent．这是因为"standard"模式会导致总是为新的intent创建新的实例．

## 处理任务亲和力
亲和力表明了一个activity＂心仪＂哪个任务．默认下，属于同一个应用的所有activitie之间具有相同的任务亲和力．所以，默认下，一个应用的所有activitie首选属于同一任务．然而，你可以修改一个activity的默认任务亲和力．定义于不同应用的Activitie可以具有相同的任务亲和力，或者同一应用中的activitie可以分配不同的任务亲和力．
你可以使用<activity>元素的taskAffinity属性来修改一个activity的任务亲和力．taskAffinity属性使用字符串作为值，这个字符串必须与在<manifest>中声明的默认包名不同，因为系统使用包名来标识默认的任务亲和力．
亲和力在以下两种情况起作用：
•当启动一个activity的intent包含FLAG_ACTIVITY_NEW_TASK标志．
　　一个新的activity默认是在调用startActivity()的activity所在的任务中安置．然而，如果传给startActivity()的intent包含了FLAG_ACTIVITY_NEW_TASK标志，系统就会查找另一个能安置这个新activity的任务．通常，它会是一个新任务．然而但是，并不是必须这样做．如果有一个已存在的任务具有与新activity相同的亲和力，那么这个activity就被启动并安置到这个已存在的任务中．如果没有这样的任务，才开始一个新的任务．如果这个标志导致了一个activity在一个新的任务中启动然后用户按下了HOME键离开了这个新任务，那么必须有一些方法使得用户可以重新回到这个任务．一些实体(比如通知管理器)总是在一个另外的任务中启动activity而从不作为自己任务的一部分，于是它总是把FLAG_ACTIVITY_NEW_TASK设置到传给startActivity()的intents中．如果你有一个activity可以被外部实体使用这个标志调用，应小心用户可能用一个独立的方法回到这个启动的任务，比如使用启动图标（任务的根activity有一个CATEGORY_LAUNCHERintent 过滤器）．－翻译得挺难受，这句话也就是说，只要使用了相同的亲和力，用户就能回到这个已启动的任务中．
•当一个activity的allowTaskReparenting属性为"true"时．
　　在此情况下，activity可以从启动它的任务移动到一个亲和的任务中，当后一个任务来到前台时．例如，假设一个报告所选城市的天气状况的activity是作为一个旅游应用的一部分．它与同一个应用中的其它activity具有相同的亲和力(默认的application亲合力)并且它被允许重认父母．当你的一个activity启动了这个天气预报activity，它起初是与你的actvity属于同一个任务．然而，当旅游应用的任务进入前台时，天气预报activity就被重新分配到这个任务并在其只显示．
小提示:：如果一个.apk文件包含多个从用户角度所认为的"应用"，你可能想通过为activity指定属性taskAffinity来使它们连接到不同的"应用"．

## Task：清空后退栈
如果用户离开了一个任务很长一段时间，系统会清空任务中除了根activity之外的所有其它activity．当用户重新返回这个任务时，只有根activity被恢复．系统之所以这样做，是因为经过一大段时间之后，用户很可能已抛弃掉他们已经做的并且回到任务开始做一些新的事情．
有一些activity属性你可以用来改变这种行为：
•alwaysRetainTaskState
如果任务的根activity的这个属性被设置为"true"，前面所述的默认行为就不会发生．任务保持所有的后退栈中的activity，即使经过很长一段时间．
•ClearTaskOnLaunch
如果任务的根activity的这个属性被设置为"true"，在用户离开任务再回来时，栈中是清空到只剩下根activity．换句话说，它是与alwaysRetainTaskState反着来的．用户回到任务时永远见到的是初始状态，即使只离开了一小会．
•finishOnTaskLaunch
这个属性很像clearTaskOnLaunch，但是它作用于一个单独的activity，而不是整个任务．它也可以导致任何activity死亡，包含根activity．当它被置为"true"时，activity只在当前会话中存活．如果用户离开然后回来，它就已经不在了．


## 启动一个task
你可以设置一个activity为一个任务的入口，通过给它一个值为"android.intent.action.MAIN"的intent过滤器"和一个值为"android.intent.category.LAUNCHER"的过滤器．例如：
<activity... >
<intent-filter... >
<actionandroid:name="android.intent.action.MAIN" />
<categoryandroid:name="android.intent.category.LAUNCHER" />
</intent-filter>
...
</activity>
一个intent这种类型的过滤器导致activity的一个图标和标签被显示于应用启动界面上．使得用户可以启动这个activity并且再次回到这个任务．
这第二个能力是很重要的：用户必须能离开一个任务并且之后还能通过启动器回来．为此，两种使得activity永远在新任务中启动的启动模式："singleTask"和"singleInstance"，应该只在当activity具有ACTION_MAIN和CATEGORY_LAUNCHER过滤器时使用．想像一下，例如，如果没有这些过滤器将会发生什么：一个intent启动一个"singleTask"activity，在一个新的任务中初始化，并且用户在这个任务中忙乎了一些时间．然后用户按下HOME按钮．任务现在被移到后台并且不可见了．因为这个activity不是应用的启动activity，用户就再也没有办法回到这个任务了．
但遇到那些你不希望用户能够回到一个activity的情况时怎么办呢？有办法：设置<activity>元素的finishOnTaskLaunch属性为"true"!

## String 是最基本的数据类型吗?
不是。Java中的基本数据类型只有8个：byte、short、int、long、float、double、char、boolean；除了基本类型（primitive type）和枚举类型（enumeration type），剩下的都是引用类型（reference type）。
 
## short s1 = 1; s1 = s1 + 1;有错吗?short s1 = 1; s1 += 1;有错吗? 
对于short s1 = 1; s1 = s1 + 1;由于1是int类型，因此s1+1运算结果也是int 型，需要强制转换类型才能赋值给short型。
而short s1 = 1; s1 += 1;可以正确编译，因为s1+= 1;相当于s1 = (short)(s1 + 1);其中有隐含的强制类型转换。
 
## int 和Integer 有什么区别?
Java是一个近乎纯洁的面向对象编程语言，但是为了编程的方便还是引入不是对象的基本数据类型，但是为了能够将这些基本数据类型当成对象操作，Java为每一个基本数据类型都引入了对应的包装类型（wrapper class），int的包装类就是Integer，从JDK 1.5开始引入了自动装箱/拆箱机制，使得二者可以相互转换。
Java 为每个原始类型提供了包装类型：
原始类型: boolean，char，byte，short，int，long，float，double
包装类型：Boolean，Character，Byte，Short，Integer，Long，Float，Double

## 解释内存中的栈（stack）、堆(heap)和静态存储区的用法。
通常我们定义一个基本数据类型的变量，一个对象的引用，还有就是函数调用的现场保存都使用内存中的栈空间；而通过new关键字和构造器创建的对象放在堆空间；程序中的字面量（literal）如直接书写的100、“hello”和常量都是放在静态存储区中。栈空间操作最快但是也很小，通常大量的对象都是放在堆空间，整个内存包括硬盘上的虚拟内存都可以被当成堆空间来使用。
String str = new String(“hello”);
上面的语句中str放在栈上，用new创建出来的字符串对象放在堆上，而“hello”这个字面量放在静态存储区。
补充：较新版本的Java中使用了一项叫“逃逸分析“的技术，可以将一些局部对象放在栈上以提升对象的操作性能。
 (当变量（或者对象）在方法中分配后，其指针有可能被返回或者被全局引用，这样就会被其他过程或者线程所引用，这种现象称作指针（或者引用）的逃逸(Escape)。Java对象总是在堆中分配的，因此Java对象的创建和回收对系统的开销是很大的。没有发生逃逸的对象由于生命周期都在一个方法体内，因此它们是可以在运行时栈上分配并销毁。)

## swtich 是否能作用在byte 上，是否能作用在long 上，是否能作用在String上?
早期的JDK中，switch（expr）中，expr可以是byte、short、char、int。从1.5版开始，Java中引入了枚举类型（enum），expr也可以是枚举，从JDK 1.7版开始，还可以是字符串（String）。长整型（long）是不可以的。
 
## 数组有没有length()方法?String 有没有length()方法？
数组没有length()方法，有length 的属性。String 有length()方法。
  
## 构造器（constructor）是否可被重写（override）?
构造器不能被继承，因此不能被重写，但可以被重载。
 
 
## 是否可以继承String 类? 
String 类是final类，不可以被继承。
补充：继承String本身就是一个错误的行为，对String类型最好的重用方式是关联（HAS-A）而不是继承（IS-A）。
 
## 当一个对象被当作参数传递到一个方法后，此方法可改变这个对象的属性，并可返回变化后的结果，那么这里到底是值传递还是引用传递?
是值传递。Java 编程语言只有值传递参数。当一个对象实例作为一个参数被传递到方法中时，参数的值就是对该对象的引用。对象的属性可以在被调用过程中被改变，但对象的引用是永远不会改变的。C++和C#中可以通过传引用或传输出参数来改变传入的参数的值。
补充：Java中没有传引用实在是非常的不方便，这一点在Java 8中仍然没有得到改进，正是如此在Java编写的代码中才会出现大量的Wrapper类（将需要通过方法调用修改的引用置于一个Wrapper类中，再将Wrapper对象传入方法），这样的做法只会让代码变得臃肿，尤其是让从C和C++转型为Java程序员的开发者无法容忍。
 
 
## 描述一下JVM 加载class文件的原理机制?
JVM 中类的装载是由类加载器（ClassLoader） 和它的子类来实现的，Java中的类加载器是一个重要的Java 运行时系统组件，它负责在运行时查找和装入类文件中的类。
补充：
1.由于Java的跨平台性，经过编译的Java源程序并不是一个可执行程序，而是一个或多个类文件。当Java程序需要使用某个类时，JVM会确保这个类已经被加载、连接(验证、准备和解析)和初始化。类的加载是指把类的.class文件中的数据读入到内存中，通常是创建一个字节数组读入.class文件，然后产生与所加载类对应的Class对象。加载完成后，Class对象还不完整，所以此时的类还不可用。当类被加载后就进入连接阶段，这一阶段包括验证、准备(为静态变量分配内存并设置默认的初始值)和解析(将符号引用替换为直接引用)三个步骤。最后JVM对类进行初始化，包括：1如果类存在直接的父类并且这个类还没有被初始化，那么就先初始化父类；2如果类中存在初始化语句，就依次执行这些初始化语句。
2.类的加载是由类加载器完成的，类加载器包括：根加载器（BootStrap）、扩展加载器（Extension）、系统加载器（System）和用户自定义类加载器（java.lang.ClassLoader的子类）。从JDK 1.2开始，类加载过程采取了父亲委托机制(PDM)。PDM更好的保证了Java平台的安全性，在该机制中，JVM自带的Bootstrap是根加载器，其他的加载器都有且仅有一个父类加载器。类的加载首先请求父类加载器加载，父类加载器无能为力时才由其子类加载器自行加载。JVM不会向Java程序提供对Bootstrap的引用。下面是关于几个类加载器的说明：
a)Bootstrap：一般用本地代码实现，负责加载JVM基础核心类库（rt.jar）；
b)Extension：从java.ext.dirs系统属性所指定的目录中加载类库，它的父加载器是Bootstrap；
c)System：又叫应用类加载器，其父类是Extension。它是应用最广泛的类加载器。它从环境变量classpath或者系统属性java.class.path所指定的目录中记载类，是用户自定义加载器的默认父加载器。
 
## char 型变量中能不能存贮一个中文汉字?为什么?
char类型可以存储一个中文汉字，因为Java中使用的编码是Unicode（不选择任何特定的编码，直接使用字符在字符集中的编号，这是统一的唯一方法），一个char类型占2个字节（16bit），所以放一个中文是没问题的。
补充：使用Unicode意味着字符在JVM内部和外部有不同的表现形式，在JVM内部都是Unicode，当这个字符被从JVM内部转移到外部时（例如存入文件系统中），需要进行编码转换。所以Java中有字节流和字符流，以及在字符流和字节流之间进行转换的转换流，如InputStreamReader和OutputStreamReader，这两个类是字节流和字符流之间的适配器类，承担了编码转换的任务；对于C程序员来说，要完成这样的编码转换恐怕要依赖于union（联合体/共用体）共享内存的特征来实现了。
 
## 抽象类（abstract class）和接口（interface）有什么异同?
抽象类和接口都不能够实例化，但可以定义抽象类和接口类型的引用。一个类如果继承了某个抽象类或者实现了某个接口都需要对其中的抽象方法全部进行实现，否则该类仍然需要被声明为抽象类。接口比抽象类更加抽象，因为抽象类中可以定义构造器，可以有抽象方法和具体方法，而接口中不能定义构造器而且其中的方法全部都是抽象方法。抽象类中的成员可以是private、默认、protected、public的，而接口中的成员全都是public的。抽象类中可以定义成员变量，而接口中定义的成员变量实际上都是常量。有抽象方法的类必须被声明为抽象类，而抽象类未必要有抽象方法。
 
## 静态嵌套类(Static Nested Class)和内部类（Inner Class）的不同？
Static Nested Class是被声明为静态（static）的内部类，它可以不依赖于外部类实例被实例化。而通常的内部类需要在外部类实例化后才能实例化。

## Java 中会存在内存泄漏吗，请简单描述。
理论上Java因为有垃圾回收机制（GC）不会存在内存泄露问题（这也是Java被广泛使用于服务器端编程的一个重要原因）；然而在实际开发中，可能会存在无用但可达的对象，这些对象不能被GC回收也会发生内存泄露。一个例子就是Hibernate的Session（一级缓存）中的对象属于持久态，垃圾回收器是不会回收这些对象的，然而这些对象中可能存在无用的垃圾对象。下面的例子也展示了Java中发生内存泄露的情况：
import java.util.Arrays;  
import java.util.EmptyStackException;  
  
public class MyStack<T> {  
   	private T[] elements;  
    private int size = 0;  
      
    private static final int INIT_CAPACITY = 16;  
      
    public MyStack() {  
        elements = (T[]) new Object[INIT_CAPACITY];  
    }  
      
    public void push(T elem) {  
        ensureCapacity();  
        elements[size++] = elem;  
    }  
      
    public T pop() {  
        if(size == 0)   
            throw new EmptyStackException();  
        return elements[--size];  
    }  
      
    private void ensureCapacity() {  
        if(elements.length == size) {  
            elements = Arrays.copyOf(elements, 2 * size + 1);  
        }  
    }  
}  
上面的代码实现了一个栈（先进后出（FILO））结构，乍看之下似乎没有什么明显的问题，它甚至可以通过你编写的各种单元测试。然而其中的pop方法却存在内存泄露的问题，当我们用pop方法弹出栈中的对象时，该对象不会被当作垃圾回收，即使使用栈的程序不再引用这些对象，因为栈内部维护着对这些对象的过期引用（obsolete reference）。在支持垃圾回收的语言中，内存泄露是很隐蔽的，这种内存泄露其实就是无意识的对象保持。如果一个对象引用被无意识的保留起来了，那么垃圾回收器不会处理这个对象，也不会处理该对象引用的其他对象，即使这样的对象只有少数几个，也可能会导致很多的对象被排除在垃圾回收之外，从而对性能造成重大影响，极端情况下会引发Disk Paging（物理内存与硬盘的虚拟内存交换数据），甚至造成OutOfMemoryError。 
 
## 抽象的（abstract）方法是否可同时是静态的（static）,是否可同时是本地方法（native），是否可同时被synchronized修饰?
都不能。抽象方法需要子类重写，而静态的方法是无法被重写的，因此二者是矛盾的。本地方法是由本地代码（如C代码）实现的方法，而抽象方法是没有实现的，也是矛盾的。synchronized和方法的实现细节有关，抽象方法不涉及实现细节，因此也是相互矛盾的。
 
## 静态变量和实例变量的区别？
静态变量是被static修饰符修饰的变量，也称为类变量，它属于类，不属于类的任何一个对象，一个类不管创建多少个对象，静态变量在内存中有且仅有一个拷贝；实例变量必须依存于某一实例，需要先创建对象然后通过对象才能访问到它。静态变量可以实现让多个对象共享内存。在Java开发中，上下文类和工具类中通常会有大量的静态成员。
 
## 是否可以从一个静态（static）方法内部发出对非静态（non-static）方法的调用？
不可以，静态方法只能访问静态成员，因为非静态方法的调用要先创建对象，因此在调用静态方法时可能对象并没有被初始化。
 
## 如何实现对象克隆？
有两种方式：
1.实现Cloneable接口并重写Object类中的clone()方法；
2.实现Serializable接口，通过对象的序列化和反序列化实现克隆，可以实现真正的深度克隆，代码如下。
注意：基于序列化和反序列化实现的克隆不仅仅是深度克隆，更重要的是通过泛型限定，可以检查出要克隆的对象是否支持序列化，这项检查是编译器完成的，不是在运行时抛出异常，这种是方案明显优于使用Object类的clone方法克隆对象。

## GC 是什么？为什么要有GC？
GC是垃圾收集的意思，内存处理是编程人员容易出现问题的地方，忘记或者错误的内存回收会导致程序或系统的不稳定甚至崩溃，Java提供的GC功能可以自动监测对象是否超过作用域从而达到自动回收内存的目的，Java语言没有提供释放已分配内存的显示操作方法。Java程序员不用担心内存管理，因为垃圾收集器会自动进行管理。要请求垃圾收集，可以调用下面的方法之一：System.gc() 或Runtime.getRuntime().gc() ，但JVM可以屏蔽掉显式的垃圾回收调用。
垃圾回收可以有效的防止内存泄露，有效的使用可以使用的内存。垃圾回收器通常是作为一个单独的低优先级的线程运行，不可预知的情况下对内存堆中已经死亡的或者长时间没有使用的对象进行清除和回收，程序员不能实时的调用垃圾回收器对某个对象或所有对象进行垃圾回收。在Java诞生初期，垃圾回收是Java最大的亮点之一，因为服务器端的编程需要有效的防止内存泄露问题，然而时过境迁，如今Java的垃圾回收机制已经成为被诟病的东西。移动智能终端用户通常觉得iOS的系统比Android系统有更好的用户体验，其中一个深层次的原因就在于Android系统中垃圾回收的不可预知性。
补充：垃圾回收机制有很多种，包括：分代复制垃圾回收、标记垃圾回收、增量垃圾回收等方式。标准的Java进程既有栈又有堆。栈保存了原始型局部变量，堆保存了要创建的对象。Java平台对堆内存回收和再利用的基本算法被称为标记和清除，但是Java对其进行了改进，采用“分代式垃圾收集”。这种方法会根据Java对象的生命周期将堆内存划分为不同的区域，在垃圾收集过程中，可能会将对象移动到不同区域：
•	伊甸园（Eden）：这是对象最初诞生的区域，并且对大多数对象来说，这里是它们唯一存在过的区域。
•	幸存者乐园（Survivor）：从伊甸园幸存下来的对象会被挪到这里。
•	终身颐养园（Tenured）：这是足够老的幸存对象的归宿。年轻代收集（Minor-GC）过程是不会触及这个地方的。当年轻代收集不能把对象放进终身颐养园时，就会触发一次完全收集（Major-GC），这里可能还会牵扯到压缩，以便为大对象腾出足够的空间。
与垃圾回收相关的JVM参数：
•	-Xms / -Xmx --- 堆的初始大小 / 堆的最大大小
•	-Xmn --- 堆中年轻代的大小
•	-XX:-DisableExplicitGC --- 让System.gc()不产生任何作用
•	-XX:+PrintGCDetail --- 打印GC的细节
•	-XX:+PrintGCDateStamps --- 打印GC操作的时间戳
 
## String s=new String(“xyz”);创建了几个字符串对象？
两个对象，一个是静态存储区的"xyz",一个是用new创建在堆上的对象。
 
## 接口是否可继承（extends）接口? 抽象类是否可实现（implements）接口? 抽象类是否可继承具体类（concrete class）?
接口可以继承接口。抽象类可以实现(implements)接口，抽象类可继承具体类，但前提是具体类必须有明确的构造函数。
 
## 一个“.java”源文件中是否可以包含多个类（不是内部类）？有什么限制？
可以，但一个源文件中最多只能有一个公开类（public class）而且文件名必须和公开类的类名完全保持一致。
 
## Anonymous Inner Class(匿名内部类)是否可以继承其它类？是否可以实现接口？
可以继承其他类或实现其他接口，在Swing编程中常用此方式来实现事件监听和回调。
（匿名内部类也就是没有名字的内部类正因为没有名字，所以匿名内部类只能使用一次，它通常用来简化代码编写但使用匿名内部类还有个前提条件：必须继承一个父类或实现一个接口
基于一个抽象类：
abstract class Person {
    public abstract void eat();
}
或者一个接口：
interface Person {
    public void eat();
}
匿名内部类的具体实现：
...
Person p = new Person() {
	public void eat() {
  	System.out.println("eat something");
	}
};
p.eat();
）
 
## 内部类可以引用它的包含类（外部类）的成员吗？有没有什么限制？
一个内部类对象可以访问创建它的外部类对象的成员，包括私有成员。
 
## Java 中的final关键字有哪些用法？
（1）修饰类：表示该类不能被继承；
（2）修饰方法：表示方法不能被重写；
（3）修饰变量：表示变量只能一次赋值以后值不能被修改（常量）。
 
## 指出下面程序的运行结果
class A{  
    static{  
        System.out.print("1");  
    }  
  
    public A(){  
        System.out.print("2");  
    }  
}  
  
class B extends A{  
    static{  
        System.out.print("a");  
    }  
  
    public B(){  
        System.out.print("b");  
    }  
}  
  
public class Hello{  
      public static void main(String[] args){  
        A ab = new B();  
        ab = new B();  
    }  
  
}  
执行结果：1a2b2b。创建对象时构造器的调用顺序是：先初始化静态成员，然后调用父类构造器，再初始化非静态成员，最后调用自身构造器。 
 
## String与基本数据类型之间的转换
1)如何将字符串转换为基本数据类型？
2)如何将基本数据类型转换为字符串？
答：
1)调用基本数据类型对应的包装类中的方法parseXXX(String)或valueOf(String)即可返回相应基本类型；
2)一种方法是将基本数据类型与空字符串（””）连接（+）即可获得其所对应的字符串；另一种方法是调用String 类中的valueOf(…)方法返回相应字符串
 
## 怎样将GB2312编码的字符串转换为ISO-8859-1编码的字符串？
String s1 = "你好";
String s2 = new String(s1.getBytes("GB2312"), "ISO-8859-1");
 
## 日期和时间
1)如何取得年月日、小时分钟秒？
2)如何取得从1970年1月1日0时0分0秒到现在的毫秒数？
3)如何取得某月的最后一天？
4)如何格式化日期？
答：
1)创建java.util.Calendar 实例，调用其get()方法传入不同的参数即可获得参数所对应的值
2)以下方法均可获得该毫秒数:
Calendar.getInstance().getTimeInMillis();  
System.currentTimeMillis();  
3)示例代码如下: 
Calendar time = Calendar.getInstance();  
time.getActualMaximum(Calendar.DAY_OF_MONTH);  
4)利用java.text.DataFormat 的子类（如SimpleDateFormat类）中的format(Date)方法可将日期格式化。
 
## 打印昨天的当前时刻
Calendar cal = Calendar.getInstance();  
cal.add(Calendar.DATE, -1);  
System.out.println(cal.getTime());  
 
## 什么时候用assert？
assertion(断言)在软件开发中是一种常用的调试方式，很多开发语言中都支持这种机制。一般来说，assertion用于保证程序最基本、关键的正确性。assertion检查通常在开发和测试时开启。为了提高性能，在软件发布后， assertion检查通常是关闭的。在实现中，断言是一个包含布尔表达式的语句，在执行这个语句时假定该表达式为true；如果表达式计算为false，那么系统会报告一个AssertionError。
断言用于调试目的：
assert(a > 0); // throws an AssertionError if a <= 0
断言可以有两种形式：
assert Expression1;
assert Expression1 : Expression2 ;
Expression1 应该总是产生一个布尔值。
Expression2 可以是得出一个值的任意表达式；这个值用于生成显示更多调试信息的字符串消息。
断言在默认情况下是禁用的，要在编译时启用断言，需使用source 1.4 标记：
javac -source 1.4 Test.java
要在运行时启用断言，可使用-enableassertions 或者-ea 标记。
要在运行时选择禁用断言，可使用-da 或者-disableassertions 标记。
要在系统类中启用断言，可使用-esa 或者-dsa 标记。还可以在包的基础上启用或者禁用断言。可以在预计正常情况下不会到达的任何位置上放置断言。断言可以用于验证传递给私有方法的参数。不过，断言不应该用于验证传递给公有方法的参数，因为不管是否启用了断言，公有方法都必须检查其参数。不过，既可以在公有方法中，也可以在非公有方法中利用断言测试后置条件。另外，断言不应该以任何方式改变程序的状态。
   
##  try{}里有一个return语句，那么紧跟在这个try后的finally{}里的code会不会被执行，什么时候被执行，在return前还是后?
会执行，在方法返回调用者前执行。Java允许在finally中改变返回值的做法是不好的，因为如果存在finally代码块，try中的return语句不会立马返回调用者，而是记录下返回值待finally代码块执行完毕之后再向调用者返回其值，然后如果在finally中修改了返回值，这会对程序造成很大的困扰，C#中就从语法上规定不能做这样的事。
 
##  Java 语言如何进行异常处理，关键字：throws、throw、try、catch、finally分别如何使用？
Java 通过面向对象的方法进行异常处理，把各种不同的异常进行分类，并提供了良好的接口。在Java中，每个异常都是一个对象，它是Throwable 类或其子类的实例。当一个方法出现异常后便抛出一个异常对象，该对象中包含有异常信息，调用这个对象的方法可以捕获到这个异常并进行处理。Java 的异常处理是通过5 个关键词来实现的：try、catch、throw、throws和finally。一般情况下是用try来执行一段程序，如果出现异常，系统会抛出（throw）一个异常，这时候你可以通过它的类型来捕捉（catch）它，或最后（finally）由缺省处理器来处理；try用来指定一块预防所有“异常”的程序；catch 子句紧跟在try块后面，用来指定你想要捕捉的“异常”的类型；throw 语句用来明确地抛出一个“异常”；throws用来标明一个成员函数可能抛出的各种“异常”；finally 为确保一段代码不管发生什么“异常”都被执行一段代码；可以在一个成员函数调用的外面写一个try语句，在这个成员函数内部写另一个try语句保护其他代码。每当遇到一个try 语句，“异常”的框架就放到栈上面，直到所有的try语句都完成。如果下一级的try语句没有对某种“异常”进行处理，栈就会展开，直到遇到有处理这种“异常”的try 语句。
 
## 运行时异常与受检异常有何异同
异常表示程序运行过程中可能出现的非正常状态，运行时异常表示虚拟机的通常操作中可能遇到的异常，是一种常见运行错误，只要程序设计得没有问题通常就不会发生。受检异常跟程序运行的上下文环境有关，即使程序设计无误，仍然可能因使用的问题而引发。Java编译器要求方法必须声明抛出可能发生的受检异常，但是并不要求必须声明抛出未被捕获的运行时异常。异常和继承一样，是面向对象程序设计中经常被滥用的东西，神作《Effective Java》中对异常的使用给出了以下指导原则：
•	不要将异常处理用于正常的控制流（设计良好的API不应该强迫它的调用者为了正常的控制流而使用异常）
•	对可以恢复的情况使用受检异常，对编程错误使用运行时异常
•	避免不必要的使用受检异常（可以通过一些状态检测手段来避免异常的发生）
•	优先使用标准的异常
•	每个方法抛出的异常都要有文档
•	保持异常的原子性
•	不要在catch中忽略掉捕获到的异常
 
## 列出一些你常见的运行时异常
ArithmeticException（算术异常）
ClassCastException （类转换异常）
IllegalArgumentException （非法参数异常）
IndexOutOfBoundsException （下表越界异常）
NullPointerException （空指针异常）
SecurityException （安全异常）
 
## final, finally, finalize 的区别
final：修饰符（关键字）有三种用法：如果一个类被声明为final，意味着它不能再派生出新的子类，即不能被继承，因此它和abstract是反义词。将变量声明为final，可以保证它们在使用中不被改变，被声明为final 的变量必须在声明时给定初值，而在以后的引用中只能读取不可修改。被声明为final 的方法也同样只能使用，不能在子类中被重写。finally：通常放在try…catch的后面构造总是执行代码块，这就意味着程序无论正常执行还是发生异常，这里的代码只要JVM不关闭都能执行，可以将释放外部资源的代码写在finally块中。finalize：Object类中定义的方法，Java中允许使用finalize() 方法在垃圾收集器将对象从内存中清除出去之前做必要的清理工作。这个方法是由垃圾收集器在销毁对象时调用的，通过重写finalize() 方法可以整理系统资源或者执行其他清理工作。

## transient和volatile是java关键字吗
是。
如果用transient声明一个实例变量，当对象存储时，它的值不需要维持。例如： 
class T 
{   
      transient int a;  //不需要维持 
      int b;  //需要维持
}  
这里，如果T类的一个对象写入一个持久的存储区域，a的内容不被保存，但b的将被保存。 
volatile修饰符告诉编译器被volatile修饰的变量可以被程序的其他部分改变。在多线程程序中，有时两个或更多的线程共享一个相同的实例变量。考虑效率问题，每个线程可以自己保存该共享变量的私有拷贝。实际的变量副本在不同的时候更新，如当进入synchronized方法时。 

## 能说一下java的反射(reflection)机制吗 
开放性和原因连接(causally-connected)是反射系统的两大基本要素

## 如果类a继承类b，实现接口c，而类b和接口c中定义了同名变量，请问会出现什么问题？
interface	A
{
	int x = 0;
}
class B
{
	int x =1;
}
class C extends B implements A
{
	public void pX()
{
		System.out.println(x);
	}
public static void main(String[] args) {
  	new C().pX();
	}
}
在编译时会发生错误(错误描述不同的JVM有不同的信息，意思就是未明确的x调用，两个x都匹配（就象在同时import java.util和java.sql两个包时直接声明Date一样）。对于父类的变量,可以用super.x来明确(输出的是1)，而接口的属性默认隐含为 public static final.所以可以通过A.x来明确(输出的是0)。

## 如果main方法被声明为private会怎样？
能正常编译，但运行的时候会提示"main方法不是public的"。

## 如果要重写一个对象的equals方法，还要考虑什么？
hashCode

## 说明一下public static void main(String args[])这段声明里每个关键字的作用
public: main方法是Java程序运行时调用的第一个方法，因此它必须对Java环境可见。所以可见性设置为pulic.
static: Java平台调用这个方法时不会创建这个类的一个实例，因此这个方法必须声明为static。
void: main方法没有返回值。
String是命令行传进参数的类型，args是指命令行传进的字符串数组。

## ==与equals的区别
==比较两个对象在内存里是不是同一个对象，就是说在内存里的存储位置一致。两个String对象存储的值是一样的，但有可能在内存里存储在不同的地方 .
==比较的是引用而equals方法比较的是内容。public boolean equals(Object obj) 这个方法是由Object对象提供的，可以由子类进行重写。默认的实现只有当对象和自身进行比较时才会返回true,这个时候和==是等价的。String, BitSet, Date, 和File都对equals方法进行了重写，对两个String对象 而言，值相等意味着它们包含同样的字符序列。对于基本类型的包装类来说，值相等意味着对应的基本类型的值一样。

## 如果去掉了main方法的static修饰符会怎样？
程序能正常编译。运行时会抛NoSuchMethodError异常。

## Comparable和Comparator接口是干什么的？列出它们的区别。
Java提供了只包含一个compareTo()方法的Comparable接口。这个方法可以个给两个对象排序。具体来说，它返回负数，0，正数来表明输入对象小于，等于，大于已经存在的对象。
Java提供了包含compare()和equals()两个方法的Comparator接口。compare()方法用来给两个输入参数排序，返回负数，0，正数表明第一个参数是小于，等于，大于第二个参数。equals()方法需要一个对象作为参数，它用来决定输入参数是否和comparator相等。只有当输入参数也是一个comparator并且输入参数和当前comparator的排序结果是相同的时候，这个方法才返回true。

## sleep()和wait()有什么区别? 
sleep()方法是线程类（Thread）的静态方法，导致此线程暂停执行指定时间，将执行机会给其他线程，但是监控状态依然保持，到时后会自动恢复（线程回到就绪（ready）状态），因为调用sleep 不会释放对象锁(也就是说如果有synchronized同步快，其他线程仍然不能访问共享数据)。wait()是Object 类的方法，对此对象调用wait()方法导致本线程放弃对象锁(线程暂停执行)，进入等待此对象的等待锁定池，只有针对此对象发出notify 方法（或notifyAll）后本线程才进入对象锁定池准备获得对象锁进入就绪状态。
 
## sleep()和yield()有什么区别?
① sleep()方法给其他线程运行机会时不考虑线程的优先级，因此会给低优先级的线程以运行的机会；yield()方法只会给相同优先级或更高优先级的线程以运行的机会；
② 线程执行sleep()方法后转入阻塞（blocked）状态(sleep结束后恢复到ready状态，而不是直接执行)，而执行yield()方法后转入就绪（ready）状态；
③ sleep()方法声明抛出InterruptedException，而yield()方法没有声明任何异常；
④ sleep()方法比yield()方法（跟操作系统相关）具有更好的可移植性。
 
## 当一个线程进入一个对象的synchronized方法之后，其它线程是否可进入此对象的synchronized方法？
不能。其它线程只能访问该对象的非同步方法，同步方法则不能进入。
 
## 请说出与线程同步相关的方法。
1.	wait():使一个线程处于等待（阻塞）状态，并且释放所持有的对象的锁；
2.	sleep():使一个正在运行的线程处于睡眠状态，是一个静态方法，调用此方法要捕捉InterruptedException 异常；
3.	notify():唤醒一个处于等待状态的线程，当然在调用此方法的时候，并不能确切的唤醒某一个等待状态的线程，而是由JVM确定唤醒哪个线程，而且与优先级无关；
4.	notityAll():唤醒所有处入等待状态的线程，注意并不是给所有唤醒线程一个对象的锁，而是让它们竞争；
5.	JDK 1.5通过Lock接口提供了显式(explicit)的锁机制，增强了灵活性以及对线程的协调。Lock接口中定义了加锁（lock()）和解锁(unlock())的方法，同时还提供了newCondition()方法来产生用于线程之间通信的Condition对象；
6.	JDK 1.5还提供了信号量(semaphore)机制，信号量可以用来限制对某个共享资源进行访问的线程的数量。在对资源进行访问之前，线程必须得到信号量的许可（调用Semaphore对象的acquire()方法）；在完成对资源的访问后，线程必须向信号量归还许可（调用Semaphore对象的release()方法）。
 
## 编写多线程程序有几种实现方式？
Java 5以前实现多线程有两种实现方法：一种是继承Thread类；另一种是实现Runnable接口。两种方式都要通过重写run()方法来定义线程的行为，推荐使用后者，因为Java中的继承是单继承，一个类有一个父类，如果继承了Thread类就无法再继承其他类了，显然使用Runnable接口更为灵活。
补充：Java 5以后创建线程还有第三种方式：实现Callable接口，该接口中的call方法可以在线程执行结束时产生一个返回值，代码如下所示：
 
## synchronized关键字的用法？
synchronized关键字可以将对象或者方法标记为同步，以实现对对象和方法的互斥访问，可以用synchronized(对象) { … }定义同步代码块，或者在声明方法时将synchronized作为方法的修饰符。
 
## 线程的基本状态以及状态之间的关系？ 
  
![image](https://github.com/woojean/woojean.github.io/blob/master/images/java_18.png)

除去起始（new）状态和结束（finished）状态，线程有三种状态，分别是：就绪（ready）、运行（running）和阻塞（blocked）。其中就绪状态代表线程具备了运行的所有条件，只等待CPU调度（万事俱备，只欠东风）；处于运行状态的线程可能因为CPU调度（时间片用完了）的原因回到就绪状态，也有可能因为调用了线程的yield方法回到就绪状态，此时线程不会释放它占有的资源的锁，坐等CPU以继续执行；运行状态的线程可能因为I/O中断、线程休眠、调用了对象的wait方法而进入阻塞状态（有的地方也称之为等待状态）；而进入阻塞状态的线程会因为休眠结束、调用了对象的notify方法或notifyAll方法或其他线程执行结束而进入就绪状态。注意：调用wait方法会让线程进入等待池中等待被唤醒，notify方法或notifyAll方法会让等待锁中的线程从等待池进入等锁池，在没有得到对象的锁之前，线程仍然无法获得CPU的调度和执行。

## 简述synchronized 和java.util.concurrent.locks.Lock的异同？
Lock是Java 5以后引入的新的API，和关键字synchronized相比主要相同点：Lock 能完成synchronized所实现的所有功能；主要不同点：Lock 有比synchronized 更精确的线程语义和更好的性能。synchronized 会自动释放锁，而Lock 一定要求程序员手工释放，并且必须在finally 块中释放（这是释放外部资源的最好的地方）。
 
## Java中如何实现序列化，有什么意义？ 
序列化就是一种用来处理对象流的机制，所谓对象流也就是将对象的内容进行流化。可以对流化后的对象进行读写操作，也可将流化后的对象传输于网络之间。序列化是为了解决对象流读写操作时可能引发的问题（如果不进行序列化可能会存在数据乱序的问题）。
要实现序列化，需要让一个类实现Serializable接口，该接口是一个标识性接口，标注该类对象是可被序列化的，然后使用一个输出流来构造一个对象输出流并通过writeObject(Object obj)方法就可以将实现对象写出(即保存其状态)；如果需要反序列化则可以用一个输入流建立对象输入流，然后通过readObject方法从流中读取对象。序列化除了能够实现对象的持久化之外，还能够用于对象的深度克隆.

## 写一个单例类
单例模式主要作用是保证在Java应用程序中，一个类只有一个实例存在。下面给出两种不同形式的单例：
第一种形式：饿汉式单例
public class Singleton {  
private Singleton(){}  
private static Singleton instance = new Singleton();  
public static Singleton getInstance(){  
return instance;  
}  
}  

第二种形式：懒汉式单例
public class Singleton {  
private static Singleton instance = null;  
private Singleton() {}  
public static synchronized Singleton getInstance(){  
if (instance==null) instance＝newSingleton();  
return instance;  
}  
}
}  
单例的特点：外界无法通过构造器来创建对象，该类必须提供一个静态方法向外界提供该类的唯一实例。用Java进行服务器端编程时，使用单例模式的机会还是很多的，服务器上的资源都是很宝贵的，对于那些无状态的对象其实都可以单例化或者静态化（在内存中仅有唯一拷贝），如果使用了Spring这样的框架来进行对象托管，Spring的IoC容器在默认情况下对所有托管对象都是进行了单例化处理的。
 

## 面向对象的编程（设计模式）原则
（1）单一职责原则：一个类只做它该做的事情。（单一职责原则想表达的就是“高内聚”，写代码最终极的原则只有六个字“高内聚、低耦合”，就如同葵花宝典或辟邪剑谱的中心思想就八个字“欲练此功必先自宫”，所谓的高内聚就是一个代码模块只完成一项功能，在面向对象中，如果只让一个类完成它该做的事，而不涉及与它无关的领域就是践行了高内聚的原则，这个类就只有单一职责。我们都知道一句话叫“因为专注，所以专业”，一个对象如果承担太多的职责，那么注定它什么都做不好。这个世界上任何好的东西都有两个特征，一个是功能单一，好的相机绝对不是电视购物里面卖的那种一个机器有一百多种功能的，它基本上只能照相；另一个是模块化，好的自行车是组装车，从减震叉、刹车到变速器，所有的部件都是可以拆卸和重新组装的，好的乒乓球拍也不是成品拍，一定是底板和胶皮可以拆分和自行组装的，一个好的软件系统，它里面的每个功能模块也应该是可以轻易的拿到其他系统中使用的，这样才能实现软件复用的目标。）
（2）开闭原则：软件实体应当对扩展开放，对修改关闭。（在理想的状态下，当我们需要为一个软件系统增加新功能时，只需要从原来的系统派生出一些新类就可以，不需要修改原来的任何一行代码。要做到开闭有两个要点：①抽象是关键，一个系统中如果没有抽象类或接口系统就没有扩展点；②封装可变性，将系统中的各种可变因素封装到一个继承结构中，如果多个可变因素混杂在一起，系统将变得复杂而换乱，如果不清楚如何封装可变性，可以参考《设计模式精解》一书中对桥梁模式的讲解的章节。）
（3）依赖倒转原则：面向接口编程。（该原则说得直白和具体一些就是声明方法的参数类型、方法的返回类型、变量的引用类型时，尽可能使用抽象类型而不用具体类型，因为抽象类型可以被它的任何一个子类型所替代，请参考下面的里氏替换原则。）
（4）里氏替换原则：任何时候都可以用子类型替换掉父类型。（关于里氏替换原则的描述，Barbara Liskov女士的描述比这个要复杂得多，但简单的说就是能用父类型的地方就一定能使用子类型。里氏替换原则可以检查继承关系是否合理，如果一个继承关系违背了里氏替换原则，那么这个继承关系一定是错误的，需要对代码进行重构。例如让猫继承狗，或者狗继承猫，又或者让正方形继承长方形都是错误的继承关系，因为你很容易找到违反里氏替换原则的场景。需要注意的是：子类一定是增加父类的能力而不是减少父类的能力，因为子类比父类的能力更多，把能力多的对象当成能力少的对象来用当然没有任何问题。）
（5）接口隔离原则：接口要小而专，绝不能大而全。（臃肿的接口是对接口的污染，既然接口表示能力，那么一个接口只应该描述一种能力，接口也应该是高度内聚的。例如，琴棋书画就应该分别设计为四个接口，而不应设计成一个接口中的四个方法，因为如果设计成一个接口中的四个方法，那么这个接口很难用，毕竟琴棋书画四样都精通的人还是少数，而如果设计成四个接口，会几项就实现几个接口，这样的话每个接口被复用的可能性是很高的。Java中的接口代表能力、代表约定、代表角色，能否正确的使用接口一定是编程水平高低的重要标识。）
（6）合成聚合复用原则：优先使用聚合或合成关系复用代码。（通过继承来复用代码是面向对象程序设计中被滥用得最多的东西，因为所有的教科书都无一例外的对继承进行了鼓吹从而误导了初学者，类与类之间简单的说有三种关系，IS-A关系、HAS-A关系、USE-A关系，分别代表继承、关联和依赖。其中，关联关系根据其关联的强度又可以进一步划分为关联、聚合和合成，但说白了都是HAS-A关系，合成聚合复用原则想表达的是优先考虑HAS-A关系而不是IS-A关系复用代码，原因嘛可以自己从百度上找到一万个理由，需要说明的是，即使在Java的API中也有不少滥用继承的例子，例如Properties类继承了Hashtable类，Stack类继承了Vector类，这些继承明显就是错误的，更好的做法是在Properties类中放置一个Hashtable类型的成员并且将其键和值都设置为字符串来存储数据，而Stack类的设计也应该是在Stack类中放一个Vector对象来存储数据。记住：任何时候都不要继承工具类，工具是可以拥有并可以使用的（HAS/USE），而不是拿来继承的。）
（7）迪米特法则：迪米特法则又叫最少知识原则，一个对象应当对其他对象有尽可能少的了解。（迪米特法则简单的说就是如何做到“低耦合”，门面模式和调停者模式就是对迪米特法则的践行。对于门面模式可以举一个简单的例子，你去一家公司洽谈业务，你不需要了解这个公司内部是如何运作的，你甚至可以对这个公司一无所知，去的时候只需要找到公司入口处的前台美女，告诉她们你要做什么，她们会找到合适的人跟你接洽，前台的美女就是公司这个系统的门面。再复杂的系统都可以为用户提供一个简单的门面，Java Web开发中作为前端控制器的Servlet或Filter不就是一个门面吗，浏览器对服务器的运作方式一无所知，但是通过前端控制器就能够根据你的请求得到相应的服务。调停者模式也可以举一个简单的例子来说明，例如一台计算机，CPU、内存、硬盘、显卡、声卡各种设备需要相互配合才能很好的工作，但是如果这些东西都直接连接到一起，计算机的布线将异常复杂，在这种情况下，主板作为一个调停者的身份出现，它将各个设备连接在一起而不需要每个设备之间直接交换数据，这样就减小了系统的耦合度和复杂度。迪米特法则用通俗的话来将就是不要和陌生人打交道，如果真的需要，找一个自己的朋友，让他替你和陌生人打交道。）

## 在进行数据库编程时，连接池有什么作用？
由于创建连接和释放连接都有很大的开销（尤其是数据库服务器不在本地时，每次建立连接都需要进行TCP的三次握手，再加上网络延迟，造成的开销是不可忽视的），为了提升系统访问数据库的性能，可以事先创建若干连接置于连接池中，需要时直接从连接池获取，使用结束时归还连接池而不必关闭连接，从而避免频繁创建和释放连接所造成的开销，这是典型的用空间换取时间的策略（浪费了空间存储连接，但节省了创建和释放连接的时间）。池化技术在Java开发中是很常见的，在使用线程时创建线程池的道理与此相同。基于Java的开源数据库连接池主要有： C3P0、Proxool、DBCP、BoneCP、Druid等。
【补充】在计算机系统中时间和空间是不可调和的矛盾，理解这一点对设计满足性能要求的算法是至关重要的。大型网站性能优化的一个关键就是使用缓存，而缓存跟上面讲的连接池道理非常类似，也是使用空间换时间的策略。可以将热点数据置于缓存中，当用户查询这些数据时可以直接从缓存中得到，这无论如何也快过去数据库中查询。当然，缓存的置换策略等也会对系统性能产生重要影响，对于这个问题的讨论已经超出了这里要阐述的范围。
 
## 现在有T1、T2、T3三个线程，你怎样保证T2在T1执行完后执行，T3在T2执行完后执行？
这个多线程问题比较简单，可以用join方法实现。
(join是Thread类的一个方法，启动线程后直接调用，例如：
Thread t = new AThread(); 
t.start(); 
t.join();
在很多情况下，主线程生成并起动了子线程，如果子线程里要进行大量的耗时的运算，主线程往往将于子线程之前结束，但是如果主线程处理完其他的事务后，需要用到子线程的处理结果，也就是主线程需要等待子线程执行完成之后再结束，这个时候就要用到join()方法了。 即join()的作用是：“等待该线程终止”，这里需要理解的就是该线程是指的主线程等待子线程的终止。也就是在子线程调用了join()方法后面的代码，只有等到子线程结束了才能执行(效果类似于将两个线程进行了合并))

## 在Java中Lock接口比synchronized块的优势是什么？
你需要实现一个高效的缓存，它允许多个用户读，但只允许一个用户写，以此来保持它的完整性，你会怎样去实现它？
lock接口在多线程和并发编程中最大的优势是它们为读和写分别提供了锁，它能满足你写像ConcurrentHashMap这样的高性能数据结构和有条件的阻塞。Java线程面试的问题越来越会根据面试者的回答来提问。我强烈建议在你去参加多线程的面试之前认真读一下Locks，因为当前其大量用于构建电子交易终统的客户端缓存和交易连接空间。

## 为什么我们调用start()方法时会执行run()方法，为什么我们不能直接调用run()方法？
当你调用start()方法时你将创建新的线程，并且执行在run()方法里的代码。但是如果你直接调用run()方法，它不会创建新的线程也不会执行调用线程的代码。

## 什么是不可变对象(immutable object)
1.可变类和不可变类(Mutable and Immutable Objects)的初步定义：
 	可变类：当你获得这个类的一个实例引用时，你可以改变这个实例的内容。
 	不可变类：当你获得这个类的一个实例引用时，你不可以改变这个实例的内容。不可变类的实例一但创建，其内在成员变量的值就不能被修改。 
2.如何创建一个自己的不可变类：
	所有成员都是private
	不提供对成员的改变方法，例如：setXXXX
	确保所有的方法不会被重载。手段有两种：使用final Class(强不可变类)，或者将所有类方法加上final(弱不可变类)。
	如果某一个类成员不是原始变量(primitive)或者不可变类，必须通过在成员初始化(in)或者get方法(out)时通过深度clone方法，来确保类的不可变。
jdk的可变类和不可变类：
primitive变量: boolean,byte, char, double ,float, integer, long, short 
 	jdk的不可变类：jdk的java.lang包中 Boolean, Byte, Character, Double, Float, Integer, Long, Short, String. 
StringBuffer 可变类
  	java.util.Date 可变类

## Java的String类为什么要设成immutable类型？
String s = "ABC";  
s.toLowerCase(); 
如上s.toLowerCase()并没有改变“ABC“的值，而是创建了一个新的String类“abc”，然后将新的实例的指向变量s。
相对于可变对象，不可变对象有很多优势：
1).不可变对象可以提高String Pool的效率和安全性。如果你知道一个对象是不可变的，那么需要拷贝这个对象的内容时，就不用复制它的本身而只是复制它的地址，复制地址（通常一个指针的大小）需要很小的内存效率也很高。对于同时引用这个“ABC”的其他变量也不会造成影响。
2).不可变对象对于多线程是安全的，因为在多线程同时进行的情况下，一个可变对象的值很可能被其他进程改变，这样会造成不可预期的结果，而使用不可变对象就可以避免这种情况。
当然也有其他方面原因，但是Java把String设成immutable最大的原因应该是效率和安全。

## Anonymous Inner Class (匿名内部类)是否可以extends(继承)其它类，是否可以implements(实现)interface(接口)?
匿名的内部类是没有名字的内部类。不能extends(继承) 其它类，但一个内部类可以作为一个接口，由另一个内部类实现。

## Static Nested Class和Inner Class的不同
Nested Class （一般是C++的说法），Inner Class (一般是JAVA的说法)。Java内部类与C++嵌套类最大的不同就在于是否有指向外部的引用上。
　　注： 静态内部类（Inner Class）意味着
1.创建一个static内部类的对象，不需要一个外部类对象
2.不能从一个static内部类的一个对象访问一个外部类对象

•	
## 什么是CopyOnWriteArrayList，它与ArrayList有何不同？
CopyOnWriteArrayList是ArrayList的一个线程安全的变体，其中所有可变操作（add、set等等）都是通过对底层数组进行一次新的复制来实现的。相比较于ArrayList它的写操作要慢一些，因为它需要实例的快照。
CopyOnWriteArrayList中写操作需要大面积复制数组，所以性能肯定很差，但是读操作因为操作的对象和写操作不是同一个对象，读之间也不需要加锁，读和写之间的同步处理只是在写完后通过一个简单的"="将引用指向新的数组对象上来，这个几乎不需要时间，这样读操作就很快很安全，适合在多线程里使用，绝对不会发生ConcurrentModificationException ，因此CopyOnWriteArrayList适合使用在读操作远远大于写操作的场景里，比如缓存。

## Hashmap如何同步?
当我们需要一个同步的HashMap时，有两种选择：
•	使用Collections.synchronizedMap（..）来同步HashMap。
•	使用ConcurrentHashMap
这两个选项之间的首选是使用ConcurrentHashMap，这是因为我们不需要锁定整个对象，以及通过ConcurrentHashMap分区地图来获得锁。

## IdentityHashMap和HashMap的区别
IdentityHashMap是Map接口的实现。不同于HashMap的，这里采用参考平等。
•	在HashMap中如果两个元素是相等的，则key1.equals(key2)
•	在IdentityHashMap中如果两个元素是相等的，则key1 == key2

## Java的泛型是如何工作的 ? 什么是类型擦除 ? 
泛型是通过类型擦除来实现的，编译器在编译时擦除了所有类型相关的信息，所以在运行时不存在任何类型相关的信息。例如List<String>在运行时仅用一个List来表示。这样做的目的，是确保能和Java 5之前的版本开发二进制类库进行兼容。你无法在运行时访问到类型参数，因为编译器已经把泛型类型转换成了原始类型。

## 什么是泛型中的限定通配符和非限定通配符 ? 
限定通配符对类型进行了限制。有两种限定通配符，一种是<? extends T>它通过确保类型必须是T的子类来设定类型的上界，另一种是<? super T>它通过确保类型必须是T的父类来设定类型的下界。泛型类型必须用限定内的类型来进行初始化，否则会导致编译错误。另一方面<?>表示了非限定通配符，因为<?>可以用任意类型来替代。更多信息请参阅我的文章泛型中限定通配符和非限定通配符之间的区别。 

## 如何编写一个泛型方法，让它能接受泛型参数并返回泛型类型? 
编写泛型方法并不困难，你需要用泛型类型来替代原始类型，比如使用T, E or K,V等被广泛认可的类型占位符。泛型方法的例子请参阅Java集合类框架。最简单的情况下，一个泛型方法可能会像这样: 
public V put(K key, V value) {
		return cache.put(key, value);
}

## 你可以把List<String>传递给一个接受List<Object>参数的方法吗？ 
对任何一个不太熟悉泛型的人来说，这个Java泛型题目看起来令人疑惑，因为乍看起来String是一种Object，所以List<String>应当可以用在需要List<Object>的地方，但是事实并非如此。真这样做的话会导致编译错误。如果你再深一步考虑，你会发现Java这样做是有意义的，因为List<Object>可以存储任何类型的对象包括String, Integer等等，而List<String>却只能用来存储Strings。 
List<Object> objectList;
List<String> stringList;
 	objectList = stringList;  //compilation error incompatible types

## Java中List<Object>和原始类型List之间的区别? 
原始类型和带参数类型<Object>之间的主要区别是，在编译时编译器不会对原始类型进行类型安全检查，却会对带参数的类型进行检查，通过使用Object作为类型，可以告知编译器该方法可以接受任何类型的对象，比如String或Integer。这道题的考察点在于对泛型中原始类型的正确理解。它们之间的第二点区别是，你可以把任何带参数的类型传递给原始类型List，但却不能把List<String>传递给接受List<Object>的方法，因为会产生编译错误。


## Java中List<?>和List<Object>之间的区别是什么? 
这道题跟上一道题看起来很像，实质上却完全不同。List<?> 是一个未知类型的List，而List<Object>其实是任意类型的List。你可以把List<String>, List<Integer>赋值给List<?>，却不能把List<String>赋值给List<Object>。      
List<?> listOfAnyType;
List<Object> listOfObject = new ArrayList<Object>();
List<String> listOfString = new ArrayList<String>();
List<Integer> listOfInteger = new ArrayList<Integer>();
      
listOfAnyType = listOfString; //legal
listOfAnyType = listOfInteger; //legal
listOfObjectType = (List<Object>) listOfString; //compiler error - in-convertible types

## 能不能自己写个类，也叫java.lang.String?
可以，但在应用的时候，需要用自己的类加载器去加载，否则，系统的类加载器永远只是去加载jre.jar包中的那个java.lang.String。
  

## 使用命令行解释和执行Java程序
	javac Welcome.java
	java Welcome
	javac程序是Java的编译器，它把Welcome.java文件编译成Welcome.class文件。java程序是Java解释器，它负责解释执行编译器生成到class文件中的字节码。注意大小写。


## Java共有八种基本类型
Java共有八种基本类型，四种是整型，两种浮点型，一种字符型以及用于表示真假的布尔类型。
	1）整型
		long 8字节、int 4字节、short 2字节、byte 1字节；
		表示float类型数据时需要添加后缀F。没有后缀F的浮点数据总是被认为是double类型的。
		有三种特殊的浮点值：正无穷大、负无穷大、NaN（非数字）用于表示溢出和出错。
		不能用if(x == Double.NaN)的形式来判断x是否为数字，正确方法是：if( Double.isNaN(x) )。
		如果需要进行不产生舍入误差的精确数字计算，需要使用BigDecimal类。
	2）字符型
		char 2字节；
		Unicode编码表中前256个字符与ASCII码相等。
		尽管理论上可以在Java应用程序和applet中使用任意的Unicode字符，但实际上能不能看到它们	还取决于所使用的浏览器以及操作系统。
	3）布尔类型
		boolean 不能和整数相互转换，强制类型转换也不行。

## 字符串
	1）在标准Java库中包含一个名为String的预定义类。每个被双括号引起来的字符串都是String类的实例。
	2）当用+号连接一个字符串和一个非字符串时，后者将被转换成字符串。
	3）String类的对象是不可改变的，编译器把字符串设置为共享的。
	4）对于直接操作字符串的情况，Java提供了单独的StringBuffer类。 
	5）不要使用==操作符来检测两个字符串是否相等，它只能判定两个串是否存储在同一个位置。
	6）如果虚拟机总是把相等的串设为共享，那么可以使用==来测试它们是否相等。但实际上只有字符串常量会被共享，而+或substring等操作产生的结果串并不共享。


## Java控制流结构与C/C++的不同
只有两处：Java中没有goto语句，但有一个标签化版本的break。
read_data:
	while(…)
	{
		for(…)
		{
			…
			if(x)
			break read_data;
			…
		}
	}
	//break之后从这里开始执行
	if(x){…//处理错误情况}
	else{…//执行正常处理}


## 大数字
如果基本的整型和浮点型数据无法达到要求的精度，可以使用BigInteger和BigDecimal。这两个类可以操作任意长的数字。前者实现任意精度的整数运算，后者实现任意精度的浮点数运算。使用普通方法可以将普通数字转换成大数字：BigInteger a = BigInteger.valueOf(100);

## Java字符串重载
Java中无法编程实现重载操作符，Java语言设计者为字符串的连接重载了+操作符，但没有对其他操作符进行重载。


## 数组变量拷贝
可以把一个数组变量拷贝给另一个，这时两个变量都指向相同的数组。如果实际上是想将一个数组中的值拷贝给另一个变量，则需要使用System类中的arraycopy方法


## 对象变量并不包含对象，它只是指向一个对象。
在Java中，任何对象变量的值都是指向存储在别处的对象的一个引用。new运算符的返回值也是一个引用。



## Java中利用方法参数可以做到和不能做到的几种情况：
	1）方法不能修改基本类型的参数
	2）方法可以修改对象参数的状态
	3）方法不能让对象参数指向新的对象


## 显式字段初始化
可以在类的定义中简单地把值赋给任何字段，构造器执行之前，赋值会先被执行。当类的所有构造器要把某个特定实例字段赋以相同的值时，这种语法特别有用。初始值未必一定要是常量。


## 调用构造器后详细的执行过程：
	1）初始化所有数据字段为默认值
	2）按照在类声明中出现的次序依次执行所有字段初始化语句和初始化块
	3）如果构造器的第一行代码调用了另一个构造器，则执行被调用的构造器主体
	4）执行构造器主体


## 实现不用main方法就写出一个“Hello world”程序：
	public class Hello
	{
		static
		{
			System.out.println(“Hello,World”);
		}
		System.exit(0);
	}


## super不同于this
它不是一个对对象的引用（不能把super赋给另一个对象变量），而是指示编译器调用超类方法的专用关键字。


## 动态绑定
一个对象变量可以指向多种实际类型的现象被称为“多态”。而在运行时自动选择正确的方法进行调用的现象称为“动态绑定”。动态绑定有一个非常重要的特性：它使程序无需重新编译已有代码就能获得可扩展性。在Java中不需要把方法声明为虚拟的，默认情况就是动态绑定。如果不想让一个方法称为虚拟方法，可以把它标记为final。


## 按照Java语言规范要求编写equals方法的建议：
	1）显式参数命名为otherObject；
	2）测试this同otherObject是否是同一对象：
		if(this==otherObject) return true;
	3）测试otherObject是否为null，如果是，就返回false，这项测试是必须的。
		if(otherObject==null) return false;
	4）测试this和otherObject是否属于同一个类，这项测试是“对称性规则”所要求的。
		if(getClass()!=otherObject.getClass()) return false;
	5）把otherObject的类型转换成所需的类型：
		ClassName other=(ClassName)otherObject；
	6）比较所有的字段，使用==比较基本类型字段，使用equals比较对象字段。如果所有字段都匹配，则返回true，否则返回false。
	遵循以上规则定义一个类的equals方法，则在定义其子类的equals方法时，首先调用超类的equals方法。如果这项测试不能通过，对象也就不可能相等。如果超类字段相等，那么需要比较子类的实例字段。

## 对象与字符串连接
无论何时用+操作符将对象和字符串进行相连接Java编译器都会自动调用toString方法获得对象的字符串表示。

## 数组类型转换
所有的数组类型，不管它是对象数组还是基本类型数组，都是从Object派生出来的类类型。只能把对象数组转换成Object[]数组，而不能把int[]数组转换成Object[]数组，不过两种数组都可以转换成Object。

## 运行时类型识别
程序运行时，Java运行时系统一直对所有的对象进行运行时类型识别。这项信息记录了每个对象所属的类。虚拟机通常使用运行时类型信息选择正确的方法去执行。用来保存这些信息的类就是Class类。


## 获取Class类型对象的三种方法：
	1）调用Object类中的getClass方法；
	2）使用Class类的静态方法forName获得与字符串对应的Class对象：
		String className=”Manager”;
		Class c1=Class.forName(className);
	3）如果T是一个Java类型，那么T.class就代表了匹配的类对象：
		Class cl1=Manager.class;
		Class cl2=int.class;
		Class cl3=Double[].class;
	注意的是，Class对象实际上描述的只是类型，而这类型未必是类。

## newInstance()方法
可以利用newInstance()方法为类创建一个实例，如e.getClass().newInstance();创建了一个同e一个类型的新实例。newInstance方法调用默认构造器（无参数构造器）初始化新建对象。


## 使用反射分析类的功能
java.lang.reflect包中的三个类Field、Method、Constructor类分别描述类的字段、方法和构造器。它们都有一个getName方法。Field类有个getType方法，返回一个用来描述字段类型的Class对象。而Method和Constructor类都有报告这些方法的返回类型和参数类型的方法（一系列get方法）。三个类都有一个getModifiers方法，它返回一个整数，其不同位的设置描述了所使用的修饰符。可以使用Modifier类的静态方法来分析getModifiers返回的整数。
如果f是一个Field类型的对象，且obj是f字段所属类的对象，那么f.get(obj)会返回一个对象，它的值是obj中该字段的当前值。也可以通过f.set(obj,value)把obj对象的f字段设为新值。但是，只能使用get方法获取可访问字段的值。除非具有访问权限，Java安全机制允许你找出任意对象有哪些字段，但它不会允许你读取那些字段的值。（可以通过setAccessible方法屏蔽访问控制）
反射机制通过在运行时探查字段和方法，从而可以帮助写出通用性很好的程序。这项能力对系统编程来说特别有用，但它不适合于应用编程。而且反射是脆弱的，编译器不能帮助你发现编程错误，任何错误在运行时被发现都会导致异常。


## 方法指针
Method类有一个invoke方法允许调用包装在当前Method对象中的方法。方法原型：
	Object invoke(Object obj,Object[] args)
	第一个参数是隐式参数，对象数组提供了显式参数。对于静态方法来说，第一个参数会被忽略，可以设为null。如果方法没有显式参数，则可以为args参数传递一个null或一个长度为0的数组。


## 接口定义
1.接口中的任何方法都自动是public类型的，无需提供public关键字。不能在接口中放置静态方法，接口中也绝不会去实现方法。在实现接口时必须把方法声明为public。
2.接口中可以提供常量。
3.接口中决不能有实例字段。接口中的字段总是默认为public static final的，无需提供关键字说明。
4.接口不是类，不能构造接口对象，但还是可以声明接口变量。
5.也可以用instanceof来检查对象是否实现了某个接口。


## 克隆对象
使用=号拷贝一个变量时，原始值和拷贝指向同一个对象。如果需要copy成一个新对象，需要使用clone方法。clone方法只是定义在Object中的一个protect方法，它只会按照字段逐一拷贝。
如果源对象和克隆对象共享的子对象是不可改变的，则浅拷贝没有问题。有两种情况可能发生子对象不可改变的现象：子对象属于不能改变的类，如String。或者子对象在其生命周期内只保存一些常量，在其上没有更改方法，也没有方法生成对它的引用。
当子对象（即对象变量）是可变的时候，必须重新定义clone方法进行深拷贝以同时克隆子对象。
Cloneable接口是一个标记接口，跟clone方法没有关系（该方法是在Object类中定义的）。如果对象要求克隆，但没有实现这个接口，那么会产生一个已检查异常。

建立深拷贝的clone方法：
	Class Employee implements Cloneable{
		public Object clone(){
			try{
				//调用Object.clone()
				//clone方法总是返回Object，需要进行类型转换。
				Employee cloned = (Employee)super.clone();										 			cloned.hireDay=(Date)hireDay.clone();
				return cloned;
			}
			catch(ClonNotSupportedException e) { return null;}
		}
	}

## 使用内部类的原因主要有四个：
	1）内部类对象能够访问创建它的对象的实现，包括其私有数据；（内部类的对象实例含有一个隐式引用，指向那个实例化它的外部类对象。通过这个指针，内部类对象可以访问外部对象的全部状态。）
	2）内部类能够隐藏起来，不为同一包中的其他类所见；
	3）匿名内部类可以方便地定义运行时回调；
	4）使用内部类在编写事件驱动的程序时很方便

内部类对象并不是外部类的实例字段，而是外部类方法中的局部变量；
只有内部类才是私有的，普通类总是具有包可见性或者公有可见性。
局部内部类不会使用访问修饰符来指示，它们的范围总是限定在声明它们的程序块中。它们能够对外部世界完全隐藏起来，除了定义局部内部类所在的方法，没有方法知道其存在。它不仅能访问外部类中的字段，甚至还能访问局部变量，不过那些局部变量必须被声明为final的：
	public class BankAccount{
		…
		public void start(final double rate){
			class InterestAdder implements ActionListener{
				public void actionPerformed(ActionEvent event){
					double interest = balance*rate/100;
					balance+=interest;
					…
				}
			}
			ActionListener adder = new InterestAdder();
			Timer t= new Timer(1000,adder);
			t.start();
		}
	}
	为了让actionPerformed方法中的代码能够工作，InterestAdder类必然在释放start方法的局部变量rate之前给它做了一份拷贝。

## 匿名内部类的构造器
由于构造器名必须和类名相同，而匿名类没有名字，所以匿名内部类不能有构造器。取而代之的是，构造器参数被送到超类的构造器中，用于构造对象的任何参数都要放在超类型名字后的括号中，一般情况下语法如下：
	new SuperType(construction parameters)
	{
		内部类方法和数据
	}
这里，超类型可以是接口，那么内部类实现该接口；也可以是类，那么内部类扩展这个类。例如：
	Person queen = new Person(“Mary”); //一个Person对象
	Person count = new Person(“Dracula”){…}; //一个扩展了Person的内部类的对象
如果内部类实现的是接口，那么该内部类没有任何构造参数，不仅如此，还必须按照如下语法提供一组括号：
	new InterfaceType(){methods and data}


## 异常对象
一个异常对象总是Throwable子类的实例。Throwable的子类演变成两个分支：Error和Exception。Error类体系描述了Java运行系统中的内部错误以及资源耗尽的情况，应用程序不应该抛出这种类型的对象。Exception自身也演变成两个分支：一个是RuntimeException的子类，以及不从它衍生的其他异常。由编程导致的错误会导致RuntimeException异常，而其他错误原因导致的异常，例如因为IO错误导致曾经运行正确的程序出错，都不会导致RuntimeException异常。
Java语言规范将任何Error的子类以及RuntimeException的子类都称为未检查异常，而其他的异常被称为已检查异常。
一个方法不仅仅要告诉编译器它返回什么样的值，还要告诉编译器什么样的错误可能发生。方法在其方法头中声明它可能会抛出的异常，这样定义的方法头反映了该方法会抛出哪些“已检查”异常。

## 抛出异常的场景
仅仅在如下4种情况下才会抛出异常：
	1）调用了一个会抛出“已检查异常”的方法；
	2）程序运行过程中发生了错误，并且用throw语句抛出一个“已检查异常”；
	3）程序错误，如数组下标越界；
	4）Java虚拟机或者运行时库出现了内部错误；
	如果是出现头两种情况，就必须告诉那些会调用该方法的程序员：如果调用该方法，可能会抛出异常。因为任何抛出异常的方法都可能是一个死亡陷阱，如果没有合适的异常处理器来捕获这些异常，则当前线程会被终止。

如果一个方法要抛出多于一个的已检查异常，则应该在该方法头中声明所有的异常，如：
	Class MyAnimation
	{
		public Image loadImage(String s)
				throws EOFException,MalformedURLException
		{
			…
		}
	}

不需要声明Java的内部错误，也就是那些从Error继承来的错误，任何代码都可以抛出这样的异常，但是我们对其没有控制权；也不应该声明从RuntimeException继承来的那些未检查异常，因为这样的异常通常是可以避免的。
除了声明异常以外，还可以捕获异常。通过捕获异常，就可以使方法不会将异常抛出，从而不需要使用throws子句。
如果在自己的子类中覆盖了一个来自父类的方法，则子类方法所能抛出的“已检查异常”不能超过其父类方法（只可以抛出更少的异常）。如果父类方法根本没有抛出任何“已检查异常”，则子类也只能如此，此时只能在子类方法的代码中捕捉每一个“已检查异常”。
假如方法内的任何代码抛出了一个异常，同时它的类型没有在catch从句中指定，则该方法会立即退出。通常应该捕捉并处理那些已知如何处理的异常，而传递那些不知如何处理的异常。

## 在三种可能的情况下，程序会执行finally子句
	1）代码不抛出异常：执行完try块内的所有代码，随后（哪怕try块中执行了return语句）会执行finally中的代码；
	2）代码抛出的异常在catch中捕获：try块中发生异常剩下的语句将被忽略，随后执行相应catch块中的代码，然后执行finally从句的代码；如果catch块抛出了异常，则这个异常会返回到该方法的调用者；
	3）代码抛出异常，但未在任何catch块中捕获：try块内剩余代码被跳过，随后执行finally从句的代码，再将异常“抛回”该方法的调用者；
因为在方法返回前，finally子句中的语句会被执行。因此从具有return语句的try块中退出，finally子句可能会导致非预期的控制流。如果finally块中也包含return语句，则该值可能会屏蔽try块中的原始返回值。

## 流过滤器的分层
对某些流来说，可以从文件或者其他地方读入字节，而对另一些流来说，它们则可将字节组装成更有用的数据类型。Java程序员通过将一个现成的流传递给另一个流的构造器来综合运用这两种流，将其合并成所谓“过滤流”。例如为了能从文件中读取数字，首先要创建一个FileInputStream，然后将其传递给一个DataInputStream的构造器：
	FileInputStream fin=new FileInputStream(“employee.dat”);
	DataInputStream din=new DataInputStream(fin);
	double s=din.readDouble();


##  PushbackInputstream
有时候将多个中间流串连到一起时，需要对它们进行跟踪，例如在读取输入时提前检查下一个字节，看看是否是希望的值，为此可利用PushbackInputStream来实现：
	PushbackInputstream pbin=new 
				PushbackInputstream(new BufferedInputStream(new FileInputStream(“employee.dat”)));
	int b=pbin.read();
	if(b!’<’) pbin.unread(b);
	如果想在“向前看”的同时也能读入数字，就同时需要一个pushback输入流以及一个数据输入引用流：
	PushbackInputstream pbin;
	DataInputStream din=new DataInputStream(
						pbin= new PushbackInputstream(new
							 BufferedInputStream(new 
								FileInputStream(“employee.dat”))));


## 文本输出
要想以二进制格式写入数据，使用DataOutputstream；要想以文本格式写入则使用PrintWriter；但是这两个类尽管提供了有用的输出方法，却没有定义目的地，因此PrintWriter必须同目标writer合并到一起，如：
	PrintWriter out=new PrintWriter(new FileWriter(“employee.txt”));
	也可以将其同目标流合并到一起，如：
	PrintWriter out=new PrintWriter(new FileOutputStream(“employee.txt”));
	PrintWriter(OutputStream)构造器会自动增加一个OutputStreamWriter以便将Unicode字符转换成流内的字节。为了向PrintWriter进行写操作，应使用print方法或者println方法。


## 读取文本
在Java中唯一用来处理文本输入的是BufferedReader，它包含一个ReadLine方法，可用来读取整行文本，需要将一个BufferedReader同一个输入源合并起来：	
	BufferedReader in=new BufferedReader(new FileReader(“employee.txt”));
	一个典型的输入循环如下：
	String line;
	while((line=in.readline())!=null){
		…
	}

## 对象数据流
如果想在对象流中保存和恢复任何一个类，则该类必须实现Serializable接口。Serializable接口没有方法。对象序列化机制使得可以如同存储文本或者数据字段一样简单地存储对象。
保存对象数据需要使用ObjectOutputStream，如：
	ObjectOutputStream out=new ObjectOutputStream(new FileOutputStream(“employee.dat”));
	Employee harry=…;
	Manager boss=…;
	out.writeObject(harry);
	out.writeObject(boss);
读回对象需要使用ObjectInputStream对象，如：
	ObjectInputStream in=new ObjectInputStream(new FileInputStream(“employee.dat”));
	Employee e1=(Employee)in.readObject();
	Employee e2=(Employee)in.readObject();
	读回对象时必须对已保存对象的数量，它们的顺序以及它们的类型做到心中有数。

因为当一个对象被重新装载后，它可能占据一个和原来那个截然不同的内存地址，所以对于对象内部的对象引用，不能保存和恢复它们的内存地址。为此，Java采用“序列化”方式，具体算法为：
	1）保存到磁盘的所有对象都获得一个序列号；
	2）当要保存一个对象时，先检查该对象是否已经被保存；
	3）如果以前保存过，只需写入“与已保存的具有序列号x的对象相同”标记；否则，保存它的所有数据；
	当要读回对象时，将上述过程简单地逆转即可。对于载入的每个对象，都要注意它的序列号，并记住它在内存中的位置。如果遇到与已经保存的具有序列号x的对象相同标记，就根据序列号来获取该对象的位置，并设置对象引用，令其指向那个内存地址。
	当使用对象流时，这些过程都会自动完成，对象流会分配序列号，并跟踪重复的对象。

## Binder
Binder用于完成进程间通信（IPC），工作在Linux层面，属于一个驱动，只是这个驱动不需要硬件，或者说其操作的硬件是基于一小段内存。从线程的角度讲，Binder驱动代码运行在内核态，客户端程序调用Binder是通过系统调用完成的。Binder是一种架构，这种架构提供了服务端接口、Binder驱动、客户端接口三个模块。一个Binder服务端实际上就是一个Binder类的对象，该对象一旦创建就会在内部启动一个隐藏线程，该线程接下来会接收Binder驱动发送的消息，收到消息后会执行Binder对象中的onTransact()函数，并按照该函数的参数执行不同的服务代码。任意一个服务端Binder对象被创建时，同时会在Binder驱动中创建一个mRemote对象，该对象的类型也是Binder类。客户端要访问远程服务时都是通过mRemote对象，必须获取远程服务在Binder驱动中对应的mRemote引用，然后调用其transact()方法，transact()方法的内容主要包括以下几项：
	1）以线程间消息通信的模式，向服务端发送客户端传递过来的参数；
	2）挂起客户端线程，并等待服务端线程执行完指定服务函数后通知；
	3）接收到服务端线程的通知，然后继续执行客户端线程，并返回到客户端代码区；
例：
	设计Service端：
	public class MusicPlayerService extends Binder{
		@override
		protected boolean onTransact( int code, Parcel data, Parcel reply, int flags)
				throws RemoteException{
			return super.onTransact(code, data, reply, flags);
		}
		public void start( String filePath){
		}
		public void stop(){
		}
	}
	有关code标识、data变量中的参数信息等需要调用者和服务者双方有个约定。假设用正整数1000代表双方约定的要调用start()函数的值，客户端在传入的包裹data中存放的第一个数据就是filepath变量，那么onTransact()的代码可以如下所示：
	switch( code ){
		case 1000:
			data.enforceInterface(“MusicPlayerService”); 
//一种校验，与客户端的writeInterfaceToken()对应
			String filePath = data.readString();
			start(filePath);
			//replay.writeXXX(); 
//返回客户端期望的一些结果
			break;
	}
	Binder客户端设计：
	客户端可以如下调用transact()方法：
	IBinder mRemote = null;
	String filePath = “/sdcard/music/song.mp3”;
	int code = 1000;
	Parcel data = Parcel.obtain(); //包裹不是自己创建的，而是调用Parcel.obtain();申请的
	Parcel reply = Parcel.obtain();
	data.writeInterfaeToken(“MusicPlayerService”); //标注远程服务名称，理论上是不需要的
	data.writeString(filePath); //包裹中添加的内容是有序的，这个顺序必须事先约定
	mRemote.transact(code, data, reply, 0); //最后一个参数指定IPC调用模式，0表示双向，1表示单向
	IBinder binder = reply.readStrongBinder();
	reply.recycle();
	data.recycle();
	在transact()方法调用后，客户端线程进入Binder驱动，Binder驱动会挂起当前的客户端线程，并向远程服务发送一个消息，消息中包含了客户端传进来的包裹。服务端拿到包裹后会对包裹进行拆解，然后执行指定的服务函数，执行完毕后再把执行结果放入客户端提供的reply包裹中，然后服务端向Binder驱动发送一个notify，从而使得客户端线程从Binder驱动代码区返回到客户端代码区。
客户端获取服务端Binder对象的引用：
	可以仅使用Binder类扩展系统服务，但对于客户端服务则必须基于Service类来编写。AmS（Activity manager Service）提供了startService()函数用于启动客户服务，而对于客户端来讲，可以使用如下的两个函数来和一个服务建立连接，其原型在ContextImpl类中：
	1）public ComponentName startService( Intent intent );
	该函数用于启动intent指定的服务，但是启动后客户端暂时还没有获得服务端的Binder引用，因此暂时还不能调用任何服务功能。
	2）public boolean bindService( Intent service, ServiceConnection conn, int flags );
	该函数用于绑定一个服务，其中参数conn的类型信息如下：
		public interface ServiceConnection{
			public void onServiceConnected( ComponentName name, IBinder service );
			public void onServiceDisconnected( ComponetName name );
		}
		onServiceConnected()函数的第二个变量为IBinder类型，当客户端请求AmS启动某个Service后，如果该Service正常启动，那么AmS就会远程调用ActivityThread类中的ApplicationThread对象，调用的参数中会包含Service的Binder引用，然后在ApplicationThread中会回调bindService中的conn接口。因此在客户端中，可以在onServiceConnected()方法中将其参数Service保存为一个全局变量，从而在客户端的任何地方都可以随时调用该远程服务。
	客户端和服务端的事先约定：
	Android的SDK中提供了一个aidl工具，该工具可以把一个aidl文件转换为一个Java类文件，在该Java类文件中同时重载了transact()和onTransact()方法，统一了存入包裹和读取包裹参数。aidl实际就是一个脚本，该工具并不是必须的。
	系统服务中的Binder对象
	系统服务的信息可以通过getSystemService()函数获取，该函数实现在ContextImpl类中。系统服务并不是通过startService()启动的。ServiceManager是一个独立进程，管理各种系统服务，ServiceManager本身也是一个Service，Framework提供了一个静态系统函数BinderInternal.getContextObject()可以获取该Service对应的Binder引用，之后就可以通过ServiceManager提供的方法来获取其他系统Service的Binder引用（其他系统服务在启动时会首先把自己的Binder对象传递给ServiceManager，即注册）。


## Framework框架
框架中包含三个主要部分，分别为服务端、客户端和Linux驱动。
服务端主要包含两个重要类，分别是WindowManagerService（WmS）和ActivityManagerService（AmS），此外服务端还包括两个消息处理类：KeyQ类（WmS的内部类）和InputDispatcherThread类。WmS的作用是为所有的应用程序分配窗口，并管理这些窗口，包括分配窗口的大小，调节各窗口的叠放层次，隐藏或者显示窗口；AmS的作用是管理所有应用程序中的Activity；KeyQ类继承于KeyInputQueue类，一旦创建就立即启动一个线程，该线程会不断地读取用户的UI操作消息，并把这些消息放到一个消息队列QueueEvent类中；InputDispatcherThread类一旦创建也会立即启动一个线程不断地从QueueEvent中取出用户消息，并进行一定的过滤，再将这些消息发送给当前活动的客户端程序中。
客户端主要包括以下重要类：
ActivityThread类，为应用程序的主线程类，所有的APK程序都有且仅有一个该类，程序的入口为该类中的static main()函数；
Activity类，为APK程序的最小运行单元，ActivityThread主类会根据用户操作选择运行哪个Activity对象；
PhoneWindow类，继承于Window类，同时内部包含一个DecorView对象。PhoneWindow是把一个FrameLayout进行了一定的包装，并提供了一组通用的窗口操作接口；
Window类，是一个抽象类，提供了一组通用的窗口操作API，这里的窗口指一个View或者ViewGroup类，一般就是指DecorView类，即一个DecorView就是Wms所管理的一个窗口（WmS所管理的窗口并不是Window类）；
DecorView类，是FrameLayout的一个子类，并且是PhoneWindow中的一个内部类，就是对普通FrameLayout进行了一定的修饰，如添加一个通用的Title bar，并响应特定的按键消息；
ViewRoot类，继承于Handler，主要用来接收WmS的异步消息，并通知客户端进行某种操作，以此实现WmS对客户端窗口的管理；每个客户端的窗口都会对应一个ViewRoot类；
W类，ViewRoot的一个内部类，继承于Binder，用于向WmS提供一个IPC接口，从而让WmS控制窗口客户端的行为；
WindowManager类，和WmS进行交互，客户端不能直接和WmS进行交互；
Linux驱动和Framework相关的主要包含两部分：SurfaceFlingger和Binder；每一个窗口都对应一个Surface，SF驱动的作用就是把各个Surface显示在同一个屏幕上。


## APK程序的运行过程
	1）ActivityThread从main()函数中开始执行，调用prepareMainLooper()为UI线程创建一个消息队列；
	2）创建一个ActivityThread对象，在ActivityThread的初始化代码中会创建一个H（Handler）对象和一个ApplicationThread（Binder），其中Binder负责接收远程AmS的IPC调用，接收到调用后则通过Handler把消息发送到消息队列，UI主线程会异步地从消息队列中取出消息并执行相应操作，比如start、stop、pause等；
	3）UI主线程调用Looper.loop()方法进入消息循环体，进入后就会不断地从消息队列中读取并处理消息；
	4）当ActivityThread接收到AmS发送start某个Activity后，就会创建指定的Activity对象，Activity又会创建PhoneWindow类→创建DecorView类→创建相应的View或者ViewGroup，创建完成后，Activity需要把创建好的界面显示到屏幕上，于是调用WindowManager类创建一个ViewRoot对象，该对象实际上创建了ViewRoot类和W类，创建ViewRoot对象后，WindowManager再调用WmS提供的远程接口完成添加一个窗口并显示到屏幕上；
	5）用户开始在程序界面上操作，KeyQ线程不断把用户消息存储到QueueEvent队列中，InputDispatcherThread线程逐个取出消息，然后调用WmS中的相应函数处理该消息。当WmS发现该消息属于客户端某个窗口时，就会调用相应窗口的W接口；
	6）W类是一个Binder，负责接收WmS的IPC调用，并把调用消息传递给ViewRoot，ViewRoot再把消息传递给UI主线程ActivityThread，ActivityThread解析该消息并做相应的处理。在客户端程序中首先处理消息的是DecorView，如果DecorView不想处理某个消息，则可以将该消息传递给其内部包含的子View或者ViewGroup，如果还没有处理，则传递给PhoneWindow，最后再传递给Activity；
	因为每个Binder对象都对应一个线程，因此包含有Activity的客户端程序至少拥有三个线程：Activity启动后会创建一个ViewRoot.W对象，同时ActivityThread会创建一个ApplicationThread对象，这两个对象都继承于Binder，因此会启动两个线程负责接收Linux Binder驱动发送的IPC调用。最后一个主要线程也就是程序本身所在的线程，即UI（用户交互）线程。
UI线程与自定义Thread的区别在于，UI线程是从ActivityThread运行的，在该类的main()方法中已经使用Looper.prepareMainLooper()为该线程添加了Looper对象，即已经为该线程创建了消息队列，因此程序员才可以在Activity中定义Handler对象（因为声明Handler时，所在的线程必须已经创建了消息队列），而普通的自定义Thread是一个裸线程，因此不能直接在Thread中定义Handler对象。

有关Activity类之间传递数据：与普通类传递数据有所不同，普通类的实例化都是程序员显式完成的，而Activity类的实例化却是由Framework完成的，程序员只能使用startActivity()方法来告诉Framework去运行哪个Activity，也即程序员不能得到Activity对象的引用，也就不能直接访问该对象的内部数据。解决方法是使用Activity.getApplication()函数，因为同一个程序中的不同Activity调用该函数所返回的Application对象是相同的，，该对象名称可以在AndroidManifest.xml中指定。此外Framework提供的标准的Activity之间传递数据的方法是Intent类（结合startActivity和startActivityForResult）。另外也可以借助Preference Storage、文件、数据库等。


## Context相关类的继承关系
Context本身是抽象类，ContextWrapper和ContextImpl直接继承于Context类。ContextImpl真正实现了Context中的所有函数，应用程序中所调用的各种Context类的方法其实现均来自于该类。ContextWrapper的构造函数中必须包含一个真正的Context引用（ContextImpl），可以使用attachBaseContext()给ContextWrapper对象指定真正的Context对象，调用ContextWrapper的方法都会被转向其所包含的真正的Context对象。Service类直接继承自ContextWrapper，但是Activity类继承自ContextThemeWrapper，而ContextThemeWrapper则直接集成自ContextWrapper。
每一个应用程序在客户端都是从ActivityThread类开始的，创建Context对象也是在该类中完成，具体创建ContextImpl类的地方一共有7处：
	1）在PackageInfo.makeApplication()中；
	2）在performLaunchActivity()中；
	3）在handleCreateBackupAgent()中；
	4）在handleCreateService()中；
	5）在handleBindApplication()中；（有2处）
	6）在attach()方法中；该方法仅在Framework进程（system_server）启动时调用，应用程序运行时不会调用到该方法；

Application对应的Context
每个应用程序在第一次启动时都会首先创建一个Application对象，默认为应用程序的包名。程序第一次启动时会辗转调用到handleBindApplication()方法中，该方法中有两处创建了ContextImpl对象，但都是在if( data.instrumentationName!=null )条件中，如果不是测试工程的话，则调用makeApplication()方法：
	Application app = data.info.makeApplication( data...
	而在makeApplication()方法中，主要包含以下代码：
		ContextImpl appContext = new ContextImpl();
		appContext.init( this, null, mActivityThread );
			// 参数this指的就是当前PackageInfo对象，该对象将赋值给ContextImpl类中的重要成员变量mPackageInfo
		app = mActivityThread.mInstrumentation.newApp( ...appClass, appContext);
		appContext.setOuterContext( app);
		
Activity对应的Context
启动Activity时，AmS会通过IPC调用到ActivityThread的scheduleLaunchActivity()方法，该方法包含的参数中包括一个ActivityInfo类型的参数，这是一个实现了Parcelable接口的数据类，意味着该对象是AmS创建的，并通过IPC传递到ActivityThread。scheduleLaunchActivity()方法中会构造一个本地ActivityRecord数据类，ActivityThread内部会为每一个Activity创建一个ActivityRecord对象，并使用这些数据来管理Activity。接着会调用handleLaunchActivity()，然后会调用performLaunchActivity()，该方法中创建ContextImpl的代码如下：
	ContextImpl appContext = new ContextImpl();
	appContext.init( r.packageInfo, r.token, this );
	appContext.setOuterContext( activity );
	r.packageInfo对象的PackageInfo对象和Application对应的packageInfo对象是同一个。

Service对应的Context
启动Service时，AmS首先会通过IPC调用到ActivityThread的scheduleCreateService()方法，该方法也包含一个ServiceInfo类型的参数，该参数同样实现了Parcelable接口的数据类，意味着该对象由AmS创建，并通过IPC传递到ActivityThread内部。在scheduleCreateService()方法中会构造一个CreateServiceData()数据对象，并通过其来管理Service。接着会执行handleCreateService()方法，其中创建ContextImpl对象的代码如下：
	ContextImpl context = new ContextImpl();
	context.init( packageInfo, null, this );
	Application app = packageInfo.makeApplication( false,...
	context.setOuterContext( service );

Context之间的关系
可见创建Context对象的过程基本上是相同的，包括代码的结构也十分类似，所不同的仅仅是针对Application、Activity、Service使用了不同的数据对象，不同Context子类中PackageInfo对象的来源总结为：
	Application的远程数据类为ApplicationInfo，本地数据类为AppBindData，赋值方式是通过getPackageInfoNoCheck()；
	Activity的远程数据类为ActivityInfo，本地数据类为ActivityRecord，赋值方式是通过getPackageInfo ()；
Service的远程数据类为ServiceInfo，本地数据类为CreateServiceData，赋值方式是通过getPackageInfo NoCheck() ()；
由此可见一个应用程序包含的Context个数为：
	Context个数 = Service个数 + Activity个数 + 1；
因此，应用程序中包含多个ContextImpl对象，但其内部变量mPackageInfo却指向同一个PackageInfo对象，这种设计表明ContextImpl是一个轻量级类，而PackageInfo是一个重量级类，ContextImpl中的大多数重量级函数实际上都是转向了mPackageInfo对象相应的方法。


## 创建窗口的过程
从 WmS的角度来看，一个窗口并不是Window类，而是一个View类。WmS收到用户消息后，需要把消息派发到窗口（View），View类本身并不能直接接收WmS传递过来的消息，真正接收用户消息的必须是IWindow类，而实现IWindow类的是ViewRoot.W类，每一个W 内部都包含了一个View变量。WmS并不关心该窗口（View)是属于哪个应用程序的，WmS会按一定的规则判断哪个窗口处于活动状态，然后把用户消息给W类，W类再把用户消息传递给内部的View变量，剩下的消息处理就由View对象完成。

窗口类型
Framework定义了三种窗口类型，三种类型的定义在WindowManager类中。
1）应用窗口。该窗口对应一个Activity，由于加载Activity是由AmS完成的，因此，对于应用程序来讲，要创建一个应用类窗口，只能在 Activity内部完成。
2）子窗口。该窗口必须有一个父窗口，父窗口可以是一个应用类型窗口，也可以是任何其他类型的窗口。
3）系统窗口。系统窗口不需要对应任何Activity，也不需要有父窗口。对于应用程序而言，理论上是无法创建系统窗口的，因为所有的应用程序都没有这个权限，然而系统进程却可以创建系统窗口。
WindowManager类对这三种类型进行了细化，把每一种类型都用一个int常量表示，这些实际上代表了窗口对应的层（Layer)。WmS在进行窗口叠加时，会按照该int常量的大小分配不同层，int值越大，代表层的位置越靠上面，即所谓的z-order。层值在WmS进行窗口叠加时会动态改变。应用型窗口的层值为1~99，子窗口的层值为1000~1999，系统窗口的层值为2000~2999。


## 创建应用窗口的过程
	1）每个应用类窗口都对应一个Activity对象，因此，创建应用类窗口首先需要创建一个Activity对象。当AmS决定启动某个Activity时，会通知客户端进程，而每个客户端进程都对应一个ActivityThread类，任何Activity都必须隶属于一个应用程序，因此，启动Activity的任务最终由ActivityThread完成。启动某个Activity的代码本质是构造一个Activity对象：
	Activity activity = null;
try{
java.lang.ClassLoader cl = r.packageInfo.getClassloader();
activity = mInstrumentation.newActivity( cl, component.gelClassName(), r.intent );
r.intent.setExtrasClassLoader(cl);
if(r.state!=null){
r.state.setClassLoader(cl);
}
...
	以上代码使用ClassLoader从程序文件中装载指定的Activity对应的Class文件。
	2）构造好指定的Acitvity对象后，接着调用Activity的attach()方法，其代码如下：
	activity.attach( appContext, this, getInstrumentation(), r.token, r.ident, app, r.intent, r.activityInfo, title, r.parent, r.embededID, r.lastNonConfigurationInstance, r.lastNonConfigurationChildInstances, config);
	attach()的作用是为刚刚构造好的Activity设置内部变量，这些变量是以后进行Activity调度所必需的。其中：
	appContext将作为Activity的BaseContext，该对象使用new ContextImpl()方法创建；
	this就是指当前ActivityThread对象；
	r.token，r是一个ActivityRecord对象，其内部变量token的含义是AmS中的一个HistroyRecord对象；
	r.parent，一个Activity可以有一个父Activity，这种理念是为了允许把一个Activity嵌入到另一个Activity内部执行，在应用程序使用时，常用ActivityGroup类，而ActivityGroup功能的内部支持的正是该变量；
	3）在attach()方法内部，除了进行重要变量赋值外，另一件重要的事就是为该Activity创建Window对象，这通过调用PolicyManager的静态方法makeNewWindow()完成；PolicyManager会根据com.android.internal.policy.impl.Policy的配置创建不同产品类型的窗口。其代码最终实现的是创建了一个PhoneWindow对象。当创建好Window对象后，将其赋值给Activity的内部变量mWindow，并设置该Window的Callback接口为当前的Activity对象，这也是为什么用户消息能够传递到Activity中的原因：
	final void attach( Context context,...){
		attachBaseContext( context );
		mWindow = PolicyManager.makeNewWindow( this );
		mWindow.setCallback( this );
	4)创建好Window对象后，需要给Window对象中的mWindowManager变量赋值，该变量的类型为WindowManager类。事实上WindowManager类是一个接口，真正实现该接口的有两个类，一是Window.LocalWindowManager子类，另一个是WindowManagerImpl类。LocalWindowManager仅仅是一个壳，本身虽然也提供了WindowManager接口的全部功能，但是真正实现这些功能的确实壳里的WindowManager对象，这就是WindowManagerImpl类。
	mWindow.setWindowManager( null, mToken, mComponent...
	if( mParent != null ){
		mWindow.setContainer( mParent.getWindow() );
	}
	mWindowManager = mWindow.getWindowManager();
	setWindowManager()方法的第一个参数为null，而在Window类该方法实现中，如果第一个参数为null，其内部就会创建一个LocalWindowManager对象，第二个参数正是AmS中Activity对应的HistroyRecord的Binder引用，该变量将作为Window中的mAppToken的值。
	5）配置好了Activity和Window对象后，接下来就需要给该窗口中添加真正的显示元素View或者ViewGroup。这是从performLaunchActivity()内部调用callActivityOnCreate()开始的，并会辗转调用到Activity的onCreate()方法中。
	其实在Activity的onCreate()方法中调用setContentView()方法为其添加界面方式，该方法实际上却又调用了其所对应的Window对象的setContentView()方法：
	public void setContentView( int layoutResID){
		getWindow().setContentView( layoutResID );
	}
	6）PhoneWinow的setContentView()方法如下所示：
	public void setContentView( int layoutResID ){
		if( mContentParent == null ){
			installDecor(); //安装一个窗口修饰，窗口修饰就是界面上常见的标题栏。程序中指定的layout.xml界面将被包含在窗口修饰中，称为窗口内容。窗口修饰也是一个ViewGroup，窗口修饰及其内部的窗口内容加起来就是通常所说的窗口，或者叫做Window的界面。
		}
		else{
			mContentParent.removeAllViews();
		}
		mLayoutInflater.inflate( layoutResID, mContentParent ); //安装完窗口修饰后，就可以把用户界面layout.xml文件添加到窗口修饰中。
		final Callback cb = getCallback();
		if( cb!=null ){
			cb.onContentChanged(); //回调，通知应用程序窗口内容发生了改变。cb正是Activity自身，因为Activity实现了Window.CallBack接口，并且在attach()方法中将自身作为Window对象的Callback接口实现。
		}
	Framework中定义了多种窗口修饰，installDecor()代码如下：
	private void installDecor(){
		if( mDecor == null ){
			mDecor = generateDecor(); //创建一个DecorView对象，该变量并不完全等同于窗口修饰，窗口修饰是mDecor内部的唯一一个子视图
			mDecor.setDescendantFocusability( 
ViewGroup.FOCUS_AFTER_DESCENDANTS);
			mDecor.setIsRootNamespace( true );
		}
		if( mContentParent == null ){
			mContentParent = generateLayout( mDecor ); // 在generateLayout()方法中会调用mDecor..addView()根据用户指定的参数选择不同的窗口修饰，并把该窗口修饰作为mDector的子窗口。此外generateLayout()方法中返回的值的获取方式为：ViewGroup contentParent = (ViewGroup)findViewById( ID_ANDROID_CONTENT )，ID_ANDROID_CONTENT正是id=content的FrameLayout
		说明：generateLayout()方法内部所谓“根据用户指定的参数”中“用户指定”有两个地方可以指定：一是在Activity的onCreate()方法中调用得到当前Window，然后调用requestFeature()指定，另一个是在AndroidManifest.xml中Activity元素内部使用android:theme=”xxx”指定，generateLayout()方法中使用getWindowStyle()方法获取这些值。
		}
	不同的窗口修饰的区别不大，比如是否有标题栏，是否显示左右进度条等，这些修饰窗口共同的特点是其内部必须包含一个id=content的FrameLayout，因为内容窗口正是被包含在该FrameLayout之中。常见的窗口修饰对应的XML文件存放在路径frameworks/base/core /res/res/layout中，如R.layout.dialog_title_icons、R.layout.screen_title_icons、R.layout.   screen_progress等等。
	7）给Window类设置完其视图元素后，需要把创建的这个窗口告诉给WmS来把该窗口显示在屏幕上。当Activity准备好后会通知AmS，然后AmS经过各种条件的判断，并最终调用到Activity的makeVisible()方法，该方法及后续的各种调用将完成真正的把窗口添加进WmS之中，其代码如下：
	void  makeVisible(){
		if( !mWindowAdded ){
			ViewManager wm = getWindowManager();
			wm.addView( mDecor, getWindow().getAttributes());
			mWindowAdded = true;
		}
		mDecor.setVisibility(View.VISIBLE);
	}
8）在makeVisible()方法中，首先获得该Activity内部的WindowManager对象，这实际上就是Window.LocalWindowManager对象（而不是WindowManagerImpl类的addView()方法），然后调用addView()。第一个参数mDecor是一个DecorView对象，也就是用户所能看得到的、一个Activity对应的全部界面内容；第二个参数是在构造Window对象时默认构造的WindowManager.LayoutParams对象，由其构造函数中可以看到在默认情况下窗口参数的类型是一个TYPE_APPLICATION类型，即应用程序类型的窗口。
	addView()的重要代码如下：
	if( wp.type >= WindowManager.LayoutParams.FIRST_SUB_WINDOW &&
		wp.type <= WindowManager.LayoutParams.LAST_SUB_WINDOW
		if( wp.token == null ){
			View decor = peekDecorView();
			if ( decor !=null ){
				wp.token = decor.getWindowToken();
			}
		}
	如果添加的是子窗口，就会检查params中的token，如果token为空，则把Activity对应的窗口的token赋值给params的token。如果添加的不是子窗口，则把mAppToken赋值给params的token。如果该Activity被某个Activity包含，则把父Activity的mAppToken赋值给params的token，如下：
	if( wp.token == null ){
		wp.token = mContainer == null ? mAppToke ：mContainer..mAppToken;
	}
	9）过了LocalWindowManager的addView()关卡之后，即调用WindowManagerImpl的addView()方法，一个应用程序内部无论有多少个Activity，但只有一个WindowManagerImpl对象，在WindowManagerImpl类中维护三个数组用于保存该应用程序中所拥有的窗口状态：
	View[] mViews，这里每一个View对象都将成为WmS所认为的一个窗口；
	ViewRoot[] mRoots，所有的ViewRoot对象，mViews中每个View对象都对应的ViewRoot对象；
	WindowManager.LayoutParams[] mParams，当把mViews中的View对象当做一个窗口添加进WmS中，WmS要求每个被添加的窗口都要对应一个LayoutParams对象，mParams正是保存了每一个窗口对应的参数对象；
	addView()的执行流程如下：
	①检查所添加的窗口是否已经添加过；
	②如果所添加的窗口为子窗口类型，找到其父窗口，并保存在内部临时变量panelParentView中，该变量将作为后面调用ViewRoot的setView()的参数；
	③创建一个新的ViewRoot，因为每一个窗口都对应一个ViewRoot对象；
	④调用ViewRoot的setView()方法，完成最后的、真正意义上的添加工作；
	10）把新建的ViewRoot对象添加到mRoots对象中，添加的逻辑是：新建三个长度都加1的数组，然后把原来三个数组的内容都复制到新建数组，并把新创建的View、ViewRoot及WindowManager.LayoutParams对象保存到三个数组的最后：
	11）调用ViewRoot对象的setView方法完成最后的窗口添加工作。方法的三个参数意义如下：
	View view，是WindowManagerImpl中mViews数组的一个元素，也就是新建的窗口界面；
	WindowManager.LayoutParams attrs，即为添加窗口的参数，该参数描述窗口的呈现风格、大小、位置等，尤其是其内部变量token，指明了该窗口和相关Activity的关系；
	View panelParent View，也是WindowManagerImpl中mViews数组的一个元素，仅当该窗口有父窗口时才有意义；
	setView()的执行流程如下：
	①给ViewRoot的重要变量赋值，包括mView、mWindowAttributes及mAttachInfo：
		mView  = view;
		mWindowAttributes.copyFrom(attrs);
		mSoftInputMode = attrs.softInputMode;
		mWindowAttributesChanged = true;
		mAttachInfo.mRootView = view;
		...
		if( panelParentView != null ){
			mAttachInfo.mPanelParentWindowToken = 
					panelParentView.getApplicationWindowToken();
		}
	②调用requestLayout()，发出界面重绘请求。该方法仅仅是发出一个异步消息，以便UI线程下一个消息处理是界面重绘，从而让该窗口在响应任何其他用户消息之前首先变得可见；
	③调用sWindowSession.add()，通知WmS添加窗口：
		res = sWindowSession.add( mWindow, mWindowAttributes, getHostVisibility(), mAttachInfo.mContentInsets);
	sWindowSession是ViewRoot中的一个静态变量，每一个应用程序仅有一个sWindowSession对象，该对象类型为I WindowSession，即为一个Binder引用，该引用对应WmS中Session子类，WmS为每一个应用程序分配一个Session对象。
	只要能够获得sWindowSession的引用就可以任意创建窗口，而无需经过以上冗长的步骤，然而sWindowSession这个变量的访问权限为包内访问，其定义未添加任何权限修饰符：
	static IWindowSession sWindowSession;
	因此应用程序无法直接获取该对象。sWindowSession变量是在ViewRoot的构造函数中通过调用getWindowSession()方法获取的，而getWindowSession()方法内部是通过IWindowManager.Stub.asInterface()来给sWindowSession赋值的，asInterface()的参数实际上是一个WindowManager对象，但是IWindowManager类在源码中是@hide的，这意味着SDK中将不包含该类，因此试图在不修改Framework代码的情况下不经过ViewRoot类添加窗口是行不通的。
		
