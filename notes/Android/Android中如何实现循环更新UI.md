# Android中如何实现循环更新UI

```java
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