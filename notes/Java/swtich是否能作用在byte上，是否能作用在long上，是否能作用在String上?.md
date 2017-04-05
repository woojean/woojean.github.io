# swtich是否能作用在byte上，是否能作用在long上，是否能作用在String上?

早期的JDK中，switch（expr）中，expr可以是byte、short、char、int。从1.5版开始，Java中引入了枚举类型（enum），expr也可以是枚举，从JDK 1.7版开始，还可以是字符串（String）。长整型（long）是不可以的。