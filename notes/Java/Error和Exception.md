# Error和Exception

所有异常都继承自Throwable，共有两种类型：Error和Exception。Error用于表示编译时错误和系统错误，通常不需要关心。

运行时异常的类型有很多，它们会被Java虚拟机自动抛出，所以无需在异常声明中罗列（这种异常属于编程错误）。如果RuntimeException没有被捕获而最终到达main()，那么在程序退出前将调用异常的printStackTrace()方法。