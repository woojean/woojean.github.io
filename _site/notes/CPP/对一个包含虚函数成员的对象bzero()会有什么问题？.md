# 对一个包含虚函数成员的对象bzero()会有什么问题？

`对包含虚函数成员的对象bzero会破坏该对象的虚函数表（VTABLE）`，调用该虚函数时将core。
原型：extern void bzero（void *s, int n）;
用法：#include <string.h>
功能：置字节字符串s的前n个字节为零且包括‘\0’。
说明：bzero无返回值，并且使用strings.h头文件，strings.h曾经是posix标准的一部分，但是在POSIX.1-2001标准里面，这些函数被标记为了遗留函数而不推荐使用。在POSIX.1-2008标准里已经没有这些函数了。推荐使用memset替代bzero。