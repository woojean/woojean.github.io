# Object的实例具有哪些属性和方法？

Object 的每个实例都具有下列属性和方法。
constructor ：保存着用于创建当前对象的构造函数
hasOwnProperty(propertyName) ：用于检查给定的属性在当前对象实例中（而不是在实例的原型中）是否存在。其中，作为参数的属性名（ propertyName ）必须以字符串形式指定（例如： o.hasOwnProperty("name") ）。
isPrototypeOf(object) ：用于检查传入的对象是否是传入对象的原型。
propertyIsEnumerable(propertyName) ：用于检查给定的属性是否能够使用 for-in 语句来枚举
toLocaleString() ：返回对象的字符串表示，该字符串与执行环境的地区对应。
toString() ：返回对象的字符串表示。
valueOf() ：返回对象的字符串、数值或布尔值表示。通常与 toString() 方法的返回值相同。