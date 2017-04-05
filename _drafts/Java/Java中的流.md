# Java中的流

流是个抽象的概念，是对输入输出设备的抽象，Java程序中，对于数据的输入/输出操作都是以“流”的方式进行。设备可以是文件，网络，内存等。流具有方向性，至于是输入流还是输出流则是一个相对的概念，一般以程序为参考，如果数据的流向是程序至设备，我们成为输出流，反之我们称为输入流。
当程序需要从某个数据源读入数据的时候，就会开启一个输入流，数据源可以是文件、内存或网络等等。相反地，需要写出数据到某个数据源目的地的时候，也会开启一个输出流，这个数据源目的地也可以是文件、内存或网络等等。
可以从不同的角度对流进行分类：
1.处理的数据单位不同，可分为：字符流，字节流
2.数据流方向不同，可分为：输入流，输出流
3.功能不同，可分为：节点流，处理流
1.和 2.都比较好理解，对于根据功能分类的，可以这么理解：
节点流：节点流从一个特定的数据源读写数据。即节点流是直接操作文件，网络等的流，例如FileInputStream和FileOutputStream，他们直接从文件中读取或往文件中写入字节流。

![image](https://github.com/woojean/woojean.github.io/blob/master/images/java_10.png)
处理流：“连接”在已存在的流（节点流或处理流）之上通过对数据的处理为程序提供更为强大的读写功能。过滤流是使用一个已经存在的输入流或输出流连接创建的，过滤流就是对节点流进行一系列的包装。例如BufferedInputStream和BufferedOutputStream，使用已经存在的节点流来构造，提供带缓冲的读写，提高了读写的效率，以及DataInputStream和DataOutputStream，使用已经存在的节点流来构造，提供了读写Java中的基本数据类型的功能。他们都属于过滤流。

![image](https://github.com/woojean/woojean.github.io/blob/master/images/java_11.png)


流结构介绍：
Java所有的流类位于java.io包中，都分别继承自以下四种抽象流类型。
字节流	
字符流
输入流	InputStream	Reader
输出流	OutputStream	Writer
1.继承自InputStream/OutputStream的流都是用于向程序中输入/输出数据，且数据的单位都是字节(byte=8bit)，如图，深色的为节点流，浅色的为处理流。

![image](https://github.com/woojean/woojean.github.io/blob/master/images/java_12.png)

![image](https://github.com/woojean/woojean.github.io/blob/master/images/java_13.png)

2.继承自Reader/Writer的流都是用于向程序中输入/输出数据，且数据的单位都是字符(2byte=16bit)，如图，深色的为节点流，浅色的为处理流。

![image](https://github.com/woojean/woojean.github.io/blob/master/images/java_14.png)

![image](https://github.com/woojean/woojean.github.io/blob/master/images/java_15.png)

常见流类介绍：
节点流类型常见的有：
对文件操作的字符流有FileReader/FileWriter，字节流有FileInputStream/FileOutputStream。
处理流类型常见的有：
缓冲流：缓冲流要“套接”在相应的节点流之上，对读写的数据提供了缓冲的功能，提高了读写效率，同事增加了一些新的方法。
字节缓冲流有BufferedInputStream/BufferedOutputStream，字符缓冲流有BufferedReader/BufferedWriter，字符缓冲流分别提供了读取和写入一行的方法ReadLine和NewLine方法。
对于输出的缓冲流，写出的数据，会先写入到内存中，再使用flush方法将内存中的数据刷到硬盘。所以，在使用字符缓冲流的时候，一定要先flush，然后再close，避免数据丢失。
转换流：用于字节数据到字符数据之间的转换。
仅有字符流InputStreamReader/OutputStreamWriter。其中，InputStreamReader需要与InputStream“套接”，OutputStreamWriter需要与OutputStream“套接”。
数据流：提供了读写Java中的基本数据类型的功能。
DataInputStream和DataOutputStream分别继承自InputStream和OutputStream，需要“套接”在InputStream和OutputStream类型的节点流之上。
对象流：用于直接将对象写入写出。
流类有ObjectInputStream和ObjectOutputStream，本身这两个方法没什么，但是其要写出的对象有要求，该对象必须实现Serializable接口，来声明其是可以序列化的。否则，不能用对象流读写。
还有一个关键字比较重要，transient，由于修饰实现了Serializable接口的类内的属性，被该修饰符修饰的属性，在以对象流的方式输出的时候，该字段会被忽略。（当持久化对象时，可能有一个特殊的对象数据成员，我们不想用serialization机制来保存它。为了在一个特定对象的一个域上关闭serialization，可以在这个域前加上关键字transient。当一个对象被序列化的时候，transient型变量的值不包括在序列化的表示中，然而非transient型的变量是被包括进去的。
使用场景举例：如果一个用户有一些敏感信息（譬如密码，银行卡号等），为了安全起见，不希望在网络操作（主要涉及到序列化操作，本地序列化缓存也适用）中被传输。这些信息对应的变量就可以被定义为transient类型。换句话说，这个字段的生命周期仅存于调用者的内存中。 ）