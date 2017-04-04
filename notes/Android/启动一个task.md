# 启动一个task

可以设置一个activity为一个任务的入口，通过给它一个值为"android.intent.action.MAIN"的intent过滤器"和一个值为"android.intent.category.LAUNCHER"的过滤器．例如：

```xml
<activity... >
<intent-filter... >
<actionandroid:name="android.intent.action.MAIN" />
<categoryandroid:name="android.intent.category.LAUNCHER" />
</intent-filter>
...
</activity>
```
一个intent这种类型的过滤器导致activity的一个图标和标签被显示于应用启动界面上．使得用户可以启动这个activity并且再次回到这个任务．
这第二个能力是很重要的：用户必须能离开一个任务并且之后还能通过启动器回来．为此，两种使得activity永远在新任务中启动的启动模式："singleTask"和"singleInstance"，应该只在当activity具有ACTION_MAIN和CATEGORY_LAUNCHER过滤器时使用．想像一下，例如，如果没有这些过滤器将会发生什么：一个intent启动一个"singleTask"activity，在一个新的任务中初始化，并且用户在这个任务中忙乎了一些时间．然后用户按下HOME按钮．任务现在被移到后台并且不可见了．因为这个activity不是应用的启动activity，用户就再也没有办法回到这个任务了．
但遇到那些你不希望用户能够回到一个activity的情况时怎么办呢？有办法：设置<activity>元素的finishOnTaskLaunch属性为"true"!