# 适配器模式（C#）

将一个类的接口转换成客户希望的另外一个接口，使得原本由于接口不兼容而不能一起工作的那些类可以一起工作。适配器模式包括类适配器模式和对象适配器模式，类适配器模式需要支持多重继承。

**Target类，是客户所期待的接口。目标可以是具体的或抽象的类，也可以是接口。**
```java
class Target
{
    public virtual void Request()
{
        Console.WriteLine("普通请求");
}
}
```
**Adaptee，需要适配的类。**
```java
class Adaptee
{
    public void SpecificRequest()
    {
        Console.WriteLine("特殊请求");
    }
}
```
**Adapter，通过在内部包装一个Adaptee对象，把源接口转换成目标接口。**
```java
class Adapter : Target
{
    private Adaptee adaptee = new Adaptee();

    public override void Request()
    {
        adaptee.SpecificRequest();
    }
}
```
**客户端代码**
```java
Target target = new Adapter();
target.Request();
```