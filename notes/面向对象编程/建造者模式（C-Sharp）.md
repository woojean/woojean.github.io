# 建造者模式（C#）

也称生成器模式，将一个复杂对象的构建与它的表示分离，使得同样的构建过程可以创建不同的表示。用户只需指定需要建造的类型就可以得到它们，而具体的过程和细节就不需知道了。

**产品类，由多个部件组成**

```java
class Product
{
    IList<string> parts = new List<string>();

    public void Add(string part)// 添加产品部件
    {
        parts.Add(part);
    }

    public void Show()
    {
        Console.WriteLine("\n产品 创建 ----");
        foreach (string part in parts)
        {
            Console.WriteLine(part);
        }
    }
}
```
**抽象建造者类，确定产品由两个部件PartA和PartB组成，并声明一个得到产品建造后结果的方法GetResult。**

```java
abstract class Builder
{
    public abstract void BuildPartA();
    public abstract void BuildPartB();
    public abstract Product GetResult();
}
```
**具体建造者类**

```java
class ConcreteBuilder1 : Builder
{
    private Product product = new Product();

    public override void BuildPartA()
    {
        product.Add("部件A");
    }

    public override void BuildPartB()
    {
        product.Add("部件B");
    }

    public override Product GetResult()
    {
        return product;
    }
}

class ConcreteBuilder2 : Builder
{
    private Product product = new Product();
    public override void BuildPartA()
    {
        product.Add("部件X");
    }

    public override void BuildPartB()
    {
        product.Add("部件Y");
    }

    public override Product GetResult()
    {
        return product;
    }
}
```
**指挥者类，用来指挥建造过程（即调用建造者类的实际建造方法，完成建造过程以便后续返回建造好的对象）**

```java
class Director
{
    public void Construct(Builder builder)
    {
        builder.BuildPartA();
        builder.BuildPartB();
    }
}
```
**客户端代码，客户不需要知道具体的建造过程**

```java
Director director = new Director();
Builder b1 = new ConcreteBuilder1();
Builder b2 = new ConcreteBuilder2();
director.Construct(b1);
Product p1 = b1.GetResult();
p1.Show();
director.Construct(b2);
Product p2 = b2.GetResult();
p2.Show();
```