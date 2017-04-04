# JavaScript中的函数拥有哪些属性和方法？

arguments  	//包含着传入函数中的所有参数。
// 这个对象还有一个名叫 callee 的属性，该属性是一个指针，指向拥有这个 arguments 对象的函数

this 	    // 指向函数据以执行的`环境对象`
caller      // 保存着调用当前函数的函数的引用
length      // 表示函数希望接收的命名参数的个数
prototype   // （所有引用类型）保存所有实例方法的真正所在, prototype 属性是不可枚举的，因此使用 for-in 无法发现

apply()     // 在特定的作用域中调用函数，接收两个参数：一个是在其中运行函数的作用域，另一个是参数数组
call()      // 传递给函数的参数必须逐个列举出
bind()      // 创建一个函数的实例，其 this 值会被绑定到传给 bind() 函数的值

函数继承的 toLocaleString() 和 toString() ，valueOf()方法始终都返回函数的代码