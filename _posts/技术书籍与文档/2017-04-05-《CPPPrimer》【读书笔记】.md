---
layout: post
title:  "《C++ Primer》"
date: 2017-04-05 00:03:00
categories: 技术书籍与文档
tags: C++
excerpt: 《C++ Primer》的阅读笔记。
---

* content
{:toc}

## 1
当把一个超出其取值范围的值赋给一个指定类型的对象时，编译器会**将该值对该类型的可能取值数目求模，然后取所得值**。比如8位的unsigned char，其取值范围从0到255（包括255）。如果赋给超出这个范围的值，那么编译器将会取该值对256求模后的值。例如，如果试图将336存储到8位的unsigned char中，则实际赋值为80，因为80是336对256求模后的值。
对于unsigned类型来说，负数总是超出其取值范围。unsigned类型的对象可能永远不会保存负数。有些语言中将负数赋给unsigned类型是非法的，但在C++中这是合法的。其结果是该负数对该类型的取值个数求模后的值。所以，如果把-1赋给8位的unsigned char，那么结果是255，因为**255是-1对256求模后的值**。

## 2
C++支持两种初始化变量的形式：复制初始化和直接初始化。复制初始化语法用等号（=），直接初始化则是把初始化式放在括号中：
```c
int ival(1024);     // direct-initialization
int ival = 1024;    // copy-initialization
```
**对内置类型来说，复制初始化和直接初始化几乎没有差别**。
内置类型变量是否自动初始化取决于变量定义的位置。在函数体外定义的变量都初始化成0，**在函数体里定义的内置类型变量不进行自动初始化**。除了用作赋值操作符的左操作数，未初始化变量用作任何其他用途都是没有定义的。未初始化变量引起的错误难于发现。建议每个内置类型的对象都要初始化。

## 3
**const对象的引用只能是const引用，但是const引用也可用于非const对象**。
```c
const int ival = 1024;

// ok: both reference and object are const 
const int &refVal = ival;     

// error: 将普通的引用绑定到const对象是不合法的
int &ref2 = ival;            
```
可以读取但不能修改refVal ，因此，任何对 refVal 的赋值都是不合法的。这个限制有其意义：不能直接对ival赋值，因此不能通过使用refVal来修改ival。
const 引用可以初始化为不同类型的对象或者初始化为右值，如字面值常量：
```c
int i = 42;

//  legal for const references only
const int &r = 42;
const int &r2 = r + i;
```

## 4
**数组的维数必须用值大于等于1的常量表达式定义**。此常量表达式只能包含整型字面值常量、枚举常量或者用常量表达式初始化的整型const对象。非const变量以及要到运行阶段才知道其值的const变量都不能用于定义数组的维数。
```c
const unsigned buf_size = 512, max_files = 20;
int staff_size = 27;             // nonconst
const unsigned sz = get_size();  // const value not known until run time
char input_buffer[buf_size];     // ok:const variable
string fileTable[max_files + 1]; // ok:constant expression
double salaries[staff_size];     // error: non const variable
int test_scores[get_size()];     // error: non const expression
int vals[sz];                    // error: size not known until run time
```
虽然 staff_size 是用字面值常量进行初始化，但 staff_size 本身是一个非const对象，只有在运行时才能获得它的值，因此，使用该变量来定义数组维数是非法的。而对于 sz，尽管它是一个const对象，但它的值要到运行时调用 get_size 函数后才知道，因此，它也不能用于定义数组维数。

## 5
如果没有显式提供元素初值，则**数组元素会像普通变量一样初始化**：
1. 在函数体外定义的内置数组，其元素均初始化为0。
2. 在函数体内定义的内置数组，其元素无初始化。
3. 不管数组在哪里定义，如果其元素为类类型，则自动调用该类的默认构造函数进行初始化；如果该类没有默认构造函数，则必须为该数组的元素提供显式初始化。

## 6
**指针和引用的比较**：虽然使用引用（reference）和指针都可间接访问另一个值，但它们之间有两个重要区别。第一个区别在于引用总是指向某个对象：定义引用时没有初始化是错误的。第二个重要区别则是赋值行为的差异：给引用赋值修改的是该引用所关联的对象的值，而并不是使引用与另一个对象关联。引用一经初始化，就始终指向同一个特定对象（这就是为什么引用必须在定义时初始化的原因）。

## 7
在使用下标访问数组时，实际上是对指向数组元素的指针做下标操作。**只要指针指向数组元素，就可以对它进行下标操作**：
```c
int ia[] = {0,2,4,6,8};
int *p = &ia[2];     // ok: p points to the element indexed by 2
int j = p[1];        // ok: p[1] equivalent to *(p + 1),
int k = p[-2];       // ok: p[-2] is the same element as ia[0]
```

## 8
创建动态数组：每一个程序在执行时都占用一块可用的内存空间，用于存放动态分配的对象，此内存空间称为程序的自由存储区或堆。C语言程序使用一对标准库函数malloc和free在自由存储区中分配存储空间，而 C++ 语言则使用new和delete表达式实现相同的功能。动态分配数组时，只需指定类型和数组长度，不必为数组对象命名，new 表达式返回指向新分配数组的第一个元素的指针：
```c
int *pia = new int[10]; // array of 10 uninitialized ints
```
动态分配数组时，如果数组元素具有类类型，将使用该类的默认构造函数实现初始化；如果数组元素是内置类型，则无初始化：
```c
string *psa = new string[10]; // array of 10 empty strings
int *pia = new int[10];       // array of 10 uninitialized ints
```
圆括号要求编译器对数组做值初始化：也可使用跟在数组长度后面的一对空圆括号，对数组元素做值初始化， 
```c
int *pia2 = new int[10] (); // array of 10 uninitialized ints
```
对于动态分配的数组，其元素只能初始化为元素类型的默认值，而不能像数组变量一样，用初始化列表为数组元素提供各不相同的初值。
const对象的动态数组（这样的数组实际上用处不大）：如果我们在自由存储区中创建的数组存储了内置类型的const对象，则必须为这个数组提供初始化：因为数组元素都是const对象，无法赋值。实现这个要求的唯一方法是对数组做值初始化：  
```c        
const int *pci_bad = new const int[100]; // error: uninitialized const array
const int *pci_ok = new const int[100](); // ok: value-initialized const array
```
C++ 允许定义类类型的const数组，但该类类型必须提供默认构造函数：
```c
const string *pcs = new const string[100];  // ok: array of 100 empty strings
```
C++ 虽然不允许定义长度为 0 的数组变量，但明确指出，调用 new 动态创建长度为 0 的数组是合法的：
```c
char arr[0];            // error: cannot define zero-length array
char *cp = new char[0]; // ok: but cp can't be dereferenced
```
用 new 动态创建长度为 0 的数组时，new 返回有效的非零指针。该指针与 new 返回的其他指针不同，不能进行解引用操作，因为它毕竟没有指向任何元素。而允许的操作包括：比较运算，因此该指针能在循环中使用；在该指针上加（减）0；或者减去本身，得 0 值。
如果不再需要使用动态创建的数组，程序员必须显式地将其占用的存储空间返还给程序的自由存储区。C++ 语言为指针提供 delete [] 表达式释放指针所指向的数组空间：delete [] pia; 该语句回收了 pia 所指向的数组，把相应的内存返还给自由存储区。在关键字 delete 和指针之间的空方括号对是必不可少的：它告诉编译器该指针指向的是自由存储区中的数组，而并非单个对象。

## 9
使用数组初始化vector对象，必须指出用于初始化式的第一个元素以及数组最后一个元素的下一位置的地址：
```c
const size_t arr_size = 6;
int int_arr[arr_size] = {0, 1, 2, 3, 4, 5};

// ivec has 6 elements: each a copy of the corresponding element in int_arr
vector<int> ivec(int_arr, int_arr + arr_size);
```
被标出的元素范围可以是数组的子集：
```c
vector<int> ivec(int_arr + 1, int_arr + 4);
```

## 10
使用sizeof的结果部分地依赖所涉及的类型：
1. 对char类型或值为char类型的表达式做sizeof操作保证得1。
2. 对引用类型做sizeof操作将返回存放被引用类型对象所需的内存空间大小。
3. 对指针做sizeof操作将返回存放指针所需的内存大小；注意，如果要获取该指针所指向对象的大小，则必须对指针进行引用。
4. 对数组做sizeof操作等效于将对其元素类型做sizeof操作的结果乘上数组元素的个数。
因为sizeof返回整个数组在内存中的存储长度，所以用sizeof数组的结果除以sizeof其元素类型的结果，即可求出数组元素的个数。

## 11
标准C++为了加强类型转换的可视性，引入命名的强制转换操作符：
1. **dynamic_cast**支持运行时识别指针或引用所指向的对象。
2. **const_cast**将转换掉表达式的const性质。
```c
const char *pc_str;
char *pc = string_copy(const_cast<char*>(pc_str));
```
只有使用const_cast 才能将const性质转换掉。在这种情况下，试图使用其他三种形式的强制转换都会导致编译时的错误。类似地，除了添加或删除const 特性，用const_cast 符来执行其他任何类型转换，都会引起编译错误。
3. **static_cast**：编译器隐式执行的任何类型转换都可以由static_cast显式完成
```c
void* p = &d; // ok: address of any data object can be stored in a void*
// ok: converts void* back to the original pointer type
double *dp = static_cast<double*>(p);
```
4. **reinterpret_cast**通常为操作数的位模式提供较低层次的重新解释。
旧式强制类型转换：在引入命名的强制类型转换操作符之前，显式强制转换用圆括号将类型括起来实现：
```c
char *pc = (char*) ip;
```
效果与使用reinterpret_cast符号相同，但这种强制转换的可视性比较差，难以跟踪错误的转换。
附：**dynamic_cast<type-id>(expression)**与**static_cast<type-id>(expression)**的区别：
static_cast字面意思是静态转换，编译期间就能判断是否可以转换成功，但是无法识别兄弟指针之间的转换（先提升一个兄弟指针为父指针，再向下转换）而dynamic_cast是运行时转换，可以编译通过，但是可以与NULL指针的比较来判断是否转换成功！
​	
## 12
另一个常见的调试技术是使用NDEBUG预处理变量以及assert预处理宏,assert宏是在cassert头文件中定义的。
assert宏需要一个表达式作为它的条件：
```c
assert(expr)
```
只要NDEBUG未定义，assert宏就求解条件表达式expr，如果结果为false，assert 输出信息并且终止程序的执行。如果该表达式有一个非零（例如，true）值，则 assert不做任何操作。
在测试过程中，assert 等效于检验数据是否总是具有预期的大小。一旦开发和测试工作完成，程序就已经建立好，并且定义了NDEBUG。在成品代码中，assert语句不做任何工作，因此也没有任何运行时代价。当然，也不会引起任何运行时检查。assert仅用于检查确实不可能的条件，这只对程序的调试有帮助，但不能用来代替运行时的逻辑检查，也不能代替对程序可能产生的错误的检测。

## 13
非const引用形参只能与完全同类型的非const对象关联。
应该将不需要修改的引用形参定义为const引用。普通的非const引用形参在使用时不太灵活。这样的形参既不能用const对象初始化，也不能用字面值或产生右值的表达式实参初始化。

## 14
如果形参是数组的引用，编译器不会将数组实参转化为指针，而是传递数组的引用本身。在这种情况下，数组大小成为形参和实参类型的一部分。编译器检查数组的实参的大小与形参的大小是否匹配：
```c
void printValues(int (&arr)[10]) { /* ... */ }
```

## 15
如果一对函数的区别仅在于是否将形参定义为const，则仍为重复定义。值得注意的是，形参与const形参的等价性仅适用于非引用、非指针形参。有const引用形参的函数与有非const引用形参的函数是不同的。类似地，如果函数带有指向const类型的指针形参，则与带有指向相同类型的非const对象的指针形参的函数不相同。

## 16
容器内元素的类型约束：C++语言中，大多数类型都可用作容器的元素类型。容器元素类型必须满足以下两个约束：
1. 元素类型必须支持赋值运算。
2. 元素类型的对象必须可以复制。
此外，关联容器的键类型还需满足其他的约束。

## 17
可以声明一个类而不定义它：
```c
class Screen; // declaration of the Screen class
```
这个声明，有时称为前向声明（forward declaraton），在程序中引入了类类型的 Screen。在声明之后、定义之前，类 Screen 是一个不完全类型（incompete type），即已知 Screen 是一个类型，但不知道包含哪些成员。不完全类型（incomplete type）只能以有限方式使用。不能定义该类型的对象。不完全类型只能用于定义指向该类型的指针及引用，或者用于声明（而不是定义）使用该类型作为形参类型或返回类型的函数。

## 18
在定义于类外部的成员函数中，形参表和函数体处于类作用域中，函数返回类型不一定在类作用域中：与形参类型相比，返回类型出现在成员名字前面。如果函数在类定义体之外定义，则用于返回类型的名字在类作用域之外。如果返回类型使用由类定义的类型，则必须使用完全限定名：
```c	
class Screen {
  public:
    typedef std::string::size_type index;
    index get_cursor()const;
};

inline  Screen::index  Screen::get_cursor()const
{
  return cursor;
}
```

## 19
有些成员必须在构造函数初始化列表中进行初始化。对于这样的成员，在构造函数函数体中对它们赋值不起作用。没有默认构造函数的类类型的成员，以及const或引用类型的成员，不管是哪种类型，都必须在构造函数初始化列表中进行初始化。

## 20
NoDefault类没有默认构造函数，意味着：
1. 具有 NoDefault 成员的每个类的每个构造函数，必须通过传递一个初始的 string 值给 NoDefault 构造函数来显式地初始化 NoDefault 成员。
2. 编译器将不会为具有 NoDefault 类型成员的类合成默认构造函数。如果这样的类希望提供默认构造函数，就必须显式地定义，并且默认构造函数必须显式地初始化其 NoDefault 成员。
3. 类型不能用作动态分配数组的元素类型。
4. 类型的静态分配数组必须为每个元素提供一个显式的初始化式。
5. 如果有一个保存 NoDefault 对象的容器，例如 vector，就不能使用接受容器大小而没有同时提供一个元素初始化式的构造函数。

## 21
可以用单个实参来调用的构造函数定义了从形参类型到该类类型的一个隐式转换：
```c
class Sales_item {
  public:
    
    // default argument for book is the empty string
    Sales_item(const std::string &book = ""):
      isbn(book), units_sold(0), revenue(0.0) { }
    Sales_item(std::istream &is);
    // as before
};
```

## 22
友元声明将已命名的类或非成员函数引入到外围作用域中。此外，友元函数可以在类的内部定义，该函数的作用域扩展到包围该类定义的作用域。
```c
class X {
  friend class Y;
  friend void f() { /* ok to define friend function in the class body */ }
};

class Z {
  Y *ymem; // ok: declaration for class Y introduced by friend in X
  void g() { return ::f(); } // ok: declaration of f introduced by X
};
```
附：
友元是一种定义在类外部的普通函数或类，但它需要在类体内进行说明。
友元不是该类的成员，但可以访问类中的私有成员。
友元关系不具有对称性，也不具有传递性。

## 23
只有单个形参，而且该形参是对本类类型对象的引用（常用const修饰），这样的构造函数称为复制构造函数。与默认构造函数一样，复制构造函数可由编译器隐式调用。复制构造函数可用于：
1. 根据另一个同类型的对象显式或隐式初始化一个对象。
2. 复制一个对象，将它作为实参传给一个函数。
3. 从函数返回时复制一个对象。
4. 初始化顺序容器中的元素。
5. 根据元素初始化式列表初始化数组元素。

## 24
初始化的复制形式和直接形式有所不同：直接初始化直接调用与实参匹配的构造函数，复制初始化总是调用复制构造函数。复制初始化首先使用指定构造函数创建一个临时对象，然后用复制构造函数将那个临时对象复制到正在创建的对象：
```c
string null_book = "9-999-99999-9"; // copy-initialization
string dots(10, '.');               // direct-initialization
string empty_copy = string();       // copy-initialization
string empty_direct;                // direct-initialization
```
empty_copy和empty_direct的初始化都调用默认构造函数。对前者初始化时，默认构造函数函数创建一个临时对象，然后复制构造函数用该对象初始化 empty_copy。对后者初始化时，直接运行 empty_direct 的默认构造函数。

## 25
变量（如item）在超出作用域时应该自动撤销。动态分配的对象只有在指向该对象的指针被删除时才撤销。如果没有删除指向动态对象的指针，则不会运行该对象的析构函数，对象就一直存在，从而导致内存泄漏，而且，对象内部使用的任何资源也不会释放。当对象的引用或指针超出作用域时，不会运行析构函数。只有删除指向动态分配对象的指针或实际对象（而不是对象的引用）超出作用域时，才会运行析构函数。

## 26
派生类型必须对想要重定义的每个继承成员进行声明，派生类中虚函数的声明必须与基类中的定义方式完全匹配，但有一个例外：返回对基类型的引用（或指针）的虚函数。派生类中的虚函数可以返回基类函数所返回类型的派生类的引用（或指针）。Item_base 类可以定义返回 Item_base* 的虚函数，如果这样，Bulk_item 类中定义的实例可以定义为返回 Item_base* 或 Bulk_item*。

## 27
要触发动态绑定，满足两个条件：第一，只有指定为虚函数的成员函数才能进行动态绑定，成员函数默认为非虚函数，非虚函数不进行动态绑定；第二，必须通过基类类型的引用或指针进行函数调用。

## 28
对类所继承的成员的访问由基类中的成员访问级别和派生类派生列表中使用的访问标号共同控制。派生类可以进一步限制但不能放松对所继承的成员的访问。
派生类不能访问基类的 private 成员，也不能使自己的用户能够访问那些成员。如果基类成员为 public 或 protected，则派生列表中使用的访问标号决定该成员在派生类中的访问级别：
1. 如果是公用继承，基类成员保持自己的访问级别：基类的 public 成员为派生类的 public 成员，基类的 protected 成员为派生类的 protected 成员。
2. 如果是受保护继承，基类的 public 和 protected 成员在派生类中为 protected 成员。
3. 如果是私有继承，基类的的所有成员在派生类中为 private 成员。

## 29
向基类构造函数传递实参:
派生类构造函数的初始化列表只能初始化派生类的成员，不能直接初始化继承成员。但派生类构造函数可通过将基类包含在构造函数初始化列表中来间接初始化继承成员。
```c
class Bulk_item : public Item_base {
  public:
    Bulk_item(const std::string& book, double sales_price,std::size_t qty = 0, double disc_rate = 0.0):
      Item_base(book, sales_price), min_qty(qty), discount(disc_rate) { }
    // as before
};
```
这个构造函数使用有两个形参的Item_base 的构造函数初始化基类子对象，它将自己的 book 和 sales_price 实参传递给该构造函数。这个构造函数可以这样使用：
```c     
Bulk_item bulk("0-201-82470-1", 50, 5, .19);
```
要建立 bulk，首先运行 Item_base 构造函数，该构造函数使用从 Bulk_item 构造函数初始化列表传来的实参初始化 isbn 和 price。Item_base 构造函数执行完毕之后，再初始化 Bulk_item 的成员。最后，运行 Bulk_item 构造函数的（空）函数体。

## 30
虚析构函数：删除指向动态分配对象的指针时，需要在释放对象的内存之前运行析构函数清除对象。处理继承层次中的对象时，指针的静态类型可能与被删除对象的动态类型不同，可能会删除实际指向派生类对象的基类类型指针。如果删除基类指针，则需要运行基类析构函数并清除基类的成员，如果对象实际是派生类型的，则没有定义该行为。要保证运行适当的析构函数，基类中的析构函数必须为虚函数：
```c     
class Item_base {
  public:
    // no work, but virtual destructor needed
    // if base pointer that points to a derived object is ever deleted
    virtual ~Item_base() { }
};
```

## 31
构造函数和析构函数中的虚函数：构造派生类对象时首先运行基类构造函数初始化对象的基类部分。在执行基类构造函数时，对象的派生类部分是未初始化的。实际上，此时对象还不是一个派生类对象。撤销派生类对象时，首先撤销它的派生类部分，然后按照与构造顺序的逆序撤销它的基类部分。在这两种情况下，运行构造函数或析构函数的时候，对象都是不完整的。为了适应这种不完整，编译器将对象的类型视为在构造或析构期间发生了变化。在基类构造函数或析构函数中，将派生类对象当作基类类型对象对待。如果在构造函数或析构函数中调用虚函数，则运行的是为构造函数或析构函数自身类型定义的版本。

## 32
确定函数调用遵循以下四个步骤：
1. 首先确定进行函数调用的对象、引用或指针的静态类型。
2. 在该类中查找函数，如果找不到，就在直接基类中查找，如此循着类的继承链往上找，直到找到该函数或者查找完最后一个类。如果不能在类或其相关基类中找到该名字，则调用是错误的。
3. 一旦找到了该名字，就进行常规类型检查，查看如果给定找到的定义，该函数调用是否合法。
4. 假定函数调用合法，编译器就生成代码。如果函数是虚函数且通过引用或指针调用，则编译器生成代码以确定根据对象的动态类型运行哪个函数版本，否则，编译器生成代码直接调用函数

## 33
虚继承：一个类继承多个直接基类的时候，那些类有可能本身还共享另一个基类。在这种情况下，中间类可以选择使用虚继承，声明愿意与层次中虚继承同一基类的其他类共享虚基类。用这种方法，后代派生类中将只有一个共享虚基类的副本。
istream 和 ostream 类对它们的基类进行虚继承。通过使基类成为虚基类，istream 和 ostream 指定，如果其他类（如 iostream 同时继承它们两个，则派生类中只出现它们的公共基类的一个副本。通过在派生列表中包含关键字 virtual 设置虚基类：
```c
class istream : public virtual ios { ... };
class ostream : virtual public ios { ... };
    
// iostream inherits only one copy of its ios base class
class iostream: public istream, public ostream { ... };
```
即使基类是虚基类，也照常可以通过基类类型的指针或引用操纵派生类的对象。
1. 如果在每个路径中 X 表示同一虚基类成员，则没有二义性，因为共享该成员的单个实例。
2. 如果在某个路径中 X 是虚基类的成员，而在另一路径中 X 是后代派生类的成员，也没有二义性——特定派生类实例的优先级高于共享虚基类实例。
3. 如果沿每个继承路径 X 表示后代派生类的不同成员，则该成员的直接访问是二义性的。
像非虚多重继承层次一样，这种二义性最好用在派生类中提供覆盖实例的类来解决。
特殊的初始化语义：通常，每个类只初始化自己的直接基类。如果使用常规规则，就可能会多次初始化虚基类。类将沿着包含该虚基类的每个继承路径初始化。为了解决这个重复初始化问题，从具有虚基类的类继承的类对初始化进行特殊处理。在虚派生中，由最低层派生类的构造函数初始化虚基类。虽然由最低层派生类初始化虚基类，但是任何直接或间接继承虚基类的类一般也必须为该基类提供自己的初始化式。只要可以创建虚基类派生类类型的独立对象，该类就必须初始化自己的虚基类，这些初始化式只有创建中间类型的对象时使用。

## 34
通过运行时类型识别（RTTI），程序能够使用基类的指针或引用来检索这些指针或引用所指对象的实际派生类型。通过下面两个操作符提供 RTTI：
1. typeid操作符，返回指针或引用所指对象的实际类型。
2. dynamic_cast操作符，将基类类型的指针或引用安全地转换为派生类型的指针或引用。
这些操作符只为带有一个或多个虚函数的类返回动态类型信息，对于其他类型，返回静态（即编译时）类型的信息。对于带虚函数的类，在运行时执行 RTTI 操作符，但对于其他类型，在编译时计算 RTTI 操作符。
通常，从基类指针获得派生类行为最好的方法是通过虚函数。但是，在某些情况下，不可能使用虚函数。在这些情况下，RTTI 提供了可选的机制。然而，这种机制比使用虚函数更容易出错：程序员必须知道应该将对象强制转换为哪种类型，并且必须检查转换是否成功执行了。
