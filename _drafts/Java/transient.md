# transient

有时不希望序列化对象的敏感部分（比如子对象），这可以通过将类实现为Externalizable（这样可以阻止自动序列化行为），然后在writeExternal()内部只对所需部分进行显式的序列化。
如果只使用Serializable，为了能够予以控制，可以使用transient关键字逐个字段地关闭序列化。

```java
public class Logon implements Serializable {
  private Date date = new Date();
  private String username;
  private transient String password;  // 不会被自动保存到磁盘，反序列化时也不会尝试去恢复
  public Logon(String name, String pwd) {
    username = name;
    password = pwd;
  }
  public String toString() {
    return "logon info: \n   username: " + username +
      "\n   date: " + date + "\n   password: " + password;
  }
  public static void main(String[] args) throws Exception {
    Logon a = new Logon("Hulk", "myLittlePony");
    print("logon a = " + a);
    ObjectOutputStream o = new ObjectOutputStream(
      new FileOutputStream("Logon.out"));
    o.writeObject(a);
    o.close();
    TimeUnit.SECONDS.sleep(1); // Delay
    // Now get them back:
    ObjectInputStream in = new ObjectInputStream(
      new FileInputStream("Logon.out"));
    print("Recovering object at " + new Date());
    a = (Logon)in.readObject();
    print("logon a = " + a);
  }
}
```
Externalizable对象在默认情况下不保存它们的任何字段，所以transient关键字只能和Serializable对象一起使用。

注意：对于Serializable对象，如果添加writeObject()和readObject()方法，在序列化和反序列化时就会使用它们而不是默认的序列化机制（即反序列化的时候会判断这两个方法是否存在）。
```
private void writeObject(ObjectOutputStream stream) throws IOException;
private void readObject(ObjectInputStream stream) throws IOException, ClassNotFoundException;
```
然而这两个方法并不是Serializable接口定义的一部分。