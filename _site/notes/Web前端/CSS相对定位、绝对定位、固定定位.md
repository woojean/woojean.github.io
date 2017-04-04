# CSS相对定位、绝对定位、固定定位

## 相对定位

如果对一个元素进行相对定位，它将出现在它所在的位置上，然后可以通过设置top、left等属性让这个元素相对于它的起点移动。无论是否移动，元素仍然占据原来的空间，因此移动元素会导致它覆盖其他框。相对定位实际上是普通流定位模型的一部分。
![image](https://github.com/woojean/woojean.github.io/blob/master/images/css_2.png)

## 绝对定位

绝对定位的元素的位置是相对于距离它最近的那个已定位的祖先元素确定的，如果没有已定位的祖先元素，那么它的位置是相对于初始包含块的。元素定位后生成一个块级框，而不论原来它在正常流中生成何种类型的框。绝对定位使元素的位置与文档流无关。
![image](https://github.com/woojean/woojean.github.io/blob/master/images/css_3.png)

## 固定定位

相对于viewport进行定位。