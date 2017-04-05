# JavaScript中有哪些实现继承的方式？

借用构造函数
在子类型构造函数的内部调用超类型构造函数：

```
function SuperType(){
  this.colors = ["red", "blue", "green"];
}
function SubType(){
  SuperType.call(this);
}
var instance1 = new SubType();
instance1.colors.push("black");
alert(instance1.colors); //"red,blue,green,black"
var instance2 = new SubType();
alert(instance2.colors); //"red,blue,green"
```

借用构造函数有一个很大的优势，即可以在子类型构造函数中向超类型构造函数传递参数：
```
function SuperType(name){
  this.name = name;
}
function SubType(){
SuperType.call(this, "Nicholas");
  this.age = 29; // 实例属性
}
var instance = new SubType();
alert(instance.name); //"Nicholas";
alert(instance.age); //29
```
如果仅仅是借用构造函数，那么也将无法避免构造函数模式存在的问题——方法都在构造函数中定义，因此函数复用就无从谈起了。

组合继承
也叫做伪经典继承，指的是将原型链和借用构造函数的技术组合到一块，从而发挥二者之长的一种继承模式。思路是使用原型链实现对原型属性和方法的继承，而通过借用构造函数来实现对实例属性的继承。
组合继承最大的问题就是无论什么情况下，都会调用两次超类型构造函数：一次是在创建子类型原型的时候，另一次是在子类型构造函数内部：
```
function SuperType(name){
  this.name = name;
  this.colors = ["red", "blue", "green"];
}
SuperType.prototype.sayName = function(){
  alert(this.name);
};
function SubType(name, age){
  SuperType.call(this, name); // 第二次调用 SuperType()
  this.age = age;
}
SubType.prototype = new SuperType(); // 第一次调用 SuperType()
SubType.prototype.constructor = SubType;
SubType.prototype.sayAge = function(){
  alert(this.age);
};
```
当调用 SubType 构造函数时，又会调用一次 SuperType 构造函数，这一次又在新对象上创建了实例属性 name 和 colors 。于是，这两个属性就屏蔽了原型中的两个同名属性。

原型式继承
```
function object(o){
  function F(){
}
F.prototype = o;
  return new F();
}
```
ECMAScript 5 通过新增 Object.create() 方法规范化了原型式继承。这个方法接收两个参数：一个用作新对象原型的对象和（可选的）一个为新对象定义额外属性的对象。在传入一个参数的情况下，Object.create() 与 object() 方法的行为相同。
```
var person = {
  name: "Nicholas",
  friends: ["Shelby", "Court", "Van"]
};

var anotherPerson = Object.create(person, {
  name: {
    value: "Greg"
  }
});
alert(anotherPerson.name); //"Greg"
```

寄生式继承
创建一个仅用于封装继承过程的函数，该函数在内部以某种方式来增强对象，最后再像真地是它做了所有工作一样返回对象。
```
function createAnother(original){
  var clone = object(original); //通过调用函数创建一个新对象
  clone.sayHi = function(){ //以某种方式来增强这个对象
    alert("hi");
  };
  return clone; //返回这个对象
}
```
使用寄生式继承来为对象添加函数，会由于不能做到函数复用而降低效率；这一点与构造函数模式类似。

寄生组合式继承
所谓寄生组合式继承，即通过借用构造函数来继承属性，通过原型链的混成形式来继承方法。其背后的基本思路是：不必为了指定子类型的原型而调用超类型的构造函数，我们所需要的无非就是超类型原型的一个副本而已。本质上，就是使用寄生式继承来继承超类型的原型，然后再将结果指定给子类型的原型。寄生组合式继承的基本模式如下所示。
```
function inheritPrototype(subType, superType){
  var prototype = object(superType.prototype); //创建对象
  prototype.constructor = subType; //增强对象
  subType.prototype = prototype; //指定对象
}

function SuperType(name){
  this.name = name;
  this.colors = ["red", "blue", "green"];
}

SuperType.prototype.sayName = function(){
  alert(this.name);
};

function SubType(name, age){
  SuperType.call(this, name);
  this.age = age;
}

inheritPrototype(SubType, SuperType);
SubType.prototype.sayAge = function(){
  alert(this.age);
};
```
这个例子的高效率体现在它只调用了一次 SuperType 构造函数，并且因此避免了在 SubType.prototype 上面创建不必要的、多余的属性。与此同时，原型链还能保持不变；因此，还能够正常使用instanceof 和 isPrototypeOf() 。开发人员普遍认为寄生组合式继承是引用类型最理想的继承范式。
YUI 的 YAHOO.lang.extend() 方法采用了寄生组合继承，从而让这种模式首次出现在了一个应用非常广泛的 JavaScript 库中。