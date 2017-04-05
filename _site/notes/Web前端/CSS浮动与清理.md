# CSS浮动与清理

## 浮动

浮动的框可以左右移动，直到它的外边缘碰到包含框或另一个浮动框的边缘。浮动框不在文档的普通流中。
![image](https://github.com/woojean/woojean.github.io/blob/master/images/css_4.png)
当框 1 向左浮动时，它脱离文档流并且向左移动，直到它的左边缘碰到包含框的左边缘。因为它不再处于文档流中，所以它不占据空间，实际上覆盖住了框 2，使框 2 从视图中消失：
![image](https://github.com/woojean/woojean.github.io/blob/master/images/css_5.png)
如果包含框太窄，无法容纳水平排列的三个浮动元素，那么其它浮动块向下移动，直到有足够的空间。如果浮动元素的高度不同，那么当它们向下移动时可能被其它浮动元素“卡住”：
![image](https://github.com/woojean/woojean.github.io/blob/master/images/css_6.png)

## 清理

浮动框旁边的行框被缩短，从而给浮动框留出空间，行框围绕浮动框。
因此，创建浮动框可以使文本围绕图像：
![image](https://github.com/woojean/woojean.github.io/blob/master/images/css_7.png)
要想阻止行框围绕浮动框，需要对该框应用clear属性。clear 属性定义了元素的哪边上不允许出现浮动元素。在 CSS1 和 CSS2 中，这是通过自动为清除元素（即设置了clear属性的元素）增加上外边距实现的。在 CSS2.1 中，会在元素上外边距之上增加清除空间，而外边距本身并不改变。不论哪一种改变，最终结果都一样，如果声明为左边或右边清除，会使元素的上外边框边界刚好在该边上浮动元素的下外边距边界之下。（即浏览器会自动添加上外边距）
![image](https://github.com/woojean/woojean.github.io/blob/master/images/css_8.png)