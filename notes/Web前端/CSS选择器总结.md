# CSS选择器总结

`类型选择器`、`元素选择器`、`简单选择器`：
```css
p {color:black;}
```

`后代选择器`：
```css
blockquote p{color:black;}
```

`ID选择器`：
```css
#intro{color:black;}
```

`类选择器`：
```css
.intro{color:black;}
```

`伪类`：根据文档结构之外的其他条件对元素应用样式，例如表单元素或链接的状态
```css
tr:hover{background-color:red;}
input:focus{background-color:red;}
a:hover,a:focus,a:active{color:red;}
```
:link和:visited称为链接伪类，只能应用于锚元素。:hover、:active和:focus称为动态伪类，理论上可以应用于任何元素。
可以把伪类连接在一起，创建更复杂的行为：
```css
a:visited:hover{color:red;}
```

`通用选择器`：匹配所有可用元素
```css
*{
padding:0;
margin:0;
}
```
通用选择器与其他选择器结合使用时，可以用来对某个元素的所有后代应用样式。

`子选择器`：只选择元素的直接后代，而不是像后代选择器一样选择元素的所有后代。
```css
#nav>li{
padding-left:20px;
color:red;
}
```

`相邻同胞选择器`：用于定位同一个父元素下与某个元素相邻的下一个元素。
```css
h2 + p{
font-size:1.4em;
}
```

`属性选择器`：根据某个属性是否存在或者属性的值来寻找元素。
```css
abbr[title]{
border-bottom:1px dotted #999;
}

abbr[title]:hover{
cursor:help;
}

a[rel=’nofollow’]{
color:red;
}
```
注意：属性名无引号，属性值有引号。

对于属性可以有多个值的情况（空格分割），属性选择器允许根据属性值之一来寻找元素：
```css
.blogroll a[rel~=’co-worker’]{...}
```