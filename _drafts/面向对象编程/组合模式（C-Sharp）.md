# 组合模式（C#）

将对象组合成树形结构以表示“部分-整体”的层次结构。组合模式使得用户对单个对象和组合对象的使用具有一致性。
当需求中是体现部分与整体层次的结构时，或者希望用户可以忽略组合对象与单个对象的不同，统一地使用组合结构中的所有对象时，就应该考虑用组合模式了。

**Component为组合中的对象声明接口，实现所有类共有接口的默认行为。**

```java
abstract class Component
{
    protected string name;

    public Component(string name)
    {
        this.name = name;
    }

    public abstract void Add(Component c);
    public abstract void Remove(Component c);
    public abstract void Display(int depth);
}
```
**Leaf在组合中表示叶节点对象，叶节点没有子节点。**
```java
class Leaf : Component
{
    public Leaf(string name): base(name)
    { }

    //虽然对叶子的添加删除操作没有意义，但是这样可以实现透明处理。
    public override void Add(Component c) 
    {
        Console.WriteLine("Cannot add to a leaf");
    }

    public override void Remove(Component c)
    {
        Console.WriteLine("Cannot remove from a leaf");
    }

    public override void Display(int depth)
    {
        Console.WriteLine(new String('-', depth) + name);
    }
}
```
**Composite定义有枝节点行为，用来存储子部件。**
```java
class Composite : Component
{
    private List<Component> children = new List<Component>();

    public Composite(string name)
        : base(name)
    { }

    public override void Add(Component c)
    {
        children.Add(c);
    }

    public override void Remove(Component c)
    {
        children.Remove(c);
    }

    public override void Display(int depth)
    {
        Console.WriteLine(new String('-', depth) + name);

        foreach (Component component in children)
        {
            component.Display(depth + 2);
        }
    }
}
```
**客户端代码**
```java
Composite root = new Composite("root");
root.Add(new Leaf("Leaf A"));
root.Add(new Leaf("Leaf B"));
Composite comp = new Composite("Composite X");
comp.Add(new Leaf("Leaf XA"));
comp.Add(new Leaf("Leaf XB"));
root.Add(comp);
Composite comp2 = new Composite("Composite XY");
comp2.Add(new Leaf("Leaf XYA"));
comp2.Add(new Leaf("Leaf XYB"));
comp.Add(comp2);
root.Add(new Leaf("Leaf C"));
Leaf leaf = new Leaf("Leaf D");
root.Add(leaf);
root.Remove(leaf);
root.Display(1);
```