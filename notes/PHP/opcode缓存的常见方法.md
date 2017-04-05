# opcode缓存的常见方法

## opcode
脚本语言的解释器会事先经过词法分析、语法分析、语义分析等一系列步骤将源代码编译为操作码（Operate Code），然后再执行。PHP解释器的核心引擎为Zend Engine。

PHP的parsekit扩展提供运行时API来查看PHP代码的opcode。
```
var_dump( parsekit_compile_string('print 1+1;') );
```
opcode的格式类似于汇编代码（都是三地址码的格式），因此可以方便地翻译为不同平台的本地代码。

## APC
开启：
```
apc.cache_by_default = on
```
也可以配置apc.filters让APC只对特定范围的动态程序进行opcode缓存。
APC同时提供跳过过期检查的机制，如果动态程序长期不会变化，那么可以跳过过期检查以获得更好的性能：
```
apc.stat = off
```

## XCache
和APC差不多，详略。

## 解释器扩展模块
（略）