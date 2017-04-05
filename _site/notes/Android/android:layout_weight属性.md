# android:layout_weight属性

layout_weight属性用于LinearLayout进行子组件的布局安排。在决定子组件视图的宽度（横向布局时）时，LinearLayout使用的是layout_width与layout_weight参数的混合值，具体分为以下两步：
1.LinearLayout查看layout_weight属性值（竖直方向则是layout_height属性），比如两个空间的layout_width都设置为wrap_content，则依他们的实际内容的宽度绘制控件。
2.LinearLayout依据layout_weight属性值进行额外的空间分配，若两者layout_weight属性相同，则均分剩余空间，如一个空间layout_weight为2，另一个为1，则为2的控件获得2/3的剩余空间，为1的控件获得1/3的剩余空间。
如果想让控件占据相同的宽度，可以将layout_weight设为0dp，将layout_weight设为一样。

使用ListFragment显示列表
自定义一个继承于android.v4.app.ListFragment的类，该类默认生成一个ListView布局，因此无需覆盖onCreateView()方法，或为其生成布局。

创建一个托管该ListFragment的Activity类，方法与添加一般的Fragment一样。

在自定义ListFragment的onCreate()方法中实现一个ArrayAdapter<T>，并使用setListAdapter()方法进行设置。实现ArrayAdapter有两种方式，一是通过指定Item的视图文件来定义每一项的视图，二是自定一个继承自ArrayAdapter的类，重写其getView()方法，该方法基于实际的数据集来映射出具体的视图。

重写ListFragment的onListItemClick()方法，设置点击列表项时的响应事件。该方法会传入一个position参数用以判断所点击的具体项。

之后，数据有更新时，则修改底层getview()方法所使用的数据集，然后调用ListAdapter的notifyDataSetChanged()方法,通常在Fragment的onResume()方法中调用。
## Fragment Argument
每个Fragment实例都可附带一个Bundle对象，该bundle对象包含key-value对，一个key-value对即是一个argument。通过调用Fragment.setArguments(Bundle)方法进行设置，该任务必须在fragment创建后、添加给activity之前完成。通常的做法是实现一个newInstance()方法来封装以上行为：
```java
public class CrimeFragment extends Fragment{

@Override
public void onCreate(Bundle savedInstanceState){
super.onCreate(savedInstanceState);
// 获取argument
UUID crimeId = (UUID)getArguments.getSerializable(EXTRA_CRIME_ID); 
}

public static CrimeFragment newInstance(UUID crimeId){
Bundle args = new Bundle();
args.putSerializable(EXTRA_CRIME_ID,crimeId);
CrimeFragment fragment = new CrimeFragment();
fragment.setArgument(args);
return fragment;
}
...
}
```
在Activity中调用newInstance()方法
```java
public class CrimeActivity extends SingleFragmentActivity{
@Override
protected Fragment createFragment(){
UUID crimeId = (UUID)getIntent().getSerializableExtra(CrimeFragment.EXTRA_CRIME_ID);
return CrimeFragment.newInstance(crimeId);
}
...
}
```
Fragment也有startActivityForResult(...)和onActivityResult(...)方法，但没有setResult(...)方法。