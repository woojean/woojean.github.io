# 使用SQLite本地数据库

![image](https://github.com/woojean/woojean.github.io/blob/master/images/android_7.png)

新建一个数据库操作辅助类：
```java
public class RunDatabaseHelper extends SQLiteOpenHelper{
private static final String DB_NAME =”runs.sqlite”; // 数据库名
private static final int VERSION = 1;

private static final String TABLE_RUN = “run”; // 表名
private static final String COLUMN_RUN_START_DATE = “start_date”; // 列名

private static final String TABLE_LOCATION = “location”; // 表名
private static final String COLUMN_LOCATION_LATITUDE = “latitude”; // 列名
...其他列名略


public RunDatabaseHelper(Context context){
super(context,DB_NAME,null,VERSION);  // 这里使用数据库名调用超类构造方法创建数据库
}

@Override
public void onCreate(SQLiteDatabase db){
db.execSQL(“create table run(“ // 创建run表
+”_id integer primary key autoincrement,start_date integer)”);
db.execSQL(“create table location(“ // 创建location表
+”timestamp integer,latitude real,longtitude real,altitude real,” // real是浮点数
+”provider varchar(100),run_id integer references run(_id))”);
}

@Override
public void onUpgrade(SQLiteDatabase db,int oldVersion, int newVersion){
// 数据库迁移代码，实现不同版本间的数据库结构升级或转换
}

// 往run表中插入数据
public long insertRun(Run run){
ContentValues cv = new ContentValues();
cv.put(COLUMN_RUN_START_DATE,run.getStartDate().getTime());
return getWritableDatabase().insert(TABLE_RUN,null,cv); // 往TABLE_RUN表中插入cv数据
}

// 往location表中插入数据
public long insertLocation(long runId,Location location){
ContentValues cv = new ContentValues();
cv.put(COLUMN_LOCATION_LATITUDE,location.getLatitude());
...
return getWritableDatabase().insert(TABLE_LOCATION,null,cv); // 返回新插入记录的ID
}

}
```
为Run类添加ID属性：
```java
public class Run{
private long mId;
private Date mStartDate;
public Run(){
mStartDate = new Date();
}
public Date getStartDate(){...
public void setStartDate(Date startDate...
public int getDurationSeconds(long endMillis..
}
```

修改RunManager类以使用数据库：
```java
public class RunManager{
private static final String PREFS_FILE = “runs”;
private static final String PREF_CURRENT_RUN_ID = “RunManager.currentRunId”;
private RunDatabaseHelper mHelper;
private SharedPreferences mPrefs;
private long mCurrentRunId;

public static final String ACTION_LOCATION = “xxx....”;
private static RunManager sRunManager;
private Context mAppContext;
private LocationManager mLocationManager;

private RunManager(Context appContext){
mAppContext = appContext;
mLocationManager = 
(LocationManager)mAppContext.getSystemService(Context.LOCATION_SERVICE);

mHelper = new RunDatabaseHelper(mAppContext);
mPrefs = mAppContext.getSharedPreferences(PREFS_FILE,Context.MODE_PRIVATE);
mCurrentRunId = mPrefs.getLong(PREF_CURRENT_RUN_ID,-1）;
}

...

// 插入run记录
private Run insertRun(){
Run run = new Run();
run.setId(mHelper.insertRun(run));
return run;
}

// 插入Location记录
public void insertLocation(Location loc){
if(mCurrentRunId != 1){
mHelper.insertLocation(mCurrentRunId,loc);	
}
else{
...
}
}

public Run startNewRun(){
Run run = insertRun();
startTrackingRun(run);
return run;
}

public void startTrackingRun(Run run){
// 将Run实例传入的ID分别存储在实例变量和shared preferences中，这样即使在应用完全停止的情况下仍可重新取回ID
mCurrentRunId = run.getId();
mPrefs.edit().putLong(PREF_CURRENT_RUN_ID,mCurrentRunId).commit();
startLocationUpdates();
}

public void stopRun(){
stopLocationUpdates();
mCurrentRunId = -1;
mPrefs.edit().remove(PREF_CURRENT_RUN_ID).commit();
}
}
```
更新启停按钮代码：
```java
public class RunFragment extends Fragment{
...
@Override
public View onCreateView(LayoutInflater inflater,ViewGroup container,Bundle savedInstanceState){
...
mStartButton.setOnClickListener(new View.OnClickListener(){
@Override
public void onClick(View v){
mRunManager.startLocationUpdates();
mRun = new Run();
mRun = mRunManager.startNewRun();
updateUI();
}
});

mStopButton.setOnClickListener(new View.OnClickListener(){
@Override
public void onClick(View v){
mRunManager.stopLocationUpdates();
mRunManager.stopRun();
updateUI();
}
});
```
新建一个独立的Broadcast-Receiver来调用RunManager的insertLocation(Location)方法：
```java
public class TrackingLocationReceiver extends LocationReceiver{
@Override
protected void onLocationReceived(Context c,Location loc){
RunManager.get(c).insertLocation(loc);
}
}
```
将manifest中的<receiver android:name=”.LocationReceiver”改成<receiver android:name=”.TrackingLocationReceiver”，因为现在已经不直接使用LocationReceiver了。


使用CursorAdapter显示旅程列表：
首先为RunDatabaseHelper添加查询数据库的方法：
```java
public class RunDatabaseHelper extends SQLiteOpenHelper{
...
private static final String COLUMN_RUN_ID = “_id”;
...
// 继承CursorWrapper，实现一个封装Cursor的类。
因为Cursor将结果集看成是一系列的数据行和数据列，但仅支持String以及原始数据类型的值，而这里我们想要它能够返回Run对象的实例。
CursorWrapper类将封装Cursor，并转发所有的方法调用给它
public static class RunCursor extends CursorWrapper{
public RunCursor(Cursor c){  // 构造方法中传入一个Cursor
super(c);
}

public Run getRun(){
if(isBeforeFirst() || isAfterLast())
return null;
Run run = new Run();
long runId = getLong(getColumnIndex(COLUMN_RUN_ID));
run.setId(runId);
long startDate = getLong(getColumnIndex(COLUMN_RUN_START_DATE));
run.setStartDate(new Date(startDate));
return run;
}
}

public RunCursor queryRuns(){
Cursor wrapped = 
getReadableDatabase().
query(TABLE_RUN,null,null,null,null,null,COLUMN_RUN_START_DATE+”asc”);
return new RunCursor(wrapped);
}

在RunManager类中添加查询Run的方法：
public class RunManager{
...
public RunCursor queryRuns(){
return mHelper.queryRuns();
}
...
```

创建用于展示Run列表的Fragment：
```java
public class RunListFragment extends ListFragment{
private RunCursor mCursor;

@Override
public void onCreate(Bundle savedInstanceState){
super.onCreate(savedInstanceState);
mCursor = RunManager.get(getActivity()).queryRuns();

// 这里在主线程中查询数据，设置adapter，显然是不好的实现
RunCursorAdapter adapter = new RunCursorAdapter(getActivity(),mCursor);
setListAdapter(adapter);
}

@Override 
public void onDestroy(){
mCursor.close();
super.onDestroy();
}

// 实现一个CursorAdapter类
private static class RunCursorAdapter extends CursorAdapter{
private RunCursor mRunCursor;
public RunCursorAdapter(Context context,RunCursor cursor){
super(context,cursor,0);
}

@Override
public View newView(Context context,Cursor cursor,ViewGroup parent){
LayoutInflater inflater = 
(LayoutInflater)context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
return inflater.inflate(android.R.layout.simple_list_item_1,parent,false);
}

// 当需要配置视图显示cursor中的一行数据时，CursorAdapter将调用bingView(...)方法，该方法中的View参数就是newView()方法中返回的View。
@Override
public void bindView(View view,Context context,Cursor cursor){
Run run = mRunCursor.getRun();  // getRun()方法为CursorWrapper封装Cursor后新增的方法
TextView startDateTextView = (TextView)view;
String cellText = context.getString(R.string.cell_text,run.getStartDate());
startDateTextView.setText(cellText);
}
}
}
```