---
layout: post
title:  "new、delete与malloc、free之间的联系和区别"
date: 2017-04-06 00:00:04
categories: 编程语言
tags: C C++ 内存分配
excerpt: C&C++内存分配基础知识
---
## malloc/free和new/delete的联系
1. 存储方式相同。malloc和new动态申请的内存**都位于堆中**。申请的内存**都不能自动被操作系统收回**，都需要配套的free和delete来释放。
2. 除了带有构造函数和析构函数的类等数据类型以外，**对于一般数据类型，如int、char等等，两组动态申请的方式可以通用**，作用效果一样，只是形式不一样。
3. 内存泄漏对于malloc或者new都是可以检查出来的，区别在于new可以指明是哪个文件的哪一行，而malloc没有这些信息。
4. 两组都需要配对使用，malloc配free，new配delete。在C++中，两组之间不能混着用（虽说有时能编译过，但容易存在较大的隐患）。

## malloc/free和new/delete的区别
1. malloc返回void类型指针，free的形参为void指针，new和delete直接带具体类型的指针。
2. malloc和free属于C语言中的**函数**，需要库的支持，而new/delete是C++中的**运算符**，况且可以重载，所以**new/delete的执行效率高些**。
3. 在C++中，**new是类型安全的**，而malloc不是。例如：

```c
// 编译时指出错误
int* p = new char[10];  

//对数组需要加中括号“[]”
delete [] p;  

// 编译时无法指出错误
int* p = malloc(sizeof(char)*10); 

//只需要所释放内存的头指针（free释放malloc分配的数组）。在malloc和free的面前没有对象没有数组，只有“内存块”。一次malloc分配的东西，一次free一定能回收。至于内存块的大小内存管理会进行记录，这应该是库函数的事。free的真正弊端在于它不会调用析构函数。
free (p);  
```
4. 使用new动态申请类对象的内存空间时，类对象的构建要调用构造函数，相当于对内存空间进行了初始化。而**malloc动态申请的类对象的内存空间时，不会初始化**，也就是说申请的内存空间无法使用，因为类的初始化是由构造函数完成的。
5. 不能用malloc和free来完成类对象的动态创建和删除。

## 此外：
```c
void *calloc(int n,int size);
```
函数返回值为void型指针。如果执行成功，函数从堆上获得size * n的字节空间，并返回该空间的首地址。如果执行失败，函数返回NULL。**该函数与malloc函数的一个显著不同时是，calloc函数得到的内存空间是经过初始化的，其内容全为0**。calloc函数适合为数组申请空间，可以将size设置为数组元素的空间长度，将n设置为数组的容量。

realloc函数的功能比malloc函数和calloc函数的功能更为丰富，可以实现内存分配和内存释放的功能，其函数声明如下：
```c
void * realloc(void * p,int n);
```
其中，指针p必须为指向堆内存空间的指针，即由malloc函数、calloc函数或realloc函数分配空间的指针。realloc函数将指针p指向的内存块的大小改变为n字节。如果n小于或等于p之前指向的空间大小，那么。保持原有状态不变。如果n大于原来p之前指向的空间大小，那么，系统将重新为p从堆上分配一块大小为n的内存空间，同时，将原来指向空间的内容依次复制到新的内存空间上，p之前指向的空间被释放。**relloc函数分配的空间也是未初始化的**。

注意：使用malloc函数，calloc函数和realloc函数分配的内存空间都要**使用free函数或指针参数为NULL的realloc函数来释放**。