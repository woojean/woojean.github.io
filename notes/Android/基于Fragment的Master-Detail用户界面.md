# 基于Fragment的Master-Detail用户界面

为了保持fragment的独立性，可以在fragment中定义回调接口，委托托管它的activity来完成那些不应该由fragment处理的任务。托管activity将实现回调接口，履行托管fragment的任务。有了回调接口，fragment可以直接调用托管activity的方法，而无需知道自己的托管者是谁。

具体到本例，就是为CrimeListFragment添加回调接口，然后在托管它的Activity中实现这个接口。这样，当CrimeListFragment中的某个列表项被点击时，可以调用其托管Activity添加相应的CrimeFragment（即添加Detail信息）。

为CrimeListFragment类添加回调接口：
```java
public class CrimeListFragment extends ListFragment{
private ArrayList<Crime> mCrime;
private boolean mSubtitleVisible;
private Callbacks mCallbacks;

public interface Callbacks{  // 定义一个回调接口
void onCrimeSelected(Crime crime);
}

@Override
public void onAttach(Activity activity){
super.onAttach(activity);
mCallbacks = (Callbacks)activity; // 因为托管Activity实现了该接口，所以可以转型
}

@Override
public void onDetach(){
super.onDetach();
mCallbacks = null;
}

public void onListItemClick(ListView l,View v,int position,long id){
Crime c = ((CrimeAdapter)getListAdapter()).getItem(position);
mCallbacks.onCrimeSelected(c);  // 触发回调
}
...
}

Activity实现回调接口
public class CrimeListActivity extends SingleFragmentActivity
implements CrimeListFragment.Callbacks{

@Override
protected Fragment createFragment(){
return new CrimeListFragment();
}

@Override
protected int getLayoutResId(){
return R.layout.activity_masterdetail;
}

public void onCrimeSelected(Crime crime){  //  实现接口方法
if(findViewById(R.id.detailFragmentContainer) == null){ // 手机布局
Intent i = new Intent(this,CrimePagerActivity.class);
i.putExtra(CrimeFragment.EXTRA_CRIME_ID,crime.getId());
startActivity(i);
}
else{ // 平板布局
FragmentManager fm = getSupportFragmentManager();
FragmentTransaction ft = fm.beginTransaction();

Fragment oldDetail = fm.findFragmentById(R.id.detailFragmentContainer);
Fragment newDetail = CrimeFragment.newInstance(crime.getId());
if(oldDetail!=null){
ft.remove(oldDetail); // 去掉旧的Detail
}
ft.add(R.id.detailFragmentContainer,newDetail);  // 添加新的Detail
ft.commit();
}
}
}
```
同理，如果想实现在Detail中修改信息，比如标题，提交后列表项也响应更新，只需在CrimeFragment中也定义一个内部接口，然后Activity同时再实现这个接口。当CrimeFragment中的更新操作发生时，主动通过mCallbacks对象触发Activity的UI更新操作（notifyDataSetChanged）。
总而言之，这是一种模式：父对象想要根据子对象的变化来更新自己，则需要在子对象中定义回调接口，父对象实现回调接口。子对象通过某种方式可以获取到父对象的引用，并把它转型为回调接口。当子对象的变化发生时，主动通过回调接口对象来触发父对象的相应的变化。