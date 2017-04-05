# Android中常用的五种布局

Android布局是应用界面开发的重要一环，在Android中，共有五种布局方式，分别是：FrameLayout，LinearLayout，AbsoluteLayout，RelativeLayout，TableLayout。 
1.FrameLayout    
这个布局可以看成是墙脚堆东西，有一个四方的矩形的左上角墙脚，我们放了第一个东西，要再放一个，那就在放在原来放的位置的上面，这样依次的放，会盖住原来的东西。这个布局比较简单，也只能放一点比较简单的东西。    
2.LinearLayout    
 线性布局，这个东西，从外框上可以理解为一个div，他首先是一个一个从上往下罗列在屏幕上。每一个LinearLayout里面又可分为垂直布局 （android:orientation="vertical"）和水平布局（android:orientation="horizontal" ）。当垂直布局时，每一行就只有一个元素，多个元素依次垂直往下；水平布局时，只有一行，每一个元素依次向右排列。    
LinearLayout中有一个重要的属性 android:layout_weight="1"，这个weight在垂直布局时，代表行距；水平的时候代表列宽；weight值越大就越大。    
3.AbsoluteLayout
绝对布局犹如div指定了absolute属性，用X,Y坐标来指定元素的位置android:layout_x="20px" android:layout_y="12px" 这种布局方式也比较简单，但是在垂直随便切换时，往往会出问题，而且多个元素的时候，计算比较麻烦。    
4.RelativeLayout
相对布局可以理解为某一个元素为参照物，来定位的布局方式。主要属性有：    
相对于某一个元素    
android:layout_below="@id/aaa" 该元素在 id为aaa的下面    
android:layout_toLeftOf="@id/bbb" 该元素在 id为bbb的左边     
相对于父元素的地方    
android:layout_alignParentLeft="true"  与父元素左对齐    
android:layout_alignParentRight="true" 与父元素右对齐    
还可以指定边距等，具体详见API        
5.TableLayout 
表格布局类似Html里面的Table。每一个TableLayout里面有表格行TableRow，TableRow里面可以具体定义每一个元素，设定他的对齐方式 android:gravity="" 。    
每一个布局都有自己适合的方式，另外，这五个布局元素可以相互嵌套应用，做出美观的界面。