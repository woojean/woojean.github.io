# Symbol类型

ES6引入了一种新的原始数据类型Symbol，表示独一无二的值。它是`JavaScript语言的第七种数据类型`，前六种是：Undefined、Null、布尔值（Boolean）、字符串（String）、数值（Number）、对象（Object）。
Symbol值通过Symbol函数生成。这就是说，`对象的属性名现在可以有两种类型`，一种是原来就有的字符串，另一种就是新增的Symbol类型。凡是属性名属于Symbol类型，就都是独一无二的，可以保证不会与其他属性名产生冲突。

Symbol函数前不能使用new命令，否则会报错。这是因为生成的Symbol是一个原始类型的值，不是对象。也就是说，由于Symbol值不是对象，所以不能添加属性。`基本上，它是一种类似于字符串的数据类型`。
Symbol函数可以接受一个字符串作为参数，表示对Symbol实例的描述，主要是为了在控制台显示，或者转为字符串时，比较容易区分。

```
let s = Symbol();

typeof s
// "symbol"

var s1 = Symbol('foo');
var s2 = Symbol('bar');

s1 // Symbol(foo)
s2 // Symbol(bar)

s1.toString() // "Symbol(foo)"
s2.toString() // "Symbol(bar)"

// 没有参数的情况
var s1 = Symbol();
var s2 = Symbol();

s1 === s2 // false

// 有参数的情况
var s1 = Symbol("foo");
var s2 = Symbol("foo");

s1 === s2 // false
```

Symbol值不能与其他类型的值进行运算，但是，Symbol值可以显式转为字符串，也可以转为布尔值，但是不能转为数值。：
```
var sym = Symbol('My symbol');

"your symbol is " + sym
// TypeError: can't convert symbol to string
`your symbol is ${sym}`
// TypeError: can't convert symbol to string

var sym = Symbol('My symbol');

String(sym) // 'Symbol(My symbol)'
sym.toString() // 'Symbol(My symbol)'

var sym = Symbol();
Boolean(sym) // true
!sym  // false

if (sym) {
  // ...
}

Number(sym) // TypeError
sym + 2 // TypeError
```

## 作为属性名的Symbol
由于每一个Symbol值都是不相等的，这意味着Symbol值可以作为标识符，用于对象的属性名，就能保证不会出现同名的属性：
```
var mySymbol = Symbol();

// 第一种写法
var a = {};
a[mySymbol] = 'Hello!';

// 第二种写法
var a = {
  [mySymbol]: 'Hello!'   // 在对象的内部，使用Symbol值定义属性时，Symbol值必须放在方括号之中
};

// 第三种写法
var a = {};
Object.defineProperty(a, mySymbol, { value: 'Hello!' });

// 以上写法都得到同样结果
a[mySymbol] // "Hello!"
```
Symbol值作为对象属性名时，不能用点运算符:
```
var mySymbol = Symbol();
var a = {};

a.mySymbol = 'Hello!';
a[mySymbol] // undefined
a['mySymbol'] // "Hello!"
```

## 属性名的遍历
Symbol作为属性名，该属性不会出现在for...in、for...of循环中，也不会被Object.keys()、Object.getOwnPropertyNames()返回。但是，它也不是私有属性，有一个Object.getOwnPropertySymbols方法，可以获取指定对象的所有Symbol属性名。

另一个新的API，Reflect.ownKeys方法可以返回所有类型的键名，包括常规键名和Symbol键名。

## Symbol.for()，Symbol.keyFor()
Symbol.for方法接受一个字符串作为参数，然后搜索有没有以该参数作为名称的Symbol值。如果有，就返回这个Symbol值，否则就新建并返回一个以该字符串为名称的Symbol值。
```
var s1 = Symbol.for('foo');
var s2 = Symbol.for('foo');

s1 === s2 // true
```
Symbol.for为Symbol值登记的名字，是全局环境的，可以在不同的iframe或service worker中取到同一个值。

Symbol.keyFor方法返回一个已登记的Symbol类型值的key：
```
var s1 = Symbol.for("foo");
Symbol.keyFor(s1) // "foo"

var s2 = Symbol("foo");
Symbol.keyFor(s2) // undefined
```

由于以Symbol值作为名称的属性，不会被常规方法遍历得到，可以利用这个特性，为对象定义一些非私有的、但又希望只用于内部的方法：
```
var size = Symbol('size');

class Collection {
  constructor() {
    this[size] = 0;
  }

  add(item) {
    this[this[size]] = item;
    this[size]++;
  }

  static sizeOf(instance) {
    return instance[size];
  }
}

var x = new Collection();
Collection.sizeOf(x) // 0

x.add('foo');
Collection.sizeOf(x) // 1

Object.keys(x) // ['0']
Object.getOwnPropertyNames(x) // ['0']
Object.getOwnPropertySymbols(x) // [Symbol(size)]
```

## 使用Symbol实现模块的 Singleton 模式
```
// mod.js
const FOO_KEY = Symbol.for('foo');

function A() {
  this.foo = 'hello';
}

if (!global[FOO_KEY]) {
  global[FOO_KEY] = new A();
}

module.exports = global[FOO_KEY];
```
上面代码中，可以保证global[FOO_KEY]不会被其他脚本改写。


## 内置的Symbol值
ES6还提供了11个内置的Symbol值，指向语言内部使用的方法：
1.Symbol.hasInstance：foo instanceof Foo在语言内部，实际调用的是Foo[Symbol.hasInstance](foo)

2.Symbol.isConcatSpreadable：表示该对象使用Array.prototype.concat()时，是否可以展开
```
let arr1 = ['c', 'd'];
['a', 'b'].concat(arr1, 'e') // ['a', 'b', 'c', 'd', 'e']
arr1[Symbol.isConcatSpreadable] // undefined

let arr2 = ['c', 'd'];
arr2[Symbol.isConcatSpreadable] = false;
['a', 'b'].concat(arr2, 'e') // ['a', 'b', ['c','d'], 'e']
```

3.Symbol.species:指向一个方法。该对象作为构造函数创造实例时，会调用这个方法。即如果this.constructor[Symbol.species]存在，就会使用这个属性作为构造函数，来创造新的实例对象。

4.Symbol.match :指向一个函数。当执行str.match(myObject)时，如果该属性存在，会调用它，返回该方法的返回值

5.Symbol.replace:指向一个方法，当该对象被String.prototype.replace方法调用时，会返回该方法的返回值。

6.Symbol.search:指向一个方法，当该对象被String.prototype.search方法调用时，会返回该方法的返回值。

7.Symbol.split:指向一个方法，当该对象被String.prototype.split方法调用时，会返回该方法的返回值。

8.Symbol.iterator:指向该对象的默认遍历器方法
```
var myIterable = {};
myIterable[Symbol.iterator] = function* () {
  yield 1;
  yield 2;
  yield 3;
};

[...myIterable] // [1, 2, 3]
```

9.Symbol.toPrimitive：指向一个方法。该对象被转为原始类型的值时，会调用这个方法，返回该对象对应的原始类型值。

10.Symbol.toStringTag：指向一个方法。在该对象上面调用Object.prototype.toString方法时，如果这个属性存在，它的返回值会出现在toString方法返回的字符串之中，表示对象的类型。也就是说，这个属性可以用来定制[object Object]或[object Array]中object后面的那个字符串。ES6新增内置对象的Symbol.toStringTag属性值如下：
JSON[Symbol.toStringTag]：'JSON'
Math[Symbol.toStringTag]：'Math'
Module对象M[Symbol.toStringTag]：'Module'
ArrayBuffer.prototype[Symbol.toStringTag]：'ArrayBuffer'
DataView.prototype[Symbol.toStringTag]：'DataView'
Map.prototype[Symbol.toStringTag]：'Map'
Promise.prototype[Symbol.toStringTag]：'Promise'
Set.prototype[Symbol.toStringTag]：'Set'
%TypedArray%.prototype[Symbol.toStringTag]：'Uint8Array'等
WeakMap.prototype[Symbol.toStringTag]：'WeakMap'
WeakSet.prototype[Symbol.toStringTag]：'WeakSet'
%MapIteratorPrototype%[Symbol.toStringTag]：'Map Iterator'
%SetIteratorPrototype%[Symbol.toStringTag]：'Set Iterator'
%StringIteratorPrototype%[Symbol.toStringTag]：'String Iterator'
Symbol.prototype[Symbol.toStringTag]：'Symbol'
Generator.prototype[Symbol.toStringTag]：'Generator'
GeneratorFunction.prototype[Symbol.toStringTag]：'GeneratorFunction'

11.Symbol.unscopables：指向一个对象。该对象指定了使用with关键字时，哪些属性会被with环境排除。