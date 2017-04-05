# 迭代器模式（C#）

提供一种方法顺序访问一个聚合对象中各个元素，而又不暴露该对象的内部表示。

**Iterator：迭代器抽象类，之所以要抽象，是因为可能有多种不同的遍历方式。**
```java
abstract class Iterator
{
    public abstract object First();
    public abstract object Next();
    public abstract bool IsDone();
    public abstract object CurrentItem();
}
```

**Aggregate：聚集抽象类**
```java
abstract class Aggregate
{
    public abstract Iterator CreateIterator();
}
```

**ConcreteIterator：具体迭代器类，继承Iterator**
```java
class ConcreteIterator : Iterator
{
    private ConcreteAggregate aggregate;
    private int current = 0;

    public ConcreteIterator(ConcreteAggregate aggregate)
    {
        this.aggregate = aggregate;
    }

    public override object First()
    {
        return aggregate[0];
    }

    public override object Next()
    {
        object ret = null;
        current++;

        if (current < aggregate.Count)
        {
            ret = aggregate[current];
        }

        return ret;
    }

    public override object CurrentItem()
    {
        return aggregate[current];
    }

    public override bool IsDone()
    {
        return current >= aggregate.Count ? true : false;
    }
}
```
**ConcreteAggregate：具体聚集类**
```java
class ConcreteAggregate : Aggregate
{
    private IList<object> items = new List<object>();
    public override Iterator CreateIterator()
    {
        return new ConcreteIterator(this);
    }

    public int Count
    {
        get { return items.Count; }
    }

    public object this[int index]
    {
        get { return items[index]; }
        set { items.Insert(index, value); }
    }
}
```
**客户端代码**
```java
ConcreteAggregate a = new ConcreteAggregate();
a[0] = "大鸟";
a[1] = "小菜";
a[2] = "行李";
a[3] = "老外";
a[4] = "公交内部员工";
a[5] = "小偷";
Iterator i = new ConcreteIterator(a);
object item = i.First();
while (!i.IsDone())
{
	Console.WriteLine("{0} 请买车票!", i.CurrentItem());
	i.Next();
}
```