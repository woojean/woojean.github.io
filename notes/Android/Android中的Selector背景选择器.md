# Android中的Selector背景选择器 

drawable/list_item_bg.xml

```xml
<?xml version="1.0" encoding="utf-8" ?>   
<selector xmlns:android="http://schemas.android.com/apk/res/android">   
<item 
android:drawable="@drawable/pic1" />
<item 
android:state_window_focused="false"   
android:drawable="@drawable/pic1" />   

<!-- 非触摸模式下获得焦点并单击时的背景图片 -->   
<item 
android:state_focused="true" 
android:state_pressed="true"   
android:drawable= "@drawable/pic2" />   

<!-- 触摸模式下单击时的背景图片 -->   
<item 
android:state_focused="false" 
android:state_pressed="true"   
android:drawable="@drawable/pic3" /> 
  
<item 
android:state_selected="true"   
android:drawable="@drawable/pic4" />  
 
<item 
android:state_focused="true"   
android:drawable="@drawable/pic5" />   
</selector>  
```
在listview中配置android:listSelector="@drawable/list_item_bg，
或者在listview的item中添加属性android：background="@drawable/list_item_bg"即可实现，
或者在Java代码中使用：Drawable drawable = getResources().getDrawable(R.drawable.list_item_bg);  
ListView.setSelector(drawable);同样的效果。
但是这样会出现列表有时候为黑的情况，需要加上：android:cacheColorHint="@android:color/transparent"
使其透明。

再来看看Button的一些背景效果：
android:state_selected是选中
android:state_focused是获得焦点
android:state_pressed是点击
android:state_enabled是设置是否响应事件,指所有事件
根据这些状态同样可以设置button的selector效果。也可以设置selector改变button中的文字状态。以下就是配置button中的文字效果：drawable/button_font.xml
```xml
<?xml version="1.0" encoding="utf-8"?>   
<selector xmlns:android="http://schemas.android.com/apk/res/android">   
<item android:state_selected="true" android:color="#FFF" />   
<item android:state_focused="true" android:color="#FFF" />   
<item android:state_pressed="true" android:color="#FFF" />   
<item android:color="#000" />   
</selector>  
```
Button还可以实现更复杂的效果，如渐变：
drawable/button_color.xml   
```xml
<?xml version="1.0" encoding="utf-8"?>   
<selector xmlns:android="http://schemas.android.com/apk/res/android"> /   
<item android:state_pressed="true">//定义当button 处于pressed 状态时的形态。   
<shape>   
<gradient android:startColor="#8600ff" />   
<stroke android:width="2dp" android:color="#000000" />   
<corners android:radius="5dp" />   
<padding android:left="10dp" android:top="10dp"   
android:bottom="10dp" android:right="10dp"/>   
</shape>   
</item>   
<item android:state_focused="true">//定义当button获得 focus时的形态   
<shape>   
<gradient android:startColor="#eac100"/>   
<stroke android:width="2dp" android:color="#333333" color="#ffffff"/>   
<corners android:radius="8dp" />   
<padding android:left="10dp" android:top="10dp"   
android:bottom="10dp" android:right="10dp"/>   
</shape>   
</item>   
</selector> 
```
最后，需要在包含button的xml文件里添加两项。假如是main.xml文件，我们需要在<Button/>里加两项。
android：focusable="true" 
android:backgroud="@drawable/button_color" 
这样当你使用Button的时候就可以甩掉系统自带的那黄颜色的背景了，实现个性化的背景。