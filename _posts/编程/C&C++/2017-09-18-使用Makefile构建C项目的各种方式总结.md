---
layout: post
title:  "使用Makefile构建C项目的各种方式总结"
date: 2017-09-18 00:00:00
categories: 编程
tags: C&C++
excerpt: ""
---

* content
{:toc}


[本文实例主要参考：一个工程实例来学习Makefile](http://www.cnblogs.com/OpenShiFt/p/4313351.html)
[下载代码:https://github.com/woojean/demos/tree/master/makefile-demo](https://github.com/woojean/demos/tree/master/makefile-demo)



# 待构建项目结构

```
├── add
│   ├── add_float.c
│   ├── add.h
│   └── add_int.c
├── main.c
└── sub
    ├── sub_float.c
    ├── sub.h
    └── sub_int.c
```



# 不使用Makefile进行构建

构建：
```
# -ggdb 使GCC为GDB生成专用的更为丰富的调试信息，但是，此时就不能用其他的调试器来进行调试了(如ddx)
gcc -c add/add_int.c -o add/add_int.o -ggdb
gcc -c add/add_float.c -o add/add_float.o -ggdb
gcc -c sub/sub_float.c -o sub/sub_float.o -ggdb
gcc -c sub/sub_int.c -o sub/sub_int.o -ggdb

# -I表示将指定目录作为第一个寻找头文件的目录（即优先级高于/usr/include和/usr/local/include）
gcc -c main.c -o main.o -Iadd -Isub -ggdb
gcc -o cacu add/add_int.o add/add_float.o sub/sub_int.o sub/sub_float.o main.o -ggdb
```

清理：
```
rm -f cacu
rm -rf add/*.o sub/*.o *.o
```



# 最基本的Makefile使用

Makefile的基本格式：
```
TARGET... : DEPENDEDS...
	COMMAND
	...
	...
```

据此，编写如下Makefile文件：
<u>Makefile_1</u>
```
# 最终的编译目标是生成一个名为cacu的文件
cacu:add_int.o add_float.o sub_int.o sub_float.o main.o
	gcc -o cacu add/add_int.o add/add_float.o \
		sub/sub_int.o sub/sub_float.o main.o -ggdb

add_int.o:add/add_int.c add/add.h
	gcc -c -o add/add_int.o add/add_int.c -ggdb

add_float.o:add/add_float.c add/add.h
	gcc -c -o add/add_float.o add/add_float.c -ggdb

sub_int.o:sub/sub_int.c sub/sub.h
	gcc -c -o sub/sub_int.o sub/sub_int.c -ggdb

sub_float.o:sub/sub_float.c sub/sub.h
	gcc -c -o sub/sub_float.o sub/sub_float.c -ggdb

main.o:main.c add/add.h sub/sub.h
	gcc -c -o main.o main.c -Iadd -Isub -ggdb

# 清理
clean:
	rm -f cacu add/add_int.o add/add_float.o \
		sub/sub_int.o sub/sub_float.o main.o
```

执行：
```
# 构建
make -f Makefile_1

# 清理
make clean -f Makefile_1
```



# 使用自定义变量

<u>Makefile_2</u>
```
CC = gcc
CFLAGS = -Iadd -Isub -O2
OBJS = add/add_int.o add/add_float.o sub/sub_float.o sub/sub_int.o main.o
TARGET = cacu
RM = rm -f

$(TARGET):$(OBJS)
	$(CC) -o $(TARGET) $(OBJS) $(CFLAGS)

# %o:%c是将以.o结尾的文件替换成以.c结尾的文件
$(OBJS):%o:%c
	$(CC) -c $(CFLAGS) $< -o $@

clean:
	$(RM) $(TARGET) $(OBJS)
```



# 使用预定义变量

<u>Makefile_3</u>
```
CFLAGS = -Iadd -Isub -O2
OBJS = add/add_int.o add/add_float.o \
	   sub/sub_int.o sub/sub_float.o main.o
TARGET = cacu

$(TARGET):$(OBJS)
	$(CC) $^ -o $@ $(CFLAGS)

## $(CC)即cc
$(OBJS):%o:%c
	$(CC) -c $< -o $@ $(CFLAGS)

## $(RM)即rm -f
clean:
	-$(RM) $(TARGET) $(OBJS)
```



# 使用自动变量

常见的自动变量及其含义：
`*` 表示目标文件的名称，不包含目标文件的扩展名
`+` 表示所有的依赖文件，这些依赖文件之间以空格分开，按照出现的先后为顺序，其中可能包含重复的依赖文件
`<` 表示依赖项中第一个依赖文件的名称
`?` 依赖项中，所有比目标文件时间戳晚的依赖文件，依赖文件之间以空格分开
`@` 目标项中目标文件的名称
`^` 依赖项中，所有不重复的依赖文件，这些文件之间以空格分开

<u>Makefile_4</u>
```
CFLAGS = -Iadd -Isub -O2
OBJS = add/add_int.o add/add_float.o \
	   sub/sub_int.o sub/sub_float.o main.o
TARGET = cacu

$(TARGET):$(OBJS)
	$(CC) $^ -o $@ $(CFLAGS)

$(OBJS):%o:%c
	$(CC) -c $< -o $@ $(CFLAGS)

clean:
	-$(RM) $(TARGET) $(OBJS)
```



# 设置搜索路径

指定需要搜索的目录， make会自动找到指定文件的目录并添加到文件上，VPATH变量可以实现此目的。
<u>Makefile_5</u>
```
CFLAGS = -Iadd -Isub -O2
OBJDIR = objs
VPATH = add:sub:.
OBJS = add_int.o add_float.o sub_int.o sub_float.o main.o
TARGET = cacu

$(TARGET):$(OBJSDIR) $(OBJS)
	$(CC) -o $(TARGET) $(OBJDIR)/*.o $(CFLAGS)

$(OBJDIR):
	mkdir -p ./$@

$(OBJS):%.o:%.c 
	$(CC) -c $(CFLAGS) $< -o $(OBJDIR)/$@

clean:
	-$(RM) $(TARGET)
	-$(RM) $(OBJDIR)/*.o -r
```



# 使用自动推导规则

在书写 Makefile 时，就可以省略掉描述 .c 文件和 .o 依赖关系的规则，而只需要给出那些特定的规则描述（.o 目标所需要的 .h 文件）。
对一个目标文件是“文件名.o“，依赖文件是”文件名.c“的规则，可以省略其编译规则的命令行，由make命令决定如何使用编译命令和选项。此默认规则称为make的隐含规则。

<u>Makefile_6</u>
```
CC = gcc
CFLAGS = -O2 -Iadd -Isub
TARGET = cacu
DIRS = sub add .
FILES = $(foreach dir, $(DIRS), $(wildcard $(dir)/*.c))
OBJS = $(patsubst %.c, %.o, $(FILES))
$(TARGET):$(OBJS)
	$(CC) -o $(TARGET) $(OBJS)

clean:
	-$(RM) $(TARGET)
	-$(RM) $(OBJS)
```



# 使用递归

<u>Makefile_7</u>
```
CC = gcc
CFLAGS = -O2
TARGET = cacu
export OBJSDIR = $(shell pwd)/objs

# make -C 切换到指定目录再执行make
$(TARGET):$(OBJSDIR) main.o
	$(MAKE) -C add
	$(MAKE) -C sub
	$(CC) -o $(TARGET) $(OBJSDIR)/*.o

$(OBJSDIR):
	mkdir -p $@

main.o:%.o:%.c
	$(CC) -c $< -o $(OBJSDIR)/$@ $(CFLAGS) -Iadd -Isub

clean:
	-$(RM) $(TARGET)
	-$(RM) $(OBJSDIR)/*.o
```

add/Makefile
```
OBJS = add_int.o add_float.o
all:$(OBJS)

$(OBJS):%.o:%.c
	$(CC) -c $< -o $(OBJSDIR)/$@ $(CFLAGS)

clean:
	$(RM) $(OBJS)
```

sub/Makefile
```
OBJS = sub_int.o sub_float.o
all:$(OBJS)

$(OBJS):%.o:%.c
	$(CC) -c $< -o $(OBJSDIR)/$@ $(CFLAGS)

clean:
	$(RM) $(OBJS)
```



# 使用函数

* wildcard 获取匹配模式的文件名
```
$(wildcard PATTERN)
```
* patsubst 模式替换函数
```
$(patsubst pattern, replacement, text)
```
* foreach 循环函数
```
$(foreach VAR, LIST, TEXT)
```

<u>Makefile_8</u>
```
CC = gcc
CFLAGS = -O2 -Iadd -Isub
TARGET = cacu
DIRS = sub add .
FILES = $(foreach dir, $(DIRS), $(wildcard $(dir)/*.c))
OBJS = $(patsubst %.c, %.o, $(FILES))
$(TARGET):$(OBJS)
	$(CC) -o $(TARGET) $(OBJS)

clean:
	-$(RM) $(TARGET)
	-$(RM) $(OBJS)
```







