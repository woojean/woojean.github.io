# 隐式Intent

使用隐式Intent可以启动其他应用的Activity。使用显式Intent，需要明确指定要启动的Activity类名，而使用隐式Intent只需要向操作系统描述清楚工作意图，操作系统会去启动那些对外宣传能够胜任工作的Activity。一个隐式Intent主要包含以下部分：
（1）要执行的操作；
（2）要访问的数据的位置，如网页URL、指向文件的URI、指向ContentProvider中记录的URI；
（3）操作涉及的数据的类型，即MIME形式的数据类型；
（4）可选类别：用来描述如何使用某个Activity。
如下，通过<intent-filter设置，该Activity可以对外宣称自己是适合处理ACTION_VIEW的activity：

```xml
<activity
android:name=”.BrowserActivity”
android:label=”@string/app_name>
<intent-filter>
<action android:name=”android.intent.action.VIEW” />
<category android:name=”android.intent.category.DEFAULT” />
<data android:scheme=”http” android:host=”www.xxx.com” />
</intent-filter>
</activity>
```
DEFAULT类别必须明确地在intent过滤器中进行设置。DEFAULT类别实际隐含添加到了几乎每一个隐式intent中（所以要想接收到这种隐式intent，就必须显示声明该类别），唯一的例外是LAUNCHER类别。
隐式intent也可以包含extra信息，不过操作系统在寻找合适的activity时不会使用extra信息。

例：一个指定发送一段文本信息的隐式intent（文本信息既可以通过短信应用发送，也可以通过邮件等其他应用发送，它们都可以处理如下声明的intent）
```java
Intent i = new Intent(Intent.ACTION_SEND);
i.setType(“text/plain”);
i.putExtra(Intent.EXTRA_TEXT,getCrimeReport());
i.putExtra(Intent.EXTRA_SUBJECT,getString(R.string.crime_report_subject));
i = Intent.createChooser(i,getString(R.string_send_report));  // 始终创建一个供选择的Activity，即使只有一个符合条件的应用
startActivity(i);
```

例：获取联系人信息
```java
// 发送隐式Intent
Intent i = new Intent(Intent.ACTION_PICK, // 动作
ContactsContract.Contacts.CONTENT_URI); // 数据位置
startActivityForResult(i,REQUEST_CONTACT);

// 使用ContentResolver解析ContentProvider的返回值
@Override
public void onActivityResult(int requestCode,int resultCode,Intent data){
...
if(requestCode == REQUEST_CONTACT){
Uri contactUri = data.getData();
String[] queryFields = new String[]{
ContactsContract.Contacts.DISPLAY_NAME;
}
Cursor c = 		getActivity().getContentResolver().query(contactUri,queryFields,null,null,null);
if(c.getCount() == 0){
c.close();
return;
}
c.moveToFirst();
String suspect = c.getString(0)；
mCrime.setSuspect(suspect);
c.close();
}
}
```
可用Activity的检查：如果操作系统找不到匹配的Activity，就会崩溃。一种处理方法是在onCreateView()方法中对可用Activity进行检查：
```java
PackageManager pm = getPackageManager();
List<ResolverInfo> activities = pm.queryIntentActivities(i,0);
boolean isIntentSafe = activities.size() > 0;
```