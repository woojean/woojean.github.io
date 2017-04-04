# 使用PrinterWriter输出文件的快捷方式

```java
BufferedReader in = new BufferedReader(
  new StringReader(readFuc(filename1)));
PrintWriter out = new PrintWriter(filename2);  // 接受文件名的构造函数
int lineCount = 1;
String s;
while( (s = in.readLine()) != null ){
  out.println(lineCount++ + ":" +s );
}
out.close();
```