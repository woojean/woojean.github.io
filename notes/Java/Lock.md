# Lock

Lock对象必须被显式地创建、锁定和释放。

```java
public class MutexEvenGenerator extends IntGenerator {
  private int currentEvenValue = 0;
  private Lock lock = new ReentrantLock();  // 创建Lock对象
  public int next() {
    lock.lock();   // 上锁
    try {
      ++currentEvenValue;
      Thread.yield(); // Cause failure faster
      ++currentEvenValue;
      return currentEvenValue;
    } finally {
      lock.unlock();  // 解锁
    }
  }
}
```
注意return语句的顺序，必须在try子句中，这样才能确保unlock()不会过早发生从而将数据暴露给第二个任务。
在使用synchronized时，如果发生失败，就会抛出一个异常，但是没有机会去做清理工作以维护系统使其处于良好状态。使用Lock可以在finall子句中将系统维护在正确的状态。