# Activity生命周期 

设备旋转时，当前看到的Activity实例会被系统销毁，然后重新创建。因为设备旋转会改变设备配置，所谓设备配置即用来描述设备当前状态的一系列特征，包括：屏幕的方向、屏幕的密度、屏幕的尺寸、键盘类型、底座模式、语言等等。通常为匹配不同的设备配置，应用会提供不同的备选资源，在运行时，当设备配置发生变更时，会销毁当前Activity并重建。
为了保存设备旋转以前的数据，需要覆盖`onSaveInstanceState(Bundle outState)`方法，将一些数据保存在Bundle中，然后在onCreate()方法中取回这些数据。注意：在Bundle中存储和恢复的数据类型只能是基本数据类型以及可以实现Serializable接口的对象。 
onSaveInstanceState方法通常在onPause()、onStop()、onDestory()方法之前由系统调用，并不仅仅用来处理设备配置变更的问题，当用户离开当前activity管理的用户界面，或Activity需要回收内存时，activity也会被销毁。在描述Activity的生命周期时，需要将其考虑进来：

![image](https://github.com/woojean/woojean.github.io/blob/master/images/android_2.png)

`Activity记录`：当调用onSaveInstanceState方法时，用户数据会被保存在Bundle对象中，然后操作系统将Bundle对象放入Activity记录中，在需要恢复Activity时，操作系统可以使用暂存的Activity记录重新激活Activity。即使用户离开当前应用（此时彻底停止了当前应用的进程），暂存的Activity记录依然被系统保留。除非用户通过按后退键退出应用，或者系统重启，或者长时间不适用Activity，此时系统彻底销毁当前Activity，暂存的Activity记录通常也会被清除。