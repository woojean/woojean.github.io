# 桥接模式（C#）

将抽象部分与它的实现部分分离，使它们都可以独立的变化。其实就是实现系统可能有多角度分类，每一种分类都有可能变化，那么就把这种多角度分离出来让它们独立变化，减少它们之间的耦合。
**Implementor类**

```java
abstract class Implementor
{
    public abstract void Operation();
}
```
**ConcreteImplementorA和ConcreteImplementorB等派生类**
```java
class ConcreteImplementorA : Implementor
{
    public override void Operation()
    {
        Console.WriteLine("具体实现A的方法执行");
    }
}

class ConcreteImplementorB : Implementor
{
    public override void Operation()
    {
        Console.WriteLine("具体实现B的方法执行");
    }
}
```
**Abstraction类**
```java
class Abstraction
{
    protected Implementor implementor;

    public void SetImplementor(Implementor implementor)
    {
        this.implementor = implementor;
    }

    public virtual void Operation()
    {
        implementor.Operation();
    }
}
```
**RefinedAbstraction类**
```java
class RefinedAbstraction : Abstraction
{
    public override void Operation()
    {
        implementor.Operation();
    }
}
```
**客户端实现**
```java
Abstraction ab = new RefinedAbstraction();
ab.SetImplementor(new ConcreteImplementorA());
ab.Operation();
ab.SetImplementor(new ConcreteImplementorB());
ab.Operation();
Console.Read();
```