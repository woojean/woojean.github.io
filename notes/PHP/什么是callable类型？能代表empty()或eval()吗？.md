# 什么是callable类型？能代表empty()或eval()吗？

自PHP 5.4起可用callable类型指定回调类型callback。一个PHP的函数以string类型传递其名称。可以使用任何内置或用户自定义函数，但除了语言结构，例如：array()，echo，empty()，eval()，exit()，isset()，list()，print 或 unset()。

除了普通的用户自定义函数外，create_function()可以用来创建一个匿名回调函数。