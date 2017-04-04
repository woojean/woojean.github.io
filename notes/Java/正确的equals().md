# 正确的equals()

HashMap使用equals()判断当前的键是否与表中存在的键相同，正确的equal()方法必须同时满足下列5个条件：
1.自反性：x.equals(x)返回true；
2.对称性：如果y.equals(x)返回true，则x.equals(y)也返回true
3.传递性：如果x.equals(y)和y.equals(z)返回true，则x.equals(z)也返回true
4.一致性：如果对象中用于等价比较的信息没有改变，那么无论调用x.equals(y)多少次，返回的结果应该保持一致；
5.对任何不是null的x，x.equals(null)一定返回false；

默认的Object.equals()只是比较对象的地址，`如果要使用自己的类作为HashMap的键，必须同时重载hashCode()和equals()`，否则无法正确使用各种散列结构。

实用的hashCode()必须速度快，并且有意义：基于对象的内容生成散列码，应该更关注生成速度而不是一致性（散列码不必是独一无二的），但是通过hashCode()和equals()必须能够完全确定对象的身份。好的hashCode()应该产生分布均匀的散列码。