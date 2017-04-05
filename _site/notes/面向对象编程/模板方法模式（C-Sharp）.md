# 模板方法模式（C#）

定义一个操作中的算法的骨架，而将一些步骤延迟到子类中。模板方法使得子类可以不改变一个算法的结构即可重定义该算法的某些特定步骤。

**AbstractClass是抽象类定义并实现了一个模板方法。这个模板方法一般是一个具体方法，它给出了一个顶级逻辑的骨架，而逻辑的组成步骤在相应的抽象操作中，推迟到子类实现。顶级逻辑也有可能调用一些具体方法**

```java
abstract class AbstractClass
{
    public abstract void PrimitiveOperation1();
    public abstract void PrimitiveOperation2();

    public void TemplateMethod()
    {
        PrimitiveOperation1();
        PrimitiveOperation2();
        Console.WriteLine("");
    }
}
```
**ConcreteClass实现父类所定义的一个或多个抽象方法。每一个AbstractClass都可以有任意多个ConcreteClass与之对应，而每一个ConcreteClass都可以给出这些抽象方法的不同实现，从而使得顶级逻辑的实现各不相同。**

```java
class ConcreteClassA : AbstractClass
{
    public override void PrimitiveOperation1()
    {
        Console.WriteLine("具体类A方法1实现");
    }
    public override void PrimitiveOperation2()
    {
        Console.WriteLine("具体类A方法2实现");
    }
}

class ConcreteClassB : AbstractClass
{
    … …
}
```
**客户端代码**

```java
AbstractClass c;
c = new ConcreteClassA();
c.TemplateMethod();
c = new ConcreteClassB();
c.TemplateMethod();
```


## 模板方法模式特点

模板方法模式是通过把不变行为搬到超类中，去除子类中的重复代码来体现它的优势。当不变的和可变的行为在方法的子类实现中混合在一起的时候，不变的行为就会在子类中重复出现。通过模板方法模式把这些行为搬移到单一的地方，这样就帮助子类摆脱重复的不变行为的纠缠。