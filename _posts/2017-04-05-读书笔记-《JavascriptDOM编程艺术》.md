---
layout: post
title:  "《JavaScript DOM编程艺术》读书笔记"
date: 2017-04-05 00:00:02
categories: 读书笔记
tags: JavaScript
excerpt: ""
---

* content
{:toc}

## 第1章 JavaScript简史
Web标准：HTML、CSS、DOM

ECMAScript是Javascript标准化后的名字，实际上就是Javascript。

DHTML：即“动态HTML（Dynamic HTML）”，它并不是一项新技术，而是描述HTML、CSS和Javascript技术组合的术语。

## 第2章 Javascript语法
在Javascript中，如果在对某个变量赋值之前未声明，赋值操作将自动声明该变量。

变量区分大小写。

Javascript是弱类型语言。

字符串使用单引号、双引号都可以。
 
支持两个布尔值：true、false。

定义数组：
var arr = Array(4);
var arr = Array();
var arr = Array(‘a’,’b’,’c’);
var arr = [‘a’,’b’,’c’];
var arr = Array(‘a’,100,false);

关联数组：
var arr = Array();
arr[‘name’] = ‘winston’;
arr[‘age’] = 17;
Javascript中所有的变量实际上都是某种类型的对象，以上实际是定义了一个Array的对象，并添加了2个属性。

创建对象
// 方法一
var lennon = Object();
lennon.name = ‘john’;
lennon.age = 20;

// 方法二
var lennon = { name:’john’, age:20 };

支持++、--操作符。

+操作符用于数值表示加运算，用于字符串表示连接字符串。用于字符串和数值时，会把数值转换为字符串然后连接。

因为相等操作符==认为空字符串与false的含义相同，想要进行严格比较则需要使用===。全等操作符不仅比较值，还比较类型。

如果在某个函数对变量使用了var，那个变量就将被视为是一个局部变量，它只存在于这个函数的上下文中。如果没有使用var，则这个变量将被视为是全局变量（作用域是整个脚本），将有可能造成变量污染。在定义函数时，应该把它内部的变量全都明确地声明为局部变量，以避免二义性。

对象主要有3类：自定义对象、Javascript内建对象（如Math、Array）、宿主对象（由浏览器提供）。


## 第3章 DOM
3种DOM节点
元素节点：<body>、<p>、<ul>等，元素节点的父元素必须是另一个元素节点。
文本节点：总是包含在元素节点内部，但并不是所有的元素节点都有文本节点。如：
<ul>
<li>一些文本</li>
<li>一些文本</li>
<ul>
ul这个元素节点不包含文本节点，而是包含两个li元素节点。li元素节点包含文本节点。
属性节点：元素的属性，总是放在元素的起始标签里。

3种获取DOM元素的方法：
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

获取和设置元素的属性：
getAttribute、setAttribute：不属于document对象的方法，只能通过元素节点对象调用

## 第4章 案例研究：Javascript图片库
DOM level
DOM级别0不是W3C规范。而仅仅是对在Netscape Navigator 3.0和Microsoft Internet Explorer 3.0中的等价功能性的一种定义。
DOM级别1专注于HTML和XML文档模型。它含有文档导航和处理功能。W3C 的 DOM 级别1建立于此功能性之上。
DOM级别2对DOM级别1添加了样式表对象模型，并定义了操作附于文档之上的样式信息的功能性。同时还定义了一个事件模型，并提供了对XML命名空间的支持。
DOM Level 3规定了内容模型 (DTD 和Schemas) 和文档验证。同时规定了文档加载和保存、文档查看、文档格式化和关键事件。DOM Level 3建立于DOM Core Level 2之上。

在onclick事件处理函数所触发的Javascript代码最后返回false，就可以防止在点击后的页面跳转：
onclick = “showPic(this); return false;”;

childNodes属性：获取任何一个元素的所有子元素。

nodeType属性：标识元素的类型，其值是一个数字，意义如下：
1：代表元素节点
2：代表属性节点
3：代表文本节点

nodeValue属性：用来得到和设置一个节点的值。如想得到一个<p>标签里面的文本的值，需要注意：<p>元素本身的nodeValue属性是一个空值，包含在<p>元素里的文本是另一种节点，它是<p>元素的第一个子节点，因此应该这样获取：alert(pnode.childNodes[0].nodeValue);

firstChild和lastChild属性：分别代表childNodes数组的第一个和最后一个元素。

## 第5章 最佳实践
平稳退化：让访问者在他们的浏览器不支持Javascript的情况下仍然能顺利地浏览你的网站。

javascript伪协议：通过一个链接来调用Javascript函数，这是一种非常不好的实践。
例：<a href=”javascript:popUp(‘http://www.site.com’);”>open</a>

内嵌事件处理函数方式：将链接设为空链接（‘#’），同时在调用的代码中返回false：
例：<a href=”#” onclick=”popUp(‘http://www.site.com’); return false;”>open</a>

以上两种用弹窗打开新页面的方法都不能平稳退化，较老的浏览器会尝试打开链接但失败，禁用了Javascript的浏览器（或者搜索引擎）则什么也不会做（即无法打开新页面）。改进的方法是：把href设置为真实的链接，从而为JavaScript代码预留出退路：
<a href=”http://www.site.com” onclick=”popUp(this.href); return false;”>open</a>
这样，当js被禁用时，页面仍然能够被打开。

外部js文件执行顺序：如果在<head>部分引用js文件，则会造成文档未加载，js无法操作DOM的情况。即使在</body>之前引用js，也不能确保js一定能够操作DOM，因为js加载时文档可能不完整。文档被加载到一个浏览器窗口里，document对象又是window对象的一个属性，因此当window对象触发onload事件时，document对象已经存在，所以为了确保js能够操作DOM，应该把相关方法绑定到window的onload事件上：
window.onload = foo;
function foo(){...}

检查当前浏览器是否支持某个Javascript方法：直接调用不带括号的方法名，如果方法存在则返回true，否则为false：
if(!getElementById) return false;

位于<head>块中的脚本会导致浏览器无法并行加载其他文件，如图像、其他脚本。因为根据HTTP规范，浏览器每次从同一个域名最多只能下载2个文件。把所有<script>放到</body>之前，可以让页面加载变得更快。


## 第6章 图片库改进版
该章是应用第5章的原则来改进第4章的内容。
略。


## 第7章 动态创建标记
document.write方法：
<body>
<script>
document.write(“insert text”);
</script>
</body>
该方法存在以下问题：
（1）违背了行为应该和表现分离的原则，即使把调用document.write的语句挪动到外部文件中，也还是需要在标记的<body>部分使用<script>标签才能调用那个函数：
<body>
<script src=”test.js”></script>
<script>
document.write(“insert text”);
</script>
</body>

（2）MIME类型application/xhtml+xml与document.write不兼容，浏览器在呈现这种文档时根本不会执行该函数。

innerHTML属性：该属性不是W3C DOM标准的组成部分，但是现在已经包含到了HTML5规范中。用来读、写给定元素里的HTML内容，适用于插入一大段HTML内容到文档，但不会返回任何对刚插入内容的引用，如果想对刚插入的内容进行处理，则需要使用DOM提供的那些精确方法和属性：
testdiv.innerHTML = “<p>insert text<em>this</em></p>”;

使用DOM方法创建新节点：createElement、appendChild、createTextNode：
var para = document.createElement(“p”);  // 创建元素节点
var txt = document.createTextNode(“p”);  // 创建文本节点
para.appendChild(txt);

var testdiv = document.getElementById(“testdiv”);
testdiv.appendChild(para);

insertBefore方法：把一个新元素插入到现有元素的前面
parentElement.insertBefore(newElement, targetElement);
但是DOM没有提供相应的insertAfter方法。

Ajax：Ajax的核心技术就是XMLHttpRequest对象，该对象使得JavaScript可以脱离浏览器来自己发送并响应HTTP请求。使用Ajax时要注意同源策略的限制，即：使用XMLHttpRequest对象发送的请求只能访问与其所在HTML处于同一个域中的数据。

## 第8章 充实文档的内容
本章实际是3实例，实现了以下3个函数：缩略语列表、文献来源链接、快捷键清单。

详略。

## 第9章 CSS-DOM
每个元素节点都有一个style属性，该属性包含着元素的样式，查询这个属性将返回一个对象而不是一个简单的字符串。样式都存放在这个style对象的属性里：
element.style.property

当引用一个中间带-号的CSS属性时，DOM要求使用驼峰命名法，如font-family对应为DOM属性fontFamily。

来自外部文件style.css，或者设置在<style>标签中的样式不能再用DOM的style属性来检索。style属性只包含在HTML代码里用style属性声明的样式。

className属性：与其使用DOM直接改变某个元素的样式，不如通过Javascript代码来更新这个元素的class属性。

## 第10章 用JavaScript实现动画效果
setTimeout函数：让某个函数在经过一段预定的时间之后才开始执行。
clearTimeout函数：用来取消“等待执行”队列里的某个函数，这个函数需要一个参数——保存着某个setTimeout函数调用返回值的变量：
movement = setTimeout(“moveMessage()”,5000);
clearTimeout(movement);

连续动画：使“message”元素以每次1像素的方式在浏览器窗口里移动，一旦这个元素的top和left属性同时等于100px和200px，这个函数就停止执行：
function moveMessage(){
var elem = document.getElementById(“message”);
var xpos = parseInt(elem.style.left);  // 把形如“50px”的字符串转换为数值50
var ypos = parseInt(elem.style.top);
if( xpos==200 && ypos==100 ){
return true;
}
if(xpos<200){
xpos++;
}
if(xpos>200){
xpos--;
}
if(ypos<100){
ypos++;
}
if(ypos>100){
ypos--;
}
elem.style.left = xpos+”px”;
elem.style.top = ypos+”px”;
movement = setTimeout(“moveMessage()”,10);
}

## 第11章 HTML5
Canvas、音频、视频、表单
详略。

## 第12章 综合示例
略。












