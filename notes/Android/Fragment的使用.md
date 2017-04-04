# Fragment的使用

Fragment在API 11以后才被引入到标准库。之前的版本要想使用Fragment，必须使用Android支持库中的android.support.v4.app.Fragment并配合android.support.v4.app.FragmentActivity类来使用。

Fragment不能单独呈现UI，他必须托管于Activity才能使用，有两种托管Fragment的方式：
1.添加fragment到activity布局中；（布局文件方式，在Activity的生命周期中无法切换fragment视图）
2.在activity代码中添加fragment；（代码方式，可以在运行时控制fragment）

添加Fragment到Activity的步骤：
1.定义Fragment的视图文件
2.新建继承于Fragment的自定义类，重写其onCreateView方法，渲染布局文件，设置布局中各个控件的回调函数，最后返回该视图：
```java
public class CrimeFragment extends Fragment{
@Override
public View onCreateView(LayoutInflater inflater,ViewGroup parent,Bundle savedInstanceState){
View v = inflater.inflate(R.layout.fragment_crime,parent,false);
mTitleField = (EditText)v.findViewById(R.id.xxx);
mTitleField.addTextChangedListener(
new TextWatcher(){
public void onTextChanged(CharSequence c, int start, int before, int count){
...
};
}
);
return v;
}
}
```
3.在Activity中通过FragmentManager将Fragment添加到Activity的布局中：
```java
public class CrimeActivity extends FragmentActivity{
@Override
public void onCreate(Bundle savedInstanceState){
super.onCreate(savedInstanceState);
setContentView(R.layout.activity_xxx);

FragmentManager fm = getSupportFragmentManager();
Fragment fragment = fm.findFragmentById(R.id.fragmentContainer);
if(fragment == null){ // 判断是否已存在该Fragment
fragment = new CrimentFragment();
fm.beginTransaction().add(R.id.fragmentContainer,fragment).commit();
}
}
}
```