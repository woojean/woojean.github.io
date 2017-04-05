# UncaughtExceptionHandler

将main()方法的主体放到try-catch语句块中并不能捕获在其中创建的新线程所抛出的异常。
可以在Thread对象上设置一个UncaughtExceptionHandler，它的uncaughException()方法会在线程因未捕获的异常而临近死亡时被调用。

```
class MyUncaughtExceptionHandler implements
Thread.UncaughtExceptionHandler {
  public void uncaughtException(Thread t, Throwable e) {
    System.out.println("caught " + e);
  }
}

...

Thread t = new Thread(r);
t.setUncaughtExceptionHandler(new MyUncaughtExceptionHandler());
```