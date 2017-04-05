# fork()、vfork()

`fork()`
Linux通过clone()系统调用实现fork()，clone()系统调用通过一系列的参数标志来指明父、子进程需要共享的资源，然后调用do_fork()，do_fork()完成了进程创建中的大部分工作，并调用copy_process()函数，然后让进程开始运行。copy_process()函数完成的工作如下：
调用dup_task_struct()为新进程创建一个内核栈、thread_info结构和task_struct，这些值与当前进程的值相同，此时子进程和父进程的描述符是完全相同的；
检查并确保新创建这个子进程后，当前用户所拥有的进程数目没有超出给它分配的资源的限制；
子进程使自己与父进程区别开来，进程描述符内的许多成员都要被清0或被设为初始值；
子进程的状态被设置为TASK_UNINTERRUPTIBLE，以保证它不会投入运行；
copy_process()调用copy_flags()以更新task_struct的flags成员，表明进程是否拥有超级用户权限的PF_SUPERPRIV标志被清0，表明进程还没有调用exec()函数的PF_FORKNOEXEC标志被设置；
调用alloc_pid()为新进程分配一个有效的PID；
根据传递给clone()的参数标志，copy_process()拷贝或共享打开的文件、文件系统信息、信号处理函数、进程地址空间和命名空间等；
最后，copy_process()做扫尾工作，并返回一个指向子进程的指针；
再回到do_fork()，如果copy_process()成功返回，新创建的子进程被唤醒并让其投入运行。

`vfork()`
除了不拷贝父进程的页表项外，vfork()系统调用和fork()的功能相同，如果Linux将来fork()有了写时拷贝页表项，那么vfork()就彻底没用了。理想情况下最好不要调用vfork()，内核也不用实现它。
fork()和vfork()最终实际都是通过调用clone()来创建新进程。