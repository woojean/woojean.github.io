# 中介者模式（C#）

用一个中介对象来封装一系列的对象交互。中介者使各对象不需要显式地相互引用，从而使其耦合松散，而且可以独立地改变它们之间的交互。
**Mediator类，抽象中介者**

```java
abstract class Mediator
{
    public abstract void Send(string message, Colleague colleague);
}
```
**Colleague：抽象同事类**
```java
abstract class Colleague
{
    protected Mediator mediator;

    public Colleague(Mediator mediator)
    {
        this.mediator = mediator;
    }
}
```
**ConcreteMediator类**
```java
class ConcreteMediator : Mediator
{
    private ConcreteColleague1 colleague1;
    private ConcreteColleague2 colleague2;

    public ConcreteColleague1 Colleague1
    {
        set { colleague1 = value; }
    }

    public ConcreteColleague2 Colleague2
    {
        set { colleague2 = value; }
    }

    public override void Send(string message, Colleague colleague)
    {
        if (colleague == colleague1)
        {
            colleague2.Notify(message);
        }
        else
        {
            colleague1.Notify(message);
        }
    }
}
```
**具体同事类**
```java
class ConcreteColleague1 : Colleague
{
    public ConcreteColleague1(Mediator mediator)
        : base(mediator)
    { }

    public void Send(string message)
    {
        mediator.Send(message, this);
    }

    public void Notify(string message)
    {
        Console.WriteLine("同事1得到信息:" + message);
    }
}

class ConcreteColleague2 : Colleague
{
    public ConcreteColleague2(Mediator mediator)
        : base(mediator)
    { }
    
    public void Send(string message)
    {
        mediator.Send(message, this);
    }
    
    public void Notify(string message)
    {
        Console.WriteLine("同事2得到信息:" + message);
    }
}
```
**客户端调用**
```java
ConcreteMediator m = new ConcreteMediator();
ConcreteColleague1 c1 = new ConcreteColleague1(m);
ConcreteColleague2 c2 = new ConcreteColleague2(m);
m.Colleague1 = c1;
m.Colleague2 = c2;
c1.Send("吃过饭了吗?");
c2.Send("没有呢，你打算请客？");
```