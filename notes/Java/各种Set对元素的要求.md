# 各种Set对元素的要求

Set：元素必须实现equals()方法（因为需要唯一），Set接口不保证元素次序；
HashSet：元素必须定义hashCode()；
TreeSet：有次序的Set，元素必须实现Comparable接口；
LinkedHashSet：使用链表维护元素（插入顺序），元素必须定义hashCode()方法；

虽然hashCode()只有在当前类元素被置于HashSet或者LinkedHashSet时才是必须的，但是对于良好的编程风格而言，应该在覆盖equals()方法时总是同时覆盖hashCode()方法。