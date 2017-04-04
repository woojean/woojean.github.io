# 3种获取DOM元素的方法

getElementById：document对象所特有的函数，返回指定id的元素节点
document.getElementById(id); 

getElementsByTagName：可用于元素对象、可使用通配符、返回数组
document.getElementsByTagName(‘li’); 
可使用通配符，获取所有元素：
document.getElementsByTagName(‘*’); 
可用于元素对象：
var shopping = document.getElementById(‘purchases’);
var items = shopping.getElementsByTagName(‘*’);

getElementsByClassName：HTML5新增、根据class属性中的类名来访问元素、返回数组