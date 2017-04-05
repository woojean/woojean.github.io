# SearchView

Android3.0之前的版本基于一个重叠在Activity上的对话框实现搜索界面及功能，具体过程略过。3.0以后的版本基于SearchView来实现。SearchView类属于操作视图，可内置在操作栏里。

定义搜索配置文件：res/xml/searchable.xml
搜索配置文件用来描述搜索对话框如何显示自身。
```xml
<?xml ...
<searchable xmlns:android=”http://...”
android:label=”@string/app_name”
android:hint=”@string/search_hint”
/>

将Activity定义为可搜索的Activity：
<manifest ...
<application ...
<activity
android:name=”.PhotoGalleryActivity”
android:launchMode=”singleTop” // 收到intent时，如果activity实例已经处在回退栈的顶端，则不创建新的activity，而直接路由新intent给现有activity。
android:label=”@string/title_activity_photo_gallery” >
<intent-filter>
<action android:name=”android.intent.action.MAIN” />
<category android:name=”android.intent.category.LAUNCHER” />
</intent-filter>
<intent-filter>  // 表明该activity可监听搜索intent
<action android:name=”android.intent.action.SEARCH” />
</intent-filter>
<meta-data  // 将搜索配置文件与目标activity关联起来
android:name=”android.app.searchable”
android:resource=”@xml/searchable”
/>
</activity>	
</application>
</manifest>
```

修改Activity，覆盖onNewIntent方法来处理搜索工作：
```java
public class PhotoGalleryActivity extends SingleFragmentActivity{
...
@Override
public void onNewIntent(Intent intent){
...
// 搜索、缓存、更新UI
}
}
```
在菜单中添加SearchView操作视图：res/menu/fragment_photo_gallery.xml
```xml
<menu xmlns:android=”http://...”>
		<item android:id=”@+id/menu_item_search”
android:title=”Search”
android:icon=”@android:drawable/ic_menu_search”
android:showAsAction=”ifRoom”
android:actionViewClass=”android.widget.SearchView”
/>
<item android:id=”@+id/menu_item_clear”
...
/>
</menu>
```
将菜单项的actionViewClass设为android.widget.SearchView，相当于告诉Android，不要在操作栏对此菜单项使用常规的视图部件，而是使用指定的视图类。SearchView将不会产生任何onOptionItemSelected(...)回调方法。

配置SearchView：
在发送搜索Intent之前，SearchView需要知道当前的搜索配置信息：通过SearchManager获取到一个SearchableInfo对象，并将它设置给SearchView。SearchableInfo包含了有关搜索的全部信息，包括应该接收intent的activity名称，以及所有searchable.xml中的配置信息。
```java
public class PhotoGalleryFragment extends Fragment{
...
	@Override
@TargetApi(11)
public void onCreateOptionMenu(Menu menu,MenuInflater inflater){
super.onCreateOptionsMenu(menu,inflater);
inflater.inflate(R.menu.fragment_photo_gallery,menu);
if(Build.VERSION.SDK_INT >= Build.VERSION_CODES.HONEYCOMB){
MenuItem searchItem = menu.findItem(R.id.menu_item_search);
SearchView searchView = (SearchView)searchItem.getActionView();

SearchManager searchManager = 
(SearchManager)getActivity().getSystemService(Context.SEARCH_SERVICE);

// searchable activity的component name，由此系统可通过intent进行唤起
ComponentName name = getActivity().getComponentName();
SearchableInfo searchInfo = searchManager.getSearchableInfo(name);

searchView.setSearchableInfo(searchInfo);
}
}
}
```
SearchView相当于可搜索Activity在其他Activity中的一个入口，点击后展示可搜索Activity。菜单中的Item定义了SearchView的显示，而搜索配置文件searchable.xml定义了可搜索Activity的显示。