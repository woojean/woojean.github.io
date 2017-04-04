# ListView美化的一些属性

用心的朋友应该会发现，listview中在设置了背景之后。会有些问题。

1）listview在拖动的时候背景图片消失变成黑色背景。等到拖动完毕我们自己的背景图片才显示出来。

2）listview的上边和下边有黑色的阴影。

3）lsitview的每一项之间需要设置一个图片做为间隔。

针对以上问题 在listview的xml文件中设置以下语句。

问题1 有如下代码结解决 android:scrollingCache="false" 
android:scrollingCache		 When set to true, the list uses a drawing cache during scrolling. 

问题2 用如下代码解决：android:fadingEdge="none"  
问题3  用如下代码解决：  android:divider="@drawable/list_driver"  其中  @drawable/list_driver 是一个图片资源

android:cacheColorHint="#00000000" 隐藏的颜色提示设为透明
android:listSelector="@android:color/transparent" 列表选择器设为颜色透明：当你不使用android:listSelector属性，默认会显示选中的item为橙黄底色，有时候我们需要去掉这种效果
6，设置分割线Divider样式
给ListView设置分割线，只需设置如下两个属性：
android:divider="#000" //设置分割线显示颜色
android:dividerHeight="1px" //此处非0，否则无效
    <ListView android:id="@+id/listView"          android:layout_width="fill_parent"         android:layout_height="fill_parent"          android:divider="#FFF"         android:dividerHeight="1px"         android:layout_margin="10dip"/>