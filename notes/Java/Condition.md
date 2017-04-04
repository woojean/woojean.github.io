# Condition

可以通过在Condition对象上调用await()来挂起一个任务，并通过signal()或signalAll()来唤醒任务，与使用notifyAll()相比，signalAll()是更安全的方式。

```
Lock lock = new ReentrantLock();
Condition condition = lock.newCondition();
...
condition.await();
...
condition.signalAll();
```
