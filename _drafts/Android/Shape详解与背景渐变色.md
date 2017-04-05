# Shape详解与背景渐变色

res/drawable/background_login.xml

```xml
<?xml version="1.0" encoding="utf-8"?>
<shape xmlns:android="http://schemas.android.com/apk/res/android">
 	<gradient 
  		android:startColor="#FFF"
  		android:endColor="#000"
  		android:angle="45"
 	/>
</shape>
```
shape是用来定义形状的，gradient定义该形状里面为渐变色填充，startColor起始颜色，endColor结束颜色，angle表示方向角度。当angle=0时，渐变色是从左向右。 然后逆时针方向转，当angle=90时为从下往上。
res/layout/login.xml
```xml
<?xml version="1.0" encoding="utf-8"?>
< LinearLayout
  	xmlns:android="http://schemas.android.com/apk/res/android"
  	android:orientation="vertical"
  	android:layout_width="fill_parent"
  	android:layout_height="fill_parent"
  	android:background="@drawable/background_login">
< /LinearLayout>
```

```java
import android.app.Activity;
import android.os.Bundle;
public class LoginActivity extends Activity {
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.login);
    }
}
```
效果图：

![image](https://github.com/woojean/woojean.github.io/blob/master/images/java_2.png)

在Android开发过程中，经常需要改变控件的默认样式， 那么通常会使用多个图片来解决。不过这种方式可能需要多个图片，比如一个按钮，需要点击时的式样图片，默认的式样图片。 这样就容易使apk变大。
那么除了使用drawable这样的图片外，还有其他方法吗？
本次就谈一下自定义图形shape，Android上支持以下几种属性gradient、stroke、corners、padding、solid等。
简单说明一下：
gradient：对应颜色渐变。 startcolor、endcolor就不多说了。 android:angle 是指从哪个角度开始变。
solid：填充。
stroke：描边。
corners：圆角。
padding：定义内容离边界的距离。与android:padding_left、android:padding_right这些是一个道理。