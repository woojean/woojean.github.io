# 如何查看Linux进程之间的关系？

ps -o pid,pgid,ppid,comm | cat

输出：
  PID  PGID  PPID COMMAND
 3003  3003  2986 su
 3004  3004  3003 bash
 3423  3423  3004 ps
 3424  3423  3004 cat

每个进程都会属于一个进程组(process group)，每个进程组中可以包含多个进程。进程组会有一个进程组领导进程 (process group leader)，领导进程的PID (PID见Linux进程基础)成为进程组的ID (process group ID, PGID)，以识别进程组。PID为进程自身的ID，PGID为进程所在的进程组的ID， PPID为进程的父进程ID。