# Fragment的保留

调用setRetainInstance(true)。
保留Fragment利用了这样一个事实：可销毁和重建fragment的视图而无需销毁fragment自身。
fragment的restainInstance属性默认为false，表明其不会被保留。设置为true后可保留fragment，已保留的fragment不会随activity一起被销毁（如旋转设备），其全部实例变量值也将保持不变。当新的activity创建后，新的FragmentManager会找到被保留的Fragment，并重新创建它的视图。
虽然保留的fragment没有被销毁，但它已脱离消亡中的activity并处于保留状态，尽管此时fragment仍然存在，但已经没有任何activity在托管它。fragment必须同时满足两个条件才能进入保留状态：
1.已调用fragment的setRetainInstance(true)方法；
2.因设备配置改变，托管activity正在被销毁；

```java
@Override
public void onCreate(Bundle savedInstanceState){
super.onCreate(savedInstanceState);
setRetainInstance(true);
}
```
Android并不鼓励保留Fragment，情况未知。

保留Fragment与重写onSaveInstanceState()方法的主要区别在于数据可以存多久。如只是短暂保存数据，则使用保留Fragment比较方便，可以不用操心要保留的对象是否实现了Serializable接口。但是如果需要持久化地存储数据，则需要重写onSaveInstanceState()方法，因为当用户短暂离开应用后，如果因为系统回收内存需要销毁activity，则保留的fragment也会被随之销毁。
