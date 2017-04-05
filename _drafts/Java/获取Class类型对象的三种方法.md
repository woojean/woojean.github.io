# 获取Class类型对象的三种方法

1）调用Object类中的getClass方法；

2）使用Class类的静态方法forName获得与字符串对应的Class对象：
	String className=”Manager”;
	Class c1=Class.forName(className);

3）如果T是一个Java类型，那么T.class就代表了匹配的类对象：
	Class cl1=Manager.class;
	Class cl2=int.class;
	Class cl3=Double[].class;
注意的是，Class对象实际上描述的只是类型，而这类型未必是类。