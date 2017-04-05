# 策略模式（C#）

策略模式（Strategy）定义了算法家族，让它们之间可以互相替换，此模式让算法的变化不会影响到使用算法的客户。

**Strategy类定义了所有支持的算法的公共接口**

```java
abstract class Strategy
{
    //算法方法
    public abstract void AlgorithmInterface();
}
```
**ConcreteStrategy类封装了具体的算法或行为，继承于Strategy**

```java
//具体算法A
class ConcreteStrategyA : Strategy
{
    //算法A实现方法
    public override void AlgorithmInterface()
    {
        Console.WriteLine("算法A实现");
    }
}
//具体算法B
class ConcreteStrategyB : Strategy{…}
//具体算法C
class ConcreteStrategyC : Strategy{…}
```
**上下文类维护一个策略类的引用，可以使用不同的策略类进行初始化，上下文类对用户提供一致的接口，并将实际工作委托给策略类完成**

```java
//上下文
class Context
{
    Strategy strategy;
    public Context(Strategy strategy)
    {
        this.strategy = strategy;
    }
  
    //上下文接口
    public void ContextInterface()
    {
        strategy.AlgorithmInterface();
    }
}
```
**客户端代码**

```java
Context context;
context = new Context(new ConcreteStrategyA());
context.ContextInterface();
context = new Context(new ConcreteStrategyB());
context.ContextInterface();
… … 
```