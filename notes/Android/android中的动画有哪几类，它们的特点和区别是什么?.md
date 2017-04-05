# android中的动画有哪几类，它们的特点和区别是什么?

Android提供两种创建简单动画的机制：tweened animation（补间动画） 和 frame-by-frame animation（帧动画）.
•tweened animation：通过对场景里的对象不断做图像变换(平移、缩放、旋转)产生动画效果
•frame-by-frame animation：顺序播放事先做好的图像，跟电影类似
这两种动画类型都能在任何View对象中使用，用来提供简单的旋转计时器，activity图标及其他有用的UI元素。Tweened animation被andorid.view.animation包所操作；frame-by-frame animation被android.graphics.drawable.AnimationDrawable类所操作。
想了解更多关于创建tweened和frame-by-frame动画的信息，读一下Dev Guide-Graphics-2D Graphics里面相关部分的讨论。Animation 是以 XML格式定义的，定义好的XML文件存放在res/anim中。