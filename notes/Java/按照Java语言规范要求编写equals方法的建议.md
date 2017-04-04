# 按照Java语言规范要求编写equals方法的建议

1）显式参数命名为otherObject；

2）测试this同otherObject是否是同一对象：
	if(this==otherObject) return true;

3）测试otherObject是否为null，如果是，就返回false，这项测试是必须的。
	if(otherObject==null) return false;

4）测试this和otherObject是否属于同一个类，这项测试是“对称性规则”所要求的。
	if(getClass()!=otherObject.getClass()) return false;

5）把otherObject的类型转换成所需的类型：
	ClassName other=(ClassName)otherObject；

6）比较所有的字段，使用==比较基本类型字段，使用equals比较对象字段。如果所有字段都匹配，则返回true，否则返回false。
遵循以上规则定义一个类的equals方法，则在定义其子类的equals方法时，首先调用超类的equals方法。如果这项测试不能通过，对象也就不可能相等。如果超类字段相等，那么需要比较子类的实例字段。