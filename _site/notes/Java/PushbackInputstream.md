# PushbackInputstream

有时候将多个中间流串连到一起时，需要对它们进行跟踪，例如在读取输入时提前检查下一个字节，看看是否是希望的值，为此可利用PushbackInputStream来实现：

```java
PushbackInputstream pbin=new PushbackInputstream(new BufferedInputStream(new FileInputStream(“employee.dat”)));
int b=pbin.read();
if(b!’<’) pbin.unread(b);
```
如果想在“向前看”的同时也能读入数字，就同时需要一个pushback输入流以及一个数据输入引用流：
```java
PushbackInputstream pbin;
DataInputStream din=new DataInputStream(pbin= new PushbackInputstream(new BufferedInputStream(new FileInputStream(“employee.dat”))));
```