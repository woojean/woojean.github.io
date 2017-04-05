# Activity.finish()、onDestroy() 、System.exit()、Process.killProcess

Activity.finish()：在你的activity动作完成的时候，或者Activity需要关闭的时候，调用此方法。当你调用此方法的时候，系统只是将最上面的Activity移出了栈，并没有及时的调用onDestory（）方法，其占用的资源也没有被及时释放。因为移出了栈，所以当你点击手机上面的“back”按键的时候，也不会再找到这个。调用finish()会执行onDestory()；（如果想要主动销毁当前Activity，可以再onPause中调用this.finish()，而不是onDestory()，不能直接调用onDestroy()来结束你的Activity,一般做法是finish()， 在onDestroy()中可以做一些清理操作。）
（A->B(FINISH)->C(BACK)?）S
Activity.onDestory():系统销毁了这个Activity的实例在内存中占据的空间。
在Activity的生命周期中，onDestory()方法是他生命的最后一步，资源空间等就被回收了。当重新进入此Activity的时候，必须重新创建，执行onCreate()方法。
System.exit(0):退出整个应用程序，是针对整个Application的。将整个进程直接KO掉（释放掉所有资源，当然包括Activity占用的资源）0表示正常退出，1表示异常退出。
（其实Process.killProcess（杀死进程）或System.exit(0)（终止当前虚拟机）都不应该直接调用，进程是由os底层进行管理的，android系统会自己进行处理回收进程。退出应用你就直接finish掉activity就行了。正常情况下back键退出应用以后os就会回收app进程，但当app中有推送服务等需要长时间运行的服务时os就不会kill掉进程，也就是说应用将一直在线。即使你手动kill掉进程，进程也会自动重启（估计android os认为app是被意外终止的（如内存不足），os底层有监听服务，app被意外终止会自动重启。））