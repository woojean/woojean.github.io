# 枚举的values()方法

虽然可以在枚举类型上调用values()方法，但是Enum类中实际并没有定义该方法。
`values()是由编译器添加的static方法`。（Enum的valueOf()方法带两个参数，只带一个参数的valueOf()方法也是编译器添加的 ）