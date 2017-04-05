# 单例模式（C#）

保证一个类仅有一个实例，并提供一个访问它的全局访问点。即，让类自身负责保存它的唯一实例。这个类可以保证没有其他实例可以被创建，并且它可以提供一个访问该实例的方法。简单来说就是对唯一实例的受控访问。

**Singleton类**
```java
class Singleton
{
    private static Singleton instance;
    private static readonly object syncRoot = new object();
    private Singleton()
    {
    }

    public static Singleton GetInstance()
    {
        if (instance == null)
        {

            lock (syncRoot)
            {
    
                if (instance == null)
                {
                    instance = new Singleton();
                }
            }
        }
        return instance;
    }
}
```
**客户端代码**
```java
Singleton s1 = Singleton.GetInstance();
Singleton s2 = Singleton.GetInstance();
if (s1 == s2)
{
	Console.WriteLine("Objects are the same instance");
}
```