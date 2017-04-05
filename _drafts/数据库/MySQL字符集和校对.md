# MySQL字符集和校对

`字符集`是一种从二进制编码到某类字符符号的映射，`校对`是一组用于某个字符集的排序规则。

每种字符集都可能有多种校对规则，并且都有一个默认的校对规则。

只有基于字符的值才有字符集的概念，对于其他类型的值，字符集只是一个设置，指定用哪一种字符集来做比较或其他操作。

MySQL服务器、每个数据库、每个表都有自己的字符集默认值。最靠底层的设置将影响最终创建的对象。

当服务器和客户端通信时，它们可能使用不同的字符集，这时服务器端将进行必要的翻译转换工作：
1.服务器端总是假设客户端按照character_set_client设置的字符来传输数据和SQL语句；
2.当服务器收到客户端的SQL语句时，先将其转换成字符集character_set_connection，也会使用这个设置来决定如何将数据转换成字符串；
3.当服务器端返回数据或错误信息给客户端时，会将其转换成character_set_result；
即：
```
客户端 -->SQL
  --> 从character_set_client转换成character_set_connection
    --> 处理SQL语句
      --> 从character_set_connection转换成character_set_result
        --> 查询结果
```
可以通过SET NAMES或SET CHARACTER SET来改变设置。