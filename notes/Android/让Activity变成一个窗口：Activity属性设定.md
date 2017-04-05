# 让Activity变成一个窗口：Activity属性设定

android :theme="@android:style/Theme.Dialog" 
这就使你的应用程序变成对话框的形式弹出来了
（4.0上如果还用Theme.Dialog，只能说很土，跟整体UI风格差别很大
请使用android:theme="@android:style/Theme.Holo.DialogWhenLarge"
上效果对比图:
@android:style/Theme.Holo.DialogWhenLarge效果

![image](https://github.com/woojean/woojean.github.io/blob/master/images/java_3.png)
@android:style/Theme.Dialog效果

![image](https://github.com/woojean/woojean.github.io/blob/master/images/java_4.png)
　　android:theme="@android:style/Theme.Translucent" 
就变成半透明的。
类似的这种activity的属性可以在android.R.styleable 类的AndroidManifestActivity 方法中看到，AndroidManifest.xml中所有元素的属性的介绍都可以参考这个类android.R.styleable