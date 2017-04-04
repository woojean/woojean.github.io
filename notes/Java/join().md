# join()

如果某个线程在另一个线程t上调用t.join()，此线程将被挂起，直到目标线程t结束（t.isAlive() == false）才恢复。
对join()方法的调用可以被终止：在调用线程上调用interrupt()方法（需要在try-catch中执行）。