# Handler机制的原理

andriod提供了Handler 和Looper 来满足线程间的通信。Handler 先进先出原则。Looper类用来管理特定线程内对象之间的消息交换(Message Exchange)。
1)Looper: 一个线程可以产生一个Looper对象，由它来管理此线程里的Message Queue(消息队列)。
2)Handler: 你可以构造Handler对象来与Looper沟通，以便push新消息到Message Queue里;或者接收Looper从Message Queue取出)所送来的消息。
3)Message Queue(消息队列):用来存放线程放入的消息。
4)线程：UI thread 通常就是main thread，而Android启动程序时会替它建立一个Message Queue。
Android系统的消息队列和消息循环都是针对具体线程的，一个线程可以存在（当然也可以不存在）一个消息队列和一个消息循环（Looper），特定线程的消息只能分发给本线程，不能进行跨线程，跨进程通讯。但是创建的工作线程默认是没有消息循环和消息队列的，如果想让该 线程具有消息队列和消息循环，需要在线程中首先调用Looper.prepare()来创建消息队列，然后调用Looper.loop()进入消息循环。

```java
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
```java
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