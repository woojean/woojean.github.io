# JavaScript中__proto__与prototype的关系

__proto__ 对象的内部原型
prototype 构造函数的原型

1.所有函数/构造函数（包括内置的、自定义的）的__proto__都指向Function.prototype，它是一个空函数（Empty function）
  Number.__proto__ === Function.prototype  // true

  Global对象的__proto__不能直接访问；
  Arguments对象仅在函数调用时由JS引擎创建；
  Math，JSON是以对象形式存在的，无需new，它们的__proto__是Object.prototype：
  JSON.__proto__ === Object.prototype  // true

2.构造函数都来自于Function.prototype，包括Object及Function自身，因此都继承了Function.prototype的属性及方法。如length、call、apply、bind等

3.Function.prototype也是唯一一个typeof XXX.prototype为 “function”的prototype。其它的构造器的prototype都是一个对象：
  console.log(typeof Function.prototype) // function  一个空函数
  console.log(typeof Object.prototype)   // object
  console.log(typeof Number.prototype)   // object

4.Function.prototype的__proto__等于Object的prototype：
  console.log(Function.prototype.__proto__ === Object.prototype) // true  体现了在Javascript中`函数也是一等公民`

5.Object.prototype的__proto__为null
  Object.prototype.__proto__ === null  // true  到顶了

6.所有对象的__proto__都指向其构造器的prototype
  var obj = {name: 'jack'}
  var arr = [1,2,3]
  var reg = /hello/g

  console.log(obj.__proto__ === Object.prototype) // true
  console.log(arr.__proto__ === Array.prototype)  // true
  console.log(reg.__proto__ === RegExp.prototype) // true

  function Person(name) {
    this.name = name
  }
  var p = new Person('jack')
  console.log(p.__proto__ === Person.prototype) // true

7.每个对象都有一个`constructor属性`，可以获取它的构造器
  function Person(name) {
    this.name = name
  }

  Person.prototype.getName = function() {}  // 修改原型
  var p = new Person('jack')
  console.log(p.__proto__ === Person.prototype) // true
  console.log(p.__proto__ === p.constructor.prototype) // true

8.使用对象字面量方式定义对象的构造函数，则对象的constructor的prototype可能不等于对象的__proto__
  function Person(name) {
    this.name = name
  }
  // 使用对象字面量方式定义的对象其constructor指向Object，Object.prototype是一个空对象{}
  Person.prototype = {
    getName: function() {}
  }
  var p = new Person('jack')
  console.log(p.__proto__ === Person.prototype) // true
  console.log(p.__proto__ === p.constructor.prototype) // false