# 命令模式（C#）

将一个请求封装为一个对象，从而使得可用不同的请求对客户进行参数化，对请求排队或记录请求日志，以及支持可撤销的操作。
**Command类，用来声明执行操作的接口**

```java
abstract class Command
{
    protected Receiver receiver;

    public Command(Receiver receiver)
    {
        this.receiver = receiver;
    }

    abstract public void Execute();
}
```
**ConcreteCommand类，将一个接受者对象绑定于一个动作，调用接收者相应的操作，以实现Excute**
```java
class ConcreteCommand : Command
{
    public ConcreteCommand(Receiver receiver)
        :
      base(receiver) { }

    public override void Execute()
    {
        receiver.Action();
    }
}
```
**Invoker类，要求该命令执行这个请求**
```java
class Invoker
{
    private Command command;

    public void SetCommand(Command command)
    {
        this.command = command;
    }

    public void ExecuteCommand()
    {
        command.Execute();
    }
}
```
**Receiver类，知道如何实施与执行一个与请求相关的操作，任何类都有可能作为一个接收者**
```java
class Receiver
{
    public void Action()
    {
        Console.WriteLine("执行请求！");
    }
}
```
**客户端代码，创建一个具体命令对象并设定它的接收者**
```java
Receiver r = new Receiver();
Command c = new ConcreteCommand(r);
Invoker i = new Invoker();
i.SetCommand(c);
i.ExecuteCommand();
```

## 命令模式的优点
1）能够较容易地设计一个命令队列；
2）在需要的情况下可以较容易地将命令写入日志；
3）允许接收请求的一方决定是否要否决请求；
4）可以容易地实现对请求的撤销和重做；
5）由于加入新的具体命令不影响其他的类，因此增加新的具体命令很容易；
6）最关键的一点：命令模式把请求一个操作的对象与知道怎么执行一个操作的对象分割开。