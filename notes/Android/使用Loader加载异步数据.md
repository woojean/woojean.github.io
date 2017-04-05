# 使用Loader加载异步数据

Loader用来在activity或者fragment中异步加载数据。它有如下一些特性：
1.对每个activity或fragment都是可用的；
2.提供数据的异步加载功能；
3.控制数据源，并且当数据源内容发生变化时传送新的结果；
4.当设备配置发生改变时，能够自动重新连接至之前的状态；

LoadManager是一个用来管理一个或多个Loader实例的虚拟类。每一个activity或fragment都只包含一个LoadManager。LoaderManager在Activity和Fragment的生命周期中管理Loaders，并且在配置变化时保持已载入的数据

LoaderManager.LoaderCallbacks：LoadManager的回调接口，被客户端用来与LoadManager进行互动。该接口包含如下方法：
// 使用给定ID创建并返回一个Loader
abstract Loader<D>	onCreateLoader(int id, Bundle args)

// 当Loader完成数据加载时调用
abstract void	onLoadFinished(Loader<D> loader, D data)

// 当Loader重置时调用
abstract void	onLoaderReset(Loader<D> loader)

Loader的继承关系：
![image](https://github.com/woojean/woojean.github.io/blob/master/images/android_8.png)


创建一个类型参数为Cursor的AsyncTaskLoader的子类：
```java
public abstract class SQLiteCursorLoader extends AsyncTaskLoader<Cursor>{
private Cursor mCursor;
public SQLiteCursorLoader(Context context){
supere(context);
}
protected abstract Cursor loadCursor();

@Override
public Cursor loadInBackground(){
Cursor cursor = loadCursor();
if(cursor != null){
cursor.getCount(); // 保证数据在发送给主线程之前已加载到内存中
}
return cursor;
}

@Override
public void deliverResult(Cursor data){
Cursor oldCursor = mCursor;
mCursor = data;
if(isStarted()){
super.deliverResult(data);
}
// 在关闭旧的cursor之前，必须确认新旧cursor并不相同
if(oldCursor != null && oldCursor != data && !oldCursor.isClosed()){
oldCursor.close();
}
}

@Override
protected void onStartLoading(){
if(mCursor != null ){
deliverResult(mCursor);
}
if(takeContentChanged() || mCursor == null ){
forceLoad();
}
}

@Override
protected void onStopLoading(){
		cancelLoad();
}

@Override
public void onCanceled(Cursor cursor){
		if(cursor != null && !cursor.isClosed()){
			cursor.close();
}
}

@Override
protected void onReset(){
super.onReset();
onStopLoading();
if(mCursor != null && !mCursor.isClosed()){
mCursor.close();
}
mCursor = null;
}
}
```
在RunListFragment内部实现一个SQLiteCursorLoader 的具体实现类：
```java
public class RunListFragment extends ListFragment implements LoaderCallbacks<Cursor>{
...
@Override
public void onCreate(Bundle savedInstanceState){
super.onCreate(savedInstanceState);
setHasOptionsMenu(true);
getLoaderManager().initLoader(0,null,this);  // 初始化Loader。第3个参数为一个LoaderCallbacks的实现，被LoadManager用来发送Load事件，这里是自身
第1个参数是一个ID，当调用initLoader时，如果指定ID的Loader已经存在，则返回，否则触发LoaderCallbacks的onCreateLoader()方法
}

@Override
public void onActivityResult(int requestCode, int resultCode,Intent data){
if(REQUEST_NEW_RUN == requestCode){
getLoaderManager().restartLoader(0,null,this);
}
}

@Override
public Loader<Cursor> onCreateLoader(int id,Bundle args){
return new RunListCursorLoader(getActivity());
}

@Override
public void onLoadFinished(Loader<Cursor> loader,Cursor cursor){
		RunCursorAdapter adapter = new RunCursorAdapter(getActivity(),(RunCursor)cursor);
setListAdapter(adapter);
}

@Override
public void onLoaderReset(Loader<Cursor> loader){
		setListAdapter(null);
}

private static class RunListCursorLoader extends SQLiteCursorLoader{
public RunListCursorLoader(Context context){
super(context);
}

@Override
protected Cursor loadCursor(){
return RunManager.get(getContext()).queryRuns();
}
}

}
```