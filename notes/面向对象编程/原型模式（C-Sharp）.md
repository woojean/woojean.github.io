# 原型模式（C#）

用原型实例指定创建对象的种类，并且通过拷贝这些原型创建新的对象。其实就是从一个对象再创建另外一个可定制的对象。

**原型类**

```java
abstract class Prototype
{
    private string id;

    // Constructor 
    public Prototype(string id)
    {
        this.id = id;
    }

    // Property 
    public string Id
    {
        get { return id; }
    }
		
    // 原型类需要定义一个Clone方法
    public abstract Prototype Clone();
}
```
**具体原型类**

```java
class ConcretePrototype1 : Prototype
{
    // Constructor 
    public ConcretePrototype1(string id): base(id)
    {
    }

    public override Prototype Clone()
    {
        // Shallow copy 
        return (Prototype)this.MemberwiseClone();
        // MemberwiseClone方法创建当前对象的浅表副本
    }
}

class ConcretePrototype2 : Prototype
{
    … …
}
```
**客户端代码**

```java
ConcretePrototype1 p1 = new ConcretePrototype1("I");
ConcretePrototype1 c1 = (ConcretePrototype1)p1.Clone();
Console.WriteLine("Cloned: {0}", c1.Id);
ConcretePrototype2 p2 = new ConcretePrototype2("II");
ConcretePrototype2 c2 = (ConcretePrototype2)p2.Clone();
Console.WriteLine("Cloned: {0}", c2.Id);
```