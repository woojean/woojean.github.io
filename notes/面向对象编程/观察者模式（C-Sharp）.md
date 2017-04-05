# 观察者模式（C#）

定义了一种一对多的依赖关系，让多个观察者对象同时监听某一个主题对象。这个主题对象在状态发生变化时，会通知所有观察者对象，使它们能够自动更新自己。
当一个对象的改变需要同时改变其他的对象，而且它不知道具体有多少对象有待改变时，应该考虑使用观察者模式。观察者模式所做的工作其实就是在解除耦合。让耦合的双方都依赖于抽象，而不是依赖于具体，从而使得各自的变化都不会影响另一边的变化。

观察者模式结构图：
![image](https://github.com/woojean/woojean.github.io/blob/master/images/dm_2.png)



**Subject类，即主题或抽象通知者。一般用一个抽象类或者一个接口实现。**

```java
abstract class Subject
{
    private IList<Observer> observers = new List<Observer>();

    //增加观察者
    public void Attach(Observer observer)
    {
        observers.Add(observer);
    }
    //移除观察者
    public void Detach(Observer observer)
    {
        observers.Remove(observer);
    }
    //通知
    // Subject类有一个Observers的列表，并有一个通知方法用来遍历调用所有Observers的方法
    public void Notify()
    {
        foreach (Observer o in observers)
        {
            o.Update();
        }
    }
}
```
**Observer类，抽象观察者，为所有的具体观察者定义一个接口，在得到主题的通知时更新自己。这个接口叫做更新接口。抽象观察者一般用一个抽象类或者一个接口实现，更新接口通常包含一个更新方法。**

```java
abstract class Observer
{
    public abstract void Update();
}
```
**ConcreteSubject类，即具体主题。**

```java
class ConcreteSubject : Subject
{
    private string subjectState;

    //具体通知者状态
    public string SubjectState
    {
        get { return subjectState; }
        set { subjectState = value; }
    }
}
```
**ConcreteObserver类，具体观察者。可以保存一个指向具体主题对象的引用。**

```java
class ConcreteObserver : Observer
{
    private string name;
    private string observerState;
    private ConcreteSubject subject;

    public ConcreteObserver(ConcreteSubject subject, string name)
    {
        this.subject = subject;
        this.name = name;
    }
    //更新
    public override void Update()
    {
        observerState = subject.SubjectState;
        Console.WriteLine("观察者{0}的新状态是{1}",name, observerState);
    }

    public ConcreteSubject Subject
    {
        get { return subject; }
        set { subject = value; }
    }
}
**客户端代码**
​```java
ConcreteSubject s = new ConcreteSubject();
s.Attach(new ConcreteObserver(s, "X"));
s.Attach(new ConcreteObserver(s, "Y"));
s.Attach(new ConcreteObserver(s, "Z"));
s.SubjectState = "ABC";
s.Notify();
```

## 不足

尽管已经用了依赖倒换原则，但是“抽象通知者”还是依赖“抽象观察者”，也就是说，万一没有了抽象观察者这样的接口，通知的功能就完成不了。此外，每个具体的观察者不一定就是调用相同的“更新”方法。
改进（事件委托实现）：委托就是一种引用方法的类型。一旦为委托分配了方法，委托将与该方法具有完全相同的行为。委托方法的使用可以像其他任何方法一样，具有参数和返回值。委托可以看作是对函数的抽象，是函数的“类”，委托的实例将代表一个具体的函数。一个委托可以搭载多个方法，所有方法被依次唤起，并且可以使得委托对象所搭载的方法并不需要属于同一个类。不过委托对象所搭载的方法必须具有相同的原型和形式。