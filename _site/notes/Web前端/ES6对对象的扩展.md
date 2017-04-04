# ES6对对象的扩展

## 属性的简洁表示法
ES6允许直接写入变量和函数，作为对象的属性和方法（允许在对象之中，只写属性名，不写属性值。这时，属性值等于属性名所代表的变量的值）：
```
var birth = '2000/01/01';

var Person = {

  name: '张三',

  //等同于birth: birth
  birth,

  // 等同于hello: function ()...
  hello() { console.log('我的名字是', this.name); }

};
```
## 属性名表达式
ES6允许字面量定义对象时，用表达式作为属性名（或方法名），表达式必须放在方括号内：
```
let propKey = 'foo';

let obj = {
  [propKey]: true,
  ['a' + 'bc']: 123,
  ['h'+'ello']() {
    return 'hi';
  }
};
```

属性名表达式与简洁表示法，不能同时使用:
```
// 报错
var foo = 'bar';
var bar = 'abc';
var baz = { [foo] };

// 正确
var foo = 'bar';
var baz = { [foo]: 'abc'};
```

## 方法的name属性
```
var person = {
  sayName() {
    console.log(this.name);
  },
  get firstName() {
    return "Nicholas";
  }
};

person.sayName.name   // "sayName"
person.firstName.name // "get firstName"
```
有两种特殊情况：bind方法创造的函数，name属性返回“bound”加上原函数的名字；Function构造函数创造的函数，name属性返回“anonymous”。
```
(new Function()).name // "anonymous"

var doSomething = function() {
  // ...
};
doSomething.bind().name // "bound doSomething"
```

如果对象的方法是一个Symbol值，那么name属性返回的是这个Symbol值的描述：
```
const key1 = Symbol('description');
const key2 = Symbol();
let obj = {
  [key1]() {},
  [key2]() {},
};
obj[key1].name // "[description]"
obj[key2].name // ""
```

## Object.is()
相等运算符（==）和严格相等运算符（===）都有缺点，前者会自动转换数据类型，后者的NaN不等于自身，以及+0等于-0。ES6提出“Same-value equality”（同值相等）算法，用来解决这个问题。Object.is就是部署这个算法的新方法。它用来比较两个值是否严格相等，与严格比较运算符（===）的行为基本一致，不同之处只有两个：一是+0不等于-0，二是NaN等于自身：
```
+0 === -0 //true
NaN === NaN // false

Object.is(+0, -0) // false
Object.is(NaN, NaN) // true
```

## Object.assign()
Object.assign方法用于对象的合并，将源对象（source）的所有可枚举属性，复制到目标对象（target）：
```
var target = { a: 1 };

var source1 = { b: 2 };
var source2 = { c: 3 };

Object.assign(target, source1, source2);
target // {a:1, b:2, c:3}
```
如果目标对象与源对象有同名属性，或多个源对象有同名属性，则后面的属性会覆盖前面的属性。
Object.assign拷贝的属性是有限制的，只拷贝源对象的自身属性（不拷贝继承属性），也不拷贝不可枚举的属性（enumerable: false）。
属性名为Symbol值的属性，也会被Object.assign拷贝。
```
Object.assign({ a: 'b' }, { [Symbol('c')]: 'd' })
// { a: 'b', Symbol(c): 'd' }
```
Object.assign方法实行的是浅拷贝，而不是深拷贝。也就是说，如果源对象某个属性的值是对象，那么目标对象拷贝得到的是这个对象的引用。

对于嵌套的对象，一旦遇到同名属性，Object.assign的处理方法是替换，而不是添加:
```
var target = { a: { b: 'c', d: 'e' } }
var source = { a: { b: 'hello' } }
Object.assign(target, source)
// { a: { b: 'hello' } }
```

Object.assign可以用来处理数组，但是会把数组视为对象：
```
Object.assign([1, 2, 3], [4, 5])
// [4, 5, 3]
```

## 属性的可枚举性
对象的每个属性都有一个描述对象（Descriptor），用来控制该属性的行为。`Object.getOwnPropertyDescriptor`方法可以获取该属性的描述对象。
```
let obj = { foo: 123 };
Object.getOwnPropertyDescriptor(obj, 'foo')
//  {
//    value: 123,
//    writable: true,
//    enumerable: true,
//    configurable: true
//  }
```
ES5有三个操作会忽略enumerable为false的属性。
1.for...in循环：只遍历对象自身的和继承的可枚举的属性
2.Object.keys()：返回对象自身的所有可枚举的属性的键名
3.JSON.stringify()：只串行化对象自身的可枚举的属性
ES6新增了一个操作Object.assign()，会忽略enumerable为false的属性，只拷贝对象自身的可枚举的属性。
这四个操作之中，只有for...in会返回继承的属性。
另外，ES6规定，所有Class的原型的方法都是不可枚举的。
```
Object.getOwnPropertyDescriptor(class {foo() {}}.prototype, 'foo').enumerable
// false
```

## 属性的遍历
ES6一共有5种方法可以遍历对象的属性。
1.`for...in`
for...in循环遍历对象自身的和继承的可枚举属性（不含Symbol属性）。

2.`Object.keys(obj)`
Object.keys返回一个数组，包括对象自身的（不含继承的）所有可枚举属性（不含Symbol属性）。

3.`Object.getOwnPropertyNames(obj)`
Object.getOwnPropertyNames返回一个数组，包含对象自身的所有属性（不含Symbol属性，但是包括不可枚举属性）。

4.`Object.getOwnPropertySymbols(obj)`
Object.getOwnPropertySymbols返回一个数组，包含对象自身的所有Symbol属性。

5.`Reflect.ownKeys(obj)`
Reflect.ownKeys返回一个数组，包含对象自身的所有属性，不管是属性名是Symbol或字符串，也不管是否可枚举。

以上的5种方法遍历对象的属性，都遵守同样的属性遍历的次序规则：
首先遍历所有属性名为`数值`的属性，按照数字排序。
其次遍历所有属性名为`字符串`的属性，按照生成时间排序。
最后遍历所有属性名为`Symbol值`的属性，按照生成时间排序。
```
Reflect.ownKeys({ [Symbol()]:0, b:0, 10:0, 2:0, a:0 })
// ['2', '10', 'b', 'a', Symbol()]
```

## __proto__属性，Object.setPrototypeOf()，Object.getPrototypeOf() 
__proto__属性用来读取或设置当前对象的prototype对象,在实现上，__proto__调用的是Object.prototype.__proto__。如果一个对象本身部署了__proto__属性，则该属性的值就是对象的原型。
```
// es6的写法
var obj = {
  method: function() { ... }
};
obj.__proto__ = someOtherObj;

// es5的写法
var obj = Object.create(someOtherObj);
obj.method = function() { ... };
```
无论从语义的角度，还是从兼容性的角度，都不要使用这个属性，而是使用下面的Object.setPrototypeOf()（写操作）、Object.getPrototypeOf()（读操作）、Object.create()（生成操作）代替。

## Object.values()，Object.entries()
ES7有一个提案，引入了跟Object.keys配套的Object.values和Object.entries。
(略)

## 对象的扩展运算符
ES7有一个提案，将Rest解构赋值/扩展运算符（...）引入对象
```
let { x, y, ...z } = { x: 1, y: 2, a: 3, b: 4 };
x // 1
y // 2
z // { a: 3, b: 4 }
```
Rest解构赋值不会拷贝继承自原型对象的属性：
```
let o1 = { a: 1 };
let o2 = { b: 2 };
o2.__proto__ = o1;
let o3 = { ...o2 };
o3 // { b: 2 }
```

扩展运算符（...）用于取出参数对象的所有可遍历属性，拷贝到当前对象之中：
```
let z = { a: 3, b: 4 };
let n = { ...z };
n // { a: 3, b: 4 }
```
这等同于使用Object.assign方法。


## Object.getOwnPropertyDescriptors() 
ES7有一个提案，提出了Object.getOwnPropertyDescriptors方法，返回指定对象所有自身属性（非继承属性）的描述对象。

可以用来实现Mixin（混入）模式：
```
let mix = (object) => ({
  with: (...mixins) => mixins.reduce(
    (c, mixin) => Object.create(
      c, Object.getOwnPropertyDescriptors(mixin)
    ), object)
});

// multiple mixins example
let a = {a: 'a'};
let b = {b: 'b'};
let c = {c: 'c'};
let d = mix(c).with(a, b);
```