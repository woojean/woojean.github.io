# 装饰模式（C#）

动态地给一个对象添加一些额外的职责，就增加功能来说，装饰模式比生成子类更为灵活。

**Component定义一个对象接口，可以给这些对象动态地添加职责**

```java
abstract class Component
{
    public abstract void Operation();
}
```
**ConcreteComponent定义一个具体的对象，也可以给这个对象添加一些职责**

```java
class ConcreteComponent : Component
{
    public override void Operation()
    {
       Console.WriteLine("具体对象的操作");
    }
}
```
**装饰器类一定也要继承被装饰类（的抽象基类），这是实现链式操作的关键。**

Decorator为装饰抽象类，继承了Component类，从外类来扩展Component类的功能，但对于Component来说，是无须知道Decorator存在的。

```java
abstract class Decorator : Component
{
    protected Component component;
  
    public void SetComponent(Component component)
    {
        this.component = component;
    }
  
    public override void Operation()
    {
        if (component != null)
        {
            component.Operation();
        }
    }
}
```
**ConcreteDecorator就是具体的装饰对象，起到给Component添加职责的功能**

```java
class ConcreteDecoratorA : Decorator
{
    private string addedState;
    public override void Operation()
    {
        base.Operation();
        addedState = "New State";
        Console.WriteLine("具体装饰对象A的操作");
    }
}

class ConcreteDecoratorB : Decorator
{
    public override void Operation()
    {
        base.Operation();
        AddedBehavior();
        Console.WriteLine("具体装饰对象B的操作");
    }
    private void AddedBehavior()
    {
    }
}
```
**客户端代码**

```
ConcreteComponent c = new ConcreteComponent();
ConcreteDecoratorA d1 = new ConcreteDecoratorA();
ConcreteDecoratorB d2 = new ConcreteDecoratorB();
d1.SetComponent(c);
d2.SetComponent(d1);
d2.Operation();
```