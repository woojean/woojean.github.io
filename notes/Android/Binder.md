# Binder

Binder用于完成进程间通信（IPC），工作在Linux层面，属于一个驱动，只是这个驱动不需要硬件，或者说其操作的硬件是基于一小段内存。从线程的角度讲，Binder驱动代码运行在内核态，客户端程序调用Binder是通过系统调用完成的。Binder是一种架构，这种架构提供了服务端接口、Binder驱动、客户端接口三个模块。一个Binder服务端实际上就是一个Binder类的对象，该对象一旦创建就会在内部启动一个隐藏线程，该线程接下来会接收Binder驱动发送的消息，收到消息后会执行Binder对象中的onTransact()函数，并按照该函数的参数执行不同的服务代码。任意一个服务端Binder对象被创建时，同时会在Binder驱动中创建一个mRemote对象，该对象的类型也是Binder类。客户端要访问远程服务时都是通过mRemote对象，必须获取远程服务在Binder驱动中对应的mRemote引用，然后调用其transact()方法，transact()方法的内容主要包括以下几项：
1）以线程间消息通信的模式，向服务端发送客户端传递过来的参数；
2）挂起客户端线程，并等待服务端线程执行完指定服务函数后通知；
3）接收到服务端线程的通知，然后继续执行客户端线程，并返回到客户端代码区；
例：
设计Service端：

```java
public class MusicPlayerService extends Binder{
  @override
  protected boolean onTransact( int code, Parcel data, Parcel reply, int flags)
    throws RemoteException{
    return super.onTransact(code, data, reply, flags);
  }
  public void start( String filePath){
  }
  public void stop(){
  }
}
```
有关code标识、data变量中的参数信息等需要调用者和服务者双方有个约定。假设用正整数1000代表双方约定的要调用start()函数的值，客户端在传入的包裹data中存放的第一个数据就是filepath变量，那么onTransact()的代码可以如下所示：
```java
switch( code ){
  case 1000:
    data.enforceInterface(“MusicPlayerService”); 
    //一种校验，与客户端的writeInterfaceToken()对应
    String filePath = data.readString();
    start(filePath);
    //replay.writeXXX(); 
    //返回客户端期望的一些结果
    break;
	}
```
Binder客户端设计：
客户端可以如下调用transact()方法：
```java
IBinder mRemote = null;
String filePath = “/sdcard/music/song.mp3”;
int code = 1000;
Parcel data = Parcel.obtain(); //包裹不是自己创建的，而是调用Parcel.obtain();申请的
Parcel reply = Parcel.obtain();
data.writeInterfaeToken(“MusicPlayerService”); //标注远程服务名称，理论上是不需要的
data.writeString(filePath); //包裹中添加的内容是有序的，这个顺序必须事先约定
mRemote.transact(code, data, reply, 0); //最后一个参数指定IPC调用模式，0表示双向，1表示单向
IBinder binder = reply.readStrongBinder();
reply.recycle();
data.recycle();
```
在transact()方法调用后，客户端线程进入Binder驱动，Binder驱动会挂起当前的客户端线程，并向远程服务发送一个消息，消息中包含了客户端传进来的包裹。服务端拿到包裹后会对包裹进行拆解，然后执行指定的服务函数，执行完毕后再把执行结果放入客户端提供的reply包裹中，然后服务端向Binder驱动发送一个notify，从而使得客户端线程从Binder驱动代码区返回到客户端代码区。
客户端获取服务端Binder对象的引用：
可以仅使用Binder类扩展系统服务，但对于客户端服务则必须基于Service类来编写。AmS（Activity manager Service）提供了startService()函数用于启动客户服务，而对于客户端来讲，可以使用如下的两个函数来和一个服务建立连接，其原型在ContextImpl类中：
1）public ComponentName startService( Intent intent );
该函数用于启动intent指定的服务，但是启动后客户端暂时还没有获得服务端的Binder引用，因此暂时还不能调用任何服务功能。
2）public boolean bindService( Intent service, ServiceConnection conn, int flags );
该函数用于绑定一个服务，其中参数conn的类型信息如下：
```java
public interface ServiceConnection{
  public void onServiceConnected( ComponentName name, IBinder service );
  public void onServiceDisconnected( ComponetName name );
}
```
onServiceConnected()函数的第二个变量为IBinder类型，当客户端请求AmS启动某个Service后，如果该Service正常启动，那么AmS就会远程调用ActivityThread类中的ApplicationThread对象，调用的参数中会包含Service的Binder引用，然后在ApplicationThread中会回调bindService中的conn接口。因此在客户端中，可以在onServiceConnected()方法中将其参数Service保存为一个全局变量，从而在客户端的任何地方都可以随时调用该远程服务。

客户端和服务端的事先约定：
Android的SDK中提供了一个aidl工具，该工具可以把一个aidl文件转换为一个Java类文件，在该Java类文件中同时重载了transact()和onTransact()方法，统一了存入包裹和读取包裹参数。aidl实际就是一个脚本，该工具并不是必须的。
系统服务中的Binder对象
系统服务的信息可以通过getSystemService()函数获取，该函数实现在ContextImpl类中。系统服务并不是通过startService()启动的。ServiceManager是一个独立进程，管理各种系统服务，ServiceManager本身也是一个Service，Framework提供了一个静态系统函数BinderInternal.getContextObject()可以获取该Service对应的Binder引用，之后就可以通过ServiceManager提供的方法来获取其他系统Service的Binder引用（其他系统服务在启动时会首先把自己的Binder对象传递给ServiceManager，即注册）。