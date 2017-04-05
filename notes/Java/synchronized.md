# synchronized

如果某个任务处于一个被标记为synchronized的方法的调用中，那么在这个线程从该方法返回之前，其他所有要调用类中任何标记为synchronized方法的线程都会被阻塞。所以，对于某个特定对象来说，其所有synchronized方法共享同一个锁。

```java
public class SynchronizedEvenGenerator extends IntGenerator {
  private int currentEvenValue = 0;
  public synchronized int next() {
    ++currentEvenValue;
    Thread.yield(); // Cause failure faster
    ++currentEvenValue;
    return currentEvenValue;
  }
}
```

将域设置为private非常重要，否则synchronized关键字就不能防止其他任务直接访问域（从而产生冲突）。

`一个任务可以多次获得对象的锁`（一个方法在同一个对象上调用了第二个方法）：只有首先获得了锁的任务才能继续获得多个锁，JVM负责跟踪对象被加锁的次数：
```java
public class MultiLock {
  public synchronized void f1(int count) {
    if(count-- > 0) {
      print("f1() calling f2() with count " + count);
      f2(count);
    }
  }
  public synchronized void f2(int count) {
    if(count-- > 0) {
      print("f2() calling f1() with count " + count);
      f1(count);
    }
  }
  public static void main(String[] args) throws Exception {
    final MultiLock multiLock = new MultiLock();
    new Thread() {
      public void run() {
        multiLock.f1(10);
      }
    }.start();
  }
}
```

针对每个类也有一个锁（属于Class对象的一部分），synchronized static方法可以在类的范围内防止对static数据的并发访问。