# 用layer-list一次加载多个层次图片

layer.xml

```xml
<?xml version="1.0" encoding="UTF-8"?>
<layer-list xmlns:android="http://schemas.android.com/apk/res/android">
	<item android:id="@+id/user_faceback_drawable" android:drawable="@drawable/faceback" />
	<item android:id="@+id/user_face_drawable" android:drawable="@drawable/h001"
		android:left="10.0dip" android:top="18.0dip" android:right="25.0dip"
		android:bottom="35.0dip" />
</layer-list>
```
就是把drawable文件夹下的image图片faceback和h001多层叠加来组成新图片。
上面的xml可以这样引用：
（1）在其它xml布局里引用：
```xml
<ImageView 
android:id="@+id/faceImg" 
android:background="@drawable/layer" (即layer.xml的文件名)
android:layout_width="wrap_content" 
android:layout_height="wrap_content" />
```
（2）在代码里引用：
((ImageView) findViewById(R.id.imageview)).setImageDrawable(getResources().getDrawable(R.drawable.layer));
或者：
```java
Resources r = getResources(); 
Drawable[] layers = new Drawable[2]; 
layers[0] = r.getDrawable(R.drawable.cor_info); 
layers[1] = r.getDrawable(R.drawable.icon); 
LayerDrawable layerDrawable = new LayerDrawable(layers); 
((ImageView) findViewById(R.id.imageview)).setImageDrawable(layerDrawable);
```