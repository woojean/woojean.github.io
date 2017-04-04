# 使用Xdebug实现代码覆盖率、函数跟踪、瓶颈分析等

## Xdebug
xdebug_time_index();  // 返回从脚本开始处执行到当前位置所花费的时间
xdebug_call_line();   // 当前函数在哪一行被调用
xdebug_call_function();  // 当前函数在哪个函数中被调用

## 代码覆盖率
xdebug_start_code_coverage();
...
var_dump(xdebug_get_code_coverage());

## 函数跟踪
根据程序在实际运行时的执行顺序跟踪记录所有函数的执行时间，以及函数调用时的上下文，包括实际参数和返回值。

在php.ini中配置记录文件的存储目录和文件名前缀：
```
xdebug.trace_output_dir = /tmp/xdebug
xdebug.trace_output_name trace.%c
```
%c代表函数调用。

输出示例：
```
0.0167  1009988  -> MarkerInfo->getMarkerInfo()
0.0167  1009988    -> DataAccess->selectDb(string(11))
0.0168  1010040      -> DataAccess->connect()
...
0.0170  1010288      -> mysql_connect(string(9), string(4), string(0))
0.0207  1011320      -> ...
```
通过以上片段可以分析出代码执行的时间主要消耗在mysql_connect()处。

## 瓶颈分析
Xdebug提供性能跟踪器：
```
xdebug.profiler_output_dir = /tmp/xdebug
xdebug.profiler_output_name = cachegrind.out.%p
```
其中%p是运行时PHP解释器所在进程的PID。
可以使用图形界面工具CacheGrind分析日志。