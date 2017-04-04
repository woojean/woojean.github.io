# jiffies

全局变量jiffies用来记录`自系统启动以来产生的节拍的总数`。系统启动时内核将该变量初始化为0，此后每次时钟中断处理程序会增加该变量的值，`一秒内增加的值即HZ`，系统运行时间以秒为单位计算就等于jiffies/HZ。Jiffies定义在文件linux/jiffies.h中：
extern unsigned long volatile jiffies;

常用的一些运算：
```
unsigned long time_stamp = jiffies;			/* 现在 */
unsigned long next_tick = jiffies+1;			/* 从现在开始的下一个节拍 */
unsigned long later = jiffies + 5*HZ;			/* 从现在开始后的5秒 */
unsigned long fraction = jiffies + HZ/10;			/* 从现在开始后的100ms */
```
因为jiffies使用unsigned long，所以在32位机器上，时钟频率为100HZ的情况下，497天后会溢出，如果频率为1000HZ，49.7天后就会溢出。溢出后其值会绕回到0。内核中提供了四个宏用来帮助比较节拍计数，它们能正确地处理节拍计数回绕的情况。