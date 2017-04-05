# Fragment的生命周期

![image](https://github.com/woojean/woojean.github.io/blob/master/images/android_3.png)

托管Activity的onCreate()方法执行之后，Fragment的onActivityCreated(...)方法也会被调用。在Activity处于停止、暂停或运行状态下时，FragmentManager立即驱使fragment跟上activity的步伐（维护两者的状态一致），直到与activity的最新状态保持同步。比如向处于运行状态的activity中添加fragment时，以下fragment生命周期的方法会被依次调用：onAttach()、onCreate()、onCreateView()、onActivityCreated()、onStart()、onResume()。