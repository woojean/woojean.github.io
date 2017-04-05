# Cloneable接口

Cloneable接口是一个标记接口，跟clone方法没有关系（该方法是在Object类中定义的）。如果对象要求克隆，但没有实现这个接口，那么会产生一个已检查异常。

## 建立深拷贝的clone方法

```java
Class Employee implements Cloneable{
  public Object clone(){
    try{
      //调用Object.clone()
      //clone方法总是返回Object，需要进行类型转换。
      Employee cloned = (Employee)super.clone();										 			cloned.hireDay=(Date)hireDay.clone();
      return cloned;
    }
    catch(ClonNotSupportedException e) { return null;}
  }
}
```