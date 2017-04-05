# 4种Activity的启动模式

Activity的启动模式有4种，分别为standard、singleTop、singleTask、singleInstance，在Manifest中定义：

```xml
<activity
  android:name=".A1"
  android:launchMode="standard" />
```
(1)standard：每次激活Activity时(startActivity)，都创建Activity实例，并放入任务栈；

![image](https://github.com/woojean/woojean.github.io/blob/master/images/java_6.png)
(2)singleTop：如果某个Activity自己激活自己，即任务栈栈顶就是该Activity，则不需要创建，其余情况都要创建Activity实例；

![image](https://github.com/woojean/woojean.github.io/blob/master/images/java_7.png)
(3)singleTask：如果要激活的那个Activity在任务栈中存在该实例，则不需要创建，只需要把此Activity放入栈顶，并把该Activity以上的Activity实例都pop；（可以用来退出整个应用：将主Activity设为SingTask模式，然后在要退出的Activity中转到主Activity，然后重写主Activity的onNewIntent函数，并在函数中加上一句finish）
（launchMode为singleTask的时候，通过Intent启到一个Activity,如果系统已经存在一个实例，系统就会将请求发送到这个实例上，但这个时候，系统就不会再调用通常情况下我们处理请求数据的onCreate方法，而是调用onNewIntent方法。
但是系统可能会随时杀掉后台运行的Activity，如果这一切发生，那么系统就会调用onCreate方法，而不调用onNewIntent方法，一个好的解决方法就是在onCreate和onNewIntent方法中调用同一个处理数据的方法）

![image](https://github.com/woojean/woojean.github.io/blob/master/images/java_8.png)
(4)singleInstance：如果应用1的任务栈中创建了MainActivity实例，如果应用2也要激活MainActivity，则不需要创建，两应用共享该Activity实例；（跨应用的场景）

![image](https://github.com/woojean/woojean.github.io/blob/master/images/java_9.png)