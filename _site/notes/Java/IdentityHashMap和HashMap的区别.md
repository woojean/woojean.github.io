# IdentityHashMap和HashMap的区别

IdentityHashMap是Map接口的实现。不同于HashMap的，这里采用参考平等。
•在HashMap中如果两个元素是相等的，则key1.equals(key2)
•在IdentityHashMap中如果两个元素是相等的，则key1 == key2