# Module

在ES6之前，社区制定了一些模块加载方案，最主要的有CommonJS和AMD两种。前者用于服务器，后者用于浏览器。ES6在语言规格的层面上，实现了模块功能，而且实现得相当简单，完全可以取代现有的CommonJS和AMD规范，成为浏览器和服务器通用的模块解决方案。

ES6模块的设计思想，是尽量的静态化，使得编译时就能确定模块的依赖关系，以及输入和输出的变量。`CommonJS和AMD模块，都只能在运行时确定这些东西`。比如，CommonJS模块就是对象，输入时必须查找对象属性。

```
// CommonJS模块
let { stat, exists, readFile } = require('fs');

// 等同于
let _fs = require('fs');
let stat = _fs.stat, exists = _fs.exists, readfile = _fs.readfile;
```
上面代码的实质是整体加载fs模块（即加载fs的所有方法），生成一个对象（_fs），然后再从这个对象上面读取3个方法。这种加载称为“运行时加载”，因为只有运行时才能得到这个对象，导致完全没办法在编译时做“静态优化”。

ES6模块不是对象，而是通过export命令显式指定输出的代码，输入时也采用静态命令的形式。
```
// ES6模块
import { stat, exists, readFile } from 'fs';
```
上面代码的实质是从fs模块加载3个方法，其他方法不加载。这种加载称为`“编译时加载”，即ES6可以在编译时就完成模块加载，效率要比CommonJS模块的加载方式高`。由于ES6模块是编译时加载，使得静态分析成为可能。

浏览器使用ES6模块的语法如下:
```
<script type="module" src="foo.js"></script>
```

## ES6的模块自动采用严格模式
严格模式主要有以下限制:
变量必须声明后再使用
函数的参数不能有同名属性，否则报错
不能使用with语句
不能对只读属性赋值，否则报错
不能使用前缀0表示八进制数，否则报错
不能删除不可删除的属性，否则报错
不能删除变量delete prop，会报错，只能删除属性delete global[prop]
eval不会在它的外层作用域引入变量
eval和arguments不能被重新赋值
arguments不会自动反映函数参数的变化
不能使用arguments.callee
不能使用arguments.caller
禁止this指向全局对象
不能使用fn.caller和fn.arguments获取函数调用的堆栈
增加了保留字（比如protected、static和interface）

## export命令
export命令用于规定模块的对外接口。一个模块就是一个独立的文件。该文件内部的所有变量，外部无法获取。如果希望外部能够读取模块内部的某个变量，就必须使用export关键字输出该变量。
```
// profile.js
var firstName = 'Michael';
var lastName = 'Jackson';
var year = 1958;

export {firstName, lastName, year};
```
通常情况下，export输出的变量就是本来的名字，但是可以使用as关键字重命名。

需要特别注意的是，export命令规定的是对外的接口，必须与模块内部的变量建立一一对应关系:
```
// 写法一
export var m = 1;

// 写法二
var m = 1;
export {m};

// 写法三
var n = 1;
export {n as m};
```
它们的实质是，在接口名与模块内部变量之间，建立了一一对应的关系。
export语句输出的接口，与其对应的值是动态绑定关系，即`通过该接口，可以取到模块内部实时的值`。
这一点与CommonJS规范完全不同。CommonJS模块输出的是值的缓存，不存在动态更新。
export命令可以出现在模块的任何位置，只要处于模块顶层就可以。如果处于块级作用域内，就会报错，下一节的import命令也是如此。这是因为处于条件代码块之中，就没法做静态优化了，违背了ES6模块的设计初衷。

## import命令
import命令接受一个对象（用大括号表示），里面指定要从其他模块导入的变量名。大括号里面的变量名，必须与被导入模块对外接口的名称相同。如果想为输入的变量重新取一个名字，import命令要使用as关键字，将输入的变量重命名。

注意，import命令具有提升效果，会提升到整个模块的头部，首先执行：
```
foo(); // 不会报错，因为import的执行早于foo的调用

import { foo } from 'my_module';
```

## 模块的整体加载
```
import * as circle from './circle';

console.log('圆面积：' + circle.area(4));
console.log('圆周长：' + circle.circumference(14));
```


## export default命令
```
// export-default.js
export default function () {
  console.log('foo');
}

// import-default.js
import customName from './export-default';
customName(); // 'foo'
```

export default命令用于指定模块的默认输出。本质上，export default就是输出一个叫做default的变量或方法，然后系统允许导入时为它取任意名字。

export default也可以用来输出类:
```
// MyClass.js
export default class { ... }

// main.js
import MyClass from 'MyClass';
let o = new MyClass();
```

## 模块的继承
```
export * from 'circle';  // 继承了circle模块
export var e = 2.71828182846;
export default function(x) {
  return Math.exp(x);
}
```
export *命令会忽略circle模块的default方法。然后，上面代码又输出了自定义的e变量和默认方法。

## ES6模块加载的实质
CommonJS模块输出的是一个值的拷贝，而ES6模块输出的是值的引用。
ES6模块的运行机制与CommonJS不一样，它遇到模块加载命令import时，不会去执行模块，而是只生成一个动态的只读引用。等到真的需要用到时，再到模块里面去取值，换句话说，ES6的输入有点像Unix系统的“符号连接”，原始值变了，import输入的值也会跟着变。因此，ES6模块是动态引用，并且不会缓存值，模块里面的变量绑定其所在的模块。

export通过接口，输出的是同一个值。不同的脚本加载这个接口，得到的都是同样的实例。
```
// mod.js
function C() {
  this.sum = 0;
  this.add = function () {
    this.sum += 1;
  };
  this.show = function () {
    console.log(this.sum);
  };
}

export let c = new C();

// x.js
import {c} from './mod';
c.add();

// y.js
import {c} from './mod';
c.show();

// main.js
import './x';
import './y';
```
$ babel-node main.js
1

## 循环加载
CommonJS模块的重要特性是加载时执行，即脚本代码在require的时候，就会全部执行。一旦出现某个模块被"循环加载"，就只输出已经执行的部分，还未执行的部分不会输出。

ES6处理“循环加载”与CommonJS有本质的不同。ES6模块是动态引用，如果使用import从一个模块加载变量（即import foo from 'foo'），那些变量不会被缓存，而是成为一个指向被加载模块的引用，需要开发者自己保证，真正取值的时候能够取到值。(即，可能会由于循环加载导致取到的值为undefined)