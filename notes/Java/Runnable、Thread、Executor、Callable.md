# Runnable、Thread、Executor、Callable

## Runnable
```java
// 定义任务
public class LiftOff implements Runnable {
  protected int countDown = 10;  // Default
  private static int taskCount = 0;
  private final int id = taskCount++;
  public LiftOff() {}
  public LiftOff(int countDown) {
    this.countDown = countDown;
  }
  public String status() {
    return "#" + id + "(" +
      (countDown > 0 ? countDown : "Liftoff!") + "), ";
  }
  public void run() {
    while(countDown-- > 0) {
      System.out.print(status());
      Thread.yield();  // 对线程调度器的建议，即可以切换给其他任务执行一段时间
    }
  }
}

// 在主线程中执行任务
public static void main(String[] args) {
  LiftOff launch = new LiftOff();
  launch.run();
}
```

## Thread
// 使用新线程执行任务
```java
public class BasicThreads {
  public static void main(String[] args) {
    Thread t = new Thread(new LiftOff());
    t.start();
    System.out.println("Waiting for LiftOff");
  }
}
```
每个Thread都会注册它自己（即会保存一个对它的引用），所以在调用start()之后，任务完成之前，不会被垃圾回收器回收。

## Executor
Executor用来管理Thread对象从而简化并发编程，是启动任务的首选方式。
```java
ExecutorService exec = Executors.newCachedThreadPool();
for(int i = 0; i < 5; i++){
  exec.execute(new LiftOff());
}
exec.shutdown();  // 防止新的任务被提交给这个Executor
```
CachedThreadPool()会为每一个任务创建一个新的线程，而使用FixedThreadPool()则会使用有限的线程集来执行所提交的任务，好处在于可以预先执行代价高昂的线程分配：
```
// 创建大小为5的线程池
ExecutorService exec = Executors.newFixedThreadPool(5);
```
在任何线程池中，现有的线程在可能的情况下都会被自动复用。

SingleThreadExecutor只有一个线程，如果向它提交了多个任务，那么这些任务将排队执行（SingleThreadExecutor会序列化所有提交的任务，并维护它们的悬挂任务队列）。


## Callable
使用Callable接口定义的任务可以在任务完成时得到一个返回值（Runnable不返回任何值）。
```java
// Callable的类型参数表示从call()方法中返回的返回值的类型
class TaskWithResult implements Callable<String> {
  private int id;
  public TaskWithResult(int id) {
    this.id = id;
  }
  public String call() {
    return "result of TaskWithResult " + id;
  }
}

public class CallableDemo {
  public static void main(String[] args) {
    ExecutorService exec = Executors.newCachedThreadPool();
    ArrayList<Future<String>> results = new ArrayList<Future<String>>();
    for(int i = 0; i < 10; i++){
      // submit()将返回一个Future对象，它用Callable的返回结果进行了参数化
      results.add(exec.submit(new TaskWithResult(i)));
    }
    // 打印返回值
    for(Future<String> fs : results){
      try {
        // get() blocks until completion:
        System.out.println(fs.get());  // get()将阻塞
      } catch(InterruptedException e) {
        System.out.println(e);
        return;
      } catch(ExecutionException e) {
        System.out.println(e);
      } finally {
        exec.shutdown();
      }
    }
  }
}
```

## sleep()
```java
TimeUtil.MILLISECONDS.sleep(100);
```
对sleep()的调用会抛出InterruptedException异常，因为异常不能跨线程传播，所以必须在run()中捕获。