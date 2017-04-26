---
layout: post
title:  "《TypeScript官方文档》读书笔记"
date: 2017-04-15 00:00:01
categories: 读书笔记-技术
tags: TypeScript JavaScript
excerpt: "完整地过一遍TypeScript文档"
---

* content
{:toc}
# 手册指南

## 基础类型

TypeScript支持与JavaScript几乎相同的数据类型，此外还提供了实用的枚举类型：

```javascript
let isDone: boolean = false;
let decLiteral: number = 6;
let name: string = "bob";
```

### 模版字符串

用于定义多行文本和内嵌表达式。 这种字符串是被反引号包围（ \`\`），并且以`${ expr }`这种形式嵌入表达式：
```javascript
let name: string = "Gene";
let age: number = 37;
let sentence: string = `Hello, my name is ${ name }.

I'll be ${ age + 1 } years old next month.`;
```

### 数组

有两种方式可以定义数组：

```javascript
let list: number[] = [1, 2, 3];
let list: Array<number> = [1, 2, 3];
```

### 元组

元组类型可以表示一个已知元素数量和类型的数组，各元素的类型不必相同。 
```javascript
// Declare a tuple type
let x: [string, number];
// Initialize it
x = ['hello', 10]; // OK
// Initialize it incorrectly
x = [10, 'hello']; // Error
```
**当访问一个越界的元素，会使用联合类型替代**：
```javascript
x[3] = 'world'; // OK, 字符串可以赋值给(string | number)类型

console.log(x[5].toString()); // OK, 'string' 和 'number' 都有 toString

x[6] = true; // Error, 布尔不是(string | number)类型
```

### 枚举

和理解的差不多，略。

### 任意值
用于不希望类型检查器对某些值进行检查而是直接让它们通过编译阶段的检查。
```javascript
let notSure: any = 4;
notSure = "maybe a string instead";
notSure = false; // okay, definitely a boolean

let list: any[] = [1, true, "free"];
list[1] = 100;
```
**any与Object的区别**：
Object类型的变量只是允许给它赋任意值但是却**不能够在它上面调用任意的方法**，即便它真的有这些方法：
```javascript
let notSure: any = 4;
notSure.ifItExists(); // okay, ifItExists might exist at runtime
notSure.toFixed(); // okay, toFixed exists (but the compiler doesn't check)

let prettySure: Object = 4;
prettySure.toFixed(); // Error: Property 'toFixed' doesn't exist on type 'Object'.
```

### 空值
表示没有任何类型。
```javascript
// 该函数没有返回值
function warnUser(): void {
    alert("This is my warning message");
}
```
**void类型的变量没有什么大用，因为只能为它赋予undefined和null**。

### Null和Undefined
undefined和null两者各自有自己的类型分别叫做undefined和null。 和 void相似，它们的本身的类型用处不是很大。
默认情况下null和undefined是所有类型的子类型，当指定了strictNullChecks标记，null和undefined只能赋值给void和它们各自。

### Never
表示永不存在的值的类型：是那些总是会抛出异常或根本就不会有返回值的函数表达式或箭头函数表达式的返回值类型； 变量也可能是never类型（当它们被永不为真的类型保护所约束时）。

never类型是任何类型的子类型。
```javascript
// 返回never的函数必须存在无法达到的终点
function error(message: string): never {
    throw new Error(message);
}

// 推断的返回值类型为never
function fail() {
    return error("Something failed");
}

// 返回never的函数必须存在无法达到的终点
function infiniteLoop(): never {
    while (true) {
    }
}
```

### 类型断言
类似其它语言里的类型转换，但是不进行特殊的数据检查和解构。 它没有运行时的影响，只是在编译阶段起作用。 
类型断言有两种形式：尖括号法和as语法：
```javascript
let someValue: any = "this is a string";

// 尖括号法
let strLength: number = (<string>someValue).length;

// as语法
let strLength: number = (someValue as string).length;
```
**当在TypeScript里使用JSX时，只有as语法断言是被允许的**。


## 变量声明
### var声明
JavaScript中var带来的作用域问题的回顾，略。

### let声明
let的基本介绍（块作用域及相关的时间死区、屏蔽等问题），略。

### const声明
ES6中const变量的内部状态是可修改的，TypeScript允许将对象的成员设置成只读的。

### 解构
```javascript
// 数组解构
let input = [1, 2];
let [first, second] = input;

// 对象解构
let o = {
    a: "foo",
    b: 12,
    c: "bar"
}
let { a, b } = o;

({ a, b } = { a: "baz", b: 101 });  // 这种方式需要用圆括号
```

解构时使用...语法创建**剩余变量**：
```javascript
let { a, ...passthrough } = o;
let total = passthrough.b + passthrough.c.length;
```

解构时可以给属性重命名：
```javascript
let { a: newName1, b: newName2 } = o;
```
这里给属性重命名的语法不同于给属性指定类型，正确的指定类型的方式是：
```javascript
let {a, b}: {a: string, b: number} = o;
```

**解构时使用默认值可以使得在属性为undefined时使用缺省值**：
```javascript
let { a, b = 1001 } = wholeObject;
```

**解构也能用于函数声明**：
```javascript
type C = { a: string, b?: number }
function f({ a, b }: C): void {
    // ...
}
```

### 展开
将一个数组展开为另一个数组，或将一个对象展开为另一个对象：
```javascript
let first = [1, 2];
let second = [3, 4];
let bothPlus = [0, ...first, ...second, 5];

let defaults = { food: "spicy", price: "$$", ambiance: "noisy" };
let search = { ...defaults, food: "rich" };
```
从左至右进行处理，出现在展开对象**后面的属性会覆盖前面的属性**。
对象展开只能应用于对象自身的可枚举的属性上（对象方法会丢失）。
TypeScript编译器不允许展开泛型函数上的类型参数。


## 接口
接口的作用就是为类型命名。
```javascript
// 编译器只会检查那些必需的属性是否存在，并且其类型是否匹配，忽略其他属性和属性的顺序
interface LabelledValue {
  label: string;
}

function printLabel(labelledObj: LabelledValue) {
  console.log(labelledObj.label);
}

let myObj = {size: 10, label: "Size 10 Object"};
printLabel(myObj);
```

### 可选属性
```javascript
interface SquareConfig {
  color?: string;
  width?: number;
}

function createSquare(config: SquareConfig): {color: string; area: number} {
   // ...
}

let mySquare = createSquare({color: "black"});
```

### 只读属性
```javascript
interface Point {
    readonly x: number;
    readonly y: number;
}
```
最简单判断该用readonly还是const的方法是看要把它做为变量使用还是做为一个属性。 做为变量使用的话用const，若做为属性则使用readonly。

TypeScript具有ReadonlyArray<T>类型，它与Array<T>相似，只是把所有可变方法去掉了，因此可以确保数组创建后再也不能被修改：
```javascript
let a: number[] = [1, 2, 3, 4];
let ro: ReadonlyArray<number> = a;
ro[0] = 12; // error!
```

### 额外的属性检查
TypeScript中对象字面量会被特殊对待而且会经过 额外属性检查，当将它们赋值给变量或作为参数传递的时候。 如果一个对象字面量存在任何目标类型不包含的属性时，会得到一个错误：
```javascript
// error: 'colour' not expected in type 'SquareConfig'（尽管width属性存在）
let mySquare = createSquare({ colour: "red", width: 100 });
```
使用类型断言绕开检查：
```javascript
let mySquare = createSquare({ width: 100, opacity: 0.5 } as SquareConfig);
```
最佳的方式是能够**添加一个字符串索引签名**，如果SquareConfig带有上面定义的类型的color和width属性，并且还会带有任意数量的其它属性，那么可以这样定义它：
```javascript
interface SquareConfig {
    color?: string;
    width?: number;
    [propName: string]: any;
}
```

### 函数类型
```javascript
interface SearchFunc {
  (source: string, subString: string): boolean;
}

let mySearch: SearchFunc;
mySearch = function(source: string, subString: string) {
  let result = source.search(subString);
  if (result == -1) {
    return false;
  }
  else {
    return true;
  }
}
```
参数名不需要与接口里定义的名字相匹配。

### 可索引的类型
可以描述那些能够**通过索引得到**的类型，比如a[10]或ageMap["daniel"]。
```javascript
interface StringArray {
  [index: number]: string;
}

let myArray: StringArray;
myArray = ["Bob", "Fred"];

let myStr: string = myArray[0];
```
共有支持两种索引签名：字符串和数字。 可以同时使用两种类型的索引，但是数字索引的返回值必须是字符串索引返回值类型的子类型。

可以将索引签名设置为只读，这样就防止了给索引赋值：
```javascript
interface ReadonlyStringArray {
    readonly [index: number]: string;
}
let myArray: ReadonlyStringArray = ["Alice", "Bob"];
myArray[2] = "Mallory"; // error!
```

## 类类型
```javascript
interface ClockInterface {
    currentTime: Date;
    setTime(d: Date);
}

class Clock implements ClockInterface {
    currentTime: Date;
    setTime(d: Date) {
        this.currentTime = d;
    }
    constructor(h: number, m: number) { }
}
```

**类静态部分与实例部分的区别**
有点复杂，回头细看，略。

### 扩展接口
一个接口可以继承多个接口，创建出多个接口的合成接口。

### 混合类型
即接口内有方法、属性等，略。

### 接口继承类
```javascript
// 当接口继承了一个类类型时，它会继承类的成员但不包括其实现。
class Control {
    private state: any;
}

interface SelectableControl extends Control {
    select(): void;
}
```

## 类
TypeScript的面相对象：
类继承（extends）、访问控制符（public、private、protected、readonly）、存取器（getter、setter）、静态属性（static）、抽象类（abstract）。

详略。

## 函数
在TypeScript里，虽然已经支持类，命名空间和模块，但函数仍然是主要的定义行为的地方。

### 可选参数
```javascript
function buildName(firstName: string, lastName?: string) {
    // ...
}
```

### 默认参数
```javascript
function buildName(firstName: string, lastName = "Smith") {
    // ...
}
```

### 剩余参数
```javascript
function buildName(firstName: string, ...restOfName: string[]) {
  return firstName + " " + restOfName.join(" ");
}

let employeeName = buildName("Joseph", "Samuel", "Lucas", "MacKinzie");
```

### this
箭头函数能保存函数创建时的this值，而不是调用时的值。
比较复杂，回头再看，略。

### 重载
JavaScript本身是个动态语言。 JavaScript里函数根据传入不同的参数而返回不同类型的数据是很常见的。


## 泛型
组件不仅能够支持当前的数据类型，同时也能支持未来的数据类型，这在创建大型系统时提供了十分灵活的功能。
```javascript
function identity<T>(arg: T): T {
    return arg;
}
```

### 泛型类
细节讨论略。

### 泛型约束
```javascript
interface Lengthwise {
    length: number;
}

function loggingIdentity<T extends Lengthwise>(arg: T): T {
    console.log(arg.length);  // Now we know it has a .length property, so no more error
    return arg;
}
```

## 枚举
略。

## 类型推论
TypeScript里，在有些没有明确指出类型的地方，类型推论会帮助提供类型。

### 最佳通用类型
当需要从几个表达式中推断类型时候，会使用这些表达式的类型来推断出一个最合适的通用类型。

### 上下文类型
略。

## 类型兼容性
TypeScript里的类型兼容性是基于结构子类型的（而非名义（nominal）类型）：
```javascript
interface Named {
    name: string;
}

class Person {
    name: string;
}

let p: Named;
// OK, because of structural typing
p = new Person();
```

详略。

## 高级类型

### 交叉类型
将多个类型合并为一个类型。例如， Person & Serializable & Loggable同时是Person和Serializable和Loggable。 就是说这个类型的对象同时拥有了这三种类型的成员。

大多是在混入（mixins）或其它不适合典型面向对象模型的地方看到交叉类型的使用。
```javascript 
function extend<T, U>(first: T, second: U): T & U {
    let result = <T & U>{};
    for (let id in first) {
        (<any>result)[id] = (<any>first)[id];
    }
    for (let id in second) {
        if (!result.hasOwnProperty(id)) {
            (<any>result)[id] = (<any>second)[id];
        }
    }
    return result;
}

class Person {
    constructor(public name: string) { }
}
interface Loggable {
    log(): void;
}
class ConsoleLogger implements Loggable {
    log() {
        // ...
    }
}
var jim = extend(new Person("Jim"), new ConsoleLogger());
var n = jim.name;
jim.log();
```

### 联合类型
```javascript 
function padLeft(value: string, padding: string | number) {
    // ...
}

let indentedString = padLeft("Hello world", true); // errors during compilation
```
联合类型表示一个值可以是几种类型之一。如果一个值是联合类型，则只能访问此联合类型的所有类型里共有的成员

## 类型保护与区分类型
类型保护就是一些表达式，它们会在运行时检查以确保在某个作用域里的类型。 要定义一个类型保护，只要简单地定义一个函数，它的返回值是一个类型断言，一个断言是 `parameterName is Type`这种形式：
```javascript 
function isFish(pet: Fish | Bird): pet is Fish {
    return (<Fish>pet).swim !== undefined;
}
```

### typeof类型保护
```javascript
function isNumber(x: any): x is number {
    return typeof x === "number";
}
```

### instanceof类型保护
```javascript
let padder: Padder = getRandomPadder();

if (padder instanceof SpaceRepeatingPadder) {
    padder; // 类型细化为'SpaceRepeatingPadder'
}
```

### 类型别名
可以作用于原始值，联合类型，元组以及其它任何你需要手写的类型。
```javascript
type Name = string;
type NameResolver = () => string;
type NameOrResolver = Name | NameResolver;
type Container<T> = { value: T };
```

### 字符串字面量类型
```javascript
type Easing = "ease-in" | "ease-out" | "ease-in-out";
class UIElement {
    animate(dx: number, dy: number, easing: Easing) {
        if (easing === "ease-in") {
            // ...
```

### 可辨识联合
可以合并字符串字面量类型，联合类型，类型保护和类型别名来创建一个叫做可辨识联合的高级模式，它也称做标签联合或代数数据类型。 可辨识联合在函数式编程很有用处。 

过于复杂，回头再看，略。

### 多态的this类型
略。


## Symbols
ES6语法介绍，略。

## 迭代器和生成器
ES6语法介绍，略。


## 模块
TypeScript与ECMAScript 2015一样，任何包含顶级import或者export的文件都被当成一个模块。

模块基本语法，略。

### 使用其它的JavaScript库
要想描述非TypeScript编写的类库的类型，需要声明类库所暴露出的API。
```javascript
/// <reference path="node.d.ts"/>
import * as URL from "url";
let myUrl = URL.parse("http://www.typescriptlang.org");
```

### 创建模块结构指导
虽然命名空间有时候有它们的用处，在使用模块的时候它们额外地增加了一层。 这对用户来说是很不便的并且通常是多余的。命名空间在使用模块时几乎没什么价值。

如果仅导出单个class或function，使用export default。

**危险信号**
以下均为模块结构上的危险信号：
* 文件的顶层声明是export namespace Foo { ... } （删除Foo并把所有内容向上层移动一层）
* 文件只有一个export class或export function （考虑使用export default）
* 多个文件的顶层具有同样的export namespace Foo { （不要以为这些会合并到一个Foo中！）

## 命名空间
```javascript
namespace Validation {
    export interface StringValidator {
        isAcceptable(s: string): boolean;
    }
    // ...
```
同一个命名空间的内容可以分布在多个文件中。


## 命名空间和模块
命名空间是位于全局命名空间下的一个普通的带有名字的JavaScript对象。

### 命名空间和模块的陷阱
详略。

## 模块解析
### 相对 vs. 非相对模块导入
相对导入是以/，./或../开头的：
```javascript
import Entry from "./components/Entry";
import { DefaultHeaders } from "../constants/http";
import "/mod";
```
所有其它形式的导入被当作非相对的。
相对导入解析时是相对于导入它的文件来的，并且不能解析为一个外部模块声明。 **你应该为你自己写的模块使用相对导入，这样能确保它们在运行时的相对位置**。

### 模块解析策略
共有两种可用的模块解析策略：Node和Classic。 你可以使用 --moduleResolution标记指定使用哪种模块解析策略。 若未指定，那么在使用了 --module AMD | System | ES2015时的默认值为Classic，其它情况时则为Node。
详略。

### 路径映射
TypeScript编译器通过使用tsconfig.json文件里的"paths"来支持这样的声明映射。


## 声明合并
指编译器将针对同一个名字的两个独立声明合并为单一声明。 合并后的声明同时拥有原先两个声明的特性。 任何数量的声明都可被合并；不局限于两个声明。
比如合并接口：
```javascript
interface Box {
    height: number;
    width: number;
}

interface Box {
    scale: number;
}

let box: Box = {height: 5, width: 6, scale: 10};
```
详略。

## JSX
略。

## 装饰器
ES超前特性（建议征集中），略。

## Mixins
略。

## 三斜线指令
三斜线指令是包含单个XML标签的单行注释。 注释的内容会做为编译器指令使用。

仅可放在包含它的文件的最顶端。
```javascript
/// <reference path="..." />
```
三斜线引用告诉编译器在编译过程中要引入的额外的文件。

详略。

## 错误信息列表
略。

# 声明文件

## 结构
涵盖了如何识别常见库的模式，与怎么样书写符合相应模式的声明文件。
略。

## 举例
略。

## 规范

* 不要使用如下类型：Number，String，Boolean或Object。 这些类型指的是非原始的装盒对象，它们几乎没在JavaScript代码里正确地使用过。
应该使用类型number，string，and boolean：
```javascript
function reverse(s: string): string;
```
* 如果要使用Object类型，考虑使用any代替。

* 不要定义一个从来没使用过其类型参数的泛型类型。 

* 不要为返回值被忽略的回调函数设置一个any类型的返回值类型，应该设置为void类型。
```javascript
function fn(x: () => void) {
    x();
}
```

* 不要在回调函数里使用可选参数
```javascript
/* 错误 */
interface Fetcher {
    getObject(done: (data: any, elapsedTime?: number) => void): void;
}
```
* 不要因为回调函数参数个数不同而写不同的重载。

* 不要把一般的重载放在精确的重载前面。

* 不要为仅在末尾参数不同时写不同的重载。

* 不要为仅在某个位置上的参数类型不同的情况下定义重载。

## 深入
高级主题，略。

## 模板
一些ts文件模板，略。

## 发布
发布npm包，略。

## 使用
**获取类型声明文件**
比如获取lodash库的声明文件：
```javascript
npm install --save @types/lodash
```
然后使用：
```javascript
import * as _ from "lodash";
_.padStart("Hello TypeScript!", 20, " ");
```
大多数情况下，类型声明包的名字总是与它们在npm上的包的名字相同，但是有@types/前缀， 但如果你需要的话，可以在https://aka.ms/types这里查找。


# 项目配置
## tsconfig.json

**compilerOptions**
可以被忽略，这时编译器会使用默认值。

**files**
指定一个包含相对或绝对文件路径的列表。 

**include、exclude**
指定一个文件glob匹配模式列表。 支持的glob通配符有：
* \* 匹配0或多个字符（不包括目录分隔符）
* ? 匹配一个任意字符（不包括目录分隔符）
* \*\*/ 递归匹配任意子目录
((如果一个glob模式里的某部分只包含*或.*，那么仅有支持的文件扩展名类型被包含在内**（比如默认.ts，.tsx，和.d.ts， 如果 allowJs设置能true还包含.js和.jsx）。

如果"files"和"include"都没有被指定，编译器默认包含当前目录和子目录下所有的TypeScript文件（.ts, .d.ts 和 .tsx），排除在"exclude"里指定的文件。如果allowJs被设置成true，JS文件（.js和.jsx）也被包含进来。 如果指定了 "files"或"include"，编译器会将它们结合一并包含进来。 使用 "outDir"指定的目录下的文件永远会被编译器排除，除非明确地使用"files"将其包含进来（这时就算用exclude指定也没用）。

使用"include"引入的文件可以使用"exclude"属性过滤。 然而，**通过 "files"属性明确指定的文件却总是会被包含在内，不管"exclude"如何设置**。 如果没有特殊指定， "exclude"默认情况下会排除node_modules，bower_components，jspm_packages和<outDir>目录。

任何被"files"或"include"指定的文件所引用的文件也会被包含进来。A.ts引用了B.ts，因此B.ts不能被排除，除非引用它的A.ts在"exclude"列表中。

tsconfig.json文件可以是个空文件，那么所有默认的文件都会以默认配置选项编译。

在命令行上指定的编译选项会覆盖在tsconfig.json文件里的相应选项。

默认所有可见的"@types"包会在编译过程中被包含进来。 node_modules/@types文件夹下以及它们子文件夹下的所有包都是可见的； 也就是说， ./node_modules/@types/，../node_modules/@types/和../../node_modules/@types/等等。

如果指定了typeRoots，只有typeRoots下面的包才会被包含进来。

tsconfig.json文件可以利用extends属性从另一个配置文件里继承配置。

详略。

## 编译选项
略。

## 在MSBuild里使用编译选项
略。

## 与其它构建工具整合
### webpack
```
npm install ts-loader --save-dev
```
基本配置：
```javascript
module.exports = {
    entry: "./src/index.tsx",
    output: {
        filename: "bundle.js"
    },
    resolve: {
        // Add '.ts' and '.tsx' as a resolvable extension.
        extensions: ["", ".webpack.js", ".web.js", ".ts", ".tsx", ".js"]
    },
    module: {
        loaders: [
            // all files with a '.ts' or '.tsx' extension will be handled by 'ts-loader'
            { test: /\.tsx?$/, loader: "ts-loader" }
        ]
    }
};
```
其他，略。
