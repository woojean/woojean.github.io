# Handler和ProgressBar实现进度条的开始，暂停，停止，后退和循环 

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
```java
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