# 代理模式（C#）

为其他对象提供一种代理以控制对这个对象的访问。

**Subject类定义了RealSubject和Proxy的公用接口，这样在任何RealSubject的地方都可以使用Proxy。**

```java
abstract class Subject
{
    public abstract void Request();
}
```
**RealSubject类**

```java
class RealSubject : Subject
{
    public override void Request()
    {
        Console.WriteLine("真实的请求");
    }
}
```
**代理类和被代理类都继承于同样的基类（因此具有同样的对外接口），代理类中维护一个被代理类的引用，并将实际工作委托给被代理类完成（代理类可以添加额外的操作）。**

Proxy类：保存一个引用使得代理可以访问实体，并提供一个与Subject的接口相同的接口，这样代理就可以用来替代实体。

```java
class Proxy : Subject
{
    RealSubject realSubject;
    public override void Request()
    {
        if (realSubject == null)
        {
            realSubject = new RealSubject();
        }
        realSubject.Request();
    }
}
```
**客户端代码**

```java
Proxy proxy = new Proxy();
proxy.Request();
```



## 代理模式的应用场合

1）远程代理，为一个对象在不同的地址空间提供局部代表，这样可以隐藏一个对象存在于不同地址空间的事实。如WebService。

2）虚拟代理，根据需要创建开销很大的对象。通过它来存放实例化需要很长时间的真实对象。如浏览器中未打开的图片框就是通过虚拟代理来替代了真实的图片，此时代理存储了真实图片的路径和尺寸，图片仍在下载中。

3）安全代理，用来控制真实对象访问时的权限。

4）智能指引，是指当调用真实的对象时，代理处理另外一些事。如计算真实对象的引用次数，或在第一次引用一个持久对象时将它装入内存。