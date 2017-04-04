# 是否可以继承String类?

String 类是final类，不可以被继承。
补充：继承String本身就是一个错误的行为，对String类型最好的重用方式是关联（HAS-A）而不是继承（IS-A）。