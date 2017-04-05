# 基于Fragment的对话框及同一个Activity的两个Fragment之间的数据传递

Android推荐的做法是将对话框，比如一个AlertDialog，封装在DialogFragment实例中，以通过FragmentManager来管理对话框，而不是直接显示。使用FragmentManager管理对话框可使用更多配置选项来显示对话框，另外如果设备发生旋转，独立配置使用的AlertDialog会在旋转后消失，而配置封装在fragment中的AlertDialog则不会有此问题。

要将DialogFragment添加给FragmentManager管理，并放到屏幕上，可调用以下两种方法：
public void show(FragmentManager manager, String tag)  // 事务可自动创建并提交
public void show(FragmentTransaction transaction, String tag)

从一个Fragment中打开另一个Fragment，并传值，使用Fragment Arguments。
从被打开的Fragment中返回数据，需要调用setTargetFragment()设置目标Fragment,并主动去调用目标Fragment的onActivityResult()方法，其效果类似Activity的sendResult()方法，但是Fragment没有该方法，因此可以自定义一个来实现同样的功能。

以在一个Fragment上点击弹出日期选择的对话框为例：
```java
// 定义一个DialogFragment的子类，重写其onCreateDialog()方法，返回一个对话框
public class DatePickerFragment extends DialogFragment{
@Override
public Dialog onCreateDialog(Bundle savedInstanceState){
View v = getActivity().getLayoutInflater().inflater(R.layout.dialog_date,null);
return new AlertDialog.Builder(getActivity())
.setView(v)  // 设置对话框的显示内容，如一个根节点为DatePicker的布局文件
.setTitle(‘...’)
	.setPositiveButton(
android.R.string.ok,
new DialogInterface.OnClickListener(){
public void onClick(DialogInterface dialog,int which){
sendResult(Activity.RESULT_OK);
}
}
)
.create();
}

// 模拟Activity的sendResult()方法
private void sendResult(int resultCode){  
if(getTargetFragment() == null)
return;

Intent i = new Intent();
i.putExtra(EXTRA_DATE,mDate);  // 设置返回值，mDate的值通过重写DatePicker的onDateChanged()方法进行设置
getTargetFragment().onActivityResult(getTargetRequestCode(),resultCode,i);
}
}

// 触发显示对话框
public class CrimeFragment extends Fragment{
...
@Override
public View onCreateView(LayoutInflater inflater, ViewGroup parent,Bundle savedInstanceState){
mDateButton = (Button)findViewById(R.id.crime_date);
mDateButton.setOnClickListener(new View.OnClickListener(){
public void onClick(View v){
FragmentManager fm = getActivity().getSupportFragmentManager();
DatePickerFragment dialog = new DatePickerFragment();
dialog.setTargetFragment(CrimeFragment.this,REQUEST_DATE);
dialog.show(fm,DIALOG_DATE);
}
}); 
...
}

@Override
public void onActivityResult(int requestCode, int resultCode, Intent data){
// 接受、处理从Fragment中返回的数据
}
}
```