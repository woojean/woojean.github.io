# 抽象工厂模式（C#）

提供一个创建一系列相关或相互依赖对象的接口，而无需指定它们具体的类。使得改变一个应用的具体工厂变得非常容易，它只需要改变具体工厂即可使用不同的产品配置。此外，它让具体的创建实例过程与客户端分离，客户端是通过它们的抽象接口操纵实例，产品的具体类名也被具体工厂的实现分离，不会出现在客户代码中。

**抽象工厂使产品类型、工厂类型（实际还是对应不同的产品类型）两个维度都可以扩展**

```java
abstract class AbstractFactory
{
    public abstract AbstractProductA CreateProductA();
    public abstract AbstractProductB CreateProductB();
}

class ConcreteFactory1 : AbstractFactory
{
    public override AbstractProductA CreateProductA()
    {
        return new ProductA1();
    }
    public override AbstractProductB CreateProductB()
    {
        return new ProductB1();
    }
}

class ConcreteFactory2 : AbstractFactory
{
    public override AbstractProductA CreateProductA()
    {
        return new ProductA2();
    }
    public override AbstractProductB CreateProductB()
    {
        return new ProductB2();
    }
}
abstract class AbstractProductA
{
}

abstract class AbstractProductB
{
    public abstract void Interact(AbstractProductA a);
}

class ProductA1 : AbstractProductA
{
}

class ProductB1 : AbstractProductB
{
    public override void Interact(AbstractProductA a)
   {
        Console.WriteLine(this.GetType().Name +
          " interacts with " + a.GetType().Name);
   }
}

class ProductA2 : AbstractProductA
{
}

class ProductB2 : AbstractProductB
{
    public override void Interact(AbstractProductA a)
    {
        Console.WriteLine(this.GetType().Name +
          " interacts with " + a.GetType().Name);
    }
}

class Client
{
    private AbstractProductA AbstractProductA;
    private AbstractProductB AbstractProductB;

    // Constructor 
    public Client(AbstractFactory factory)
    {
        abstractProductB = factory.CreateProductB();
        abstractProductA = factory.CreateProductA();
    }

    public void Run()
    {
        abstractProductB.Interact(AbstractProductA);
    }
}

static void Main(string[] args)
{
    abstractFactory factory1 = new ConcreteFactory1();
        Client c1 = new Client(factory1);
        c1.Run();

    abstractFactory factory2 = new ConcreteFactory2();
        Client c2 = new Client(factory2);
        c2.Run();
}
```
所有在用简单工厂的地方，都可以考虑用反射技术来去除switch或if，解除分支判断带来的耦合。
反射的基本格式：Assembly.Load(“程序集名称”).CreateInstance(“命名空间.类名称”)；因为是字符串，所以可以用变量来处理，也可以根据需要更换。