# java的反射(reflection)机制

```java
interface	A
{
	int x = 0;
}
class B
{
	int x =1;
}
class C extends B implements A
{
	public void pX()
{
		System.out.println(x);
	}
public static void main(String[] args) {
  	new C().pX();
	}
}
```
在编译时会发生错误(错误描述不同的JVM有不同的信息，意思就是未明确的x调用，两个x都匹配（就象在同时import java.util和java.sql两个包时直接声明Date一样）。对于父类的变量,可以用super.x来明确(输出的是1)，而接口的属性默认隐含为 public static final.所以可以通过A.x来明确(输出的是0)。