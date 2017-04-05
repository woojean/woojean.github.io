# Redis底层实现-排序

SORT命令可以对列表、集合或者有序集合的值进行排序。

```
redis> RPUSH numbers 5 3 1 4 2
(integer) 5#

redis> LRANGE numbers 0 -1
1) "5"
2) "3"
3) "1"
4) "4"
5) "2"

redis> SORT numbers
1) "1"
2) "2"
3) "3"
4) "4"
5) "5"
```

## SORT<key>命令的实现
服务器执行SORT numbers命令的详细步骤如下：
1.创建一个和numbers列表长度相同的数组，该数组的每个项都是一个redisSortObject结构；
2.遍历数组，将各个数组项的obj指针分别指向numbers列表的各个项，构成obj指针和列表项之间的一对一关系；
3.遍历数组，将各个数组项的obj指针分别指向numbers列表的各个项，构成obj指针和列表项之间的一对一关系；
4.根据数组项u.score属性的值，对数组进行数字值排序，排序后的数组项按u.score属性的值从小到大排列；
5.遍历数组，将各个数组项的obj指针所指向的列表项作为排序结果返回给客户端，程序首先访问数组的索引0，返回u.score值为1.0的列表项"1"；然后访问数组的索引1，返回u.score值为2.0的列表项"2"；最后访问数组的索引2，返回u.score值为3.0的列表项"3"。

## ALPHA选项的实现
通过使用ALPHA选项，SORT命令可以对包含字符串值的键进行排序：
```
SORT <key> ALPHA
```
详略。

## ASC选项和DESC选项的实现
在默认情况下，SORT命令执行升序排序，排序后的结果按值的大小从小到大排列，以下两个命令是完全等价的：
```
SORT <key>
SORT <key> ASC
```

## BY选项的实现
在默认情况下，SORT命令使用被排序键包含的元素作为排序的权重，元素本身决定了元素在排序之后所处的位置。通过使用BY选项，SORT命令可以指定某些字符串键，或者某个哈希键所包含的某些域（field）来作为元素的权重，对一个键进行排序。
```
redis> MSET apple-price 8 banana-price 5.5 cherry-price 7
OK

redis> SORT fruits BY *-price
1) "banana"
2) "cherry"
3) "apple"
```

## 带有ALPHA选项的BY选项的实现
略。

## LIMIT选项的实现
通过LIMIT选项，可以让SORT命令只返回其中一部分已排序的元素。
```
redis> SORT alphabet ALPHA LIMIT 0 4
```

## STORE选项的实现
默认情况下，SORT命令只向客户端返回排序结果，而不保存排序结果。通过使用STORE选项，可以将排序结果保存在指定的键里面，并在有需要时重用这个排序结果。
```
redis> SORT students ALPHA STORE sorted_students
(integer) 3

redis> LRANGE sorted_students 0-1
1) "jack"
2) "peter"
3) "tom"
```

## 多个选项的执行顺序
一个SORT命令的执行过程可以分为以下几步：
1.排序：在这一步，命令会使用ALPHA、ASC或DESC、BY这几个选项，对输入键进行排序，并得到一个排序结果集。
2.限制排序结果集的长度：在这一步，命令会使用LIMIT选项，对排序结果集的长度进行限制，只有LIMIT选项指定的那部分元素会被保留在排序结果集中。
3.获取外部键：在这一步，命令会使用GET选项，根据排序结果集中的元素，以及GET选项指定的模式，查找并获取指定键的值，并用这些值来作为新的排序结果集。
4.保存排序结果集：在这一步，命令会使用STORE选项，将排序结果集保存到指定的键上面去。
5.向客户端返回排序结果集：在最后这一步，命令遍历排序结果集，并依次向客户端返回排序结果集中的元素。

详略。