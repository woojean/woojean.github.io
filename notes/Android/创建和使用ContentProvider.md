# 创建和使用ContentProvider

要创建我们自己的Content Provider的话，我们需要遵循以下几步：
a.创建一个继承了ContentProvider父类的类
b.定义一个名为CONTENT_URI，并且是public static final的Uri类型的类变量，你必须为其指定一个唯一的字符串值，最好的方案是以类的全名称， 如:
public static final Uri CONTENT_URI = Uri.parse(“content://com.google.android.MyContentProvider”);
c.定义你要返回给客户端的数据列名。如果你正在使用Android数据库，必须为其定义一个叫_id的列，它用来表示每条记录的唯一性。
d.创建你的数据存储系统。大多数Content Provider使用Android文件系统或SQLite数据库来保持数据，但是你也可以以任何你想要的方式来存储。
e.如果你要存储字节型数据，比如位图文件等，数据列其实是一个表示实际保存文件的URI字符串，通过它来读取对应的文件数据。处理这种数据类型的Content Provider需要实现一个名为_data的字段，_data字段列出了该文件在Android文件系统上的精确路径。这个字段不仅是供客户端使用，而且也可以供ContentResolver使用。客户端可以调用ContentResolver.openOutputStream()方法来处理该URI指向的文件资源；如果是ContentResolver本身的话，由于其持有的权限比客户端要高，所以它能直接访问该数据文件。
f.声明public static String型的变量，用于指定要从游标处返回的数据列。
g.查询返回一个Cursor类型的对象。所有执行写操作的方法如insert(), update() 以及delete()都将被监听。我们可以通过使用ContentResover().notifyChange()方法来通知监听器关于数据更新的信息。
h.在AndroidMenifest.xml中使用<provider>标签来设置Content Provider。
i.如果你要处理的数据类型是一种比较新的类型，你就必须先定义一个新的MIME类型，以供ContentProvider.geType(url)来返回。
MIME类型有两种形式:一种是为指定的单个记录的，还有一种是为多条记录的。（结尾方式不同）这里给出一种常用的格式：
vnd.android.cursor.item/vnd.yourcompanyname.contenttype （单个记录的MIME类型）
比如, 一个请求列车信息的URI如content://com.example.transportationprovider/trains/122 可能就会返回typevnd.android.cursor.item/vnd.example.rail这样一个MIME类型。
vnd.android.cursor.dir/vnd.yourcompanyname.contenttype （多个记录的MIME类型）
比如, 一个请求所有列车信息的URI如content://com.example.transportationprovider/trains 可能就会返回vnd.android.cursor.dir/vnd.example.rail这样一个MIME 类型。
Content Provider的入口需要在AndroidManifest.xml中配置:

```xml
<provider 
android:name=”MyContentProvider” 
android:authorities=”com.wissen.MyContentProvider” />
android:authorities的值一定要与定义的常量字符串AUTHORITY的值相同，否则会报错。
```