# wait()与notifyAll()

wait()提供了一种在任务间对活动进行同步的方式，会将任务挂起，然后在notify()或notifyAll()发生时被唤醒并去检查所发生的变化。

当调用sleep()或yield()的时候，锁并没有被释放，而调用wait()时线程的执行将被挂起，对象上的锁会被释放。

wait()、notify()、notifyAll()都是Object类的一部分，而不是Thread的一部分。`只能在同步控制块或者同步控制方法里调用它们`。如果在非同步控制方法里调用，程序可以编译通过，但是在运行时将抛出IllegalMonitorStateException。（即这些方法能够被调用的前提是拥有对象的锁）

如果要向一个对象发送notifyAll()，必须在能够取得该对象的锁的同步控制块中这么做：
```
synchronized(x){
  x.notifyAll();
}
```