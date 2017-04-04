# 匿名函数的中的this指向哪里？

this 对象是`在运行时基于函数的执行环境绑定的`：在全局函数中， this 等于 window ，而当函数被作为某个对象的方法调用时， this 等于那个对象。不过，`匿名函数的执行环境具有全局性`，因此其 this 对象通常指向 window。

```
var name = "The Window";
var object = {
  name : "My Object",
  getNameFunc : function(){
    return function(){
      return this.name;
    };
  }
};
alert(object.getNameFunc()()); //"The Window"（在非严格模式下）
```

把外部作用域中的 this 对象保存在一个闭包能够访问到的变量里，就可以让闭包访问该对象了
```
var name = "The Window";
var object = {
  name : "My Object",
  getNameFunc : function(){
    var that = this;
    return function(){
      return that.name;
    };
  }
};
alert(object.getNameFunc()()); //"My Object"
```