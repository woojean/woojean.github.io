# Proxy和Reflect

Proxy可以理解成，在目标对象之前架设一层“拦截”，外界对该对象的访问，都必须先通过这层拦截，因此提供了一种机制，可以对外界的访问进行过滤和改写。ES6原生提供Proxy构造函数，用来生成Proxy实例：
var proxy = new Proxy(target, handler);
target参数表示所要拦截的目标对象，handler参数也是一个对象，用来定制拦截行为。

```
var obj = new Proxy({}, {
  get: function (target, key, receiver) {
    console.log(`getting ${key}!`);
    return Reflect.get(target, key, receiver);
  },
  set: function (target, key, value, receiver) {
    console.log(`setting ${key}!`);
    return Reflect.set(target, key, value, receiver);
  }
});

obj.count = 1
//  setting count!
++obj.count
//  getting count!
//  setting count!
//  2
```

## Proxy支持的拦截操作
对于可以设置、但没有设置拦截的操作，则直接落在目标对象上，按照原先的方式产生结果。
1.get(target, propKey, receiver)
拦截对象属性的读取，比如proxy.foo和proxy['foo']。
参数receiver是一个对象，可选。

2.set(target, propKey, value, receiver)
拦截对象属性的设置，比如proxy.foo = v或proxy['foo'] = v，返回一个布尔值。

3.has(target, propKey)
拦截propKey in proxy的操作，以及对象的hasOwnProperty方法，返回一个布尔值。

4.deleteProperty(target, propKey)
拦截delete proxy[propKey]的操作，返回一个布尔值。

5.ownKeys(target)
拦截Object.getOwnPropertyNames(proxy)、Object.getOwnPropertySymbols(proxy)、Object.keys(proxy)，返回一个数组。该方法返回对象所有自身的属性，而Object.keys()仅返回对象可遍历的属性。

6.getOwnPropertyDescriptor(target, propKey)
拦截Object.getOwnPropertyDescriptor(proxy, propKey)，返回属性的描述对象。

7.defineProperty(target, propKey, propDesc)
拦截Object.defineProperty(proxy, propKey, propDesc）、Object.defineProperties(proxy, propDescs)，返回一个布尔值。

8.preventExtensions(target)
拦截Object.preventExtensions(proxy)，返回一个布尔值。

9.getPrototypeOf(target)
拦截Object.getPrototypeOf(proxy)，返回一个对象。

10.isExtensible(target)
拦截Object.isExtensible(proxy)，返回一个布尔值。

11.setPrototypeOf(target, proto)
拦截Object.setPrototypeOf(proxy, proto)，返回一个布尔值。
如果目标对象是函数，那么还有两种额外操作可以拦截。

12.apply(target, object, args)
拦截Proxy实例作为函数调用的操作，比如proxy(...args)、proxy.call(object, ...args)、proxy.apply(...)。

13.construct(target, args)
拦截Proxy实例作为构造函数调用的操作，比如new proxy(...args)。

## Proxy.revocable()
Proxy.revocable方法返回一个对象，该对象的proxy属性是Proxy实例，revoke属性是一个函数，可以取消Proxy实例:
```
let target = {};
let handler = {};

let {proxy, revoke} = Proxy.revocable(target, handler);

proxy.foo = 123;
proxy.foo // 123

revoke();
proxy.foo // TypeError: Revoked
```

## Reflect
Reflect对象的设计目的有这样几个：
1.将Object对象的一些明显属于语言内部的方法（比如Object.defineProperty），放到Reflect对象上。现阶段，某些方法同时在Object和Reflect对象上部署，未来的新方法将只部署在Reflect对象上。

2.修改某些Object方法的返回结果，让其变得更合理。比如，Object.defineProperty(obj, name, desc)在无法定义属性时，会抛出一个错误，而Reflect.defineProperty(obj, name, desc)则会返回false。

3.让Object操作都变成函数行为。某些Object操作是命令式，比如name in obj和delete obj[name]，而Reflect.has(obj, name)和Reflect.deleteProperty(obj, name)让它们变成了函数行为。

4.Reflect对象的方法与Proxy对象的方法一一对应，只要是Proxy对象的方法，就能在Reflect对象上找到对应的方法。这就`让Proxy对象可以方便地调用对应的Reflect方法，完成默认行为，作为修改行为的基础`。也就是说，`不管Proxy怎么修改默认行为，总可以在Reflect上获取默认行为`：
```
var loggedObj = new Proxy(obj, {
  get(target, name) {
    console.log('get', target, name);
    return Reflect.get(target, name);
  },
  deleteProperty(target, name) {
    console.log('delete' + name);
    return Reflect.deleteProperty(target, name);
  },
  has(target, name) {
    console.log('has' + name);
    return Reflect.has(target, name);
  }
});
```
上面代码中，每一个Proxy对象的拦截操作（get、delete、has），内部都调用对应的Reflect方法，保证原生行为能够正常执行。添加的工作，就是将每一个操作输出一行日志。

## Reflect对象的方法
Reflect.apply(target,thisArg,args) 

Reflect.construct(target,args)
等同于new target(...args)，这提供了一种不使用new，来调用构造函数的方法。

Reflect.get(target,name,receiver)
查找并返回target对象的name属性，如果没有该属性，则返回undefined。
如果name属性部署了读取函数，则读取函数的this绑定receiver。
```
var obj = {
  get foo() { return this.bar(); },
  bar: function() { ... }
};

// 下面语句会让 this.bar()
// 变成调用 wrapper.bar()
Reflect.get(obj, "foo", wrapper);
Reflect.set(target,name,value,receiver)
Reflect.defineProperty(target,name,desc)
Reflect.deleteProperty(target,name)
Reflect.has(target,name)
Reflect.ownKeys(target)
Reflect.isExtensible(target)
Reflect.preventExtensions(target)
Reflect.getOwnPropertyDescriptor(target, name)

Reflect.getPrototypeOf(target)
读取对象的__proto__属性，对应Object.getPrototypeOf(obj)

Reflect.setPrototypeOf(target, prototype)
```
