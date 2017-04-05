# 外观模式（C#）

为子系统中的一组接口提供一个一致的界面，此模式定义了一个高层接口，这个接口使得这一子系统更加容易使用。

**四个子系统的类**

```java
class SubSystemOne
{
    public void MethodOne()
    {
        Console.WriteLine(" 子系统方法一");
    }
}
class SubSystemTwo{…}
class SubSystemThree{…}
class SubSystemFour{…}
```
**外观类**

```java
class Facade
{
    SubSystemOne one;
    SubSystemTwo two;
    SubSystemThree three;
    SubSystemFour four;

    public Facade()
    {
        one = new SubSystemOne();
        two = new SubSystemTwo();
        three = new SubSystemThree();
        four = new SubSystemFour();
    }

    public void MethodA()
    {
        Console.WriteLine("\n方法组A() ---- ");
        one.MethodOne();
        two.MethodTwo();
        four.MethodFour();
    }

    public void MethodB()
    {
        Console.WriteLine("\n方法组B() ---- ");
        two.MethodTwo();
        three.MethodThree();
    }
}
```
**客户端调用**

```java
Facade facade = new Facade();
facade.MethodA();
facade.MethodB();
```


“三层架构”使用的就是该模式。