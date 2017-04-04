# const命令的基本特性

const声明的变量不得改变值，一旦声明，就必须立即初始化：

```
const foo;
// SyntaxError: Missing initializer in const declaration
```

对于复合类型的变量，变量名不指向数据，而是指向数据所在的地址。const命令只是保证变量名指向的地址不变，并不保证该地址的数据不变：
```
const foo = {};
foo.prop = 123;

foo.prop
// 123

foo = {}; // TypeError: "foo" is read-only
```
如果真的想将对象冻结，应该使用`Object.freeze`方法：
```
// 彻底冻结一个对象（包括对象的属性）
var constantize = (obj) => {
  Object.freeze(obj);
  Object.keys(obj).forEach( (key, value) => {
    if ( typeof obj[key] === 'object' ) {
      constantize( obj[key] );
    }
  });
};
```