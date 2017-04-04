# ES6的Class

基本上，ES6的class可以看作只是一个语法糖，它的绝大部分功能，ES5都可以做到，新的class写法只是让对象原型的写法更加清晰、更像面向对象编程的语法而已。ES6的类，完全可以看作构造函数的另一种写法：

```
class Point {
  // ...
}

typeof Point // "function"
Point === Point.prototype.constructor // true
```
使用的时候，也是直接对类使用new命令，跟构造函数的用法完全一致。

构造函数的prototype属性，在ES6的“类”上面继续存在。事实上，类的所有方法都定义在类的prototype属性上面。
```
class Point {
  constructor(){
    // ...
  }

  toString(){
    // ...
  }

  toValue(){
    // ...
  }
}

// 等同于

Point.prototype = {
  toString(){},
  toValue(){}
};
```
在类的实例上面调用方法，其实就是调用原型上的方法：
```
class B {}
let b = new B();

b.constructor === B.prototype.constructor // true
```
prototype对象的constructor属性，直接指向“类”的本身，这与ES5的行为是一致的：
```
Point.prototype.constructor === Point // true
```

类的内部所有定义的方法，都是不可枚举的（non-enumerable）。这一点与ES5的行为不一致：
```
// ES 6
class Point {
  constructor(x, y) {
    // ...
  }

  toString() {
    // ...
  }
}

Object.keys(Point.prototype)
// []
Object.getOwnPropertyNames(Point.prototype)
// ["constructor","toString"]


// ES 5
var Point = function (x, y) {
  // ...
};

Point.prototype.toString = function() {
  // ...
};

Object.keys(Point.prototype)
// ["toString"]
Object.getOwnPropertyNames(Point.prototype)
// ["constructor","toString"]
```

## constructor方法
constructor方法默认返回实例对象（即this），完全可以指定返回另外一个对象。
类的构造函数，不使用new是没法调用的，会报错。这是它跟普通构造函数的一个主要区别，后者不用new也可以执行。

## 类的实例对象
与ES5一样，实例的属性除非显式定义在其本身（即定义在this对象上），否则都是定义在原型上（即定义在class上）。
```
//定义类
class Point {

  constructor(x, y) {
    this.x = x;
    this.y = y;
  }

  toString() {
    return '(' + this.x + ', ' + this.y + ')';
  }

}

var point = new Point(2, 3);

point.toString() // (2, 3)

point.hasOwnProperty('x') // true
point.hasOwnProperty('y') // true
point.hasOwnProperty('toString') // false  !!!
point.__proto__.hasOwnProperty('toString') // true
```

与ES5一样，类的所有实例共享一个原型对象：
```
var p1 = new Point(2,3);
var p2 = new Point(3,2);

p1.__proto__ === p2.__proto__
//true
```

不存在变量提升：
```
new Foo(); // ReferenceError
class Foo {}
```

与函数一样，类也可以使用表达式的形式定义：
```
const MyClass = class {
  getClassName() {
    return Me.name;
  }
};
```

ES6的类不提供私有方法，可以利用Symbol来模拟：
```
const bar = Symbol('bar');
const snaf = Symbol('snaf');

export default class myClass{

  // 公有方法
  foo(baz) {
    this[bar](baz);
  }

  // 私有方法
  [bar](baz) {
    return this[snaf] = baz;
  }

  // ...
};
```

## this的指向
类的方法内部如果含有this，它默认指向类的实例，单独使用方法时容易出错：
```
class Logger {
  printName(name = 'there') {
    this.print(`Hello ${name}`);
  }

  print(text) {
    console.log(text);
  }
}

const logger = new Logger();
const { printName } = logger;
printName(); // TypeError: Cannot read property 'print' of undefined
```
应该使用箭头函数：
```
class Logger {
  constructor() {
    this.printName = (name = 'there') => {
      this.print(`Hello ${name}`);
    };
  }

  // ...
}
```

类和模块的内部，默认就是严格模式。

## Class的继承 
```
class ColorPoint extends Point {
  constructor(x, y, color) {
    super(x, y); // 调用父类的constructor(x, y)
    this.color = color;
  }

  toString() {
    return this.color + ' ' + super.toString(); // 调用父类的toString()
  }
}
```
ES5的继承，实质是先创造子类的实例对象this，然后再将父类的方法添加到this上面（Parent.apply(this)）。ES6的继承机制完全不同，实质是先创造父类的实例对象this（所以必须先调用super方法），然后再用子类的构造函数修改this。

## 类的prototype属性和__proto__属性
Class作为构造函数的语法糖，同时有prototype属性和__proto__属性，因此同时存在两条继承链。
1.子类的__proto__属性，表示`构造函数的继承`，总是指向父类。
2.子类prototype属性的__proto__属性，表示`方法的继承`，总是指向父类的prototype属性。
```
class A {
}

class B extends A {
}

B.__proto__ === A // true
B.prototype.__proto__ === A.prototype // true
```
这样的结果是因为，类的继承是按照下面的模式实现的:
```
class A {
}

class B {
}

// B的实例继承A的实例
Object.setPrototypeOf(B.prototype, A.prototype);

// B继承A的静态属性
Object.setPrototypeOf(B, A);
```
这两条继承链，可以这样理解：作为一个对象，子类（B）的原型（__proto__属性）是父类（A）；作为一个构造函数，子类（B）的原型（prototype属性）是父类的实例。

## Extends 的继承目标
只要是一个有prototype属性的函数，就能被继承。由于函数都有prototype属性（除了Function.prototype函数），因此可以是任意函数。

子类继承Object类：
```
class A extends Object {
}

A.__proto__ === Object // true
A.prototype.__proto__ === Object.prototype // true
```
这种情况下，A其实就是构造函数Object的复制，A的实例就是Object的实例。

不存在任何继承:
```
class A {
}

A.__proto__ === Function.prototype // true
A.prototype.__proto__ === Object.prototype // true
```
这种情况下，A作为一个基类（即不存在任何继承），就是一个普通函数，所以直接继承Funciton.prototype。但是，A调用后返回一个空对象（即Object实例），所以A.prototype.__proto__指向构造函数（Object）的prototype属性。


子类继承null：
```
class A extends null {
}

A.__proto__ === Function.prototype // true
A.prototype.__proto__ === undefined // true
```

## Object.getPrototypeOf() 
Object.getPrototypeOf方法可以用来从子类上获取父类。
```
Object.getPrototypeOf(ColorPoint) === Point
// true
```
因此，可以使用这个方法判断，一个类是否继承了另一个类。

## super关键字
super这个关键字，有两种用法，含义不同。
1.作为函数调用时（即super(...args)），super代表父类的构造函数。
2.作为对象调用时（即super.prop或super.method()），super代表父类。注意，此时super即可以引用父类实例的属性和方法，也可以引用父类的静态方法。

## 实例的__proto__属性
子类实例的__proto__属性的__proto__属性，指向父类实例的__proto__属性。也就是说，子类的原型的原型，是父类的原型。

## 原生构造函数的继承
ECMAScript的原生构造函数大致有下面这些：
Boolean()
Number()
String()
Array()
Date()
Function()
RegExp()
Error()
Object()
ES6允许继承原生构造函数定义子类，因为ES6是先新建父类的实例对象this，然后再用子类的构造函数修饰this，使得父类的所有行为都可以继承。

## Class的取值函数（getter）和存值函数（setter）
与ES5一样，在Class内部可以使用get和set关键字，对某个属性设置存值函数和取值函数，拦截该属性的存取行为。

## Class的静态方法
类相当于实例的原型，所有在类中定义的方法，都会被实例继承。如果在一个方法前，加上static关键字，就表示该方法不会被实例继承，而是直接通过类来调用，这就称为“静态方法”。
```
class Foo {
  static classMethod() {
    return 'hello';
  }
}

Foo.classMethod() // 'hello'

var foo = new Foo();
foo.classMethod()
// TypeError: foo.classMethod is not a function
```

## Class的静态属性和实例属性
ES6明确规定，Class内部只有静态方法，没有静态属性。
目前只有这种方法可行：
```
class Foo {
}

Foo.prop = 1;
Foo.prop // 1
```

## new.target属性 
new是从构造函数生成实例的命令。ES6为new命令引入了一个new.target属性，（在构造函数中）返回new命令作用于的那个构造函数。如果构造函数不是通过new命令调用的，new.target会返回undefined，因此这个属性可以用来确定构造函数是怎么调用的。需要注意的是，子类继承父类时，new.target会返回子类。

## Mixin模式的实现
(略)