# 备忘录模式（C#）

在不破坏封装性的前提下，捕获一个对象的内部状态，并在该对象之外保存这个状态，这样以后就可以将该对象恢复到原先保存的状态。
**Originator**

```java
class Originator
{
    private string state;
    public string State
    {
        get { return state; }
        set { state = value; }
    }

    public Memento CreateMemento()
    {
        return (new Memento(state));
    }

    public void SetMemento(Memento memento)
    {
        state = memento.State;
    }

    public void Show()
    {
        Console.WriteLine("State=" + state);
    }
}
```
**Memento，负责存储Originator对象的内部状态，并可防止Originator以外的其他对象访问备忘录Memento。备忘录有两个接口，Caretaker只能看到备忘录的窄接口，它只能将备忘录传递给其他对象。Originator能够看到一个宽接口，允许它访问返回到先前状态所需的所有数据。**
```java
class Memento
{
    private string state;

    public Memento(string state)
    {
        this.state = state;
    }

    public string State
    {
        get { return state; }
    }
}
```
**Caretaker，负责保存好备忘录Memento，不能对备忘录的内容进行检查或操作。**
```java
class Caretaker
{
    private Memento memento;

    public Memento Memento
    {
        get { return memento; }
        set { memento = value; }
    }
}
```
**客户端代码**
```java
Originator o = new Originator();
o.State = "On";
o.Show();
Caretaker c = new Caretaker();
c.Memento = o.CreateMemento();
o.State = "Off";
o.Show();
o.SetMemento(c.Memento);
o.Show();
```