# sendMessage与obtainMessage(sendToTarget)比较

```java
//Message msg = new Message()
Message msg = handler.obtainMessage(); msg.what = xxx; msg.arg1  = xxx; msg.arg2  = xxx; msg.obj    = xxx;
```
通过obtainMessage最终得到的Message 已经不是自己创建的了,而是从MessagePool拿的,省去了创建对象申请内存的开销。尽量使用 Message msg = handler.obtainMessage();的形式创建Message，不要自己New Message 。至于message产生之后你使用obtainMessage 或者是 sendMessage 效率影响并不大。同时我们也要注意以后谈论性能的时候要找准位置,譬如这里性能的问题不是在调用 obtainMessage 和 sendMessage 的方法上,而是调用他们之前对象的创建问题上。