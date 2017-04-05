# Linux进程状态

`进程状态`：task_struct中的state域描述了进程的当前状态，有5种情况：
TASK_RUNNING：进程是`可执行`的，它或者正在执行，或者在运行队列中等待执行，这是进程在用户空间中执行的唯一可能的状态，也可以应用到内核空间中正在执行的进程。
TASK_INTERRUPTIBLE：进程是`可中断`的，进程被阻塞等待某些条件的达成，一旦这些条件达成，内核会把进程状态设置为可执行。
TASK_UNINTERRUPTIBLE：进程是`不可中断`的，处于此状态的进程对信号不做响应。
__TASK_TRACED：被其他进程跟踪的进程，例如正在被调试程序跟踪。
__TASK_STOPPED：进程停止执行，进程没有投入运行也不能投入运行，通常发生在接收到SIGSTOP、SIGTSTP、SIGTTIN、SIGTTOU等信号的时候，此外在调试期间接收到任何信号都会使进程进入这种状态。


设置当前进程状态：
set_task_state(task,state);
或者：
set_current_state(state);