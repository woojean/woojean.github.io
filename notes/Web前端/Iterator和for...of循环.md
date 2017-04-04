# Iterator和for...of循环

Iterator的作用有三个：
1.为各种数据结构，提供一个统一的、简便的访问接口；
2.使得数据结构的成员能够按某种次序排列；
3.ES6创造了一种新的遍历命令for...of循环，Iterator接口主要供for...of消费。

Iterator的遍历过程：
1.创建一个指针对象，指向当前数据结构的起始位置。也就是说，遍历器对象本质上，就是一个指针对象。
2.第一次调用指针对象的next方法，可以将指针指向数据结构的第一个成员。
3.第二次调用指针对象的next方法，指针就指向数据结构的第二个成员。
4.不断调用指针对象的next方法，直到它指向数据结构的结束位置。
每一次调用next方法，都会返回数据结构的当前成员的信息。具体来说，就是返回一个包含value和done两个属性的对象。其中，value属性是当前成员的值，done属性是一个布尔值，表示遍历是否结束。

由于Iterator只是把接口规格加到数据结构之上，所以，遍历器与它所遍历的那个数据结构，实际上是分开的，完全可以写出没有对应数据结构的遍历器对象，或者说用遍历器对象模拟出数据结构：
```
// 无限运行的遍历器对象
var it = idMaker();

it.next().value // '0'
it.next().value // '1'
it.next().value // '2'
// ...

function idMaker() {
  var index = 0;

  return {
    next: function() {
      return {value: index++, done: false};
    }
  };
}
```


## 数据结构的默认Iterator接口
ES6规定，默认的Iterator接口部署在数据结构的Symbol.iterator属性。一个数据结构只要具有Symbol.iterator属性，就可以认为是“可遍历的”（iterable）。调用Symbol.iterator方法，就会得到当前数据结构默认的遍历器生成函数。Symbol.iterator本身是一个表达式，返回Symbol对象的iterator属性，这是一个预定义好的、类型为Symbol的特殊值，所以要放在方括号内。一个对象如果要有可被for...of循环调用的Iterator接口，就必须在Symbol.iterator的属性上部署遍历器生成方法（原型链上的对象具有该方法也可）：
```
class RangeIterator {
  constructor(start, stop) {
    this.value = start;
    this.stop = stop;
  }

  [Symbol.iterator]() { return this; }

  next() {
    var value = this.value;
    if (value < this.stop) {
      this.value++;
      return {done: false, value: value};
    } else {
      return {done: true, value: undefined};
    }
  }
}

function range(start, stop) {
  return new RangeIterator(start, stop);
}

for (var value of range(0, 3)) {
  console.log(value);
}
```

对象（Object）之所以没有默认部署Iterator接口，是因为对象的哪个属性先遍历，哪个属性后遍历是不确定的，需要开发者手动指定。

对于类似数组的对象（存在数值键名和length属性），部署Iterator接口，有一个简便方法，就是Symbol.iterator方法直接引用数组的Iterator接口：
```
let iterable = {
  0: 'a',
  1: 'b',
  2: 'c',
  length: 3,
  [Symbol.iterator]: Array.prototype[Symbol.iterator]
};
for (let item of iterable) {
  console.log(item); // 'a', 'b', 'c'
}
```
注意，普通对象部署数组的Symbol.iterator方法，并无效果：
```
let iterable = {
  a: 'a',
  b: 'b',
  c: 'c',
  length: 3,
  [Symbol.iterator]: Array.prototype[Symbol.iterator]
};
for (let item of iterable) {
  console.log(item); // undefined, undefined, undefined
}
```

## 默认调用Iterator接口（即Symbol.iterator方法）的场合
1.解构赋值
2.扩展运算符
3.yield*
4.由于数组的遍历会调用遍历器接口，所以任何接受数组作为参数的场合，其实都调用了遍历器接口，如：
for...of
Array.from()
Map(), Set(), WeakMap(), WeakSet()（比如new Map([['a',1],['b',2]])）
Promise.all()
Promise.race()

## 字符串的Iterator接口
字符串是一个类似数组的对象，也原生具有Iterator接口。
```
var someString = "hi";
typeof someString[Symbol.iterator]
// "function"

var iterator = someString[Symbol.iterator]();

iterator.next()  // { value: "h", done: false }
iterator.next()  // { value: "i", done: false }
iterator.next()  // { value: undefined, done: true }
```

## Iterator接口与Generator函数
```
let obj = {
  * [Symbol.iterator]() {
    yield 'hello';
    yield 'world';
  }
};

for (let x of obj) {
  console.log(x);
}
// hello
// world
```

## 遍历器对象的return()，throw()
遍历器对象除了具有next方法，还可以具有return方法和throw方法。如果自己写遍历器对象生成函数，那么next方法是必须部署的，return方法和throw方法是否部署是可选的。
return方法的使用场合是，如果for...of循环提前退出（通常是因为出错，或者有break语句或continue语句），就会调用return方法。如果一个对象在完成遍历前，需要清理或释放资源，就可以部署return方法。
```
function readLinesSync(file) {
  return {
    next() {
      if (file.isAtEndOfFile()) {
        file.close();
        return { done: true };
      }
    },
    return() {
      file.close();
      return { done: true };
    },
  };
}

for (let line of readLinesSync(fileName)) {
  console.log(x);  // 触发return()方法
  break;
}
```

throw方法主要是配合Generator函数使用，一般的遍历器对象用不到这个方法。

## for...of循环
一个数据结构只要部署了Symbol.iterator属性，就被视为具有iterator接口，就可以用for...of循环遍历它的成员。