# 如果后台的Activity由于某原因被系统回收了，如何在被系统回收之前保存当前状态？

当你的程序中某一个Activity A 在运行时中，主动或被动地运行另一个新的Activity B 
这个时候A会执行

```java
public void onSaveInstanceState(Bundle outState) {   
      super.onSaveInstanceState(outState);    
      outState.putLong("id", 1234567890);
}  
```
B完成以后又会来找A,这个时候就有两种情况，一种是A被回收，一种是没有被回收，被回收的A就要重新调用onCreate()方法，不同于直接启动的是这回onCreate()里是带上参数savedInstanceState，没被收回的就还是onResume就好了。
savedInstanceState是一个Bundle对象，你基本上可以把他理解为系统帮你维护的一个Map对象。在onCreate()里你可能会用到它，如果正常启动onCreate就不会有它，所以用的时候要判断一下是否为空。
```java
if(savedInstanceState != null){  
     long id = savedInstanceState.getLong("id");  
}  
```
就像官方的Notepad教程里的情况，你正在编辑某一个note，突然被中断，那么就把这个note的id记住，再起来的时候就可以根据这个id去把那个note取出来，程序就完整一些。