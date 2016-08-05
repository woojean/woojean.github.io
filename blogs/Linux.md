## 进程为什么要挂起？
在多进程程序系统中，进程在处理器上交替运行，在运行、就绪和阻塞3种基本状态之间不断地发生变化。由于进程的不断创建，系统资源（特别是主存资源）已不能满足进程运行的要求。此时就必须将某些进程挂起，对换到磁盘镜像区，暂时不参与进程调度，以平衡系统负载的目的。如果系统出现故障，或者是用户调试程序，也可能需要将进程挂起检查问题。        
所谓挂起状态，实际上就是一种静止的状态。一个进程被挂起后，不管它是否在就绪状态，系统都不分配给它处理机。（区别于阻塞状态）。这样进程的三态模型（执行、就绪、阻塞）就变为五态模型：执行状态、活动就绪状态、静止就绪状态、活动阻塞状态和静止阻塞状态 
活动就绪：指进程在主存并且可被调度的状态 （对应于三态的就绪状态）
静止就绪：指进程被对换到辅存时的就绪状态，是不能被直接调度的状态，只有当主存中没有活动就绪态进程，或者是挂起态进程具有更高的优先级，系统将把挂起就绪态进程调回主存并转换为活动就绪。 
活动阻塞：指进程在主存中。一旦等待的事件产生，便进入活动就绪状态（对应于三态的阻塞状态） 
静止阻塞：指进程对换到辅存时的阻塞状态。一旦等待的事件产生，便进入静止就绪状态。

## 处理机调度算法
1.先来先服务调度算法
2.短作业(进程)优先调度算法
3.高优先权优先调度算法（静态优先级、动态优先级）
4.基于时间片的轮转调度算法
5.多级队列： 每个作业固定归入一个队列，各队列作不同处理
6.多级反馈队列：时间片轮转算法和优先级算法的综合和发展



## 非 const 引用形参只能与完全同类型的非 const 对象关联。
应该将不需要修改的引用形参定义为 const 引用。普通的非 const 引用形参在使用时不太灵活。这样的形参既不能用 const 对象初始化，也不能用字面值或产生右值的表达式实参初始化。

## 寄存器及其作用
	数据寄存器 - 用来储存整数数字。
	浮点寄存器- 用来储存浮点数字。
  	地址寄存器 - 持有存储器地址，以及用来访问存储器。
  	通用目的寄存器  - 可以保存数据或地址两者，也就是说他们是结合 数据/地址 寄存器的功用。
	常数寄存器 - 用来持有只读的数值（例如 0、1、圆周率等等）。
 	向量寄存器 - 用来储存由向量处理器运行SIMD（Single Instruction, Multiple Data）指令所得到的数据。
  	指令寄存器（instruction register） - 储存现在正在被运行的指令
  	索引寄存器（index register） - 是在程序运行实用来更改运算对象地址之用。
	特殊目的寄存器 - 储存CPU内部的数据，像是程序计数器（或称为指令指针），堆栈寄存器，以及状态寄存器（或称微处理器状态字组）。

## select、poll、epoll
文件描述符（fd）：文件描述符是一个简单的整数，用以标明每一个被进程所打开的文件和socket的索引。第一个打开的文件是0，第二个是1，依此类推。最前面的三个文件描述符（0，1，2）分别与标准输入（stdin），标准输出（stdout）和标准错误（stderr）对应。`Unix 操作系统通常给每个进程能打开的文件数量强加一个限制。当用完所有的文件描述符后，将不能接收用户新的连接，直到一部分当前请求完成，相应的文件和socket被关闭`。

select，poll，epoll都是IO多路复用的机制。`I/O多路复用通过一种机制，可以监视多个文件描述符，一旦某个描述符就绪（一般是读就绪或者写就绪），能够通知程序进行相应的读写操作`。select，poll，epoll本质上都是同步I/O，因为他们都需要在读写事件就绪后自己负责进行读写，也就是说这个读写过程是阻塞的，而异步I/O则无需自己负责进行读写，异步I/O的实现会负责把数据从内核拷贝到用户空间。

epoll的改进：
（1）select，poll实现需要自己不断轮询所有fd集合，直到设备就绪，期间可能要睡眠和唤醒多次交替。而epoll其实也需要调用epoll_wait不断轮询就绪链表，期间也可能多次睡眠和唤醒交替，但是它是设备就绪时，调用回调函数，把就绪fd放入就绪链表中，并唤醒在epoll_wait中进入睡眠的进程。`虽然都要睡眠和交替，但是select和poll在“醒着”的时候要遍历整个fd集合，而epoll在“醒着”的时候只要判断一下就绪链表是否为空就行了`，这节省了大量的CPU时间。这就是回调机制带来的性能提升（本质的改进在于epoll采用基于事件的就绪通知方式）。
（2）select，poll每次调用都要把fd集合从用户态往内核态拷贝一次，并且要把current往设备等待队列中挂一次，而epoll只要一次拷贝，而且把current往等待队列上挂也只挂一次（在epoll_wait的开始，注意这里的等待队列并不是设备等待队列，只是一个epoll内部定义的等待队列）。（另一个本质的改进就是使用了内存映射（mmap）技术）

epoll被公认为Linux2.6下性能最好的多路I/O就绪通知方法，实现高效处理百万句柄。


## Linux程序、进程管理
Linux操作系统包括如下3种不同类型的进程，每种进程都有其自己的特点和属性。
（1）交互进程：由shell启动的进程。可在前台运行，也可在后台运行；
（2）批处理进程：一个进程序列；
（3）守护进程：守护进程（Daemon，也称为精灵进程）是指在后台运行而又没有启动终端或登录shell。守护进程一般由系统开机时通过脚本（script）自动激活启动或者由root用户通过shell启动。守护进程总是活跃的，一般在后台运行，所以它所处的状态是等待处理任务的请求。
启动守护进程有如下几种方法：
		1）在引导系统时启动：通过脚本启动，这些脚本一般位于/etc/rc.d中。在/etc目录下的很多rc文件都是启动脚本 。rc0.d，rc1.d,rc2.d,rc3.d,rc4.d,rc5.d,rc6.d,其中的数字代表在指定的runlevel下运行相应的描述，0代表关机，6代表重启。其中，以k开头的文件表示关闭，以s开头的文件表示重启。可查看相应文件夹中的readme文件。rc0.d，rc1.d,rc2.d,rc3.d,rc4.d,rc5.d,rc6.d，rcS.d都连接到/etc/init.d文件夹，该目录中存放着守护进程的运行文件。
		2）人工手动从shell提示符启动：任何具有权限的用户都可以启动相应的守护进程
root@Ubuntu:~# /etc/init.d/vsftpd start//启动ＦＴＰ服务器，ubuntu下默认已经安装了vsfptd服务器
		3）使用crond守护进程启动
		4）执行at命令启动
后台进程：在shell下直接输入命令后，shell将进程放到前台执行。如果要将进程放到后台执行，需要在命令行的结尾加上一个 “ & ” 符号。例如： root@Ubuntu:~#  find / -name passwd&
一般将一些比较耗时的操作放到后台执行。
前台进程与后台进程的区别：前台进程有控制终端，后台进程没有控制终端,所以没有结果可以显示。 前台进程绝大部分是用户进程，后台的一般（大多数是）系统进程。守护进程都是后台进程
进程的执行模式划分为用户模式和内核模式：系统进程，只运行在内核模式，执行操作系统代码，完成一些管理性的工作。用户进程，运行在用户模式下，通过系统调用或在出现中断、异常时进入内核模式。

## SOCKET函数
socket()
我们使用系统调用socket()来获得文件描述符：
int socket(int domain,int type,int protocol);
第一个参数domain设置为“AF_INET”。
第二个参数是套接口的类型：SOCK_STREAM或SOCK_DGRAM。
第三个参数设置为0。
系统调用socket()只返回一个套接口描述符，如果出错，则返回-1。 

bind()
一旦你有了一个套接口以后，下一步就是把套接口绑定到本地计算机的某一个端口上。但如果你只想使用connect()则无此必要。下面是系统调用bind()的使用方法：
int bind(int sockfd,struct sockaddr*my_addr,int addrlen);
第一个参数sockfd是由socket()调用返回的套接口文件描述符。
第二个参数my_addr是指向数据结构sockaddr的指针。数据结构sockaddr中包括了关于你的地址、端口和IP地址的信息。
第三个参数addrlen可以设置成sizeof(structsockaddr)。
下面是一个例子：
```
#define MYPORT 3490
main()
{
	int sockfd;
	struct sockaddr_in my_addr;
	sockfd=socket(AF_INET,SOCK_STREAM,0);	/*do someerror checking!*/
	my_addr.sin_family=AF_INET;	/*hostbyteorder*/
	my_addr.sin_port=htons(MYPORT);	/*short,network byte order*/
	my_addr.sin_addr.s_addr=inet_addr('132.241.5.10');
	bzero(&(my_addr.sin_zero),8);	/*zero the rest of the struct*/
	/*don't forget your error checking for bind():*/
	bind(sockfd,(struct sockaddr*)&my_addr,sizeof(struct sockaddr));
	...
```
	如果出错，bind()也返回-1。
	如果你使用connect()系统调用，那么你不必知道你使用的端口号。当你调用connect()时，它检查套接口是否已经绑定，如果没有，它将会分配一个空闲的端口。 
 
connect()
系统调用connect()的用法如下：
int connect(int sockfd,struct sockaddr* serv_addr,int addrlen);
第一个参数还是套接口文件描述符，它是由系统调用socket()返回的。
第二个参数是serv_addr是指向数据结构sockaddr的指针，其中包括目的端口和IP地址。
第三个参数可以使用sizeof(structsockaddr)而获得。
下面是一个例子：
```
#define DEST_IP '132.241.5.10'
#define DEST_PORT 23
main()
{
	intsockfd;
	struct sockaddr_in dest_addr;	/*will hold the destination addr*/
	sockfd=socket(AF_INET,SOCK_STREAM,0);	/*do some error checking!*/
	dest_addr.sin_family=AF_INET;	/*hostbyteorder*/
	dest_addr.sin_port=htons(DEST_PORT);/*short,network byte order*/
	dest_addr.sin_addr.s_addr=inet_addr(DEST_IP);
	bzero(&(dest_addr.sin_zero),8);/*zero the rest of the struct*/
		/*don'tforgettoerrorchecktheconnect()!*/
	connect(sockfd,(structsockaddr*)&dest_addr,sizeof(struct sockaddr));
	...
	同样，如果出错，connect()将会返回-1。 
 	
listen()
```
如果你希望不连接到远程的主机，也就是说你希望等待一个进入的连接请求，然后再处理它们。这样，你通过首先调用listen()，然后再调用accept()来实现。
系统调用listen()的形式如下：
int listen(int sockfd,int backlog);
第一个参数是系统调用socket()返回的套接口文件描述符。
第二个参数是进入队列中允许的连接的个数。进入的连接请求在使用系统调用accept()应答之前要在进入队列中等待。这个值是队列中最多可以拥有的请求的个数。大多数系统的缺省设置为20。你可以设置为5或者10。当出错时，listen()将会返回-1值。
当然，在使用系统调用listen()之前，我们需要调用bind()绑定到需要的端口，否则系统内核将会让我们监听一个随机的端口。
所以，如果你希望监听一个端口，下面是应该使用的系统调用的顺序：
	socket();
	bind();
	listen();
		/*accept()goes here*/ 
 
accept()
系统调用accept()比较起来有点复杂。在远程的主机可能试图使用connect()连接你使用listen()正在监听的端口。但此连接将会在队列中等待，直到使用accept()处理它。调用accept()之后，将会返回一个全新的套接口文件描述符来处理这个单个的连接。这样，对于同一个连接来说，你就有了两个文件描述符。原先的一个文件描述符正在监听你指定的端口，新的文件描述符可以用来调用send()和recv()。
调用的例子如下：
int accept(intsockfd,void*addr,int*addrlen);
第一个参数是正在监听端口的套接口文件描述符。
第二个参数addr是指向本地的数据结构
sockaddr_in的指针。调用connect()中的信息将存储在这里。通过它你可以了解哪个主机在哪个端口呼叫你。
第三个参数同样可以使用sizeof(structsockaddr_in)来获得。
如果出错，accept()也将返回-1。下面是一个简单的例子：
```
#define MYPORT 3490	/*theportuserswillbeconnectingto*/
#define BACKLOG 10/*howmanypendingconnectionsqueuewillhold*/
main()
{
	intsockfd,new_fd;/*listenonsock_fd,newconnectiononnew_fd*/
	struct sockaddr_in my_addr;/*myaddressinformation*/
	struct sockaddr_in their_addr;/*connector'saddressinformation*/
	int sin_size;
 	sockfd=socket(AF_INET,SOCK_STREAM,0);	/*dosomeerrorchecking!*/
	my_addr.sin_family=AF_INET;	/*hostbyteorder*/
	my_addr.sin_port=htons(MYPORT);	/*short,networkbyteorder*/
	my_addr.sin_addr.s_addr=INADDR_ANY;/*auto-fillwithmyIP*/
	bzero(&(my_addr.sin_zero),8);/*zerotherestofthestruct*/
		/*don'tforgetyourerrorcheckingforthesecalls:*/
	bind(sockfd,(structsockaddr*)&my_addr,sizeof(structsockaddr));
	listen(sockfd,BACKLOG);
	sin_size=sizeof(structsockaddr_in);
	new_fd=accept(sockfd,&their_addr,&sin_size);
	...
```
下面，我们将可以使用新创建的套接口文件描述符new_fd来调用send()和recv()。 
 
send() 和recv()
系统调用send()的用法如下：
int send(int sockfd,const void* msg,int len,int flags);
第一个参数是你希望给发送数据的套接口文件描述符。它可以是你通过socket()系统调用返回的，也可以是通过accept()系统调用得到的。
第二个参数是指向你希望发送的数据的指针。
第三个参数是数据的字节长度。第四个参数标志设置为0。
下面是一个简单的例子：
```
char* msg='Beejwashere!';
int len,bytes_sent;
..
len=strlen(msg);
bytes_sent=send(sockfd,msg,len,0);
...
```
系统调用send()返回实际发送的字节数，这可能比你实际想要发送的字节数少。如果返回的字节数比要发送的字节数少，你在以后必须发送剩下的数据。当send()出错时，将返回-1。
系统调用recv()的使用方法和send()类似：
int recv(int sockfd,void* buf,int len,unsigned int flags);
第一个参数是要读取的套接口文件描述符。
第二个参数是保存读入信息的地址。
第三个参数是缓冲区的最大长度。第四个参数设置为0。
系统调用recv()返回实际读取到缓冲区的字节数，如果出错则返回-1。
这样使用上面的系统调用，你可以通过数据流套接口来发送和接受信息。 
 
sendto() 和recvfrom()
因为数据报套接口并不连接到远程的主机上，所以在发送数据包之前，我们必须首先给出目的地址，请看：
int sendto(int sockfd,const void* msg,int len,unsigned int flags,
								conststruct sockaddr*to,inttolen);
除了两个参数以外，其他的参数和系统调用send()时相同。
参数to是指向包含目的IP地址和端口号的数据结构sockaddr的指针。
参数tolen可以设置为sizeof(structsockaddr)。
系统调用sendto()返回实际发送的字节数，如果出错则返回-1。
系统调用recvfrom()的使用方法也和recv()的十分近似：
int recvfrom(int sockfd,void* buf,int len,unsigned int flags
						struct sockaddr* from,int* fromlen);
参数from是指向本地计算机中包含源IP地址和端口号的数据结构sockaddr的指针。
参数fromlen设置为sizeof(struct sockaddr)。
系统调用recvfrom()返回接收到的字节数，如果出错则返回-1。 
 
close() 和shutdown()
你可以使用close()调用关闭连接的套接口文件描述符：
close(sockfd);
这样就不能再对此套接口做任何的读写操作了。
使用系统调用shutdown()，可有更多的控制权。它允许你在某一个方向切断通信，或者切断双方的通信：
int shutdown(int sockfd,int how);
第一个参数是你希望切断通信的套接口文件描述符。第二个参数how值如下：
0—Furtherreceivesaredisallowed
1—Furthersendsaredisallowed
2—Furthersendsandreceivesaredisallowed(likeclose())
shutdown()如果成功则返回0，如果失败则返回-1。 
 
getpeername()
这个系统的调用十分简单。它将告诉你是谁在连接的另一端：
int getpeername(int sockfd,struct sockaddr* addr,int* addrlen);
第一个参数是连接的数据流套接口文件描述符。
第二个参数是指向包含另一端的信息的数据结构sockaddr的指针。
第三个参数可以设置为sizeof(structsockaddr)。
如果出错，系统调用将返回-1。
一旦你获得了它们的地址，你可以使用inet_ntoa()或者gethostbyaddr()来得到更多的信息。
 
gethostname()
系统调用gethostname()比系统调用getpeername()还简单。它返回程序正在运行的计算机的名字。系统调用gethostbyname()可以使用这个名字来决定你的机器的IP地址。
下面是一个例子：
int gethostname(char*hostname,size_tsize);
如果成功，gethostname将返回0。如果失败，它将返回-1。 
•htonl()：把32位值从主机字节序转换成网络字节序 
•htons()：把16位值从主机字节序转换成网络字节序 
•ntohl()：把32位值从网络字节序转换成主机字节序 
•ntohs()：把16位值从网络字节序转换成主机字节序 
  
设置Socket缓冲区 
int zero = 0;
setsockopt( ov->m_Socket, SOL_SOCKET, SO_SNDBUF, (char *) &zero, sizeof zero );
setsockopt( ov->m_Socket, SOL_SOCKET, SO_RCVBUF, (char *) &zero, sizeof zero );

## 死锁产生的四个必要条件
（1） 互斥条件：一个资源每次只能被一个进程使用。
（2） 请求与保持条件：一个进程因请求资源而阻塞时，对已获得的资源保持不放。
（3） 不剥夺条件:进程已获得的资源，在未使用完之前，不能强行剥夺。
（4） 循环等待条件:若干进程之间形成一种头尾相接的循环等待资源关系。
这四个条件是死锁的必要条件。只要系统发生死锁，这些条件必然成立，而只要上述条件之
一不满足，就不会发生死锁。

## nohup与&的区别
& 要是关闭终端那么脚本也停了，
加nohup  既使把终端关了，脚本也会跑，是在服务器那运行的。

nohup 命令运行由 Command 参数和任何相关的 Arg 参数指定的命令，忽略所有挂断（SIGHUP）信号。在注销后使用 nohup 命令运行后台中的程序。

一般结合使用：nohup command & 

## LINUX中什么是僵尸进程和孤儿进程，它们是否消耗系统资源？
僵尸进程将会导致资源浪费，而孤儿则不会。
由于子进程的结束和父进程的运行是一个异步过程,即父进程永远无法预测子进程到底什么时候结束. 那么会不会因为父进程太忙来不及wait子进程,或者说不知道子进程什么时候结束,而丢失子进程结束时的状态信息呢? 不会。因为UNIX提供了一种机制可以保证只要父进程想知道子进程结束时的状态信息，就可以得到。这种机制就是: 在每个进程退出的时候,内核释放该进程所有的资源,包括打开的文件,占用的内存等。 但是仍然为其保留一定的信息(包括进程号、退出状态、运行时间等)。直到父进程通过wait / waitpid来取时才释放. 但这样就导致了问题,如果进程不调用wait/waitpid的话, 那么保留的那段信息就不会释放,其进程号就会一直被占用,但是系统所能使用的进程号是有限的,如果大量的产生僵死进程,将因为没有可用的进程号而导致系统不能产生新的进程. 此即为僵尸进程的危害,应当避免。
	子进程结束后为什么要进入僵尸状态? 
	因为父进程可能要取得子进程的退出状态等信息。 
	僵尸状态是每个子进程必经的状态吗？ 
	是的。 任何一个子进程(init除外)在exit()之后，并非马上就消失掉，而是留下一个称为僵尸进程(Zombie)的数据结构，等待父进程处理。这是每个子进程在结束时都要经过的阶段。如果子进程在exit()之后，父进程没有来得及处理，这时用ps命令就能看到子进程的状态是“Z”。如果父进程能及时 处理，可能用ps命令就来不及看到子进程的僵尸状态，但这并不等于子进程不经过僵尸状态。 * 如果父进程在子进程结束之前退出，则子进程将由init接管。init将会以父进程的身份对僵尸状态的子进程进行处理。 
	如何查看僵尸进程？
	$ ps -el 其中，有标记为Z的进程就是僵尸进程 S代表休眠状态；D代表不可中断的休眠状态；R代表运行状态；Z代表僵死状态；T代表停止或跟踪状态
	僵尸进程变为孤儿进程：父进程死后，僵尸进程成为"孤儿进程"，过继给1号进程init，init始终会负责清理僵尸进程．它产生的所有僵尸进程也跟着消失。

## IO阻塞、非阻塞、同步、异步
同步和异步
同步和异步是针对应用程序和内核的交互而言的，同步指的是用户进程触发I/O操作并等待或者轮询的去查看I/O操作是否就绪，而异步是指用户进程触发I/O操作以后便开始做自己的事情，而当I/O操作已经完成的时候会得到I/O完成的通知。

阻塞和非阻塞
阻塞和非阻塞是针对于进程在访问数据的时候，根据I/O操作的就绪状态来采取的不同方式，是一种读取或者写入函数的实现方式，阻塞方式下读取或者写入函数将一直等待，而非阻塞方式下，读取或者写入函数会立即返回一个状态值。

服务器端有以下几种IO模型：
（1）阻塞式模型（blocking IO）
大部分的socket接口都是阻塞型的（ listen()、accpet()、send()、recv() 等）。阻塞型接口是指系统调用（一般是 IO 接口）不返回调用结果并让当前线程一直阻塞，只有当该系统调用获得结果或者超时出错时才返回。在线程被阻塞期间，线程将无法执行任何运算或响应任何的网络请求，这给多客户机、多业务逻辑的网络编程带来了挑战。
![image](https://github.com/woojean/woojean.github.io/blob/master/images/basic_9.png)
（2）多线程的服务器模型（Multi-Thread）
应对多客户机的网络应用，最简单的解决方式是在服务器端使用多线程（或多进程）。多线程（或多进程）的目的是让每个连接都拥有独立的线程（或进程），这样任何一个连接的阻塞都不会影响其他的连接。但是如果要同时响应成千上万路的连接请求，则无论多线程还是多进程都会严重占据系统资源，降低系统对外界响应效率。
在多线程的基础上，可以考虑使用“线程池”或“连接池”，“线程池”旨在减少创建和销毁线程的频率，其维持一定合理数量的线程，并让空闲的线程重新承担新的执行任务。“连接池”维持连接的缓存池，尽量重用已有的连接、减少创建和关闭连接的频率。这两种技术都可以很好的降低系统开销，都被广泛应用很多大型系统。

（3）非阻塞式模型（Non-blocking IO）
相比于阻塞型接口的显著差异在于，在被调用之后立即返回。
![image](https://github.com/woojean/woojean.github.io/blob/master/images/basic_10.png)
需要应用程序调用许多次来等待操作完成。这可能效率不高，因为在很多情况下，当内核执行这个命令时，应用程序必须要进行`忙碌等待`，直到数据可用为止。
另一个问题，在循环调用非阻塞IO的时候，将大幅度占用CPU，所以一般使用select等来检测”是否可以操作“。

（4）多路复用IO（IO multiplexing）
支持I/O复用的系统调用有select、poll、epoll、kqueue等。使用Select返回后，仍然需要轮询再检测每个socket的状态（读、写），这样的轮训检测在大量连接下也是效率不高的。因为当需要探测的句柄值较大时，select () 接口本身需要消耗大量时间去轮询各个句柄。
很多操作系统提供了更为高效的接口，如 linux 提供 了 epoll，BSD 提供了 kqueue，Solaris 提供了 /dev/poll …。如果需要实现更高效的服务器程序，类似 epoll 这样的接口更被推荐。
![image](https://github.com/woojean/woojean.github.io/blob/master/images/basic_11.png)

（5）使用事件驱动库libevent的服务器模型
libevent是一个事件触发的网络库，适用于windows、linux、bsd等多种平台，内部使用select、epoll、kqueue、IOCP等系统调用管理事件机制。著名分布式缓存软件memcached也是基于libevent，而且libevent在使用上可以做到跨平台。
libevent 库提供一种事件机制，它作为底层网络后端的包装器。`事件系统让为连接添加处理函数变得非常简便，同时降低了底层IO复杂性。这是 libevent 系统的核心`。
创建 libevent 服务器的基本方法是，注册当发生某一操作（比如接受来自客户端的连接）时应该执行的函数，然后调用主事件循环 event_dispatch()。执行过程的控制现在由 libevent 系统处理。注册事件和将调用的函数之后，事件系统开始自治；在应用程序运行时，可以在事件队列中添加（注册）或 删除（取消注册）事件。事件注册非常方便，可以通过它添加新事件以处理新打开的连接，从而构建灵活的网络处理系统。

（6）信号驱动IO模型（Signal-driven IO）
让内核在描述符就绪时发送SIGIO信号通知应用程序。
![image](https://github.com/woojean/woojean.github.io/blob/master/images/basic_12.png)

（7）异步IO模型（asynchronous IO）
告知内核启动某个操作，并`让内核`在整个操作（`包括将数据从内核复制到我们自己的缓冲区`）完成后通知我们。这种模型与信号驱动模型的主要区别在于：信号驱动式I/O是由内核通知我们何时可以启动一个I/O操作，而异步I/O模型是由内核通知我们I/O操作何时完成。
![image](https://github.com/woojean/woojean.github.io/blob/master/images/basic_13.png)

同步和异步IO的区别：
A synchronous I/O operation causes the requesting process to be blocked until that I/O operation completes;
An asynchronous I/O operation does not cause the requesting process to be blocked; 
两者的区别就在于synchronous IO做”IO operation”的时候会将process阻塞。按照这个定义阻塞、非阻塞、IO多路复用其实都属于同步IO。

**<font color='red'>非阻塞与异步IO的区别</font>**
在non-blocking IO中，虽然进程大部分时间都不会被block，但是它仍然要求进程去主动的check，并且当数据准备完成以后，也需要进程主动的再次调用recvfrom来将数据拷贝到用户内存。而asynchronous IO则完全不同。它就像是用户进程将整个IO操作（分为两步：准备数据、将数据从内核复制到用户空间）交给了他人（kernel）完成，然后他人做完后发信号通知。在此期间，用户进程不需要去检查IO操作的状态，也不需要主动的去拷贝数据。

## 分页和分段的主要区别 
(1)页是信息的物理单位，分页是为实现离散分配方式，以消减内存的外零头，提高内存的利用率。或者说，分页仅仅是由于系统管理的需要而不是用户的需要。段则是信息的逻辑单位，它含有一组其意义相对完整的信息。分段的目的是为了能更好地满足用户的需要。 
(2)页的大小固定且由系统决定，由系统把逻辑地址划分为页号和页内地址两部分，是由机器硬件实现的，因而在系统中只能有一种大小的页面；而段的长度却不固定，决定于用户所编写的程序，通常由编译程序在对源程序进行编译时，根据信息的性质来划分。
(3)分页的作业地址空间是一维的，即单一的线性地址空间，程序员只需利用一个记忆符，即可表示一个地址；而分段的作业地址空间则是二维的，程序员在标识一个地址时，既需给出段名， 又需给出段内地址。

## Linux中通过编译安装的方式安装程序，各步骤操作分别做什么工作？
源码要运行，必须先转成二进制的机器码。这是编译器的任务。
对于简单的代码，可以直接调用编译器生成二进制文件后运行，如：
$ gcc test.c
$ ./a.out
对于复杂的项目，编译过程通常分成3个部分：
$ ./configure
$ make  
$ make install

整个编译安装过程分为以下步骤：
（1）配置
配置信息保存在一个配置文件之中，约定俗成是一个叫做configure的脚本文件。通常它是由autoconf工具生成的。编译器通过运行这个脚本，获知编译参数。如果用户的系统环境比较特别，或者有一些特定的需求，就需要手动向configure脚本提供编译参数，如：
$ ./configure --prefix=/www --with-mysql	# 指定安装后的文件保存在www目录，并且编译时加入mysql模块的支持

（2）确定标准库和头文件的位置
从配置文件中知道标准库和头文件的位置。

（3）确定依赖关系
源码文件之间往往存在依赖关系，编译器需要确定编译的先后顺序。假定A文件依赖于B文件，编译器应该保证做到下面两点。
1）只有在B文件编译完成后，才开始编译A文件。
2）当B文件发生变化时，A文件会被重新编译。
编译顺序保存在一个叫做makefile的文件中，里面列出哪个文件先编译，哪个文件后编译。而makefile文件由configure脚本运行生成，这就是为什么编译时configure必须首先运行的原因。

（4）预编译头文件
不同的源码文件，可能引用同一个头文件（比如stdio.h）。编译的时候，头文件也必须一起编译。为了节省时间，编译器会在编译源码之前，先编译头文件。这保证了头文件只需编译一次，不必每次用到的时候，都重新编译了。不过，并不是头文件的所有内容都会被预编译。用来声明宏的#define命令，就不会被预编译。

（5）预处理
编译器就开始替换掉源码中的头文件和宏以及移除注释。

（6）编译
编译器就开始生成机器码。对于某些编译器来说，还存在一个中间步骤，会先把源码转为汇编码（assembly），然后再把汇编码转为机器码。这种转码后的文件称为对象文件（object file）。

（7）链接
把外部函数的代码（通常是后缀名为.lib和.a的文件）添加到可执行文件中。这就叫做连接（linking）。这种通过拷贝，将外部函数库添加到可执行文件的方式，叫做静态连接（static linking）
make命令的作用，就是从第（4）步头文件预编译开始，一直到做完这一步。

（8）安装
将可执行文件保存到用户事先指定的安装目录。这一步还必须完成创建目录、保存文件、设置权限等步骤。这整个的保存过程就称为"安装"（Installation）。

（9）操作系统链接
以某种方式通知操作系统，让其知道可以使用这个程序了。这就要求在操作系统中，登记这个程序的元数据：文件名、文件描述、关联后缀名等等。Linux系统中，这些信息通常保存在/usr/share/applications目录下的.desktop文件中。
make install命令，就用来完成"安装"和"操作系统连接"这两步。

（10）生成安装包
将上一步生成的可执行文件，做成可以分发的安装包。通常是将可执行文件（连带相关的数据文件），以某种目录结构，保存成压缩文件包，交给用户。

（11）动态链接
开发者可以在编译阶段选择可执行文件连接外部函数库的方式，到底是静态连接（编译时连接），还是动态连接（运行时连接）。
静态连接就是把外部函数库，拷贝到可执行文件中。这样做的好处是，适用范围比较广，不用担心用户机器缺少某个库文件；缺点是安装包会比较大，而且多个应用程序之间，无法共享库文件。
动态连接的做法正好相反，外部函数库不进入安装包，只在运行时动态引用。好处是安装包会比较小，多个应用程序可以共享库文件；缺点是用户必须事先安装好库文件，而且版本和安装位置都必须符合要求，否则就不能正常运行。
现实中，大部分软件采用动态连接，共享库文件。这种动态共享的库文件，Linux平台是后缀名为.so的文件，Windows平台是.dll文件，Mac平台是.dylib文件。

## 什么是大尾表示法？什么是小尾表示法？
Little-Endian:低位字节放在内存的低地址段；
Big-Endian：高位字节放在内存的低地址段；
问题1
unsigned char endian[2] = {1, 0}; 
short x; 
x = *(short *) endian; 
代码运行后，x的值是多少？
 ![image](https://github.com/woojean/woojean.github.io/blob/master/images/img_5.png)
 
问题2
int i = 1; 
 if (*(char*)&i == 0) 
     return true 
 else 
     return false
答案：
1.	是big endian，x = 256；是little endian，x = 1
2.	是big endian，true；是little endian，false
考察点：
1.如果应试者直接回答，不太妥当，应该问面试官系统是高字节还是低字节
2.如何判断系统是高字节还是低字节

## 如何查看Linux进程之间的关系？
ps -o pid,pgid,ppid,comm | cat

输出：
  PID  PGID  PPID COMMAND
 3003  3003  2986 su
 3004  3004  3003 bash
 3423  3423  3004 ps
 3424  3423  3004 cat

每个进程都会属于一个进程组(process group)，每个进程组中可以包含多个进程。进程组会有一个进程组领导进程 (process group leader)，领导进程的PID (PID见Linux进程基础)成为进程组的ID (process group ID, PGID)，以识别进程组。PID为进程自身的ID，PGID为进程所在的进程组的ID， PPID为进程的父进程ID。

## 进程通信的类型 
1. 共享存储器系统(Shared-Memory System)（全局变量）
	(1)基于共享数据结构的通信方式。 
	(2)基于共享存储区的通信方式。 
2. 消息传递系统(Message passing system)
进程间的数据交换，是以格式化的消息(message)为单位的；在计算机网络中，又把message称为报文。
3. 管道(Pipe)通信
“管道”，是指用于连接一个读进程和一个写进程以实现他们之间通信的一个共享文件，又名pipe文件。向管道(共享文件)提供输入的发送进程(即写进程)， 以字符流形式将大量的数据送入管道；而接受管道输出的接收进程(即读进程)，则从管道中接收(读)数据。
为了协调双方的通信，管道机制必须提供以下三方面的协调能力：
	① 互斥，即当一个进程正在对pipe执行读/写操作时，其它(另一)进程必须等待。 
	② 同步，指当写(输入)进程把一定数量(如4 KB)的数据写入pipe，便去睡眠等待， 直到读(输出)进程取走数据后，再把他唤醒。当读进程读一空pipe时，也应睡眠等待，直至写进程将数据写入管道后，才将之唤醒。
	③ 确定对方是否存在，只有确定了对方已存在时，才能进行通信。

## 简述ext2和ext3的区别？
Linux ext2/ext3文件系统使用索引节点来记录文件信息，作用像windows的文件分配表。索引节点是一个结构，它包含了一个文件的长度、创建及修改时间、权限、所属关系、磁盘中的位置等信息。一个文件系统维护了一个索引节点的数组，每个文件或目录都与索引节点数组中的唯一一个元素对应。系统给每个索引节点分配了一个号码，也就是该节点在数组中的索引号，称为索引节点号。 linux文件系统将文件索引节点号和文件名同时保存在目录中。所以，目录只是将文件的名称和它的索引节点号结合在一起的一张表，目录中每一对文件名称和索引节点号称为一个连接。 对于一个文件来说有唯一的索引节点号与之对应，对于一个索引节点号，却可以有多个文件名与之对应。因此，在磁盘上的同一个文件可以通过不同的路径去访问它。
Linux缺省情况下使用的文件系统为Ext2，ext2文件系统的确高效稳定。但是，随着Linux系统在关键业务中的应用，Linux文件系统的弱点也渐渐显露出来了:其中系统缺省使用的ext2文件系统是非日志文件系统。这在关键行业的应用是一个致命的弱点。本文向各位介绍Linux下使用ext3日志文件系统应用。
Ext3文件系统是直接从Ext2文件系统发展而来，目前ext3文件系统已经非常稳定可靠。它完全兼容ext2文件系统。用户可以平滑地过渡到一个日志功能健全的文件系统中来。这实际上了也是ext3日志文件系统初始设计的初衷。
Ext3日志文件系统的特点
1、高可用性
系统使用了ext3文件系统后，即使在非正常关机后，系统也不需要检查文件系统。宕机发生后，恢复ext3文件系统的时间只要数十秒钟。
2、数据的完整性:
ext3文件系统能够极大地提高文件系统的完整性，避免了意外宕机对文件系统的破坏。在保证数据完整性方面，ext3文件系统有2种模式可供选择。其中之一就是“同时保持文件系统及数据的一致性”模式。采用这种方式，你永远不再会看到由于非正常关机而存储在磁盘上的垃圾文件。
3、文件系统的速度:
尽管使用ext3文件系统时，有时在存储数据时可能要多次写数据，但是，从总体上看来，ext3比ext2的性能还要好一些。这是因为ext3的日志功能对磁盘的驱动器读写头进行了优化。所以，文件系统的读写性能较之Ext2文件系统并来说，性能并没有降低。
4、数据转换
  由ext2文件系统转换成ext3文件系统非常容易，只要简单地键入两条命令即可完成整个转换过程，用户不用花时间备份、恢复、格式化分区等。用一个ext3文件系统提供的小工具tune2fs，它可以将ext2文件系统轻松转换为ext3日志文件系统。另外，ext3文件系统可以不经任何更改，而直接加载成为ext2文件系统。
5、多种日志模式
  Ext3有多种日志模式，一种工作模式是对所有的文件数据及metadata（定义文件系统中数据的数据,即数据的数据）进行日志记录（data=journal模式）；另一种工作模式则是只对metadata记录日志，而不对数据进行日志记录，也即所谓data=ordered或者data=writeback模式。系统管理人员可以根据系统的实际工作要求，在系统的工作速度与文件数据的一致性之间作出选择。


## Linux中有哪些设备类型？
在Linux中，设备被分为以下三种类型：
1.块设备：可寻址，寻址以块为单位，块大小取决于设备。通常支持对数据的随机访问，如硬盘、蓝光光碟、闪存等。通过称为“块设备节点”的特殊文件来访问，`通常被挂载为文件系统`。
2.字符设备：不可寻址，仅提供数据的流式访问，即一个个字符或一个个字节，如键盘、鼠标、打印机等。通过称为“字符设备节点”的特殊文件来访问，`与块设备不同，应用程序通过直接访问设备节点与字符设备交互`。
3.网络设备：通过一个物理适配器和一种特定的网络协议提供了对网络的访问，打破了Unix所有东西都是文件的设计原则，不是通过设备节点来访问，而是通过套接字API这样的特殊接口来访问。

伪设备
并不是所有设备驱动都表示物理设备，有些设备驱动是虚拟的，仅提供访问内核功能而已，被称为“伪设备”，如内核随机数发生器（/dev/random）、空设备（/dev/null）、零设备（/dev/zero）等等。

## 后台进程与守护进程有什么区别？
（1）最直观的区别：守护进程没有控制终端，而后台进程还有。如通过命令firefox &在后台运行firefox，此时firefox虽然在后台运行，但是并没有脱离终端的控制，如果把终端关掉则firefox也会一起关闭。
（2）后台进程的文件描述符继承自父进程，例如shell，所以它也可以在当前终端下显示输出数据。但是`守护进程自己变成进程组长`，其文件描述符号和控制终端没有关联，是控制台无关的。
（3）`守护进程肯定是后台进程，但后台进程不一定是守护进程`。基本上任何一个程序都可以后台运行，但守护进程是具有特殊要求的程序，比如它能够脱离自己的父进程，成为自己的会话组长等（这些需要在程序代码中显式地写出来）。


## 乐观锁与悲观锁的区别
`悲观锁`(Pessimistic Lock)每次去拿数据的时候都认为别人会修改，所以每次在拿数据的时候都会上锁，这样别人想拿这个数据就会block直到它拿到锁。传统的关系型数据库里边就用到了很多这种锁机制，比如行锁，表锁等，读锁，写锁等，都是在做操作之前先上锁。
`乐观锁`(Optimistic Lock)每次去拿数据的时候都认为别人不会修改，所以不会上锁，但是`在更新的时候`会判断一下在此期间别人有没有去更新这个数据，可以使用版本号等机制。`乐观锁适用于多读的应用类型`，这样可以提高吞吐量，像数据库如果提供类似于write_condition机制的其实都是提供的乐观锁。
两种锁各有优缺点，不可认为一种好于另一种，像乐观锁适用于写比较少的情况下，即冲突真的很少发生的时候，这样可以省去了锁的开销，加大了系统的整个吞吐量。但如果经常产生冲突，上层应用会不断的进行retry，这样反倒是降低了性能，所以这种情况下用悲观锁就比较合适。





































































