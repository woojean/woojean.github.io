# 异步操作和Async函数

## 回调函数、Promise
（略） 

## Generator函数执行异步任务
```
var fetch = require('node-fetch');

function* gen(){
  var url = 'https://api.github.com/users/github';
  var result = yield fetch(url);
  console.log(result.bio);
}

var g = gen();
var result = g.next();

result.value.then(function(data){
  return data.json();
}).then(function(data){
  g.next(data);
});
```

## Thunk函数
```
var x = 1;

function f(m){
  return m * 2;
}

f(x + 5)
```

`传值调用`：即在进入函数体之前，就计算x + 5的值（等于6），再将这个值传入函数f 。C语言就采用这种策略。
```
f(x + 5)
// 传值调用时，等同于
f(6)
```

`传名调用`:即直接将表达式x + 5传入函数体，只在用到它的时候求值。Haskell语言采用这种策略。
```
f(x + 5)
// 传名调用时，等同于
(x + 5) * 2
```

编译器的"传名调用"实现，往往是将参数放到一个临时函数之中，再将这个临时函数传入函数体。这个临时函数就叫做Thunk函数:

```
function f(m){
  return m * 2;
}

f(x + 5);

// 等同于

var thunk = function () {
  return x + 5;
};

function f(thunk){
  return thunk() * 2;
}
```
凡是用到原参数的地方，对Thunk函数求值即可。这就是Thunk函数的定义，它是"传名调用"的一种实现策略，用来替换某个表达式。

## JavaScript语言的Thunk函数
在JavaScript语言中，Thunk函数替换的不是表达式，而是多参数函数，将其替换成单参数的版本，且只接受回调函数作为参数。
```
// 正常版本的readFile（多参数版本）
fs.readFile(fileName, callback);

// Thunk版本的readFile（单参数版本）
var readFileThunk = Thunk(fileName);
readFileThunk(callback);

var Thunk = function (fileName){
  return function (callback){
    return fs.readFile(fileName, callback);
  };
};
```
任何函数，只要参数有回调函数，就能写成Thunk函数的形式：
```
// Thunk函数转换器
var Thunk = function(fn) {
  return function (...args) {
    return function (callback) {
      return fn.call(this, ...args, callback);
    }
  };
};
```

## Thunkify模块
$ npm install thunkify
var thunkify = require('thunkify');
var fs = require('fs');

var read = thunkify(fs.readFile);
read('package.json')(function(err, str){
  // ...
});

## Generator 函数的流程管理
ES6有了Generator函数，Thunk函数现在可以用于Generator函数的自动流程管理:
```
function run(fn) {
  var gen = fn();

  function next(err, data) {
    var result = gen.next(data);
    if (result.done) return;
    result.value(next);
  }

  next();
}

function* g() {
  // ...
}

run(g);
```

## co模块

## async函数
async函数就是Generator函数的语法糖。
```
var fs = require('fs');

var readFile = function (fileName) {
  return new Promise(function (resolve, reject) {
    fs.readFile(fileName, function(error, data) {
      if (error) reject(error);
      resolve(data);
    });
  });
};

var gen = function* (){
  var f1 = yield readFile('/etc/fstab');
  var f2 = yield readFile('/etc/shells');
  console.log(f1.toString());
  console.log(f2.toString());
};
```
写成async函数:
```
var asyncReadFile = async function (){
  var f1 = await readFile('/etc/fstab');
  var f2 = await readFile('/etc/shells');
  console.log(f1.toString());
  console.log(f2.toString());
};
```
async函数对 Generator 函数的改进，体现在以下四点。
1.内置执行器。Generator函数的执行必须靠执行器，所以才有了co模块，而async函数自带执行器。也就是说，async函数的执行，与普通函数一模一样，只要一行。
  var result = asyncReadFile();
上面的代码调用了asyncReadFile函数，然后它就会自动执行，输出最后结果。这完全不像Generator函数，需要调用next方法，或者用co模块，才能得到真正执行，得到最后结果。

2.更好的语义。async和await，比起星号和yield，语义更清楚了。async表示函数里有异步操作，await表示紧跟在后面的表达式需要等待结果。

3.更广的适用性。 co模块约定，yield命令后面只能是Thunk函数或Promise对象，而async函数的await命令后面，可以是Promise对象和原始类型的值（数值、字符串和布尔值，但这时等同于同步操作）。

4.返回值是Promise。async函数的返回值是Promise对象，这比Generator函数的返回值是Iterator对象方便多了。可以用then方法指定下一步的操作。

进一步说，async函数完全可以看作多个异步操作，包装成的一个Promise对象，而await命令就是内部then命令的语法糖。

## async函数语法
1.async函数返回一个Promise对象
async函数内部return语句返回的值，会成为then方法回调函数的参数:
```
async function f() {
  return 'hello world';
}

f().then(v => console.log(v))
// "hello world"
```
async函数内部抛出错误，会导致返回的Promise对象变为reject状态。抛出的错误对象会被catch方法回调函数接收到。

2.async函数返回的Promise对象，必须等到内部所有await命令的Promise对象执行完，才会发生状态改变。也就是说，只有async函数内部的异步操作执行完，才会执行then方法指定的回调函数。

3.正常情况下，await命令后面是一个Promise对象。如果不是，会被转成一个立即resolve的Promise对象。

4.如果await后面的异步操作出错，那么等同于async函数返回的Promise对象被reject。

## async函数的实现
async 函数的实现，就是将 Generator 函数和自动执行器，包装在一个函数里：
```
async function fn(args){
  // ...
}

// 等同于

function fn(args){
  return spawn(function*() {
    // ...
  });
}
```
所有的async函数都可以写成上面的第二种形式，其中的 spawn 函数就是自动执行器。

-------------
（略）