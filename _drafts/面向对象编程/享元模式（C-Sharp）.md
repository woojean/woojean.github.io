# 享元模式（C#）

运用共享技术有效地支持大量细粒度的对象。
如果发现某个对象的生成了大量细粒度的实例，并且这些实例除了几个参数外基本是相同的，如果把那些共享参数移到类外面，在方法调用时将他们传递进来，就可以通过共享大幅度减少单个实例的数目。
**Flyweight类**

```java
abstract class Flyweight
{
    public abstract void Operation(int extrinsicstate);
}
```
**ConcreteFlyweight类**
```java
class ConcreteFlyweight : Flyweight
{
    public override void Operation(int extrinsicstate)
    {
        Console.WriteLine("具体Flyweight:" + extrinsicstate);
    }
}
```
**UnsharedConcreteFlyweight类**
```java
class UnsharedConcreteFlyweight : Flyweight
{
    public override void Operation(int extrinsicstate)
    {
        Console.WriteLine("不共享的具体Flyweight:" + extrinsicstate);
    }
}
```
**FlyweightFactory**
```java
class FlyweightFactory
{
    private Hashtable flyweights = new Hashtable();
    //不一定非要事先生成对象，也可以需要时根据判断是否为NULL再进行实例化
	public FlyweightFactory()
    {
        flyweights.Add("X", new ConcreteFlyweight());
        flyweights.Add("Y", new ConcreteFlyweight());
        flyweights.Add("Z", new ConcreteFlyweight());

    }
    public Flyweight GetFlyweight(string key)
    {
        return ((Flyweight)flyweights[key]);
    }
}
```
**客户端代码**
```java
int extrinsicstate = 22; //代码外部状态
FlyweightFactory f = new FlyweightFactory();
Flyweight fx = f.GetFlyweight("X");
fx.Operation(--extrinsicstate);
Flyweight fy = f.GetFlyweight("Y");
fy.Operation(--extrinsicstate);
Flyweight fz = f.GetFlyweight("Z");
fz.Operation(--extrinsicstate);
UnsharedConcreteFlyweight uf = new UnsharedConcreteFlyweight();
uf.Operation(--extrinsicstate);
```

## 内部状态与外部状态
在享元对象内部，并且不会随环境改变而改变的共享部分，称为享元对象的内部状态，而随环境改变而改变的、不可以共享的状态就是外部状态。也就是说，享元模式Flyweight执行时所需的状态是有内部的也有可能有外部的。内部状态存储于ConcreteFlyweight对象之中，而外部对象则应该考虑由客户端对象存储或计算，当调用Flyweight对象的操作时，将该状态传递给它。