# 读文档笔记-《Redux文档》

Redux是JavaScript`状态容器`，提供`可预测化的状态管理`。所有的state都以一个对象树的形式储存在一个`单一的store`中。
惟一改变state的办法是触发action，一个描述发生什么的对象。
为了描述action如何改变state树，需要编写reducers。
只有一个单一的store和一个根级的reduce函数（reducer）。随着应用不断变大，应该把根级的reducer拆成多个小的reducers，分别独立地操作state树的不同部分，而不是添加新的stores。

## 动机
Redux试图让state的变化变得可预测。


## 三大原则
1.单一数据源
整个应用的state被储存在一棵object tree中，并且这个object tree只存在于唯一一个store中。

2.惟一改变state的方法就是触发action。
这样确保了视图和网络请求都不能直接修改state，相反它们只能表达想要修改的意图。
因为所有的修改都被集中化处理，且严格按照一个接一个的顺序执行，因此不用担心race condition的出现。

3.使用`纯函数`来执行修改
编写reducer函数来描述action如何改变state tree。


### 先前技术
Flux、Elm、Immutable、Baobab、Rx















































