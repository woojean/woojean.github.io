# 简单工厂模式（C#）

简单工厂模式实现计算器。

**Operation运算类**

```java
public class Operation
{
    private double _numberA = 0;
    private double _numberB = 0;
    public double NumberA{…}
    public double NumberB{…}
    public virtual double GetResult()
    {
        double result = 0;
        return result;
    }
}
```
**加减乘除类**

```java
class OperationAdd:Operation
{
    public override double GetResult()
    {
        double result = 0;
        result = NumberA + NumberB;
        return result;
    }
}
class OperationSub:Operation{…}
class OperationMul:Operation{…}
class OperationDiv:Operation{…}
```
**简单运算工厂类**

```java
public class OperationFactory
{
    // 工厂类根据传入的类型创建特定类的不同子类
    public static Operation createOperate(string operate)
    {
        Operation oper = null;
        switch(operate)
        {
            case”+”:
                oper = new OperationAdd();
                break;
            case”-”:
                … …
        }
        return oper;
    }
}
```
**客户端代码**

```java
Operation oper；
oper = OperationFactory.createOperate(“+”);
oper.NumberA = 1;
oper.NumberB = 2;
double result = oper.GetResult();
```