# 状态模式（C#）

当一个对象的内在状态改变时允许改变其行为，这个对象看起来是改变了其类。
状态模式主要解决的是当控制一个对象状态转换的条件表达式过于复杂时的情况。把状态的判断逻辑转移到表示不同状态的一系列类当中，可以把复杂的判断逻辑简化。当然，如果这个状态判断很简单，就没有必要这么做了。
当一个对象的行为取决于它的状态，并且它必须在运行时刻根据状态改变它的行为时，就可以考虑使用状态模式了。
**State类，抽象状态类，定义一个接口以封装与Context的一个特定状态相关的行为。**

```java
abstract class State
{
    public abstract void Handle(Context context);
}
```
**ConcreteState类，每一个子类实现一个与Context的一个状态相关的行为。**
```java
class ConcreteStateA : State
{
    public override void Handle(Context context)
    {
        context.State = new ConcreteStateB();// 设置ConcreteStateA的下一个状态是ConcreteStateB
    }
}

class ConcreteStateB : State
{
    public override void Handle(Context context)
    {
        context.State = new ConcreteStateA();
    }
}
```
**Context类，维护一个ConcreteState子类的实例，这个实例定义当前的状态。**
```java	
class Context
{
    private State state;
    public Context(State state)
    {
        this.state = state;
    }
    public State State
    {
        get
        {
            return state;
        }
        set
        {
            state = value;
            Console.WriteLine("当前状态:" + state.GetType().Name);
        }
    }

    public void Request() //对请求做处理，并设置下一状态
    {
        state.Handle(this);
    }
}
```
**客户端代码。**
```java
Context c = new Context(new ConcreteStateA());
c.Request();
c.Request();
c.Request();
c.Request();
```