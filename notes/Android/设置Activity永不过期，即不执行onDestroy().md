# 设置Activity永不过期，即不执行onDestroy()

```java
/** 重写finish()方法 */
@Override
public void finish() {
    //super.finish(); //记住不要执行此句
    moveTaskToBack(true); //设置该activity永不过期，即不执行onDestroy()
}    
```
注意：不要调用super.finish()，只需调用moveTaskToBack(true)就可以，这样只有在第一次启动的时候会执行onCreate()。以后(只要进程不死掉)都不会执行onCreate()
方法：public boolean moveTaskToBack(boolean nonRoot)
activity里有这个方法，参数说明如下：
nonRoot=false→ 仅当activity为task根（即首个activity例如启动activity之类的）时才生效
nonRoot=true→ 忽略上面的限制
这个方法不会改变task中的activity中的顺序，效果基本等同于home键
应用场景：
比如有些activity诸如引导图之类的，用户在按返回键的时候你并不希望退出（默认就finish了），而是只希望置后台，就可以调这个方法。