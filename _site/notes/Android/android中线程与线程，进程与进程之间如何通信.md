# android中线程与线程，进程与进程之间如何通信

1.一个Android 程序开始运行时，会单独启动一个Process。
   默认情况下，所有这个程序中的Activity或者Service都会跑在这个Process。
   默认情况下，一个Android程序也只有一个Process，但一个Process下却可以有许多个Thread。
2.一个Android 程序开始运行时，就有一个主线程Main Thread被创建。该线程主要负责UI界面的显示、更新和控件交互，所以又叫UI Thread。
   一个Android程序创建之初，一个Process呈现的是单线程模型--即Main Thread，所有的任务都在一个线程中运行。所以，Main Thread所调用的每一个函数，其耗时应该越短越好。而对于比较费时的工作，应该设法交给子线程去做，以避免阻塞主线程（主线程被阻塞，会导致程序假死现象）。 
3.Android单线程模型：Android UI操作并不是线程安全的并且这些操作必须在UI线程中执行。如果在子线程中直接修改UI，会导致异常。