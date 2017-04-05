# FilterInputStream和FilterOutputStream

FilterInputStream和FilterOutputStream是用来`提供装饰器类接口`以控制特定输入流和输出流的两个类。

FilterInputStream类型：
1.DataInputStream：与DataOutputStream搭配使用，可以从流读取基本数据类型；
2.BufferedInputStream：使用缓冲区；
3.LineNumberInputStream：跟踪输入流中的行号，可调用getLineNumber()和setLineNumber(int)；
4.PushbackInputStream：具有能弹出一个字节的缓冲区，因此可以将读到的最后一个字符回退；

FilterOutputStream类型：
1.DataOutputStream：可以按照可移植方式向流中写入基本类型数据；
2.PrintStream：用于产生格式化输出；
3.BufferedOutputStream：使用缓冲区；