# 使用DataInputStream和DataOutputStream存储和恢复数据

如果使用DataOutputStream写入数据，Java保证可以使用DataInputStream准确地读取数据（即使读和写是在不同的平台）。

```java
DataOutputStream out = new DataOutputStream(
  new BufferedOutputStream(
    new FileOutputStream("data.txt")));
out.writeDouble(3.14);
out.writeUTF("abcde");
out.writeDouble();
out.write(1.41);
out.writeUTF("hijkl");
out.close();

DataInputStream in = new DataInputStream(
  new BufferedInputStream(
    new FileInputStream("data.txt")));
System.out.println(in.readDouble());
System.out.println(in.readUTF());
System.out.println(in.readDouble());
System.out.println(in.readUTF());
```
对于字符串，能够恢复它的唯一可靠做法是使用UTF-8编码（使用writeUTF()和readUTF()），UTF-8是多字节格式，即其编码长度根据实际使用的字符集会有所变化。ASCII使用一个字节，非ASCII字符使用两到三个字节。字符串的长度存储在UTF-8字符串的前两个字节中。
但是，writeUTF()和readUTF()使用的是Java自定义的UTF-8变体，因此`如果用非Java程序读取用writeUTF()写的字符串时，必须编写一些特殊代码才能正确读取字符串`。

