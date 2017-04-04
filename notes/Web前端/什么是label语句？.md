# 什么是label语句？

使用 label 语句可以在代码中添加标签，以便将来使用。以下是 label 语句的语法：
label: statement
下面是一个示例：
start: for (var i=0; i < count; i++) {
alert(i);
}
这个例子中定义的 start 标签可以在将来由 break 或 continue 语句引用。加标签的语句一般都
要与 for 语句等循环语句配合使用。