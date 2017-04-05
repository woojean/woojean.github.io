# 访问者模式（C#）

表示一个作用于某对象结构中的各元素的操作。它使你可以在不改变各元素的类的前提下定义作用于这些元素的新操作。
访问者模式适用于数据结构相对稳定的系统，它把数据结构和作用于结构上的操作之间的耦合解脱开，使得操作集合可以相对自由地演化。访问者的优势在于增加新的操作很容易，因为这就意味着增加一个新的访问者。不足在于，使增加新的数据结构变得困难了。

**Visitor类**
```java
abstract class Visitor
{
    public abstract void VisitConcreteElementA(ConcreteElementA concreteElementA);
    public abstract void VisitConcreteElementB(ConcreteElementB concreteElementB);
}
```
**ConcreteVisitor类**
```java
class ConcreteVisitor1 : Visitor
{
    public override void VisitConcreteElementA(ConcreteElementA concreteElementA)
    {
        Console.WriteLine("{0}被{1}访问", concreteElementA.GetType().Name, this.GetType().Name);
    }

    public override void VisitConcreteElementB(ConcreteElementB concreteElementB)
    {
        Console.WriteLine("{0}被{1}访问", concreteElementB.GetType().Name, this.GetType().Name);
    }
}

class ConcreteVisitor2 : Visitor
{
    public override void VisitConcreteElementA(ConcreteElementA concreteElementA)
    {
        Console.WriteLine("{0}被{1}访问", concreteElementA.GetType().Name, this.GetType().Name);
    }

    public override void VisitConcreteElementB(ConcreteElementB concreteElementB)
    {
        Console.WriteLine("{0}被{1}访问", concreteElementB.GetType().Name, this.GetType().Name);
    }
}
```
**Element类**
```java
abstract class Element
{
    public abstract void Accept(Visitor visitor);
}
```
**ConcreteElement类**
```java
class ConcreteElementA : Element
{
	// 被访问元素定义Accept操作来接受一个Visitor，使该Visitor指向当前元素后，调用Visitor的访问方法
    public override void Accept(Visitor visitor)
    {
        visitor.VisitConcreteElementA(this);
    }

    public void OperationA()
    { }
}

class ConcreteElementB : Element
{
    public override void Accept(Visitor visitor)
    {
        visitor.VisitConcreteElementB(this);
    }

    public void OperationB()
    { }
}
```
**ObjectStucture类**
```java
class ObjectStructure
{
    private IList<Element> elements = new List<Element>();

    public void Attach(Element element)
    {
        elements.Add(element);
    }

    public void Detach(Element element)
    {
        elements.Remove(element);
    }

    public void Accept(Visitor visitor)
    {
        foreach (Element e in elements)
        {
            e.Accept(visitor);
        }
    }
}
```
**客户端代码**
```java
ObjectStructure o = new ObjectStructure();
o.Attach(new ConcreteElementA());
o.Attach(new ConcreteElementB());
ConcreteVisitor1 v1 = new ConcreteVisitor1();
ConcreteVisitor2 v2 = new ConcreteVisitor2();
o.Accept(v1);
o.Accept(v2);
```
