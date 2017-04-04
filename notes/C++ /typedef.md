# typedef

typedef int *p;
标识符p将被定义为了一个typedef name，这个typedef name表示一个类型，什么类型呢？就是int *p这个声明中标识符p的类型(int*)。

typedef double MYDOUBLE;  
分析:去掉typedef ，得到正常变量声明=> double MYDOUBLE;变量MYDOUBLE的类型为double;
MYDOUBLE d; // d是一个double类型的变量

typedef int* Func(int);
分析:去掉typedef  ，得到正常变量声明=> int* Func(int);
变量Func的类型为一个函数标识符，该函数返回值类型为int*，参数类型为int;
Func *fptr; // fptr是一个pointer to function with one int parameter, returning a pointer to int
Func f;   这样的声明意义就不大了。

typedef int (*PFunc)(int);
分析:去掉typedef，得到正常变量声明 int (*PFunc)(int);
变量PFunc的类型为一个函数指针，指向的返回值类型为int，参数类型为int的函数原型;

typedef int A[5];
分析:去掉typedef，得到正常变量声明int A[5];
变量A的类型为一个含有5个元素的整型数组；