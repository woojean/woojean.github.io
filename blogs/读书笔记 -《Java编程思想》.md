《Java编程思想》读书笔记

# 第1章 对象导论
（略）

# 第2章 一切都是对象
在Java中，（几乎）一切都是对象，用来表示对象的标识符实际上是对对象的一个引用（区别于C++，C++需要兼容C，因此实际上是一种杂合性语言）。
```
String s;  // 创建了一个String引用，而非一个String对象
```

## 基本类型
使用new创建的对象存储在堆中，所以使用new创建小的、简单变量效率不高。对于这些小的、简单的基本类型，Java采取与C和C++相同的方式，即不使用new来创建，而是创建一个`并非引用`的`自动变量`，这个变量直接将值存储在栈中，因此更加高效。

Java每种基本类型所占用的存储空间大小具有不可变性，即与平台无关。

共有如下基本类型：boolean（大小未指定，仅定义为能够取字面值true或false）、char（16位）、byte（8位）、short（16位）、int（32位）、long（64位）、float（32位）、double（64位）、void（未指定）；所有数值类型都有符号。

```
// 计算字符串的字节数
int storage(String s){
    return s.length() * 2;
}
```

每种基本类型都有一个对应的`包装器类型`，使得可以在`堆`中创建一个非基本类型的对象用来表示对应的基本类型。基本类型和对应的包装器类型之间可以自动地互相转换：
```
Character ch = 'x';
char c = ch;
```

用于高精度计算的BigInteger和BigDecimal没有对应的基本类型。

Java确保数组会被初始化（对象数组，即引用数组初始化为null，基本类型数组初始化为零），而且不会被越界访问（通过额外的内存数据及运行时下标检查实现）。

## 销毁对象
作用域：由花括号定义，决定了在其内部定义的变量名的`可见性`和`生命周期`。

Java不支持在子作用域中定义与外部作用域同名的变量来`屏蔽`外部作用域的变量，所以如下的代码将会报错：
```
{
  int x = 1;
  {
    int x = 2; // Illegal
  }
}
```

Java垃圾回收器会监视用new创建的所有对象，并辨别出那些不再被引用的对象，随后释放这些对象的内存空间。如下：
```
{
  String s = new String('abc');
}
// s引用在这里已被销毁，但其之前所引用的对象在内存中仍然存在，直到被垃圾回收器回收
```

## 类
如果类的属性是基本数据类型，且没有被初始化，Java将会将其置为对应基本类型的默认值。（当变量作为类的成员使用时，Java才确保其初始化，对于没有被初始化的变量，在编译时会报错）

Java中的类无需前向声明就可以使用。

java.lang会被自动导入到每一个Java文件中，因此无需显式使用import语句导入。（其中包括System类，所以可以直接调用System.out.println(...)，out是一个静态PrintStream对象）

类文件中必须存在某个类与该文件同名。

## javadoc
javadoc基于Java编译器提供的功能来提取Java注释（查找特殊注释的标签）并生成文档，属于JDK安装的一部分。
（详略）


# 第3章 操作符
基本类型可以直接使用==和!=来比较是否相等，但是对于对象，==和!=实际比较的是对象的引用。如果想要比较对象的内容是否相等，应该使用equals()方法（equals的默认行为是比较引用，自定义的类通常需要覆盖equals方法）。

在Java中，不可以将一个非布尔值当做布尔值在逻辑表达式中使用。

## 直接常量
对于使用直接常量存在模棱两可的情况，可以添加一些标识来对编译器加以指导，如0x2f、200L、1F、2D等，详略。

Java不支持操作符重载（不同于C++、C#）。

## 类型转换
对于扩展类型转换（类型提升），编译器会自动进行，但是对于窄化类型转换（有数据丢失），编译器会强制要求显式进行类型转换。通常表达式中出现的最大的数据类型决定了表达式最终结果的数据类型。

布尔类型不能进行任何类型转换处理。类数据类型也不允许进行类型转换。对象可以在其所属类型的类族之间进行类型转换。

除了布尔类型以外，任何一种基本类型都可以通过类型转换变为其他基本类型。

对char、byte、short的任何算术运算，都会获得一个int结果（所以如果要赋值给原来类型的变量，需要进行窄化转换）。


# 第4章 控制执行流程
Java中唯一使用了逗号操作符（而不是逗号分隔符）的地方就是for循环的控制表达式，在控制表达式的初始化和步进控制部分可以使用一系列由逗号分隔的语句，这些语句均会独立执行：
```
for(int i = 1, j = i + 10; i < 5; j ++, j = i * 2){
    //...
}
```

## Foreach
Foreach是一种更加简洁的应用于数组和容器的迭代语句，无需借助int变量就可以自动访问序列的每一项：
```
float f[] = new float[10];
// ... 
for(float x:f){
  // ...
}
```

## goto及标签
goto在Java中是保留字，但是并未使用，取而代之的是用一种标签语法结合break和continue来实现跳转：
```
label1:
outer-iteration{  // 比如for，while循环
  inner-iteration{
    ...
    break;  // 中断inner-iteration 
    ...
    continue;  // 跳过inner-iteration的当前循环
    ...
    continue label1; // 中断inner-iteration和outer-iteration,跳转到label1处，继续迭代过程
    ...
    break label;  // 中断inner-iteration和outer-iteration,跳转到label1处，不再进入迭代
  }
}
```
标签必须直接定义在循环语句之前。


## switch
Java中供switch进行判断的值必须是整数值，或者是生成整数值的表达式（不同于PHP、js可以是字符串）。如果想对非整数值进行switch调用，可以借助enum来实现。


# 第5章 初始化与清理

## 构造函数
```
class Rock{
  Rock(){  // 构造函数，没有返回值，没有访问控制，首字母大写
    System.out.print("Rock");
  }
}
```

## 方法重载
每个重载的方法（含构造方法）都必须有一个独一无二的参数类型列表（类型、数量、顺序）。不能根据方法的返回值来区分重载方法。

## 默认构造器
默认构造器即无参构造器，其作用是创建一个默认对象，如果类中没有写构造器，则编译器会自动创建一个默认构造函数。如果已经定义了一个构造器（无论是否有参数），编译器就不会自动创建默认构造器。

## this
通过对象调用类实例方法时，编译器会自动把指向当前对象的引用作为第一个参数传递给实例方法。在实例方法内部可以使用专门的this关键字来访问这个参数。
在实例方法内部调用同一个类的另一个方法时不必使用this（当然也可以写），当前方法中的this引用会自动应用于同一类中的其他方法。

##  static
static方法就是没有this的方法，在static方法内部不能调用非静态方法（反过来可以）。
可以在没有创建任何对象的前提下仅仅通过类本身来调用static方法。

## finalize()
垃圾回收器只知道释放那些通过new分配的内存，Java支持在类中定义finalize()方法来自定义一些对象回收行为。一旦垃圾回收器准备释放对象占用的存储空间，将会首先调用其finalize()方法，`并且在下一次垃圾回收动作发生时才会真正回收对象占用的内存`。

`finalize()不等于C++中的析构函数`，因为其触发依赖于垃圾回收器，而垃圾回收器的执行取决于当前的内存消耗状况(而在C++中对象使用完成后一定会被销毁）。finalize()应该仅用于回收那些通过创建对象以外的方式分配的存储空间（比如native调用，即在Java中调用非Java代码）。

finalize()的另一个用法是用来对终结条件进行验证，即在finalize()中对对象被回收时应该处于的正确状态的判断。

## 垃圾回收器的工作方式
Java中除了基本类型外的所有对象都在堆上分配，Java虚拟机的工作方式使得Java从堆分配空间的速度可以和其他语言从栈上分配空间的速度相媲美。
垃圾回收器在回收空间的同时会使堆中的对象紧凑排列，因此堆指针可以很容易地移动到尚未被分配的内存区域。

常见的垃圾回收方式：
1.引用计数：简单、效率低、循环引用难处理；
2.停止-复制：需要暂停程序（所以不属于回台回收模式），需要有两个堆空间；
3.标记-清扫：如果希望得到连续的空间，需要重新整理剩下的对象；
实际Java虚拟机会进行监视，并根据当前的碎片情况采取不同的垃圾回收方式。

## JIT
JIT即Just In Time，即时编译。把程序全部或者部分翻译成本地机器码（而非JVM字节码），以此提高程序运行速度。

## 成员初始化
Java类的所有成员变量都会在使用前得到初始化（如果定义时不初始化，基本类型成员会被置为零，对象成员会被置为null，且这些行为发生在构造函数被调用之前），对于方法中的局部变量，如果未被初始化则会发生编译时错误。

可以在定义类成员时通过调用类方法进行初始化，但是这种情况下对成员的定义顺序有要求：
```
public class MethodInit{
  int j = g(i); // 错误，i未定义
  int i= f();
  int f(){ return 1;}
  int g(int n){
    return n * 10;
  }
}
```

对象创建过程：
1.`构造函数实际上是静态方法`，当首次创建类的对象，或者调用类的静态方法、静态属性时，Java解释器会查找类的.class文件，并载入，这时有关静态初始化的所有动作都会执行（未赋初值的成员会被置为默认值）。静态初始化只在.class文件首次加载的时候执行一次，无论创建多少个对象，静态数据都只占用一份存储区域。
2.当通过new创建类的对象时，首先会在堆上为对象分配足够的存储空间（这块空间会被置0），然后执行所有类成员变量的初始化。
3.执行构造器。

`Java中，static关键字不能应用于局部变量`。

## 静态块
可以将多个静态初始化动作组织成一个静态块，与其他静态初始化动作一样，这段代码仅会执行一次（当首次生成这个类的一个对象，或者首次访问属于该类的静态数据成员时）。
```
public class Spoon{
  static int i;
  static {  // 静态块
    i = 1;
  }
}
```
也支持实例初始化块，即把static去掉。

## 数组初始化
Java中，定义数组时，不允许指定数组的大小。

数组可以在定义的同时初始化：
```
int[] a1 = {1,2,3,4,5};
int[] a2 = new int[rand.nextInt(20)]; // [0,0,0,0...]
```
## 可变参数列表
```
static void printArray(Object... args){
  for(Object obj:args){
    System.out.print(obj + " ");
  }
}

//调用
printArray(1,2,3,4);
printArray(new A(), new A(), new A());
printArray((Object[])new Integer[]{1,2,3,4});
printArray();  // 0个参数
```

## 枚举
```
public enum Spiciness{
  NOT,MILD,MEDIUM,HOT,FLAMING
}

// 使用
Spiciness howHot = Spiciness.MEDIUM;
```

# 第6章 访问权限控制
Java中访问权限控制的等级从最大权限到最小权限依次为：public、protected、包访问权限（没有关键字）、private。
注意：protected权限比包访问权限大。

## 编译单元
每一个后缀名为.java的Java源代码文件被称为一个编译单元，每个编译单元内最多只能有一个public类（可以没有，此时文件可以随意命名），且该public类的名称必须与文件的名称相同（包括大小写）。
当编译一个.java文件时，在.java文件中的每个类都会有一个后缀为.class的输出文件。Java可执行程序由一组打包并压缩为Java文档文件（jar）的.class组成，Java解释器负责这些文件的查找、装载和解释。
类库实际就是一组由package组织起来的类文件。

## Java解释器的运行过程
解释器获取包的名称，并将每个点号（.）替换为反斜杠（\），由此得到一个相对路径名（所以包的名称必须与其目录结构相对应）。然后根据CLASSPATH的配置（包含一个或多个目录），得到一个或多个绝对路径名，解释器就在这些绝对路径中查找与要创建的类名称相关的.class文件。（解释器还会去查找某些涉及Java解释器所在位置的标准目录）。
对于jar文件，在CLASSPATH中必须配置实际的jar文件的位置。一个示例：
```
CLASSPATH=.:D:\JAVA\LIB;C:\flavors\grape.jar
```
注意`.目录`被包含在内。

## 静态导入
可以不同过调用包名，直接使用包里的静态方法：
```
import static java.lang.System.out;
public static void main(String args[]){
  out.println("输出内容");
}
```

## 包访问权限
如果不提供任何访问权限修饰词，则默认为包访问权限。
`处于相同目录`且`都没有设定任何包名`的文件将会被看作是隶属于该目录的默认包之中。

## 类的访问权限
类的访问权限不可以是private或者protected（内部类可以）。

如果没有为类指定访问权限，它就会默认地得到包访问权限，意味着该类的对象可以由包内任何其他类来创建（但是在包外不行）。但是，如果该类的某个static成员是public的话，则包外的类仍然可以调用该static成员（尽管不能new该类的对象）。


## 第7章 复用类
当创建一个类时，如果没有明确指出要从其他类中继承，则会隐式地从Java的标准根类Object继承。

如果想在子类的构造器中调用父类的带参数的构造函数，必须在子类构造函数中的一开始就调用（说明在创建类对象时，父类的构造函数先执行）：
```
class Game{
  Game(int i){
    // ...
  }
}

class BoardGame extends Game{
  BoardGame(int i){
    super(i);   // 调用父类构造函数
    // ...
  }
}
```

## @Override
在子类中定义与父类同名的方法，实际效果是新增了一个重载方法，而非像C++中那样屏蔽了父类的方法。如果需要像C++中那样覆盖父类中的方法，则需要使用与父类中方法相同的方法签名。可以使用@Override注解来注明想要覆盖（而非重载）父类的方法，如果实际效果是重载而非覆盖（方法签名不一致），编译器会生成错误信息。

## final
对于基本类型，final使其数值恒定不变（可以使用字面值常量在编译时初始化，也可以使用运行时函数初始化，初始化后值不可变）。对于对象引用，final使其引用恒定不变（而不是引用所指向的对象不变）。

Java允许生成`空白final`属性，即声明为final，但不给定初值（留待构造函数进行初始化，实现根据对象不同而值不同）。

使用final修饰方法，将使得方法不能被子类覆盖（仍然可继承）。所有private方法都隐式地指定为final（如果在子类中定义了与父类中private方法同名的public方法，实际效果是在子类中新增了方法）。

将类定义为final意味着该类无法被继承。final类中的所有方法都被隐式地指定为final。


# 第8章 多态
多态通过分离做什么和怎么做，从另一个角度将接口和实现分离开来。

## 向上转型
对象既可以作为它自己本身的类型使用，也可以作为它的基类型使用。

## 方法绑定
方法绑定即将一个方法调用同一个方法主体关联起来。在程序执行之前进行绑定，称为`前期绑定`。在运行时根据对象的类型进行绑定称为`后期绑定`（或者动态绑定、运行时绑定）。后期绑定基于对象中的类型信息实现。

Java中除了static方法和final方法，其他所有的方法都是后期绑定。

类属性不具有多态性，子类中定义的同名属性会覆盖父类中对应的属性。

## 构造器与多态
构造器实际是静态方法，因此不支持多态。
基类的构造器总是在子类的构造过程中被调用（因为子类不能访问基类的private成员，无法对其进行初始化），并且按照继承层次向上链接，以使每个基类的构造器都能得到调用。

对象初始化的过程：
1.将分配给对象的存储空间初始化为0；
2.调用基类构造器；
3.按照声明的顺序调用父类成员的初始化方法；
4.调用子类的构造函数主体；

在编写构造器时应该用尽可能简单的方法使对象进入正常状态，在构造器内唯一能够安全调用的方法是基类中的final方法。（因为普通方法都有多态行为，而在构造器中对象尚不完整）

## 协变返回类型
在子类中被覆盖的方法可以返回基类方法的返回类型的子类类型。


# 第9章 接口

## 抽象类和抽象方法

抽象类不能实例化。

抽象方法仅有声明而没有方法体：
```
abstract void f();
```

如果一个类包含一个或多个抽象方法，那么该类就必须为抽象类。

## 接口
接口实际是一个完全抽象的类，不提供任何具体实现（抽象类可以有部分具体实现）。

接口也可以包含字段，这些字段隐式地是static final的。

接口中的方法都是public的。

一个类可以实现多个接口。

接口可以extends其他接口。

接口可以嵌套在类或其他接口中，且可以被定义为private。


# 第10章 内部类
内部类可以访问其外围类的方法和字段，就像自己拥有它们似得（即无需使用任何前缀）。

使用内部类的最主要原因是：每个内部类都能够独立地实现一个接口，所以无论外围类是否已经实现了某个接口，对内部类都没有影响（内部类使得多重继承的解决方案变得完整）。

## .this
如果想要在内部类中生成外部类对象的引用，需要在外部类名称上执行.this：
```
public class DotThis{
  void f(){
    System.out.println('Dot this !');
  }
    
  public class Inner{
    public DotThis outer(){
      return DotThis.this;  // 返回外部类对象的引用
    }
  }

  public Inner inner(){
    return new Inner();
  }

  public static void main(String[] args){
    DotThis dt = new DotThis();
    DotThis.Inner dti = dt.inner();
    dti.outer().f();
  }
}
```

## .new
当想要创建某对象的外部类对象时，需要在该对象上执行.new操作：
```
public class DotNew{
  public class Inner{
    // ...
  }

  public static void main(String[] args){
    DotNew dn = new DotNew();
    DotNew.Inner dni = dn.new Inner();  // 不能使用dn.new DotNew.Inner()
  }
}
```
在拥有外部类对象之前不能创建内部类对象（因为内部类对象会连接到创建它的外部类对象上），但是如果创建的是静态内部类，就不需要对外部类对象的引用。

## 内部类与向上转型
将内部类定义为private，同时实现某个接口，并在外部类的公共方法中返回该内部类的对象（返回类型为接口），通过这种方式可以完全阻止任何依赖于类型的编码，并且完全隐藏了实现细节（因为不能访问内部类的名字，所以甚至不能向下转型）。

## 局部内部类
可以在一个方法里或者任意的作用域内定义内部类，这样创建的内部类属于定义它的作用域，在作用域外不可见。

局部内部类不能有访问控制符，但是它可以访问当前代码块内的常量以及外围类的所有成员。


## 匿名内部类
```
public class Parcel{
  public Contents contents(){
    return new Contents(){  // 创建了一个继承自Contents的匿名类的对象（自动向上转型）
      private int i = 1;
      public int value(){
        return i;
      }
    };  // 分号（表达式结束）
  }

  public static void main(String[] args){
    Parcel p = new Parcel();
    Contents c = p.contents();
  }
}
```

如果在匿名内部类中引用了其外部定义的对象，编译器要求改对象必须是final的：
```
public class Parcel{
  public Destination destination(final String dest){  // 因为这个参数会在匿名内部类中使用，所以必须为final（此处如果传递的参数仅供Destination类做构造函数参数使用，则无需为final）
    return new Destination(){  
      private String label = dest;
      public String readLabel(){
        return label;
      }
    };  // 分号（表达式结束）
  }

  public static void main(String[] args){
    Parcel p = new Parcel();
    Destination d = p.destination("test");
  }
}
```

选择使用局部内部类而非匿名内部类的常见原因有：
1.需要一个已命名的构造器；
2.需要重载构造器；
3.需要不止一个该内部类的对象；


## 嵌套类
嵌套类即静态内部类，它与其外部类对象之间没有联系。
普通内部类不能有static数据，但是嵌套类可以有。
可以在接口中定义嵌套类。

## 闭包与回调
闭包是一个可调用的对象，它记录了一些来自于创建它的作用域的信息（内部类是面向对象的闭包）。

## 内部类的继承与覆盖
略

## 内部类标识符
内部类文件的名称为外部类名称+$+内部类名称：OuterClass$InnerClass.class
对于匿名内部类编译器会简单地产生一个数字作为其标识符。


# 第11章 持有对象
如果使用ArrayList来添加属于不同类的对象，编译会通过，但是会给出警告。且在从ArrayList中取出元素时，必须进行向下转换，因为取出来的元素是Object类型。
使用泛型方式ArrayList<T>，添加非指定类型对象时将会编译报错。且取出的元素无需进行向下转型。


## 迭代器
Java中的Iterator只能单向移动，可执行的操作包括：
1.使用iterator()要求容器返回一个Iterator对象；
2.使用next()获得序列中的下一个元素；
3.使用hasNext()检查序列中是否还有元素；
4.使用remove()将最近返回的元素删除（即next()返回的最后一个元素，意味着使用remove()之前必须先调用next()）；

## ListIterator
ListIterator继承自Iterator，只能用于各种List的访问，可以双向移动，可以返回前一个及后一个元素的索引，并且可以使用set()方法设置最近返回的元素。

## Java容器类库
总体上所有数据结构实现了`2个根接口`：Collection、Map，独立于这2个根接口之外还有`3个辅助根接口`：Iterator、Comparable、Comparator。

Collection是所有列表类数据结构的接口，Map是所有映射类数据结构的接口，Iterator用于遍历一个序列，Collection可以生成这样的序列，而Map接口可以生成Collection（entrySet()、values()）。：
所有实现Collection的数据结构都支持生成一个ListIterator接口，该接口是Iterator的子类。
Map -----生成-----> Collection -----生成-----> Iterator
                                                 ↑
                       ...      ---生成-----> ListIterator

Collection族的继承树：
Collection接口
    - List接口
       - ArrayList（标记了RandomAccess接口）
       -------------------- LinkedList（同时实现了List、Queue接口）
    - Set接口                 |
       - HashSet             |
         - LinkedHashSet     |
       - TreeSet             |
    - Queue接口              |
       - PriorityQueue      |
       ----------------------
除了TreeSet，其他Set都拥有与Collection完全一样的接口。
以上未包括Queue的concurrent实现。
新版本容器类库没有Stack，可以用LinkedList模拟（也没有Queue类）。

Map族的继承树：
Map接口
    - HashMap
        - LinkedHashMap
    - TreeMap

Comparable与Comparator可以互相生成。

不应该再使用过时的Vector、Hashtable、Stack等容器类。


# 第12章 通过异常处理错误
异常处理把在正常执行过程中做什么事的代码和出了问题怎么办的代码相分离。

所有标准异常类都有两个构造器：一个默认构造器和一个接收字符串作为参数的构造器：
```
throw new NullPointerException('x == null');
```

能够抛出任意类型的Throwable的对象，它是异常类型的根类。异常将在一个恰当的异常处理程序中得到解决，它的位置可能离异常被抛出的地方很远，也可能会跨越方法调用栈的许多层次。

## 捕获异常
异常处理程序必须紧跟在try块之后，当异常被抛出时异常处理机制将负责搜寻参数与异常类型相匹配的第一个处理程序（只有匹配的catch子句才能得到执行）：
```
try{
    // ...
}
catch(Type1 e){
    // ...
}
catch(Type2 e){
    // ...
}
catch(Type3 e){
    // ...
}
finall{
    // 无论try块中是否抛出异常，这里都将执行，即使try中正常执行了return
}
```
如果在try块中执行System.exit(0);finally中的代码不会被执行。
finally块的语句在try或catch中的return语句执行之后返回之前执行且finally里的修改语句可能影响也可能不影响try或catch中return已经确定的返回值（取决于是值还是引用），若finally里也有return语句则覆盖try或catch中的return语句直接返回。
在finall中执行return，异常将丢失（极其糟糕）。

## 自定义异常
对异常类来说，最重要的就是类名。
```
class SimpleException extends Exception{}

public class Demo{
  public void f() throws SimpleException{
    throw new SimpleException();
  }

  public static void main(String[] args){
    Demo demo = new Demo();
    try{
        demo.f();
    }
    catch(SimpleException e){
        System.out.println('exception!');
    }
  }
}
```

## 异常声明
异常声明属于方法声明的一部分，描述了方法可能抛出的异常类型的列表：
```
void f() throws T1Exception,T2Exception{
    // ...
}
```
如果没有异常声明，就表示该方法不会抛出任何异常（除了RuntimeException，它们可以在没有异常声明的情况下被抛出）。
如果方法里的代码产生了异常却没有进行处理，编译器会提示：要么处理这个异常，要么抛出这个异常。

## 捕获所有异常
```
catch(Exception e){
    // ...
}
``` 

## 重新抛出异常
```
catch(Exception e){
    // ...
    throw e;
}
```

## Java标准异常
所有异常都继承自Throwable，共有两种类型：Error和Exception。Error用于表示编译时错误和系统错误，通常不需要关心。

运行时异常的类型有很多，它们会被Java虚拟机自动抛出，所以无需在异常声明中罗列（这种异常属于编程错误）。如果RuntimeException没有被捕获而最终到达main()，那么在程序退出前将调用异常的printStackTrace()方法。

## 异常的限制
当覆盖方法的时候，只能抛出在基类方法的异常声明中列出的异常。

异常限制对构造器不起作用：子类构造器可以抛出基类构造器中没有的异常，但是子类构造器的异常声明必须包含基类构造器的所有异常声明。

派生类构造器不能捕获基类构造器抛出的异常。


# 第13章 字符串
 
## String对象不可变
String对象是不可变的，String类中每一个看起来会修改String值的方法实际上都是创建了一个全新的String对象，并返回指向新的对象的引用。

Java不允许程序员重载操作符，但是自身重载了两个用于连接String对象的操作符`=`和`+=`，当使用这两个操作符连接字符串时，编译器会自动地进行优化，最终使用StringBuilder的append()方法来构建新的字符串对象。但是当在循环体中使用+连接字符串时，实际优化出来的代码会在循环体内创建StringBuilder，意味着每循环一次就会创建一个StringBuilder对象。所以，当为一个类编写toString()方法时，如果字符串的操作比较简单，可以信赖编译器，但是如果要在toString()方法中使用循环，则最好自己创建一个StringBuilder对象。

与StringBuilder对应的线程安全的工具类是StringBuffer。

## 打印对象内存地址
如果想打印对象的内存地址，应该调用Object的toString()方法（即调用super.toString()），而不应该使用this，否则可能发生递归调用：因为编译器在遇到`字符串+对象`的时候，会调用对象的toString()方法：
```
public class InfiniteRecursion{
  public String toString(){
    // 会发生自动类型转换，进而发生递归，最终产生异常
    return "InfiniteRecursion address: " + this + "\n";
  }
}
```

## String类的API
（略）

## 字符串格式化
System.out.println("Row 1:[" + x + " " + y + "]");  // old way 
System.out.format("Row 1:[%d %f]\n", x, y);
System.out.printf("Row 1:[%d %f]\n", x, y);

// 使用java.util.Formatter类
Formatter f = new Formatter(System.out);
f.format("Row 1:[%d %f]\n", x, y);
（详略）

// 使用String.format()对象
String s = String.format("Row 1:[%d %f]\n", x, y);
实际在String.format()内部也是通过创建Formatter类对象来实现格式化。

## 正则表达式
（略）


# 第14章 类型信息
Java主要有两种在运行时识别对象和类信息的方式：RTTI和反射。

## RTTI
使用RTTI可以查询某个基类引用所指向的对象的确切类型。每当编译一个新类，就会产生一个Class对象（属于Class类，被保存在类的.class文件中）。所有类都是在对其第一次使用时（第一次引用类的静态成员，构造函数也是静态成员）动态加载到JVM中。类加载器会首先检查当前被引用类的Class对象是否已经被加载，如果没有，则会根据类名查找对应的.class文件（其中包含了Class对象），并加载Class对象。`一旦某个类的Class对象被载入内存，它就被用来创建这个类的所有对象`。

可以调用`Class.forName()`获取一个指定类名的类的Class对象的引用：
```
Class.forName("ClassName");
```
如果该类还没有被加载过，就加载它，在加载的过程中该类的static子句会被执行。

如果当前已经有了一个目标类的对象，则可以通过调用`getClass()`方法来获取Class对象的引用，这个方法定义在Object中。

Class类的API：
getName();           // 类的完全限定名（含包名）
getSimpleName();     // 简单类名
getCanonicalName();  // 和getName()一样
getInterfaces();     
getSuperclass();     // 返回直接基类
newInstance();       // 使用newInstance()来创建对象的类必须带有默认的构造器

## 类字面常量
可以不使用forName()方法，而直接使用类字面常量来获取对Class对象的引用：
```
ClassName.class;
```
使用这种方式的好处是在编译时就能够受到检查，因此不需要置于try语句块中。类字面常量还可以应用于接口、数组以及基本数据类型。

对于基本数据类型的包装器类，有一个TYPE字段，指向对应的基本数据类型的Class对象。即：
int.class等价于Integer.TYPE

为了使用类而做的准备工作实际包含三个步骤：
1.加载：类加载器查找字节码，并从字节码中创建一个Class对象；
2.链接：验证类中的字节码，为静态域分配存储空间，解析这个类对其他类的所有引用；
3.初始化：初始化超类，执行静态初始化器和静态初始化块；

注意：当使用.class来创建对Class对象的引用时，不会自动地初始化该Class对象，而Class.forName()立即就进行了初始化。

如果一个static final域被用“字面值常量”初始化(即编译期常量)，那么这个域无需对类进行初始化（意味着执行静态块）就可以被读取。否则仍然需要初始化。
对于非final的static域，总是要求在它被读取之前先进行链接和初始化。

## 泛化的Class引用
普通的类引用可以被重新赋值为指向任何其他的Class对象，而泛型类引用只能赋值为指向其声明的类型，所以通过使用泛型语法可以让编译器强制执行额外的类型检查。
```
Class<Integer> genericIntClass = int.class;
genericIntClass = Integer.class; // 与int.class一样
genericIntClass = double.class;  // 非法
```

注意泛型对子类型的限制，比如虽然Integer继承自Number，但是如下语句无法执行：
```
Class<Number> genericNumberClass = int.class;
```
因为Integer Class对象不是Number Class对象的子类。

泛型支持通配符：
```
Class<?> intClass = int.class;  // 与非泛型等效，但是更明确
intClass = double.class;
```

指定为特定类的子类：
```
Class<? extends Number> bounded = int.class;
bounded = double.class;  // OK
```

指定为特定类的超类：
```
Class<FancyToy> ftClass = FancyToy.class;
Class<? super FancyToy> up = ftClass.getSuperclass();
Object obj = up.newInstance();  // 将返回Object（因为无法确定是哪一个基类）
```

可以使用cast()方法进行引用转型：
```
class Building {}
class House extends Building {}
...
Building b = new House();
Class<House> houseType = House.class;
House h = houseType.cast(b);
h = (House)b;  // 效果一样
```

## instanceof
```
if( x instanceof Dog){  // 只能与类型做比较，而不能与Class对象做比较
  ((Dog)x).bark();
}
```

## Class.isInstance()
```
String s = new String("abcd");
System.out.println(String.class.isInstance(s)); // true
```

使用instanceof或Class.isInstance()进行判断时，会考虑类的继承关系，而使用Class对象进行比较时，没有考虑继承关系（父类型不等于子类型）。


## 反射
当通过反射与一个未知类型的对象打交道时，JVM只是简单地检查这个对象，看它属于哪个类（和RTTI一样），然后加载那个类的Class对象（所以JVM必须能够获取该类的.class文件）。反射和RTTI之间真正的区别在于：对于RTTI，编译器在编译时打开和检查.class文件，而对于反射机制，.class文件在编译时是不可获取的，所以在运行时打开和检查.class文件。

```
import java.lang.reflect.*;

Class<?> c = Class.forName("...");
Method[] methods = c.getMethods();
Constructor[] ctors = c.getConstructors();
...
```

## 空对象
空对象是用来替代null的一种解决方案，空对象可以响应实际对象可以响应的所有消息（仍需要某种方式去测试其是否为空）。

## 绕过访问权限的操作
通过反射可以到达并调用所有方法，包括private方法（在Method对象上setAccessible(true)）。

对于编译后发布的代码，可以执行：
```
javap -private C
```
列出包括private成员在内的所有成员（包括私有内部类）。
不过，通过反射修改final域实际是无效的（也不会抛出异常）。


# 第15章 泛型
泛型实现了`类型的参数化`。
```
public class Holder<T>{
  private T a;
  public Holder(T a){
    this.a = a;
  }

  public void set(T a){
    this.a = a;
  }

  public T get(){
    return a;
  }

  public static void main(String[] args){
    Holder<Automobile> h = new Holder<Automobile>(new Automobile());
    Automobile a = h.get();  // 无需cast，取出来的类型就是正确的
    h.set(1); // error
  }
}
```

## 泛型接口
```
public interface Generator<T>{
  T next();
}
```
（略）

## 泛型方法
可以在类中定义参数化方法，且这个方法所在的类可以是泛型类，也可以不是泛型类。
```
public class GenericMethods{
  public <T> void f(T x){   // 定义泛型方法时，泛型参数列表置于返回类型之前
    System.out.println(x.getClass().getName());
  }

  public static void main(String[] args){
    GenericMethods gm = new GenericMethods();
    gm.f("");   // java.lang
    gm.f(1);
    gm.f(1.0);
  }
}
```

static方法无法访问其所在泛型类的类型参数，所以如果static方法需要使用泛型能力，必须将其定义为泛型方法。

返回类型为泛型参数的方法，实际将返回确切的类型。

## 类型参数推断
当使用泛型类时，必须在创建对象的时候指定类型参数的值，而使用泛型方法时通常不必指明参数类型，编译器会自动找出具体的类型。所以，可以像调用普通方法一样调用泛型方法（就好像方法被多次重载过）。如果用基本类型调用泛型方法，自动打包机制会介入。

如果将一个泛型方法调用的结果作为参数传递给另一个泛型方法，这时编译器不会执行类型推断，编译器会认为调用泛型方法后其返回值被赋给了一个Object类型的变量。

在调用泛型方法时可以显式地指明类型：
```
f(New.<Person, List<Pet>>map());
```

## 泛型方法与可变参数
泛型方法可以结合可变参数使用：
```
public static <T>  List<T> makeList(T... args){
  ...
}
``` 

## 擦除
`在泛型代码内部无法获得任何有关泛型参数类型的信息`。擦除丢失了在泛型代码中执行某些操作的能力，任何在运行时需要知道确切类型信息的操作都无法进行。
```
public class Erased<T>{
  private final int SIZE = 100;
  public static void f(Object arg){
    if(arg instanceof T){             // error
      // ...
    }

    T var = new T();                  // error

    T[] array = new T[SIZE];          // error

    T[] array = (T)new Object[SIZE];  // Unchecked warning
  }
}
```

Java泛型使用擦除来实现，即在使用泛型时，任何具体的类型信息都被擦除了，因此List<String>和List<Integer>在运行时实际上是相同的类型：都被擦除成它们原生的类型，即List。

`泛型类型参数将擦除到它的第一个边界`（边界即使用extends对类型参数的范围做限制，可能会有多个边界），如下的边界：
```
<T extends HasF>
```
T将擦除到HasF，就好像在类的声明中用HasF替换了T一样。

Java的泛型之所以基于擦除来实现，是因为要兼容旧版本（Java1.0中没有泛型功能），因此泛型类型只有在静态类型检查期间才出现，在此之后程序中的所有泛型类型都将被擦除并替换为它们的非泛型上界。

有时必须通过引入类型标签来对擦除进行补偿，即显式地传递类型的Class对象以便在类型表达式中使用。
```
ClassTypeCapture<Building> ctt = new ClassTypeCapture<Building>(Building.class);
```

## 泛型数组
不能创建泛型数组，一般在任何想要创建泛型数组的地方都使用ArrayList。

## 边界
Java通过重用关键字extends实现在泛型参数类型上设置限制条件（可以是类或接口），从而实现强制规定泛型可以应用的类型。

默认extends了Object，即
<T>等价于<T extends Object>

## 通配符
Java中的数组是协变的，也是不安全的：
```
Fruit[] fruit = new Apple[10];  // 数组是协变的，可以向上转型
fruit[0] = new Apple();   // OK
fruit[1] = new Fruit(); // 编译不会报错，但运行时会报错，因为数组实际类型是Apple 
```

通配符的使用可以对泛型参数做出某些限制，使代码更安全。

通配符引用的是明确的类型（尽管其形式上类似普通边界可以接受一系列不同的类型）。
```
List<? extends Fruit> flist = new ArrayList<Apple>(); 
flist.add(new Apple());   // 编译错误
flist.add(new Fruit());   // 编译错误
flist.add(new Object());  // 编译错误
flist.add(null);   // 唯一可以添加的是 null
```
需要注意的是，flist却可以调用contains和indexOf方法，因为在ArrayList的实现中，add()接受一个泛型类型作为参数，但是contains和indexOf接受一个Object类型的参数。


## 无边界通配符
无边界通配符的使用形式是一个单独的问号：List<?>，也就是没有任何限定。
List<?> list 表示list是持有某种特定类型的List，但是不知道具体是哪种类型。`因为并不知道实际是哪种类型，所以不能添加任何类型`，这是不安全的。

## 逆变
可以使用超类型通配符来定义泛型参数的下界：
<? super MyClass>甚至<? super T>，
但是不能这样定义：
<T super MyClass>

## 泛型的问题
任何基本类型都不能作为类型参数，因此不能创建类似ArrayList<int>的变量。

由于擦除的原因，一个类不能实现同一个泛型接口的两种变体。

使用带有泛型类型参数的转型或instanceof不会有任何效果。

由于擦除的原因，仅泛型参数名不同的重载方法将产生相同的类型签名。

由于擦除的原因，catch语句不能捕获泛型类型的异常（其实泛型类也不能直接或间接地继承Throwable）。

## 自限定的类型

不能直接继承一个泛型参数，但是可以继承在其自己的定义中使用了这个泛型参数的类：
```
class GenericType<T>{}
class CuriouslyRecurringGeneric 
  extends GenericType<CuriouslyRecurringGeneric>{}
```

泛型自限定就是要求在继承关系中将正在定义的类当做参数传递给基类：
```
class A extends SelfBounded<A>{}
```
自限定可以保证类型参数与正在被定义的类相同。
自限定类型的价值在于它们可以产生协变参数类型：方法参数类型会随子类而变化。

## 混型
混型即混合多个类的能力，以产生一个可以表示混型中所有类型的类。在C++中可以使用多重继承实现混型，不过更好的方式是继承其类型参数的类。
在Java中常见的做法是使用接口来产生混型效果（装饰器模式）。

## 潜在类型机制
“如果它走起来像鸭子，并且叫起来也像鸭子，那么就可以把它当做鸭子对待”
（策略模式，略）


# 第16章 数组
数组与其他容器的主要区别在三个方面：效率（更高）、类型（固定）、能够保存基本类型。
（基础知识，略）

## Arrays类
java.util.Arrays类提供了一套用于数组操作的静态方法：
```
equals()  // 总数、对应位置元素都要相等
fill()    
sort()
binarySearch()  // 用于在已经排序的数组中查找元素
toString()
hashCode()
asList()    // 接受任意的序列或数组作为其参数，并将其转变为List容器
```

## 复制数组
使用System.arraycopy复制数组要比用for快很多：
```
System.arraycopy(arr1,0,arr2,0,arr1.length); 
```

## 比较与排序
使用内置的排序方法就可以对任意的基本类型的数组进行排序。也可以对任意的对象数组进行排序，只要该对象实现了Compareable接口或具有相关联的Comparator。


# 第17章 容器深入研究

## 填充容器
使用`Collections类`（不是Collection接口）的静态方法：
```
List<StringAddress> list = new ArrayList<StringAddress>(Collections.nCopies(4,new StringAddress("hello")));  // 4个指向同一个对象的引用

Collections.fill(list,new StringAddress("world!")); // 4个指向同一个对象的引用
```
所有Collection子类型都有一个接收另一个Collection对象的构造器，用所接收的Collection对象中的元素来填充新的容器。

享元模式，略。

Collection的功能方法，略。

List的功能方法，略。

## Set对元素的要求
Set：元素必须实现equals()方法（因为需要唯一），Set接口不保证元素次序；
HashSet：元素必须定义hashCode()；
TreeSet：有次序的Set，元素必须实现Comparable接口；
LinkedHashSet：使用链表维护元素（插入顺序），元素必须定义hashCode()方法；

虽然hashCode()只有在当前类元素被置于HashSet或者LinkedHashSet时才是必须的，但是对于良好的编程风格而言，应该在覆盖equals()方法时总是同时覆盖hashCode()方法。

## 队列
队列在Java中仅有的两个实现是LinkedList和PriorityQueue，它们的差异在于排序行为而非性能。

LinkedList中包含支持双向队列的方法。

## Map
标准Java类库中实现的Map有：
HashMap：插入和查询的开销是固定的；
LinkedHashMap：迭代遍历时，取得键值对的顺序就是其插入顺序或者最近最少使用次序；
TreeMap：基于红黑树，是唯一带有subMap()方法的Map，是目前唯一实现的SortedMap；
WeakHashMap：如果Map之外没有引用指向某个键，则此键可以被垃圾回收器回收；
ConcurrentHashMap：线程安全的Map，无需同步加锁；
IdentityHashMap：使用==代替equals()对键进行比较；

hashCode()是Object中定义的方法，返回代表对象的整数值。

## 正确的equals()
HashMap使用equals()判断当前的键是否与表中存在的键相同，正确的equal()方法必须同时满足下列5个条件：
1.自反性：x.equals(x)返回true；
2.对称性：如果y.equals(x)返回true，则x.equals(y)也返回true
3.传递性：如果x.equals(y)和y.equals(z)返回true，则x.equals(z)也返回true
4.一致性：如果对象中用于等价比较的信息没有改变，那么无论调用x.equals(y)多少次，返回的结果应该保持一致；
5.对任何不是null的x，x.equals(null)一定返回false；

默认的Object.equals()只是比较对象的地址，`如果要使用自己的类作为HashMap的键，必须同时重载hashCode()和equals()`，否则无法正确使用各种散列结构。

实用的hashCode()必须速度快，并且有意义：基于对象的内容生成散列码，应该更关注生成速度而不是一致性（散列码不必是独一无二的），但是通过hashCode()和equals()必须能够完全确定对象的身份。好的hashCode()应该产生分布均匀的散列码。

例子，略。

## HashMap的性能因子
可以通过为HashMap设置不同的性能因子来提高其性能：
1.容量
2.初始容量
3.尺寸：当前存储项数
4.负载因子：尺寸/容量，负载因子小的表产生冲突的可能性小。当负载情况达到负载因子水平时，容器将自动增加其容量：使容量大致加倍，并重新将现有的对象分布到新的位置。默认的负载因子是0.75
```
HashMap(int initialCapacity, float loadFactor);
```

## ConcurrentModificationException
ConcurrentHashMap、CopyOnWriteArrayList、CopyOnWriteArraySet都使用了可以避免ConcurrentModificationException的技术。

## WeakHashMap
java.lang.ref中包含了一组类用来为垃圾回收提供更大的灵活性：SoftReference、WeakReference、PhantomReference，它们都继承自Reference类。当垃圾回收器正在考察的对象只能通过某个Reference对象才能获得时（指对象被Reference对象所代理，且没有其他的引用指向该对象，是否要保留仅仅取决于当前的Reference对象），这些不同的Reference类为垃圾回收器提供了不同级别的间接性指示。

不同的Reference派生类对应不同的“可获得性”级别（由强到弱）：
SoftReference：用以实现内存敏感的高速缓存；
WeakReference：用以实现“规范映射”而设计，不妨碍垃圾回收器回收映射（Map）的键或值；
PhantomReference：用以调度回收前的清理工作，比Java终止机制更灵活。

WeakHashMap用来保存WeakReference，允许垃圾回收器自动清理键和值。对于向WeakHashMap添加键和值的操作，会被自动用WeakReference包装。

## 已废弃的容器
Vector、Enumeration、Hashtable、Stack、BitSet


# 第18章 Java I/O系统




































