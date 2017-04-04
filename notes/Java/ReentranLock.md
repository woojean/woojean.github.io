# ReentranLock

可以使用ReentranLock实现`尝试获取锁`。

```java
private ReentrantLock lock = new ReentrantLock();
boolean captured = false;
try {
  // 尝试2秒
  captured = lock.tryLock(2, TimeUnit.SECONDS);
} catch(InterruptedException e) {
  throw new RuntimeException(e);
}
try {
  System.out.println("tryLock(2, TimeUnit.SECONDS): " + captured);
} finally {
  if(captured){
    lock.unlock();
  }
}
```
在ReentrantLock上阻塞的任务具备可以被中断的能力。