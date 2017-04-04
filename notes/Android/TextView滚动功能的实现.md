# TextView滚动功能的实现

滚动条大家肯定不陌生的，当然这里说的不是ScrollView，在很多的时候需要一个TextView来显示很多内容，但是内容太多了超过了TextView的显示范围，这个时候就需要TextView里面的内容滚动起来。首先看下布局文件：

```xml
<TextView  
  android:id="@+id/reportContent"  
  android:layout_width="fill_parent"  
  android:layout_height="wrap_content"  
  android:maxLines="20"  
  android:scrollbars="vertical"  
  android:singleLine="false" /> 
```
当内容超过了20行的时候就会出现滚动条了，这样才符合上面的描述，但是即便布局文件这样设置了相关属性，在显示的时候仍然达不到我们的要求，因为我们肯定需要把没有显示出来的内容通过滚动能够显示出来，这就需要在代码里面设置TextView的相关属性来，代码如下：
```java
reportContent = (TextView)findViewById(R.id.reportContent);  
//如果reportContent内容太多了的话，我们需要让其滚动起来，  
//具体可以查看SDK中android.text.method了解更多，代码如下：  
reportContent.setMovementMethod(ScrollingMovementMethod.getInstance()) ;
```