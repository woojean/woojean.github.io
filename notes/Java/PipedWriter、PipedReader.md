# PipedWriter、PipedReader

管道基本上是一个阻塞队列，存在于引入BlockingQueue之前的Java版本中。

```java
// 一个写入任务
class Sender implements Runnable{
  public void run(){
    PipedWriter out = new PipedWriter();
    out.write(c);
    ...
  }
  ...
}

// 一个读入任务
class Receiver implements Runnable{
  ...
  PipedReader in = new PipedReader(sender.getPipedWriter());  // 连接管道
  char c =（char）in.read();
}

// 执行
Sender sender = new Sender();
Receiver reveiver = new Receiver(sender);
exec.execute(sender);
exec.execute(receiver);
```