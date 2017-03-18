# 《C++ Primer》读书笔记
## 第一章 快速入门
## 第二章 变量和基本类型
### 1. 基本内置类型
	C++定义了一组表示整数、浮点数、单个字符和布尔值的算术类型，另外还定义了一种称为void的特殊类型。void 类型没有对应的值，仅用在有限的一些情况下，通常用作无返回值函数的返回类型。
	算术类型的存储空间依机器而定。C++标准规定了每个算术类型的最小存储空间，但它并不阻止编译器使用更大的存储空间。
整型：表示整数、字符和布尔值的算术类型合称为整型。
	字符类型有两种：char和wchar_t。char类型保证了有足够的空间，能够存储机器基本字符集中任何字符相应的数值，因此，char类型通常是单个机器字节（byte）。wchar_t 类型用于扩展字符集，比如汉字和日语，这些字符集中的一些字符不能用单个char表示。
	short、int和long类型都表示整型值，存储空间的大小不同。一般， short 类型为半个机器字长，int 类型为一个机器字长，而 long 类型为一个或两个机器字长（在 32 位机器中 int 类型和 long 类型通常字长是相同的）。
	bool 类型表示真值 true 和 false。可以将算术类型的任何值赋给 bool 对象。0 值算术类型代表 false，任何非 0 的值都代表 true。
	除 bool 类型外，整型可以是带符号的（signed）也可以是无符号的（unsigned）。
	整型 int、short 和 long 都默认为带符号型。要获得无符号型则必须指定该类型为 unsigned，比如 unsigned long。unsigned int 类型可以简写为 unsigned，也就是说，unsigned 后不加其他类型说明符意味着是 unsigned int 。
	char 有三种不同的类型：plain char、unsigned char和signed char。虽然char有三种不同的类型，但只有两种表示方式。可以使用unsigned char或 signed char表示char类型。使用哪种char表示方式由编译器而定。
	C++ 标准并未定义 signed 类型如何用位来表示，而是由每个编译器自由决定如何表示 signed 类型。这些表示方式会影响signed 类型的取值范围。8位signed类型的取值肯定至少是从-127到127，但也有许多实现允许取值从-128到127。
	当把一个超出其取值范围的值赋给一个指定类型的对象时，对于 unsigned 类型来说，编译器必须调整越界值使其满足要求。编译器会将该值对 unsigned 类型的可能取值数目求模，然后取所得值。比如 8 位的 unsigned char，其取值范围从 0 到 255（包括 255）。如果赋给超出这个范围的值，那么编译器将会取该值对 256 求模后的值。例如，如果试图将 336 存储到 8 位的 unsigned char 中，则实际赋值为 80，因为 80 是 336 对 256 求模后的值。对于 unsigned 类型来说，负数总是超出其取值范围。unsigned 类型的对象可能永远不会保存负数。有些语言中将负数赋给 unsigned 类型是非法的，但在 C++ 中这是合法的。其结果是该负数对该类型的取值个数求模后的值。所以，如果把 -1 赋给8位的 unsigned char，那么结果是 255，因为 255 是 -1 对 256 求模后的值。
浮点型：类型 float、 double 和 long double 分别表示单精度浮点数、双精度浮点数和扩展精度浮点数。一般 float 类型用一个字（32 位）来表示，double 类型用两个字（64 位）来表示，long double 类型用三个或四个字（96 或 128 位）来表示。类型的取值范围决定了浮点数所含的有效数字位数。对于实际的程序来说，float 类型精度通常是不够的——float 型只能保证 6 位有效数字，而 double 型至少可以保证 10 位有效数字，能满足大多数计算的需要。

### 2. 字面值常量
	称之为字面值是因为只能用它的值称呼它，称之为常量是因为它的值不能修改。每个字面值都有相应的类型，例如：0是int型，3.14159是double型。只有内置类型存在字面值，没有类类型的字面值。因此，也没有任何标准库类型的字面值。
	定义字面值整数常量可以使用以下三种进制中的任一种：十进制、八进制和十六进制。当然这些进制不会改变其二进制位的表示形式。例如，能将值 20 定义成下列三种形式中的任意一种：
     	20     // decimal
     	024    // octal
     	0x14   // hexadecimal
	字面值整数常量的类型默认为int或long类型。其精度类型决定于字面值——其值适合int就是int类型，比int大的值就是long类型。通过增加后缀，能够强制将字面值整数常量转换为 long、unsigned 或 unsigned long 类型。如：
		128u     /* unsigned   */          1024UL    /* unsigned long   */
     	1L       /* long    */             8Lu        /* unsigned long   */
没有 short 类型的字面值常量。
	通常可以用十进制或者科学计数法来表示浮点字面值常量。使用科学计数法时，指数用 E 或者 e 表示。默认的浮点字面值常量为 double 类型。在数值的后面加上 F 或 f 表示单精度。同样加上 L 或者 l 表示扩展精度。下面每一组字面值表示相同的值：
     	3.14159F            .001f          	12.345L            0.
     	3.14159E0f          1E-3F          	1.2345E1L          0e0
	单词 true 和 false 是布尔型的字面值：
	可打印的字符型字面值通常用一对单引号来定义：
     'a'         '2'         ','         ' ' // blank
	在字符字面值前加 L 就能够得到 wchar_t 类型的宽字符字面值。如：L'a'
	字符串字面值常量用双引号括起来的零个或者多个字符表示。不可打印字符表示成相应的转义字符。为了兼容 C 语言，C++ 中所有的字符串字面值都由编译器自动在末尾添加一个空字符：
	"A" // 表示包含字母 A 和空字符两个字符的字符串。
	也存在宽字符串字面值，一样在前面加“L”，如L"a wide string literal"宽字符串字面值是一串常量宽字符，同样以一个宽空字符结束。
	两个相邻的仅由空格、制表符或换行符分开的字符串字面值（或宽字符串字面值），可连接成一个新字符串字面值。这使得多行书写长字符串字面值变得简单：
     std::cout << "a multi-line "
                "string literal "
                "using concatenation"
             << std::endl;
	执行这条语句将会输出：a multi-line string literal using concatenation
	如果连接字符串字面值和宽字符串字面值其结果是未定义的，也就是说，连接不同类型的行为标准没有定义。这个程序可能会执行，也可能会崩溃或者产生没有用的值，而且在不同的编译器下程序的动作可能不同：std::cout << "multi-line " L"literal " << std::endl;
	处理长字符串有一个更基本的（但不常使用）方法，这个方法依赖于很少使用的程序格式化特性：在一行的末尾加一反斜线符号可将此行和下一行当作同一行处理：	
	std::cou\
    t << "Hi" << st\
    d::endl;
	等价于std::cout << "Hi" << std::endl;
	可以使用这个特性来编写长字符串字面值：
           std::cout << "a multi-line \
      	string literal \
     	using a backslash"
                 << std::endl;
	注意反斜线符号必须是该行的尾字符——不允许有注释或空格符。同样，后继行行首的任何空格和制表符都是字符串字面值的一部分。正因如此，长字符串字面值的后继行才不会有正常的缩进。

### 3. 变量
	变量提供了程序可以操作的有名字的存储区。C++ 中的每一个变量都有特定的类型，该类型决定了变量的内存大小和布局、能够存储于该内存中的值的取值范围以及可应用在该变量上的操作集。C++程序员常常把变量称为“变量”或“对象（object）”。
	左值（发音为 ell-value）：左值可以出现在赋值语句的左边或右边。
	右值（发音为 are-value）：右值只能出现在赋值的右边，不能出现在赋值语句的左边。
	变量是左值，因此可以出现在赋值语句的左边。数字字面值是右值，因此不能被赋值。
	左值出现的上下文决定了左值是如何使用的。例如，表达式
      units_sold = units_sold + 1; units_sold 变量被用作两种不同操作符的操作数。
	C++ 所有的关键字
		asm		do		if		return	try		auto		double	inline	short	typedef	bool		int		signed	typeid	break	else		long		sizeof	typename		case
 		enum	mutable	static	union	catch	explicit	namespace	static_cast	unsigned
 		char		export	new		struct	using	class		extern	operator	switch	virtual
		const	false		private	template	void		const_cast	float		protected		this
 		volatile	continue	for		public	throw	wchar_t	default	friend	register	true
 		while	delete	goto		reinterpret_cast 	dynamic_cast	
   	C++保留了一些词用作各种操作符的替代名。这些替代名用于支持某些不支持标准C++操作符号集的字符集。它们也不能用作标识符：
		and		bitand	compl	not_eq	or_eq	xor_eq
 		and_eq	bitor		not		or		xor
   	C++ 标准还保留了一组标识符用于标准库。标识符不能包含两个连续的下划线，也不能以下划线开头后面紧跟一个大写字母。有些标识符（在函数外定义的标识符）不能以下划线开头。
	多个变量可以定义在同一条语句中：
      double salary, wage;    // defines two variables of type double
      int month,
          day, year;          // defines three variables of type int
      std::string address;    // defines one variable of type std::string
	C++ 支持两种初始化变量的形式：复制初始化和直接初始化。复制初始化语法用等号（=），直接初始化则是把初始化式放在括号中：
      int ival(1024);     // direct-initialization
      int ival = 1024;    // copy-initialization
	初始化内置类型的对象只有一种方法：提供一个值，并且把这个值复制到新定义的对象中。对内置类型来说，复制初始化和直接初始化几乎没有差别。
	当定义没有初始化式的变量时，系统有时候会帮我们初始化变量。这时，系统提供什么样的值取决于变量的类型，也取决于变量定义的位置。
	内置类型变量是否自动初始化取决于变量定义的位置。在函数体外定义的变量都初始化成 0，在函数体里定义的内置类型变量不进行自动初始化。除了用作赋值操作符的左操作数，未初始化变量用作任何其他用途都是没有定义的。未初始化变量引起的错误难于发现。建议每个内置类型的对象都要初始化。虽然这样做并不总是必需的，但是会更加容易和安全，除非你确定忽略初始化式不会带来风险。
	C++ 程序通常由许多文件组成。为了让多个文件访问相同的变量，C++ 区分了声明和定义。在 C++ 语言中，变量必须且仅能定义一次，而且在使用变量之前必须定义或声明变量。
	变量的定义用于为变量分配存储空间，还可以为变量指定初始值。在一个程序中，变量有且仅有一个定义。
	声明用于向程序表明变量的类型和名字。定义也是声明：当定义变量时我们声明了它的类型和名字。可以通过使用extern关键字声明变量名而不定义它。不定义变量的声明包括对象名、对象类型和对象类型前的关键字extern：
      extern int i;   // declares but does not define i
      int i;        //  declares and defines i
extern 声明不是定义，也不分配存储空间。事实上，它只是说明变量定义在程序的其他地方。程序中变量可以声明多次，但只能定义一次。
	如果声明有初始化式，那么它可被当作是定义，即使声明标记为 extern：
      extern double pi = 3.1416; // definition
	只有当 extern 声明位于函数外部时，才可以含有初始化式。

### 4. const 限定符
	魔数（magic number），它的意义在上下文中没有体现出来。好像这个数是魔术般地从空中出现的。
	因为常量在定义后就不能被修改，所以定义时必须初始化：
      const std::string hi = "hello!"; // ok: initialized
      const int i, j = 0;  // error: i is uninitialized const
	在全局作用域里定义非 const 变量时，它在整个程序中都可以访问。我们可以把一个非 const 变更定义在一个文件中，假设已经做了合适的声明，就可在另外的文件中使用这个变量：
      // file_1.cc
      int counter;  // definition
      // file_2.cc
      extern int counter; // uses counter from file_1
      ++counter;          // increments counter defined in file_1
	与其他变量不同，除非特别说明，在全局作用域声明的 const 变量是定义该对象的文件的局部变量。此变量只存在于那个文件中，不能被其他文件访问。
	通过指定 const 变更为 extern，就可以在整个程序中访问 const 对象：
      // file_1.cc
      // defines and initializes a const that is accessible to other files
      extern const int bufSize = fcn();
      // file_2.cc
      extern const int bufSize; // uses bufSize from file_1
      // uses bufSize defined in file_1
      for (int index = 0; index != bufSize; ++index)
            // ...
	非 const 变量默认为 extern。要使 const 变量能够在其他的文件中访问，必须地指定它为 extern。

### 5. 引用
	引用就是对象的另一个名字。在实际程序中，引用主要用作函数的形式参数。引用是一种复合类型，通过在变量名前添加“&”符号来定义。复合类型是指用其他类型定义的类型。在引用的情况下，每一种引用类型都“关联到”某一其他类型。不能定义引用类型的引用，但可以定义任何其他类型的引用。
	非const引用必须用与该引用同类型的对象初始化：
      int ival = 1024;
      int &refVal = ival; // ok: refVal refers to ival
      int &refVal2;       // error: a reference must be initialized
      int &refVal3 = 10;  // error: initializer must be an object
	当引用初始化后，只要该引用存在，它就保持绑定到初始化时指向的对象。不可能将引用绑定到另一个对象。初始化是指明引用指向哪个对象的唯一方法。
	const引用是指向const对象的引用：
      const int ival = 1024;
      const int &refVal = ival;      // ok: both reference and object are const
      int &ref2 = ival;            // error: 将普通的引用绑定到 const 对象是不合法的
	可以读取但不能修改refVal ，因此，任何对 refVal 的赋值都是不合法的。这个限制有其意义：不能直接对 ival 赋值，因此不能通过使用 refVal 来修改 ival。
	const 引用可以初始化为不同类型的对象或者初始化为右值，如字面值常量：
      int i = 42;
      //  legal for const references only
      const int &r = 42;
      const int &r2 = r + i;

### 6. typedef 名字
	typedef 可以用来定义类型的同义词： 
     typedef double wages;       //  wages is a synonym for double
     typedef wages salary;       //  indirect synonym for double
	typedef 通常被用于以下三种目的：
		①为了隐藏特定类型的实现，强调使用类型的目的。
		②简化复杂的类型定义，使其更易理解。
		③允许一种类型用于多个目的，同时使得每次使用该类型的目的明确。

### 7. 枚举
	枚举不但定义了整数常量集，而且还把它们聚集成组。
	可以为一个或多个枚举成员提供初始值，用来初始化枚举成员的值必须是一个常量表达式。常量表达式是编译器在编译时就能够计算出结果的整型表达式。
	枚举成员值可以是不唯一的。
	不能改变枚举成员的值。枚举成员本身就是一个常量表达式，所以也可用于需要常量表达式的任何地方。枚举类型的对象的初始化或赋值，只能通过其枚举成员或同一枚举类型的其他对象来进行：
		enum Points { point2d = 2, point2w, point3d = 3, point3w };
		Points pt3d = point3d; //  ok: point3d is a Points enumerator
     	Points pt2w = 3; // error:把3赋给Points对象是非法的，即使3与一个Points枚举成员相关联。
     	pt2w = polygon;        //  error: polygon is not a Points enumerator
     	pt2w = pt3d;           //  ok: both are objects of Points enum type

### 8. 类类型
	类定义以关键字 class 开始，其后是该类的名字标识符。类体位于花括号里面。花括号后面必须要跟一个分号。
	访问标号负责控制使用该类的代码是否可以使用给定的成员。类的成员函数可以使用类的任何成员，而不管其访问级别。访问标号 public、private 可以多次出现在类定义中。给定的访问标号应用到下一个访问标号出现时为止。
	定义类的数据成员和定义普通变量存在非常重要的区别：一般不能把类成员的初始化作为其定义的一部分。当定义数据成员时，只能指定该数据成员的名字和类型。类不是在类定义里定义数据成员时初始化数据成员，而是通过构造函数控制初始化。
	struct：如果使用class关键字来定义类，那么定义在第一个访问标号前的任何成员都隐式指定为 private；如果使用struct关键字，那么这些成员都是 public。使用 class 还是 struct 关键字来定义类，仅仅影响默认的初始访问级别。

### 9. 编写自己的头文件
	头文件为相关声明提供了一个集中存放的位置。头文件一般包含类的定义、extern 变量的声明和函数的声明。使用或定义这些实体的文件要包含适当的头文件。
	头文件用于声明而不是用于定义：因为头文件包含在多个源文件中，所以不应该含有变量或函数的定义。	
		extern int ival = 10;      // initializer, so it's a definition
     	double fica_rate;          // no extern, so it's a definition
	对于头文件不应该含有定义这一规则，有三个例外。头文件可以定义类、值在编译时就已知道的 const 对象和 inline 函数。这些实体可在多个源文件中定义，只要每个源文件中的定义是相同的。在头文件中定义这些实体，是因为编译器需要它们的定义（不只是声明）来产生代码。
	一些 const 对象定义在头文件中，const 变量默认时是定义该变量的文件的局部变量。当我们在头文件中定义了 const 变量后，每个包含该头文件的源文件都有了自己的 const 变量，其名称和值都一样。但是在实践中，大部分的编译器在编译时都会用相应的常量表达式替换这些 const 变量的任何使用。所以，在实践中不会有任何存储空间用于存储用常量表达式初始化的 const 变量。
	#include 设施是C++ 预处理器的一部分。预处理器处理程序的源代码，在编译器之前运行。#include 指示只接受一个参数：头文件名。预处理器用指定的头文件的内容替代每个 #include。我们自己的头文件存储在文件中。系统的头文件可能用特定于编译器的更高效的格式保存。
	头文件经常需要其他头文件，因此，设计头文件时，应使其可以多次包含在同一源文件中。必须保证多次包含同一头文件不会引起该头文件定义的类和对象被多次定义。使得头文件安全的通用做法，是使用预处理器定义头文件保护符。头文件保护符用于避免在已经见到头文件的情况下重新处理该头文件的内容。
	预处理器允许我们自定义变量。预处理器变量 的名字在程序中必须是唯一的。任何与预处理器变量相匹配的名字的使用都关联到该预处理器变量。预处理器变量有两种状态：已定义或未定义。定义预处理器变量和检测其状态所用的预处理器指示不同。#define 指示接受一个名字并定义该名字为预处理器变量。#ifndef 指示检测指定的预处理器变量是否未定义。如果预处理器变量未定义，那么跟在其后的所有指示都被处理，直到出现 #endif。
	可以使用这些设施来预防多次包含同一头文件：
     #ifndef SALESITEM_H
     #define SALESITEM_H
     // Definition of Sales_itemclass and related functions goes here
     #endif
	如果 SALESITEM_H 未定义，那么 #ifndef 测试成功，跟在 #ifndef 后面的所有行都被执行，直到发现 #endif。相反，如果 SALESITEM_H 已定义，那么 #ifndef 指示测试为假，该指示和 #endif 指示间的代码都被忽略。
	#include 指示接受以下两种形式：
     #include <standard_header>
     #include "my_file.h"
	如果头文件名括在尖括号（< >）里，那么认为该头文件是标准头文件。编译器将会在预定义的位置集查找该头文件，这些预定义的位置可以通过设置查找路径环境变量或者通过命令行选项来修改。使用的查找方法因编译器的不同而差别迥异。如果头文件名括在一对引号里，那么认为它是非系统头文件，非系统头文件的查找通常开始于源文件所在的路径。

## 第三章 标准库类型

### 1. 命名空间的 using 声明
	使用 using 声明可以在不需要加前缀 namespace_name:: 的情况下访问命名空间中的名字。using 声明的形式如下：
     using namespace::name;
	一个 using 声明一次只能作用于一个命名空间成员。using 声明可用来明确指定在程序中用到的命名空间中的名字，如果希望使用 std（或其他的命名空间）中的几个名字，则必须为要用到的每个名字都提供一个 using 声明。
	有一种情况下，必须总是使用完全限定的标准库名字：在头文件中。理由是头文件的内容会被预处理器复制到程序中。如果在头文件中放置 using 声明，就相当于在包含该头文件 using 的每个程序中都放置了同一 using，不论该程序是否需要 using 声明。

### 2. 标准库string 类型
	用户程序要使用 string 类型对象，必须包含相关头文件。如果提供了合适的 using 声明，那么编写出来的程序将会变得简短些：
     #include <string>
     using std::string;
	因为历史原因以及为了与 C 语言兼容，字符串字面值与标准库 string 类型不是同一种类型。
	string s;          // empty string
    cin >> s;          // read whitespace-separated string into s
	从标准输入读取 string 并将读入的串存储在 s 中。string 类型的输入操作符：
		1）读取并忽略开头所有的空白字符（如空格，换行符，制表符）。
		2）读取字符直至再次遇到空白字符，读取终止。
读入未知数目的 string 对象：和内置类型的输入操作一样，string 的输入操作符也会返回所读的数据流。因此，可以把输入操作作为判断条件
	string word;
    while (cin >> word)
        cout << word << endl;
    return 0;
使用 getline 读取整行文本：
	string line;
    while (getline(cin, line))
         cout << line << endl;
    return 0;
	由于 getline 函数返回时丢弃换行符，换行符将不会存储在 string 对象中。由于 line 不含换行符，若要逐行输出需要自行添加。
string 对象的操作
	s.empty() 如果 s 为空串，则返回 true，否则返回 false。
 	s.size() 返回 s 中字符的个数
 	s[n] 返回 s 中位置为 n 的字符，位置从 0 开始计数
 	s1 + s2 把 s1 和s2 连接成一个新字符串，返回新生成的字符串
 	s1 = s2 把 s1 内容替换为 s2 的副本
 	v1 == v2 比较 v1 与 v2的内容，相等则返回 true，否则返回 false
	!=, <, <=, >, and >= 保持这些操作符惯有的含义
string::size_type 类型：事实上，size 操作返回的是 string::size_type 类型的值。string 类类型和许多其他库类型都定义了一些配套类型（companion type）。通过这些配套类型，库类型的使用就能与机器无关（machine-independent）。size_type 就是这些配套类型中的一种。它定义为与 unsigned 型（unsigned int 或 unsigned long）具有相同的含义，而且可以保证足够大能够存储任意 string 对象的长度。为了避免溢出，保存一个 stirng 对象 size 的最安全的方法就是使用标准库类型 string::size_type。
	从string对象获取字符：string类型通过下标操作符（[ ]）来访问 string 对象中的单个字符。下标操作符需要取一个 size_type 类型的值，来标明要访问字符的位置。这个下标中的值通常被称为“下标”或“索引”（index）
	用下标操作符分别取出 string 对象的每个字符，分行输出：
		string str("some string");
     	for (string::size_type ix = 0; ix != str.size(); ++ix)
         	cout << str[ix] << endl;
	下标操作可用作左值：str[ix] = '*';
	任何可产生整型值的表达式可用作下标操作符的索引。例如，假设 someval 和 someotherval 是两个整形对象，可以这样写：
     	str[someotherval * someval] = someval;
	虽然任何整型数值都可作为索引，但索引的实际数据类型却是类型 unsigned 类型 string::size_type。
	应该用 string::size_type 类型的变量接受 size 函数的返回值。在定义用作索引的变量时，出于同样的道理，string 对象的索引变量最好也用 string::size_type 类型。
string 对象中字符的处理
	这些函数都在 cctype 头文件中定义。
	isalnum(c) 如果 c 是字母或数字，则为 true。
 	isalpha(c) 如果 c 是字母，则为 true。
 	iscntrl(c) 如果 c 是控制字符，则为 true 
 	isdigit(c) 如果 c 是数字，则为 true。
 	isgraph(c) 如果 c 不是空格，但可打印，则为 true。
	islower(c) 如果 c 是小写字母，则为 true。
 	isprint(c) 如果 c 是可打印的字符，则为 true。
 	ispunct(c) 如果 c 是标点符号，则 true。
 	isspace(c) 如果 c 是空白字符，则为 true。
	isupper(c) 如果 c 是大写字母，则 true。
 	isxdigit(c) 如果是 c 十六进制数，则为 true。
	tolower(c) 如果 c 大写字母，返回其小写字母形式，否则直接返回 c。
 	toupper(c) 如果 c 是小写字母，则返回其大写字母形式，否则直接返回 c。
	可打印的字符是指那些可以表示的字符，空白字符则是空格、制表符、垂直制表符、回车符、换行符和进纸符中的任意一种；标点符号则是除了数字、字母或（可打印的）空白字符（如空格）以外的其他可打印字符。
	C++ 标准库除了定义了一些选定于 C++ 的设施外，还包括 C 标准库。C 标准库头文件命名形式为 name 而 C++ 版本则命名为 cname ，少了后缀，.h 而在头文件名前加了 c 表示这个头文件源自 C 标准库。因此，cctype 与 ctype.h 文件的内容是一样的，只是采用了更适合 C++程序的形式。特别地，cname 头文件中定义的名字都定义在命名空间 std 内，而 .h 版本中的名字却不是这样。通常，C++ 程序中应采用 cname 这种头文件的版本，而不采用 name.h 版本，这样，标准库中的名字在命名空间 std 中保持一致。

### 3. 标准库 vector 类型
	vector 是同一种类型的对象的集合，每个对象都有一个对应的整数索引值。和 string 对象一样，标准库将负责管理与存储元素相关的内存。
	使用 vector 之前，必须包含相应的头文件。本书给出的例子，都是假设已作了相应的 using 声明：
     	#include <vector>
     	using std::vector;
	vector 是一个类模板（class template）。使用模板可以编写一个类定义或函数定义，而用于多个不同的数据类型。
	vector<int> ivec;               // ivec holds objects of type int
   	vector<Sales_item> Sales_vec;   // holds Sales_items
	vector 不是一种数据类型，而只是一个类模板，可用来定义任意多种数据类型。vector 类型的每一种都指定了其保存元素的类型。因此，vector<int> 和 vector<string> 都是数据类型。
	vector 对象的构造函数：
		vector<T> v1;	 默认构造函数 v1 为空。
 		vector<T> v2(v1);	 v2 是 v1 的一个副本。v1和v2必须同类型
 		vector<T> v3(n, i);	 v3 包含 n 个值为 i 的元素。
 		vector<T> v4(n);  v4 has n copies of a value-initialized object
vector对象动态增长：vector 对象（以及其他标准库容器对象）的重要属性就在于可以在运行时高效地添加元素。因为 vector 增长的效率高，在元素值已知的情况下，最好是动态地添加元素。
值初始化：如果没有指定元素的初始化式，那么标准库将自行提供一个元素初始值进行值初始化（value initializationd）。这个由库生成的初始值将用来初始化容器中的每个元素，具体值为何，取决于存储在 vector 中元素的数据类型。
	vector<string> fvec(10); // 10 elements, each initialized to 0
	vector<string> svec(10); // 10 elements, each an empty string
	对于有自定义构造函数但没有默认构造函数的类，在初始化这种类型的 vector 对象时，程序员就不能仅提供元素个数，还需要提供元素初始值。
	如果元素类型是没有定义任何构造函数的类类型。这种情况下，标准库仍产生一个带初始值的对象，这个对象的每个成员进行了值初始化。
vector 对象的操作：
	v.empty() 如果 v 为空，则返回 true，否则返回 false。
	v.size() 返回 v 中元素的个数。（返回相应 vector 类定义的 size_type 的值）
 	v.push_back(t) 在 v 的末尾增加一个值为 t 的元素。
 	v[n] 返回 v 中位置为 n 的元素。
 	v1 = v2 把 v1 的元素替换为 v2 中元素的副本。
 	v1 == v2 如果 v1 与 v2 相等，则返回 true。
	!=, <, <=, >, and >= 保持这些操作符惯有的含义。
	使用 size_type 类型时，必须指出该类型是在哪里定义的。vector 类型总是包括总是包括 vector 的元素类型：
 	vector<int>::size_type        // ok
    vector::size_type            // error
向 vector 添加元素：
		string word;
     	vector<string> text;    // empty vector
     	while (cin >> word) {
         	text.push_back(word);     // append word to text
     	}
vector 的下标操作：vector 中的对象是没有命名的，可以按 vector 中对象的位置来访问它们。通常使用下标操作符来获取元素：
	// reset the elements in the vector to zero
     for (vector<int>::size_type ix = 0; ix != ivec.size(); ++ix)
         ivec[ix] = 0;
下标操作不添加元素：
		vector<int> ivec;   // empty vector
     	for (vector<int>::size_type ix = 0; ix != 10; ++ix)
         	ivec[ix] = ix; // disaster: ivec has no elements
	仅能对确知已存在的元素进行下标操作！

### 4. 迭代器简介
	标准库为每一种标准容器（包括 vector）定义了一种迭代器类型。迭代器类型提供了比下标操作更通用化的方法：所有的标准库容器都定义了相应的迭代器类型，而只有少数的容器支持下标操作。因为迭代器对所有的容器都适用，现代 C++ 程序更倾向于使用迭代器而不是下标操作访问容器元素，即使对支持下标操作的 vector 类型也是这样。
	每种容器类型都定义了自己的迭代器类型，如 vector：
		vector<int>::iterator iter;
	定义了一个名为 iter 的变量，它的数据类型是 vector<int> 定义的 iterator 类型。每个标准库容器类型都定义了一个名为 iterator 的成员，这里的 iterator 与迭代器实际类型的含义相同。
迭代器和迭代器类型：同一个术语 iterator 往往表示两个不同的事物。一般意义上指的是迭代器的概念；而具体而言时指的则是由容器定义的具体的 iterator 类型，如 vector<int>。重点要理解的是，有许多用作迭代器的类型，这些类型在概念上是相关的。若一种类型支持一组确定的操作（这些操作可用来遍历容器内的元素，并访问这些元素的值），我们就称这种类型为迭代器。各容器类都定义了自己的 iterator 类型，用于访问容器内的元素。换句话说，每个容器都定义了一个名为 iterator 的类型，而这种类型支持（概念上的）迭代器的各种操作。
begin和end 操作：每种容器都定义了一对命名为 begin 和 end 的函数，用于返回迭代器。如果容器中有元素的话，由 begin 返回的迭代器指向第一个元素：
	vector<int>::iterator iter = ivec.begin();
由 end 操作返回的迭代器指向 vector 的“末端元素的下一个”。“超出末端迭代器”（off-the-end iterator）。表明它指向了一个不存在的元素。如果 vector 为空，begin 返回的迭代器与 end 返回的迭代器相同。
vector 迭代器的自增和解引用运算：迭代器类型定义了一些操作来获取迭代器所指向的元素，并允许程序员将迭代器从一个元素移动到另一个元素。
	迭代器类型可使用解引用操作符（dereference operator）（*）来访问迭代器所指向的元素：*iter = 0;
	迭代器使用自增操作符向前移动迭代器指向容器中下一个元素。
可以用 == 或 != 操作符来比较两个迭代器，如果两个迭代器对象指向同一个元素，则它们相等，否则就不相等。
const_iterator：每种容器类型还定义了一种名为 const_iterator 的类型，该类型只能用于读取容器内元素，但不能改变其值。当我们对普通 iterator 类型解引用时，得到对某个元素的非 const。而如果我们对 const_iterator 类型解引用时，则可以得到一个指向 const 对象的引用，如同任何常量一样，该对象不能进行重写。
	使用 const_iterator 类型时，我们可以得到一个迭代器，它自身的值可以改变，但不能用来改变其所指向的元素的值。可以对迭代器进行自增以及使用解引用操作符来读取值，但不能对该元素赋值。
const_iterator 对象与 const 的 iterator 对象的区别：声明一个 const 迭代器时，必须初始化迭代器。一旦被初始化后，就不能改变它的值：
		vector<int> nums(10);  // nums is nonconst
     	const vector<int>::iterator cit = nums.begin();
     	*cit = 1;               // ok: cit can change its underlying element
     	++cit;                  // error: can't change the value of cit
迭代器的算术操作：除了一次移动迭代器的一个元素的增量操作符外，vector 迭代器（其他标准库容器迭代器很少）也支持其他的算术操作。这些操作称为迭代器算术操作（iterator arithmetic），包括：
		iter + n
		iter - n
		iter1 - iter2：该表达式用来计算两个迭代器对象的距离，该距离是名为 difference_type 的 signed 类型 size_type 的值
	可以用迭代器算术操作来移动迭代器直接指向某个元素，例如，下面语句直接定位于 vector 中间元素： vector<int>::iterator mid = vi.begin() + vi.size() / 2;
任何改变 vector 长度的操作都会使已存在的迭代器失效。例如，在调用 push_back 之后，就不能再信赖指向 vector 的迭代器的值了

### 5. 标准库 bitset

## 第四章 数组和指针
### 1. 数组
 	数组定义中的类型名可以是内置数据类型或类类型；除引用之外，数组元素的类型还可以是任意的复合类型。没有所有元素都是引用的数组。
	数组的维数必须用值大于等于1的常量表达式定义。此常量表达式只能包含整型字面值常量、枚举常量或者用常量表达式初始化的整型 const 对象。非 const 变量以及要到运行阶段才知道其值的 const 变量都不能用于定义数组的维数。
	const unsigned buf_size = 512, max_files = 20;
    int staff_size = 27;            // nonconst
    const unsigned sz = get_size();  // const value not known until run time
    char input_buffer[buf_size];     // ok: const variable
    string fileTable[max_files + 1]; // ok: constant expression
    double salaries[staff_size];     // error: non const variable
    int test_scores[get_size()];     // error: non const expression
    int vals[sz];                    // error: size not known until run time
	虽然 staff_size 是用字面值常量进行初始化，但 staff_size 本身是一个非 const 对象，只有在运行时才能获得它的值，因此，使用该变量来定义数组维数是非法的。而对于 sz，尽管它是一个 const 对象，但它的值要到运行时调用 get_size 函数后才知道，因此，它也不能用于定义数组维数。
如果没有显式提供元素初值，则数组元素会像普通变量一样初始化：
	1）在函数体外定义的内置数组，其元素均初始化为 0。
	2）在函数体内定义的内置数组，其元素无初始化。
	3）不管数组在哪里定义，如果其元素为类类型，则自动调用该类的默认构造函数进行初始化；如果该类没有默认构造函数，则必须为该数组的元素提供显式初始化。
显式初始化的数组不需要指定数组的维数值，编译器会根据列出的元素个数来确定数组的长度
如果指定了数组维数，那么初始化列表提供的元素个数不能超过维数值。如果维数大于列出的元素初值个数，则只初始化前面的数组元素；剩下的其他元素，若是内置类型则初始化为0，若是类类型则调用该类的默认构造函数进行初始化：
特殊的字符数组：字符数组既可以用一组由花括号括起来、逗号隔开的字符字面值进行初始化，也可以用一个字符串字面值进行初始化。然而，要注意这两种初始化形式并不完全相同，字符串字面值包含一个额外的空字符（null）用于结束字符串。当使用字符串字面值来初始化创建的新数组时，将在新数组中加入空字符：
        char ca1[] = {'C', '+', '+'};                // no null
        char ca2[] = {'C', '+', '+', '\0'};         // explicit null
        char ca3[] = "C++";     // null terminator added automatically
		const char ch3[6] = "Daniel"; // error: Daniel is 7 elements
不允许数组直接复制和赋值: 与vector不同，一个数组不能用另外一个数组初始化，也不能将一个数组赋值给另一个数组:
		  int ia[] = {0, 1, 2}; // ok: array of ints
          int ia2[](ia);        // error: cannot initialize one array with another
          int main()
          {
              const unsigned array_size = 3;
              int ia3[array_size]; // ok: but elements are uninitialized!

              ia3 = ia;           //  error: cannot assign one array to another
              return 0;
          }
数组操作: 在用下标访问元素时，vector 使用 vector::size_type 作为下标的类型，而数组下标的正确类型则是 size_t:
			  const size_t array_size = 10;
              int ia[array_size]; // 10 ints, elements are uninitialized
              for (size_t ix = 0; ix != array_size; ++ix)
                    ia[ix] = ix;
              return 0;

### 2. 指针的引入
注意声明格式：string* ps1, ps2; // ps1 is a pointer to string,  ps2 is a string
一个有效的指针必然是以下三种状态之一：保存一个特定对象的地址；指向某个对象后面的另一对象；或者是0值。若指针保存0值，表明它不指向任何对象。未初始化的指针是无效的，直到给该指针赋值后，才可使用它。下列定义和赋值都是合法的：
          int ival = 1024;
          int *pi = 0;       // pi initialized to address no object
          int *pi2 = & ival; // pi2 initialized to address of ival
          int *pi3;          // ok, but dangerous, pi3 is uninitialized
          pi = pi2;          // pi and pi2 address the same object, e.g. ival
          pi2 = 0;           // pi2 now addresses no object
C++ 语言无法检测指针是否未被初始化，也无法区分有效地址和由指针分配到的存储空间中存放的二进制位形成的地址。建议程序员在使用之前初始化所有的变量，尤其是指针。如果必须分开定义指针和其所指向的对象，则将指针初始化为 0。因为编译器可检测出 0 值的指针，程序可判断该指针并未指向一个对象。
对指针进行初始化或赋值只能使用以下四种类型的值：
	1）0 值常量表达式，例如，在编译时可获得 0 值的整型 const 对象或字面值常量 0。
	2）类型匹配的对象的地址。
	3）另一对象末的下一地址。
	4）同类型的另一个有效指针。
除了使用数值0或在编译时值为 0 的 const 量外，还可以使用 C++ 语言从 C 语言中继承下来的预处理器变量 NULL，该变量在 cstdlib 头文件中定义，其值为 0。如果在代码中使用了这个预处理器变量，则编译时会自动被数值 0 替换。因此，把指针初始化为 NULL 等效于初始化为 0 值：
    int *pi = NULL; // ok: equivalent to int *pi = 0;
由于指针的类型用于确定指针所指对象的类型，因此初始化或赋值时必须保证类型匹配。指针用于间接访问对象，并基于指针的类型提供可执行的操作，例如，int 型指针只能把其指向的对象当作 int 型数据来处理，如果该指针确实指向了其他类型（如 double 类型）的对象，则在指针上执行的任何操作都有可能出错。
void* 指针：C++ 提供了一种特殊的指针类型 void*，它可以保存任何类型对象的地址：
void* 指针只支持几种有限的操作：与另一个指针进行比较；向函数传递 void* 指针或从函数返回 void* 指针；给另一个 void* 指针赋值。不允许使用 void* 指针操纵它所指向的对象。
指针和引用的比较：虽然使用引用（reference）和指针都可间接访问另一个值，但它们之间有两个重要区别。第一个区别在于引用总是指向某个对象：定义引用时没有初始化是错误的。第二个重要区别则是赋值行为的差异：给引用赋值修改的是该引用所关联的对象的值，而并不是使引用与另一个对象关联。引用一经初始化，就始终指向同一个特定对象（这就是为什么引用必须在定义时初始化的原因）。
指向指针的指针：指针本身也是可用指针指向的内存对象。指针占用内存空间存放其值，因此指针的存储地址可存放在指针中。
使用指针访问数组元素：C++ 语言中，指针和数组密切相关。特别是在表达式中使用数组名时，该名字会自动转换为指向数组第一个元素的指针：
		int ia[] = {0,2,4,6,8};
       	int *ip = ia; // ip points to ia[0]
	如果希望使指针指向数组中的另一个元素，则可使用下标操作符给某个元素定位，然后用取地址操作符 & 获取该元素的存储地址：
		ip = &ia[4];    // ip points to last element in ia
指针的算术操作：指针的算术操作只有在原指针和计算出来的新指针都指向同一个数组的元素，或指向该数组存储空间的下一单元时才是合法的。如果指针指向一对象，我们还可以在指针上加1从而获取指向相邻的下一个对象的指针。
 	两个指针减法操作的结果是标准库类型（library type）ptrdiff_t 的数据。与 size_t 类型一样，ptrdiff_t 也是一种与机器相关的类型，在 cstddef 头文件中定义。
	允许在指针上加减 0，使指针保持不变。更有趣的是，如果一指针具有 0 值（空指针），则在该指针上加 0 仍然是合法的，结果得到另一个值为 0 的指针。也可以对两个空指针做减法操作，得到的结果仍是 0。
在使用下标访问数组时，实际上是对指向数组元素的指针做下标操作。只要指针指向数组元素，就可以对它进行下标操作：
	int ia[] = {0,2,4,6,8};
   	int *p = &ia[2];     // ok: p points to the element indexed by 2
	int j = p[1];        // ok: p[1] equivalent to *(p + 1),
  	int k = p[-2];       // ok: p[-2] is the same element as ia[0]
如果指针指向 const 对象，则不允许用指针来改变其所指的 const 值。为了保证这个特性，C++ 语言强制要求指向 const 对象的指针也必须具有 const 特性：
	const double *cptr;  // cptr may point to a double that is const
cptr 是一个指向 double 类型 const 对象的指针，const 限定了 cptr 指针所指向的对象类型，而并非 cptr 本身。也就是说，cptr 本身并不是 const。在定义时不需要对它进行初始化，如果需要的话，允许给 cptr 重新赋值，使其指向另一个 const 对象。但不能通过 cptr 修改其所指对象的值。
	把一个 const 对象的地址赋给一个普通的、非 const 对象的指针也会导致编译时的错误：
          const double pi = 3.14;
          double *ptr = &pi;        // error: ptr is a plain pointer
          const double *cptr = &pi; // ok: cptr is a pointer to const
	不能使用 void*保存 const 对象的地址，而必须使用 const void* 类型的指针保存 const 对象的地址：
          const int universe = 42;
          const void *cpv = &universe; // ok: cpv is const
          void *pv = &universe;        // error: universe is const
	允许把非 const 对象的地址赋给指向 const 对象的指针，例如：
          double dval = 3.14; // dval is a double; its value can be changed
          cptr = &dval;       // ok: but can't change dval through cptr
	尽管 dval 不是 const 对象，但任何企图通过指针 cptr 修改其值的行为都会导致编译时的错误。cptr 一经定义，就不允许修改其所指对象的值。如果该指针恰好指向非 const 对象时，同样必须遵循这个规则。
	重要的是要记住：不能保证指向 const 的指针所指对象的值一定不可修改。
const 指针：——本身的值不能修改
		int errNumb = 0;
        int *const curErr = &errNumb; // curErr is a constant pointer
	与任何 const 量一样，const 指针也必须在定义时初始化。
	指针本身是 const 的事实并没有说明是否能使用该指针修改它所指向对象的值。指针所指对象的值能否修改完全取决于该对象的类型。
指向 const 对象的 const 指针：
		const double pi = 3.14159;
    	// pi_ptr is const and points to a const object
     	const double *const pi_ptr = &pi;
用 typedef 写 const 类型定义时，const 限定符加在类型名前面容易引起对所定义的真正类型的误解：
          string s;
          typedef string *pstring;
          const pstring cstr1 = &s; // written this way the type is obscured
          pstring const cstr2 = &s; // all three decreations are the same type
          string *const cstr3 = &s; // they're all const pointers to string
	把 const 放在类型 pstring 之后，然后从右向左阅读该声明语句就会非常清楚地知道 cstr2 是 const pstring 类型，即指向 string 对象的 const 指针。

### 3. C 风格字符串
字符串字面值的类型就是 const char 类型的数组。C++ 从 C 语言继承下来的一种通用结构是C 风格字符串，而字符串字面值就是该类型的实例。实际上，C 风格字符串既不能确切地归结为 C 语言的类型，也不能归结为 C++ 语言的类型，而是以空字符 null 结束的字符数组：
C++ 语言通过(const)char*类型的指针来操纵 C 风格字符串。
    	const char *cp = "some value";
C 风格字符串的标准库函数：要使用这些标准库函数，必须包含相应的 C 头文件，cstring 是 string.h 头文件的 C++ 版本，而 string.h 则是 C 语言提供的标准库。这些标准库函数不会检查其字符串参数。传递给这些标准库函数例程的指针必须具有非零值，并且指向以 null 结束的字符数组中的第一个元素。
		strlen(s) 返回 s 的长度，不包括字符串结束符 null
 		strcmp(s1, s2) 比较两个字符串 s1 和 s2 是否相同。若 s1 与 s2 相等，返回 0；若 s1 大于 s2，返回正数；若 s1 小于 s2，则返回负数
 		strcat(s1, s2) 将字符串 s2 连接到 s1 后，并返回 s1
 		strcpy(s1, s2)	将 s2 复制给 s1，并返回 s1
 		strncat(s1, s2,n) 将 s2 的前 n 个字符连接到 s1 后面，并返回 s1
		strncpy(s1, s2, n) 将 s2 的前 n 个字符复制给 s1，并返回 s1
C++ 语言提供普通的关系操作符实现标准库类型 string 的对象的比较。这些操作符也可用于比较指向C风格字符串的指针，但效果却很不相同：实际上，此时比较的是指针上存放的地址值，而并非它们所指向的字符串：
		if (cp1 < cp2) // compares addresses, not the values pointed to
	字符串的比较和比较结果的解释都须使用标准库函数 strcmp 进行
创建动态数组：每一个程序在执行时都占用一块可用的内存空间，用于存放动态分配的对象，此内存空间称为程序的自由存储区或堆。C 语言程序使用一对标准库函数 malloc 和 free 在自由存储区中分配存储空间，而 C++ 语言则使用 new 和 delete 表达式实现相同的功能。
	动态分配数组时，只需指定类型和数组长度，不必为数组对象命名，new 表达式返回指向新分配数组的第一个元素的指针：
		int *pia = new int[10]; // array of 10 uninitialized ints
	动态分配数组时，如果数组元素具有类类型，将使用该类的默认构造函数实现初始化；如果数组元素是内置类型，则无初始化：
          string *psa = new string[10]; // array of 10 empty strings
          int *pia = new int[10];       // array of 10 uninitialized ints
	圆括号要求编译器对数组做值初始化：也可使用跟在数组长度后面的一对空圆括号，对数组元素做值初始化， int *pia2 = new int[10] (); // array of 10 uninitialized ints
	对于动态分配的数组，其元素只能初始化为元素类型的默认值，而不能像数组变量一样，用初始化列表为数组元素提供各不相同的初值。
const 对象的动态数组（这样的数组实际上用处不大）：如果我们在自由存储区中创建的数组存储了内置类型的 const 对象，则必须为这个数组提供初始化：因为数组元素都是 const 对象，无法赋值。实现这个要求的唯一方法是对数组做值初始化：          
		const int *pci_bad = new const int[100]; // error: uninitialized const array
  		const int *pci_ok = new const int[100](); // ok: value-initialized const array
	C++ 允许定义类类型的 const 数组，但该类类型必须提供默认构造函数：
     	const string *pcs = new const string[100];  // ok: array of 100 empty strings
C++ 虽然不允许定义长度为 0 的数组变量，但明确指出，调用 new 动态创建长度为 0 的数组是合法的：
	char arr[0];            // error: cannot define zero-length array
  	char *cp = new char[0]; // ok: but cp can't be dereferenced
	用 new 动态创建长度为 0 的数组时，new 返回有效的非零指针。该指针与 new 返回的其他指针不同，不能进行解引用操作，因为它毕竟没有指向任何元素。而允许的操作包括：比较运算，因此该指针能在循环中使用；在该指针上加（减）0；或者减去本身，得 0 值。
如果不再需要使用动态创建的数组，程序员必须显式地将其占用的存储空间返还给程序的自由存储区。C++ 语言为指针提供 delete [] 表达式释放指针所指向的数组空间：delete [] pia; 该语句回收了 pia 所指向的数组，把相应的内存返还给自由存储区。在关键字 delete 和指针之间的空方括号对是必不可少的：它告诉编译器该指针指向的是自由存储区中的数组，而并非单个对象。
C 风格字符串与 C++ 的标准库类型 string 的比较：通常，由于 C 风格字符串与字符串字面值具有相同的数据类型，而且都是以空字符 null 结束，因此可以把 C 风格字符串用在任何可以使用字符串字面值的地方：
	1）可以使用 C 风格字符串对 string 对象进行初始化或赋值。
	2）string 类型的加法操作需要两个操作数，可以使用 C 风格字符串作为其中的一个操作数，也允许将 C 风格字符串用作复合赋值操作的右操作数。
在要求C风格字符串的地方不可直接使用标准库 string 类型对象。例如，无法使用 string 对象初始化字符指针：
		char *str = st2; // compile-time type error
	但是，string 类提供了一个名为 c_str 的成员函数，以实现我们的要求：
		char *str = st2.c_str(); // almost ok, but not quite
	如果 c_str 返回的指针指向 const char 类型的数组，则上述初始化失败，这样做是为了避免修改该数组。正确的初始化应为：
		const char *str = st2.c_str(); // ok
	c_str 返回的数组并不保证一定是有效的，接下来对 st2 的操作有可能会改变 st2 的值，使刚才返回的数组失效。如果程序需要持续访问该数据，则应该复制 c_str 函数返回的数组。
使用数组初始化 vector 对象：使用数组初始化 vector 对象，必须指出用于初始化式的第一个元素以及数组最后一个元素的下一位置的地址：
		const size_t arr_size = 6;
      	int int_arr[arr_size] = {0, 1, 2, 3, 4, 5};
          // ivec has 6 elements: each a copy of the corresponding element in int_arr
      	vector<int> ivec(int_arr, int_arr + arr_size);
	被标出的元素范围可以是数组的子集：
		vector<int> ivec(int_arr + 1, int_arr + 4);

### 4. 多维数组
多维数组的初始化：
	int ia[3][4] = {     /*  3 elements, each element is an array of size 4 */
         {0, 1, 2, 3} ,   /*  initializers for row indexed by 0 */
         {4, 5, 6, 7} ,   /*  initializers for row indexed by 1 */
         {8, 9, 10, 11}   /*  initializers for row indexed by 2 */
     };
     int ia[3][4] = {0,1,2,3,4,5,6,7,8,9,10,11};//与前面的声明完全等价
     int ia[3][4] = { { 0 } , { 4 } , { 8 } };//只初始化了每行的第一个元素
     int ia[3][4] = {0, 3, 6, 9};//初始化了第一行的元素，其余元素都被初始化为 0
指针和多维数组：
		int ia[3][4];      // array of size 3, each element is an array of ints of size 4
     	int (*ip)[4] = ia;  // ip points to an array of 4 ints
     	ip = &ia[2];       // ia[2] is an array of 4 ints
		int *ip[4]; // array of pointers to int

## 第五章 表达式
### 1. 算术操作符
	操作符 % 称为“求余（remainder）”或“求模（modulus）”操作符，用于计算左操作数除以右操作数的余数。该操作符的操作数只能为整型，包括 bool、char、short 、int 和 long 类型，以及对应的 unsigned 类型。

### 2. 关系操作符和逻辑操作符
	逻辑与和逻辑或操作符总是先计算其左操作数，然后再计算其右操作数。只有在仅靠左操作数的值无法确定该逻辑表达式的结果时，才会求解其右操作数。我们常常称这种求值策略为“短路求值（short-circuit evaluation）”。

### 3. 位操作符
	位操作符使用整型的操作数。位操作符将其整型操作数视为二进制位的集合，为每一位提供检验和设置的功能。另外，这类操作符还可用于 bitset 类型的操作数。
	对于位操作符，由于系统不能确保如何处理其操作数的符号位，所以强烈建议使用unsigned整型操作数。
	一般而言，标准库提供的 bitset 操作更直接、更容易阅读和书写、正确使用的可能性更高。而且，bitset 对象的大小不受 unsigned 数的位数限制。通常来说，bitset 优于整型数据的低级直接位操作。
	输入输出标准库（IO library）分别重载了位操作符 >> 和 << 用于输入和输出。
	移位操作符具有中等优先级：其优先级比算术操作符低，但比关系操作符、赋值操作符和条件操作符优先级高。若 IO 表达式的操作数包含了比IO操作符优先级低的操作符，相关的优先级别将影响书写该表达式的方式。通常需使用圆括号强制先实现右结合：
     cout << 42 + 10;   // ok, + has higher precedence, so the sum is printed
     cout << (10 < 42); // ok: parentheses force intended grouping; prints 1
     cout << 10 < 42;   // error: attempt to compare cout to 42!

### 4. 赋值操作符
	数组名是不可修改的左值：因此数组不可用作赋值操作的目标。
	赋值表达式的值是其左操作数的值，其结果的类型为左操作数的类型。
	与其他二元操作符不同，赋值操作具有右结合特性。当表达式含有多个赋值操作符时，从右向左结合。
	多个赋值操作中，各对象必须具有相同的数据类型，或者具有可转换为同一类型的数据类型：
     int ival; int *pval;
     ival = pval = 0; // error: cannot assign the value of a pointer to an int
     string s1, s2;
     s1 = s2 = "OK";  // ok: "OK" converted to string

### 5. 自增和自减操作符
	由于后自增操作的优先级高于解引用操作，因此 *iter++ 等效于 *(iter++)。子表达式 iter++ 使 iter 加 1，然后返回 iter 原值的副本作为该表达式的结果。因此，解引用操作 * 的操作数是 iter 未加 1 前的副本。

### 6. 箭头操作符
	假设有一个指向类类型对象的指针（或迭代器），下面的表达式相互等价：
     (*p).foo; // dereference p to get an object and fetch its member named foo
     p->foo;   // equivalent way to fetch the foo from the object to which p points

### 7. 条件操作符
	条件操作符的优先级相当低。当我们要在一个更大的表达式中嵌入条件表达式时，通常必须用圆括号把条件表达式括起来。
     cout << (i < j ? i : j);  // ok: prints larger of i and j
     cout << (i < j) ? i : j;  // prints 1 or 0!
     cout << i < j ? i : j;    // error: compares cout to int
	第二个表达式等效于：
     cout << (i < j); // prints 1 or 0
     cout ? i : j;    // test cout and then evaluate i or j
                      // depending on whether cout evaluates to true or false

### 8. sizeof 操作符
	sizeof 操作符的作用是返回一个对象或类型名的长度，返回值的类型为 size_t，长度的单位是字节。size_t 表达式的结果是编译时常量，该操作符有以下三种语法形式：
     sizeof (type name);
     sizeof (expr);
     sizeof expr;
将 sizeof 应用在表达式 expr 上，将获得该表达式的结果的类型长度：
     Sales_item item, *p;
     // three ways to obtain size required to hold an object of type Sales_item
     sizeof(Sales_item); // size required to hold an object of type Sales_item
     sizeof item; // size of item's type, e.g., sizeof(Sales_item)
     sizeof *p;   // size of type to which p points, e.g., sizeof(Sales_item)
	将 sizeof 用于 expr 时，并没有计算表达式 expr 的值。特别是在 sizeof *p 中，指针 p 可以持有一个无效地址，因为不需要对 p 做解引用操作。
使用 sizeof 的结果部分地依赖所涉及的类型：
	1）对 char 类型或值为 char 类型的表达式做 sizeof 操作保证得 1。
	2）对引用类型做 sizeof 操作将返回存放被引用类型对象所需的内存空间大小。
	3）对指针做 sizeof 操作将返回存放指针所需的内存大小；注意，如果要获取该指针所指向对象的大小，则必须对指针进行引用。
	4）对数组做 sizeof 操作等效于将对其元素类型做 sizeof 操作的结果乘上数组元素的个数。
	因为 sizeof 返回整个数组在内存中的存储长度，所以用 sizeof 数组的结果除以 sizeof 其元素类型的结果，即可求出数组元素的个数：

### 9. 逗号操作符
逗号表达式是一组由逗号分隔的表达式，这些表达式从左向右计算。逗号表达式的结果是其最右边表达式的值。如果最右边的操作数是左值，则逗号表达式的值也是左值。

### 10. 复合表达式的求值

### 11. new 和 delete 表达式
动态创建对象时，只需指定其数据类型，而不必为该对象命名。取而代之的是，new 表达式返回指向新创建对象的指针，我们通过该指针来访问此对象：
     int i;              // named, uninitialized int variable
     int *pi = new int;  // pi points to dynamically allocated,
                         // unnamed, uninitialized int
	动态创建的对象可用初始化变量的方式实现初始化：
     int i(1024);              // value of i is 1024
     int *pi = new int(1024);  // object to which pi points is 1024
     string s(10, '9');                   // value of s is "9999999999"
     string *ps = new string(10, '9');    // *ps is "9999999999"
	如果不提供显式初始化，动态创建的对象与在函数内定义的变量初始化方式相同。对于类类型的对象，用该类的默认构造函数初始化；而内置类型的对象则无初始化。
	同样也可对动态创建的对象做值初始化（value-initialize）：
     string *ps = new string();  // initialized to empty string
     int *pi = new int();  // pi points to an int value-initialized to 0
     cls *pc = new cls();  // pc points to a value-initialized object of type cls
如果 new 表达式无法获取需要的内存空间，系统将抛出名为 bad_alloc 的异常。
动态创建的对象用完后，程序员必须显式地将该对象占用的内存返回给自由存储区。C++ 提供了 delete 表达式释放指针所指向的地址空间。
如果指针指向不是用 new 分配的内存地址，则在该指针上使用 delete 是不合法的。C++ 没有明确定义如何释放指向不是用 new 分配的内存地址的指针。
执行语句delete p; 后，p 变成没有定义。在很多机器上，尽管 p 没有定义，但仍然存放了它之前所指向对象的地址，然而 p 所指向的内存已经被释放，因此 p 不再有效。一旦删除了指针所指向的对象，立即将指针置为 0，这样就非常清楚地表明指针不再指向任何对象。
C++ 允许动态创建 const 对象：const int *pci = new const int(1024);
与其他常量一样，动态创建的 const 对象必须在创建时初始化，并且一经初始化，其值就不能再修改。
尽管程序员不能改变 const 对象的值，但可撤销对象本身。如同其他动态对象一样， const 动态对象也是使用删除指针来释放的：
     delete pci; // ok: deletes a const object
当两个指针指向同一个动态创建的对象，删除时就会发生错误。如果在其中一个指针上做 delete 运算，将该对象的内存空间返还给自由存储区，然后接着 delete 第二个指针，此时则自由存储区可能会被破坏。

### 12. 类型转换
标准 C++ 为了加强类型转换的可视性，引入命名的强制转换操作符：
	1）dynamic_cast 支持运行时识别指针或引用所指向的对象。
	2）const_cast将转换掉表达式的 const 性质。
		const char *pc_str;
     	char *pc = string_copy(const_cast<char*>(pc_str));
		只有使用 const_cast 才能将 const 性质转换掉。在这种情况下，试图使用其他三种形式的强制转换都会导致编译时的错误。类似地，除了添加或删除 const 特性，用 const_cast 符来执行其他任何类型转换，都会引起编译错误。
	3）static_cast：编译器隐式执行的任何类型转换都可以由 static_cast 显式完成
		void* p = &d; // ok: address of any data object can be stored in a void*
     	// ok: converts void* back to the original pointer type
     	double *dp = static_cast<double*>(p);
	4）reinterpret_cast 通常为操作数的位模式提供较低层次的重新解释。
旧式强制类型转换：在引入命名的强制类型转换操作符之前，显式强制转换用圆括号将类型括起来实现：
     char *pc = (char*) ip;
	效果与使用 reinterpret_cast 符号相同，但这种强制转换的可视性比较差，难以跟踪错误的转换。

## 第六章 语句
### 1. 简单语句
程序语句最简单的形式是空语句，它使用以下的形式（只有一个单独的分号）：;  // null statement

### 2. 声明语句

### 3. 复合语句（块）
复合语句，通常被称为块，是用一对花括号括起来的语句序列（也可能是空的）。块标识了一个作用域，在块中引入的名字只能在该块内部或嵌套在块中的子块里访问。通常，一个名字只从其定义处到该块的结尾这段范围内可见。
与其他大多数语句不同，块并不是以分号结束的。

### 4. 语句作用域
在条件表达式中定义的变量必须初始化，该条件检验的就是初始化对象的值。
在语句的控制结构中定义的变量，仅在定义它们的块语句结束前有效。这种变量的作用域限制在语句体内。

### 5. if 语句
如果在条件表达式中定义了变量，那么变量必须初始化。将已初始化的变量值转换为 bool 值后，该 bool 值决定条件是否成立。变量类型可以是任何可转换为 bool 型的类型，这意味着它可以是算术类型或指针类型。一个类类型能否用在条件表达式中取决于类本身。如IO 类型可以用作条件，但 vector 类型和 string 类型一般不可用作条件。

### 6. switch 语句
每个 case 标号的值都必须是一个常量表达式。除此之外，还有一个特殊的 case 标号——default 标号。
为了避免继续执行其后续 case 标号的内容，程序员必须利用 break 语句清楚地告诉编译器停止执行 switch 中的语句。
case 标号必须是整型常量表达式。例如，下面的标号将导致编译时的错误：
     case 3.14:  // noninteger
     case ival:  // nonconstant
每个 case 标号不一定要另起一行。为了强调这些 case 标号表示的是一个要匹配的范围，可以将它们全部在一行中列出：
     switch (ch)
     {
         case 'a': case 'e': case 'i': case 'o': case 'u':
             ++vowelCnt;
             break;
     }
如果所有的 case 标号与 switch 表达式的值都不匹配，并且 default 标号存在，则执行 default 标号后面的语句。
如果 switch 结构以 default 标号结束，而且 default 分支不需要完成任何任务，那么该标号后面必须有一个空语句。
对于 switch 结构，只能在它的最后一个 case 标号或 default 标号后面定义变量：
     case true:
          // error: declaration precedes a case label
          string file_name = get_file_name();
          break;
     case false:
          // ...
制定这个规则是为避免出现代码跳过变量的定义和初始化的情况。回顾变量的作用域，变量从它的定义点开始有效，直到它所在块结束为止。现在考虑如果在两个 case 标号之间定义变量会出现什么情况。该变量会在块结束之前一直存在。对于定义该变量的标号后面的其他 case 标号，它们所关联的代码都可以使用这个变量。如果 switch 从那些后续 case 标号开始执行，那么这个变量可能还未定义就要使用了。

### 7. while 语句
在循环条件中定义的变量在每次循环里都要经历创建和撤销的过程。

### 8. for 循环语句
for 语句头中，可以省略 init-statement、condition 或者 expression（表达式）中的任何一个（或全部）。省略 condition，则等效于循环条件永远为 true。
可以在 for 语句的 init-statement 中定义多个对象；但是不管怎么样，该处只能出现一个语句，因此所有的对象必须具有相同的一般类型：
	for (int ival = 0, *pi = ia, &ri = val;
           ival != size;
           ++ival, ++pi, ++ri)
                   // ...

### 9. do while 语句
与 while 语句不同。do-while 语句总是以分号结束。

### 10. break 语句
break 只能出现在循环或 switch 结构中，或者出现在嵌套于循环或 switch 结构中的语句里。对于 if 语句，只有当它嵌套在 switch 或循环里面时，才能使用 break。break 出现在循环外或者 switch 外将会导致编译时错误。当 break 出现在嵌套的 switch 或者循环语句中时，将会终止里层的 switch 或循环语句，而外层的 switch 或者循环不受影响。

### 11. continue 语句
continue 语句导致最近的循环语句的当次迭代提前结束。对于 while 和 do while 语句，继续求解循环条件。而对于 for 循环，程序流程接着求解 for 语句头中的 expression 表达式。
continue 语句只能出现在 for、while 或者 do while 循环中，包括嵌套在这些循环内部的块语句中。

### 12. goto 语句
goto 语句提供了函数内部的无条件跳转，实现从 goto 语句跳转到同一函数内某个带标号的语句。

### 13. try 块和异常处理
异常机制提供程序中错误检测与错误处理部分之间的通信。C++ 的异常处理中包括：
	1）throw 表达式，错误检测部分使用这种表达式来说明遇到了不可处理的错误。可以说，throw 引发了异常条件。
	2)try 块，错误处理部分使用它来处理异常。try 语句块以 try 关键字开始，并以一个或多个 catch 子句结束。在 try 块中执行的代码所抛出（throw）的异常，通常会被其中一个 catch 子句处理。由于它们“处理”异常，catch 子句也称为处理代码。
	3)由标准库定义的一组异常类，用来在 throw 和相应的 catch 之间传递有关的错误信息。
每一个标准库异常类都定义了名为 what 的成员函数。这个函数不需要参数，返回 C 风格字符串。
寻找处理代码的过程与函数调用链刚好相反。抛出一个异常时，首先要搜索的是抛出异常的函数。如果没有找到匹配的 catch，则终止这个函数的执行，并在调用这个函数的函数中寻找相配的 catch。如果仍然找到相应的处理代码，该函数同样要终止，搜索调用它的函数。如此类推，继续按执行路径回退，直到找到适当类型的 catch 为止。
如果不存在处理该异常的 catch 子句，程序的运行就要跳转到名为 terminate 的标准库函数，该函数在 exception 头文件中定义。这个标准库函数的行为依赖于系统，通常情况下，它的执行将导致程序非正常退出。
在程序中出现的异常，如果没有经 try 块定义，则都以相同的方式来处理：毕竟，如果没有任何 try 块，也就没有捕获异常的处理代码（catch 子句）。此时，如果发生了异常，系统将自动调用 terminate 终止程序的执行。
C++ 标准库定义了一组类，用于报告在标准库中的函数遇到的问题。程序员可在自己编写的程序中使用这些标准异常类。标准库异常类定义在四个头文件中：
	1) exception 头文件定义了最常见的异常类，它的类名是 exception。这个类只通知异常的产生，但不会提供更多的信息。
	2) stdexcept 头文件定义了几种常见的异常类
	3) new 头文件定义了 bad_alloc 异常类型，提供因无法分配内在而由 new抛出的异常。
	4) type_info 头文件定义了 bad_cast 异常类型

### 14. 使用预处理器进行调试
程序所包含的调试代码仅在开发过程中执行。当应用程序已经完成，并且准备提交时，就会将调试代码关闭。可使用 NDEBUG 预处理变量实现有条件的调试代码：
     int main()
     {
     #ifndef NDEBUG
     	cerr << "starting main" << endl;
     #endif
     // ...
预处理器还定义了其余四种在调试时非常有用的常量：
__FILE__ 文件名
__LINE__ 当前行号
__TIME__ 文件被编译的时间
__DATE__ 文件被编译的日期
另一个常见的调试技术是使用 NDEBUG 预处理变量以及 assert 预处理宏,assert 宏是在 cassert 头文件中定义的.
assert 宏需要一个表达式作为它的条件：
	assert(expr)
只要 NDEBUG 未定义，assert 宏就求解条件表达式 expr，如果结果为 false，assert 输出信息并且终止程序的执行。如果该表达式有一个非零（例如，true）值，则 assert 不做任何操作。
与异常不同（异常用于处理程序执行时预期要发生的错误），程序员使用 assert 来测试“不可能发生”的条件。例如，对于处理输入文本的程序，可以预测全部给出的单词都比指定的阈值长。那么程序可以包含这样一个语句：
     assert(word.size() > threshold);
在测试过程中，assert 等效于检验数据是否总是具有预期的大小。一旦开发和测试工作完成，程序就已经建立好，并且定义了 NDEBUG。在成品代码中，assert 语句不做任何工作，因此也没有任何运行时代价。当然，也不会引起任何运行时检查。assert 仅用于检查确实不可能的条件，这只对程序的调试有帮助，但不能用来代替运行时的逻辑检查，也不能代替对程序可能产生的错误的检测。

## 第七章 函数

### 1. 函数的定义
函数调用做了两件事情：用对应的实参初始化函数的形参，并将控制权转移给被调用函数。主调函数的执行被挂起，被调函数开始执行。函数的运行以形参的（隐式）定义和初始化开始。
与初始化式的类型必须与初始化对象的类型匹配一样，实参的类型也必须与其对应形参的类型完全匹配：实参必须具有与形参类型相同、或者能隐式转换为形参类型的数据类型。
函数不能返回另一个函数或者内置数组类型，但可以返回指向函数的指针，或指向数组元素的指针的指针.
在定义或声明函数时，没有显式指定返回类型是不合法的.
没有任何形参的函数可以用空形参表或含有单个关键字 void 的形参表来表示。例如，下面关于 process 的声明是等价的：
     void process() { /* ... */ }      // implicit void parameter list
     void process(void){ /* ... */ }  // equivalent declaration

### 2. 参数传递
形参的初始化与变量的初始化一样：如果形参具有非引用类型，则复制实参的值，如果形参为引用类型，则它只是实参的别名。
非引用形参表示对应实参的局部副本。对这类形参的修改仅仅改变了局部副本的值。一旦函数执行结束，这些局部变量的值也就没有了。
指针形参: 函数的形参可以是指针，此时将复制实参指针。与其他非引用类型的形参一样，该类形参的任何改变也仅作用于局部副本。如果函数将新指针赋给形参，主调函数使用的实参指针的值没有改变
如果函数形参是非 const 类型的指针，则函数可通过指针实现赋值，修改指针所指向对象的值. 如果保护指针指向的值，则形参需定义为指向 const 对象的指针：
     void use_ptr(const int *p)
     {
          // use_ptr may read but not write to *p
     }
可以将指向 const 对象的指针初始化为指向非 const 对象，但不可以让指向非 const 对象的指针向 const 对象。
const 形参: 在调用函数时，如果该函数使用非引用的非 const 形参，则既可给该函数传递 const 实参也可传递非 const 的实参。这种行为源于 const 对象的标准初始化规则。因为初始化复制了初始化式的值，所以可用 const 对象初始化非 const 对象，反之亦然。
如果将形参定义为非引用的 const 类型：
     void fcn(const int i) { /* fcn can read but not write to i */ }
则在函数中，不可以改变实参的局部副本。由于实参仍然是以副本的形式传递，因此传递给 fcn 的既可以是 const 对象也可以是非 const 对象。
尽管函数的形参是 const，但是编译器却将 fcn 的定义视为其形码被声明为普通的 int 型：
     void fcn(const int i) { /* fcn can read but not write to i */ }
     void fcn(int i) { /* ... */ }            // error: redefines fcn(int)
这种用法是为了支持对 C 语言的兼容，因为在 C 语言中，具有 const 形参或非 const 形参的函数并无区别。
复制实参的局限性: 复制实参并不是在所有的情况下都适合，不适宜复制实参的情况包括：
	1) 当需要在函数中修改实参的值时。
	2) 当需要以大型对象作为实参传递时。对实际的应用而言，复制对象所付出的时间和存储空间代价往往过大。
	3) 当没有办法实现对象的复制时。
对于上述几种情况，有效的解决办法是将形参定义为引用或指针类型。
引用形参: 引用形参直接关联到其所绑定的实参，而并非这些对象的副本。
引用形参的另一种用法是向主调函数返回额外的结果：函数只能返回单个值，但有些时候，函数有不止一个的内容需要返回。
如果使用引用形参的唯一目的是避免复制实参，则应将形参定义为 const 引用：
 	bool isShorter(const string &s1, const string &s2)
     {
         return s1.size() < s2.size();
     }
其每一个形参都是 const string 类型的引用。因为形参是引用，所以不复制实参。又因为形参是 const 引用，所以 isShorter 函数不能使用该引用来修改实参。
更灵活的指向 const 的引用：如果函数具有普通的非 const 引用形参，则显然不能通过 const 对象进行调用。毕竟，此时函数可以修改传递进来的对象，这样就违背了实参的 const 特性。但比较容易忽略的是，调用这样的函数时，传递一个右值或具有需要转换的类型的对象同样是不允许的：
     // function takes a non-const reference parameter
     int incr(int &val)
     {
         return ++val;
     }
     int main()
     {
         short v1 = 0;
         const int v2 = 42;
         int v3 = incr(v1);   // error: v1 is not an int
         v3 = incr(v2);       // error: v2 is const
         v3 = incr(0);        // error: literals are not lvalues
         v3 = incr(v1 + v2);  // error: addition doesn't yield an lvalue
         int v4 = incr(v3);   // ok: v3 is a non const object type int
     }
问题的关键是非 const 引用形参只能与完全同类型的非 const 对象关联。
应该将不需要修改的引用形参定义为 const 引用。普通的非 const 引用形参在使用时不太灵活。这样的形参既不能用 const 对象初始化，也不能用字面值或产生右值的表达式实参初始化。
传递指向指针的引用：
	void ptrswap(int *&v1, int *&v2)
     {
         int *tmp = v2;
         v2 = v1;
         v1 = tmp;
     }
形参int *&v1的定义应从右至左理解：v1 是一个引用，与指向 int 型对象的指针相关联。也就是说，v1 只是传递进 ptrswap 函数的任意指针的别名。
vector 和其他容器类型的形参：通常，函数不应该有 vector 或其他标准库容器类型的形参。调用含有普通的非引用 vector 形参的函数将会复制 vector 的每一个元素。
从避免复制 vector 的角度出发，应考虑将形参声明为引用类型。然而事实上，C++ 程序员倾向于通过传递指向容器中需要处理的元素的迭代器来传递容器：
     // pass iterators to the first and one past the last element to print
     void print(vector<int>::const_iterator beg, vector<int>::const_iterator end)
     {
         while (beg != end) {
             cout << *beg++;
             if (beg != end) cout << " "; // no space after last element
         }
         cout << endl;
     }
数组形参：数组有两个特殊的性质，影响我们定义和使用作用在数组上的函数：一是不能复制数组；二是使用数组名字时，数组名会自动转化为指向其第一个元素的指针。因为数组不能复制，所以无法编写使用数组类型形参的函数。因为数组会被自动转化为指针，所以处理数组的函数通常通过操纵指向数组指向数组中的元素的指针来处理数组。
如果要编写一个函数，输出 int 型数组的内容，可用下面三种方式指定数组形参：
     void printValues(int*) { /* ... */ }
     void printValues(int[]) { /* ... */ }
     void printValues(int[10]) { /* ... */ }
虽然不能直接传递数组，但是函数的形参可以写成数组的形式。虽然形参表示方式不同，但可将使用数组语法定义的形参看作指向数组元素类型的指针。上面的三种定义是等价的，形参类型都是 int*。
编译器忽略为任何数组形参指定的长度：当编译器检查数组形参关联的实参时，它只会检查实参是不是指针、指针的类型和数组元素的类型时是否匹配，而不会检查数组的长度。
如果形参是数组的引用，编译器不会将数组实参转化为指针，而是传递数组的引用本身。在这种情况下，数组大小成为形参和实参类型的一部分。编译器检查数组的实参的大小与形参的大小是否匹配：
	 void printValues(int (&arr)[10]) { /* ... */ }
     int main()
     {
         int i = 0, j[2] = {0, 1};
         int k[10] = {0,1,2,3,4,5,6,7,8,9};
         printValues(&i); // error: argument is not an array of 10 ints
         printValues(j);  // error: argument is not an array of 10 ints
         printValues(k);  // ok: argument is an array of 10 ints
         return 0;
     }
	&arr 两边的圆括号是必需的，因为下标操作符具有更高的优先级：
      	f(int &arr[10])     // error: arr is an array of references
     	f(int (&arr)[10]) // ok: arr is a reference to an array of 10 ints
多维数组的传递：除了第一维以外的所有维的长度都是元素类型的一部分，必须明确指定：
     void printValues(int (matrix*)[10], int rowSize);
上面的语句将 matrix 声明为指向含有 10 个 int 型元素的数组的指针。
也可以用数组语法定义多维数组。与一维数组一样，编译器忽略第一维的长度，所以最好不要把它包括在形参表内：
     void printValues(int matrix[][10], int rowSize);
这条语句把 matrix 声明为二维数组的形式。实际上，形参是一个指针，指向数组的数组中的元素。数组中的每个元素本身就是含有 10 个 int 型对象的数组。
有三种常见的编程技巧确保函数的操作不超出数组实参的边界：
	第一种方法是在数组本身放置一个标记来检测数组的结束。C 风格字符串就是采用这种方法的一个例子，它是一种字符数组，并且以空字符 null 作为结束的标记。处理 C 风格字符串的程序就是使用这个标记停止数组元素的处理。
	第二种方法是传递指向数组第一个和最后一个元素的下一个位置的指针。
	第三种方法是将第二个形参定义为表示数组的大小，这种用法在 C 程序和标准化之前的 C++ 程序中十分普遍。
含有可变形参的函数：在无法列举出传递给函数的所有实参的类型和数目时，可以使用省略符形参。省略符暂停了类型检查机制。它们的出现告知编译器，当调用函数时，可以有 0 或多个实参，而实参的类型未知。省略符形参有下列两种形式：
     void foo(parm_list, ...);
     void foo(...);
	第一种形式为特定数目的形参提供了声明。在这种情况下，当函数被调用时，对于与显示声明的形参相对应的实参进行类型检查，而对于与省略符对应的实参则暂停类型检查。在第一种形式中，形参声明后面的逗号是可选的。

### 3. return 语句
return 语句有两种形式：
     return;
     return expression;
不带返回值的 return 语句只能用于返回类型为 void 的函数。在返回类型为 void 的函数中，return 返回语句不是必需的，隐式的 return 发生在函数的最后一个语句完成时。
return 语句的第二种形式提供了函数的结果。任何返回类型不是 void 的函数必须返回一个值，而且这个返回值的类型必须和函数的返回类型相同，或者能隐式转化为函数的返回类型。
返回类型不是 void 的函数必须返回一个值，但此规则有一个例外情况：允许主函数 main 没有返回值就可结束。如果程序控制执行到主函数 main 的最后一个语句都还没有返回，那么编译器会隐式地插入返回 0 的语句。非 0 返回值的意义因机器不同而不同，为了使返回值独立于机器，cstdlib 头文件定义了两个预处理变量，分别用于表示程序运行成功和失败：
     #include <cstdlib>
     int main()
     {
         if (some_failure)
             return EXIT_FAILURE;
         else
             return EXIT_SUCCESS;
     }
返回引用：当函数返回引用类型时，没有复制返回值。相反，返回的是对象本身。
	const string &shorterString(const string &s1, const string &s2)
     {
         return s1.size() < s2.size() ? s1 : s2;
     }
形参和返回类型都是指向 const string 对象的引用，调用函数和返回结果时，都没有复制这些 string 对象。
理解返回引用至关重要的是：千万不能返回局部变量的引用。当函数执行完毕时，将释放分配给局部对象的存储空间。此时，对局部对象的引用就会指向不确定的内存
返回引用的函数返回一个左值。因此，这样的函数可用于任何要求使用左值的地方。
如果不希望引用返回值被修改，返回值应该声明为 const：
     const char &get_val(...
返回指向局部对象的指针也是错误的。一旦函数结束，局部对象被释放，返回的指针就变成了指向不再存在的对象的悬垂指针。

### 4. 函数声明
将提供函数声明头文件包含在定义该函数的源文件中，可使编译器能检查该函数的定义和声明时是否一致。特别地，如果函数定义和函数声明的形参列表一致，但返回类型不一致，编译器会发出警告或出错信息来指出这种差异。
默认实参：默认实参是一种虽然并不普遍、但在多数情况下仍然适用的实参值。调用函数时，可以省略有默认值的实参。编译器会为我们省略的实参提供默认值。默认实参是通过给形参表中的形参提供明确的初始值来指定的。程序员可为一个或多个形参定义默认值。但是，如果有一个形参具有默认实参，那么，它后面所有的形参都必须有默认实参。
调用包含默认实参的函数时，可以为该形参提供实参，也可以不提供。如果提供了实参，则它将覆盖默认的实参值；否则，函数将使用默认实参值。
函数调用的实参按位置解析，默认实参只能用来替换函数调用缺少的尾部实参。
设计带有默认实参的函数，其中部分工作就是排列形参，使最少使用默认实参的形参排在最前，最可能使用默认实参的形参排在最后。
既可以在函数声明也可以在函数定义中指定默认实参。但是，只能为一个形参指定默认实参一次。
通常，应在函数声明中指定默认实参，并将该声明放在合适的头文件中。如果在函数定义的形参表中提供默认实参，那么只有在包含该函数定义的源文件中调用该函数时，默认实参才是有效的。

### 5. 局部对象
静态局部对象：一个变量如果位于函数的作用域内，但生命期跨越了这个函数的多次调用，这种变量往往很有用。则应该将这样的对象定义为 static（静态的）。这种对象一旦被创建，在程序结束前都不会撤销。当定义静态局部对象的函数结束时，静态局部对象不会撤销。在该函数被多次调用的过程中，静态局部对象会持续存在并保持它的值。

### 6. 内联函数
inline 函数避免函数调用的开销：
	inline const string & shorterString(const string &s1, const string &s2)
     {
             return s1.size() < s2.size() ? s1 : s2;
     }
将函数指定为 inline 函数，（通常）就是将它在程序中每个调用点上“内联地”展开。假设我们将 shorterString 定义为内联函数，则调用：
         cout << shorterString(s1, s2) << endl;
在编译时将展开为：
         cout << (s1.size() < s2.size() ? s1 : s2) << endl;
从而消除了把 shorterString 写成函数的额外执行开销。
inline 说明对于编译器来说只是一个建议，编译器可以选择忽略这个。
内联函数应该在头文件中定义：inline 函数的定义对编译器而言必须是可见的，以便编译器能够在调用点内联展开该函数的代码。此时，仅有函数原型是不够的。把 inline 函数的定义放在头文件中，可以确保在调用函数时所使用的定义是相同的，并且保证在调用点该函数的定义对编译器可见。
在头文件中加入或修改 inline 函数时，使用了该头文件的所有源文件都必须重新编译。

### 7. 类的成员函数
函数原型必须在类中定义。但是，函数体则既可以在类中也可以在类外定义。
类的所有成员都必须在类定义的花括号里面声明，此后，就不能再为类增加任何成员。类的成员函数必须如声明的一般定义。类的成员函数既可以在类的定义内也可以在类的定义外定义。
编译器隐式地将在类内定义的成员函数当作内联函数。
成员函数含有额外的、隐含的形参：调用成员函数时，实际上是使用对象来调用的。
每个成员函数（除了static 成员函数外）都有一个额外的、隐含的形参 this。在调用成员函数时，形参 this 初始化为调用函数的对象的地址。
const 成员函数：const 改变了隐含的 this 形参的类型。在调用 total.same_isbn(trans) 时，隐含的 this 形参将是一个指向 total 对象的 const Sales_Item* 类型的指针。就像如下编写 same_isbn 的函数体一样：
     bool Sales_item::same_isbn(const Sales_item *const this, const Sales_item &rhs) const
     { return (this->isbn == rhs.isbn); }
用这种方式使用 const 的函数称为常量成员函数。由于 this 是指向 const 对象的指针，const 成员函数不能修改调用该函数的对象。
在成员函数中，不必显式地使用 this 指针来访问被调用函数所属对象的成员。对这个类的成员的任何没有前缀的引用，都被假定为通过指针 this 实现的引用。由于 this 指针是隐式定义的，因此不需要在函数的形参表中包含 this 指针，实际上，这样做也是非法的。但是，在函数体中可以显式地使用 this 指针。
构造函数的初始化列表：在冒号和花括号之间的代码称为构造函数的初始化列表。构造函数的初始化列表为类的一个或多个数据成员指定初值。它跟在构造函数的形参表之后，以冒号开关。构造函数的初始化式是一系列成员名，每个成员后面是括在圆括号中的初始值。多个成员的初始化用逗号分隔。
默认构造函数：如果没有为一个类显式定义任何构造函数，编译器将自动为这个类生成默认构造函数。它将依据如同变量初始化的规则初始化类中所有成员。对于具有类类型的成员，则会调用该成员所属类自身的默认构造函数实现初始化。内置类型成员的初值依赖于对象如何定义。如果对象在全局作用域中定义（即不在任何函数中）或定义为静态局部对象，则这些成员将被初始化为 0。如果对象在局部作用域中定义，则这些成员没有初始化。

### 8. 重载函数
出现在相同作用域中的两个函数，如果具有相同的名字而形参表不同，则称为重载函数。main 函数不能重载。
函数不能仅仅基于不同的返回类型而实现重载。
如果两个形参的差别只是一个使用 typedef 定义的类型名，而另一个使用 typedef 对应的原类型名，则这两个形参并无不同。
默认实参并没有改变形参的个数。
如果一对函数的区别仅在于是否将形参定义为 const，则仍为重复定义。值得注意的是，形参与 const 形参的等价性仅适用于非引用形参。有 const 引用形参的函数与有非 const 引用形参的函数是不同的。类似地，如果函数带有指向 const 类型的指针形参，则与带有指向相同类型的非 const 对象的指针形参的函数不相同。
如果局部地声明一个函数，则该函数将屏蔽而不是重载在外层作用域中声明的同名函数。由此推论，每一个版本的重载函数都应在同一个作用域中声明。一般来说，局部地声明函数是一种不明智的选择。函数的声明应放在头文件中。
重载确定的三个步骤：
	第一步是确定该调用所考虑的重载函数集合，该集合中的函数称为候选函数。候选函数是与被调函数同名的函数，并且在调用点上，它的声明可见。
	第二步是从候选函数中选择一个或多个函数，它们能够用该调用中指定的实参来调用。因此，选出来的函数称为可行函数。可行函数必须满足两个条件：第一，函数的形参个数与该调用的实参个数相同；第二，每一个实参的类型必须与对应形参的类型匹配，或者可被隐式转换为对应的形参类型。如果函数具有默认实参，则调用该函数时，所用的实参可能比实际需要的少。默认实参也是实参，在函数匹配过程中，它的处理方式与其他实参一样。
	第三步是确定与函数调用中使用的实际参数匹配最佳的可行函数。这个过程考虑函数调用中的每一个实参，选择对应形参与之最匹配的一个或多个可行函数。这里所谓“最佳”的原则是实参类型与形参类型越接近则匹配越佳。因此，实参类型与形参类型之间的精确类型匹配比需要转换的匹配好。
含有多个形参的重载确定：
	已有重载函数：f(int, int)和f(double, double)，调用f(42, 2.56);
	编译器通过依次检查每一个实参来决定哪个或哪些函数匹配最佳。如果有且仅有一个函数满足下列条件，则匹配成功：
	1）其每个实参的匹配都不劣于其他可行函数需要的匹配。
	2）至少有一个实参的匹配优于其他可行函数提供的匹配。
	如果在检查了所有实参后，仍找不到唯一最佳匹配函数，则该调用错误。编译器将提示该调用具有二义性。
	首先分析第一个实参，发现函数 f(int, int) 匹配精确。如果使之与第二个函数匹配，就必须将 int 型实参 42 转换为 double 型的值。通过内置转换的匹配“劣于”精确匹配。所以，如果只考虑这个形参，带有两个 int 型形参的函数比带有两个 double 型形参的函数匹配更佳。
	但是，当分析第二个实参时，有两个 double 型形参的函数为实参 2.56 提供了精确匹配。而调用两个 int 型形参的 f 函数版本则需要把 2.56 从 double 型转换为 int 型。所以只考虑第二个形参的话，函数 f(double, double) 匹配更佳。
	因此，这个调用有二义性：每个可行函数都对函数调用的一个实参实现更好的匹配。编译器将产生错误。解决这样的二义性，可通过显式的强制类型转换强制函数匹配：
     f(static_cast<double>(42), 2.56);  // calls f(double, double)
     f(42, static_cast<int>(2.56));     // calls f(int, int)
实参类型转换：为了确定最佳匹配，编译器将实参类型到相应形参类型转换划分等级。转换等级以降序排列如下：
	1）精确匹配。实参与形参类型相同。
	2）通过类型提升实现的匹配
	3）通过标准转换实现的匹配
	4）通过类类型转换实现的匹配
需要类型提升或转换的匹配：
	1）通过类型提升实现的转换优于其他标准转换。假设有两个函数，一个的形参为 int 型，另一个的形参则是 short 型。对于任意整型的实参值，int 型版本都是优于 short 型版本的较佳匹配，即使从形式上看 short 型版本的匹配较佳：
     void ff(int);
     void ff(short);
     ff('a');    // char promotes to int, so matches f(int)
字符字面值是 char 类型，char 类型可提升为 int 型。提升后的类型与函数 ff(int) 的形参类型匹配。char 类型同样也可转换为 short 型，但需要类型转换的匹配“劣于”需要类型提升的匹配。结果应将该调用解释为对 ff (int) 的调用。
	2）没有哪个标准转换比其他标准转换具有更高的优先级。
		extern void manip(long);
     	extern void manip(float);
     	manip(3.14);  // error: ambiguous call
	字面值常量 3.14 的类型为 double。这种类型既可转为 long 型也可转为 float 型。由于两者都是可行的标准转换，因此该调用具有二义性。
参数匹配和枚举类型：整数对象即使具有与枚举元素相同的值也不能用于调用期望获得枚举类型实参的函数。
	 enum Tokens {INLINE = 128, VIRTUAL = 129};
     void ff(Tokens);
     void ff(int);
     int main() {
         Tokens curTok = INLINE;
         ff(128);    // exactly matches ff(int)
         ff(INLINE); // exactly matches ff(Tokens)
         ff(curTok); // exactly matches ff(Tokens)
         return 0;
     }
虽然无法将整型值传递给枚举类型的形参，但可以将枚举值传递给整型形参。此时，枚举值被提升为 int 型或更大的整型。具体的提升类型取决于枚举成员的值。如果是重载函数，枚举值提升后的类型将决定调用哪个函数。
重载和 const 形参：仅当形参是引用或指针时，形参是否为 const 才有影响。可基于函数的引用形参是指向 const 对象还是指向非 const 对象，实现函数重载。将引用形参定义为 const 来重载函数是合法的，因为编译器可以根据实参是否为 const 确定调用哪一个函数：
     	Record lookup(Account&);
     	Record lookup(const Account&); // new function
     	const Account a(0);
     	Account b;
     	lookup(a);   // calls lookup(const Account&)
     	lookup(b);   // calls lookup(Account&)
	非 const 对象既可用于初始化 const 引用，也可用于初始化非 const 引用。但是，将 const 引用初始化为非 const 对象，需通过转换来实现，而非 const 形参的初始化则是精确匹配。
	可将 const 对象的地址值只传递给带有指向 const 对象的指针形参的函数。也可将指向非 const 对象的指针传递给函数的 const 或非 const 类型的指针形参。如果两个函数仅在指针形参时是否指向 const 对象上不同，则指向非 const 对象的指针形参对于指向非 const 对象的指针（实参）来说是更佳的匹配。重复强调，编译器可以判断：如果实参是 const 对象，则调用带有 const* 类型形参的函数；否则，如果实参不是 const 对象，将调用带有普通指针形参的函数。
	注意不能基于指针本身是否为 const 来实现函数的重载：
     f(int *);
     f(int *const); // redeclaration

### 9. 指向函数的指针
函数指针是指指向函数而非指向对象的指针。像其他指针一样，函数指针也指向某个特定的类型。函数类型由其返回类型以及形参表确定，而与函数名无关
用 typedef 简化函数指针的定义：
	typedef bool (*cmpFcn)(const string &, const string &);
函数指针只能通过同类型的函数或函数指针或 0 值常量表达式进行初始化或赋值。此时，直接引用函数名等效于在函数名上应用取地址操作符。指向不同函数类型的指针之间不存在转换：
通过指针调用函数：指向函数的指针可用于调用它所指向的函数。可以不需要使用解引用操作符，直接通过指针调用函数：
	 cmpFcn pf = lengthCompare; // 等价：cmpFcn pf = &lengthCompare;
     lengthCompare("hi", "bye"); // direct call
     pf("hi", "bye");            // equivalent call: pf1 implicitly dereferenced
     (*pf)("hi", "bye");         // equivalent call: pf1 explicitly dereferenced
函数指针形参：函数的形参可以是指向函数的指针。这种形参可以用以下两种形式编写：
	// third parameter is a function type and is automatically treated as a pointer to function
     void useBigger(const string &, const string &,
                    bool(const string &, const string &));
     // equivalent declaration: explicitly define the parameter as a pointer to function
     void useBigger(const string &, const string &,
                    bool (*)(const string &, const string &));
返回指向函数的指针：
	// ff is a function taking an int and returning a function pointer the function pointed to returns an int and takes an int* and an int
     int (*ff(int))(int*, int);
ff(int) 将 ff 声明为一个函数，它带有一个 int 型的形参。该函数返回int (*)(int*, int); 它是一个指向函数的指针，所指向的函数返回 int 型并带有两个分别是 int* 型和 int 型的形参。
使用 typedef 可使该定义更简明易懂：
     // PF is a pointer to a function returning an int, taking an int* and an int
     typedef int (*PF)(int*, int);
     PF ff(int);  // ff returns a pointer to function
允许将形参定义为函数类型，但函数的返回类型则必须是指向函数的指针，而不能是函数。具有函数类型的形参所对应的实参将被自动转换为指向相应函数类型的指针。但是，当返回的是函数时，同样的转换操作则无法实现：
     // func is a function type, not a pointer to function!
     typedef int func(int*, int);
     void f1(func); // ok: f1 has a parameter of function type
     func f2(int);  // error: f2 has a return type of function type
     func *f3(int); // ok: f3 returns a pointer to function type
指向重载函数的指针：
	C++ 语言允许使用函数指针指向重载的函数：
     extern void ff(vector<double>);
     extern void ff(unsigned int);
     // which function does pf1 refer to?
     void (*pf1)(unsigned int) = &ff; // ff(unsigned)
指针的类型必须与重载函数的一个版本精确匹配。如果没有精确匹配的函数，则对该指针的初始化或赋值都将导致编译错误：
     // error: no match: invalid parameter list
     void (*pf2)(int) = &ff;
     // error: no match: invalid return type
     double (*pf3)(vector<double>);
     pf3 = &ff;

## 第八章 标准 IO 库
### 1. 面向对象的标准库
IO 类型在三个独立的头文件中定义：iostream 定义读写控制窗口的类型，fstream 定义读写已命名文件的类型，而 sstream 所定义的类型则用于读写存储在内存中的 string 对象。在 fstream 和 sstream 里定义的每种类型都是从 iostream 头文件中定义的相关类型派生而来。istream 是 ifstream 和 istringstream 的基类，同时也是 iostream 的基类，而 iostream 则是 stringstream 和 fstream 的基类。
iostream.h
	istream 从流中读取
	ostream 写到流中去
	iostream 对流进行读写；从 istream 和 ostream 派生而来
fstream.h
	ifstream 从文件中读取；由 istream 派生而来
	ofstream 写到文件中去；由 ostream 派生而来
	fstream 读写文件；由 iostream 派生而来
sstream
	istringstream 从 string 对象中读取；由 istream 派生而来
	ostringstream 写到 string 对象中去；由 ostream 派生而来
	stringstream 对 string 对象进行读写；由 iostream 派生而来
标准库还定义了一组相关的类型，支持 wchar_t 类型。每个类都加上“w”前缀，以此与 char 类型的版本区分开来。于是，wostream、wistream 和 wiostream 类型从控制窗口读写 wchar_t 数据。标准库还定义了从标准输入输出读写宽字符的对象。这些对象加上“w”前缀，以此与 char 类型版本区分：wchar_t 类型的标准输入对象是 wcin；标准输出是 wcout；而标准错误则是 wcerr。
IO 对象不可复制或赋值:
	ofstream out1, out2;
    	out1 = out2;   // error: cannot assign stream objects
    	// print function: parameter is copied
    	ofstream print(ofstream);
    	out2 = print(out2);  // error: cannot copy stream objects
这个要求有两层特别重要的含义:
	1) 只有支持复制的元素类型可以存储在 vector 或其他容器类型里。由于流对象不能复制，因此不能存储在 vector（或其他）容器中（即不存在存储流对象的 vector 或其他容器）。
	2) 形参或返回类型也不能为流类型。如果需要传递或返回 IO 对象，则必须传递或返回指向该对象的指针或引用：
    ofstream &print(ofstream&);              // ok: takes a reference, no copy
    while (print(out2)) { /* ... */ } // ok: pass reference to out2
一般情况下，如果要传递 IO 对象以便对它进行读写，可用非 const 引用的方式传递这个流对象。对 IO 对象的读写会改变它的状态，因此引用必须是非 const 的。

### 2. 条件状态
IO 标准库管理一系列条件状态（condition state）成员，用来标记给定的 IO 对象是否处于可用状态，或者碰到了哪种特定的错误。
流必须处于无错误状态，才能用于输入或输出。检测流是否用的最简单的方法是检查其真值：
          if (cin)
               // ok to use cin, it is in a valid state
          while (cin >> word)
               // ok: read operation successful ...
if 语句直接检查流的状态，而 while 语句则检测条件表达式返回的流，从而间接地检查了流的状态。如果成功输入，则条件检测为 true。
所有流对象都包含一个条件状态成员，该成员由 setstate 和 clear 操作管理。这个状态成员为 iostate 类型，这是由各个 iostream 类分别定义的机器相关的整型。该状态成员以二进制位（bit）的形式使用.
每个 IO 类还定义了三个 iostate 类型的常量值，分别表示特定的位模式。这些常量值用于指出特定类型的 IO 条件，可与位操作符一起使用，以便在一次操作中检查或设置多个标志。badbit 标志着系统级的故障，如无法恢复的读写错误。如果出现了这类错误，则该流通常就不能再继续使用了。如果出现的是可恢复的错误，如在希望获得数值型数据时输入了字符，此时则设置 failbit 标志，这种导致设置 failbit 的问题通常是可以修正的。eofbit 是在遇到文件结束符时设置的，此时同时还设置了 failbit。
流的状态由 bad、fail、eof 和 good 操作提示。如果 bad、fail 或者 eof 中的任意一个为 true，则检查流本身将显示该流处于错误状态。类似地，如果这三个条件没有一个为 true，则 good 操作将返回 true。
clear 和 setstate 操作用于改变条件成员的状态。clear 操作将条件重设为有效状态。在流的使用出现了问题并做出补救后，如果我们希望把流重设为有效状态，则可以调用 clear 操作。使用 setstate 操作可打开某个指定的条件，用于表示某个问题的发生。除了添加的标记状态，setstate 将保留其他已存在的状态变量不变。
可以如下管理输入操作:
	int ival;
    // read cin and test only for EOF; loop is executed even if there are other IO failures
    while (cin >> ival, !cin.eof()) {
        if (cin.bad())         // input stream is corrupted; bail out
            throw runtime_error("IO stream corrupted");
        if (cin.fail()) {                        // bad input
            cerr<< "bad data, try again";        // warn the user
            cin.clear(istream::failbit);         // reset the stream
            continue;                            // get next input
        }
        // ok to process ival
    }
条件状态的访问: rdstate 成员函数返回一个 iostate 类型值，该值对应于流当前的整个条件状态：
     // remember current state of cin
     istream::iostate old_state = cin.rdstate();
     cin.clear();
     process_input();  // use cin
     cin.clear(old_state); // now reset cin to old state
多种状态的处理: is.setstate(ifstream::badbit | ifstream::failbit);

### 3. 输出缓冲区的管理
每个 IO 对象管理一个缓冲区，用于存储程序读写的数据。下面几种情况将导致缓冲区的内容被刷新，即写入到真实的输出设备或者文件：
	1)程序正常结束。作为 main 返回工作的一部分，将清空所有输出缓冲区。
	2)在一些不确定的时候，缓冲区可能已经满了，在这种情况下，缓冲区将会在写下一个值之前刷新。
	3)用操纵符显式地刷新缓冲区，例如行结束符 endl。
	4)在每次输出操作执行完后，用 unitbuf 操作符设置流的内部状态，从而清空缓冲区。
	5)可将输出流与输入流关联（tie）起来。在这种情况下，在读输入流时将刷新其关联的输出缓冲区。
除endl之外，C++ 语言还提供了另外两个类似的操纵符。第一个经常使用的 flush，用于刷新流，但不在输出中添加任何字符。第二个则是比较少用的 ends，这个操纵符在缓冲区中插入空字符 null，然后后刷新它：
    cout << "hi!" << flush;      // flushes the buffer; adds no data
    cout << "hi!" << ends;       // inserts a null, then flushes the buffer
    cout << "hi!" << endl;       // inserts a newline, then flushes the buffer
如果需要刷新所有输出，最好使用 unitbuf 操纵符。这个操纵符在每次执行完写操作后都刷新流：
    cout << unitbuf << "first" << " second" << nounitbuf;
等价于：
    cout << "first" << flush << " second" << flush;
nounitbuf 操纵符将流恢复为使用正常的、由系统管理的缓冲区刷新方式。
如果程序不正常结束，输出缓冲区将不会刷新。在尝试调试已崩溃的程序时，通常会根据最后的输出找出程序发生错误的区域。如果崩溃出现在某个特定的输出语句后面，则可知是在程序的这个位置之后出错。
如果仅因为缓冲区没有刷新，程序员将浪费大量的时间跟踪调试并没有执行的代码。基于这个原因，输出时应多使用 endl 而非 '\n'。使用 endl 则不必担心程序崩溃时输出是否悬而未决（即还留在缓冲区，未输出到设备中）。
将输入和输出绑在一起: 当输入流与输出流绑在一起时，任何读输入流的尝试都将首先刷新其输出流关联的缓冲区。标准库将 cout 与 cin 绑在一起，因此语句：
         cin >> ival;
导致 cout 关联的缓冲区被刷新。
交互式系统通常应确保它们的输入和输出流是绑在一起的。这样做意味着可以保证任何输出，包括给用户的提示，都在试图读之前输出。
tie 函数可用 istream 或 ostream 对象调用，使用一个指向 ostream 对象的指针形参。调用 tie 函数时，将实参流绑在调用该函数的对象上。如果一个流调用 tie 函数将其本身绑在传递给 tie 的 ostream 实参对象上，则该流上的任何 IO 操作都会刷新实参所关联的缓冲区。
    cin.tie(&cout);   // illustration only: the library ties cin and cout for us
    ostream *old_tie = cin.tie();
    cin.tie(0); // break tie to cout, cout no longer flushed when cin is read
    cin.tie(&cerr);   // ties cin and cerr, not necessarily a good idea!
    // ...
    cin.tie(0);       // break tie between cin and cerr
    cin.tie(old_tie); // restablish normal tie between cin and cout
一个 ostream 对象每次只能与一个 istream 对象绑在一起。如果在调用 tie 函数时传递实参 0，则打破该流上已存在的捆绑。

### 4. 文件的输入和输出
fstream 类型除了继承下来的行为外，还定义了两个自己的新操作—— open 和 close，以及形参为要打开的文件名的构造函数。fstream、ifstream 或 ofstream 对象可调用这些操作，而其他的 IO 类型则不能调用。
文件流对象的使用:
	ifstream infile;    // unbound input file stream
    ofstream outfile;   // unbound output file stream
	infile.open("in");   // open file named "in" in the current directory
    outfile.open("out"); // open file named "out" in the current directory
由于历史原因，IO 标准库使用 C 风格字符串而不是 C++ strings 类型的字符串作为文件名。在创建 fstream 对象时，如果调用 open 或使用文件名作初始化式，需要传递的实参应为 C 风格字符串，而不是标准库 strings 对象。程序常常从标准输入获得文件名。通常，比较好的方法是将文件名读入 string 对象，而不是 C 风格字符数组。假设要使用的文件名保存在 string 对象中，则可调用 c_str 成员获取 C 风格字符串。
	ifstream infile(ifile.c_str());
    ofstream outfile(ofile.c_str());
检查文件打开是否成功:
	if (!infile) {
        cerr << "error: unable to open input file: "
             << ifile << endl;
        return -1;
    }
将文件流与新文件重新捆绑: fstream 对象一旦打开，就保持与指定的文件相关联。如果要把 fstream 对象与另一个不同的文件关联，则必须先关闭（close）现在的文件，然后打开（open）另一个文件：要点是在尝试打开新文件之前，必须先关闭当前的文件流。open 函数会检查流是否已经打开。如果已经打开，则设置内部状态，以指出发生了错误。接下来使用文件流的任何尝试都会失败。
		ifstream infile("in");      // opens file named "in" for reading
     	infile.close();             // closes "in"
     	infile.open("next");        // opens file named "next" for reading
清除文件流的状态: 如果程序员需要重用文件流读写多个文件，必须在读另一个文件之前调用 clear 清除该流的状态。
文件模式: 在打开文件时，无论是调用 open 还是以文件名作为流初始化的一部分，都需指定文件模式（file mode）。每个 fstream 类都定义了一组表示不同模式的值，用于指定流打开的不同模式。与条件状态标志一样，文件模式也是整型常量，在打开指定文件时，可用位操作符设置一个或多个模式。文件流构造函数和 open 函数都提供了默认实参设置文件模式。默认值因流类型的不同而不同。此外，还可以显式地以模式打开文件。
		in打开文件做读操作
		out打开文件做写操作
		app在每次写之前找到文件尾
		ate打开文件后立即将文件定位在文件尾
		trunc打开文件时清空已存在的文件流
		binary以二进制模式进行 IO 操作
out、trunc 和 app 模式只能用于指定与 ofstream 或 fstream 对象关联的文件；in 模式只能用于指定与 ifstream 或 fstream 对象关联的文件。所有的文件都可以用 ate 或 binary 模式打开。ate 模式只在打开时有效：文件打开后将定位在文件尾。以 binary 模式打开的流则将文件以字节序列的形式处理，而不解释流中的字符。
默认时，与 ifstream 流对象关联的文件将以 in 模式打开，该模式允许文件做读的操作：与 ofstream 关联的文件则以 out 模式打开，使文件可写。以 out 模式打开的文件会被清空：丢弃该文件存储的所有数据。
对于用 ofstream 打开的文件，要保存文件中存在的数据，唯一方法是显式地指定 app 模式打开：
	ofstream appfile("file2", ofstream::app);
对同一个文件作输入和输出运算: fstream 对象既可以读也可以写它所关联的文件。fstream 如何使用它的文件取决于打开文件时指定的模式。
默认情况下，fstream 对象以 in 和 out 模式同时打开。当文件同时以 in 和 out 打开时不清空。如果打开 fstream 所关联的文件时，只使用 out 模式，而不指定 in 模式，则文件会清空已存在的数据。如果打开文件时指定了 trunc 模式，则无论是否同时指定了 in 模式，文件同样会被清空。下面的定义将 copyOut 文件同时以输入和输出的模式打开：t
    fstream inOut("copyOut", fstream::in | fstream::out);
打开模式的有效组合: 并不是所有的打开模式都可以同时指定。有些模式组合是没有意义的，例如同时以 in 和 trunc 模式打开文件，准备读取所生成的流，但却因为 trunc 操作而导致无数据可读。有效的模式组合及其含义:
	out打开文件做写操作，删除文件中已有的数据
	out | app打开文件做写操作，在文件尾写入
	out | trunc与 out 模式相同
	in打开文件做读操作
 	in | out打开文件做读、写操作，并定位于文件开头处
	in | out | trunc打开文件做读、写操作，删除文件中已有的数据
上述所有的打开模式组合还可以添加 ate 模式。对这些模式添加 ate 只会改变文件打开时的初始化定位，在第一次读或写之前，将文件定位于文件末尾处。
一个打开并检查输入文件的程序:
	ifstream& open_file(ifstream &in, const string &file)
    {
        in.close();     // close in case it was already open
        in.clear();     // clear any existing errors
        // if the open fails, the stream will be in an invalid state
        in.open(file.c_str()); // open the file we were given
        return in; // condition state is good if open succeeded
    }
由于不清楚流 in 的当前状态，因此首先调用 close 和 clear 将这个流设置为有效状态。然后尝试打开给定的文件。如果打开失败，流的条件状态将标志这个流是不可用的。最后返回流对象 in，此时，in 要么已经与指定文件绑定起来了，要么处于错误条件状态。

### 5. 字符串流
iostream 标准库支持内存中的输入／输出，只要将流与存储在程序内存中的 string 对象捆绑起来即可。此时，可使用 iostream 输入和输出操作符读写这个 string 对象。标准库定义了三种类型的字符串流：
	istringstream、ostringstream、stringstream；
sstream 类型除了继承的操作外，还各自定义了一个有 string 形参的构造函数，这个构造函数将 string 类型的实参复制给 stringstream 对象。对 stringstream 的读写操作实际上读写的就是该对象中的 string 对象。这些类还定义了名为 str 的成员，用来读取或设置 stringstream 对象所操纵的 string 值。
stringstream 特定的操作：
	stringstream strm；创建自由的 stringstream 对象
	stringstream strm(s)；创建存储 s 的副本的 stringstream 对象，其中 s 是 string 类型的对象
	strm.str()；返回 strm 中存储的 string 类型对象
 	strm.str(s)；将 string 类型的 s 复制给 strm，返回 void
stringstream对象的使用：
	string line, word;      // will hold a line and word from input, respectively
    while (getline(cin, line))   {            // read a line from the input into line
       // do per-line processing
       istringstream stream(line);            // bind to stream to the line we read
       while (stream >> word){          // read a word from line
           // do per-word processing
       }
    }
stringstream 提供的转换和／或格式化：stringstream 对象的一个常见用法是，需要在多种数据类型之间实现自动格式化时使用该类类型。例如，sstream 输入和输出操作可自动地把算术类型转化为相应的 string 表示形式，反过来也可以。
	int val1 = 512, val2 = 1024;
    ostringstream format_message;
    // ok: converts values to a string representation
    format_message << "val1: " << val1 << "\n"
                   << "val2: " << val2 << "\n";
	// str member obtains the string associated with a stringstream
   istringstream input_istring(format_message.str());
   string dump; // place to dump the labels from the formatted message
   // extracts the stored ascii values, converting back to arithmetic types
   input_istring >> dump >> val1 >> dump >> val2;
   cout << val1 << " " << val2 << endl;  // prints 512 1024
为了读取 input_string，必须把该 string 对象分解为若干个部分。我们要的是数值型数据；为了得到它们，必须读取（和忽略）处于所需数据周围的标号。
因为输入操作符读取的是有类型的值，因此读入的对象类型必须和由 stringstream 读入的值的类型一致。一般情况下，使用输入操作符读 string 时，空白符将会忽略。

## 第九章 顺序容器
顺序容器内的元素按其位置存储和访问。标准库定义了三种顺序容器类型：vector、list 和 deque。它们的差别在于访问元素的方式，以及添加或删除元素相关操作的运行代价。标准库还提供了三种容器适配器。实际上，适配器是根据原始的容器类型所提供的操作，通过定义新的操作接口，来适应基础的容器类型。顺序容器适配器包括 stack、queue 和 priority_queue 类型
### 1. 顺序容器的定义
容器构造函数：
	C<T> c;	创建一个名为 c 的空容器。C 是容器类型名，如 vector，T 是元素类型，如 int 或 string 适用于所有容器。
	C c(c2);	创建容器 c2 的副本 c；c 和 c2 必须具有相同的容器类型，并存放相同类型的元素。适用于所有容器。
	C c(b, e);	创建 c，其元素是迭代器 b 和 e 标示的范围内元素的副本。适用于所有容器。
	C c(n, t);	用 n 个值为 t 的元素创建容器 c，其中值 t 必须是容器类型 C 的元素类型的值，或者是可转换为该类型的值。只适用于顺序容器
	C c(n);	创建有 n 个值初始化（第 3.3.1 节）（value-initialized）元素的容器 c。只适用于顺序容器
将一个容器初始化为另一个容器的副本：当不使用默认构造函数，而是用其他构造函数初始化顺序容器时，必须指出该容器有多少个元素，并提供这些元素的初值。同时指定元素个数和初值的一个方法是将新创建的容器初始化为一个同类型的已存在容器的副本
	vector<int> ivec;
    vector<int> ivec2(ivec);   // ok: ivec is vector<int>
    list<int>   ilist(ivec);   // error: ivec is not list<int>
    vector<double> dvec(ivec); // error: ivec holds int not double
初始化为一段元素的副本：尽管不能直接将一种容器内的元素复制给另一种容器，但系统允许通过传递一对迭代器间接实现该实现该功能。使用迭代器时，不要求容器类型相同。容器内的元素类型也可以不相同，只要它们相互兼容，能够将要复制的元素转换为所构建的新容器的元素类型，即可实现复制。
迭代器标记了要复制的元素范围，这些元素用于初始化新容器的元素。迭代器标记出要复制的第一个元素和最后一个元素。采用这种初始化形式可复制不能直接复制的容器。更重要的是，可以实现复制其他容器的一个子序列：
     list<string> slist(svec.begin(), svec.end());
     vector<string>::iterator mid = svec.begin() + svec.size()/2;
     deque<string> front(svec.begin(), mid);
     deque<string> back(mid, svec.end());
指针就是迭代器，因此允许通过使用内置数组中的一对指针初始化容器：
     char *words[] = {"stately", "plump", "buck", "mulligan"};
     size_t words_size = sizeof(words)/sizeof(char *);
     list<string> words2(words, words + words_size);
	其中第二个指针提供停止复制的条件，其所指向的位置上存放的元素并没有复制。
分配和初始化指定数目的元素：创建顺序容器时，可显式指定容器大小和一个（可选的）元素初始化式。容器大小可以是常量或非常量表达式，元素初始化则必须是可用于初始化其元素类型的对象的值：
     const list<int>::size_type list_size = 64;
     list<string> slist(list_size, "eh?"); // 64 strings, each is eh?
不提供元素初始化式时，标准库将为该容器实现值初始化。采用这种类型的初始化，元素类型必须是内置或复合类型，或者是提供了默认构造函数的类类型。如果元素类型没有默认构造函数，则必须显式指定其元素初始化式。
	list<int> ilist(list_size); // 64 elements, each initialized to 0
	接受容器大小做形参的构造函数只适用于顺序容器，而关联容器不支持这种初始化。
容器内元素的类型约束：C++ 语言中，大多数类型都可用作容器的元素类型。容器元素类型必须满足以下两个约束：
	1）元素类型必须支持赋值运算。
	2）元素类型的对象必须可以复制。
	此外，关联容器的键类型还需满足其他的约束。
	大多数类型满足上述最低限度的元素类型要求。除了引用类型外，所有内置或复合类型都可用做元素类型。引用不支持一般意义的赋值运算，因此没有元素是引用类型的容器。IO 库类型不支持复制或赋值。因此，不能创建存放 IO 类型对象的容器。除输入输出（IO）标准库类型之外，所有其他标准库类型都是有效的容器元素类型。特别地，容器本身也满足上述要求，因此，可以定义元素本身就是容器类型的容器。
容器操作的特殊要求：支持复制和赋值功能是容器元素类型的最低要求。此外，一些容器操作对元素类型还有特殊要求。如果元素类型不支持这些特殊要求，则相关的容器操作就不能执行：我们可以定义该类型的容器，但不能使用某些特定的操作。例如，假设类 Foo 没有默认构造函数，但提供了需要一个 int 型形参的构造函数。现在，考虑下面的声明：
     vector<Foo> empty;     // ok: no need for element default constructor
     vector<Foo> bad(10);   // error: no default constructor for Foo
     vector<Foo> ok(10, 1); // ok: each element initialized to 1
	在描述容器操作时，我们应该留意（如果有的话）每个操作对元素类型的约束。
容器的容器：注意，在指定容器元素为容器类型时，必须如下使用空格：
     vector< vector<string> > lines; // ok: space required between close >
     vector< vector<string>> lines; // error: >> treated as shift operator
	必须用空格隔开两个相邻的 > 符号，以示这是两个分开的符号，否则，系统会认为 >> 是单个符号，为右移操作符，并导致编译时错误。 

### 2. 迭代器和迭代器范围
每种容器类型都提供若干共同工作的迭代器类型。与容器类型一样，所有迭代器具有相同的接口：如果某种迭代器支持某种操作，那么支持这种操作的其他迭代器也会以相同的方式支持这种操作。
常用迭代器运算：
	*iter	返回迭代器 iter 所指向的元素的引用
 	iter->mem对 iter 进行解引用，获取指定元素中名为 mem 的成员。等效于 (*iter).mem
 	++iter iter++给 iter 加 1，使其指向容器里的下一个元素
 	--iter iter--给 iter 减 1，使其指向容器里的前一个元素
 	iter1 == iter2  iter1 != iter2比较两个迭代器是否相等（或不等）。当两个迭代器指向同一个容器中的同一个元素，或者当它们都指向同一个容器的超出末端的下一位置时，两个迭代器相等
C++ 定义的容器类型中，只有 vector 和 deque 容器提供下面两种重要的运算集合：迭代器算术运算，以及使用除了 == 和 != 之外的关系操作符（>, >=, <, <=）来比较两个迭代器。这两种容器都支持通过元素位置实现的随机访问，因此它们的迭代器可以有效地实现算术和关系运算。
迭代器范围：C++ 语言使用一对迭代器标记迭代器范围（iterator range），这两个迭代器分别指向同一个容器中的两个元素或超出末端的下一位置，通常将它们命名为 first 和 last，或 beg 和 end，用于标记容器中的一段元素范围。
迭代器 first 和 last 如果满足以下条件，则可形成一个迭代器范围：
	1）它们指向同一个容器中的元素或超出末端的下一位置。
	2）如果这两个迭代器不相等，则对 first 反复做自增运算必须能够到达 last。换句话说，在容器中，last 绝对不能位于 first 之前。
	编译器自己不能保证上述要求。编译器无法知道迭代器所关联的是哪个容器，也不知道容器内有多少个元素。若不能满足上述要求，将导致运行时未定义的行为。
假设 first 和 last 标记了一个有效的迭代器范围，于是：
	1）当 first 与 last 相等时，迭代器范围为空；
	2）当 first 与不相等时，迭代器范围内至少有一个元素，而且 first 指向该区间中的第一元素。此外，通过若干次自增运算可以使 first 的值不断增大，直到 first == last 为止。
这两个性质意味着程序员可以安全地编写如下的循环，通过测试迭代器处理一段元素：
     while (first != last) {
         // safe to use *first because we know there is at least one element
         ++first;
     }
使迭代器失效的容器操作：任何指向已删除元素的迭代器都具有无效值，使用无效迭代器是没有定义的，可能会导致与悬垂指针相同的问题。无法检查迭代器是否有效，也无法通过测试来发现迭代器是否已经失效。使用迭代器时，通常可以编写程序使得要求迭代器有效的代码范围相对较短。然后，在该范围内，严格检查每一条语句，判断是否有元素添加或删除，从而相应地调整迭代器的值。

### 3.顺序容器的操作：
每种顺序容器都提供了一组有用的类型定义以及以下操作：
	1）在容器中添加元素。
	2）在容器中删除元素。
	3）设置容器大小。
	4）（如果有的话）获取容器内的第一个和最后一个元素。
容器定义的类型别名:
	size_type		无符号整型，足以存储此容器类型的最大可能容器长度
	iterator	此容器类型的迭代器类型
 	const_iterator		元素的只读迭代器类型
 	reverse_iterator	按逆序寻址元素的迭代器
 	const_reverse_iterator	元素的只读（不能写）逆序迭代器
 	difference_type	足够存储两个迭代器差值的有符号整型，可为负数
 	value_type	元素类型
 	reference	   元素的左值类型，是 value_type& 的同义词
 	const_reference	元素的常量左值类型，等效于 const value_type&
begin 和 end 成员: begin 和 end 操作产生指向容器内第一个元素和最后一个元素的下一位置的迭代器。这两个迭代器通常用于标记包含容器中所有元素的迭代器范围。如果容器是 const，则其返回类型要加上 const_ 前缀，也就是 const_iterator类型。
在顺序容器中添加元素的操作:
	c.push_back(t)	在容器 c 的尾部添加值为 t 的元素。返回 void 类型
 	c.push_front(t)	在容器 c 的前端添加值为 t 的元素。返回 void 类型,只适用于 list 和 deque 容器类型.
 	c.insert(p,t)	在迭代器 p 所指向的元素前面插入值为 t 的新元素。返回指向新添加元素的迭代器
 	c.insert(p,n,t)	在迭代器 p 所指向的元素前面插入 n 个值为 t 的新元素。返回 void 类型
 	c.insert(p,b,e)	在迭代器 p 所指向的元素前面插入由迭代器 b 和 e 标记的范围内的元素。返回 void 类型
容器元素都是副本: 在容器中添加元素时，系统是将元素值复制到容器里。类似地，使用一段元素初始化新容器时，新容器存放的是原始元素的副本。被复制的原始值与新容器中的元素各不相关，此后，容器内元素值发生变化时，被复制的原值不会受到影响，反之亦然。
添加或删除 deque 或 vector 容器内的元素都会导致存储的迭代器失效:
vector<int>::iterator first = v.begin(),
              	 last = v.end(); // cache end iterator
     // diaster: behavior of this loop is undefined
     while (first != last) {
         // do some processing
         // insert new value and reassign first, which otherwise would be invalid
         first = v.insert(first, 42);
         ++first;  // advance first just past the element we added
      }
	上述代码的行为未定义。在很多实现中，该段代码将导致死循环。问题在于这个程序将 end 操作返回的迭代器值存储在名为 last 的局部变量中。循环体中实现了元素的添加运算，添加元素会使得存储在 last 中的迭代器失效。该迭代器既没有指向容器 v 的元素，也不再指向 v 的超出末端的下一位置。
为了避免存储 end 迭代器，可以在每次做完插入运算后重新计算 end 迭代器值：
     // safer: recalculate end on each trip whenever the loop adds/erases elements
     while (first != v.end()) {
         // do some processing
         first = v.insert(first, 42); // insert new value
         ++first; // advance first just past the element we added
     }
关系操作符: 所有的容器类型都支持用关系操作符来实现两个容器的比较。比较的容器必须具有相同的容器类型，而且其元素类型也必须相同。容器的比较是基于容器内元素的比较。容器的比较使用了元素类型定义的同一个关系操作符：两个容器做 != 比较使用了其元素类型定义的 != 操作符。如果容器的元素类型不支持某种操作符，则该容器就不能做这种比较运算:
	1) 如果两个容器具有相同的长度而且所有元素都相等，那么这两个容器就相等；否则，它们就不相等。
	2) 如果两个容器的长度不相同，但较短的容器中所有元素都等于较长容器中对应的元素，则称较短的容器小于另一个容器。
	3) 如果两个容器都不是对文的初始子序列，则它们的比较结果取决于所比较的第一个不相等的元素。
容器大小的操作:
	c.size()	返回容器 c 中的元素个数。返回类型为 c::size_type
 	c.max_size()	返回容器 c 可容纳的最多元素个数，返回类型为 c::size_type
 	c.empty()	返回标记容器大小是否为 0 的布尔值
 	c.resize(n) 调整容器 c 的长度大小，使其能容纳 n 个元素，如果 n < c.size()，则删除多出来的元素；否则，添加采用值初始化的新元素
 	c.resize(n,t)	调整容器 c 的长度大小，使其能容纳 n 个元素。所有新添加的元素值都为 t
resize 操作可能会使迭代器失效。在 vector 或 deque 容器上做 resize 操作有可能会使其所有的迭代器都失效。对于所有的容器类型，如果 resize 操作压缩了容器，则指向已删除的元素迭代器失效。
访问元素:
	c.back()	返回容器 c 的最后一个元素的引用。如果 c 为空，则该操作未定义
 	c.front()	返回容器 c 的第一个元素的引用。如果 c 为空，则该操作未定义
 	c[n]	返回下标为 n 的元素的引用如果 n <0 或 n >= c.size()，则该操作未定义
		只适用于 vector 和 deque 容器
 	c.at(n) 返回下标为 n 的元素的引用。如果下标越界，则该操作未定义.只适用于 vector 和 deque 容器
	if (!ilist.empty()) {
         // val and val2 refer to the same element
         list<int>::reference val = *ilist.begin();
         list<int>::reference val2 = ilist.front();

         // last and last2 refer to the same element
         list<int>::reference last = *--ilist.end();
         list<int>::reference last2 = ilist.back(); }
删除元素:
	c.erase(p)	 删除迭代器 p 所指向的元素.返回一个迭代器，它指向被删除元素后面的元素。如果 p 指向容器内的最后一个元素，则返回的迭代器指向容器的超出末端的下一位置。如果 p 本身就是指向超出末端的下一位置的迭代器，则该函数未定义
 	c.erase(b,e) 删除迭代器 b 和 e 所标记的范围内所有的元素.返回一个迭代器，它指向被删除元素段后面的元素。如果 e 本身就是指向超出末端的下一位置的迭代器，则返回的迭代器也指向容器的超出末端的下一位置
 	c.clear()	删除容器 c 内的所有元素。返回 void
 	c.pop_back() 删除容器 c 的最后一个元素。返回 void。如果 c 为空容器，则该函数未定义
 	c.pop_front() 删除容器 c 的第一个元素。返回 void。如果 c 为空容器，则该函数未定义		
			只适用于 list 或 deque 容器
在删除元素之前，必须确保迭代器是不是 end 迭代器。使用 erase 操作删除单个必须确保元素确实存在——如果删除指向超出末端的下一位置的迭代器，那么 erase 操作的行为未定义。
erase、pop_front 和 pop_back 函数使指向被删除元素的所有迭代器失效。对于 vector 容器，指向删除点后面的元素的迭代器通常也会失效。而对于 deque 容器，如果删除时不包含第一个元素或最后一个元素，那么该 deque 容器相关的所有迭代器都会失效。
赋值:
	c1 = c2删除容器 c1 的所有元素，然后将 c2 的元素复制给 c1。c1 和 c2 的类型（包括容器类型和元素类型）必须相同赋值后，左右两边的容器相等：尽管赋值前两个容器的长度可能不相等，但赋值后两个容器都具有右操作数的长度。
	c1.swap(c2)	交换内容：调用完该函数后，c1 中存放的是 c2 原来的元素，c2 中存放的则是 c1 原来的元素。c1 和 c2 的类型必须相同。该函数的执行速度通常要比将 c2 复制到 c1 的操作快
 	c.assign(b,e)	重新设置 c 的元素：将迭代器 b 和 e 标记的范围内所有的元素复制到 c 中。b 和 e 必须不是指向 c 中元素的迭代器
	c.assign(n,t)	将容器 c 重新设置为存储 n 个值为 t 的元素
赋值和 assign 操作使左操作数容器的所有迭代器失效。swap 操作则不会使迭代器失效。完成 swap 操作后，尽管被交换的元素已经存放在另一容器中，但迭代器仍然指向相同的元素。
如果在不同（或相同）类型的容器内，元素类型不相同但是相互兼容，则其赋值运算必须使用 assign 函数。assign 操作允许我们将一个容器的元素赋给另一个不同类型的容器。例如，可通过 assign 操作实现将 vector 容器中一段 char* 类型的元素赋给 string 类型 list 容器。
由于 assign 操作首先删除容器中原来存储的所有元素，因此，传递给 assign 函数的迭代器不能指向调用该函数的容器内的元素。
swap 操作实现交换两个容器内所有元素的功能。要交换的容器的类型必须匹配：操作数必须是相同类型的容器，而且所存储的元素类型也必须相同。调用了 swap 函数后，右操作数原来存储的元素被存放在左操作数中，反之亦然。该操作不会删除或插入任何元素，而且保证在常量时间内实现交换。由于容器内没有移动任何元素，因此迭代器不会失效。它们指向同一元素，就像没作 swap 运算之前一样。虽然，在 swap 运算后，这些元素已经被存储在不同的容器之中了。例如，在做 swap 运算之前，有一个迭代器 iter 指向 svec1[3] 字符串；实现 swap 运算后，该迭代器则指向 svec2[3] 字符串（这是同一个字符串，只是存储在不同的容器之中而已）。

### 4. vector 容器的自增长
为了支持快速的随机访问，vector 容器的元素以连续的方式存放——每一个元素都紧挨着前一个元素存储。为了使 vector 容器实现快速的内存分配，其实际分配的容量要比当前所需的空间多一些。vector 容器预留了这些额外的存储区，用于存放新添加的元素。于是，不必为每个新元素重新分配容器。
capacity 和 reserve 成员: capacity 操作获取在容器需要分配更多的存储空间之前能够存储的元素总数，而 reserve 操作则告诉 vector 容器应该预留(即capacity的最小值)多少个元素的存储空间。每当 vector 容器不得不分配新的存储空间时，以加倍当前容量的分配策略实现重新分配。空 vector 容器的 size 是 0，而标准库显然将其 capacity 也设置为 0。当程序员在 vector 中插入元素时，容器的 size 就是所添加的元素个数，而其 capacity 则必须至少等于 size，但通常比 size 值更大。

### 5. 容器的选用
vector 和 deque 容器提供了对元素的快速随机访问，但付出的代价是，在容器的任意位置插入或删除元素，比在容器尾部插入和删除的开销更大。list 类型在任何位置都能快速插入和删除，但付出的代价是元素的随机访问开销较大。通常来说，除非找到选择使用其他容器的更好理由，否则 vector 容器都是最佳选择。
deque 容器同时提供了 list 和 vector 的一些性质：
	1)与 vector 容器一样，在 deque 容器的中间 insert 或 erase 元素效率比较低。
	2)不同于 vector 容器，deque 容器提供高效地在其首部实现 insert 和 erase 的操作，就像在容器尾部的一样。
	3)与 vector 容器一样而不同于 list 容器的是， deque 容器支持对所有元素的随机访问。
	4)在 deque 容器首部或尾部插入元素不会使任何迭代器失效，而首部或尾部删除元素则只会使指向被删除元素的迭代器失效。在 deque 容器的任何其他位置的插入和删除操作将使指向该容器元素的所有迭代器都失效。
一些选择容器类型的法则：
	1)如果程序要求随机访问元素，则应使用 vector 或 deque 容器。
	2)如果程序必须在容器的中间位置插入或删除元素，则应采用 list 容器。
	3)如果程序不是在容器的中间位置，而是在容器首部或尾部插入或删除元素，则应采用 deque 容器。
	4)如果只需在读取输入时在容器的中间位置插入元素，然后需要随机访问元素，则可考虑在输入时将元素读入到一个 list 容器，接着对此容器重新排序，使其适合顺序访问，然后将排序后的 list 容器复制到一个 vector 容器。
决定使用哪种容器可能要求剖析各种容器类型完成应用所要求的各类操作的性能。如果无法确定某种应用应该采用哪种容器，则编写代码时尝试只使用 vector 和 lists 容器都提供的操作：使用迭代器，而不是下标，并且避免随机访问元素。这样编写，在必要时，可很方便地将程序从使用 vector 容器修改为使用 list 的容器。

### 6. 再谈 string 类型
在某些方面，可将 string 类型视为字符容器。除了一些特殊操作，string 类型提供与 vector 容器相同的操作。string 类型与 vector 容器不同的是，它不支持以栈方式操纵容器：在 string 类型中不能使用 front、back 和 pop_back 操作。
string 类型提供容器操作意味着可将操纵 vector 对象的程序改写为操纵 string 对象。例如，以下程序使用迭代器将一个 string 对象的字符以每次一行的方式输出到标准输出设备：
     string s("Hiya!");
     string::iterator iter = s.begin();
     while (iter != s.end())
         cout << *iter++ << endl; // postfix increment: print old value
构造 string 对象的其他方法
	string s(cp, n) 创建一个 string 对象，它被初始化为 cp 所指向数组的前 n 个元素的副本
	string s(s2, pos2) 创建一个 string 对象，它被初始化为一个已存在的 string 对象 s2 中从下标 pos2 开始的字符的副本
	string s(s2, pos2, len2) 创建一个 string 对象，它被初始化为 s2 中从下标 pos2 开始的 len2 个字符的副本。如果 pos2 > s2.size()，则该操作未定义，无论 len2 的值是多少，最多只能复制 s2.size() - pos2 个字符.注意：n、len2 和 pos2 都是 unsigned 值
子串操作:
	s.substr(pos, n) 返回一个 string 类型的字符串，它包含 s 中从下标 pos 开始的 n 个字符
	s.substr(pos) 返回一个 string 类型的字符串，它包含从下标 pos 开始到 s 末尾的所有字符
	s.substr() 返回 s 的副本 
修改 string 对象的操作:
	s.append( args) 将 args 串接在 s 后面。返回 s 引用
	s.replace(pos, len, args) 删除 s 中从下标 pos 开始的 len 个字符，用 args 指定的字符替换之。返回 s 的引用.在这个版本中，args 不能为 b2，e2
	s.replace(b, e, args) 删除迭代器 b 和 e 标记范围内所有的字符，用 args 替换之。返回 s 的引用.在这个版本中，args 不能为 s2，pos2，len2
string 类型的查找操作:
	s.find( args)	在 s 中查找 args 的第一次出现
 	s.rfind( args)	在 s 中查找 args 的最后一次出现
 	s.find_first_of( args) 在 s 中查找 args 的任意字符的第一次出现
 	s.find_last_of( args) 在 s 中查找 args 的任意字符的最后一次出现
 	s.find_first_not_of( args) 在 s 中查找第一个不属于 args 的字符
 	s.find_last_not_of( args)	在 s 中查找最后一个不属于 args 的字符
string 类型 compare 操作:
	s.compare(s2)	比较 s 和 s2
 	s.compare(pos1, n1, s2)	让 s 中从 pos 下标位置开始的 n1 个字符与 s2 做比较
 	s.compare(pos1, n1, s2, pos2, n2)	让 s 中从 pos1 下标位置开始的 n1 个字符与 s2 中从 pos2 下标位置开始的 n2 个字符做比较
 	s.compare(cp)	比较 s 和 cp 所指向的以空字符结束的字符串
 	s.compare(pos1, n1, cp)	让 s 中从 pos1 下标位置开始的 n1 个字符与 cp 所指向的字符串做比较
 	s.compare(pos1, n1, cp, n2)	让 s 中从 pos1 下标位置开始的 n1 个字符与 cp 所指向的字符串的前 n2 个字符做比较

### 7. 容器适配器
除了顺序容器，标准库还提供了三种顺序容器适配器：queue、priority_queue 和 stack。适配器（adaptor）是标准库中通用的概念，包括容器适配器、迭代器适配器和函数适配器。本质上，适配器是使一事物的行为类似于另一事物的行为的一种机制。容器适配器让一种已存在的容器类型采用另一种不同的抽象类型的工作方式实现。例如，stack（栈）适配器可使任何一种顺序容器以栈的方式工作。
适配器通用的操作和类型:
	size_type	 一种类型，足以存储此适配器类型最大对象的长度
	value_type 元素类型
 	container_type 基础容器的类型，适配器在此容器类型上实现
	A a; 创建一个新空适配器，命名为 a
 	A a(c); 创建一个名为 a 的新适配器，初始化为容器 c 的副本
	关系操作符:所有适配器都支持全部关系操作符：==、 !=、 <、 <=、 >、 >=
使用适配器时，必须包含相关的头文件：
     #include <stack>    // stack adaptor
     #include <queue>    // both queue and priority_queue adaptors
所有适配器都定义了两个构造函数：默认构造函数用于创建空对象，而带一个容器参数的构造函数将参数容器的副本作为其基础值。例如，假设 deq 是 deque<int> 类型的容器，则可用 deq 初始化一个新的栈，如下所示：
     stack<int> stk(deq);      // copies elements from deq into stk
覆盖基础容器类型: 默认的 stack 和 queue 都基于 deque 容器实现，而 priority_queue 则在 vector 容器上实现。在创建适配器时，通过将一个顺序容器指定为适配器的第二个类型实参，可覆盖其关联的基础容器类型：
     // empty stack implemented on top of vector
     stack< string, vector<string> > str_stk;
     // str_stk2 is implemented on top of vector and holds a copy of svec
     stack<string, vector<string> > str_stk2(svec);
对于给定的适配器，其关联的容器必须满足一定的约束条件。stack 适配器所关联的基础容器可以是任意一种顺序容器类型。因此，stack 栈可以建立在 vector、list 或者 deque 容器之上。而 queue 适配器要求其关联的基础容器必须提供 push_front 运算，因此只能建立在 list 容器上，而不能建立在 vector 容器上。priority_queue 适配器要求提供随机访问功能，因此可建立在 vector 或 deque 容器上，但不能建立在 list 容器上。
两个相同类型的适配器可以做相等、不等、小于、大于、小于等于以及等于关系比较，只要基础元素类型支持等于和小于操作符既可。这些关系运算由元素依次比较来实现。第一对不相等的元素将决定两者之间的小于或大于关系。
栈容器适配器支持的操作:
	s.empty() 如果栈为空，则返回 true，否则返回 stack
 	s.size() 返回栈中元素的个数
 	s.pop() 删除栈顶元素的值，但不返回其值
 	s.top() 返回栈顶元素的值，但不删除该元素
 	s.push(item) 在栈顶压入新元素
尽管栈是以 deque 容器为基础实现的，但是程序员不能直接访问 deque 所提供的操作。例如，不能在栈上调用 push_back 函数，而是必须使用栈所提供的名为 push 的操作。
队列和优先级队列: priority_queue 允许用户为队列中存储的元素设置优先级。这种队列不是直接将新元素放置在队列尾部，而是放在比它优先级低的元素前面。标准库默认使用元素类型的 < 操作符来确定它们之间的优先级关系。

## 第十章 关联容器
关联容器和顺序容器的本质差别在于：关联容器通过键（key）存储和读取元素，而顺序容器则通过元素在容器中的位置顺序存储和访问元素。
### 1. 引言：pair 类型
	pair 包含两个数据值。与容器一样，pair 也是一种模板类型。但又与之前介绍的容器不同，在创建 pair 对象时，必须提供两个类型名：pair 对象所包含的两个数据成员各自对应的类型名字
	与其他标准库类型不同，对于 pair 类，可以直接访问其数据成员：其成员都是仅有的，分别命名为 first 和 second。只需使用普通的点操作符——成员访问标志即可访问其成员：
     string firstBook;
     if (author.first == "James" && author.second == "Joyce")
         firstBook = "Stephen Hero";
pairs 类型提供的操作:
	pair<T1, T2> p1; 创建一个空的 pair 对象，它的两个元素分别是 T1 和 T2 类型，采用值初始化
 	pair<T1, T2> p1(v1, v2); 创建一个 pair 对象，它的两个元素分别是 T1 和 T2 ，其中 first 成员初始化为 v1，而 second 成员初始化为 v2
	make_pair(v1, v2) 以 v1 和 v2 值创建一个新 pair 对象，其元素类型分别是 v1 和 v2 的类型
 	p1 < p2 两个 pair 对象之间的小于运算，其定义遵循字典次序：如果 p1.first < p2.first 或者 !(p2.first < p1.first) && p1.second < p2.second，则返回 true
 	p1 == p2如果两个 pair 对象的 first 和 second 成员依次相等，则这两个对象相等。该运算使用其元素的 == 操作符
	p.first 返回 p 中名为 first 的（公有）数据成员
 	p.second 返回 p 的名为 second 的（公有）数据成员

### 2. 关联容器
关联容器共享大部分——但并非全部——的顺序容器操作。关联容器不提供 front、 push_front、 pop_front、back、push_back 以及 pop_back 操作。“容器元素根据键的次序排列”这一事实就是一个重要的结论：在迭代遍历关联容器时，我们可确保按键的顺序的访问元素，而与元素在容器中的存放位置完全无关。

### 3. map 类型
map 是键－值对的集合。map 类型通常可理解为关联数组（associative array）：可使用键作为下标来获取一个值，正如内置数组类型一样。而关联的本质在于元素的值与某个特定的键相关联，而并非通过元素在数组中的位置来获取。
map 对象的定义: 要使用 map 对象，则必须包含 map 头文件。在定义 map 对象时，必须分别指明键和值的类型（value type）:
	map<k, v> m; 创建一个名为 m 的空 map 对象，其键和值的类型分别为 k 和 v
 	map<k, v> m(m2);创建 m2 的副本 m，m 与 m2 必须有相同的键类型和值类型
 	map<k, v> m(b, e);创建 map 类型的对象 m，存储迭代器 b 和 e 标记的范围内所有元素的副本。元素的类型必须能转换为 pair<const k, v>
键类型的约束: 在使用关联容器时，它的键不但有一个类型，而且还有一个相关的比较函数。默认情况下，标准库使用键类型定义的 < 操作符来实现键（key type）的比较。
所用的比较函数必须在键类型上定义严格弱排序（strict weak ordering）。所谓的严格弱排序可理解为键类型数据上的“小于”关系，虽然实际上可以选择将比较函数设计得更复杂。但无论这样的比较函数如何定义，当用于一个键与自身的比较时，肯定会导致 false 结果。此外，在比较两个键时，不能出现相互“小于”的情况，而且，如果 k1“小于”k2，k2“小于”k3，则 k1 必然“小于”k3。对于两个键，如果它们相互之间都不存在“小于”关系，则容器将之视为相同的键。用做 map 对象的键时，可使用任意一个键值来访问相应的元素。
对于键类型，唯一的约束就是必须支持 < 操作符，至于是否支持其他的关系或相等运算，则不作要求。
map 定义的类型: map 对象的元素是键－值对，也即每个元素包含两个部分：键以及由键关联的值。map 的 value_type 就反映了这个事实。value_type 是存储元素的键以及值的 pair 类型，而且键为 const。例如，word_count 数组的 value_type 为 pair<const string, int> 类型。
	map<K, V>::key_type 在 map 容器中，用做索引的键的类型
 	map<K, V>::mapped_type 在 map 容器中，键所关联的值的类型
 	map<K, V>::value_type 一个 pair 类型，它的 first 元素具有 const map<K, V>::key_type 类型，而 second 元素则为 map<K, V>::mapped_type 类型
map 迭代器进行解引用将产生 pair 类型的对象:
	map<string, int>::iterator map_it = word_count.begin();
     cout << map_it->first;                  // prints the key for this element
     cout << " " << map_it->second;          // prints the value of the element
     map_it->first = "new key";              // error: key is const
     ++map_it->second;     // ok: we can change value through an iterator
map 类额外定义了两种类型：key_type 和 mapped_type，以获得键或值的类型。如同顺序容器一样，可使用作用域操作符（scope operator）来获取类型成员，如 map<string, int>::key_type。
使用下标访问 map 对象:
如同其他下标操作符一样，map 的下标也使用索引（其实就是键）来获取该键所关联的值。如果该键已在容器中，则 map 的下标运算与 vector 的下标运算行为相同：返回该键所关联的值。只有在所查找的键不存在时，map 容器才为该键创建一个新的元素，并将它插入到此 map 对象中。此时，所关联的值采用值初始化：类类型的元素用默认构造函数初始化，而内置类型的元素初始化为 0。
	map <string, int> word_count; // empty map
    word_count["Anna"] = 1;
将发生以下事情：
	1)在 word_count 中查找键为 Anna 的元素，没有找到。
	2)将一个新的键－值对插入到 word_count 中。它的键是 const string 类型的对象，保存 Anna。而它的值则采用值初始化，这就意味着在本例中值为 0。
	3)将这个新的键－值对插入到 word_count 中。 
	4)读取新插入的元素，并将它的值赋为 1。 
有别于 vector 或 string 类型，map 下标操作符返回的类型与对 map 迭代器进行解引用获得的类型不相同。map 迭代器返回 value_type 类型的值——包含 const key_type 和 mapped_type 类型成员的 pair 对象；下标操作符则返回一个 mapped_type 类型的值。
下标行为的编程意义:
	// count number of times each word occurs in the input
     map<string, int> word_count; // empty map from string to int
     string word;
     while (cin >> word)
       ++word_count[word];
	这段程序创建一个 map 对象，用来记录每个单词出现的次数。while 循环每次从标准输入读取一个单词。如果这是一个新的单词，则在 word_count 中添加以该单词为索引的新元素。如果读入的单词已在 map 对象中，则将它所对应的值加 1。中最有趣的是，在单词第一次出现时，会在 word_count 中创建并插入一个以该单词为索引的新元素，同时将它的值初始化为 0。然后其值立即加 1，所以每次在 map 中添加新元素时，所统计的出现次数正好从 1 开始。
map::insert 的使用:
	m.insert(e)  e 是一个用在 m 上的 value_type 类型的值。如果键（e.first）不在 m 中，则插入一个值为 e.second 的新元素；如果该键在 m 中已存在，则保持 m 不变。该函数返回一个 pair 类型对象，包含指向键为 e.first 的元素的 map 迭代器，以及一个 bool 类型的对象，表示是否插入了该元素
 	m.insert(beg, end) beg 和 end 是标记元素范围的迭代器，其中的元素必须为 m.value_type 类型的键－值对。对于该范围内的所有元素，如果它的键在 m 中不存在，则将该键及其关联的值插入到 m。返回 void 类型
 	m.insert(iter, e)  e 是一个用在 m 上的 value_type 类型的值。如果键（e.first）不在 m 中，则创建新元素，并以迭代器 iter 为起点搜索新元素存储的位置。返回一个迭代器，指向 m 中具有给定键的元素
以 insert 代替下标运算:使用下标给 map 容器添加新元素时，元素的值部分将采用值初始化。通常，我们会立即为其赋值，其实就是对同一个对象进行初始化并赋值。而插入元素的另一个方法是：直接使用 insert 成员，其语法更紧凑：
     // if Anna not already in word_count, inserts new element with value 1
     word_count.insert(map<string, int>::value_type("Anna", 1));
	在添加新 map 元素时，使用 insert 成员可避免使用下标操作符所带来的副作用：不必要的初始化。
传递给 insert 的实参相当笨拙。可用两种方法简化：使用 make_pair:
     word_count.insert(make_pair("Anna", 1));
	或使用 typedef
     typedef map<string,int>::value_type valType;
     word_count.insert(valType("Anna", 1));
检测 insert 的返回值: 带有一个键－值 pair 形参的 insert 版本将返回一个值：包含一个迭代器和一个 bool 值的 pair 对象，其中迭代器指向 map 中具有相应键的元素，而 bool 值则表示是否插入了该元素。如果该键已在容器中，则其关联的值保持不变，返回的 bool 值为 true。在这两种情况下，迭代器都将指向具有给定键的元素。含有一个或一对迭代器形参的 insert 函数版本并不说明是否有或有多少个元素插入到容器中。
语法展开: pair<map<string, int>::iterator, bool> ret = word_count.insert(make_pair(word, 1));
	对这个表达式一步步地展开解释：
	1) ret 存储 insert 函数返回的 pair 对象。该 pair 的 first 成员是一个 map 迭代器，指向插入的键。
	2) ret.first 从 insert 返回的 pair 对象中获取 map 迭代器。
	3) ret.first->second 对该迭代器进行解引用，获得一个 value_type 类型的对象。这个对象同样是 pair 类型的，它的 second 成员即为我们所添加的元素的值部分。
	4) ++ret.first->second 实现该值的自增运算。
	归结起来，这个自增语句获取指向按 word 索引的元素的迭代器，并将该元素的值加 1。
查找并读取 map 中的元素:
	下标操作符给出了读取一个值的最简单方法：
     map<string,int> word_count;
     int occurs = word_count["foobar"];
	但是，使用下标存在一个很危险的副作用：如果该键不在 map 容器中，那么下标操作会插入一个具有该键的新元素。
不修改 map 对象的查询操作:
	m.count(k) 返回 m 中 k 的出现次数
 	m.find(k) 如果 m 容器中存在按 k 索引的元素，则返回指向该元素的迭代器。如果不存在，则返回超出末端迭代器.
从 map 对象中删除元素:
	m.erase(k) 删除 m 中键为 k 的元素。返回 size_type 类型的值，表示删除的元素个数
 	m.erase(p) 从 m 中删除迭代器 p 所指向的元素。p 必须指向 m 中确实存在的元素，而且不能等于 m.end()。返回 void
	m.erase(b, e) 从 m 中删除一段范围内的元素，该范围由迭代器对 b 和 e 标记。b 和 e 必须标记 m 中的一段有效范围：即 b 和 e 都必须指向 m 中的元素或最后一个元素的下一个位置。而且，b 和 e 要么相等（此时删除的范围为空），要么 b 所指向的元素必须出现在 e 所指向的元素之前。返回 void 类型
map 对象的迭代遍历: 在使用迭代器遍历 map 容器时，迭代器指向的元素按键的升序排列。
     map<string, int>::const_iterator map_it = word_count.begin();
     while (map_it != word_count.end()) {
         cout << map_it->first << " occurs "
              << map_it->second << " times" << endl;
         ++map_it; // increment iterator to denote the next element
     }

### 4. set 类型
set 容器只是单纯的键的集合, 除了两种例外情况，set 容器支持大部分的 map 操作. 两种例外包括：set 不支持下标操作符，而且没有定义 mapped_type 类型。在 set 容器中，value_type 不是 pair 类型，而是与 key_type 相同的类型。它们指的都是 set 中存储的元素类型。
与 map 容器一样，set 容器的每个键都只能对应一个元素。以一段范围的元素初始化 set 对象，或在 set 对象中插入一组元素时，对于每个键，事实上都只添加了一个元素：
     vector<int> ivec;
     for (vector<int>::size_type i = 0; i != 10; ++i) {
         ivec.push_back(i);
         ivec.push_back(i); // duplicate copies of each number
     }
     set<int> iset(ivec.begin(), ivec.end());
     cout << ivec.size() << endl;      // prints 20
     cout << iset.size() << endl;      // prints 10
与 map 容器的操作一样，带有一个键参数的 insert 版本返回 pair 类型对象，包含一个迭代器和一个 bool 值，迭代器指向拥有该键的元素，而 bool 值表明是否添加了元素。使用迭代器对的 insert 版本返回 void 类型。
	set<string> set1;         // empty set
     set1.insert("the");       // set1 now has one element
     set1.insert("and");       // set1 now has two elements
set 容器不提供下标操作符。为了通过键从 set 中获取元素，可使用 find 运算。如果只需简单地判断某个元素是否存在，同样可以使用 count 运算，返回 set 中该键对应的元素个数。
正如不能修改 map 中元素的键部分一样，set 中的键也为 const。在获得指向 set 中某元素的迭代器后，只能对其做读操作，而不能做写操作

### 5. multimap 和 multiset 类型
multiset 和 multimap 类型则允许一个键对应多个实例。multimap 和 multiset 类型与相应的单元素版本具有相同的头文件定义：分别是 map 和 set 头文件。
multimap 和 multiset 所支持的操作分别与 map 和 set 的操作相同，只有一个例外：multimap 不支持下标运算。为了顺应一个键可以对应多个值这一性质，map 和 multimap，或 set 和 multiset 中相同的操作都以不同的方式做出了一定的修改。在使用 multimap 或 multiset 时，对于某个键，必须做好处理多个值的准备，而非只有单一的值。
由于键不要求是唯一的，因此每次调用 insert 总会添加一个元素。// adds first element with key Barth
     authors.insert(make_pair(
       string("Barth, John"),
       string("Sot-Weed Factor")));
     // ok: adds second element with key Barth
     authors.insert(make_pair(
       string("Barth, John"),
       string("Lost in the Funhouse")));
带有一个键参数的 erase 版本将删除拥有该键的所有元素，并返回删除元素的个数。而带有一个或一对迭代器参数的版本只删除指定的元素，并返回 void 类型.
	multimap<string, string> authors;
     string search_item("Kazuo Ishiguro");
     // erase all elements with this key; returns number of elements removed
     multimap<string, string>::size_type cnt =
                               authors.erase(search_item);
关联容器 map 和 set 的元素是按顺序存储的。而 multimap 和 multset 也一样。因此，在 multimap 和 multiset 容器中，如果某个键对应多个实例，则这些实例在容器中将相邻存放。迭代遍历 multimap 或 multiset 容器时，可保证依次返回特定键所关联的所有元素。
在multimap 或 multiset中查找一个元素的过程较复杂：某键对应的元素可能出现多次。该问题可用三种策略解决。而且三种策略都基于一个事实——在 multimap 中，同一个键所关联的元素必然相邻存放。
	1）使用 find 和 count 操作：count 函数求出某键出现的次数，而 find 操作则返回一个迭代器，指向第一个拥有正在查找的键的实例：
     string search_item("Alain de Botton");
     typedef multimap<string, string>::size_type sz_type;
     sz_type entries = authors.count(search_item);
     multimap<string,string>::iterator iter = authors.find(search_item);
     for (sz_type cnt = 0; cnt != entries; ++cnt, ++iter) cout <<
            iter->second << endl; // print each title
	2）与众不同的面向迭代器的解决方案：另一个更优雅简洁的方法是使用两个未曾见过的关联容器的操作：lower_bound 和 upper_bound。以下这些操作适用于所有的关联容器，也可用于普通的 map 和 set 容器，但更常用于 multimap 和 multiset。所有这些操作都需要传递一个键，并返回一个迭代器：
	m.lower_bound(k) 返回一个迭代器，指向键不小于 k 的第一个元素
	m.upper_bound(k) 返回一个迭代器，指向键大于 k 的第一个元素
 	m.equal_range(k)	返回一个迭代器的 pair 对象，它的 first 成员等价于 m.lower_bound(k)。而 second 成员则等价于 m.upper_bound(k)
在同一个键上调用 lower_bound 和 upper_bound，将产生一个迭代器范围，指示出该键所关联的所有元素。如果该键在容器中存在，则会获得两个不同的迭代器：lower_bound 返回的迭代器指向该键关联的第一个实例，而 upper_bound 返回的迭代器则指向最后一个实例的下一位置。如果该键不在 multimap 中，这两个操作将返回同一个迭代器，指向依据元素的排列顺序该键应该插入的位置。这两个操作不会说明键是否存在，其关键之处在于返回值给出了迭代器范围。
这些操作返回的也可能是容器自身的超出末端迭代器。如果所查找的元素拥有 multimap 容器中最大的键，那么的该键上调用 upper_bound 将返回超出末端迭代器。如果所查找的键不存在，而且比 multimap 容器中所有的键都大，则 low_bound 也将返回超出末端迭代器。lower_bound 返回的迭代器不一定指向拥有特定键的元素。如果该键不在容器中，则 lower_bound 返回在保持容器元素顺序的前提下该键应被插入的第一个位置。
     typedef multimap<string, string>::iterator authors_it;
     authors_it beg = authors.lower_bound(search_item),
                end = authors.upper_bound(search_item);
     while (beg != end) {
         cout << beg->second << endl; // print each title
         ++beg;
     }
若该键没有关联的元素，则 lower_bound 和 upper_bound 返回相同的迭代器：都指向同一个元素或同时指向 multimap 的超出末端位置。它们都指向在保持容器元素顺序的前提下该键应被插入的位置。
	3）equal_range 函数返回存储一对迭代器的 pair 对象。如果该值存在，则 pair 对象中的第一个迭代器指向该键关联的第一个实例，第二个迭代器指向该键关联的最后一个实例的下一位置。如果找不到匹配的元素，则 pair 对象中的两个迭代器都将指向此键应该插入的位置。
	pair<authors_it, authors_it> pos = authors.equal_range(search_item);
     while (pos.first != pos.second) {
         cout << pos.first->second << endl; // print each title
         ++pos.first;
     }
### 6. 容器的综合应用：文本查询程序

## 第十一章 泛型算法
### 1. 概述
算法永不执行容器提供的操作：泛型算法本身从不执行容器操作，只是单独依赖迭代器和迭代器操作实现。算法基于迭代器及其操作实现，而并非基于容器操作。这个事实也许比较意外，但本质上暗示了：使用“普通”的迭代器时，算法从不修改基础容器的大小。正如我们所看到的，算法也许会改变存储在容器中的元素的值，也许会在容器内移动元素，但是，算法从不直接添加或删除元素。

### 2. 初窥算法
使用泛型算法必须包含 algorithm 头文件： #include <algorithm>
标准库还定义了一组泛化的算术算法（generalized numeric algorithm），其命名习惯与泛型算法相同。使用这些算法则必须包含 numeric 头文件： #include <numeric>
除了少数例外情况，所有算法都在一段范围内的元素上操作，我们将这段范围称为“输入范围（input range）”。带有输入范围参数的算法总是使用头两个形参标记该范围。这两个形参是分别指向要处理的第一个元素和最后一个元素的下一位置的迭代器。
不检查写入操作的算法：fill_n 函数带有的参数包括：一个迭代器、一个计数器以及一个值。该函数从迭代器指向的元素开始，将指定数量的元素设置为给定的值。fill_n 函数假定对指定数量的元素做写操作是安全的。初学者常犯的错误的是：在没有元素的空容器上调用 fill_n 函数（或者类似的写元素算法）。
     vector<int> vec; // empty vector
     // disaster: attempts to write to 10 (nonexistent) elements in vec
     fill_n(vec.begin(), 10, 0);
这个 fill_n 函数的调用将带来灾难性的后果。我们指定要写入 10 个元素，但这些元素却不存在——vec 是空的。其结果未定义，很可能导致严重的运行时错误。
插入迭代器：确保算法有足够的元素存储输出数据的一种方法是使用插入迭代器。插入迭代器是可以给基础容器添加元素的迭代器。通常，用迭代器给容器元素赋值时，被赋值的是迭代器所指向的元素。而使用插入迭代器赋值时，则会在容器中添加一个新元素，其值等于赋值运算的右操作数的值。
back_inserter 函数是迭代器适配器。与容器适配器一样，迭代器适配器使用一个对象作为实参，并生成一个适应其实参行为的新对象。在本例中，传递给 back_inserter 的实参是一个容器的引用。back_inserter 生成一个绑定在该容器上的插入迭代器。在试图通过这个迭代器给元素赋值时，赋值运算将调用 push_back 在容器中添加一个具有指定值的元素。使用 back_inserter 可以生成一个指向 fill_n 写入目标的迭代器：
     vector<int> vec; // empty vector
     // ok: back_inserter creates an insert iterator that adds elements to vec
     fill_n (back_inserter(vec), 10, 0); // appends 10 elements to vec
现在，fill_n 函数每写入一个值，都会通过 back_inserter 生成的插入迭代器实现。效果相当于在 vec 上调用 push_back，在 vec 末尾添加 10 个元素，每个元素的值都是 0。

### 3. 再谈迭代器
标准库所定义的迭代器不依赖于特定的容器。事实上，C++ 语言还提供了另外三种迭代器：
	1）插入迭代器：这类迭代器与容器绑定在一起，实现在容器中插入元素的功能。
	2）iostream 迭代器：这类迭代器可与输入或输出流绑定在一起，用于迭代遍历所关联的 IO 流。
	3）反向迭代器：这类迭代器实现向后遍历，而不是向前遍历。所有容器类型都定义了自己的 reverse_iterator 类型，由 rbegin 和 rend 成员函数返回。
	上述迭代器类型都在 iterator 头文件中定义。
插入迭代器：back_inserter 函数是一种插入器。插入器是一种迭代器适配器，带有一个容器参数，并生成一个迭代器，用于在指定容器中插入元素。通过插入迭代器赋值时，迭代器将会插入一个新的元素。C++ 语言提供了三种插入器，其差别在于插入元素的位置不同。
	1）back_inserter，创建使用 push_back 实现插入的迭代器。
	2）front_inserter，该函数将创建一个迭代器，调用它所关联的基础容器的 push_front 成员函数代替赋值操作。有当容器提供 push_front 操作时，才能使用 front_inserter。在 vector 或其他没有 push_front 运算的容器上使用 front_inserter，将产生错误。
	3）inserter，使用 insert 实现插入操作。除了所关联的容器外，inserter 还带有第二实参：指向插入起始位置的迭代器：inserter 函数总是在它的迭代器实参所标明的位置前面插入新元素。
     list<int>::iterator it = find (ilst.begin(), ilst.end(), 42);
     replace_copy (ivec.begin(), ivec.end(),inserter (ilst, it), 100, 0);
iostream 迭代器：istream_iterator 用于读取输入流，而 ostream_iterator 则用于写输出流。这些迭代器将它们所对应的流视为特定类型的元素序列。使用流迭代器时，可以用泛型算法从流对象中读数据（或将数据写到流对象中）。
iostream 迭代器的构造函数：
	istream_iterator<T> in(strm); 创建从输入流 strm 中读取 T 类型对象的 istream_iterator 对象
	istream_iterator<T> in; istream_iterator 对象的超出末端迭代器
 	ostream_iterator<T> in(strm);创建将 T 类型的对象写到输出流 strm 的 ostream_iterator 对象
 	ostream_iterator<T> in(strm, delim);创建将 T 类型的对象写到输出流 strm 的 ostream_iterator 对象，在写入过程中使用 delim 作为元素的分隔符。delim 是以空字符结束的字符数组
流迭代器只定义了最基本的迭代器操作：自增、解引用和赋值。此外，可比较两个 istream 迭代器是否相等（或不等）。而 ostream 迭代器则不提供比较运算。
流迭代器的定义：流迭代器都是类模板：任何已定义输入操作符（>> 操作符）的类型都可以定义 istream_iterator。类似地，任何已定义输出操作符（<< 操作符）的类型也可定义 ostream_iterator。在创建流迭代器时，必须指定迭代器所读写的对象类型：
     istream_iterator<int> cin_it(cin);    // reads ints from cin
     istream_iterator<int> end_of_stream;  // end iterator value
     // writes Sales_items from the ofstream named outfile
     // each element is followed by a space
     ofstream outfile;
     ostream_iterator<Sales_item> output(outfile, " ");
	ostream_iterator 对象必须与特定的流绑定在一起。在创建 istream_iterator 时，可直接将它绑定到一个流上。另一种方法是在创建时不提供实参，则该迭代器指向超出末端位置。ostream_iterator 不提供超出末端迭代器。在创建 ostream_iterator 对象时，可提供第二个（可选的）实参，指定将元素写入输出流时使用的分隔符。分隔符必须是 C 风格字符串。因为它是 C 风格字符串，所以必须以空字符结束；否则，其行为将是未定义的。
istream_iterator 对象上的操作：构造与流绑定在一起的 istream_iterator 对象时将对迭代器定位，以便第一次对该迭代器进行解引用时即可从流中读取第一个值。
使用 istream_iterator 对象将标准输入读到 vector 对象中。
     istream_iterator<int> in_iter(cin); 
     istream_iterator<int> eof;
     while (in_iter != eof)
             vec.push_back(*in_iter++);
ostream_iterator 对象的使用：可使用 ostream_iterator 对象将一个值序列写入流中，其操作的过程与使用迭代器将一组值逐个赋给容器中的元素相同：
     ostream_iterator<string> out_iter(cout, "\n");
     istream_iterator<string> in_iter(cin), eof;
     while (in_iter != eof)
        *out_iter++ = *in_iter++;
流迭代器的限制：
	1）不可能从 ostream_iterator 对象读入，也不可能写到 istream_iterator 对象中。
	2）一旦给 ostream_iterator 对象赋了一个值，写入就提交了。赋值后，没有办法再改变这个值。此外，ostream_iterator 对象中每个不同的值都只能正好输出一次。
	3）ostream_iterator 没有 -> 操作符。
与算法一起使用流迭代器：从标准输入读取一些数，再将读取的不重复的数写到标准输出：
     istream_iterator<int> cin_it(cin);    // reads ints from cin
     istream_iterator<int> end_of_stream;  // end iterator value
     vector<int> vec(cin_it, end_of_stream);
     sort(vec.begin(), vec.end());
     ostream_iterator<int> output(cout, " ");
     unique_copy(vec.begin(), vec.end(), output);
反向迭代器：反向迭代器是一种反向遍历容器的迭代器。也就是，从最后一个元素到第一个元素遍历容器。反向迭代器将自增（和自减）的含义反过来了：对于反向迭代器，++ 运算将访问前一个元素，而 -- 运算则访问下一个元素。
容器还定义了 rbegin 和 rend 成员，分别返回指向容器尾元素和首元素前一位置的反向迭代器。与普通迭代器一样，反向迭代器也有常量（const）和非常量（nonconst）类型。
	vector<int> vec;
     for (vector<int>::size_type i = 0; i != 10; ++i)
         vec.push_back(i); // elements are 0,1,2,...9
下面的 for 循环将以逆序输出这些元素：
     vector<int>::reverse_iterator r_iter;
     for (r_iter = vec.rbegin(); // binds r_iter to last element
          r_iter != vec.rend();  // rend refers 1 before 1st element
          ++r_iter)              // decrements iterator one element
         cout << *r_iter << endl;    // prints 9,8,7,...0
为了以降序排列 vector，只需向 sort 传递一对反向迭代器：
     // sorts vec in "normal" order
     sort(vec.begin(), vec.end());
     // sorts in reverse: puts smallest element at the end of vec
     sort(vec.rbegin(), vec.rend());
由于不能反向遍历流，因此流迭代器不能创建反向迭代器。
反向迭代器与其他迭代器之间的关系：反向迭代器用于表示范围，而所表示的范围是不对称的，这个事实可推导出一个重要的结论：使用普通的迭代器对反向迭代器进行初始化或赋值时，所得到的迭代器并不是指向原迭代器所指向的元素。[line.rbegin(), rcomma) 和 [rcomma.base(), line.end()) 标记的是 line 中的相同元素。
五种迭代器：算法要求的迭代器操作分为五个类别，分别对应五种迭代器：
	1）Input iterator（输入迭代器）读，不能写；只支持自增运算。要求在这个层次上提供支持的泛型算法包括 find 和 accumulate。标准库 istream_iterator 类型输入迭代器。
	2）Output iterator（输出迭代器）写，不能读；只支持自增运算。输出迭代器一般用作算法的第三个实参，标记起始写入的位置。
	3）Forward iterator（前向迭代器）读和写；只支持自增运算
	4）Bidirectional iterator（双向迭代器）读和写；支持自增和自减运算
	5）Random access iterator（随机访问迭代器）读和写；支持完整的迭代器算术运算
除了输出迭代器，其他类别的迭代器形成了一个层次结构：需要低级类别迭代器的地方，可使用任意一种更高级的迭代器。对于需要输入迭代器的算法，可传递前向、双向或随机访问迭代器调用该算法。调用需要随机访问迭代器的算法时，必须传递随机访问迭代器。map、set 和 list 类型提供双向迭代器，而 string、vector 和 deque 容器上定义的迭代器都是随机访问迭代器都是随机访问迭代器，用作访问内置数组元素的指针也是随机访问迭代器。istream_iterator 是输入迭代器，而 ostream_iterator 则是输出迭代器。
关联容器与算法：尽管 map 和 set 类型提供双向迭代器，但关联容器只能使用算法的一个子集。问题在于：关联容器的键是 const 对象。因此，关联容器不能使用任何写序列元素的算法。只能使用与关联容器绑在一起的迭代器来提供用于读操作的实参。在处理算法时，最好将关联容器上的迭代器视为支持自减运算的输入迭代器，而不是完整的双向迭代器。
C++ 标准为所有泛型和算术算法的每一个迭代器形参指定了范围最小的迭代器种类。例如，find（以只读方式单步遍历容器）至少需要一个输入迭代器。replace 函数至少需要一对前向迭代器。replace_copy 函数的头两个迭代器必须至少是前向迭代器，第三个参数代表输出目标，必须至少是输出迭代器。对于每一个形参，迭代器必须保证最低功能。将支持更少功能的迭代器传递给函数是错误的；而传递更强功能的迭代器则没问题。向算法传递无效的迭代器类别所引起的错误，无法保证会在编译时被捕获到。

### 4. 泛型算法的结构
算法最基本的性质是需要使用的迭代器种类。所有算法都指定了它的每个迭代器形参可使用的迭代器类型。
算法的形参模式：大多数算法采用下面四种形式之一：
	 alg (beg, end, other parms);
     alg (beg, end, dest, other parms);
     alg (beg, end, beg2, other parms);
     alg (beg, end, beg2, end2, other parms);
	调用这些算法时，必须确保输出容器有足够大的容量存储输出数据，这正是通常要使用插入迭代器或者 ostream_iterator 来调用这些算法的原因。如果使用容器迭代器调用这些算法，算法将假定容器里有足够多个需要的元素。
算法的命名规范：标准库使用一组相同的命名和重载规范。它们包括两种重要模式：第一种模式包括测试输入范围内元素的算法，第二种模式则应用于对输入范围内元素重新排序的算法。
很多算法通过检查其输入范围内的元素实现其功能。这些算法通常要用到标准关系操作符：== 或 <。其中的大部分算法会提供第二个版本的函数，允许程序员提供比较或测试函数取代操作符的使用。
检查指定值的算法默认使用 == 操作符。系统为这类算法提供另外命名的（而非重载的）版本，带有谓词函数形参。带有谓词函数形参的算法，其名字带有后缀 _if：
     find(beg, end, val);       // find first instance of val in the input range
     find_if(beg, end, pred);   // find first instance for which pred is true
上述两个算法都在输入范围内寻找指定元素的第一个实例。其中，find 算法查找一个指定的值，而 find_if 算法则用于查找一个使谓词函数 pred 返回非零值的元素。
区别是否实现复制的算法版本：无论算法是否检查它的元素值，都可能重新排列输入范围内的元素。在默认情况下，这些算法将重新排列的元素写回其输入范围。标准库也为这些算法提供另外命名的版本，将元素写到指定的输出目标。此版本的算法在名字中添加了 _copy 后缀：
     reverse(beg, end);
     reverse_copy(beg, end, dest);
第一个函数版本将自己的输入序列中的元素反向重排。而第二个版本，reverse_copy，则复制输入序列的元素，并将它们逆序存储到 dest 开始的序列中。

### 5. 容器特有的算法
list 容器上的迭代器是双向的，而不是随机访问类型。由于 list 容器不支持随机访问，因此，在此容器上不能使用需要随机访问迭代器的算法。这些算法包括 sort 及其相关的算法。还有一些其他的泛型算法，如 merge、remove、reverse 和 unique，虽然可以用在 list 上，但却付出了性能上的代价。如果这些算法利用 list 容器实现的特点，则可以更高效地执行。与其他顺序容器所支持的操作相比，标准库为 list 容器定义了更精细的操作集合，使它不必只依赖于泛型操作。
list 容器特有的操作：
	lst.merge(lst2) lst.merge(lst2, comp)：将 lst2 的元素合并到 lst 中。这两个 list 容器对象都必须排序。lst2 中的元素将被删除。合并后，lst2 为空。返回 void 类型。第一个版本使用 < 操作符，而第二个版本则使用 comp 指定的比较运算
	lst.remove(val) lst.remove_if(unaryPred)：调用 lst.erase 删除所有等于指定值或使指定的谓词函数返回非零值的元素。返回 void 类型
 	lst.reverse()：反向排列 lst 中的元素
 	lst.sort：对 lst 中的元素排序
 	lst.splice(iter, lst2)
 	lst.splice(iter, lst2, iter2)
 	lst.splice(iter, beg, end)：将 lst2 的元素移到 lst 中迭代器 iter 指向的元素前面。在 lst2 中删除移出的元素。第一个版本将 lst2 的所有元素移到 lst 中；合并后，lst2 为空。lst 和 lst2 不能是同一个 list 对象。第二个版本只移动 iter2 所指向的元素，这个元素必须是 lst2 中的元素。在这种情况中，lst 和 lst2 可以是同一个 list 对象。也就是说，可在一个 list 对象中使用 splice 运算移动一个元素。第三个版本移动迭代器 beg 和 end 标记的范围内的元素。beg 和 end 照例必须指定一个有效的范围。这两个迭代器可标记任意 list 对象内的范围，包括 lst。当它们指定 lst 的一段范围时，如果 iter 也指向这个范围的一个元素，则该运算未定义。
 	lst.unique() lst.unique(binaryPred)：调用 erase 删除同一个值的团结副本。第一个版本使用 == 操作符判断元素是否相等；第二个版本则使用指定的谓词函数实现判断
大多数 list 容器特有的算法类似于其泛型形式中已经见过的相应的算法，但并不相同。对于 list 对象，应该优先使用 list 容器特有的成员版本，而不是泛型算法。
list 容器特有的算法与其泛型算法版本之间有两个至关重要的差别。其中一个差别是 remove 和 unique 的 list 版本修改了其关联的基础容器：真正删除了指定的元素。例如，list::unique 将 list 中第二个和后续重复的元素删除出该容器。与对应的泛型算法不同，list 容器特有的操作能添加和删除元素。另一个差别是 list 容器提供的 merge 和 splice 运算会破坏它们的实参。使用 merge 的泛型算法版本时，合并的序列将写入目标迭代器指向的对象，而它的两个输入序列保持不变。但是，使用 list 容器的 merge 成员函数时，则会破坏它的实参 list 对象——当实参对象的元素合并到调用 merge 函数的 list 对象时，实参对象的元素被移出并删除。

## 第十二章 类
### 1. 类的定义和声明
成员函数：在类内部定义的函数默认为 inline，在类外部定义的成员函数必须指明它们是在类的作用域中。成员函数有一个附加的隐含实参，将函数绑定到调用函数的对象。将关键字 const 加在形参表之后，就可以将成员函数声明为常量：double avg_price() const; const 成员不能改变其所操作的对象的数据成员。const 必须同时出现在声明和定义中，若只出现在其中一处，就会出现一个编译时错误。
数据抽象是一种依赖于接口和实现分离的编程（和设计）技术。类设计者必须关心类是如何实现的，但使用该类的程序员不必了解这些细节。相反，使用一个类型的程序员仅需了解类型的接口，他们可以抽象地考虑该类型做什么，而不必具体地考虑该类型如何工作。
一个访问标号可以出现的次数通常是没有限制的。每个访问标号指定了随后的成员定义的访问级别。这个指定的访问级别持续有效，直到遇到下一个访问标号或看到类定义体的右花括号为止。
具体类型和抽象类型：并非所有类型都必须是抽象的。标准库中的 pair 类就是一个实用的、设计良好的具体类而不是抽象类。具体类会暴露而非隐藏其实现细节。pair 类型只是将两个数据成员捆绑成单个对象。在这种情况下，隐藏数据成员没有必要也没有明显的好处。在像 pair 这样的类中隐藏数据成员只会造成类型使用的复杂化。
数据抽象和封装提供了两个重要优点：
	1）避免类内部出现无意的、可能破坏对象状态的用户级错误。
	2）随时间推移可以根据需求改变或缺陷（bug）报告来完美类实现，而无须改变用户级代码。
同一类型的多个数据成员：如果一个类具有多个同一类型的数据成员，则这些成员可以在一个成员声明中指定，这种情况下，成员声明和普通变量声明是相同的：	
	class Screen {
     public:
         // interface member functions
     private:
         std::string contents;
         std::string::size_type height, width;
     };
类还可以定义自己的局部类型名字：	
	class Screen {
     public:
         // interface member functions
         typedef std::string::size_type index;
     private:
         std::string contents;
         index height, width;
     };
	类所定义的类型名遵循任何其他成员的标准访问控制。将 index 的定义放在类的 public 部分，是因为希望用户使用这个名字。Screen 类的使用者不必了解用 string 实现的底层细节。定义 index 来隐藏 Screen 的实现细节。将这个类型设为 public，就允许用户使用这个名字。
像其他 inline 一样，inline 成员函数的定义必须在调用该函数的每个源文件中是可见的。不在类定义体内定义的 inline 成员函数，其定义通常应放在有类定义的同一头文件中。
可以声明一个类而不定义它：
     class Screen; // declaration of the Screen class
这个声明，有时称为前向声明（forward declaraton），在程序中引入了类类型的 Screen。在声明之后、定义之前，类 Screen 是一个不完全类型（incompete type），即已知 Screen 是一个类型，但不知道包含哪些成员。不完全类型（incomplete type）只能以有限方式使用。不能定义该类型的对象。不完全类型只能用于定义指向该类型的指针及引用，或者用于声明（而不是定义）使用该类型作为形参类型或返回类型的函数。
在创建类的对象之前，必须完整地定义该类。必须定义类，而不只是声明类，这样，编译器就会给类的对象预定相应的存储空间。同样地，在使用引用或指针访问类的成员之前，必须已经定义类。
只有当类定义已经在前面出现过，数据成员才能被指定为该类类型。如果该类型是不完全类型，那么数据成员只能是指向该类类型的指针或引用。因为只有当类定义体完成后才能定义类，因此类不能具有自身类型的数据成员。然而，只要类名一出现就可以认为该类已声明。因此，类的数据成员可以是指向自身类型的指针或引用：
     class LinkScreen {
         Screen window;
         LinkScreen *next;
         LinkScreen *prev;
     };
定义对象时，将为其分配存储空间，但（一般而言）定义类型时不进行存储分配。
定义了一个类类型之后，可以按以下两种方式使用。
	1）将类的名字直接用作类型名。
	2）指定关键字 class 或 struct，后面跟着类的名字：
     Sales_item item1;       // default initialized object of type Sales_item
     class Sales_item item1; // equivalent definition of item1
类的定义分号结束。分号是必需的，因为在类定义之后可以接一个对象定义列表。定义必须以分号结束：
	class Sales_item { /* ... */ };
	class Sales_item { /* ... */ } accum, trans;

### 2. 隐含的 this 指针
成员函数具有一个附加的隐含形参，即指向该类对象的一个指针。这个隐含形参命名为 this，与调用成员函数的对象绑定在一起。成员函数不能定义 this 形参，而是由编译器隐含地定义。成员函数的函数体可以显式使用 this 指针，但不是必须这么做。如果对类成员的引用没有限定，编译器会将这种引用处理成通过 this 指针的引用。
尽管在成员函数内部显式引用 this 通常是不必要的，但有一种情况下必须这样做：当我们需要将一个对象作为整体引用而不是引用对象的一个成员时。最常见的情况是在这样的函数中使用 this：该函数返回对调用该函数的对象的引用。
返回 *this：
	Screen& Screen::set(char c)
     {
         contents[cursor] = c;
         return *this;
     }
该函数的返回类型是 Screen&，指明该成员函数返回对其自身类类型的对象的引用。每个函数都返回调用自己的那个对象。使用 this 指针来访问该对象。this 是一个指向非常量 Screen 的指针。
从 const 成员函数返回 *this：在普通的非 const 成员函数中，this 的类型是一个指向类类型的 const 指针。可以改变 this 所指向的值，但不能改变 this 所保存的地址。在 const 成员函数中，this 的类型是一个指向 const 类类型对象的 const 指针。既不能改变 this 所指向的对象，也不能改变 this 所保存的地址。不能从 const 成员函数返回指向类对象的普通引用。const 成员函数只能返回 *this 作为一个 const 引用。
基于 const 的重载：基于成员函数是否为 const，可以重载一个成员函数；同样地，基于一个指针形参是否指向 const，可以重载一个函数。const 对象只能使用 const 成员。非 const 对象可以使用任一成员，但非 const 版本是一个更好的匹配。
	class Screen {
     public:
         Screen& display(std::ostream &os)
                       { do_display(os); return *this; }
         const Screen& display(std::ostream &os) const
                       { do_display(os); return *this; }
    	…
      };
	当将 display 嵌入到一个长表达式中时，将调用非 const 版本。当我们 display 一个 const 对象时，就调用 const 版本：
     Screen myScreen(5,3);
     const Screen blank(5, 3);
     myScreen.set('#').display(cout); // calls nonconst version
     blank.display(cout);             // calls const version
可变数据成员：有时（但不是很经常），我们希望类的数据成员（甚至在 const 成员函数内）可以修改。这可以通过将它们声明为 mutable 来实现。可变数据成员（mutable data member）永远都不能为 const，甚至当它是 const 对象的成员时也如此。因此，const 成员函数可以改变 mutable 成员。要将数据成员声明为可变的，必须将关键字 mutable 放在成员声明之前：
     class Screen {
     public:
     // interface member functions
     private:
         mutable size_t access_ctr; // may change in a const members
         // other data members as before
      };
给 Screen 添加了一个新的可变数据成员 access_ctr。使用 access_ctr 来跟踪调用 Screen 成员函数的频繁程度：
     void Screen::do_display(std::ostream& os) const
     {
         ++access_ctr; // keep count of calls to any member function
         os << contents;
     }
尽管 do_display 是 const，它也可以增加 access_ctr。该成员是可变成员，所以，任意成员函数，包括 const 函数，都可以改变 access_ctr 的值。

### 3. 类作用域
	尽管成员是在类的定义体之外定义的，但成员定义就好像它们是在类的作用域中一样。出现在类的定义体之外的成员定义必须指明成员出现在哪个类中：在定义于类外部的成员函数中，形参表和函数体处于类作用域中。
	函数返回类型不一定在类作用域中：与形参类型相比，返回类型出现在成员名字前面。如果函数在类定义体之外定义，则用于返回类型的名字在类作用域之外。如果返回类型使用由类定义的类型，则必须使用完全限定名：
	class Screen {
     public:
         typedef std::string::size_type index;
         index get_cursor() const;
     };
     inline  Screen::index  Screen::get_cursor() const
     {
         return cursor;
     }
	如果在类定义体之外定义 get_cursor，则在函数名被处理之前，代码在不在类作用域内。当看到返回类型时，其名字是在类作用域之外使用。必须用完全限定的类型名 Screen::index 来指定所需要的 index 是在类 Screen 中定义的名字。
类成员声明的名字查找：检查出现在名字使用之前的类成员的声明。如果查找不成功，则检查包含类定义的作用域中出现的声明以及出现在类定义之前的声明。
	typedef double Money;
     class Account {
     public:
         Money balance() { return bal; }
     private:
         Money bal;
         // ...
     };
	在处理 balance 函数的声明时，编译器首先在类 Account 的作用域中查找 Money 的声明。编译器只考虑出现在 Money 使用之前的声明。因为找不到任何成员声明，编译器随后在全局作用域中查找 Money 的声明。只考虑出现在类 Account 的定义之前的声明。找到全局的类型别名 Money 的声明，并将它用作函数 balance 的返回类型和数据成员 bal 的类型。编译器按照成员声明在类中出现的次序来处理它们。通常，名字必须在使用之前进行定义。而且，一旦一个名字被用作类型名，该名字就不能被重复定义：
     typedef double Money;
     class Account {
     public:
         Money balance() { return bal; } // uses global definition of Money
     private:
         // error: cannot change meaning of Money
         typedef long double Money;
         Money bal;
         // ...
     };
	按以下方式确定在成员函数的函数体中用到的名字：首先检查成员函数局部作用域中的声明。如果在成员函数中找不到该名字的声明，则检查对所有类成员的声明。如果在类中找不到该名字的声明，则检查在此成员函数定义之前的作用域中出现的声明。

### 4. 构造函数
	构造函数不能声明为 const：const 构造函数是不必要的。创建类类型的 const 对象时，运行一个普通构造函数来初始化该 const 对象。构造函数的工作是初始化对象。不管对象是否为 const，都用一个构造函数来初始化化该对象。
	在构造函数初始化列表中没有显式提及的每个成员，使用与初始化变量相同的规则来进行初始化。运行该类型的默认构造函数，来初始化类类型的数据成员。内置或复合类型的成员的初始值依赖于对象的作用域：在局部作用域中这些成员不被初始化，而在全局作用域中它们被初始化为 0。
	在构造函数初始化列表中初始化成员，还是在构造函数函数体中对它们赋值，不同之外在于：使用构造函数初始化列表的版本初始化数据成员，没有定义初始化列表的构造函数版本在构造函数函数体中对数据成员赋值。这个区别的重要性取决于数据成员的类型。
构造函数初始化只在构造函数的定义中而不是声明中指定。
	使用构造函数初始化列表能够使得省略初始化列表在构造函数的函数体内对数据成员赋值是合法的：
     Sales_item::Sales_item(const string &book)
     {
         isbn = book; // no constructor initializer
         units_sold = 0;
         revenue = 0.0;
     }
	这个构造函数隐式使用默认的 string 构造函数来初始化 isbn。执行构造函数的函数体时，isbn 成员已经有值了。该值被构造函数函数体中的赋值所覆盖。
	有些成员必须在构造函数初始化列表中进行初始化。对于这样的成员，在构造函数函数体中对它们赋值不起作用。没有默认构造函数的类类型的成员，以及 const 或引用类型的成员，不管是哪种类型，都必须在构造函数初始化列表中进行初始化。
	可以初始化 const 对象或引用类型的对象，但不能对它们赋值。在开始执行构造函数的函数体之前，要完成初始化。初始化 const 或引用类型数据成员的唯一机会是构造函数初始化列表中。
下面的构造函数是错误的：
     class ConstRef {
     public:
         ConstRef(int ii);
     private:
         int i;
         const int ci;
         int &ri;
     };
     // no explicit constructor initializer: error ri is uninitialized
     ConstRef::ConstRef(int ii)
     {              // assignments:
          i = ii;   // ok
          ci = ii;  // error: cannot assign to a const
          ri = i;   // assigns to ri which was not bound to an object
     }
编写该构造函数的正确方式为
     // ok: explicitly initialize reference and const members
     ConstRef::ConstRef(int ii): i(ii), ci(i), ri(ii) { }
必须对任何 const 或引用类型成员以及没有默认构造函数的类类型的任何成员使用初始化式。当类成员需要使用初始化列表时，通过常规地使用构造函数初始化列表，就可以避免发生编译时错误。
	构造函数初始化列表仅指定用于初始化成员的值，并不指定这些初始化执行的次序。成员被初始化的次序就是定义成员的次序。初始化的次序常常无关紧要。然而，如果一个成员是根据其他成员而初始化，则成员初始化的次序是至关重要的。
	初始化类类型的成员时，要指定实参并传递给成员类型的一个构造函数。可以使用该类型的任意构造函数。可以将 isbn 初始化为由 10 个 9 构成的串：
     // alternative definition for Sales_item default constructor
     Sales_item(): isbn(10, '9'), units_sold(0), revenue(0.0) {}
合成的默认构造函数（synthesized default constructor）使用与变量初始化相同的规则来初始化成员。具有类类型的成员通过运行各自的默认构造函数来进行初始化。内置和复合类型的成员，如指针和数组，只对定义在全局作用域中的对象才初始化。当对象定义在局部作用域中时，内置或复合类型的成员不进行初始化。只有当一个类没有定义构造函数时，编译器才会自动生成一个默认构造函数。
	假定有一个 NoDefault 类，它没有定义自己的默认构造函数，却有一个接受一个 string 实参的构造函数。因为该类定义了一个构造函数，因此编译器将不合成默认构造函数。NoDefault 没有默认构造函数，意味着：
	1.具有 NoDefault 成员的每个类的每个构造函数，必须通过传递一个初始的 string 值给 NoDefault 构造函数来显式地初始化 NoDefault 成员。
	2.编译器将不会为具有 NoDefault 类型成员的类合成默认构造函数。如果这样的类希望提供默认构造函数，就必须显式地定义，并且默认构造函数必须显式地初始化其 NoDefault 成员。
	3.类型不能用作动态分配数组的元素类型。
	4.类型的静态分配数组必须为每个元素提供一个显式的初始化式。
	5.如果有一个保存 NoDefault 对象的容器，例如 vector，就不能使用接受容器大小而没有同时提供一个元素初始化式的构造函数。
使用默认构造函数：
	Sales_item myobj();   // ok: but defines a function, not an object
    if (myobj.same_isbn(Primer_3rd_ed))   // error: myobj is a function
	使用默认构造函数定义一个对象的正确方式是去掉最后的空括号：
     // ok: defines a class object ...
     Sales_item myobj;
	另一方面，下面这段代码也是正确的：
     // ok: create an unnamed, empty Sales_itemand use to initialize myobj
     Sales_item myobj = Sales_item();
	在这里，创建并初始化一个 Sales_item 对象，然后用它来按值初始化 myobj。编译器通过运行 Sales_item 的默认构造函数来按值初始化一个 Sales_item
	可以用单个实参来调用的构造函数定义了从形参类型到该类类型的一个隐式转换：
	class Sales_item {
     public:
         // default argument for book is the empty string
         Sales_item(const std::string &book = ""):
                   isbn(book), units_sold(0), revenue(0.0) { }
         Sales_item(std::istream &is);
         // as before
      };
	这里的每个构造函数都定义了一个隐式转换。因此，在期待一个 Sales_item 类型对象的地方，可以使用一个 string 或一个 istream：
     string null_book = "9-999-99999-9";
     item.same_isbn(null_book); 
	 item.same_isbn(cin);
	新生成的（临时的）Sales_item 被传递给 same_isbn。一旦 same_isbn 结束，就不能再访问它。
可以通过将构造函数声明为 explicit，来防止在需要隐式转换的上下文中使用构造函数：
	class Sales_item {
     public:
         // default argument for book is the empty string
         explicit Sales_item(const std::string &book = ""):
                   isbn(book), units_sold(0), revenue(0.0) { }
         explicit Sales_item(std::istream &is);
         // as before
     };
	explicit 关键字只能用于类内部的构造函数声明上。在类的定义体外部所做的定义上不再重复它：
     // error: explicit allowed only on constructor declaration in class header
     explicit Sales_item::Sales_item(istream& is)
     {
         is >> *this; // uses Sales_iteminput operator to read the members
     }
	现在，两个构造函数都不能用于隐式地创建对象。前两个使用都不能编译：
     item.same_isbn(null_book); // error: string constructor is explicit
     item.same_isbn(cin);       // error: istream constructor is explicit
为转换而显式地使用构造函数：只要显式地按下面这样做，就可以用显式的构造函数来生成转换：
     string null_book = "9-999-99999-9";
     // ok: builds a Sales_itemwith 0 units_soldand revenue from
     // and isbn equal to null_book
     item.same_isbn(Sales_item(null_book));
显式使用构造函数只是中止了隐式地使用构造函数。任何构造函数都可以用来显式地创建临时对象。
通常，除非有明显的理由想要定义隐式转换，否则，单形参构造函数应该为 explicit。将构造函数设置为 explicit 可以避免错误，并且当转换有用时，用户可以显式地构造对象。
直接初始化简单的非抽象类的数据成员仍是可能的。对于没有定义构造函数并且其全体数据成员均为 public 的类，可以采用与初始化数组元素相同的方式初始化其成员：
     struct Data {
         int ival;
         char *ptr;
     };
     // val1.ival = 0; val1.ptr = 0
     Data val1 = { 0, 0 };
     // val2.ival = 1024;
     // val2.ptr = "Anna Livia Plurabelle"
     Data val2 = { 1024, "Anna Livia Plurabelle" };
这种形式的初始化从 C 继承而来，支持与 C 程序兼容。显式初始化类类型对象的成员有三个重大的缺点。
	1）要求类的全体数据成员都是 public。
	2）将初始化每个对象的每个成员的负担放在程序员身上。这样的初始化是乏味且易于出错的，因为容易遗忘初始化式或提供不适当的初始化式。
	3）如果增加或删除一个成员，必须找到所有的初始化并正确更新。

### 5. 友元
	友元机制允许一个类将对其非公有成员的访问权授予指定的函数或类。友元的声明以关键字 friend 开始。它只能出现在类定义的内部。友元声明可以出现在类中的任何地方：友元不是授予友元关系的那个类的成员，所以它们不受声明出现部分的访问控制影响。
	友元可以是普通的非成员函数，或前面定义的其他类的成员函数，或整个类。将一个类设为友元，友元类的所有成员函数都可以访问授予友元关系的那个类的非公有成员。
当我们将成员函数声明为友元时，函数名必须用该函数所属的类名字加以限定：
	class Screen {
         // Window_Mgrmust be defined before class Screen
         friend Window_Mgr&
             Window_Mgr::relocate(Window_Mgr::index,
                                  Window_Mgr::index,
                                  Screen&);
         // ...restofthe Screen class
     };
为了正确地构造类，需要注意友元声明与友元定义之间的互相依赖。在前面的例子中，类 Window_Mgr 必须先定义。否则，Screen 类就不能将一个 Window_Mgr 函数指定为友元。然而，只有在定义类 Screen 之后，才能定义 relocate 函数——毕竟，它被设为友元是为了访问类 Screen 的成员。更一般地讲，必须先定义包含成员函数的类，才能将成员函数设为友元。另一方面，不必预先声明类和非成员函数来将它们设为友元
友元声明将已命名的类或非成员函数引入到外围作用域中。此外，友元函数可以在类的内部定义，该函数的作用域扩展到包围该类定义的作用域。
	class X {
         friend class Y;
         friend void f() { /* ok to define friend function in the class body */ }
     };
     class Z {
         Y *ymem; // ok: declaration for class Y introduced by friend in X
         void g() { return ::f(); } // ok: declaration of f introduced by X
     };

### 6. static 类成员
类也可以定义 static 成员函数。static 成员函数没有 this 形参，它可以直接访问所属类的 static 成员，但不能直接使用非 static 成员。
使用类的 static 成员的优点： 
	1）static 成员的名字是在类的作用域中，因此可以避免与其他类的成员或全局对象名字冲突。
	2）可以实施封装。static 成员可以是私有成员，而全局对象不可以。
	3）通过阅读程序容易看出 static 成员是与特定类关联的。这种可见性可清晰地显示程序员的意图。
当我们在类的外部定义 static 成员时，无须重复指定 static 保留字，该保留字只出现在类定义体内部的声明处。
static 成员是类的组成部分但不是任何对象的组成部分，因此，static 成员函数没有 this 指针。而且static 成员函数不能被声明为 const。毕竟，将成员函数声明为 const 就是承诺不会修改该函数所属的对象。最后，static 成员函数也不能被声明为虚函数。
static 数据成员可以声明为任意类型，可以是常量、引用、数组、类类型，等等。static 数据成员必须在类定义体的外部定义（正好一次）。不像普通数据成员，static 成员不是通过类构造函数进行初始化，而是应该在定义时进行初始化。
只要初始化式是一个常量表达式，整型 const static 数据成员就可以在类的定义体中进行初始化：
     class Account {
     public:
         static double rate() { return interestRate; }
         static void rate(double);  // sets a new rate
     private:
         static const int period = 30; // interest posted every 30 days
         double daily_tbl[period]; // ok: period is constant expression
     };
用常量值初始化的整型 const static 数据成员是一个常量表达式。同样地，它可以用在任何需要常量表达式的地方，例如指定数组成员 daily_tbl 的维。
const static 数据成员在类的定义体中初始化时，该数据成员仍必须在类的定义体之外进行定义。
static 数据成员的类型可以是该成员所属的类类型。非 static 成员被限定声明为其自身类对象的指针或引用：
     class Bar {
     public:
         // ...
     private:
         static Bar mem1; // ok
         Bar *mem2;       // ok
         Bar mem3;        // error
     };
static 数据成员可用作默认实参：
     class Screen {
     public:
         // bkground refers to the static member
         // declared later in the class definition
         Screen& clear(char = bkground);
     private:
         static const char bkground = '#';
     };

## 第十三章 复制控制
	复制构造函数、赋值操作符和析构函数总称为复制控制。编译器自动实现这些操作，但类也可以定义自己的版本。
	当定义一个新类型的时候，需要显式或隐式地指定复制、赋值和撤销该类型的对象时会发生什么——这是通过定义特殊成员：复制构造函数、赋值操作符和析构函数来达到的。如果没有显式定义复制构造函数或赋值操作符，编译器（通常）会为我们定义。
	复制构造函数是一种特殊构造函数，具有单个形参，该形参（常用 const 修饰）是对该类类型的引用。当定义一个新对象并用一个同类型的对象对它进行初始化时，将显式使用复制构造函数。当将该类型的对象传递给函数或函数返回该类型的对象时，将隐式使用复制构造函数。
	析构函数是构造函数的互补：当对象超出作用域或动态分配的对象被删除时，将自动应用析构函数。析构函数可用于释放对象时构造或在对象的生命期中所获取的资源。不管类是否定义了自己的析构函数，编译器都自动执行类中非 static 数据成员的析构函数。
	与构造函数一样，赋值操作符可以通过指定不同类型的右操作数而重载。右操作数为类类型的版本比较特殊：如果我们没有编写这种版本，编译器将为我们合成一个。
### 1. 复制构造函数
	只有单个形参，而且该形参是对本类类型对象的引用（常用 const 修饰），这样的构造函数称为复制构造函数。与默认构造函数一样，复制构造函数可由编译器隐式调用。复制构造函数可用于：
	1）根据另一个同类型的对象显式或隐式初始化一个对象。
	2）复制一个对象，将它作为实参传给一个函数。
	3）从函数返回时复制一个对象。
	4）初始化顺序容器中的元素。
	5）根据元素初始化式列表初始化数组元素。
	初始化的复制形式和直接形式有所不同：直接初始化直接调用与实参匹配的构造函数，复制初始化总是调用复制构造函数。复制初始化首先使用指定构造函数创建一个临时对象，然后用复制构造函数将那个临时对象复制到正在创建的对象：
     string null_book = "9-999-99999-9"; // copy-initialization
     string dots(10, '.');               // direct-initialization
     string empty_copy = string();       // copy-initialization
     string empty_direct;                // direct-initialization
	empty_copy 和 empty_direct 的初始化都调用默认构造函数。对前者初始化时，默认构造函数函数创建一个临时对象，然后复制构造函数用该对象初始化 empty_copy。对后者初始化时，直接运行 empty_direct 的默认构造函数。
	对于类类型对象，只有指定单个实参或显式创建一个临时对象用于复制时，才使用复制初始化。
	通常直接初始化和复制初始化仅在低级别上存在差异。然而，对于不支持复制的类型，或者使用非 explicit 构造函数的情形，它们有本质区别：
     ifstream file1("filename"); // ok: direct initialization
     ifstream file2 = "filename"; // error: copy constructor is private
     // This initialization is okay only if
     // the Sales_item(const string&) constructor is not explicit
     Sales_item item = string("9-999-99999-9");
	看上去等效的 file2 初始化使用复制初始化，但该定义不正确。由于不能复制 IO 类型的对象，所以不能对那些类型的对象使用复制初始化。
	item 的初始化是否正确，取决于正在使用哪个版本的 Sales_item 类。某些版本将参数为一个 string 的构造函数定义为 explicit。如果构造函数是显式的，则初始化失败；如果构造函数不是显式的，则初始化成功。
	当形参为非引用类型的时候，将复制实参的值。类似地，以非引用类型作返回值时，将返回 return 语句 中的值的副本。当形参或返回值为类类型时，由复制构造函数进行复制。
	复制构造函数可用于初始化顺序容器中的元素。例如，可以用表示容量的单个形参来初始化容器。容器的这种构造方式使用默认构造函数和复制构造函数：
     // default string constructor and five string copy constructors invoked
     vector<string> svec(5);
	编译器首先使用 string 默认构造函数创建一个临时值来初始化 svec，然后使用复制构造函数将临时值复制到 svec 的每个元素
	如果没有为类类型数组提供元素初始化式，则将用默认构造函数初始化每个元素。然而，如果使用常规的花括号括住的数组初始化列表来提供显式元素初始化式，则使用复制初始化来初始化每个元素。根据指定值创建适当类型的元素，然后用复制构造函数将该值复制到相应元素：
     Sales_item primer_eds[] = { string("0-201-16487-6"),
                                 string("0-201-54848-8"),
                                 string("0-201-82470-1"),
                                 Sales_item()
                               };
如果我们没有定义复制构造函数，编译器就会为我们合成一个。与合成的默认构造函数不同，即使我们定义了其他构造函数，也会合成复制构造函数。合成复制构造函数的行为是，执行逐个成员初始化，将新对象初始化为原对象的副本。所谓“逐个成员”，指的是编译器将现在对象的每个非 static 成员，依次复制到正创建的对象。合成复制构造函数直接复制内置类型成员的值，类类型成员使用该类的复制构造函数进行复制。数组成员的复制是个例外。虽然一般不能复制数组，但如果一个类具有数组成员，则合成复制构造函数将复制数组。复制数组时合成复制构造函数将复制数组的每一个元素。
定义自己的复制构造函数：
	复制构造函数就是接受单个类类型引用形参（通常用 const 修饰）的构造函数：
     class Foo {
     public:
        Foo();           // default constructor
        Foo(const Foo&); // copy constructor
        // ...
     };
	因为用于向函数传递对象和从函数返回对象，该构造函数一般不应设置为 explicit。
有些类必须对复制对象时发生的事情加以控制。这样的类经常有一个数据成员是指针，或者有成员表示在构造函数中分配的其他资源。而另一些类在创建新对象时必须做一些特定工作。这两种情况下，都必须定义复制构造函数。通常，定义复制构造函数最困难的部分在于认识到需要复制构造函数。只要能认识到需要复制构造函数，定义构造函数一般非常简单。
有些类需要完全禁止复制。例如，iostream 类就不允许复制。为了防止复制，类必须显式声明其复制构造函数为 private。如果复制构造函数是私有的，将不允许用户代码复制该类类型的对象，编译器将拒绝任何进行复制的尝试。然而，类的友元和成员仍可以进行复制。如果想要连友元和成员中的复制也禁止，就可以声明一个（private）复制构造函数但不对其定义：声明而不定义成员函数是合法的，但是，使用未定义成员的任何尝试将导致链接失败。通过声明（但不定义）private 复制构造函数，可以禁止任何复制类类型对象的尝试：用户代码中复制尝试将在编译时标记为错误，而成员函数和友元中的复制尝试将在链接时导致错误。
不定义复制构造函数和／或默认构造函数，会严重局限类的使用。不允许复制的类对象只能作为引用传递给函数或从函数返回，它们也不能用作容器的元素。

### 2. 赋值操作符
与复制构造函数一样，如果类没有定义自己的赋值操作符，则编译器会合成一个。
大多数操作符可以定义为成员函数或非成员函数。当操作符为成员函数时，它的第一个操作数隐式绑定到 this 指针。有些操作符（包括赋值操作符）必须是定义自己的类的成员。因为赋值必须是类的成员，所以 this 绑定到指向左操作数的指针。因此，赋值操作符接受单个形参，且该形参是同一类类型的对象。右操作数一般作为 const 引用传递。
赋值操作符的返回类型与内置类型赋值运算返回的类型相同。内置类型的赋值运算返回对右操作数的引用，因此，赋值操作符也返回对同一类类型的引用：
	Sales_item& operator=(const Sales_item &);
成赋值操作符与合成复制构造函数的操作类似。它会执行逐个成员赋值：右操作数对象的每个成员赋值给左操作数对象的对应成员。除数组之外，每个成员用所属类型的常规方式进行赋值。对于数组，给每个数组元素赋值。
合成赋值操作符根据成员类型使用适合的内置或类定义的赋值操作符，依次给每个成员赋值，该操作符返回 *this，它是对左操作数对象的引用。

### 3. 析构函数
变量（如 item）在超出作用域时应该自动撤销。动态分配的对象只有在指向该对象的指针被删除时才撤销。如果没有删除指向动态对象的指针，则不会运行该对象的析构函数，对象就一直存在，从而导致内存泄漏，而且，对象内部使用的任何资源也不会释放。当对象的引用或指针超出作用域时，不会运行析构函数。只有删除指向动态分配对象的指针或实际对象（而不是对象的引用）超出作用域时，才会运行析构函数。
撤销一个容器（不管是标准库容器还是内置数组）时，也会运行容器中的类类型元素的析构函数。
		{
         	Sales_item *p = new Sales_item[10]; // dynamically allocated
         	vector<Sales_item> vec(p, p + 10);  // local object
         	// ...
         	delete [] p; // array is freed; destructor run on each element
      	}   // vec goes out of scope; destructor run on each element
	容器中的元素总是按逆序撤销：首先撤销下标为 size() - 1 的元素，然后是下标为 size() - 2 的元素……直到最后撤销下标为 [0] 的元素。
	许多类不需要显式析构函数，如果类需要析构函数，则它也需要赋值操作符和复制构造函数，这是一个有用的经验法则。这个规则常称为三法则，指的是如果需要析构函数，则需要所有这三个复制控制成员。
	与复制构造函数或赋值操作符不同，编译器总是会为我们合成一个析构函数。合成析构函数按对象创建时的逆序撤销每个非 static 成员，因此，它按成员在类中声明次序的逆序撤销成员。对于类类型的每个成员，合成析构函数调用该成员的析构函数来撤销对象。合成析构函数并不删除指针成员所指向的对象。
	分配了资源的类一般需要定义析构函数以释放那些资源。析构函数是个成员函数，它的名字是在类名字之前加上一个代字号（~），它没有返回值，没有形参。因为不能指定任何形参，所以不能重载析构函数。虽然可以为一个类定义多个构造函数，但只能提供一个析构函数，应用于类的所有对象。
	析构函数与复制构造函数或赋值操作符之间的一个重要区别是，即使我们编写了自己的析构函数，合成析构函数仍然运行：
	为 Sales_item: 类编写如下的空析构函数：
     class Sales_item {
     public:
        // empty; no work to do other than destroying the members,
        // which happens automatically
         ~Sales_item() { }
        // other members as before
     };
	撤销 Sales_item 类型的对象时，将运行这个什么也不做的析构函数，它执行完毕后，将运行合成析构函数以撤销类的成员。合成析构函数调用 string 析构函数来撤销 string 成员，string 析构函数释放了保存 isbn 的内存。units_sold 和 revenue 成员是内置类型，所以合成析构函数撤销它们不需要做什么。
### 4. 消息处理示例
### 5. 管理指针成员
	包含指针的类需要特别注意复制控制，原因是复制指针时只复制指针中的地址，而不会复制指针指向的对象。
管理指针成员的三种不同方法：
	1）指针成员采取常规指针型行为。这样的类具有指针的所有缺陷但无需特殊的复制控制。
	2）类可以实现所谓的“智能指针”行为。指针所指向的对象是共享的，但类能够防止悬垂指针。
	3）类采取值型行为。指针所指向的对象是唯一的，由每个类对象独立管理。
一个带指针成员的简单类：
	class HasPtr {
     public:
         HasPtr(int *p, int i): ptr(p), val(i) { }
         int *get_ptr() const { return ptr; }
         int get_int() const { return val; }
         void set_ptr(int *p) { ptr = p; }
         void set_int(int i) { val = i; }
         int get_ptr_val() const { return *ptr; }
         void set_ptr_val(int val) const { *ptr = val; }
     private:
         int *ptr;
         int val;
     };
默认复制／赋值与指针成员：因为 HasPtr 类没有定义复制构造函数，所以复制一个 HasPtr 对象将复制两个成员：
     int obj = 0;
     HasPtr ptr1(&obj, 42); // int* member points to obj, val is 42
     HasPtr ptr2(ptr1);     // int* member points to obj, val is 42
	复制之后，ptr1 和 ptr2 中的指针指向同一对象且两个对象中的 int 值相同。但是，因为指针的值不同于它所指对象的值，这两个成员的行为看来非常不同。复制之后，int 值是清楚和独立的，而指针则纠缠在一起，可能出现悬垂指针：HasPtr 保存着给定指针。用户必须保证只要 HasPtr 对象存在，该指针指向的对象就存在：
     int *ip = new int(42); // dynamically allocated int initialized to 42
     HasPtr ptr(ip, 10);    // Has Ptr points to same object as ip does
     delete ip;             // object pointed to by ip is freed
     ptr.set_ptr_val(0); // disaster: The object to which Has Ptr points was freed!
	ip 和 ptr 中的指针指向同一对象。删除了该对象时，ptr 中的指针不再指向有效对象。然而，没有办法得知对象已经不存在了。
定义智能指针类：智能指针除了增加功能外，其行为像普通指针一样。本例中让智能指针负责删除共享对象。用户将动态分配一个对象并将该对象的地址传给新的 HasPtr 类。用户仍然可以通过普通指针访问对象，但绝不能删除指针。HasPtr 类将保证在撤销指向对象的最后一个 HasPtr 对象时删除对象。HasPtr 在其他方面的行为与普通指针一样。具体而言，复制对象时，副本和原对象将指向同一基础对象，如果通过一个副本改变基础对象，则通过另一对象访问的值也会改变。新的 HasPtr 类需要一个析构函数来删除指针，但是，析构函数不能无条件地删除指针。如果两个 HasPtr 对象指向同一基础对象，那么，在两个对象都撤销之前，我们并不希望删除基础对象。为了编写析构函数，需要知道这个 HasPtr 对象是否为指向给定对象的最后一个。
	定义智能指针的通用技术是采用一个使用计数。智能指针类将一个计数器与类指向的对象相关联。使用计数跟踪该类有多少个对象共享同一指针。使用计数为 0 时，删除对象。使用计数有时也称为引用计数。每次创建类的新对象时，初始化指针并将使用计数置为 1。当对象作为另一对象的副本而创建时，复制构造函数复制指针并增加与之相应的使用计数的值。对一个对象进行赋值时，赋值操作符减少左操作数所指对象的使用计数的值（如果使用计数减至 0，则删除对象），并增加右操作数所指对象的使用计数的值。最后，调用析构函数时，析构函数减少使用计数的值，如果计数减至 0，则删除基础对象。
计数器不能直接放在 HasPtr 对象中：定义一个单独的具体类用以封闭使用计数和相关指针：
     // private class for use by HasPtr only
     class U_Ptr {
         friend class HasPtr;
         int *ip;
         size_t use;
         U_Ptr(int *p): ip(p), use(1) { }
         ~U_Ptr() { delete ip; }
     };
	U_Ptr 类保存指针和使用计数，每个 HasPtr 对象将指向一个 U_Ptr 对象，使用计数将跟踪指向每个 U_Ptr 对象的 HasPtr 对象的数目。U_Ptr 定义的仅有函数是构造函数和析构函数，构造函数复制指针，而析构函数删除它。构造函数还将使用计数置为 1，表示一个 HasPtr 对象指向这个 U_Ptr 对象。新的 HasPtr 类保存一个指向 U_Ptr 对象的指针，U_Ptr 对象指向实际的 int 基础对象。必须改变每个成员以说明的 HasPtr 类指向一个 U_Ptr 对象而不是一个 int。
	class HasPtr {
     public:
         HasPtr(int *p, int i): ptr(new U_Ptr(p)), val(i) { }
         HasPtr(const HasPtr &orig):
            ptr(orig.ptr), val(orig.val) { ++ptr->use; }
         HasPtr& operator=(const HasPtr&);
         ~HasPtr() { if (--ptr->use == 0) delete ptr; }
     private:
         U_Ptr *ptr;        // points to use-counted U_Ptr class
         int val;
     };
赋值与使用计数：
	HasPtr& HasPtr::operator=(const HasPtr &rhs)
     {
         ++rhs.ptr->use;     // increment use count on rhs first
         if (--ptr->use == 0)
              delete ptr;    // if use count goes to 0 on this object, delete it
         ptr = rhs.ptr;      // copy the U_Ptr object
         val = rhs.val;      // copy the int member
         return *this;
     }
	首先将右操作数中的使用计数加 1，然后将左操作数对象的使用计数减 1 并检查这个使用计数。像析构函数中那样，如果这是指向 U_Ptr 对象的最后一个对象，就删除该对象，这会依次撤销 int 基础对象。将左操作数中的当前值减 1（可能撤销该对象）之后，再将指针从 rhs 复制到这个对象。赋值照常返回对这个对象的引用。
	改变访问 int* 的其他成员，以便通过 U_Ptr 指针间接获取 int：
     class HasPtr {
     public:
         int *get_ptr() const { return ptr->ip; }
         int get_int() const { return val; }
         void set_ptr(int *p) { ptr->ip = p; }
         void set_int(int i) { val = i; }
         int get_ptr_val() const { return *ptr->ip; }
         void set_ptr_val(int i) { *ptr->ip = i; }
     private:
         U_Ptr *ptr;        // points to use-counted U_Ptr class
         int val;
     };
为了管理具有指针成员的类，必须定义三个复制控制成员：复制构造函数、赋值操作符和析构函数。这些成员可以定义指针成员的指针型行为或值型行为。值型类将指针成员所指基础值的副本给每个对象。复制构造函数分配新元素并从被复制对象处复制值，赋值操作符撤销所保存的原对象并从右操作数向左操作数复制值，析构函数撤销对象。
定义值型类：要使指针成员表现得像一个值，复制 HasPtr 对象时必须复制指针所指向的对象：
     class HasPtr {
     public:
         HasPtr(const int &p, int i): ptr(new int(p)), val(i) {}
         HasPtr(const HasPtr &orig):
            ptr(new int (*orig.ptr)), val(orig.val) { }
         HasPtr& operator=(const HasPtr&);
         ~HasPtr() { delete ptr; }
         int get_ptr_val() const { return *ptr; }
         int get_int() const { return val; }
         void set_ptr(int *p) { ptr = p; }
         void set_int(int i) { val = i; }
         int *get_ptr() const { return ptr; }
         void set_ptr_val(int p) const { *ptr = p; }
     private:
         int *ptr;        // points to an int
         int val;
     };
	复制构造函数不再复制指针，它将分配一个新的 int 对象，并初始化该对象以保存与被复制对象相同的值。每个对象都保存属于自己的 int 值的不同副本。因为每个对象保存自己的副本，所以析构函数将无条件删除指针。
	赋值操作符不需要分配新对象，它只是必须记得给其指针所指向的对象赋新值，而不是给指针本身赋值：
     HasPtr& HasPtr::operator=(const HasPtr &rhs)
     {
         *ptr = *rhs.ptr;       // copy the value pointed to
         val = rhs.val;         // copy the int
         return *this;
     }
	即使要将一个对象赋值给它本身，赋值操作符也必须总是保证正确。本例中，即使左右操作数相同，操作本质上也是安全的，因此，不需要显式检查自身赋值。

## 第十四章 重载操作符与转换
### 1. 重载操作符的定义
	重载操作符是具有特殊名称的函数：保留字 operator 后接需定义的操作符号。像任意其他函数一样，重载操作符具有返回类型和形参表，如下语句：
     Sales_item operator+(const Sales_item&, const Sales_item&);
可以重载的操作符：	
+	-	*	/	%	^
&	|	~	!	,	=
<	>	<=	>=	++	--
<<	>>	==	!=	&&	||
+=	-=	/=	%=	^=	&=
|=	*=	<<=	>>=	[]	()
->	->*	new	new []	delete	delete []
不能重载的操作符：	
	::   .*  .   ?:
通过连接其他合法符号可以创建新的操作符。例如，定义一个 operator** 以提供求幂运算是合法的。
重载操作符必须具有至少一个类类型或枚举类型的操作数。这条规则强制重载操作符不能重新定义用于内置类型对象的操作符的含义
操作符的优先级、结合性或操作数目不能改变。
有四个符号（+, -, * 和 &）既可作一元操作符又可作二元操作符，这些操作符有的在其中一种情况下可以重载，有的两种都可以，定义的是哪个操作符由操作数数目控制。除了函数调用操作符 operator() 之外，重载操作符时使用默认实参是非法的。
不再具备短路求值特性：重载操作符并不保证操作数的求值顺序，尤其是，不会保证内置逻辑 AND、逻辑 OR和逗号操作符的操作数求值。在 && 和 || 的重载版本中，两个操作数都要进行求值，而且对操作数的求值顺序不做规定。因此，重载 &&、|| 或逗号操作符不是一种好的做法。
类成员与非成员：大多数重载操作符可以定义为普通非成员函数或类的成员函数。作为类成员的重载函数，其形参看起来比操作数数目少 1。作为成员函数的操作符有一个隐含的 this 形参，限定为第一个操作数。重载一元操作符如果作为成员函数就没有（显式）形参，如果作为非成员函数就有一个形参。类似地，重载二元操作符定义为成员时有一个形参，定义为非成员函数时有两个形参。
一般将算术和关系操作符定义非成员函数，而将赋值操作符定义为成员：
     Sales_item& Sales_item::operator+=(const Sales_item&);
     Sales_item operator+(const Sales_item&, const Sales_item&);
操作符定义为非成员函数时，通常必须将它们设置为所操作类的友元。
也可以像调用普通函数一样调用重载操作符函数，指定函数并传递适当类型适当数目的形参：
     cout << operator+(item1, item2) << endl;
	 item1.operator+=(item2);   // equivalent call to member operator function
不要重载具有内置含义的操作符：重载逗号、取地址、逻辑与、逻辑或等等操作符通常不是好做法。这些操作符具有有用的内置含义，如果我们定义了自己的版本，就不能再使用这些内置含义。
大多数操作符对类对象没有意义：除非提供了重载定义，赋值、取地址和逗号操作符对于类类型操作数没有意义。设计类的时候，应该确定要支持哪些操作符。
当内置操作符和类型上的操作存在逻辑对应关系时，操作符重载最有用。使用重载操作符而不是创造命名操作，可以令程序更自然、更直观，而滥用操作符重载使得我们的类难以理解。当一个重载操作符的含义不明显时，给操作取一个名字更好。对于很少用的操作，使用命名函数通常也比用操作符更好。如果不是普通操作，没有必要为简洁而使用操作符。
如果类定义了相等操作符，它也应该定义不等操作符 !=。类用户会假设如果可以进行相等比较，则也可以进行不等比较。同样的规则也应用于其他关系操作符。如果类定义了 <，则它可能应该定义全部的四个关系操作符（>，>=，<，<=）。
选择成员或非成员实现：
	1）赋值（=）、下标（[]）、调用（()）和成员访问箭头（->）等操作符必须定义为成员，将这些操作符定义为非成员函数将在编译时标记为错误。
	2）像赋值一样，复合赋值操作符通常应定义为类的成员，与赋值不同的是，不一定非得这样做，如果定义非成员复合赋值操作符，不会出现编译错误。
	3）改变对象状态或与给定类型紧密联系的其他一些操作符，如自增、自减和解引用，通常就定义为类成员。
	4）对称的操作符，如算术操作符、相等操作符、关系操作符和位操作符，最好定义为普通非成员函数。

### 2. 输入和输出操作符
输出操作符 << 的重载：为了与 IO 标准库一致，操作符应接受 ostream& 作为第一个形参，对类类型 const 对象的引用作为第二个形参，并返回对 ostream 形参的引用。
	ostream& operator <<(ostream& os, const ClassType &object)
     {
         // any special logic to prepare object
         // actual output of members
         os << // ...
         // return ostream object
         return os;
     }
第一个形参是对 ostream 对象的引用，在该对象上将产生输出。ostream 为非 const，因为写入到流会改变流的状态。该形参是一个引用，因为不能复制 ostream 对象。第二个形参一般应是对要输出的类类型的引用。该形参是一个引用以避免复制实参。它可以是 const，因为（一般而言）输出一个对象不应该改变对象。使形参成为 const 引用，就可以使用同一个定义来输出 const 和非 const 对象。返回类型是一个 ostream 引用，它的值通常是输出操作符所操作的 ostream 对象。
一般而言，输出操作符应输出对象的内容，进行最小限度的格式化，它们不应该输出换行符。
IO 操作符必须为非成员函数：不能将该操作符定义为类的成员，否则，左操作数将只能是该类类型的对象：
     // if operator<< is a member of Sales_item
     Sales_item item;
     item << cout;
输入操作符 >> 的重载：输入操作符的第一个形参是一个引用，指向它要读的流，并且返回的也是对同一个流的引用。它的第二个形参是对要读入的对象的非 const 引用，该形参必须为非 const，因为输入操作符的目的是将数据读到这个对象中。
输入和输出操作符有如下区别：输入操作符必须处理错误和文件结束的可能性。
Sales_item 的输入操作符如下：
     istream& operator>>(istream& in, Sales_item& s)
     {
         double price;
         in >> s.isbn >> s.units_sold >> price;
         // check that the inputs succeeded无需检查每次读入，只在使用读入数据之前检查一次即可
         if (in)
            s.revenue = s.units_sold * price;
         else
            s = Sales_item(); // input failed: reset object to default state
         return in;
     }

### 3. 算术操作符和关系操作符
一般而言，将算术和关系操作符定义为非成员函数。为了与内置操作符保持一致，加法返回一个右值，而不是一个引用。
定义了 operator== 的类更容易与标准库一起使用。有些算法，如 find，默认使用 == 操作符，如果类定义了 ==，则这些算法可以无须任何特殊处理而用于该类类型。定义了相等操作符的类一般也具有关系操作符。尤其是，因为关联容器和某些算法使用小于操作符，所以定义 operator< 可能相当有用。

### 4. 赋值操作符
赋值操作符可以重载。无论形参为何种类型，赋值操作符必须定义为成员函数，这一点与复合赋值操作符有所不同。
string 类包含如下成员：
     class string {
     public:
         string& operator=(const string &);      // s1 = s2;
         string& operator=(const char *);        // s1 = "str";
         string& operator=(char);                // s1 = 'c';
         // ....
      };
赋值必须返回对 *this 的引用，例如，这是 Sales_item 复合赋值操作符的定义：
     Sales_item& Sales_item::operator+=(const Sales_item& rhs)
     {
        units_sold += rhs.units_sold;
        revenue += rhs.revenue;
        return *this;
     }

### 5. 下标操作符
下标操作符必须定义为类成员函数。
提供读写访问：定义下标操作符比较复杂的地方在于，它在用作赋值的左右操作符数时都应该能表现正常。下标操作符出现在左边，必须生成左值，可以指定引用作为返回类型而得到左值。只要下标操作符返回引用，就可用作赋值的任意一方。应用于 const 对象时，返回值应为 const 引用，因此不能用作赋值的目标。
类定义下标操作符时，一般需要定义两个版本：一个为非 const 成员并返回引用，另一个为 const 成员并返回 const 引用。
下面的类定义了下标操作符。为简单起见，假定 Foo 所保存的数据存储在一个 vector<int>: 中：
     class Foo {
     public:
         int &operator[] (const size_t);
         const int &operator[] (const size_t) const;
     private:
         vector<int> data;
      };
下标操作符本身可能看起来像这样：
     int& Foo::operator[] (const size_t index)
     {
         return data[index];  // no range checking on index
     }
     const int& Foo::operator[] (const size_t index) const
     {
         return data[index];  // no range checking on index
     }

### 6. 成员访问操作符
C++ 语言允许重载解引用操作符（*）和箭头操作符（->)）。箭头操作符必须定义为类成员函数。解引用操作不要求定义为成员，但将它作为成员一般也是正确的。
像下标操作符一样，我们需要解引用操作符的 const 和非 const 版本。它们的区别在于返回类型：const 成员返回 const 引用以防止用户改变基础对象：
重载箭头操作符必须返回指向类类型的指针，或者返回定义了自己的箭头操作符的类类型对象。如果返回类型是类类型的其他对象（或是这种对象的引用），则将递归应用该操作符。编译器检查返回对象所属类型是否具有成员箭头，如果有，就应用那个操作符；否则，编译器产生一个错误。这个过程继续下去，直到返回一个指向带有指定成员的的对象的指针，或者返回某些其他值，在后一种情况下，代码出错。
	class ScreenPtr {
     public:
         Screen &operator*() { return *ptr->sp; }
         Screen *operator->() { return ptr->sp; }
         const Screen &operator*() const { return *ptr->sp; }
         const Screen *operator->() const { return ptr->sp; }
     private:
         ScrPtr *ptr; // points to use-counted ScrPtr class
     };

### 7. 自增操作符和自减操作符
C++ 语言不要求自增操作符或自减操作符一定作为类的成员，但是，因为这些操作符改变操作对象的状态，所以更倾向于将它们作为成员。
对内置类型而言，自增操作符和自减操作符有前缀和后缀两种形式。也可以为我们自己的类定义自增操作符和自减操作符的前缀和后缀实例。
定义前自增／前自减操作符：
	CheckedPtr& CheckedPtr::operator++()
     {
         if (curr == end)
             throw out_of_range
                   ("increment past the end of CheckedPtr");
         ++curr;                // advance current state
         return *this;
     }
同时定义前缀式操作符和后缀式操作符存在一个问题：它们的形参数目和类型相同，普通重载不能区别所定义的前缀式操作符还是后缀式操作符。为了解决这一问题，后缀式操作符函数接受一个额外的（即，无用的）int 型形参。使用后缀式操作符进，编译器提供 0 作为这个形参的实参。尽管我们的前缀式操作符函数可以使用这个额外的形参，但通常不应该这样做。那个形参不是后缀式操作符的正常工作所需要的，它的唯一目的是使后缀函数与前缀函数区别开来：
	CheckedPtr CheckedPtr::operator++(int)
     {
         // no check needed here, the call to prefix increment will do the check
         CheckedPtr ret(*this);        // save current value
         ++*this;                      // advance one element, checking the increment
         return ret;                   // return saved state
     }
	为了与内置操作符一致，后缀式操作符应返回旧值（即，尚未自增或自减的值），并且，应作为值返回，而不是返回引用。
显式调用前缀式操作符：
	CheckedPtr parr(ia, ia + size);        // iapoints to an array of ints
     parr.operator++(0);                    // call postfix operator++
			//所传递的值通常被忽略，但该值是必要的，用于通知编译器需要的是后缀式版本
     parr.operator++();                     // call prefix operator++

### 8. 调用操作符和函数对象
函数调用操作符必须声明为成员函数。一个类可以定义函数调用操作符的多个版本，由形参的数目或类型加以区别。定义了调用操作符的类，其对象常称为函数对象，即它们是行为类似函数的对象。
	struct absInt {
         int operator() (int val) {
             return val < 0 ? -val : val;
         }
     };
	int i = -42;
     absInt absObj;  // object that defines function call operator
     unsigned int ui = absObj(i);     // calls absInt::operator(int)
	标准库定义的函数对象：标准库定义了一组算术、关系与逻辑函数对象类，标准库还定义了一组函数适配器，使我们能够特化或者扩展标准库所定义的以及自定义的函数对象类。每个函数对象类都是一个类模板。这些标准库函数对象类型是在 functional 头文件中定义的。每个标准库函数对象类表示一个操作符，即，每个类都定义了应用命名操作的调用操作符。例如，plus 是表示加法操作符的模板类型。plus 模板中的调用操作符对一对操作数应用 + 运算。
	函数对象常用于覆盖算法使用的默认操作符。例如，sort 默认使用 operator< 按升序对容器进行排序。为了按降序对容器进行排序，可以传递函数对象 greater。该类将产生一个调用操作符，调用基础对象的大于操作符。如果 svec 是一个 vector<string> 对象，以下代码
     sort(svec.begin(), svec.end(), greater<string>());
	将按降序对 vector 进行排序。像通常那样，传递一对迭代器以指明被排序序列。第三个实参用于传递比较元素的谓词函数。该实参 greater<string> 类型的临时对象，是一个将 > 操作符应用于两个 string 操作符的函数对象。
函数对象的函数适配器：标准库提供了一组函数适配器，用于特化和扩展一元和二元函数对象。函数适配器分为如下两类：
	1）绑定器，是一种函数适配器，它通过将一个操作数绑定到给定值而将二元函数对象转换为一元函数对象。
	2）求反器，是一种函数适配器，它将谓词函数对象的真值求反。
标准库定义了两个绑定器适配器：bind1st 和 bind2nd。每个绑定器接受一个函数对象和一个值。bind1st 将给定值绑定到二元函数对象的第一个实参，bind2nd 将给定值绑定到二元函数对象的第二个实参。例如，为了计算一个容器中所有小于或等于 10 的元素的个数，可以这样给 count_if 传递值：
     count_if(vec.begin(), vec.end(),
              bind2nd(less_equal<int>(), 10));
	传给 count_if 的第三个实参使用 bind2nd 函数适配器，该适配器返回一个函数对象，该对象用 10 作右操作数应用 <= 操作符。这个 count_if 调用计算输入范围中小于或等于 10 的元素的个数。
标准库还定义了两个求反器：not1 和 not2。not1 将一元函数对象的真值求反，not2 将二元函数对象的真值求反。
为了对 less_equal 函数对象的绑定求反，可以编写这样的代码：
     count_if(vec.begin(), vec.end(),
             not1(bind2nd(less_equal<int>(), 10)));
这里，首先将 less_equal 对象的第二个操作数绑定到 10，实际上是将该二元操作转换为一元操作。再用 not1 对操作的返回值求反，效果是测试每个元素是否 <=。然后，对结果真值求反。这个 count_if 调用的效果是对不 <= 10 的那些元素进行计数。

### 9. 转换与类类型
我们可以定义转换操作符，给定类类型的对象，该操作符将产生其他类型的对象。像其他转换一样，编译器将自动应用这个转换。
转换操作符是一种特殊的类成员函数。它定义将类类型值转变为其他类型值的转换。转换操作符在类定义体内声明，在保留字 operator 之后跟着转换的目标类型：
     class SmallInt {
     public:
         SmallInt(int i = 0): val(i)
         { if (i < 0 || i > 255)
            throw std::out_of_range("Bad SmallInt initializer");
         }
         operator int() const { return val; }
     private:
         std::size_t val;
     };
转换函数采用如下通用形式：转换函数必须是成员函数，不能指定返回类型，并且形参表必须为空
     operator type();
type 表示内置类型名、类类型名或由类型别名定义的名字。对任何可作为函数返回类型的类型（除了 void 之外）都可以定义转换函数。一般而言，不允许转换为数组或函数类型，转换为指针类型（数据和函数指针）以及引用类型是可以的。
虽然转换函数不能指定返回类型，但是每个转换函数必须显式返回一个指定类型的值。例如，operator int 返回一个 int 值；如果定义 operator Sales_item，它将返回一个 Sales_item 对象，诸如此类。
使用转换函数时，被转换的类型不必与所需要的类型完全匹配。
只要存在转换，编译器将在可以使用内置转换的地方自动调用它：
	1）在表达式中：
		SmallInt si;
		double dval;
		si >= dval          // si converted to int and then convert to double
	2）在条件中：
     	if (si)                // si converted to int and then convert to bool
	3）将实参传给函数或从函数返回值：
     	int calc(int);
     	SmallInt si;
     	int i = calc(si);      // convert si to int and call calc
	4）作为重载操作符的操作数：
        cout << si << endl;
	5）在显式类型转换中：
     	int ival;
     	SmallInt si = 3.541; //instruct compiler to cast si to int
     	ival = static_cast<int>(si) + 3;
类类型转换之后不能再跟另一个类类型转换。如果需要多个类类型转换，则代码将出错。
例如，假定有另一个类 Integral，它可以转换为 SmallInt 但不能转换为 int：
     class Integral {
     public:
         Integral(int i = 0): val(i) { }
         operator SmallInt() const { return val % 256; }
     private:
         std::size_t val;
     };
可以在需要 SmallInt 的地方使用 Integral，但不能在需要 int 的地方使用 Integeral：
     int calc(int);
     Integral intVal;
     SmallInt si(intVal);  // ok: convert intVal to SmallInt and copy to si
     int i = calc(si);     // ok: convert si to int and call calc
     int j = calc(intVal); // error: no conversion to int from Integral
第一个 calc 调用也是正确的：将实参 si 自动转换为 int，然后将 int 值传给函数。
第二个 calc 调用是错误的：没有从 Integral 到 int 的直接转换。从 int 需要两次类类型转换：首先从 Integral 到 SmallInt，然后从 SmallInt 到 int。但是，语言只允许一次类类型转换，所以该调用出错。
标准转换可放在类类型转换之前：使用构造函数执行隐式转换的时候，构造函数的形参类型不必与所提供的类型完全匹配。例如，下面的代码调用 SmallInt(int) 类中定义的构造函数（SmallInt(int)）将 sobj 转换为 SmallInt 类型：
     void calc(SmallInt);
     short sobj;
     // sobj promoted from short to int
     // that int converted to SmallInt through the SmallInt(int) constructor
     calc(sobj);
如果需要，在调用构造函数执行类类型转换之前，可将一个标准转换序列应用于实参。为了调用函数 calc()，应用标准转换将 dobj 从 double 类型转换为 int 类型，然后调用构造函数 SmallInt(int) 将转换结果转换为 SmallInt 类型。
类类型转换可能是实现和使用类的一个好处。通过为 SmallInt 定义到　int　的转换，能够更容易实现和使用 SmallInt 类。int 转换使 SmallInt 的用户能够对 SmallInt 对象使用所有算术和关系操作符，而且，用户可以安全编写将 SmallInt 和其他算术类型混合使用的表达式。定义一个转换操作符就能代替定义 48 个（或更多）重载操作符，类实现者的工作就简单多了。类类型转换也可能是编译时错误的一大来源。当从一个类型转换到另一类型有多种方式时，问题就出现了。如果有几个类类型转换可以使用，编译器必须决定对给定表达式使用哪一个。在这一节，我们介绍怎样用类类型转换将实参和对应形参相匹配。首先介绍非重载函数的形参匹配，然后介绍重载函数的形参匹配。
（省略剩余高级主题：实参匹配和转换、重载确定和类的实参、重载、转换和操作符）

## 第十五章 面向对象编程
### 1. 面向对象编程：概述
在 C++ 中，基类必须指出希望派生类重写哪些函数，定义为 virtual 的函数是基类期待派生类重新定义的，基类希望派生类继承的函数不能定义为虚函数。在 C++ 中，通过基类的引用（或指针）调用虚函数时，发生动态绑定。引用（或指针）既可以指向基类对象也可以指向派生类对象，这一事实是动态绑定的关键。用引用（或指针）调用的虚函数在运行时确定，被调用的函数是引用（或指针）所指对象的实际类型所定义的。

### 2. 定义基类和派生类
继承层次的根类一般都要定义虚析构函数。
成员默认为非虚函数，对非虚函数的调用在编译时确定。为了指明函数为虚函数，在其返回类型前面加上保留字 virtual。除了构造函数之外，任意非 static 成员函数都可以是虚函数。保留字只在类内部的成员函数声明中出现，不能用在类定义体外部出现的函数定义上。
用户代码可以访问类的 public 成员而不能访问 private 成员，private 成员只能由基类的成员和友元访问。派生类对基类的 public 和 private 成员的访问权限与程序中任意其他部分一样：它可以访问 public 成员而不能访问 private 成员。
派生类只能通过派生类对象访问其基类的 protected 成员，派生类对其基类类型对象的 protected 成员没有特殊访问权限。
一旦函数在基类中声明为虚函数，它就一直为虚函数，派生类无法改变该函数为虚函数这一事实。派生类重定义虚函数时，可以使用 virtual 保留字，但不是必须这样做。
派生类型必须对想要重定义的每个继承成员进行声明，派生类中虚函数的声明必须与基类中的定义方式完全匹配，但有一个例外：返回对基类型的引用（或指针）的虚函数。派生类中的虚函数可以返回基类函数所返回类型的派生类的引用（或指针）。Item_base 类可以定义返回 Item_base* 的虚函数，如果这样，Bulk_item 类中定义的实例可以定义为返回 Item_base* 或 Bulk_item*。
如果需要声明（但并不实现）一个派生类，则声明包含类名但不包含派生列表。例如，下面的前向声明会导致编译时错误：
     class Bulk_item : public Item_base;
	正确的前向声明为：
     class Bulk_item;
C++ 中的函数调用默认不使用动态绑定。要触发动态绑定，满足两个条件：第一，只有指定为虚函数的成员函数才能进行动态绑定，成员函数默认为非虚函数，非虚函数不进行动态绑定；第二，必须通过基类类型的引用或指针进行函数调用。要理解这一要求，需要理解在使用继承层次中某一类型的对象的引用或指针时会发生什么。
因为每个派生类对象都包含基类部分，所以可将基类类型的引用绑定到派生类对象的基类部分，也可以用指向基类的指针指向派生类对象。
通过引用或指针调用虚函数时，编译器将生成代码，在运行时确定调用哪个函数，被调用的是与动态类型相对应的函数。
引用和指针的静态类型与动态类型可以不同，这是 C++ 用以支持多态性的基石。通过基类引用或指针调用基类中定义的函数时，我们并不知道执行函数的对象的确切类型，执行函数的对象可能是基类类型的，也可能是派生类型的。如果调用非虚函数，则无论实际对象是什么类型，都执行基类类型所定义的函数。如果调用虚函数，则直到运行时才能确定调用哪个函数，运行的虚函数是引用所绑定的或指针所指向的对象所属类型定义的版本。对象是非多态的——对象类型已知且不变。对象的动态类型总是与静态类型相同，这一点与引用或指针相反。运行的函数（虚函数或非虚函数）是由对象的类型定义的。只有通过引用或指针调用，虚函数才在运行时确定。只有在这些情况下，直到运行时才知道对象的动态类型。
非虚函数总是在编译时根据调用该函数的对象、引用或指针的类型而确定。
覆盖虚函数机制：在某些情况下，希望覆盖虚函数机制并强制函数调用使用虚函数的特定版本，这里可以派生类虚函数调用基类版本时，必须显式使用作用域操作符。如果派生类函数忽略了这样做，则函数调用会在运行时确定并且将是一个自身调用，从而导致无穷递归：
     Item_base *baseP = &derived;
     double d = baseP->Item_base::net_price(42);
这段代码强制将 net_price 调用确定为 Item_base 中定义的版本，该调用将在编译时确定。
只有成员函数中的代码才应该使用作用域操作符覆盖虚函数机制。
	虚函数也可以有默认实参。通常，如果有用在给定调用中的默认实参值，该值将在编译时确定。如果一个调用省略了具有默认值的实参，则所用的值由调用该函数的类型定义，与对象的动态类型无关。通过基类的引用或指针调用虚函数时，默认实参为在基类虚函数声明中指定的值，如果通过派生类的指针或引用调用虚函数，则默认实参是在派生类的版本中声明的值。在同一虚函数的基类版本和派生类版本中使用不同的默认实参几乎一定会引起麻烦。如果通过基类的引用或指针调用虚函数，但实际执行的是派生类中定义的版本，这时就可能会出现问题。在这种情况下，为虚函数的基类版本定义的默认实参将传给派生类定义的版本，而派生类版本是用不同的默认实参定义的。
公用、私有和受保护的继承：派生类中定义的成员访问控制的处理与任意其他类中完全一样。派生类可以定义零个或多个访问标号，指定跟随其后的成员的访问级别。对类所继承的成员的访问由基类中的成员访问级别和派生类派生列表中使用的访问标号共同控制。派生类可以进一步限制但不能放松对所继承的成员的访问。
派生类不能访问基类的 private 成员，也不能使自己的用户能够访问那些成员。如果基类成员为 public 或 protected，则派生列表中使用的访问标号决定该成员在派生类中的访问级别：
	1）如果是公用继承，基类成员保持自己的访问级别：基类的 public 成员为派生类的 public 成员，基类的 protected 成员为派生类的 protected 成员。
	2）如果是受保护继承，基类的 public 和 protected 成员在派生类中为 protected 成员。
	3）如果是私有继承，基类的的所有成员在派生类中为 private 成员。
无论派生列表中是什么访问标号，所有继承 Base 的类对 Base 中的成员具有相同的访问。派生访问标号将控制派生类的用户对从 Base 继承而来的成员的访问：
	class Base {
     public:
         void basemem();   // public member
     protected:
         int i;            // protected member
         // ...
     };
     struct Public_derived : public Base {
         int use_base() { return i; } // ok: derived classes can access i
         // ...
     };
     struct Private_derived : private Base {
         int use_base() { return i; } // ok: derived classes can access i
     };
	Base b;
     Public_derived d1;
     Private_derived d2;
     b.basemem();   // ok: basemem is public
     d1.basemem();  // ok: basemem is public in the derived class
     d2.basemem();  // error: basemem is private in the derived class
派生访问标号还控制来自非直接派生类的访问：
     struct Derived_from Private : public Private_derived {
         // error: Base::i is private in Private_derived
         int use_base() { return i; }
     };
     struct Derived_from_Public : public Public_derived {
         // ok: Base::i remains protected in Public_derived
         int use_base() { return i; }
     };
从 Public_derived 派生的类可以访问来自 Base 类的 i，是因为该成员在 Public_derived 中仍为 protected 成员。从 Private_derived 派生的类没有这样的访问，对它们而言，Private_derived 从 Base 继承的所有成员均为 private。
接口继承与实现继承：public 派生类继承基类的接口，它具有与基类相同的接口。设计良好的类层次中，public 派生类的对象可以用在任何需要基类对象的地方。使用 private 或 protected 派生的类不继承基类的接口，相反，这些派生通常被称为实现继承。派生类在实现中使用被继承但继承基类的部分并未成为其接口的一部分。
派生类可以恢复继承成员的访问级别，但不能使访问级别比基类中原来指定的更严格或更宽松：
	class Base {
     public:
         std::size_t size() const { return n; }
     protected:
         std::size_t n;
     };
     class Derived : private Base { . . . };
在这一继承层次中，size 在 Base 中为 public，但在 Derived 中为 private。为了使 size 在 Derived 中成为 public，可以在 Derived 的 public 部分增加一个 using 声明。如下这样改变 Derived 的定义，可以使 size 成员能够被用户访问，并使 n 能够被从 Derived 派生的类访问：
     class Derived : private Base {
     public:
        // maintain access levels for members related to the size of the object
        using Base::size;
     protected:
         using Base::n;
         // ...
      };
正如可以使用 using 声明从命名空间使用名字，也可以使用 using 声明访问基类中的名字，除了在作用域操作符左边用类名字代替命名空间名字之外，使用形式是相同的。
使用 class 保留字定义的派生默认具有 private 继承，而用 struct 保留字定义的类默认具有 public 继承：
     class Base { /* ... */ };
     struct D1 : Base { /* ... */ };   // public inheritance by default
     class D2 : Base { /* ... */ };    // private       inheritance by default
尽管私有继承在使用 class 保留字时是默认情况，但这在实践中相对罕见。因为私有继承是如此罕见，通常显式指定 private 是比依赖于默认更好的办法。显式指定可清楚指出想要私有继承而不是一时疏忽。
友元关系不能继承。基类的友元对派生类的成员没有特殊访问权限。
如果基类被授予友元关系，则只有基类具有特殊访问权限，该基类的派生类不能访问授予友元关系的类。
如果派生类想要将自己成员的访问权授予其基类的友元，派生类必须显式地这样做：基类的友元对从该基类派生的类型没有特殊访问权限。同样，如果基类和派生类都需要访问另一个类，那个类必须特地将访问权限授予基类的和每一个派生类。
如果基类定义 static 成员，则整个继承层次中只有一个这样的成员。无论从基类派生出多少个派生类，每个 static 成员只有一个实例。static 成员遵循常规访问控制：如果成员在基类中为 private，则派生类不能访问它。假定可以访问成员，则既可以通过基类访问 static 成员，也可以通过派生类访问 static 成员。一般而言，既可以使用作用域操作符也可以使用点或箭头成员访问操作符。

### 3. 转换与继承
存在从派生类型引用到基类类型引用的自动转换，即，可以将派生类对象的引用转换为基类子对象的引用，对指针也类似。基类类型对象既可以作为独立对象存在，也可以作为派生类对象的一部分而存在，因此，一个基类对象可能是也可能不是一个派生类对象的部分，结果，没有从基类引用（或基类指针）到派生类引用（或派生类指针）的（自动）转换。对象转换的情况更为复杂。虽然一般可以使用派生类型的对象对基类类型的对象进行初始化或赋值，但，没有从派生类型对象到基类类型对象的直接转换。
派生类到基类的转换：如果有一个派生类型的对象，则可以使用它的地址对基类类型的指针进行赋值或初始化。同样，可以使用派生类型的引用或对象初始化基类类型的引用。严格说来，对对象没有类似转换。编译器不会自动将派生类型对象转换为基类类型对象。但是，一般可以使用派生类型对象对基类对象进行赋值或初始化。对对象进行初始化和／或赋值以及可以自动转换引用或指针，这之间的区别是微妙的。
引用转换不同于转换对象：将对象传给希望接受引用的函数时，引用直接绑定到该对象，虽然看起来在传递对象，实际上实参是该对象的引用，对象本身未被复制，并且，转换不会在任何方面改变派生类型对象，该对象仍是派生类型对象。将派生类对象传给希望接受基类类型对象（而不是引用）的函数时，情况完全不同。在这种情况下，形参的类型是固定的——在编译时和运行时形参都是基类类型对象。如果用派生类型对象调用这样的函数，则该派生类对象的基类部分被复制到形参。即，一个是派生类对象转换为基类类型引用，一个是用派生类对象对基类对象进行初始化或赋值。
用派生类对象对基类对象进行初始化或赋值：对基类对象进行初始化或赋值，实际上是在调用函数：初始化时调用构造函数，赋值时调用赋值操作符。
用派生类对象对基类对象进行初始化或赋值时，有两种可能性。第一种（虽然不太可能的）可能性是，基类可能显式定义了将派生类型对象复制或赋值给基类对象的含义，这可以通过定义适当的构造函数或赋值操作符实现，在这种情况下，这些成员的定义将控制用 Derived 对象对 Base 对象进行初始化或赋值时会发生什么：
     class Derived;
     class Base {
     public:
         Base(const Derived&);  // create a new Base from a Derived
         Base &operator=(const Derived&);  // assign from a Derived
         // ...
     };
实际上基类一般（显式或隐式地）定义自己的复制构造函数和赋值操作符（第十三章），这些成员接受一个形参，该形参是基类类型的（const）引用。因为存在从派生类引用到基类引用的转换，这些复制控制成员可用于从派生类对象对基类对象进行初始化或赋值：
     Item_base item; // object of base type
     Bulk_item bulk; // object of derived type
     // ok: uses Item_base::Item_base(const Item_base&) constructor
     Item_base item(bulk);  // bulk is "sliced down" to its Item_base portion
     // ok: calls Item_base::operator=(const Item_base&)
     item = bulk;           // bulk is "sliced down" to its Item_base portion
用 Bulk_item 类型的对象调用 Item_base 类的复制构造函数或赋值操作符时，将发生下列步骤：
	1）将 Bulk_item 对象转换为 Item_base 引用，这仅仅意味着将一个 Item_base 引用绑定到 Bulk_item 对象。
	2）将该引用作为实参传给复制构造函数或赋值操作符。
	3）那些操作符使用 Bulk_item 的 Item_base 部分分别对调用构造函数或赋值的 Item_base 对象的成员进行初始化或赋值。
	4）一旦操作符执行完毕，对象即为 Item_base。它包含 Bulk_item 的 Item_base 部分的副本，但实参的 Bulk_item 部分被忽略。
在这种情况下，我们说 bulk 的 Bulk_item 部分在对 item 进行初始化或赋值时被“切掉”了。Item_base 对象只包含基类中定义的成员，不包含由任意派生类型定义的成员，Item_base 对象中没有派生类成员的存储空间。
如果是 public 继承，则用户代码和后代类都可以使用派生类到基类的转换。如果类是使用 private 或 protected 继承派生的，则用户代码不能将派生类型对象转换为基类对象。如果是 private 继承，则从 private 继承类派生的类不能转换为基类。如果是 protected 继承，则后续派生类的成员可以转换为基类类型。
无论是什么派生访问标号，派生类本身都可以访问基类的 public 成员，因此，派生类本身的成员和友元总是可以访问派生类到基类的转换。
基类到派生类的转换：没有从基类类型到派生类型的（自动）转换，甚至当基类指针或引用实际绑定到绑定到派生类对象时，从基类到派生类的转换也存在限制：
     Bulk_item bulk;
     Item_base *itemP = &bulk;  // ok: dynamic type is Bulk_item
     Bulk_item *bulkP = itemP;  // error: can't convert base to derived
编译器在编译时无法知道特定转换在运行时实际上是安全的。编译器确定转换是否合法，只看指针或引用的静态类型。在这些情况下，如果知道从基类到派生类的转换是安全的，就可以使用 static_cast强制编译器进行转换。或者，可以用 dynamic_cast 申请在运行时进行检查。

### 4. 构造函数和复制控制
构造函数和复制控制成员不能继承，每个类定义自己的构造函数和复制控制成员。像任何类一样，如果派生类不定义自己的默认构造函数和复制控制成员，就将使用合成版本。
本身不是派生类的基类，其构造函数和复制控制基本上不受继承影响。构造函数看起来像已经见过的许多构造函数一样.继承对基类构造函数的唯一影响是，在确定提供哪些构造函数时，必须考虑一类新用户。像任意其他成员一样，构造函数可以为 protected 或 private，某些类需要只希望派生类使用的特殊构造函数，这样的构造函数应定义为 protected.
派生类的构造函数受继承关系的影响，每个派生类构造函数除了初始化自己的数据成员之外，还要初始化派生类对象的基类部分。基类部分由基类的默认构造函数初始化.
因为 Bulk_item 具有内置类型成员，所以应定义自己的默认构造函数：
     class Bulk_item : public Item_base {
     public:
         Bulk_item(): min_qty(0), discount(0.0) { }
         // as before
     };
这个构造函数使用构造函数初始化列表初始化 min_qty 和 discount 成员，该构造函数还隐式调用 Item_base 的默认构造函数初始化对象的基类部分。运行这个构造函数的效果是，首先使用 Item_base 的默认构造函数初始化 Item_base 部分，那个构造函数将 isbn 置为空串并将 price 置为 0。Item_base 的构造函数执行完毕后，再初始化 Bulk_item 部分的成员并执行构造函数的函数体（函数体为空）。
向基类构造函数传递实参:
派生类构造函数的初始化列表只能初始化派生类的成员，不能直接初始化继承成员。相反派生类构造函数通过将基类包含在构造函数初始化列表中来间接初始化继承成员。
     class Bulk_item : public Item_base {
     public:
         Bulk_item(const std::string& book, double sales_price,
                   std::size_t qty = 0, double disc_rate = 0.0):
                      Item_base(book, sales_price), min_qty(qty), discount(disc_rate) { }
         // as before
      };
这个构造函数使用有两个形参的Item_base 的构造函数初始化基类子对象，它将自己的 book 和 sales_price 实参传递给该构造函数。这个构造函数可以这样使用：
     Bulk_item bulk("0-201-82470-1", 50, 5, .19);
要建立 bulk，首先运行 Item_base 构造函数，该构造函数使用从 Bulk_item 构造函数初始化列表传来的实参初始化 isbn 和 price。Item_base 构造函数执行完毕之后，再初始化 Bulk_item 的成员。最后，运行 Bulk_item 构造函数的（空）函数体。
一个类只能初始化自己的直接基类。直接就是在派生列表中指定的类。如果类 C 从类 B 派生，类 B 从类 A 派生，则 B 是 C 的直接基类。虽然每个 C 类对象包含一个 A 类部分，但 C 的构造函数不能直接初始化 A 部分。相反，需要类 C 初始化类 B，而类 B 的构造函数再初始化类 A。这一限制的原因是，类 B 的作者已经指定了怎样构造和初始化 B 类型的对象。像类 B 的任何用户一样，类 C 的作者无权改变这个规约。
派生类也可以使用合成复制控制成员。合成操作对对象的基类部分连同派生部分的成员一起进行复制、赋值或撤销，使用基类的复制构造函数、赋值操作符或析构函数对基类部分进行复制、赋值或撤销。Item_base 类及其派生类可以使用复制控制操作的合成版本。复制 Bulk_item 对象时，调用（合成的）Item_base 复制构造函数复制 isbn 和 price 成员。使用 string 复制构造函数复制 isbn，直接复制 price 成员。一旦复制了基类部分，就复制派生部分。Bulk_item 的两个成员都是 double 型，直接复制这些成员。赋值操作符和析构函数类似处理。
如果派生类显式定义自己的复制构造函数或赋值操作符，则该定义将完全覆盖默认定义。被继承类的复制构造函数和赋值操作符负责对基类成分以及类自己的成员进行复制或赋值。
如果派生类定义了自己的复制构造函数，该复制构造函数一般应显式使用基类复制构造函数初始化对象的基类部分：
     class Base { /* ... */ };
     class Derived: public Base {
     public:
         // Base::Base(const Base&) not invoked automatically
         Derived(const Derived& d):
              Base(d) /* other member initialization */ { /*... */ }
     };
初始化函数 Base(d) 将派生类对象 d 转换为它的基类部分的引用，并调用基类复制构造函数。如果省略基类初始化函数，如下代码：
     Derived(const Derived& d) /* derived member initizations */
               {/* ... */ }
效果是运行 Base 的默认构造函数初始化对象的基类部分。假定 Derived 成员的初始化从 d 复制对应成员，则新构造的对象将具有奇怪的配置：它的 Base 部分将保存默认值，而它的 Derived 成员是另一对象的副本。
派生类赋值操作符：赋值操作符通常与复制构造函数类似：如果派生类定义了自己的赋值操作符，则该操作符必须对基类部分进行显式赋值。
     Derived &Derived::operator=(const Derived &rhs)
     {
        if (this != &rhs) {
            Base::operator=(rhs); // assigns the base part
            // do whatever needed to clean up the old value in the derived part
            // assign the members from the derived
        }
        return *this;
     }
赋值操作符必须防止自身赋值。假定左右操作数不同，则调用 Base 类的赋值操作符给基类部分赋值。该操作符可以由类定义，也可以是合成赋值操作符，这没什么关系——我们可以直接调用它。基类操作符将释放左操作数中基类部分的值，并赋以来自 rhs 的新值。该操作符执行完毕后，接着要做的是为派生类中的成员赋值。
派生类析构函数：派生类析构函数不负责撤销基类对象的成员。编译器总是显式调用派生类对象基类部分的析构函数。每个析构函数只负责清除自己的成员：
     class Derived: public Base {
     public:
         // Base::~Base invoked automatically
         ~Derived()    { /* do what it takes to clean up derived members */ }
      };
	对象的撤销顺序与构造顺序相反：首先运行派生析构函数，然后按继承层次依次向上调用各基类析构函数。
虚析构函数：删除指向动态分配对象的指针时，需要在释放对象的内存之前运行析构函数清除对象。处理继承层次中的对象时，指针的静态类型可能与被删除对象的动态类型不同，可能会删除实际指向派生类对象的基类类型指针。如果删除基类指针，则需要运行基类析构函数并清除基类的成员，如果对象实际是派生类型的，则没有定义该行为。要保证运行适当的析构函数，基类中的析构函数必须为虚函数：
     class Item_base {
     public:
         // no work, but virtual destructor needed
         // if base pointer that points to a derived object is ever deleted
         virtual ~Item_base() { }
     };
如果析构函数为虚函数，那么通过指针调用时，运行哪个析构函数将因指针所指对象类型的不同而不同：
     Item_base *itemP = new Item_base; // same static and dynamic type
     delete itemP;          // ok: destructor for Item_base called
     itemP = new Bulk_item; // ok: static and dynamic types differ
     delete itemP;          // ok: destructor for Bulk_item called
像其他虚函数一样，析构函数的虚函数性质都将继承。因此，如果层次中根类的析构函数为虚函数，则派生类析构函数也将是虚函数，无论派生类显式定义析构函数还是使用合成析构函数，派生类析构函数都是虚函数。
只有析构函数应定义为虚函数，构造函数不能定义为虚函数。构造函数是在对象完全构造之前运行的，在构造函数运行的时候，对象的动态类型还不完整。
虽然可以在基类中将成员函数 operator= 定义为虚函数，但这样做并不影响派生类中使用的赋值操作符。每个类有自己的赋值操作符，派生类中的赋值操作符有一个与类本身类型相同的形参，该类型必须不同于继承层次中任意其他类的赋值操作符的形参类型。
将赋值操作符设为虚函数可能会令人混淆，因为虚函数必须在基类和派生类中具有同样的形参。基类赋值操作符有一个形参是自身类类型的引用，如果该操作符为虚函数，则每个类都将得到一个虚函数成员，该成员定义了参数为一个基类对象的 operator=。但是，对派生类而言，这个操作符与赋值操作符是不同的。
构造函数和析构函数中的虚函数：构造派生类对象时首先运行基类构造函数初始化对象的基类部分。在执行基类构造函数时，对象的派生类部分是未初始化的。实际上，此时对象还不是一个派生类对象。撤销派生类对象时，首先撤销它的派生类部分，然后按照与构造顺序的逆序撤销它的基类部分。在这两种情况下，运行构造函数或析构函数的时候，对象都是不完整的。为了适应这种不完整，编译器将对象的类型视为在构造或析构期间发生了变化。在基类构造函数或析构函数中，将派生类对象当作基类类型对象对待。如果在构造函数或析构函数中调用虚函数，则运行的是为构造函数或析构函数自身类型定义的版本。

### 5. 继承情况下的类作用域
在继承情况下，派生类的作用域嵌套在基类作用域中。如果不能在派生类作用域中确定名字，就在外围基类作用域中查找该名字的定义。正是这种类作用域的层次嵌套使我们能够直接访问基类的成员。
与基类成员同名的派生类成员将屏蔽对基类成员的直接访问。可以使用作用域操作符访问被屏蔽的基类成员：
     struct Derived : Base {
         int get_base_mem() { return Base::mem; }
     };
在基类和派生类中使用同一名字的成员函数，其行为与数据成员一样：在派生类作用域中派生类成员将屏蔽基类成员。即使函数原型不同，基类成员也会被屏蔽：
     struct Base {
         int memfcn();
     };
     struct Derived : Base {
         int memfcn(int); // hides memfcn in the base
     };
     Derived d; Base b;
     b.memfcn();        // calls Base::memfcn
     d.memfcn(10);      // calls Derived::memfcn
     d.memfcn();        // error: memfcn with no arguments is hidden
     d.Base::memfcn();  // ok: calls Base::memfcn
局部作用域中声明的函数不会重载全局作用域中定义的函数，同样，派生类中定义的函数也不重载基类中定义的成员。通过派生类对象调用函数时，实参必须与派生类中定义的版本相匹配，只有在派生类根本没有定义该函数时，才考虑基类函数。
成员函数（无论虚还是非虚）也可以重载。派生类可以重定义所继承的 0 个或多个版本。如果派生类重定义了重载成员，则通过派生类型只能访问派生类中重定义的那些成员。如果派生类想通过自身类型使用所有的重载版本，则派生类必须要么重定义所有重载版本，要么一个也不重定义。
派生类不用重定义所继承的每一个基类版本，它可以为重载成员提供 using 声明。一个 using 声明只能指定一个名字，不能指定形参表，因此，为基类成员函数名称而作的 using 声明将该函数的所有重载实例加到派生类的作用域。将所有名字加入作用域之后，派生类只需要重定义本类型确实必须定义的那些函数，对其他版本可以使用继承的定义。
如果基类成员与派生类成员接受的实参不同，就没有办法通过基类类型的引用或指针调用派生类函数。考虑如下（人为的）为集合：
     class Base {
     public:
         virtual int fcn();
     };
     class D1 : public Base {
     public:
          // hides fcn in the base; this fcn is not virtual
          int fcn(int); // parameter list differs from fcn in Base
          // D1 inherits definition of Base::fcn()
     };
     class D2 : public D1 {
     public:
         int fcn(int); // nonvirtual function hides D1::fcn(int)
         int fcn();    // redefines virtual fcn from Base
     };
D1 中的 fcn 版本没有重定义 Base 的虚函数 fcn，相反，它屏蔽了基类的 fcn。结果 D1 有两个名为 fcn 的函数：类从 Base 继承了一个名为 fcn 的虚函数，类又定义了自己的名为 fcn 的非虚成员函数，该函数接受一个 int 形参。但是，从 Base 继承的虚函数不能通过 D1 对象（或 D1 的引用或指针）调用，因为该函数被 fcn(int) 的定义屏蔽了.
通过基类类型的引用或指针调用函数时，编译器将在基类中查找该函数而忽略派生类：
     Base bobj;  D1 d1obj;  D2 d2obj;
     Base *bp1 = &bobj, *bp2 = &d1obj, *bp3 = &d2obj;
     bp1->fcn();   // ok: virtual call, will call Base::fcnat run time
     bp2->fcn();   // ok: virtual call, will call Base::fcnat run time
     bp3->fcn();   // ok: virtual call, will call D2::fcnat run time
理解 C++ 中继承层次的关键在于理解如何确定函数调用。确定函数调用遵循以下四个步骤：
	1)首先确定进行函数调用的对象、引用或指针的静态类型。
	2)在该类中查找函数，如果找不到，就在直接基类中查找，如此循着类的继承链往上找，直到找到该函数或者查找完最后一个类。如果不能在类或其相关基类中找到该名字，则调用是错误的。
	3)一旦找到了该名字，就进行常规类型检查（第 7.1.2 节），查看如果给定找到的定义，该函数调用是否合法。
	4)假定函数调用合法，编译器就生成代码。如果函数是虚函数且通过引用或指针调用，则编译器生成代码以确定根据对象的动态类型运行哪个函数版本，否则，编译器生成代码直接调用函数

### 6. 纯虚函数
将函数定义为纯虚能够说明，该函数为后代类型提供了可以覆盖的接口，但是这个类中的版本决不会调用.
含有（或继承）一个或多个纯虚函数的类是抽象基类。除了作为抽象基类的派生类的对象的组成部分，不能创建抽象类型的对象.
在函数形参表后面写上 = 0 以指定纯虚函数：
     class Disc_item : public Item_base {
     public:
         double net_price(std::size_t) const = 0;
     };
试图创建抽象基类的对象将发生编译时错误：
     Disc_item discounted; // error: can't define a Disc_item object

### 7. 容器与继承
如果定义 multiset 保存基类类型的对象：
     multiset<Item_base> basket;
     Item_base base;
     Bulk_item bulk;
     basket.insert(base);  // ok: add copy of base to basket
     basket.insert(bulk);  // ok: but bulk sliced down to its base part
则加入派生类型的对象时，只将对象的基类部分保存在容器中。不能通过定义容器保存派生类对象来解决这个问题。在这种情况下，不能将 Item_base 对象放入容器——没有从基类类型到派生类型的标准转换。可以显式地将基类对象强制转换为派生类对象并将结果对象加入容器，但是，如果这样做，当试图使用这样的元素时，会产生大问题：在这种情况下，元素可以当作派生类对象对待，但派生类部分的成员将是未初始化的.
唯一可行的选择可能是使用容器保存对象的指针。这个策略可行，但代价是需要用户面对管理对象和指针的问题，用户必须保证只要容器存在，被指向的对象就存在。如果对象是动态分配的，用户必须保证在容器消失时适当地释放对象。

### 8. 句柄类与继承
C++ 中面向对象编程不能使用对象支持面向对象编程，相反，必须使用指针或引用。但是，使用指针或引用会加重类用户的负担。
C++ 中一个通用的技术是定义包装（cover）类或句柄类。句柄类存储和管理基类指针。指针所指对象的类型可以变化，它既可以指向基类类型对象又可以指向派生类型对象。用户通过句柄类访问继承层次的操作。因为句柄类使用指针执行操作，虚成员的行为将在运行时根据句柄实际绑定的对象的类型而变化。因此，句柄的用户可以获得动态行为但无须操心指针的管理。
包装了继承层次的句柄有两个重要的设计考虑因素：
	1）像对任何保存指针的类一样，必须确定对复制控制做些什么。包装了继承层次的句柄通常表现得像一个智能指针或者像一个值。
	2）句柄类决定句柄接口屏蔽还是不屏蔽继承层次，如果不屏蔽继承层次，用户必须了解和使用基本层次中的对象。
  指针型句柄实例（省略）【Coding】

### 9. 再谈文本查询示例（省略）【Coding】

## 第十六章 模板和泛型编程
### 1. 模板定义
定义函数模板：函数模板是一个独立于类型的函数，可作为一种方式，产生函数的特定类型版本。模板定义以关键字 template 开始，后接模板形参表，模板形参表是用尖括号括住的一个或多个模板形参的列表，形参之间以逗号分隔。模板形参表不能为空。
	 // implement strcmp-like generic compare function
     // returns 0 if the values are equal, 1 if v1 is larger, -1 if v1 is smaller
     template <typename T>
     int compare(const T &v1, const T &v2)
     {
         if (v1 < v2) return -1;
         if (v2 < v1) return 1;
         return 0;
     }
模板形参表：模板形参表示可以在类或函数的定义中使用的类型或值。表示哪个实际类型由编译器根据所用的函数而确定。模板形参可以是表示类型的类型形参，也可以是表示常量表达式的非类型形参。非类型形参跟在类型说明符之后声明，类型形参跟在关键字 class 或 typename 之后定义，例如，class T 是名为 T 的类型形参，在这里 class 和 typename 没有区别。
	编译器将确定用什么类型代替每个类型形参，以及用什么值代替每个非类型形参。推导出实际模板实参后，编译器使用实参代替相应的模板形参产生编译该版本的函数：
         cout << compare(1, 0) << endl; 	// T is int;
         string s1 = "hi", s2 = "world";
         cout << compare(s1, s2) << endl;  // T is string;
inline 函数模板：函数模板可以用与非模板函数一样的方式声明为 inline。说明符放在模板形参表之后、返回类型之前，不能放在关键字 template 之前。
     // ok: inline specifier follows template parameter list
     template <typename T> inline T min(const T&, const T&);
     // error: incorrect placement of inline specifier
     inline template <typename T> T min(const T&, const T&);
定义类模板：
	template <class Type> class Queue {
     public:
         Queue ();                // default constructor
         Type &front ();          // return element from head of Queue
         const Type &front () const;
         void push (const Type &); // add element to back of Queue
         void pop();              // remove element from head of Queue
         bool empty() const;      // true if no elements in the Queue
     private:
         // ...
     };
使用类模板时，必须为模板形参显式指定实参：
     Queue<int> qi;                 // Queue that holds ints
     Queue< vector<double> > qc;    // Queue that holds vectors of doubles
     Queue<string> qs;              // Queue that holds strings
模板形参作用域：模板形参的名字可以在声明为模板形参之后直到模板声明或定义的末尾处使用。模板形参遵循常规名字屏蔽规则。与全局作用域中声明的对象、函数或类型同名的模板形参会屏蔽全局名字：
     typedef double T;
     template <class T> T calc(const T &a, const T &b)
     {
          // tmp has the type of the template parameter T
          // not that of the global typedef
          T tmp = a;
          // ...
          return tmp;
     }
使用模板形参名字的限制：用作模板形参的名字不能在模板内部重用。
     template <class T> T calc(const T &a, const T &b)
     {
         typedef double T; // error: redeclares template parameter T
         T tmp = a;
         // ...
         return tmp;
     }
这一限制还意味着模板形参的名字只能在同一模板形参表中使用一次：
     // error: illegal reuse of template parameter name V
     template <class V, class V> V calc(const V&, const V&) ;
模板声明：像其他任意函数或类一样，对于模板可以只声明而不定义。声明必须指出函数或类是一个模板：
	// declares compare but does not define it
     template <class T> int compare(const T&, const T&) ;
同一模板的声明和定义中，模板形参的名字不必相同。
     // all three uses of calc refer to the same function template
     // forward declarations of the template
     template <class T> T calc(const T&, const T&) ;
     template <class U> U calc(const U&, const U&) ;
     // actual definition of the template
     template <class Type>
     Type calc(const Type& a, const Type& b) { /* ... */ }
每个模板类型形参前面必须带上关键字 class 或 typename，每个非类型形参前面必须带上类型名字，省略关键字或类型说明符是错误的：
     // error: must precede U by either typename or class
     template <typename T, U> T calc (const T&, const U&) ;
typename 与 class 的区别：在函数模板形参表中，关键字 typename 和 class 具有相同含义，可以互换使用，两个关键字都可以在同一模板形参表中使用：
     // ok: no distinction between typename and class in template parameter list
     template <typename T, class U> calc (const T&, const U&);
	关键字 typename 是作为标准 C++ 的组成部分加入到 C++ 中的，因此旧的程序更有可能只用关键字 class。
通过在成员名前加上关键字 typename 作为前缀，可以告诉编译器将成员当作类型。默认情况下，编译器假定这样的名字指定数据成员，而不是类型。
	template <class Parm, class U>
     Parm fcn(Parm* array, U value)
     {
         typename Parm::size_type * p; // ok: declares p to be a pointer
     }
这一声明给用实例化 fcn 的类型增加了一个职责：那些类型必须具有名为 size_type 的成员，而且该成员是一个类型。
非类型模板形参：
	在调用函数时非类型形参将用值代替，值的类型在模板形参表中指定。下面的函数模板声明了 array_init 是一个含有一个类型模板形参和一个非类型模板形参的函数模板。函数本身接受一个形参，该形参是数组的引用
     // initialize elements of an array to zero
     template <class T, size_t N> void array_init(T (&parm)[N])
     {
         for (size_t i = 0; i != N; ++i) {
             parm[i] = 0;
         }
     }
	int x[42];
     double y[10];
     array_init(x);  // instantiates array_init(int(&)[42]
     array_init(y);  // instantiates array_init(double(&)[10]
在函数模板内部完成的操作限制了可用于实例化该函数的类型。程序员的责任是，保证用作函数实参的类型实际上支持所用的任意操作，以及保证在模板使用哪些操作的环境中那些操作运行正常。
编写模板代码时，对实参类型的要求尽可能少是很有益的。编写泛型代码的两个重要原则：
	1）模板的形参是 const 引用。
	2）函数体中的测试只用 < 比较。
链接时的编译时错误：编译模板时，编译器可能会在三个阶段中标识错误：第一阶段是编译模板定义本身时。在这个阶段中编译器一般不能发现许多错误，可以检测到诸如漏掉分号或变量名拼写错误一类的语法错误。第二个错误检测时间是在编译器见到模板的使用时。在这个阶段，编译器仍没有很多检查可做。对于函数模板的调用，许多编译器只检查实参的数目和类型是否恰当，编译器可以检测到实参太多或太少，也可以检测到假定类型相同的两个实参是否真地类型相同。对于类模板，编译器可以检测提供的模板实参的正确数目。产生错误的第三个时间是在实例化的时候，只有在这个时候可以发现类型相关的错误。根据编译器管理实例化的方式，有可能在链接时报告这些错误。要认识到编译模板定义的时候，对程序是否有效所知不多。类似地，甚至可能会在已经成功编译了使用模板的每个文件之后出现编译错误。只在实例化期间检测错误的情况很少，错误检测可能发生在链接时。

### 2. 实例化
类模板不定义类型，只有特定的实例才定义了类型。例如，Queue 不是类型，而 Queue<int> 或 Queue<string> 是类型。
模板类型形参可以用作一个以上函数形参的类型。在这种情况下，模板类型推断必须为每个对应的函数实参产生相同的模板实参类型。如果推断的类型不匹配，则调用将会出错：
     template <typename T>
     int compare(const T& v1, const T& v2)
     {
         if (v1 < v2) return -1;
         if (v2 < v1) return 1;
         return 0;
     }
     int main()
     {
         short si;
         // error: cannot instantiate compare(short, int)
         // must be: compare(short, short) or
         // compare(int, int)
         compare(si, 1024);
         return 0;
     }
如果 compare 的设计者想要允许实参的常规转换，则函数必须用两个类型形参来定义，但是，比较那些类型的值的 < 操作符必须存在：
     // argument types can differ, but must be compatible
     template <typename A, typename B>
     int compare(const A& v1, const B& v2)
     {
         if (v1 < v2) return -1;
         if (v2 < v1) return 1;
         return 0;
     }
类型形参的实参的受限转换：一般而论，不会转换实参以匹配已有的实例化，相反，会产生新的实例。除了产生新的实例化之外，编译器只会执行两种转换：
	1）const 转换：接受 const 引用或 const 指针的函数可以分别用非 const 对象的引用或指针来调用，无须产生新的实例化。如果函数接受非引用类型，形参类型实参都忽略 const，即，无论传递 const 或非 const 对象给接受非引用类型的函数，都使用相同的实例化。
	2）数组或函数到指针的转换：如果模板形参不是引用类型，则对数组或函数类型的实参应用常规指针转换。数组实参将当作指向其第一个元素的指针，函数实参当作指向函数类型的指针。
例如，考虑对函数 fobj 和 fref 的调用。fobj 函数复制它的形参，而 fref 的形参是引用：
     template <typename T> T fobj(T, T); // arguments are copied
     template <typename T>
     T fref(const T&, const T&);       // reference arguments
     string s1("a value");
     const string s2("another value");
     fobj(s1, s2);     // ok: calls f(string, string), const is ignored
     fref(s1, s2);     // ok: non const object s1 converted to const reference
     int a[10], b[42];
     fobj(a, b); // ok: calls f(int*, int*)
     fref(a, b); // error: array types don't match; arguments aren't converted to pointers
在第二种情况中，将传递不同长度的数组实参。fref 的调用是非法的，当形参为引用时，数组不能转换为指针，a 和 b 的类型不匹配，所以调用将出错。
类型转换的限制只适用于类型为模板形参的那些实参。用普通类型定义的形参可以使用常规转换。
模板实参推断与函数指针：可以使用函数模板对函数指针进行初始化或赋值，这样做的时候，编译器使用指针的类型实例化具有适当模板实参的模板版本。
	 template <typename T> int compare(const T&, const T&);
     int (*pf1) (const int&, const int&) = compare;
pf1 的类型是一个指针，指向“接受两个 const int& 类型形参并返回 int 值的函数”，形参的类型决定了 T 的模板实参的类型，T 的模板实参为 int 型，指针 pf1 引用的是将 T 绑定到 int 的实例化。
获取函数模板实例化的地址的时候，上下文必须是这样的：它允许为每个模板形参确定唯一的类型或值：
	 void func(int(*) (const string&, const string&));
     void func(int(*) (const int&, const int&));
     func(compare); // error: which instantiation of compare?
函数模板的显式实参：在某些情况下，不可能推断模板实参的类型。当函数的返回类型必须与形参表中所用的所有类型都不同时，最常出现这一问题。在这种情况下，有必要覆盖模板实参推断机制，并显式指定为模板形参所用的类型或值。
	template <class T, class U> ??? sum(T, U);
解决这一问题的一个办法，可能是强制 sum 的调用者将较小的类型强制转换为希望作为结果使用的类型：
     // ok: now either T or U works as return type
     int i; short s;
     sum(static_cast<int>(s), i); // ok: instantiates int sum(int, int)
在返回类型中使用类型形参：指定返回类型的一种方式是引入第三个模板形参，它必须由调用者显式指定：
     template <class T1, class T2, class T3>
     T1 sum(T2, T3);
没有实参的类型可用于推断 T1 的类型，相反，调用者必须在每次调用 sum 时为该形参显式提供实参。在以逗号分隔、用尖括号括住的列表中指定显式模板实参。显式模板类型的列表出现在函数名之后、实参表之前：
     long val3 = sum<long>(i, lng); // ok: calls long sum(int, long)
这一调用显式指定 T1 的类型，编译器从调用中传递的实参推断 T2 和 T3 的类型。
显式模板实参从左至右对应模板形参相匹配，假如可以从函数形参推断，则结尾（最右边）形参的显式模板实参可以省略。如果这样编写 sum 函数：
     template <class T1, class T2, class T3>
     T3 alternative_sum(T2, T1);
则总是必须为所有三个形参指定实参：
     // error: can't infer initial template parameters
     long val3 = alternative_sum<long>(i, lng);
     // ok: All three parameters explicitly specified
     long val2 = alternative_sum<long, int, long>(i, lng);
显式实参与函数模板的指针：通过使用显式模板实参能够消除二义性：
     template <typename T> int compare(const T&, const T&);
     // overloaded versions of func; each take a different function pointer type
     void func(int(*) (const string&, const string&));
     void func(int(*) (const int&, const int&));
     func(compare<int>); // ok: explicitly specify which version of compare

### 3. 模板编译模型
当编译器看到模板定义的时候，它不立即产生代码。只有在看到用到模板时，如调用了函数模板或调用了类模板的对象的时候，编译器才产生特定类型的模板实例。
一般而言，当调用函数的时候，编译器只需要看到函数的声明。类似地，定义类类型的对象时，类定义必须可用，但成员函数的定义不是必须存在的。因此，应该将类定义和函数声明放在头文件中，而普通函数和类成员函数的定义放在源文件中。模板则不同：要进行实例化，编译器必须能够访问定义模板的源代码。当调用函数模板或类模板的成员函数的时候，编译器需要函数定义，需要那些通常放在源文件中的代码。
标准 C++ 为编译模板代码定义了两种模型。在两种模型中，构造程序的方式很大程度上是相同的：类定义和函数声明放在头文件中，而函数定义和成员定义放在源文件中。两种模型的不同在于，编译器怎样使用来自源文件的定义。
包含编译模型：编译器必须看到用到的所有模板的定义。一般而言，可以通过在声明函数模板或类模板的头文件中添加一条 #include 指示使定义可用，该 #include 引入了包含相关定义的源文件。
分别编译模型：编译器会为我们跟踪相关的模板定义。但是，我们必须让编译器知道要记住给定的模板定义，可以使用 export 关键字来做这件事。export 关键字能够指明给定的定义可能会需要在其他文件中产生实例化。在一个程序中，一个模板只能定义为导出一次。编译器在需要产生这些实例化时计算出怎样定位模板定义。export 关键字不必在模板声明中出现。
	export template <typename Type>
     Type sum(Type t1, Type t2) /* ...*/
这个函数模板的声明像通常一样应放在头文件中，声明不必指定 export。相反，应该在类的实现文件中使用 export：
     // class template header goes in shared header file
     template <class Type> class Queue { ... };
     // Queue.ccimplementation file declares Queue as exported
     export template <class Type> class Queue;
     #include "Queue.h"
     // Queue member definitions
导出类的成员将自动声明为导出的。也可以将类模板的个别成员声明为导出的，在这种情况下，关键字 export 不在类模板本身指定，而是只在被导出的特定成员定义上指定。导出成员函数的定义不必在使用成员时可见。任意非导出成员的定义必须像在包含模型中一样对待：定义应放在定义类模板的头文件中。

### 4. 类模板成员（Queue 类模板的具体实现）【Coding】

### 5. 一个泛型句柄类
Handle 类行为类似于指针：复制 Handle 对象将不会复制基础对象，复制之后，两个 Handle 对象将引用同一基础对象。要创建 Handle 对象，用户需要传递属于由 Handle 管理的类型（或从该类型派生的类型）的动态分配对象的地址，从此刻起，Handle 将“拥有”这个对象。而且，一旦不再有任意 Handle 对象与该对象关联，Handle 类将负责删除该对象。
	template <class T> class Handle {
     public:
         // unbound handle
         Handle(T *p = 0): ptr(p), use(new size_t(1)) { }
         // overloaded operators to support pointer behavior
         T& operator*();
         T* operator->();
         const T& operator*() const;
         const T* operator->() const;
         // copy control: normal pointer behavior, but last Handle deletes the object
         Handle(const Handle& h): ptr(h.ptr), use(h.use)
                                             { ++*use; }
         Handle& operator=(const Handle&);
         ~Handle() { rem_ref(); }
     private:
         T* ptr;          // shared object
         size_t *use;     // count of how many Handle spointto *ptr
         void rem_ref()
             { if (--*use == 0) { delete ptr; delete use; } }
     };

	template <class T>
     inline Handle<T>& Handle<T>::operator=(const Handle &rhs)
     {
         ++*rhs.use;      // protect against self-assignment
         rem_ref();       // decrement use count and delete pointers if needed
         ptr = rhs.ptr;
         use = rhs.use;
         return *this;
     }
	template <class T> inline T& Handle<T>::operator*()
     {
         if (ptr) return *ptr;
         throw std::runtime_error
                        ("dereference of unbound Handle");
     }
     template <class T> inline T* Handle<T>::operator->()
     {
         if (ptr) return ptr;
         throw std::runtime_error
                        ("access through unbound Handle");
     }
分配一个 int 对象，并将一个 Handle 对象绑定到新分配的 int 对象来说明 Handle 的行为：
     { // new scope
       // user allocates but must not delete the object to which the Handle is attached
       Handle<int> hp(new int(42));
       { // new scope
           Handle<int> hp2 = hp; // copies pointer; use count incremented
           cout << *hp << " " << *hp2 << endl; // prints 42 42
           *hp2 = 10;           // changes value of shared underlying int
       }   // hp2 goes out of scope; use count is decremented
       cout << *hp << endl; // prints 10
     } // hp goes out of scope; its destructor deletes the int
即使是 Handle 的用户分配了 int 对象，Handle 析构函数也将删除它。在外层代码块末尾最后一个 Handle 对象超出作用域时，删除该 int 对象。为了访问基础对象，应用了 Handle 的 * 操作符，该操作符返回对基础 int 对象的引用。
使用 Handle 对象对指针进行使用计数：
	class Sales_item {
     public:
         // default constructor: unbound handle
         Sales_item(): h() { }
         // copy item and attach handle to the copy
         Sales_item(const Item_base &item): h(item.clone()) { }
         // no copy control members: synthesized versions work
         // member access operators: forward their work to the Handle class
         const Item_base& operator*() const { return *h; }
         const Item_base* operator->() const
                                { return h.operator->(); }
     private:
         Handle<Item_base> h; // use-counted handle
     };
基于 Handle 的 Sales_item 版本有一个数据成员，该数据成员是关联传给构造函数的 Item_base 对象的副本上的 Handle 对象。因为 Sales_item 的这个版本没有指针成员，所以不需要复制控制成员，Sales_item 的这个版本可以安全地使用合成的复制控制成员。管理使用计数和相关 Item_base 对象的工作在 Handle 内部完成。
double Basket::total() const
     {
         double sum = 0.0; // holds the running total
         /* find each set of items with the same isbn and calculate
          * the net price for that quantity of items
          * iter refers to first copy of each book in the set
          * upper_boundrefers to next element with a different isbn
          */
         for (const_iter iter = items.begin();
                         iter != items.end();
                         iter = items.upper_bound(*iter))
         {
             // we know there's at least one element with this key in the Basket
             // virtual call to net_priceapplies appropriate discounts, if any
             sum += (*iter)->net_price(items.count(*iter));
         }
         return sum;
     }

### 6. 模板特化（高级主题）

### 7. 重载与函数模板
函数模板可以重载：可以定义有相同名字但形参数目或类型不同的多个函数模板，也可以定义与函数模板有相同名字的普通非模板函数。
如果重载函数中既有普通函数又有函数模板，确定函数调用的步骤如下：
	1）为这个函数名建立候选函数集合，包括：
		a. 与被调用函数名字相同的任意普通函数.
		b. 任意函数模板实例化，在其中，模板实参推断发现了与调用中所用函数实参相匹配的模板实参.
	2) 确定哪些普通函数是可行的（如果有可行函数的话）。候选集合中的每个模板实例都是可行的，因为模板实参推断保证函数可以被调用.
	3) 如果需要转换来进行调用，根据转换的种类排列可靠函数，记住，调用模板函数实例所允许的转换是有限的.
		a. 如果只有一个函数可选，就调用这个函数.
		b. 如果调用有二义性，从可行函数集合中去掉所有函数模板实例.
	4) 重新排列去掉函数模板实例的可行函数.
		a. 如果只有一个函数可选，就调用这个函数.
		b. 否则，调用有二义性.
// compares two objects
     template <typename T> int compare(const T&, const T&);
     // compares elements in two sequences
     template <class U, class V> int compare(U, U, V);
     // plain functions to handle C-style character strings
     int compare(const char*, const char*);
可以在不同类型上调用这些函数:
// calls compare(const T&, const T&) with T bound to int
     compare(1, 0);
     // calls compare(U, U, V), with U and V bound to vector<int>::iterator
     vector<int> ivec1(10), ivec2(20);
     compare(ivec1.begin(), ivec1.end(), ivec2.begin());
     int ia1[] = {0,1,2,3,4,5,6,7,8,9};
     // calls compare(U, U, V) with U bound to int*
     // and V bound to vector<int>::iterator
     compare(ia1, ia1 + 10, ivec1.begin());
     // calls the ordinary function taking const char* parameters
     const char const_arr1[] = "world", const_arr2[] = "hi";
     compare(const_arr1, const_arr2);
     // calls the ordinary function taking const char* parameters
     char ch_arr1[] = "world", ch_arr2[] = "hi";
     compare(ch_arr1, ch_arr2);
转换与重载的函数模板:
	char *p1 = ch_arr1, *p2 = ch_arr2;
     compare(p1, p2);
在这个例子中，将 char* 绑定到 T 的函数模板与该调用完全匹配。普通版本仍然需要从 char* 到 const char* 的转换，所以优先选择函数模板.
当匹配同样好时，非模板版本优先.
定义函数模板特化几乎总是比使用非模板版本更好.

## 第十七章　用于大型程序的工具
### 1. 异常处理
异常是通过抛出对象而引发的。该对象的类型决定应该激活哪个处理代码。被选中的处理代码是调用链中与该对象类型匹配且离抛出异常位置最近的那个. 执行 throw 的时候，不会执行跟在 throw 后面的语句，而是将控制从 throw 转移到匹配的 catch，该 catch 可以是同一函数中局部的 catch，也可以在直接或间接调用发生异常的函数的另一个函数中.
因为在处理异常的时候会释放局部存储，所以被抛出的对象就不能再局部存储，而是用 throw 表达式初始化一个称为异常对象的特殊对象。异常对象由编译器管理，而且保证驻留在可能被激活的任意 catch 都可以访问的空间。这个对象由 throw 创建，并被初始化为被抛出的表达式的副本。异常对象将传给对应的 catch，并且在完全处理了异常之后撤销. 异常对象通过复制被抛出表达式的结果创建，该结果必须是可以复制的类型.
当抛出一个表达式的时候，被抛出对象的静态编译时类型将决定异常对象的类型.
抛出指针通常是个坏主意：无论对象的实际类型是什么，异常对象的类型都与指针的静态类型相匹配。如果该指针是一个指向派生类对象的基类类型指针，则那个对象将被分割，只抛出基类部分。如果抛出指针本身，可能会引发比分割对象更严重的问题。具体而言，抛出指向局部对象的指针总是错误的，其理由与从函数返回指向局部对象的指针是错误的一样抛出指针的时候，必须确定进入处理代码时指针所指向的对象存在.
栈展开: 抛出异常的时候，将暂停当前函数的执行，开始查找匹配的 catch 子句。首先检查 throw 本身是否在 try 块内部，如果是，检查与该 catch 相关的 catch 子句，看是否其中之一与抛出对象相匹配。如果找到匹配的 catch，就处理异常；如果找不到，就退出当前函数（释放当前函数的内存并撤销局部对象），并且继续在调用函数中查找. 如果对抛出异常的函数的调用是在 try 块中，则检查与该 try 相关的 catch 子句。如果找到匹配的 catch，就处理异常；如果找不到匹配的 catch，调用函数也退出，并且继续在调用这个函数的函数中查找. 沿嵌套函数调用链继续向上，直到为异常找到一个 catch 子句。只要找到能够处理异常的 catch 子句，就进入该 catch 子句，并在该处理代码中继续执行。当 catch 结束的时候，在紧接在与该 try 块相关的最后一个 catch 子句之后的点继续执行.
栈展开期间，释放局部对象所用的内存并运行类类型局部对象的析构函数。如果一个块直接分配资源，而且在释放资源之前发生异常，在栈展开期间将不会释放该资源。例如，一个块可以通过调用 new 动态分配内存，如果该块因异常而退出，编译器不会删除该指针，已分配的内在将不会释放。
析构函数应该从不抛出异常：栈展开期间会经常执行析构函数。在执行析构函数的时候，已经引发了异常但还没有处理它。如果在这个过程中析构函数本身抛出新的异常，将会导致调用标准库 terminate 函数。一般而言，terminate 函数将调用 abort 函数，强制从整个程序非正常退出。标准库类型都保证它们的析构函数不会引发异常。
构造函数内部所做的事情经常会抛出异常。如果在构造函数对象的时候发生异常，则该对象可能只是部分被构造，它的一些成员可能已经初始化，而另一些成员在异常发生之前还没有初始化。即使对象只是部分被构造了，也要保证将会适当地撤销已构造的成员。在初始化数组或其他容器类型的元素的时候，也可能发生异常，同样，也要保证将会适当地撤销已构造的元素。
如果找不到匹配的 catch，程序就调用库函数 terminate。
捕获异常：catch 子句中的异常说明符看起来像只包含一个形参的形参表，异常说明符是在其后跟一个（可选）形参名的类型名。说明符的类型决定了处理代码能够捕获的异常种类。类型必须是完全类型，即必须是内置类型或者是已经定义的程序员自定义类型。类型的前向声明不行。当 catch 为了处理异常只需要了解异常的类型的时候，异常说明符可以省略形参名。
在查找匹配的 catch 期间，找到的 catch 不必是与异常最匹配的那个 catch，相反，将选中第一个找到的可以处理该异常的 catch。因此，在 catch 子句列表中，最特殊的 catch 必须最先出现。
异常与 catch 异常说明符匹配的规则比匹配实参和形参类型的规则更严格，大多数转换都不允许——除下面几种可能的区别之外，异常的类型与 catch 说明符的类型必须完全匹配：
	1）允许从非 const 到 const 的转换。也就是说，非 const 对象的 throw 可以与指定接受 const 引用的 catch 匹配。
	2）允许从派生类型型到基类类型的转换。
	3）将数组转换为指向数组类型的指针，将函数转换为指向函数类型的适当指针。
在查找匹配 catch 的时候，不允许其他转换。具体而言，既不允许标准算术转换，也不允许为类类型定义的转换。
进入 catch 的时候，用异常对象初始化 catch 的形参。像函数形参一样，异常说明符类型可以是引用。异常对象本身是被抛出对象的副本。是否再次将异常对象复制到 catch 位置取决于异常说明符类型。如果说明符不是引用，就将异常对象复制到 catch 形参中，如果说明符是引用，则像引用形参一样，不存在单独的 catch 对象，catch 形参只是异常对象的另一名字。对 catch 形参所做的改变作用于异常对象。
异常说明符的静态类型决定 catch 子句可以执行的动作。如果被抛出的异常对象是派生类类型的，但由接受基类类型的 catch 处理，那么，catch 不能使用派生类特有的任何成员。
如果 catch 形参是引用类型，catch 对象就直接访问异常对象，catch 对象的静态类型可以与 catch 对象所引用的异常对象的动态类型不同。如果异常说明符不是引用，则 catch 对象是异常对象的副本，如果 catch 对象是基类类型对象而异常对象是派生类型的，就将异常对象分割为它的基类子对象。对象（相对于引用）不是多态的。
catch 子句的次序必须反映类型层次：因为 catch 子句按出现次序匹配，所以使用来自继承层次的异常的程序必须将它们的 catch 子句排序，以便 派生类型的处理代码出现在其基类类型的 catch 之前。
重新抛出：有可能单个 catch 不能完全处理一个异常。在进行了一些校正行动之后，catch 可能确定该异常必须由函数调用链中更上层的函数来处理，catch 可以通过重新抛出将异常传递函数调用链中更上层的函数。重新抛出是后面不跟类型或表达式的一个 throw：
    throw;
空 throw 语句将重新抛出异常对象，它只能出现在 catch 或者从 catch 调用的函数中。如果在处理代码不活动时碰到空 throw，就调用 terminate 函数。
虽然重新抛出不指定自己的异常，但仍然将一个异常对象沿链向上传递，被抛出的异常是原来的异常对象，而不是 catch 形参。当 catch 形参是基类类型的时候，我们不知道由重新抛出表达式抛出的实际类型，该类型取决于异常对象的动态类型，而不是 catch 形参的静态类型。catch 可以改变它的形参。在改变它的形参之后，如果 catch 重新抛出异常，那么，只有当异常说明符是引用的时候，才会传播那些改变。
捕获所有异常的处理代码：捕获所有异常的 catch 子句形式为 (...)。例如：
     // matches any exception that might be thrown
     catch (...) {
         // place our code here
     }
如果 catch(...) 与其他 catch 子句结合使用，它必须是最后一个，否则，任何跟在它后面的 catch 子句都将不能被匹配。
函数测试块与构造函数：异常可能发生在构造函数中，或者发生在处理构造函数初始化式的时候。在进入构造函数函数体之前处理构造函数初始化式，构造函数函数体内部的 catch 子句不能处理在处理构造函数初始化时可能发生的异常。为了处理来自构造函数初始化式的异常，必须将构造函数编写为函数 try 块。可以使用函数测试块将一组 catch 子句与函数联成一个整体。作为例子，可以将第十六章的 Handle 构造函数包装在一个用来检测 new 中失败的测试块当中：
    template <class T> Handle<T>::Handle(T *p)
    try : ptr(p), use(new size_t(1))
    {
         // empty function body
    }  catch(const std::bad_alloc &e)
           { handle_out_of_memory(e); }
关键字 try 出现在成员初始化列表之前，并且测试块的复合语句包围了构造函数的函数体。catch 子句既可以处理从成员初始化列表中抛出的异常，也可以处理从构造函数函数体中抛出的异常。
异常类层次：exception 类型所定义的唯一操作是一个名为 what 的虚成员，该函数返回 const char* 对象，它一般返回用来在抛出位置构造异常对象的信息。
用类管理资源分配：可能存在异常的程序以及分配资源的程序应该使用类来管理那些资源。异常安全意味着，即使发生异常，程序也能正确操作。在这种情况下，“安全”来自于保证“如果发生异常，被分配的任何资源都适当地释放“通过定义一个类来封闭资源的分配和释放，可以保证正确释放资源。这一技术常称为“资源分配即初始化”，简称 RAII。
    class Resource {
    public:
        Resource(parms p): r(allocate(p)) { }
        ~Resource() { release(r); }
        // also need to define copy and assignment
    private:
        resource_type *r;           // resource managed by this type
        resource_type *allocate(parms p);     // allocate this resource
        void release(resource_type*);         // free this resource
    };
Resource 类是分配资源和回收资源的类型，它保存表示该资源的数据成员。Resource 的构造函数分配资源，而析构函数释放它。当使用这个类的时候
    void fcn()
    {
       Resource res(args);   // allocates resource_type
       // code that might throw an exception
       // if exception occurs, destructor for res is run automatically
       // ...
    }  // res goes out of scope and is destroyed automatically
自动释放资源。如果函数正常终止，就在 Resource 对象超出作用域时释放资源；如果函数因异常而提早退出，编译器就运行 Resource 的析构函数作为异常处理过程的一部分。
auto_ptr 类：标准库的 auto_ptr 类是异常安全的“资源分配即初始化”技术的例子。auto_ptr 类是接受一个类型形参的模板，它为动态分配的对象提供异常安全。auto_ptr 类在头文件 memory 中定义。auto_ptr 只能用于管理从 new 返回的一个对象，它不能管理动态分配的数组。
当 auto_ptr 被复制或赋值的时候，有不寻常的行为，因此，不能将 auto_ptrs 存储在标准库容器类型中。auto_ptr 对象只能保存一个指向对象的指针，并且不能用于指向动态分配的数组，使用 auto_ptr 对象指向动态分配的数组会导致未定义的运行时行为。每个 auto_ptr 对象绑定到一个对象或者指向一个对象。当 auto_ptr 对象指向一个对象的时候，可以说它“拥有”该对象。当 auto_ptr 对象超出作用域或者另外撤销的时候，就自动回收 auto_ptr 所指向的动态分配对象。
如果通过常规指针分配内存，而且在执行 delete 之前发生异常，就不会自动释放该内存：
     void f()
     {
        int *ip = new int(42);     // dynamically allocate a new object
        // code that throws an exception that is not caught inside f
        delete ip;                 // return the memory before exiting
     }
如果在 new 和 delete 之间发生异常，并且该异常不被局部捕获，就不会执行 delete，则永不回收该内存。
如果使用一个 auto_ptr 对象来代替，将会自动释放内存，即使提早退出这个块也是这样：
     void f()
     {
        auto_ptr<int> ap(new int(42)); // allocate a new object
        // code that throws an exception that is not caught inside f
     }
      // auto_ptr freed automatically when function ends
auto_ptr 是可以保存任何类型指针的模板。在最常见的情况下，将 auto_ptr 对象初始化为由 new 表达式返回的对象的地址：
    auto_ptr<int> pi(new int(1024));
接受指针的构造函数为 explicit构造函数，所以必须使用初始化的直接形式来创建 auto_ptr 对象：
    // error: constructor that takes a pointer is explicit and can't be used implicitly
    auto_ptr<int> pi = new int(1024);
    auto_ptr<int> pi(new int(1024)); // ok: uses direct initialization
pi 所指的由 new 表达式创建的对象在超出作用域时自动删除。如果 pi 是局部对象，pi　所指对象在定义 pi 的块的末尾删除；如果发生异常，则 pi 也超出作用域，析构函数将自动运行 pi 的析构函数作为异常处理的一部分；如果 pi 是全局对象，就在程序末尾删除 pi 引用的对象。
auto_ptr 的主要目的，在保证自动删除 auto_ptr 对象引用的对象的同时，支持普通指针式行为。
auto_ptr 对象的复制和赋值是破坏性操作：auto_ptr 和内置指针对待复制和赋值有非常关键的重要区别。当复制 auto_ptr 对象或者将它的值赋给其他 auto_ptr 对象的时候，将基础对象的所有权从原来的 auto_ptr 对象转给副本，原来的 auto_ptr 对象重置为未绑定状态。
	auto_ptr<string> ap1(new string("Stegosaurus"));
     // after the copy ap1 is unbound
     auto_ptr<string> ap2(ap1);  // ownership transferred from ap1 to ap2
当复制 auto_ptr 对象或者对 auto_ptr 对象赋值的时候，右边的 auto_ptr 对象让出对基础对象的所有职责并重置为未绑定的 auto_ptr 对象之后，在上例中，删除 string 对象的是 ap2 而不是 ap1，在复制之后，ap1 不再指向任何对象。与其他复制或赋值操作不同，auto_ptr 的复制和赋值改变右操作数，因此，赋值的左右操作数必须都是可修改的左值。
除了将所有权从右操作数转给左操作数之外，赋值还删除左操作数原来指向的对象——假如两个对象不同。通常自身赋值没有效果。
     auto_ptr<string> ap3(new string("Pterodactyl"));
     // object pointed to by ap3 is deleted and ownership transferred from ap2 to ap3;
     ap3 = ap2;  // after the assignment, ap2 is unbound
将 ap2 赋给 ap3 之后：
	1）删除了 ap3 指向的对象。
	2）将 ap3 置为指向 ap2 所指的对象。
	3）ap2 是未绑定的 auto_ptr 对象。
auto_ptr 类型没有定义到可用作条件的类型的转换，相反，要测试 auto_ptr 对象，必须使用它的 get 成员，该成员返回包含在 auto_ptr 对象中的基础指针：
    // revised test to guarantee p_auto refers to an object
    if (p_auto.get())
        *p_auto = 1024;
应该只用 get 询问 auto_ptr 对象或者使用返回的指针值，不能用 get 作为创建其他 auto_ptr 对象的实参：在任意时刻只有一个 auto_ptrs 对象保存给定指针，如果两个 auto_ptrs 对象保存相同的指针，该指针就会被 delete 两次。
auto_ptr 对象与内置指针的另一个区别是，不能直接将一个地址（或者其他指针）赋给 auto_ptr 对象：
     p_auto = new int(1024); // error: cannot assign a pointer to an auto_ptr
相反，必须调用 reset 函数来改变指针：
     // revised test to guarantee p_auto refers to an object
     if (p_auto.get())
         *p_auto = 1024;
     else
         // reset p_auto to a new object
         p_auto.reset(new int(1024));
调用 auto_ptr 对象的 reset 函数时，在将 auto_ptr 对象绑定到其他对象之前，会删除 auto_ptr 对象所指向的对象（如果存在）。但是，正如自身赋值是没有效果的一样，如果调用该 auto_ptr 对象已经保存的同一指针的 reset 函数，也没有效果，不会删除对象。
auto_ptr 缺陷：要正确地使用 auto_ptr 类，必须坚持该类强加的下列限制：
	1）不要使用 auto_ptr 对象保存指向静态分配对象的指针，否则，当 auto_ptr 对象本身被撤销的时候，它将试图删除指向非动态分配对象的指针，导致未定义的行为。
	2）永远不要使用两个 auto_ptrs 对象指向同一对象，导致这个错误的一种明显方式是，使用同一指针来初始化或者 reset 两个不同的 auto_ptr 对象。另一种导致这个错误的微妙方式可能是，使用一个 auto_ptr 对象的 get 函数的结果来初始化或者 reset 另一个 auto_ptr 对象。
	3）不要使用 auto_ptr 对象保存指向动态分配数组的指针。当 auto_ptr 对象被删除的时候，它只释放一个对象——它使用普通 delete 操作符，而不用数组的 delete [] 操作符。
	4）不要将 auto_ptr 对象存储在容器中。容器要求所保存的类型定义复制和赋值操作符，使它们表现得类似于内置类型的操作符：在复制（或者赋值）之后，两个对象必须具有相同值，auto_ptr 类不满足这个要求。
异常说明：异常说明指定，如果函数抛出异常，被抛出的异常将是包含在该说明中的一种，或者是从列出的异常中派生的类型。异常说明跟在函数形参表之后。一个异常说明在关键字 throw 之后跟着一个（可能为空的）由圆括号括住的异常类型列表：
     void recoup(int) throw(runtime_error);
空说明列表指出函数不抛出任何异常：
     void no_problem() throw();
异常说明是函数接口的一部分，函数定义以及该函数的任意声明必须具有相同的异常说明。如果一个函数声明没有指定异常说明，则该函数可以抛出任意类型的异常。
违反异常说明：如果函数抛出了没有在其异常说明中列出的异常，就调用标准库函数 unexpected。默认情况下，unexpected 函数调用 terminate 函数，terminate 函数一般会终止程序。
因为不能在编译时检查异常说明，异常说明的应用通常是有限的。
在 const 成员函数声明中，异常说明跟在 const 限定符之后：
	virtual const char* what() const throw();
异常说明与析构函数：
	class isbn_mismatch: public std::logic_error {
     public:
         virtual ~isbn_mismatch() throw() { }
     };
isbn_mismatch 类从 logic_error 类继承而来，logic_error 是一个标准异常类，该标准异常类的析构函数包含空 throw() 说明符，它们承诺不抛出任何异常。当继承这两个类中的一个时，我们的析构函数也必须承诺不抛出任何异常。isbn_mismatch 类有两个 string 类成员，这意味着 isbn_mismatch 的合成析构函数调用 string 析构函数。C++ 标准保证，string 析构函数像任意其他标准库类析构函数一样，不抛出异常。但是，标准库的析构函数没有定义异常说明，在这种情况下，我们知道，但编译器不知道，string 析构函数将不抛出异常。我们必须定义自己的析构函数来恢复析构函数不抛出异常的承诺。
异常说明与虚函数：基类中虚函数的异常说明，可以与派生类中对应虚函数的异常说明不同。但是，派生类虚函数的异常说明必须与对应基类虚函数的异常说明同样严格，或者比后者更受限。这个限制保证，当使用指向基类类型的指针调用派生类虚函数的时候，派生类的异常说明不会增加新的可抛出异常。
	class Base {
     public:
         virtual double f1(double) throw ();
         virtual int f2(int) throw (std::logic_error);
         virtual std::string f3() throw
               (std::logic_error, std::runtime_error);
     };
     class Derived : public Base {
     public:
         // error: exception specification is less restrictive than Base::f1's
         double f1(double) throw (std::underflow_error);

         // ok: same exception specification as Base::f2
         int f2(int) throw (std::logic_error);
         // ok: Derived f3 is more restrictive
         std::string f3() throw ();
     };
函数指针的异常说明：异常说明是函数类型的一部分。这样，也可以在函数指针的定义中提供异常说明：
     void (*pf)(int) throw(runtime_error);
该函数只能抛出 runtime_error 类型的异常。如果不提供异常说明，该指针就可以指向能够抛出任意类型异常的具有匹配类型的函数。
在用另一指针初始化带异常说明的函数的指针，或者将后者赋值给函数地址的时候，两个指针的异常说明不必相同，但是，源指针的异常说明必须至少与目标指针的一样严格。
     void recoup(int) throw(runtime_error);
     // ok: recoup is as restrictive as pf1
     void (*pf1)(int) throw(runtime_error) = recoup;
     // ok: recoup is more restrictive than pf2
     void (*pf2)(int) throw(runtime_error, logic_error) = recoup;
     // error: recoup is less restrictive than pf3
     void (*pf3)(int) throw() = recoup;
     // ok: recoup is more restrictive than pf4
     void (*pf4)(int) = recoup;

### 2. 命名空间
命名空间的定义: 命名空间名字后面接着由花括号括住的一块声明和定义，可以在命名空间中放入可以出现在全局作用域的任意声明：类、变量（以及它们的初始化）、函数（以及它们的定义）、模板以及其他命名空间. 命名空间作用域不能以分号结束. 命名空间的名字在定义该命名空间的作用域中必须是唯一的。命名空间可以在全局作用域或其他作用域内部定义，但不能在函数或类内部定义.
命名空间定义以关键字 namespace 开始，后接命名空间的名字。
     namespace cplusplus_primer {
         class Sales_item { /* ... */};
         Sales_item operator+(const Sales_item&,const Sales_item&);
         class Query {
        	 public:
             	Query(const std::string&);
             	std::ostream &display(std::ostream&) const;
             	// ...
         };
     }
在命名空间中定义的名字可以被命名空间中的其他成员直接访问，命名空间外部的代码必须指出名字定义在哪个命名空间中.
总是使用限定名引用命名空间成员可能非常麻烦。可以编写 using 声明来获得对我们知道将经常使用的名字的直接访问：
     using cplusplus_primer::Query;
命名空间可以是不连续的：命名空间可以在几个部分中定义。命名空间由它的分离定义部分的总和构成，命名空间是累积的。一个命名空间的分离部分可以分散在多个文件中，在不同文本文件中的命名空间定义也是累积的。如果命名空间的一个部分需要定义在另一文件中的名字，仍然必须声明该名字。
	编写命名空间定义：
     namespace namespace_name {
     // declarations
     }
如果名字 namespace_name 不是引用前面定义的命名空间，则用该名字创建新的命名空间，否则，这个定义打开一个已存在的命名空间，并将这些新声明加到那个命名空间。
接口和实现的分离：
	// ---- Sales_item.h ----
     namespace cplusplus_primer {
         class Sales_item { /* ... */};
         Sales_item operator+(const Sales_item&,
                              const Sales_item&);
         // declarations for remaining functions in the Sales_item interface
     }
     // ---- Query.h ----
     namespace cplusplus_primer {
         class Query {
         public:
             Query(const std::string&);
             std::ostream &display(std::ostream&) const;
             // ...
         };
         class Query_base { /* ... */};
     }
     // ---- Sales_item.cc ----
     #include "Sales_item.h"
     namespace cplusplus_primer {
     // definitions for Sales_item members and overloaded operators
     }
     // ---- Query.cc ----
     #include "Query.h"
     namespace cplusplus_primer {
         // definitions for Query members and related functions
     }
	// ---- user.cc ----
     // defines the cplusplus_primer::Sales_item class
     #include "Sales_item.h"
     int main()
     {
         // ...
         cplusplus_primer::Sales_item trans1, trans2;
         // ...
         return 0;
     }
定义命名空间成员：在命名空间内部定义的函数可以使用同一命名空间中定义的名字的简写形式（无需命名空间前缀）。
也可以在命名空间定义的外部定义命名空间成员：
		cplusplus_primer::Sales_item 
		cplusplus_primer::operator+(const Sales_item& lhs, const Sales_item& rhs)
     	{
         	Sales_item ret(lhs);
         	// ...
     	}
一旦看到完全限定的函数名，就处于命名空间的作用域中。因此，形参表和函数体中的命名空间成员引用可以使用非限定名引用 Sales_item。
不能在不相关的命名空间中定义成员：虽然可以在命名空间定义的外部定义命名空间成员，对这个定义可以出现的地方仍有些限制，只有包围成员声明的命名空间可以包含成员的定义。例如，operator+ 既可以定义在命名空间 cplusplus_primer 中，也可以定义在全局作用域中，但它不能定义在不相关的命名空间中。
全局命名空间：定义在全局作用域的名字（在任意类、函数或命名空间外部声明的名字）是定义在全局命名空间中的。全局命名空间是隐式声明的，存在于每个程序中。在全局作用域定义实体的每个文件将那些名字加到全局命名空间。可以用作用域操作符引用全局命名空间的成员。因为全局命名空间是隐含的，它没有名字，所以记号
     ::member_name
	引用全局命名空间的成员。
嵌套命名空间：一个嵌套命名空间即是一个嵌套作用域——其作用域嵌套在包含它的命名空间内部。嵌套命名空间中的名字遵循常规规则：外围命名空间中声明的名字被嵌套命名空间中同一名字的声明所屏蔽。嵌套命名空间内部定义的名字局部于该命名空间。外围命名空间之外的代码只能通过限定名引用嵌套命名空间中的名字：cplusplus_primer::QueryLib::Query。
未命名的命名空间：未命名的命名空间在定义时没有给定名字。未命名的命名空间以关键字 namespace 开头，接在关键字 namespace 后面的是由花括号定界的声明块。未命名的命名空间的定义局部于特定文件，从不跨越多个文本文件。每个文件有自己的未命名的命名空间。在未命名的命名空间中定义的变量在程序开始时创建，在程序结束之前一直存在。未命名的命名空间中定义的名字可直接使用，毕竟，没有命名空间名字来限定它们。不能使用作用域操作符来引用未命名的命名空间的成员。
未命名空间中定义的名字可以在定义该命名空间所在的作用域中找到。如果在文件的最外层作用域中定义未命名的命名空间，那么，未命名的空间中的名字必须与全局作用域中定义的名字不同：
     int i;   // global declaration for i
     namespace {
         int i;
     }
     // error: ambiguous defined globally and in an unnested, unnamed namespace
     i = 10;
在 C 语言中，声明为 static 的局部实体在声明它的文件之外不可见。在C++中应该避免文件静态而使用未命名空间代替。
using 声明：一个 using 声明一次只引入一个命名空间成员，它使得无论程序中使用哪些名字，都能够非常明确：
		using std::map;
     	using std::pair;
     	using std::size_t;
using 声明中引入的名字遵循常规作用域规则。从 using 声明点开始，直到包含 using 声明的作用域的末尾，名字都是可见的。外部作用域中定义的同名实体被屏蔽。一旦该作用域结束了，就必须使用完全限定名。using 声明可以出现在全局作用域、局部作用域或者命名空间作用域中。类作用域中的 using 声明局限于被定义类的基类中定义的名字。
命名空间别名：namespace primer = cplusplus_primer;
一个命名空间可以有许多别名，所有别名以及原来的命名空间名字都可以互换使用。
using 指示：像 using 声明一样，using 指示使我们能够使用命名空间名字的简写形式。与 using 声明不同，using 指示无法控制使得哪些名字可见——它们都是可见的。
using 指示以关键字 using 开头，后接关键字 namespace，再接命名空间名字。如果该名字不是已经定义的命名空间名字，就会出错。
using 指示具有将命名空间成员提升到包含命名空间本身和 using 指示的最近作用域的效果。
	namespace blip {
        int bi = 16, bj = 15, bk = 23;
        // other declarations
    }
    int bj = 0; // ok: bj inside blip is hidden inside a namespace
    void manip()
    {
         // using directive - names in blip "added" to global scope
         using namespace blip;
                         // clash between ::bj and blip::bj
                         // detected only if bj is used
         ++bi;           // sets blip::bi to 17
         ++bj;           // error: ambiguous
                         // global bj or blip::bj?
         ++::bj;         // ok: sets global bj to 1
         ++blip::bj;     // ok: sets blip::bj to 16
         int bk = 97;    // local bk hides blip::bk
         ++bk;           // sets local bk to 98
    }
blip 的成员看来好像是在定义 blip 和 manip 的作用域中定义的一样。如果在全局作用域中定义 blip，则 blip 的成员看来好像是声明在全局作用域的一样。因为名字在不同的作用域中，manip 内部的局部声明可以屏蔽命名空间的某些成员名字。对 manip 而言，blip 成员 bj 看来好像声明在全局作用域中，但是，全局作用域存在另一名为 bj 的对象。这种冲突是允许的，但为了使用该名字，必须显式指出想要的是哪个版本。
类内部所定义的成员可以使用出现在定义文本之后的名字。当类包在命名空间中的时候，确定类成员定义中使用的名字：首先在成员中找，然后在类（包括基类）中找，再在外围作用域中找，外围作用域中的一个或多个可以是命名空间：
	namespace A {
        int i;
        int k;
        class C1 {
        public:
            C1(): i(0), j(0) { }   // ok: initializes C1::i and C1::j
            int f1()
            {
                 return k;        // returns A::k
            }
            int f2()
            {
                return h;        // error: h is not defined
            }
            int f3();
        private:
           int i;                // hides A::i within C1
           int j;
        };
        int h = i;               // initialized from A::i
     }
     // member f3 is defined outside class C1 and outside namespace A
     int A::C1::f3()
     {
         return h;               // ok: returns A::h
     }
除了定义的成员例外，总是向上查找作用域：名字在使用之前必须声明。如果使 A 中的名字在 C1 的定义之前定义，h 的使用就是合法的。类似地，f3 内部对 h 的使用是正确的，因为 f3 定义在已经定义了 A::h 之后。
可以从函数的限定名推断出查找名字时所检查作用域的次序，限定名以相反次序指出被查找的作用域。限定符 A::C1::f3 指出了查找类作用域和命名空间作用域的相反次序，首先查找函数 f3 的作用域，然后查找外围类 C1 的作用域。在查找包含 f3 定义的作用之前，最后查找命名空间 A 的作用域。
屏蔽命名空间名字规则的一个重要例外：接受类类型形参（或类类型指针及引用形参）的函数（包括重载操作符），以及与类本身定义在同一命名空间中的函数（包括重载操作符），在用类类型对象（或类类型的引用及指针）作为实参的时候是可见的。其原因在于，允许无须单独的 using 声明就可以使用概念上作为类接口组成部分的非成员函数。能够使用非成员操作对操作符函数特别有用：
	std::string s;
    // ok: calls std::getline(std::istream&, const std::string&)
    getline(std::cin, s);
当编译器看到 getline 函数的使用getline(std::cin, s);的时候，它在当前作用域，包含调用的作用域以及定义 cin 的类型和 string 类型的命名空间中查找匹配的函数。因此，它在命名空间 std 中查找并找到由 string 类型定义的 getline 函数。
	std::string s;
    cin >> s;
如果没有查找规则的这个例外，我们将必须编写下面二者之一：
    using std::operator>>;        // need to allow cin >> s
    std::operator>>(std::cin, s); // ok: explicitly use std::>>
隐式友元声明与命名空间：当一个类声明友元函数的时候，函数的声明不必是可见的。如果不存在可见的声明，那么，友元声明具有将该函数或类的声明放入外围作用域的效果。如果类在命名空间内部定义，则没有另外声明的友元函数在同一命名空间中声明。
    namespace A {
        class C {
            friend void f(const C&); // makes f a member of namespace A
        };
    }
因为该友元接受类类型实参并与类隐式声明在同一命名空间中，所以使用它时可以无须使用显式命名空间限定符：
    // f2 defined at global scope
    void f2()
    {
         A::C cobj;
         f(cobj); // calls A::f
    }
有一个或多个类类型形参的函数的名字查找包括定义每个形参类型的命名空间。这个规则还影响怎样确定候选集合，为找候选函数而查找定义形参类（以及定义其基类）的每个命名空间，将那些命名空间中任意与被调用函数名字相同的函数加入候选集合。即使这些函数在调用点不可见，也将之加入候选集合。将那些命名空间中带有匹配名字的函数加入候选集合：
    namespace NS {
        class Item_base { /* ... */ };
        void display(const Item_base&) { }
    }
    // Bulk_item's base class is declared in namespace NS
    class Bulk_item : public NS::Item_base { };
    int main() {
        Bulk_item book1;
        display(book1);
        return 0;
    }
display 函数的实参 book1 具有类类型 Bulk_item。display 调用的候选函数不仅是在调用 display 函数的地方其声明可见的函数，还包括声明 Bulk_item 类及其基类 Item_base 的命名空间中的函数。命名空间 NS 中声明的函数 display(const Item_base&) 被加到候选函数集合中。
重载与 using 声明：using 声明声明一个名字。如果命名空间内部的函数是重载的，那么，该函数名字的 using 声明声明了所有具有该名字的函数。由 using 声明引入的函数，重载出现 using 声明的作用域中的任意其他同名函数的声明。如果 using 声明在已经有同名且带相同形参表的函数的作用域中引入函数，则 using 声明出错，否则，using 定义给定名字的另一重载实例，效果是增大候选函数集合。
重载与 using 指示：using 指示将命名空间成员提升到外围作用域。如果命名空间函数与命名空间所在的作用域中声明的函数同名，就将命名空间成员加到重载集合中：
	namespace libs_R_us {
        extern void print(int);
        extern void print(double);
    }
    void print(const std::string &);
    // using directive:
    using namespace libs_R_us;
    // using directive added names to the candidate set for calls to print:
    // print(int) from libs_R_us
    // print(double) from libs_R_us
    // print(const std::string &) declared explicitly
    void fooBar(int ival)
    {
         print("Value: "); // calls global print(const string &)
         print(ival);      // calls libs_R_us::print(int)
    }
命名空间与模板：在命名空间内部声明模板影响着怎样声明模板特化：模板的显式特化必须在定义通用模板的命名空间中声明，否则，该特化将与它所特化的模板不同名。有两种定义特化的方式：一种是重新打开命名空间并加入特化的定义，可以这样做是因为命名空间定义是不连续的；或者，可以用与在命名空间定义外部定义命名空间成员相同的方式来定义特化：使用由命名空间名字限定的模板名定义特化。为了提供命名空间中所定义模板的自己的特化，必须保证在包含原始模板定义的命名空间中定义特化。

### 3. 多重继承与虚继承
	class Bear : public ZooAnimal {
    };
	class Panda : public Bear, public Endangered {
    };
派生类的构造函数可以在构造函数初始化式中给零个或多个基类传递值：
    // explicitly initialize both base classes
    Panda::Panda(std::string name, bool onExhibit)
          : Bear(name, onExhibit, "Panda"),
            Endangered(Endangered::critical) { }
构造函数初始化式只能控制用于初始化基类的值，不能控制基类的构造次序。基类构造函数按照基类构造函数在类派生列表中的出现次序调用。总是按构造函数运行的逆序调用析构函数。
派生类的指针或引用可以转换为其任意其类的指针或引用。
编译器不会试图根据派生类转换来区别基类间的转换，转换到每个基类都一样好。
	void print(const Bear&);
    void print(const Endangered&);
	Panda ying_yang("ying_yang");
    print(ying_yang);              // error: ambiguous
基于指针类型或引用类型的查找：当一个类继承于多个基类的时候，那些基类之间没有隐含的关系，不允许使用一个基类的指针访问其他基类的成员。
假定所有根基类都将它们的析构函数适当定义为虚函数，那么，无论通过哪种指针类型删除对象，虚析构函数的处理都是一致的：
    // each pointer points to a Panda
    delete pz; // pz is a ZooAnimal*
    delete pb; // pb is a Bear*
    delete pp; // pp is a Panda*
    delete pe; // pe is a Endangered*
析构函数调用的次序是构造函数次序的逆序：通过虚机制调用 Panda 析构函数。随着 Panda 析构函数的执行，依次调用 Endangered、Bear 和 ZooAnimal 的析构函数。
如果具有多个基类的类定义了自己的析构函数，该析构函数只负责清除派生类。如果派生类定义了自己的复制构造函数或赋值操作符，则类负责复制（赋值）所有的基类子部分。只有派生类使用复制构造函数或赋值操作符的合成版本，才自动复制或赋值基类部分。
假定 Panda 类使用默认复制控制成员。ling_ling 的初始化
    Panda ying_yang("ying_yang");  // create a Panda object
    Panda ling_ling = ying_yang;   // uses copy constructor
使用默认复制构造函数调用 Bear 复制构造函数，Bear 复制构造函数依次在执行 Bear 复制构造函数之前运行 ZooAnimal 复制构造函数。一旦构造了 ling_ling 的 Bear 部分，就运行 Endangered 复制构造函数来创建对象的那个部分。最后，运行 Panda 复制构造函数。
在多重继承下，成员函数中使用的名字和查找首先在函数本身进行，如果不能在本地找到名字，就继续在成员的类中查找，然后依次查找每个基类。在多重继承下，查找同时检察所有的基类继承子树。如果在多个子树中找到该名字，则那个名字的使用必须显式指定使用哪个基类；否则，该名字的使用是二义性的
首先发生名字查找：即使两个继承的函数有不同的形参表，也会产生错误。类似地，即使函数在一个类中是私有的而在另一个类中是公用或受保护的，也是错误的。最后，如果在 ZooAnimal 类中定义了 print 而Bear 类中没有定义，调用仍是错误的。名字查找总是以两个步骤发生：首先编译器找到一个匹配的声明（或者找到两个匹配的声明，这导致二义性），然后，编译器才确定所找到的声明是否合法
可以通过指定使用哪个类解决二义性：
    ying_yang.Endangered::print(cout);
避免潜在二义性最好的方法是，在解决二义性的派生类中定义函数的一个版本。例如，应该给选择使用哪个 print 版本的 Panda 类一个 print 函数：
    std::ostream& Panda::print(std::ostream &os) const
    {
        Bear::print(os);        // print the Bear part
        Endangered::print(os);  // print the Endangered part
        return os;
    }
虚继承：一个类继承多个直接基类的时候，那些类有可能本身还共享另一个基类。在这种情况下，中间类可以选择使用虚继承，声明愿意与层次中虚继承同一基类的其他类共享虚基类。用这种方法，后代派生类中将只有一个共享虚基类的副本。
istream 和 ostream 类对它们的基类进行虚继承。通过使基类成为虚基类，istream 和 ostream 指定，如果其他类（如 iostream 同时继承它们两个，则派生类中只出现它们的公共基类的一个副本。通过在派生列表中包含关键字 virtual 设置虚基类：
    class istream : public virtual ios { ... };
    class ostream : virtual public ios { ... };
    // iostream inherits only one copy of its ios base class
    class iostream: public istream, public ostream { ... };
即使基类是虚基类，也照常可以通过基类类型的指针或引用操纵派生类的对象。
虚基类成员的可见性：假定通过多个派生路径继承名为 X 的成员，有下面三种可能性：
	1）如果在每个路径中 X 表示同一虚基类成员，则没有二义性，因为共享该成员的单个实例。
	2）如果在某个路径中 X 是虚基类的成员，而在另一路径中 X 是后代派生类的成员，也没有二义性——特定派生类实例的优先级高于共享虚基类实例。
	3）如果沿每个继承路径 X 表示后代派生类的不同成员，则该成员的直接访问是二义性的。
像非虚多重继承层次一样，这种二义性最好用在派生类中提供覆盖实例的类来解决。
特殊的初始化语义：通常，每个类只初始化自己的直接基类。如果使用常规规则，就可能会多次初始化虚基类。类将沿着包含该虚基类的每个继承路径初始化。为了解决这个重复初始化问题，从具有虚基类的类继承的类对初始化进行特殊处理。在虚派生中，由最低层派生类的构造函数初始化虚基类。虽然由最低层派生类初始化虚基类，但是任何直接或间接继承虚基类的类一般也必须为该基类提供自己的初始化式。只要可以创建虚基类派生类类型的独立对象，该类就必须初始化自己的虚基类，这些初始化式只有创建中间类型的对象时使用。
如果 Panda 构造函数不显式初始化 ZooAnimal 基类，就使用 ZooAnimal 默认构造函数；如果 ZooAnimal 没有默认构造函数，则代码出错。
无论虚基类出现在继承层次中任何地方，总是在构造非虚基类之前构造虚基类：
	class Character { /* ... */ };
   class BookCharacter : public Character { /* ... */ };
   class ToyAnimal { /* ... */ };
   class TeddyBear : public BookCharacter,
                     public Bear, public virtual ToyAnimal
                     { /* ... */ };
TeddyBear 的虚基类的构造次序是先 ZooAnimal 再 ToyAnimal。一旦构造了虚基类，就按声明次序调用非虚基类的构造函数：首先是 BookCharacter，它导致调用 Character 构造函数，然后是 Bear。
在这里，由最低层派生类 TeddyBear 指定用于 ZooAnimal 和 ToyAnimal 的初始化式。
在合成复制构造函数中使用同样的构造次序，在合成赋值操作符中也是按这个次序给基类赋值。保证调用基类析构函数的次序与构造函数的调用次序相反。

## 第十八章 特殊工具与技术
### 1. 优化内存分配
C++ 提供下面两种方法分配和释放未构造的原始内存：
	1）allocator 类，它提供可感知类型的内存分配。这个类支持一个抽象接口，以分配内存并随后使用该内存保存对象。
	2）标准库中的operator new 和 operator delete，它们分配和释放需要大小的原始的、未类型化的内存。
C++ 还提供不同的方法在原始内存中构造和撤销对象：
	1）allocator 类定义了名为 construct 和 destroy 的成员，其操作正如它们的名字所指出的那样：construct 成员在未构造内存中初始化对象，destroy 成员在对象上运行适当的析构函数。
	2）定位 new 表达式接受指向未构造内存的指针，并在该空间中初始化一个对象或一个数组。
	3）可以直接调用对象的析构函数来撤销对象。运行析构函数并不释放对象所在的内存。
	4）定制算法 uninitialized_fill 和 uninitialized_copy 像 fill 和 copy 算法一样执行，除了它们的目的地构造对象而不是给对象赋值之外。
现代 C++ 程序一般应该使用 allocator 类来分配内存，它更安全更灵活。但是，在构造对象的时候，用 new 表达式比 allocator::construct 成员更灵活。有几种情况下必须使用 new。
allocator 类：allocator 类是一个模板，它提供类型化的内存分配以及对象构造与撤销。
allocator 类将内存分配和对象构造分开。当 allocator 对象分配内存的时候，它分配适当大小并排列成保存给定类型对象的空间。但是，它分配的内存是未构造的，allocator 的用户必须分别 construct 和 destroy 放置在该内存中的对象。
	allocator<T> a; //定义名为 a 的 allocator 对象，可以分配内存或构造 T 类型的对象
	a.allocate(n) //分配原始的未构造内存以保存 T 类型的 n 个对象
	a.construct(p, t) //在 T* 指针 p 所指内存中构造一个新元素。运行 T 类型的复制构造函数用 t 初始化该对象
	a.destroy(p) //运行 T* 指针 p 所指对象的析构函数
	a.deallocate(p, n) //释放内存，在名为 p 的 T* 指针中包含的地址处保存 T 类型的 n 个对象。运行调用 deallocate 之前在该内存中构造的任意对象的 destroy 是用户的责任
使用 allocator 管理类成员数据：vector 所用存储开始是未构造内存，它还没有保存任何对象。将元素复制或增加到这个预分配空间的时候，必须使用 allocator 类的 construct 成员构造元素。
实现 vector 的一小部分：
	template <class T> class Vector {
     public:
         Vector(): elements(0), first_free(0), end(0) { }
         void push_back(const T&);
          // ...
     private:
         static std::allocator<T> alloc; // object to get raw memory
         void reallocate(); // get more space and copy existing elements
         T* elements;       // pointer to first element in the array
         T* first_free;     // pointer to first free element in the array
         T* end;            // pointer to one past the end of the array
         // ...
     };
	
	template <class T>
     void Vector<T>::push_back(const T& t)
     {
         // are we out of space?
         if (first_free == end)
           reallocate(); // reallocate 分配新空间并复制现存元素，将指针重置为指向新分配的空间。
         alloc.construct(first_free, t);
         ++first_free;
     }
	template <class T> 
	void Vector<T>::reallocate()
     {
         // compute size of current array and allocate space for twice as many elements
         std::ptrdiff_t size = first_free - elements;
         std::ptrdiff_t newcapacity = 2 * max(size, 1);
         // allocate space to hold newcapacity number of elements of type T
         T* newelements = alloc.allocate(newcapacity);

         // construct copies of the existing elements in the new space
         uninitialized_copy(elements, first_free, newelements);
         // destroy the old elements in reverse order
         for (T *p = first_free; p != elements; /* empty */ )
            alloc.destroy(--p);

         // deallocate cannot be called on a 0 pointer
         if (elements)
             // return the memory that held the elements
             alloc.deallocate(elements, end - elements);
         // make our data structure point to the new elements
         elements = newelements;
         first_free = elements + size;
         end = elements + newcapacity;
     }
operator new 函数和 operator delete 函数
当使用 new 表达式：
     string * sp = new string("initialized");
的时候，实际上发生三个步骤。首先，该表达式调用名为 operator new 的标准库函数，分配足够大的原始的未类型化的内存，以保存指定类型的一个对象；接下来，运行该类型的一个构造函数，用指定初始化式构造对象；最后，返回指向新分配并构造的对象的指针。
当使用 delete 表达式：
     delete sp;
删除动态分配对象的时候，发生两个步骤。首先，对 sp 指向的对象运行适当的析构函数；然后，通过调用名为 operator delete 的标准库函数释放该对象所用内存。
new 表达式与 operator new 函数：通过调用 operator new 函数执行 new 表达式获得内存，并接着在该内存中构造一个对象，通过撤销一个对象执行 delete 表达式，并接着调用 operator delete 函数，以释放该对象使用的内存。
operator new 和 operator delete 函数有两个重载版本，每个版本支持相关的 new 表达式和 delete 表达式：
     void *operator new(size_t);       // allocate an object
     void *operator new[](size_t);     // allocate an array
     void *operator delete(void*);     // free an object
     void *operator delete[](void*);   // free an array
operator new 和 operator delete 函数可以用来获得未构造内存，类似 allocate 类的 allocator 和 deallocate 成员。例如，代替使用 allocator 对象，可以在 Vector 类中使用 operator new 和 operator delete 函数：
     // allocate space to hold newcapacity number of elements of type T
     T* newelements = alloc.allocate(newcapacity);
这可以重新编写为
     // allocate unconstructed memory to hold newcapacity elements of type T
     T* newelements = static_cast<T*>
                    (operator new[](newcapacity * sizeof(T)));
类似地，在重新分配由 Vector 成员 elements 指向的旧空间：
     // return the memory that held the elements
     alloc.deallocate(elements, end - elements);
这可以重新编写为
     // deallocate the memory that they occupied
     operator delete[](elements);
这些函数的表现与 allocate 类的 allocator 和 deallocate 成员类似。但是，它们在一个重要方面有不同：它们在 void* 指针而不是类型化的指针上进行操作。一般而言，使用 allocator 比直接使用 operator new 和 operator delete 函数更为类型安全。allocate 成员分配类型化的内存，所以使用它的程序可以不必计算以字节为单位的所需内存量，它们也可以避免对 operator new 的返回值进行强制类型转换。类似地，deallocate 释放特定类型的内存，也不必转换为 void*。
定位 new 表达式：定位 new 表达式在已分配的原始内存中初始化一个对象，它与 new 的其他版本的不同之处在于，它不分配内存。相反，它接受指向已分配但未构造内存的指针，并在该内存中初始化一个对象。实际上，定位 new 表达式使我们能够在特定的、预分配的内存地址构造一个对象。
定位 new 表达式的形式是：
     new (place_address) type
     new (place_address) type (initializer-list)
其中 place_address 必须是一个指针，而 initializer-list 提供了（可能为空的）初始化列表，以便在构造新分配的对象时使用。
可以使用定位 new 表达式代替 Vector 实现中的 construct 调用。原来的代码
     // construct a copy t in the element to which first_free points
     alloc.construct (first_free, t);
可以用等价的定位 new 表达式代替
     // copy t into element addressed by first_free
     new (first_free) T(t);
定位 new 表达式比 allocator 类的 construct 成员更灵活。定位 new 表达式初始化一个对象的时候，它可以使用任何构造函数，并直接建立对象。construct 函数总是使用复制构造函数。例如，可以用下面两种方式之一，从一对迭代器初始化一个已分配但未构造的 string 对象：
     allocator<string> alloc;
     string *sp = alloc.allocate(2); // allocate space to hold 2 strings
     // two ways to construct a string from a pair of iterators
     new (sp) string(b, e);                    // construct directly in place
     alloc.construct(sp + 1, string(b, e));   // build and copy a temporary
定位 new 表达式使用了接受一对迭代器的 string 构造函数，在 sp 指向的空间直接构造 string 对象。当调用 construct 函数的时候，必须首先从迭代器构造一个 string 对象，以获得传递给 construct 的 string 对象，然后，该函数使用 string 的复制构造函数，将那个未命名的临时 string 对象复制到 sp 指向的对象中。通常，这些区别是不相干的：对值型类而言，在适当的位置直接构造对象与构造临时对象并进行复制之间没有可观察到的区别，而且性能差别基本没有意义。但对某些类而言，使用复制构造函数是不可能的（因为复制构造函数是私有的），或者是应该避免的，在这种情况下，也许有必要使用定位 new 表达式。
显式析构函数的调用：正如定位 new 表达式是使用 allocate 类的 construct 成员的低级选择，可以使用析构函数的显式调用作为调用 destroy 函数的低级选择。
在使用 allocator 对象的 Vector 版本中，通过调用 destroy 函数清除每个元素：
     // destroy the old elements in reverse order
     for (T *p = first_free; p != elements; /* empty */ )
         alloc.destroy(--p);
对于使用定位 new 表达式构造对象的程序，显式调用析构函数：
     for (T *p = first_free; p != elements; /* empty */ )
         p->~T(); // call the destructor
显式调用析构函数的效果是适当地清除对象本身。但是，并没有释放对象所占的内存，如果需要，可以重用该内存空间
调用 operator delete 函数不会运行析构函数，它只释放指定的内存。
类特定的 new 和 delete：默认情况下，new 表达式通过调用由标准库定义的 operator new 版本分配内存。通过定义自己的名为 operator new 和 operator delete 的成员，类可以管理用于自身类型的内存。优化 new 和 delete 的行为的时候，只需要定义 operator new 和 operator delete 的新版本，new 和 delete 表达式自己照管对象的构造和撤销。
类成员 operator new 函数必须具有返回类型 void* 并接受 size_t 类型的形参。由 new 表达式用以字节计算的分配内存量初始化函数的 size_t 形参。类成员 operator delete 函数必须具有返回类型 void。它可以定义为接受单个 void* 类型形参，也可以定义为接受两个形参，即 void* 和 size_t 类型。由 delete 表达式用被 delete 的指针初始化 void* 形参，该指针可以是空指针。如果提供了 size_t 形参，就由编译器用第一个形参所指对象的字节大小自动初始化 size_t 形参。
除非类是某继承层次的一部分，否则形参 size_t 不是必需的。当 delete 指向继承层次中类型的指针时，指针可以指向基类对象，也可以指向派生类对象。派生类对象的大小一般比基类对象大。如果基类有 virtual 析构函数，则传给 operator delete 的大小将根据被删除指针所指对象的动态类型而变化；如果基类没有 virtual 析构函数，那么，通过基类指针删除指向派生类对象的指针的行为，跟往常一样是未定义的。
这些函数隐式地为静态函数，不必显式地将它们声明为 static，虽然这样做是合法的。成员 new 和 delete 函数必须是静态的，因为它们要么在构造对象之前使用（operator new），要么在撤销对象之后使用（operator delete），像任意其他静态成员函数一样，new 和 delete 只能直接访问所属类的静态成员
操作符 new[] 和操作符 delete[]：也可以定义成员 operator new[] 和 operator delete[] 来管理类类型的数组。如果这些 operator 函数存在，编译器就使用它们代替全局版本。
类成员 operator new[] 必须具有返回类型 void*，并且接受的第一个形参类型为 size_t。用表示存储特定类型给定数目元素的数组的字节数值自动初始化操作符的 size_t 形参。成员操作符 operator delete[] 必须具有返回类型 void，并且第一个形参为 void* 类型。用表示数组存储起始位置的值自动初始化操作符的 void* 形参。类的操作符 delete[] 也可以有两个形参，第二个形参为 size_t。如果提供了附加形参，由编译器用数组所需存储量的字节数自动初始化这个形参。
一个内存分配器基类：CachedObj 类【Coding】

### 2. 运行时类型识别
通过运行时类型识别（RTTI），程序能够使用基类的指针或引用来检索这些指针或引用所指对象的实际派生类型。通过下面两个操作符提供 RTTI：
	1）typeid 操作符，返回指针或引用所指对象的实际类型。
	2）dynamic_cast 操作符，将基类类型的指针或引用安全地转换为派生类型的指针或引用。
这些操作符只为带有一个或多个虚函数的类返回动态类型信息，对于其他类型，返回静态（即编译时）类型的信息。对于带虚函数的类，在运行时执行 RTTI 操作符，但对于其他类型，在编译时计算 RTTI 操作符。
通常，从基类指针获得派生类行为最好的方法是通过虚函数。但是，在某些情况下，不可能使用虚函数。在这些情况下，RTTI 提供了可选的机制。然而，这种机制比使用虚函数更容易出错：程序员必须知道应该将对象强制转换为哪种类型，并且必须检查转换是否成功执行了。
dynamic_cast 操作符：可以使用 dynamic_cast 操作符将基类类型对象的引用或指针转换为同一继承层次中其他类型的引用或指针。与 dynamic_cast 一起使用的指针必须是有效的——它必须为 0 或者指向一个对象。与其他强制类型转换不同，dynamic_cast 涉及运行时类型检查。如果绑定到引用或指针的对象不是目标类型的对象，则 dynamic_cast 失败。如果转换到指针类型的 dynamic_cast 失败，则 dynamic_cast 的结果是 0 值；如果转换到引用类型的 dynamic_cast 失败，则抛出一个 bad_cast 类型的异常。
假定 Base 是至少带一个虚函数的类，并且 Derived 类派生于 Base 类。如果有一个名为 basePtr 的指向 Base 的指针，就可以像这样在运行时将它强制转换为指向 Derived 的指针：
     if (Derived *derivedPtr = dynamic_cast<Derived*>(basePtr))
在运行时，如果 basePtr 实际指向 Derived 对象，则转换将成功，并且 derivedPtr 将被初始化为指向 basePtr 所指的 Derived 对象；否则，转换的结果是 0，意味着将 derivedPtr 置为 0，并且 if 中的条件失败。
也可以使用 dynamic_cast 将基类引用转换为派生类引用，这种 dynamic_cast 操作的形式如下：
     dynamic_cast< Type& >(val)
这里，Type 是转换的目标类型，而 val 是基类类型的对象。当转换失败的时候，它抛出一个 std::bad_cast 异常，该异常在库头文件 typeinfo 中定义。
typeid 操作符：typeid(e)，这里 e 是任意表达式或者是类型名。typeid 操作符可以与任何类型的表达式一起使用。内置类型的表达式以及常量都可以用作 typeid 操作符的操作数。如果操作数不是类类型或者是没有虚函数的类，则 typeid 操作符指出操作数的静态类型；如果操作数是定义了至少一个虚函数的类类型，则在运行时计算类型。typeid 操作符的结果是名为 type_info 的标准库类型的对象引用，要使用 type_info 类，必须包含库头文件 typeinfo
	Base *bp;
     Derived *dp;
     // compare type at run time of two objects
     if (typeid(*bp) == typeid(*dp)) {
         // bp and dp point to objects of the same type
     }
     // test whether run time type is a specific type
     if (typeid(*bp) == typeid(Derived)) {
         // bp actually points to a Derived
     }
第一个 if 中，比较 bp 所指对象与 dp 所指对象的实际类型，如果它们指向同一类型，则测试成功。类似地，如果 bp 当前指向 Derived 对象，则第二个 if 成功。
注意，typeid 的操作数是表示对象的表达式——测试 *bp，而不是 bp：测试指针（相对于指针指向的对象）返回指针的静态的、编译时类型。
     // test always fails: The type of bp is pointer to Base
     if (typeid(bp) == typeid(Derived)) {
          // code never executed
     }
如果指针 p 的值是 0，那么，如果 p 的类型是带虚函数的类型，则 typeid(*p) 抛出一个 bad_typeid 异常；如果 p 的类型没有定义任何虚函数，则结果与 p 的值是不相关的。正像计算表达式 sizeof一样，编译器不计算 *p，它使用 p 的静态类型，这并不要求 p 本身是有效指针
RTTI 的使用，设计一个类层次。【Coding】

### 3. 类成员的指针
成员指针包含类的类型以及成员的类型。成员指针只应用于类的非 static 成员。static 类成员不是任何对象的组成部分，所以不需要特殊语法来指向 static 成员，static 成员指针是普通指针。
	class Screen {
     public:
         typedef std::string::size_type index;
         char get() const;
         char get(index ht, index wd) const;
     private:
         std::string contents;
         index cursor;
         index height, width;
     };
Screen 类的 contents 成员的类型为 std::string。contents 的完全类型是“Screen 类的成员，其类型是 std::string”。因此，可以指向 contents 的指针的完全类型是“指向 std::string 类型的 Screen 类成员的指针”，这个类型可写为
     string Screen::*
可以将指向 Screen 类的 string 成员的指针定义为
     string Screen::*ps_Screen;
可以用 contents 的地址初始化 ps_Screen，代码为
     string Screen::*ps_Screen = &Screen::contents;
成员函数的指针必须在三个方面与它所指函数的类型相匹配：
	1）函数形参的类型和数目，包括成员是否为 const。
	2）返回类型。
	3）所属类的类型。
例如，不接受形参的 get 版本的 Screen 成员函数的指针具有如下类型：
     char (Screen::*)() const
	// pmf points to the Screen get member that takes no arguments
     char (Screen::*pmf)() const = &Screen::get;
也可以将带两个形参的 get 函数版本的指针定义为
     char (Screen::*pmf2)(Screen::index, Screen::index) const;
     pmf2 = &Screen::get;
包围 Screen::* 的括号是必要的，没有这个括号，编译器就将下面代码当作（无效的）函数声明：
      // error: non-member function p cannot have const qualifier
     char Screen::*p() const;
为成员指针使用类型别名：下面的类型别名将 Action 定义为带两个形参的 get 函数版本的类型的另一名字：
     // Action is a type name
     typedef char (Screen::*Action)(Screen::index, Screen::index) const;
	Action get = &Screen::get;
使用类成员指针：类似于成员访问操作符 . 和 ->，.* 和 .-> 是两个新的操作符，它们使我们能够将成员指针绑定到实际对象。这两个操作符的左操作数必须是类类型的对象或类类型的指针，右操作数是该类型的成员指针。
	1）成员指针解引用操作符（.*）从对象或引用获取成员。
	2）成员指针箭头操作符（->*）通过对象的指针获取成员。
  	// pmf points to the Screen get member that takes no arguments
     char (Screen::*pmf)() const = &Screen::get;
     Screen myScreen;
     char c1 = myScreen.get();      // call get on myScreen
     char c2 = (myScreen.*pmf)();   // equivalent call to get
     Screen *pScreen = &myScreen;
     c1 = pScreen->get();     // call get on object to which pScreen points
     c2 = (pScreen->*pmf)();  // equivalent call to get
因为调用操作符（()）比成员指针操作符优先级高，所以调用 (myScreen.*pmf)() 和 (pScreen->*pmf)() 需要括号。
也可以在通过成员函数指针进行的调用中传递实参：
     char (Screen::*pmf2)(Screen::index, Screen::index) const;
     pmf2 = &Screen::get;
     Screen myScreen;
     char c1 = myScreen.get(0,0);     // call two-parameter version of get
     char c2 = (myScreen.*pmf2)(0,0); // equivalent call to get
成员指针操作符用于访问数据成员：
     Screen::index Screen::*pindex = &Screen::width;
     Screen myScreen;
     // equivalent ways to fetch width member of myScreen
     Screen::index ind1 = myScreen.width;      // directly
     Screen::index ind2 = myScreen.*pindex;    // dereference to get width
     Screen *pScreen;
     // equivalent ways to fetch width member of *pScreen
     ind1 = pScreen->width;        // directly
     ind2 = pScreen->*pindex;      // dereference pindex to get width
成员指针函数表

### 4. 嵌套类
可以在另一个类内部定义一个类，这样的类是嵌套类，也称为嵌套类型。嵌套类最常用于定义执行类。嵌套类是独立的类，基本上与它们的外围类不相关，因此，外围类和嵌套类的对象是互相独立的。嵌套类型的对象不具备外围类所定义的成员，同样，外围类的成员也不具备嵌套类所定义的成员。嵌套类的名字在其外围类的作用域中可见，但在其他类作用域或定义外围类的作用域中不可见。嵌套类的名字将不会与另一作用域中声明的名字冲突。嵌套类定义了其外围类中的一个类型成员。像任何其他成员一样，外围类决定对这个类型的访问。在外围类的 public 部分定义的嵌套类定义了可在任何地方使用的类型，在外围类的 protected 部分定义的嵌套类定义了只能由外围类、友元或派生类访问的类型，在外围类的 private 部分定义的嵌套类定义了只能被外围类或其友元访问的类型。
	template <class Type> class Queue {
         // interface functions to Queue are unchanged
     private:
         // public members are ok: QueueItem is a private member of Queue
         // only Queue and its friends may access the members of QueueItem
         struct QueueItem {
             QueueItem(const Type &);
             Type item;            // value stored in this element
             QueueItem *next;      // pointer to next element in the Queue
         };
         QueueItem *head;      // pointer to first element in Queue
         QueueItem *tail;      // pointer to last element in Queue
     };
在其类外部定义的嵌套类成员，必须定义在定义外围类的同一作用域中。在其类外部定义的嵌套类的成员，不能定义在外围类内部，嵌套类的成员不是外围类的成员。
	// defines the QueueItem constructor
     // for class QueueItem nested inside class Queue<Type>
     template <class Type>
     Queue<Type>::QueueItem::QueueItem(const Type &t): item(t), next(0) { }
在外围类外部定义嵌套类：
	template <class Type> class Queue {
         // interface functions to Queue are unchanged
     private:
         struct QueueItem; // forward declaration of nested type QueueItem
         QueueItem *head;  // pointer to first element in Queue
         QueueItem *tail;  // pointer to last element in Queue
     };
     template <class Type>
     struct Queue<Type>::QueueItem {
         QueueItem(const Type &t): item(t), next(0) { }
         Type item;        // value stored in this element
         QueueItem *next; // pointer to next element in the Queue
     };
在看到在类定义体外部定义的嵌套类的实际定义之前，该类是不完全类型，应用所有使用不完全类型的常规限制。
嵌套类作用域中的名字查找：
	class Outer {
     public:
         struct Inner {
             // ok: reference to incomplete class
             void process(const Outer&);
             Inner2 val; // error: Outer::Inner2 not in scope
         };
         class Inner2 {
         public:
             // ok: Inner2::val used in definition
             Inner2(int i = 0): val(i) { }
             // ok: definition of process compiled after enclosing class is complete
             void process(const Outer &out) { out.handle(); }
         private:
             int val;
         };
         void handle() const; // member of class Outer
     };

### 5. 联合：节省空间的类
联合是一种特殊的类。一个 union 对象可以有多个数据成员，但在任何时刻，只有一个成员可以有值。当将一个值赋给 union 对象的一个成员的时候，其他所有都变为未定义的。使用 union 对象时，我们必须总是知道 union 对象中当前存储的是什么类型的值。通过错误的数据成员检索保存在 union 对象中的值，可能会导致程序崩溃或者其他不正确的程序行为。像任何类一样，一个 union 定义了一个新的类型。
联合提供了便利的办法表示一组相互排斥的值，这些值可以是不同类型的。
	union TokenValue {
         char   cval;
         int    ival;
         double dval;
     };
每个 union 对象的大小在编译时固定的：它至少与 union 的最大数据成员一样大。
没有静态数据成员、引用成员或类数据成员：像任何类一样，union 可以指定保护标记使成员成为公用的、私有的或受保护的。默认情况下，union 表现得像 struct：除非另外指定，否则 union 的成员都为 public 成员。union 也可以定义成员函数，包括构造函数和析构函数。但是，union 不能作为基类使用，所以成员函数不能为虚数。union 不能具有静态数据成员或引用成员，而且，union 不能具有定义了构造函数、析构函数或赋值操作符的类类型的成员：
     union illegal_members {
         Screen s;      // error: has constructor
         static int is; // error: static member
         int &rfi;      // error: reference member
         Screen *ps;    // ok: ordinary built-in pointer type
     };
union 的名字是一个类型名：
     TokenValue first_token = {'a'};  // initialized TokenValue
     TokenValue last_token;           // uninitialized TokenValue object
     TokenValue *pt = new TokenValue; // pointer to a TokenValue object
默认情况下 union 对象是未初始化的。可以用与显式初始化简单类对象一样的方法显式初始化 union 对象。但是，只能为第一个成员提供初始化式。该初始化式必须括在一对花括号中。
可以使用普通成员访问操作符（. 和 ->）访问 union 类型对象的成员：
     last_token.cval = 'z';
     pt->ival = 42;
避免通过错误成员访问 union 值的最佳办法是，定义一个单独的对象跟踪 union 中存储了什么值。这个附加对象称为 union 的判别式。
union 最经常用作嵌套类型，其中判别式是外围类的一个成员：
     class Token {
     public:
         // indicates which kind of value is in val
         enum TokenKind {INT, CHAR, DBL};
         TokenKind tok;
         union {             // unnamed union
             char   cval;
             int    ival;
             double dval;
         } val;              // member val is a union of the 3 listed types
     };
经常使用 switch 语句测试判别式，然后根据 union 中当前存储的值进行处理。
匿名联合：匿名 union 的成员的名字出现在外围作用域中。
	class Token {
     public:
         // indicates which kind of token value is in val
         enum TokenKind {INT, CHAR, DBL};
         TokenKind tok;
         union {                 // anonymous union
             char   cval;
             int    ival;
             double dval;
         };
     };
因为匿名 union 不提供访问其成员的途径，所以将成员作为定义匿名 union 的作用域的一部分直接访问。如下：
     Token token;
     switch (token.tok) {
     case Token::INT:
         token.ival = 42; break;
     case Token::CHAR:
         token.cval = 'a'; break;
     case Token::DBL:
         token.dval = 3.14; break;
     }

### 6. 局部类
可以在函数体内部定义类，这样的类称为局部类。一个局部类定义了一个类型，该类型只在定义它的局部作用域中可见。与嵌套类不同，局部类的成员是严格受限的。局部类的所有成员（包括函数）必须完全定义在类定义体内部。不允许局部类声明 static 数据成员。
局部类不能使用函数作用域中的变量。局部类可以访问的外围作用域中的名字是有限的。局部类只能访问在外围作用域中定义的类型名、static 变量和枚举成员，不能使用定义该类的函数中的变量：
     int a, val;
     void foo(int val)
     {
        static int si;
        enum Loc { a = 1024, b };
        // Bar is local to foo
        class Bar {
        public:
            Loc locVal; // ok: uses local type name
            int barVal;
            void fooBar(Loc l = a)         // ok: default argument is Loc::a
            {
               barVal = val;      // error: val is local to foo
               barVal = ::val;    // ok: uses global object
               barVal = si;       // ok: uses static local object
               locVal = b;        // ok: uses enumerator
            }
        };
        // ...
     }
外围函数对局部类的私有成员没有特殊访问权。不过局部类可以将外围函数设为友元。
局部类中 private 成员几乎是不必要的，通常局部类的所有成员都为 public 成员。
局部类中的名字查找：局部类定义体中的名字查找方式与其他类的相同。类成员声明中所用的名字必须在名字使用之前出现在作用域中，成员定义中所用的名字可以出现在局部类作用域的任何地方。没有确定为类成员的名字首先在外围局部作用域中进行查找，然后在包围函数本身的作用域中查找。
嵌套的局部类：可以将一个类嵌套在局部类内部。这种情况下，嵌套类定义可以出现在局部类定义体之外，但是，嵌套类必须在定义局部类的同一作用域中定义。照常，嵌套类的名字必须用外围类的名字进行限定，并且嵌套类的声明必须出现在局部类的定义中：
     void foo()
     {
        class Bar {
        public:
            // ...
            class Nested;    // declares class Nested
        };
        //  definition of Nested
        class Bar::Nested {
            // ...
        };
     }
嵌套在局部类中的类本身是一个带有所有附加限制的局部类。嵌套类的所有成员必须在嵌套类本身定义体内部定义。

### 7. 固有的不可移植的特征
位域：当程序需要将二进制数据传递给另一程序或硬件设备的时候，通常使用位域。位域在内存中的布局是机器相关的。位域必须是整型数据类型，可以是 signed 或 unsigned。通过在成员名后面接一个冒号以及指定位数的常量表达式，指出成员是一个位域：
     typedef unsigned int Bit;
     class File {
         Bit mode: 2;
         Bit modified: 1;
         Bit prot_owner: 3;
         Bit prot_group: 3;
         Bit prot_world: 3;
         // ...
     };
mode 位域有两个位，modified 只有一位，其他每个成员有三个位。通常最好将位域设为 unsigned 类型。存储在 signed 类型中的位域的行为由实现定义。位域不能是类的静态成员。
通常使用内置按位操作符操纵超过一位的位域：
     enum { READ = 01, WRITE = 02 }; // File modes
     int main() {
         File myFile;
         myFile.mode |= READ; // set the READ bit
         if (myFile.mode & READ) // if the READ bit is on
             cout << "myFile.mode READ is set\n";
     }
volatile 限定符：volatile 的确切含义与机器相关，只能通过阅读编译器文档来理解。使用 volatile 的程序在移到新的机器或编译器时通常必须改变。
直接处理硬件的程序常具有这样的数据成员，它们的值由程序本身直接控制之外的过程所控制。例如，程序可以包含由系统时钟更新的变量。当可以用编译器的控制或检测之外的方式改变对象值的时候，应该将对象声明为 volatile。关键字 volatile 是给编译器的指示，指出对这样的对象不应该执行优化。
用与 const 限定符相同的方式使用 volatile 限定符。volatile 限定符是一个对类型的附加修饰符：
     volatile int display_register;
     volatile Task *curr_task;
     volatile int ixa[max_size];
     volatile Screen bitmap_buf;
用与定义 const 成员函数相同的方式，类也可以将成员函数定义为 volatile，volatile 对象只能调用 volatile 成员函数。
对待 const 和 volatile 的一个重要区别是，不能使用合成的复制和赋值操作符从 volatile 对象进行初始化或赋值。合成的复制控制成员接受 const 形参，这些形参是对类类型的 const 引用，但是，不能将 volatile 对象传递给普通引用或 const 引用。
如果类希望允许复制 volatile 对象，或者，类希望允许从 volatile 操作数或对 volatile 操作数进行赋值，它必须定义自己的复制构造函数和／或赋值操作符版本：
     class Foo {
     public:
         Foo(const volatile Foo&);    // copy from a volatile object
         // assign from a volatile object to a non volatile objet
         Foo& operator=(volatile const Foo&);
         // assign from a volatile object to a volatile object
         Foo& operator=(volatile const Foo&) volatile;
         // remainder of class Foo
     };
链接指示 extern "C"：调用用其他程序设计语言编写的函数，像任何名字一样，必须声明用其他语言编写的函数的名字，该声明必须指定返回类型和形参表。编译器按处理普通 C++ 函数一样的方式检查对外部语言函数的调用，但是，编译器一般必须产生不同的代码来调用用其他语言编写的函数。C++ 使用链接指示指出任意非 C++ 函数所用的语言。
声明非 C++ 函数：链接指示有两种形式：单个的或复合的。链接指示不能出现在类定义或函数定义的内部，它必须出现在函数的第一次声明上
头文件 cstdlib 中声明的一些 C 函数：
	// illustrative linkage directives that might appear in the C++ header <cstring>
     // single statement linkage directive
     extern "C" size_t strlen(const char *);
     // compound statement linkage directive
     extern "C" {
         int strcmp(const char*, const char*);
         char *strcat(char*, const char*);
     }
第一种形式由关键字 extern 后接字符串字面值，再接“普通”函数声明构成。字符串字面值指出编写函数所用的语言。第二种形式通过将几个函数的声明放在跟在链接指示之后的花括号内部，可以给它们设定相同的链接。花括号的作用是将应用链接指示的声明聚合起来，忽略了花括号，花括号中声明的函数名就是可见的，就像在花括号之外声明函数一样
可以将多重声明形式应用于整个头文件。假定头文件中的所有普通函数声明都是用链接指示的语言编写的函数。例如，C++ 的 cstring 头文件可以像这样：
     // compound statement linkage directive
     extern "C" {
     #include <string.h>     // C functions that manipulate C-style strings
     }
导出 C++ 函数到其他语言：通过对函数定义使用链接指示，使得用其他语言编写的程序可以使用 C++ 函数：
     // the calc function can be called from C programs
     extern "C" double calc(double dparm) { /* ... */ }
当编译器为该函数产生代码的时候，它将产生适合于指定语言的代码。
用链接指示定义的函数的每个声明都必须使用相同的链接指示。
有时需要在 C 和 C++ 中编译同一源文件。当编译 C++ 时，自动定义预处理器名字 __cplusplus（两个下划线），所以，可以根据是否正在编译 C++ 有条件地包含代码。
     #ifdef __cplusplus
     // ok: we're compiling C++
     extern "C"
     #endif
     int strcmp(const char*, const char*);
重载函数与链接指示：如果语言支持重载函数，则为该语言实现链接指示的编译器很可能也支持 C++ 的这些函数的重载。C++ 保证支持的唯一语言是 C。C 语言不支持函数重载。在一组重载函数中只能为一个 C 函数指定链接指示。用带给定名字的 C 链接声明多于一个函数是错误的：
     // error: two extern "C" functions in set of overloaded functions
     extern "C" void print(const char*);
     extern "C" void print(int);
在 C++ 程序中，重载 C 函数很常见，但是，重载集合中的其他函数必须都是 C++ 函数：
     class SmallInt { /* ... */ };
     class BigNum { /* ... */ };
     // the C function can be called from C and C++ programs
     // the C++ functions overload that function and are callable from C++
     extern "C" double calc(double);
     extern SmallInt calc(const SmallInt&);
     extern BigNum calc(const BigNum&);
可以从 C 程序和 C++ 程序调用 calc 的 C 版本。其余函数是带类型形参的 C++ 函数，只能从 C++ 程序调用。声明的次序不重要。
编写函数所用的语言是函数类型的一部分。为了声明用其他程序设计语言编写的函数的指针，必须使用链接指示：
     // pf points to a C function returning void taking an int
     extern "C" void (*pf)(int);
C 函数的指针与 C++ 函数的指针具有不同的类型，不能将 C 函数的指针初始化或赋值为 C++ 函数的指针（反之亦然）。
应用于整个声明的链接指示：使用链接指示的时候，它应用于函数和任何函数指针，作为返回类型或形参类型使用：
     // f1 is a C function; its parameter is a pointer to a C function
     extern "C" void f1(void(*)(int));
因为链接指示应用于一个声明中的所有函数，所以必须使用类型别名，以便将 C 函数的指针传递给 C++ 函数：
     // FC is a pointer to C function
     extern "C" typedef void FC(int);
     // f2 is a C++ function with a parameter that is a pointer to a C function
     void f2(FC *);


