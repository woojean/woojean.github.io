# Android常用控件的信息

单选框(RadioButton与RadioGroup)：
RadioGroup用于对单选框进行分组，相同组内的单选框只有一个单选框被选中。
事件：setOnCheckedChangeListener()，处理单选框被选择事件。把RadioGroup.OnCheckedChangeListener实例作为参数传入。
多选框(CheckBox):
每个多选框都是独立的，可以通过迭代所有的多选框，然后根据其状态是否被选中在获取其值。
事件：setOnCheckedChangeListener()，处理多选框被选择事件。把CheckBox.OnCheckedChangeListener()实例作为参数传入。
下拉列表框(Spinner)：
Spinner.getItemAtPosition(Spinner.getSelectedItemPosition());获取下拉列表框的值。
事件：setOnItemSelectedListener(),处理下拉列表框被选择事件把Spinner.OnItemSelectedListener()实例作为参数传入。
拖动条(SeekBar)：
SeekBar.getProgress()获取拖动条当前值
事件:setOnSeekBarChangeListener()，处理拖动条值变化事件，把SeekBar.OnSeekBarChangeListener实例作为参数传入。
菜单(Menu):
重写Activity的onCreatOptionMenu(Menu menu)方法，该方法用于创建选项菜单，当用户按下手机的"Menu"按钮时就会显示创建好的菜单，在onCreatOptionMenu(Menu Menu)方法内部可以调用Menu.add()方法实现菜单的添加。
重写Activity的onMenuItemSelected()方法，该方法用于处理菜单被选择事件。
进度对话框(ProgressDialog)：
创建并显示一个进度对话框：ProgressDialog.show(ProgressDialogActivity.this,"请稍等"，"数据正在加载中...."，true)；
设置对话框的风格：setProgressStyle()
ProgressDialog.STYLE_SPINNER  旋转进度条风格(为默认风格)
ProgressDialog.STYLE_HORIZONTAL 横向进度条风格
下面是各种常用控件的事件监听的使用
①EditText（编辑框）的事件监听---OnKeyListener
②RadioGroup、RadioButton（单选按钮）的事件监听---OnCheckedChangeListener
③CheckBox（多选按钮）的事件监听---OnCheckedChangeListener
④Spinner（下拉列表）的事件监听---OnItemSelectedListener
⑤Menu（菜单）的事件处理---onMenuItemSelected
⑥Dialog（对话框）的事件监听---DialogInterface.OnClickListener()