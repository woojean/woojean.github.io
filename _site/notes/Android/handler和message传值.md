# handler和message传值

```java
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