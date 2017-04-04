# ES6对字符串的扩展



JavaScript`允许采用\uxxxx形式表示一个字符`，其中“xxxx”表示字符的`码点`。但是，这种表示法只限于\u0000——\uFFFF之间的字符。超出这个范围的字符，必须用两个双字节的形式表达。
```
"\uD842\uDFB7"
// "𠮷"

"\u20BB7"  // 超过0xFFFF，ES5会理解成“\u20BB+7”，\u20BB是一个不可打印字符，所以只会显示一个空格
// " 7"
```

ES6对Unicode字符的表示做出了改进，只要将码点放入大括号，就能正确解读4字节字符：
```
"\u{20BB7}"
// "𠮷"
```

因此ES6共有6种方法可以表示一个字符：
```
'\z' === 'z'  // true
'\172' === 'z' // true
'\x7A' === 'z' // true
'\u007A' === 'z' // true
'\u{7A}' === 'z' // true
```


## 字符串的遍历器接口
ES6为字符串添加了遍历器接口，使得字符串可以被for...of循环遍历：
```
for (let codePoint of 'foo') {
  console.log(codePoint)
}
// "f"
// "o"
// "o"
```

## 模板字符串
模板字符串（template string）是增强版的字符串，用反引号（`）标识。它可以当作普通字符串使用，也可以用来定义多行字符串，或者在字符串中嵌入变量。
```
// 普通字符串
`In JavaScript '\n' is a line-feed.`

// 多行字符串
`In JavaScript this is
 not legal.`

console.log(`string text line 1
string text line 2`);

// 字符串中嵌入变量
var name = "Bob", time = "today";
`Hello ${name}, how are you ${time}?`
```


## 标签模板
模板字符串可以紧跟在一个函数名后面，该函数将被调用来处理这个模板字符串：
```
alert`123`
// 等同于
alert(123)
```
标签模板其实不是模板，而是函数调用的一种特殊形式。
如果模板字符里面有变量，会将模板字符串先处理成多个参数，再调用函数：
```
var a = 5;
var b = 10;

tag`Hello ${ a + b } world ${ a * b }`;
// 等同于
tag(['Hello ', ' world ', ''], 15, 50);
```

## API
codePointAt()  // 能够正确处理4个字节储存的字符，返回一个字符的码点
String.fromCodePoint()  // 用于从码点返回对应字符
at()  // 可以识别Unicode编号大于0xFFFF的字符，返回正确的字符
normalize()  // 用来将字符的不同表示方法统一为同样的形式，这称为Unicode正规化（字符合成）
includes()  // 返回布尔值，表示是否找到了参数字符串。
startsWith()  // 返回布尔值，表示参数字符串是否在源字符串的头部。
endsWith()  // 返回布尔值，表示参数字符串是否在源字符串的尾部。
repeat() // 返回一个新字符串，表示将原字符串重复n次
padStart() // 头部补全
padEnd() // 尾部补全
String.raw()  // 往往用来充当模板字符串的处理函数，返回一个斜杠都被转义（即斜杠前面再加一个斜杠）的字符串，对应于替换变量后的模板字符串。