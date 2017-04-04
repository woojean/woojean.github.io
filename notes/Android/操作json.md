# 操作json

定义一个支持json转换的类：

```java
public class Crime{
private static final String JSON_ID = “id”;
private static final String JSON_TITLE = “title”;
private static final String JSON_DATE = “date”;

private UUID mID;
private String mTitle;
private Date mDate = new Date();

public Crime(){
mId = UUID.randomUUID();
}

// json对象转换为属性
public Crime(JSONObject json) throws JSONException{
mId = UUID.fromString(json.getString(JSON_ID));
if(json.has(JSON_TITLE)){
mTitle = json.getString(JSON_TITLE);
}
mDate = new Date(json.getLong(JSON_DATE));
}

// 属性转换成json对象
public JSONObject toJSON() throws JSONException{
JSONObject json = new JSONObject();
json.put(JSON_ID,mId.toString());
json.put(JSON_TITLE,mTitle);
json.put(JSON_DATE,mDate.getTime());
return json;
}
}
```
基于支持json转换的类，实现json文件互转的类：
```java
public class CriminalIntentJSONSerializer{
private Context mContext;
private String mFilename;
public CriminalIntentJSONSerializer(Context c, String f){
mContext = c;
mFilename = f;
}

// 将类实例对象的数组转换为JSONObject的数组，再调用JSONObject的toString()方法转换为字符串，然后写入文件中
public void saveCrimes(ArrayList<Crime> crimes) throws JSONException,IOException{
JSONArray array = new JSONArray();
for(Crime c:crimes)
array.put(c.toJSON());
Writer writer = null;
try{
OutputStream out = mContext.openFileOutput(mFilename,Context.MODE_PRIVATE);
writer = new OutputStreamWriter(out);
writer.write(array.toString());
}
finally{
if(writer != null)
writer.close();
}
}

// 读取json文件，拼接为字符串，解析为JSONArray，再转换为所需的类的实例
public ArrayList<Crime> loadCrimes() throws JSONException,IOException{
ArrayList<Crime> crimes = new ArrayList<Crime>();
BufferedReader reader = null;
try{
InputStream in = mContext.openFileInput(mFilename);
reader = new BufferdReader(new InputStreamReader(in));
StringBuilder jsonString = new StringBuilder();
String line = null;
while((line = reader.readline())!=null){
jsonString.append(line);
}
JSONArray array = (JSONArray)new JSONTokener(jsonString.toString()).nextValue(); // JSONTokener是json文本解析类
for(int i=0; i<array.length(); i++){
crimes.add(new Crime(array.getJSONObject(i)));
}
}
catch(FileNotFoundException e){
...
}
finally{
if(reader != null)
reader.close();
}
retuen crimes;
}
}
```