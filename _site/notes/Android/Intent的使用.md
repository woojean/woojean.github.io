# Intent的使用

在一个Android应用中，主要是由一些组件组成，（Activity,Service,ContentProvider,etc.)在这些组件之间的通讯中，由Intent协助完成。
正如网上一些人解析所说，Intent负责对应用中一次操作的动作、动作涉及数据、附加数据进行描述，Android则根据此Intent的描述，负责找到对应的组件，将Intent传递给调用的组件，并完成组件的调用。Intent在这里起着实现调用者与被调用者之间的解耦作用。
Intent传递过程中，要找到目标消费者（另一个Activity,IntentReceiver或Service），也就是Intent的响应者，有两种方法来匹配：
1.显式匹配（Explicit)： 

```java
public TestB extents Activity  
{  
    .........  
};  
public class Test extends Activity  
{  
    ......  
    public void switchActivity()  
    {  
        Intent i = new Intent(Test.this, TestB.class);  
        this.startActivity(i);  
    }  
}  
```
代码简洁明了，执行了switchActivity()函数，就会马上跳转到名为TestB的Activity中。 

2.隐式匹配(Implicit):
  隐式匹配，首先要匹配Intent的几项值：Action, Category, Data/Type,Component
如果填写了Componet就是上例中的Test.class)这就形成了显示匹配。所以此部分只讲前几种匹配。匹配规则为最大匹配规则，
（1）如果你填写了Action，如果有一个程序的Manifest.xml中的某一个Activity的IntentFilter段中定义了包含了相同的Action那么这个Intent就与这个目标Action匹配，如果这个Filter段中没有定义Type,Category，那么这个Activity就匹配了。但是如果手机中有两个以上的程序匹配，那么就会弹出一个对话可框来提示说明。
Action的值在Android中有很多预定义，如果你想直接转到你自己定义的Intent接收者，你可以在接收者的IntentFilter中加入一个自定义的Action值（同时要设定Category值为"android.intent.category.DEFAULT"），在你的Intent中设定该值为Intent的Action,就直接能跳转到你自己的Intent接收者中。因为这个Action在系统中是唯一的。
（2）data/type，你可以用Uri来做为data,比如Uri uri = Uri.parse(http://www.google.com );
Intent i = new Intent(Intent.ACTION_VIEW,uri);手机的Intent分发过程中，会根据http://www.google.com 的scheme判断出数据类型type
手机的Brower则能匹配它，在Brower的Manifest.xml中的IntenFilter中首先有ACTION_VIEW Action,也能处理http:的type，
（3）至于分类Category，一般不要去在Intent中设置它，如果你写Intent的接收者，就在Manifest.xml的Activity的IntentFilter中包含android.category.DEFAULT,这样所有不设置Category（Intent.addCategory(String c);）的Intent都会与这个Category匹配。
（4）extras（附加信息），是其它所有附加信息的集合。使用extras可以为组件提供扩展信息，比如，如果要执行“发送电子邮件”这个动作，可以将电子邮件的标题、正文等保存在extras里，传给电子邮件发送组件。