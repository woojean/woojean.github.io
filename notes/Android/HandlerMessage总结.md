# HandlerMessage总结

当应用程序启动时，会开启一个主线程（也就是UI线程），由她来管理UI，监听用户点击，来响应用户并分发事件等。所以一般在主线程中不要执行比较耗时的操作，如联网下载数据等，否则出现ANR错误。所以就将这些操作放在子线程中，但是由于Android UI线程是不安全的，所以只能在主线程中更新UI。Handler就是用于子线程和创建Handler的线程进行通信的。（线程安全就是多线程访问时，采用了加锁机制，当一个线程访问该类的某个数据时，进行保护，其他线程不能进行访问直到该线程读取完，其他线程才可使用。不会出现数据不一致或者数据污染。

        线程不安全就是不提供数据访问保护，有可能出现多个线程先后更改数据造成所得到的数据是脏数据）
Handler的使用分为两部分：一部分是创建Handler实例，重载handleMessage方法，来处理消息。
```java
mProgressHandler = new Handler()
        {
            public void handleMessage(Message msg)
            {
                super.handleMessage(msg);
            }
        };
```
也可继承自Handler，同样要实现handleMessage(Message msg)方法：
```java
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