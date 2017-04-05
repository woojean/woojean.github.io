# Java中的字符串分割

java中的split函数和js中的split函数不一样。 
Java中的我们可以利用split把字符串按照指定的分割符进行分割，然后返回字符串数组，下面是string.split的用法实例及注意事项： 
java.lang.string.split 
split 方法 
将一个字符串分割为子字符串，然后将结果作为字符串数组返回。 
stringObj.split([separator，[limit]])  
stringObj 
必选项。要被分解的 String 对象或文字,该对象不会被split方法修改。 
separator 
可选项。字符串或正则表达式对象，它标识了分隔字符串时使用的是一个还是多个字符。如果忽略该选项，返回包含整个字符串的单一元素数组。 
limit 
可选项。该值用来限制返回数组中的元素个数(也就是最多分割成几个数组元素,只有为正数时有影响) 
split 方法的结果是一个字符串数组，在 stingObj 中每个出现 separator 的位置都要进行分解。separator不作为任何数组元素的部分返回。 
示例1： 

```java
String str="Java string split test"; 
String[] strarray=str.split(" "); 
for (int i = 0; i < strarray.length; i++) 
    System.out.println(strarray[i]); 
```
将输出： 
Java 
string 
split 
test 

示例2： 
```java
String str="Java string split test"; 
String[] strarray=str.split(" ",2);//使用limit，最多分割成2个字符串 
for (int i = 0; i < strarray.length; i++) 
    System.out.println(strarray[i]); 
```
将输出： 
Java 
string split test 

示例3： 
```java
String str="192.168.0.1"; 
String[] strarray=str.split("."); 
for (int i = 0; i < strarray.length; i++) 
    System.out.println(strarray[i]); 
```
结果是什么也没输出,将split(".")改为split("\\."),将输出正确结果： 
192 
168 
0 
1 

经验分享： 
1、分隔符为“.”(无输出),“|”(不能得到正确结果)转义字符时,“*”,“+”时出错抛出异常,都必须在前面加必须得加"\\",如split(\\|); 
2、如果用"\"作为分隔,就得写成这样：String.split("\\\\"),因为在Java中是用"\\"来表示"\"的,字符串得写成这样：String Str="a\\b\\c"; 
转义字符,必须得加"\\"; 
3、如果在一个字符串中有多个分隔符,可以用"|"作为连字符,比如：String str="Java string-split#test",可以用Str.split(" |-|#")把每个字符串分开; 