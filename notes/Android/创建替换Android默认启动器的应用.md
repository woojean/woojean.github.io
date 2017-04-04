# 创建替换Android默认启动器的应用

该应用基于这样一个基本事实：所有应用的主Activity都会响应这样一个隐式Intent，该隐式Intent包括一个MAIN操作和一个LAUNCHER类别：

```xml
<intent-filter>
<action android:name=”android.intent.action.MAIN” />
<category android:name=”android.intent.category.LAUNCHER” />
</intent-filter>
```

```java
public class NerdLauncherFragment extends ListFragment{
@Override
public void onCreate(Bundle savedInstanceState){
super.onCreate(savedInstanceState);
// 定义隐式Intent
Intent startupIntent = new Intent(Intent.ACTION_MAIN);
startupIntent.addCategory(Intent.CATEGORY_LAUNCHER);

// 获取所有应用的主Activity的集合
PackageManager pm = getActivity().getPackageManager();
List<ResolverInfo> activities = pm.queryIntentActivities(startupIntent,0);

// 对主Activity进行排序
Collections.sort(activities,new Comparator<ResolveInfo>(){
public int compare(ResolveInfo a,ResolveInfo b){
PackageManager pm = getActivity().getPackageManager();
return String.CASE_INSENSITIVE_ORDER.compare(
a.loadLabel(pm).toString(),b.loadLabel(pm).toString());
}
}

// 封装Activity集合，得到一个列表的数据适配器
ArrayAdapter<ResolveInfo> adapter = new ArrayAdapter<ResolveInfo>(
getActivity(),android.R.layout.simple_list_item_1,activities){
public View getView(int pos,View convertView,ViewGroup parent){
PackageManager pm = getActivity().getPackageManager();
View v = super.getView(pos,convertView,parent);
TextView tv = (TextView)v;
ResolveInfo ri = getItem(pos);
tv.setText(ri.loadLabel(pm));
return v
}
}
setListAdapter(adapter);
}

@Override
public void onListItemClick(ListView l,View v,int position,long id){
ResolvedInfo resolveInfo = (ResolveInfo)l.getAdapter().getItem(position);
ActivityInfo activityInfo = resolveInfo.activityInfo;
if(activityInfo == null) return;

// 可见，显式Intent也可以指定动作
Intent i = new Intent(Intent.ACTION_MAIN);
i.setClassName(activityInfo.applicationInfo.packageName,activityInfo.name);
startActivity(i);
}
```
MAIN/LAUNCHER intet过滤器可能无法与通过startActivity(...)方法发送的MAIN/LAUNCHER隐式intent相匹配。因为调用startActivity(Intent)方法意味着启动与发送的Intent相匹配的默认的activity，操作系统会默认将Intent.CATEGORY_DEFAULT类别添加给目标intent。因此，如果希望一个intent过滤器能够与通过startActivity(...)发送的隐式intent相匹配，那么必须在对应的intent过滤器中包含DEFAULT类别。而应用的主Activity通常不包含DEFAULT类别。

将该应用设为设备主屏幕：

```
<intent-filter>

<action android:name=”android.intent.action.MAIN” />

<category android:name=”android.intent.category.LAUNCHER” />

<category android:name=”android.intent.category.HOME” />

<category android:name=”android.intent.category.DEFAULT” />

</intent-filter>

```


点击Home键，该应用会成为可选的主界面。
如果要恢复到系统默认设置，可以选中Settings->Applications->Manage Application菜单项，找到该应用，并清除它的Launch by default选项。