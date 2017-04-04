# 使用ViewPager展示Fragment

使用ViewPager配合PagerAdapter（实际是其子类FragmentPagerAdapter或FragmentStatePagerAdapter）来实现Fragment的切换，而不是使用AdapterView（AdapterView有一个和ViewPager类似的子类Gallery）与Adapter来实现，是因为：AdapterView无法使用现有的Fragment，需要编写代码及时地提供View，然而决定fragment视图何时创建的是FragmentManager，所以当Gallery要求Adapter提供fragment视图时，我们无法立即创建fragment并提供视图。PagerAdapter比Adapter复杂很多，因为其要处理更多的视图管理相关工作，其中代替使用getView()方法，PagerAdapter使用下列方法：

```java
public Object instantiateItem(ViewGroup container,int position);
public void destroyItem(ViewGroup container,int position,Object object);
public abstract boolean isViewFromObject(View view,Object object);
```

使用ViewPager时，只需为其设置PagerAdapter，并重写getCount()和getItem()这两个方法即可：
```java
public class CrimePagerActivity extends FragmentActivity{
private ViewPager mViewPager;
Private ArrayList<Crime> mCrimes;

@Override
public void onCreate(Bundle savedInstanceState){
super.onCreate();
mViewPager = new ViewPager(this);
mViewPager.setId(R.id.viewPager);  // 
```
FragmentManager要求任何作为fragment的容器的视图都需要具有一个资源ID，可在res/values/下新建一个ids.xml，写入以下内容：
<resources xmlns:android=’...’>
<item type=”id” name=”viewPager”/>
</resources>
setContentView(mViewPager);  //  以代码方式定义视图，而不是传入一个布局的ID

mCrimes = CrimeLab.get(this).getCrimes();

FragmentManager fm = getSupportFragmentManager();
mViewPager.setAdapter(new FragmentStatePagerAdapter(fm){ //这里设置了和fm的关系，相当于委托它来负责具体的Fragment视图添加工作
```java
@Override
public int getCount(){ 
return mCrimes.size();
}

@Override
public Fragment getItem(int pos){ // 返回一个Fragment
Crime crime mCrime.get(pos);
return CrimeFragment.newInstance(crime.getId());  
}
});
}
}
```
除了FragmentStatePagerAdapter外，还有一个可用的PagerAdapter的子类FragmentPagerAdapter。两者的区别在于在卸载不再需要的fragment时，所采用的处理方法不同。FragmentStatePagerAdapter不会销毁掉不需要的fragment，在销毁fragment时，会将其onSaveInstanceState(Bundle)方法中的Bundle信息保存下来，用户切换回原来的页面后，保存的实例的状态可用于恢复生成新的fragment。而FragmentPagerAdapter对于不再需要的fragment则选择调用事务的detach(Fragment)方法，而非remove(Fragment)方法来处理，即只是销毁了fragment的视图，但仍将fragment实例保留在FragmentManager中，因此用FragmentPagerAdapter创建的fragment永远不会被销毁。通常来说，FragmentStatePagerAdapter更加节省内存，对于包含图片等内容比较大的fragment，最好选用FragmentStatePagerAdapter。
ViewPager默认加载当前屏幕上的列表项以及左右相邻页的数据，从而实现页面滑动的快速切换，可以通过调用setOffscreenPageLimit(int)方法来指定预加载相邻页面的数目。
ViewPager默认显示PageAdapter中的第一个列表项，可通过调用setCurrentItem()方法来设置具体项。