# JavaScript数组的length属性有什么特点？

数组的项数保存在其 length 属性中。如果设置某个值的索引超过了数组现有项数，数组就会自动增加到该索引值加 1 的长度：
var colors = ["red", "blue", "green"]; // 定义一个字符串数组
alert(colors[0]); // 显示第一项
colors[2] = "black"; // 修改第三项
colors[3] = "brown"; // 新增第四项

length 属性不是只读的。因此，通过设置这个属性，可以从数组的末尾移除项或向数组中添加新项：
var colors = ["red", "blue", "green"]; // 创建一个包含 3 个字符串的数组
colors.length = 2;
alert(colors[2]); //undefined

var colors = ["red", "blue", "green"]; // 创建一个包含 3 个字符串的数组
colors.length = 4;
alert(colors[3]); //undefined