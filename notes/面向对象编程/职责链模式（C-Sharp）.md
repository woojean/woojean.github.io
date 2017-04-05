# 职责链模式（C#）

使多个对象都有机会处理请求，将这些对象连成一条链，并沿着这条链传递该请求，直到有一个对象处理它为止。这就使得接收者和发送者都没有对方的明确信息，且链中的对象自己也并不知道链的结构。

**Handler类，定义一个处理请求的接口**
```java
abstract class Handler
{
    protected Handler successor; 	// 维护一个同类型的引用，用于链式传递请求

    public void SetSuccessor(Handler successor)
    {
        this.successor = successor;
    }

    public abstract void HandleRequest(int request);
}
```
**ConcreteHandler类，具体处理者类，处理它所负责的请求.可访问它的后继者。**
```java
class ConcreteHandler1 : Handler
{
    public override void HandleRequest(int request)
    {
        if (request >= 0 && request < 10)
        {
            Console.WriteLine("{0}  处理请求  {1}",this.GetType().Name, request);
        }
        else if (successor != null)
        {
            successor.HandleRequest(request);
        }
    }
}

class ConcreteHandler2 : Handler
{
    public override void HandleRequest(int request)
    {
        if (request >= 10 && request < 20)
        {
            Console.WriteLine("{0}  处理请求  {1}",this.GetType().Name, request);
        }
        else if (successor != null)
        {
            successor.HandleRequest(request);
        }
    }
}

class ConcreteHandler3 : Handler
{
    public override void HandleRequest(int request)
    {
        if (request >= 20 && request < 30)
        {
            Console.WriteLine("{0}  处理请求  {1}",this.GetType().Name, request);
        }
        else if (successor != null)
        {
            successor.HandleRequest(request);
        }
    }
}
```
**客户端代码**
```java
Handler h1 = new ConcreteHandler1();
Handler h2 = new ConcreteHandler2();
Handler h3 = new ConcreteHandler3();
h1.SetSuccessor(h2);
h2.SetSuccessor(h3);
int[] requests = { 2, 5, 14, 22, 18, 3, 27, 20 };
foreach (int request in requests)
{
	h1.HandleRequest(request);
}
```