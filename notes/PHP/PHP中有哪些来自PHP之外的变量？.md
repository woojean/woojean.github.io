# PHP中有哪些来自PHP之外的变量？

来自PHP之外的变量：
1）HTML表单（GET和POST），根据特定的设置和个人的喜好，有很多种方法访问 HTML 表单中的数据。
2）HTTP Cookies

对于通过表单或者Cookies传进来的变量，PHP将会自动将变量名中的点（如果有的话）替换成下划线。
如：<input type="image" src="image.gif" name="sub" />，点击后，将会加上两个变量：sub_x和sub_y。它们包含了用户点击图像的坐标。（这里浏览器发出的是sub.x和sub.y）