# free、delete、delete[]

delete汇编化是：
call destructor.
call delete(void*)->free+一些判断

free就是free

delete[]是
调相应数量的destructor，
delete(void*)
一般的内存泄露是来源于destructor少掉了时候。