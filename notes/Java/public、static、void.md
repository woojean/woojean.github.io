# public、static、void

说明一下public static void main(String args[])这段声明里每个关键字的作用
public: main方法是Java程序运行时调用的第一个方法，因此它必须对Java环境可见。所以可见性设置为pulic.
static: Java平台调用这个方法时不会创建这个类的一个实例，因此这个方法必须声明为static。
void: main方法没有返回值。
String是命令行传进参数的类型，args是指命令行传进的字符串数组。