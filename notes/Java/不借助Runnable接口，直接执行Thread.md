# 不借助Runnable接口，直接执行Thread

```java
public class SimpleThread extends Thread {
  private int countDown = 5;
  private static int threadCount = 0;
  public SimpleThread() {
    // Store the thread name:
    super(Integer.toString(++threadCount));
    start();  // 直接启动
  }
  public String toString() {
    return "#" + getName() + "(" + countDown + "), ";
  }
  public void run() {
    while(true) {
      System.out.print(this);
      if(--countDown == 0)
        return;
    }
  }
  public static void main(String[] args) {
    for(int i = 0; i < 5; i++)
      new SimpleThread();   // 直接启动
  }
}
```
在构造器中启动线程可能会有问题：另一个任务可能会在构造器结束之前开始执行，这意味着该任务能够访问处于不稳定状态的对象，这是优选Executor而不是显式地创建Thread对象的另一个原因。