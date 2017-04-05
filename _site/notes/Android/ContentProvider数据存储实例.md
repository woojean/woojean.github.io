# ContentProvider数据存储实例

一、Content Provider基本概念
1.ContentProvider为存储和获取数据提供了统一的接口。ContentProvide对数据进行封装，不用关心数据存储的细节。使用表的形式来组织数据。（当应用继承ContentProvider类，并重写该类用于提供数据和存储数据的方法，就可以向其他应用共享其数据。虽然使用其他方法也可以对外共享数据，但数据访问方式会因数据存储的方式而不同，如：采用文件方式对外共享数据，需要进行文件操作读写数据；采用sharedpreferences共享数据，需要使用sharedpreferences API读写数据。而使用ContentProvider共享数据的好处是统一了数据访问方式。）
2.使用ContentProvider可以在不同的应用程序之间共享数据。 
3.Android为常见的一些数据提供了默认的ContentProvider（包括音频、视频、图片和通讯录等）。   
ContentProvider所提供的函数：
query(),insert(),update(),delete(),getType(),onCreate()等。

二、URI（统一资源标识符）的使用方法
为系统的每一个资源给其一个名字，比方说通话记录。
1.每一个ContentProvider都拥有一个公共的URI，这个URI用于表示这个ContentProvider所提供的数据。 
2.Android所提供的ContentProvider都存放在android.provider包中。将其分为A，B，C，D 4个部分：
A：标准前缀，用来说明一个Content Provider控制这些数据，无法改变的；"content://"
B：URI 的标识，它定义了是哪个Content Provider提供这些数据。对于第三方应用程序，为了保证URI标识的唯一性，它必须是一个完整的、小写的类名。这个标识在元素的 authorities属性中说明：一般是定义该ContentProvider的包.类的名称:"content://hx.android.text.myprovider"
C：路径，不知道是不是路径，通俗的讲就是你要操作的数据库中表的名字，或者你也可以自己定义，记得在使用的时候保持一致就ok了；"content://hx.android.text.myprovider/tablename"
D：如果URI中包含表示需要获取的记录的ID；则就返回该id对应的数据，如果没有ID，就表示返回全部； "content://hx.android.text.myprovider/tablename/#" #表示数据id
（尽管这种查询字符串格式很常见，但是它看起来还是有点令人迷惑。为此，Android提供一系列的帮助类（在android.provider包下），里面包含了很多以类变量形式给出的查询字符串，这种方式更容易让我们理解一点，因此，如上面content://contacts/people/45这个URI就可以写成如下形式：
```java
　　Uri person = ContentUris.withAppendedId(People.CONTENT_URI,  45);
```
然后执行数据查询:
Cursor cur = managedQuery(person, null, null, null);
这个查询返回一个包含所有数据字段的游标，我们可以通过迭代这个游标来获取所有的数据）