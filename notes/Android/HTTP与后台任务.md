# HTTP与后台任务

Android禁止在主线程中发生任何网络连接行为，强行为之，会抛出NetworkOnMainThreadException。

定义GridView每一项的布局：
```xml
<ImageView
android:id=”@+id/gallery_item_imageView”
android:layout_width=”match_parent”
android:layout_height=”120dp”
android:layout_gravity=”center”
android:scaleType=”centerCrop”  // 居中放置图片，然后进行放大，即放大较小图片、裁剪较大图片以匹配视图
/>
```
定义将要加载的模型对象类：
```java
	public class GalleryItem{
private String mCaption;
private String mId;
private String mUrl;

public String toString(){
return mCaption;
}
}
```

定义一个基本的HTTP访问类：
```java
public class FlickrFentchr{

private static final String ENDPOINT = “http://api.flickr.com/...”;
private static final String API_KEY= “...”;
private static final String METHOD_GET_RECENT= “...”;
 
private static final String PARAM_EXTRAS= “...”;
private static final String EXTRA_SMALL_URL= “...”;

private static final String XML_PHOTO= “photo”;

// byte数组转String
public String getUrl(String urlSpec) throws IOException{
return new String(getUrlBytes(urlSpec)); 
}

// 打开HTTP链接，将结果读入byte数组中
private byte[] getUrlBytes(String urlSpec) throws IOException{
URL url = new URL(urlSpec);
HttpURLConnection connection = (HttpURLConnection)url.openConnection();
try{
ByteArrayOutputStream out = new ByteArrayOutputStream(); // 输出流，向byte数组写数据
InputStream in = connection.getInputStream();

if(connection.getResponseCode() != HttpURLConnection.HTTP_OK){
return null;
}
int bytesRead = 0;
byte[] buffer = new byte[1024];
while((byteRead = in.read(buffer))> 0){
out.write(buffer,0,bytesRead);
}
out.close();
return out.toByteArray();
}finally{
connection.disconnect();
}
}

// 拼凑URL，调用请求方法
public void fetchItems(){
try{
String url = Uri.parse(ENDPOINT).buildUpon()
.appendQueryParameter(“method”,METHOD_GET_RECENT)
.appendQueryParameter(“api_key”,API_KEY)
.appendQueryParameter(PARAM_EXTRAS,EXTRA_SMALL_URL)
.build().toString();

String xmlString = getUrl(url);
}catch(IOException ioe){
...
}
}

// 解析得到的XML，得到模型对象的数组
private void parseItems(ArrayList<GalleryItem> items,XmlPullParser parser) 
throws XmlPullParserException,IOException{
int eventType = parser.next();
while(eventType != XmlPullParser.END_DOCUMENT){
if(eventType == XmlPullParser.START_TAG && XML_PHOTO.equals(parser.getName())){
String id = parser.getAttributeValue(null,”id”);
String caption = parser.getAttributeValue(null,”title”);
String smallUrl= parser.getAttributeValue(null,EXTRA_SMALL_URL);

GalleryItem item = new GalleryItem();
item.setId(id);
item.setCaption(caption);
item.setUrl(smallUrl);
items.add(item);
}
eventType = parser.next();
}
}
}
```
定义后台线程类
Android中使用消息队列的线程叫做消息循环（Message Loop)。消息循环会不断循环检查队列上是否有新消息。消息循环由一个线程和一个looper组成，Looper对象管理着线程的消息队列。
在Looper.loop()方法中执行了white(true)循环，在循环体内执行了Message msg = queue.next()，即在循环体内操作了消息队列。
主线程也是一个消息循环，因此也有一个looper。在哪个线程上创建的Handler，就与该线程的Looper相关联。
一般的Thread显然不带looper，因此需要自己调用Looper.prepare()和Looper.loop()，以及Handler。
HandlerThread为自带Looper的Thread类。
Handler可以post一个runnable，也可以send一个message。post一个runnable实际上仍然是通过sendMessage来实现的，因此本质上只有sendMessage一种方式。

Message包含好几个实例变量，其中有3个需要在实现时定义：
what:用户定义的int型消息代码
obj:随消息发送的用户指定的对象
target:处理消息的Handler，因此Message在创建时总是与一个Handler相关联
Handler不仅是处理Message的目标，也是创建和发布Message的接口。


以下ThumbnailDownloader这个类配合Fragment中的GridView实现图片的按需加载。所谓按需，即在GridView的adapter的getView()中触发下载行为。
```java
public class ThumbnailDownloader<Token> extends HandlerThread{
private static final int MESSAGE_DOWNLOAD = 0;

Handler mHandler; // 这个handler用来操纵自身所在线程的消息队列

// 一个同步map，用来存储和获取与特定Token相关的URL
Map<Token,String> requestMap =
Collections.synchronizedMap(new HashMap<Token,String>()); 

Handler mResponseHandler;  // 来自启动线程的Handler，在构造方法中赋值
Listener<Token> mListener; // 来自启动线程的Listener，用于在本线程中执行任务后回调

public interface Listener<Token>{
void onThumbnailDownloaded(Token token,Bitmap thumbnail);
}

public void setListener(Listener<Token> listener){
mListener = listener;
}

public ThumbnailDownloader(Handler responseHandler){
mResponseHandler = responseHandler;
}


// 定义handler如何处理消息
@SuppressLint(“HandlerLeak”)
@Override
protected void onLooerPrepared(){
mHandler = new Handler(){
@Override
public void handeMessage(Message msg){
if(msg.what == MESSAGE_DOWNLOAD){
@SuppressLint(“unchecked”)
Token token = (Token)msg.obj; // 由于类型擦除，因此这里的强制类型转换应该是不允许的，所以要添加@SuppressLint(“unchecked”)
handleRequest(token);
}	
}
}
}

private void handleRequest(final Token token){
try{
final String url = requestMap.get(token);
if(url == null) return;

byte[] bitmapBytes = new FlickrFetchr().getUrlBytes(url); 
final Bitmap bitmap = 				 
BitmapFactory.decodeByteArray(bitmapBytes,0,bitmapBytes.length);

// mResponseHandler和mListener都来自启动线程，即主线程
mResponseHandler.post(new Runnable(){
public void run(){
if(requestMap.get(token) != url) return;
requestMap.remove(token);
mListener.onThumbnailDownloaded(token,bitmap);  // 更新图片
}
}
}catch(IOException ioe){
...
}
}

// 清理
public void clearQueue(){
mHandler.removeMessages(MESSAGE_DOWNLOAD);
requestMap.clear();
}

// 该方法在启动线程中调用（在getView()中调用）
public void queueThumbnail(Token token,String url){
requestMap.put(token,url);

// 从公共循环池中获取一个Message对象，组装后，发送
mHandler.obtainMessage(MESSAGE_DOWNLOAD,token).sendToTarget();
}

}


定义用来加载图片的Fragment
public class PhotoGalleryFragment extends Fragment{
GridView mGridView;
ArrayList<GalleryItem> mItems;
ThumbnailDownloader<ImageView> mThumbnailThread;


@Override
public void onCreate(Bundle savedInstanceState){
super.onCreate(savedInstanceState);
setRetainInstance(true);
new FetchItemTask().fetchItems();  // 执行异步任务

// 主线程将自己的一个handler传递给子线程
mThumbnailThread = new ThumbnailDownloader<ImageView>(new Handler());

// 主线程将自己的回调接口对象传递给子线程
mThumbnailThread.setListener(new ThumbnailDownloader.Listener<ImageView>(){
public void onThumbnailDownloaded(ImageView imageView,Bitmap thumbnail){
if(isvisiable()){
imageView.setImageBitmap(thumbnail);
}
}
});
mThumbnailThread.start();
mThumbnailThread.getLooper();
}

@Override
public void onDestroy(){
super.onDestroy();
mThumbnailThread.quit(); 
}

@Override
public View onCreateView(LayoutInflater inflater,
ViewGroup container,Bundle savedInstanceState){
View v = inflater.inflate(R.layout.fragment_photo_gallery,container,false);
mGridView = (GridView)v.findViewById(R.id.gridView);

setupAdapter();

return v;
}

void setupAdapter(){
if(getActivity() == null || mGridView == null) return;
if(mItems != null){
// 这里没有定义ArrayAdapter的getView()方法，将调用toString(),展示文本
mGridView.setAdapter(new ArrayAdapter<GalleryItem>(
getActivity(),
android.R.layout.simple_gallery_item,
mItems));

mGridView.setAdapter(new GalleryItemAdapter(mItems));
}
else{
		mGridView.setAdapter(null);
}
}

// 实现GridView显示图片的Adapter
private class GalleryItemAdapter extends ArrayAdapter<GalleryItem>{
public GalleryItemAdapter(ArrayList<GalleryItem> items){
super(getActivity(),0,items);
)

// 覆盖getView方法
@Override
public View getView(int position,View convertView, ViewGroup parent){
if(convertView == null){
convertView = 
getActivity()
.getLayoutInflater()
.inflate(R.layout.gallery_item,parent,false);
}
// 在返回convertView之前,拿到其内部的ImageView的引用，传递给任务类
ImageView imageView = 			
		(ImageView)convertView.findViewById(R.id.gallery_item_imageView);
ImageView.setImageResource(R.drawable.xxx); // 设置一个默认图片，之后再动态替换

// 在getView中实现“按需”下载图片
GalleryItem item = getItem(position);

// 传递实体对象，通知任务类，任务类将基于实体对象发出消息
mThumbnailThread.queueThumbnail(imageView,item.getUrl()); 

return convertView;
}

// 基于AsyncTask，实现一个内部的异步任务工具类
private class FetchItemTask extends AsyncTask<void,void,ArrayList<GalleryItem>>{
@Override
protected void doInBackground(void...params){
try{
return new FlickrFetchr().fetchItems();
}catch(IOException ioe){
...
}
return null;
}

// doInBackground的返回值就是onPostExcute的输入值
onPostExcute方法运行在主线程上，且在doInBackground方法运行后执行，因此可以用来更新UI
@Override
protected void onPostExcute(ArrayList<GalleryItem> items){
mItems = items;
setupAdapter();
}
}

@Override
public void onDestroyView(){
super.onDestroyView();
mThumbnailThread.clearQueue();
}
}

```
定义加载图片Fragment的托管Activity
```java
public class PhotoGalleryActivity extends SingleFragmentActivity{
@Override
public Fragment createFragment(){
return new PhotoGalleryFragment();
}
}
```
修改Manifest，获取网络使用权限
<manifest xmlns:android=”...”
...
<uses-permission android:name=”android.permission.INTERNET” />
