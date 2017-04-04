# SOCKET函数

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
```c
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
```c
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
```c
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
```c
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