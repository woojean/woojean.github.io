# 《C++ Primer 4 Answer Book》读书笔记
《C++ Primer 4 Answer Book》整理
第1章	快速入门
	【1】1.17 编写程序，要求用户输入一组数，输出信息说明其中有多少个负数
	答：#include<iostream>
		int main()
		{
			int amount=0,value;
			while(std::cin>>value) //读入数据直到遇见文件结束符
				if(value<=0)  ++amount;
			std::cout<<”Amount of all negative values read is”
				<<amount<<std::endl;
			return 0
}

第2章	变量和基本类型
	【2】2.1 int,long和short类型之间有什么差别？
	答：它们的最小存储空间不同，分别为16位、32位和16位。一般而言，short类型为半个机器字长，int类型为一个机器字长，而long类型为一个或两个机器字长（在32位机器中，int类型和long类型的字长通常是相同的）。因此，它们表示的范围不同；
	
	【3】2.3 如果在某机器上short类型占16位，那么可以赋给short类型的最大数是什么？unsigned short类型的最大数又是什么？
	答：可以赋给short类型的最大数是2^15-1，即32767；而unsigned short类型的最大数为2^16-1，即65535；

	【4】2.4当给16位的unsigned short对象赋值100000时，赋的值是什么？
	答：100000超过了16位的unsigned short类型的表示范围，编译器对其二进制表示截取低16位，相当于对65536求模，得34464；

	【5】2.5 float类型和double类型有什么差别？
	答：二者存储的位数不同（一般而言，float类型为32位，double类型为64位），因而取值范围不同，精度也不同（float类型只能保证6位有效数字，而double类型至少能保证10位有效数字）；
	
	【6】2.7 解释下列字面值常量的不同之处：L’a’,L”a”,012,0xC,3.14,3.14f,3.14L；-10u,-10e-2;
	答：L’a’为wchar_t型字面值；
L”a”为宽字符串字面值；
012为八进制表示的int型字面值；
0xC为十六进制表示的int型字面值；
3.14为double型字面值；
3.14f为float型字面值；
3.14L为long double型字面值；
		-10u为unsigned int型；
		-10e-2为double型；
	
	【7】2.12 区分左值和右值
	答：左值就是变量的地址，或者是一个代表“对象在内存中的位置”的表达式；
		右值就是变量的值；

	【8】2.16 假设calc是一个返回double对象的函数，下面哪些是非法定义？
		a) int car=1024,auto=2048;：非法，auto是关键字；
		b) int ival=ival; ：语法没有错，但是这个初始化没有实际意义，ival仍是未初始化的。
		c) std::cin>>int input_value;：非法，>>运算符后面不能进行变量定义。
		d) double salary=wage=9999.99：同一定义语句不同中不同变量的初始化应分别进行；
		e) double calc=calc();

	【9】2.17下列变量的初始值（如果有）是什么？
		std::String global_str;
		int global_int;
		int main(0
		{
			int local_int;
			std::String local_str;
			return 0;
		}
	答：global_str和local_str的初始值均为空字符串，global_int的初始值为0，local_int没有初始值；

	【10】2.18 解释下列例子中的name的意义：
		extern std::String name;  说明语句，说明std::String变量name在程序的其他地方定义；
		std::String name(“abc”);  定义语句，定义了std::String变量name，并将name初始化为”abc”;
		extern std::String name(“abc”); 定义语句，定义了std::String变量name，并将name初始化为”abc”.但是这个语句只能出现在函数外部（即，name）是一个全局变量）；

	【11】2.23 下列哪些语句合法？
		a) const int buf;  不合法，因为定义const变量（常量）时必须进行初始化；
		b)  int cnt=0;
			const int sz=cnt;  合法；
		c) 不合法，因为修改了const变量sz的值；
		d) int &rvall=1.01;  不合法，因为rvall是一个非const引用，非const引用不能绑定到右值，而1.01是一个右值；
	
【12】2.32 下列声明和定义哪些应该放在头文件中？哪些应该放在源文件中？
	a) int var;
	b) const double pi = 3.1416;
	c) extern int total=255;
	d) const double sq2=squt(2.0);
答：(a),(c),(d)应该放在源文件中，因为(a)和(c)是变量定义，定义通常应该放在源文件中。(d)中的const变量sq2不是常量表达式的初始化，所以也应该放在源文件中。
	(b)中const变量pi是用常量表达式初始化的，应该放在头文件中；


第3章	标准库类型
	【13】3.5 编写程序实现从标准输入每次读入一行文本，然后修改程序，每次读入一个单词；
	//从标准输入每次读入一行文本
	#include <iostream>
	#include <string>
	using namespace std;
	int main()
	{
		string line;
		//一次读入一行，直到遇见文件结束符
		while(getline(cin,line))
			cout<<line<<wndl;
		return 0;
	}
	
//从标准输入每次读入一个单词
string word；
	while(cin>>word)
		cout<<word<<endl;

【14】3.6解释string类型的输入操作符和getline函数分别如何处理空白字符串
答：string类型的输入操作符对空白字符串的处理：读取并忽略有效字符（非空白字符）之前所有的空白字符，然后读取字符直至再次遇到空白字符，读取终止（该空白字符仍留在输入流中）。
getline函数对空白字符的处理：不忽略行开头的空白字符，读取字符直至遇到换行符，读取终止并丢弃换行符（换行符从输入流中去掉但并不存储在string对象中）。

	【15】3.9下列程序实现什么功能？实现合法吗？
		string s;
		cout<<s[0]<<endl;
	答：实现不合法。因为s是一个空字符串，其长度为0，因此s[0]是无效的。

	【16】3.10 编一个程序，从string对象中去掉标点符号。要求输入到程序的字符串必须含有标点符号，输出结果则是去掉标点符号后的string对象。
	答：#include <iostream>
		#include<string>
		#include<cctype>
		using namespace std;
		
		int main()
		{
			string s,result_str;
			bool has_punct=false;
char ch;
			cout<<”Enter a string:”<<endl;
			getline(cin,s);
			
			for(string::size_type index =0 ;index !=s.size();++index)
			{
				ch=s[index];
				if(ispunct(ch))
					has_punct=true;
				else
					result_str+=ch;
			}
			if(has_punct)
				cout<<”Return:”<<endl<<result_str<<endl;
			else{
				cout<<”No punctuation character in the string!”<<endl;
				return 1;
			}
			return 0;
		}
	
	【17】3.12下列每个vector对象中元素个数是多少？各元素的值是什么？
		a) vector<int> ivec1; 元素个数为0；
		b) vector<int> ivec2(10); 元素个数为10，每个元素值均为0；
		c) vector<int> ivec3(10,42); 元素个数为10，每个元素个数均为42；
		d) vector<string> svec1; 元素个数为0；
		e) vector<string> svec2(10); 元素个数为10，各元素的值均为空字符串；
		f) vector<string> svec3(10,”hello”); 元素个数为10，各元素的值均为”hello”;
	
	【18】3.13 读一组整数到vector对象，并计算输出每对相邻元素的和。如果读入元素个数为奇数，则提示用户最后一个元素没有求和，并输出其值。
	答：#include <iostream>
		#include<vector>
		using namespace std;
		int main()
		{
			vector<int> ivec;
			int ival;
			cout<<”Enter numbers(Ctrl+z to end):”<<endl;
			while(cin>>ival){ivec.push_back(ival);}
			if(ivec.size()==0){
				cout<<”No element!”<<endl;
				return -1;
}
cout<<”Sum of each pair of adjacent elements in the vector:”<<endl;
for(vector<int>::size_type ix=0;ix<ivec.size()-1;ix=ix+2)
{
	cout<<ivec[ix]+ivec[ix+1]<<”\t”;
	if((ix+1)%6==0) cout<<endl;
}
if(ivec.size()%2!=0)
	cout<<endl
		<<”The last element is not been summed”
		<<”and its value is”
		<<ivec[ivec.size()-1]<<endl;
return 0;
		}

	【19】3.14 读入一段文本到vector对象，每个单词存储为vector中的一个元素。把vector对象中每个单词转化为大写字母，输出vector对象中转化后的元素，每8个单词为一行输出；
	#include <iostream>
	#include<string>
	#include<vector>
	#include<cctype>
	using namespace std;
	int main()
	{
		vector<string> svec;
		string str;
		cout<<”Enter text(Ctrl+z to end):”<<endl;
		while(cin>>str)
			svec.push_back(str);
		if(svec.size()==0)
		{
			cout<<”No string!”<<endl;
			return -1;
		}
		cout<<”Transformed elements from the vector:”<<endl;
		for(vector<string>::size_type ix=0;ix!=svec.size();++ix)
		{
			for(string::size_type index=0;index!=svec[ix].size();++index)
			{
				if(islower(svec[ix][index]))
					svec[ix][index]=toupper(svec[ix][index]);
			}
			cout<<svec[ix]<<” ”;
			if((ix+1)%8=0)	cout<<endl;
		}
		return 0;
	}

【20】3.15 下面程序合法吗？如果不合法，如何更正？
	vector<int> ivec;
	ivec[0]=42;
答：不合法。因为ivec是空的vector对象，其中不含任何元素，而下标操作只能用于获取已存在的元素。
	更正：将赋值语句改为ivec.push_back(42);

【21】3.16 列出五种定义vector对象的方法，给定10个元素，每个元素的值为42；
答：方法一：vector<int> ivec(10,42);
	方法二：vector<int> ivec(10);
			for(ix=0;ix<10;++ix) ivec[ix]=42;
	方法三：vector<int> ivec(10);
			for(vector<int>::iterator iter=ivec.begin();iter!=ivec.end();++iter)
			*iter=42;
	方法四：vector<int> ivec;
			for(int cnt=1;cnt<=10;++cnt)	
			ivec.push_back(42);
	方法五：vector<int> ivec;
			vector<int>::iterator iter=ivec.end();
			for(int i=0;i!=10;++i){
				ivec.insert(iter,42);
				iter=ivec.end();
			}


【22】3.18 编写程序来创建有10个元素的vector对象。用迭代器把每个元素值改为当前值的两倍；
#include <iostream>
#include<vector>
using namespace std;
int main()
{
	vector<int> ivec(10,20);
	for(vector<int>::iterator iter=ivec.begin();iter!=ivec.end();++iter)
		*iter=(*iter)*2;
	return 0;
}

	【23】3.21 何时使用const迭代器？又在何时使用const_iterator？解释两者的区别。
	答：const迭代器是迭代器常量，该迭代器本身的值不能修改，即该迭代器在定义时需要初始化，而且初始化后，不能再指向其他元素。若需要指向固定元素的迭代器，则可以使用const迭代器。
	const_iterator是一种迭代器类型，对这种类型的迭代器解引用会得到一个指向const对象的引用，即通过这种迭代器访问到的对象是常量。该对象不能修改，因此，const_iterator类型只能用于读取容器内的元素，不能修改元素的值。若只需遍历容器中的元素而无需修改它们，则可以使用const_iterator；

	3.22 如果采用下面的方法来计算mid会产生什么后果？
	vector<int>::iterator mid=(vi.begin()+vi.end())/2;
	将两个迭代器相加的操作是未定义的，因此用这种方法计算会出现编译错误。

第4章	数组指针
	【24】4.2 下列数组的值是什么？
		string sa[10];
		int ia[10];
		int main(){
			string sa2[10];
			int ia2[10];
		}
	答：sa和sa2为元素类型为string的数组，自动调用string类的默认构造函数将各元素初始化为空字符串；ia为在函数体外定义的内置数组，各元素初始化为0；ia2为在函数体内定义的内置数组，各元素未初始化，其值不确定。
	
	【25】4.3下列哪些定义是错误的？
		a)int ia[7]={0,1,1,2,3,3,4};
		b)vector<int> ivec ={0,1,1,2,3,3,4};  错误，vector对象不能用这种方式进行初始化；
		c)int ia2[]=ia;  错误，不能用一个数组来初始化另一个数组；
		d)int ia3[]=ivec;  错误，不能用vector对象来初始化数组。

	【26】4.4如何初始化数组的一部分或者全部元素？
	答：定义数组时可使用初始化列表（用花括号括住的一组以逗号分隔的元素初值）来初始化数组的部分或全部元素。如果是初始化全部元素，可以省略定义数组时方括号中给出的数组维数。如果指定了数组维数，则初始化列表提供的元素个数不能超过维数值。如果数组维数大于列出的元素初值个数，则只初始化前面的数组元素，剩下的其他元素，若是内置类型则初始化为0，若是类类型则调用该类的默认构造函数进行初始化。字符数组既可以用一组花括号括起来、逗号隔开的字面值进行初始化，也可以用一个字符串字面值进行初始化。
	
	【27】4.5 列出使用数组而不是vector的缺点。
	答：与vector类型相比，数组具有如下缺点：数组的长度是固定的，而且数组不提供获取其容量大小的size操作，也不提供自动添加元素的push_back操作。因此，程序员无法在程序运行时知道一个给定数组的长度，而且如果需要更改数组的长度，程序员只能创建一个更大的新数组，然后把原数组的所有元素复制到新数组的存储空间中去。与使用vector类型相比，使用内置数组的程序更容易出错且难以调试。

	【28】4.12 已知一指针p，你可以确定该指针是否指向一个有效的对象吗？如果可以，如何确定？如果不可以，请说明原因。
	答：无法确定某指针是否指向一个有效对象。因为，在C++中，无法检测指针是否未被初始化，也无法区分一个地址是有效地址还是由指针所分配的存储空间中存放的不确定值的二进制形成的地址。

	【29】4.13下列代码中，为什么第一个指针的初始化是合法的，而第二个则不合法？
		int i=42;
		void *p=&i;
		long *lp=&i;
	答：具有void*类型的指针可以保存任意类型对象的地址，因此p的初始化是合法的；而指向long型对象的指针不能用int型对象的地址来初始化，因此lp的初始化不合法。

	【30】4.15解释指针和引用的主要区别。
	答：使用引用和指针都可以间接访问另一个值，但它们之间存在两个重要区别：
1）	引用总是指向某个确定对象，定义引用时没有进行初始化会出现编译错误；
2）赋值行为上存在差异：给引用赋值修改的是该引用所关联的对象的值，而不是使该引用与另一个对象关联。引用一经初始化，就始终指向同一个特定对象。给指针赋值修改的是指针对象本身，也就是使该指针指向另一个对象，指针在不同时刻指向不同的对象（只要保证类型匹配）；
	
	【31】4.19解释下列5个定义的含义，指出其中哪些定义是非法的。
		a)int i; 合法
		b)const int ic; 非法，定义const对象时必须进行初始化，但ic没有初始化
		c)const int *pic; 合法，定义了指向int型const对象的指针pic
		d)int *const cpi;  非法，因为cpi被定义为指向int型对象的const指针，但该指针没有初始化
		e)const int *const cpic; 非法，因为cpic被定义为指向int型const对象的const指针，但该指针没有初始化
		f)int i=-1; const int ic=i;  合法，定义了一个int型const对象ic，并且用int型对象对其进行初始化
		g)const int *pic=&ic; 合法，定义了一个指向int型const对象的指针pic，并用ic的地址对其进行初始化
		h)int *const cpi=&ic; 不合法，cpi是一个指向int型对象的const指针，不能用const int型对象ic的地址对其进行初始化
		i)const int *const cpic=&ic; 合法，定义了一个指向int型const对象的指针cpic，并用ic的地址对其进行初始化
	
	【32】4.23 下列程序实现什么功能？
		const char aaa[]={'w','s','a','x'};
			const char *p=aaa;

			while(*p)
			{

				cout<<*p<<endl;
				++p;
			}
	答：该程序从数组aaa的起始地址开始输出一段内存中存放的字符，每行输出一个字符，直至存放0值（null）的字节为止。

	【33】4.24解释strcpy和strncpy的差别在哪里，各自的优缺点是什么？
	答：strcpy和strncpy的差别在于：前者复制整个指定的字符串，后者只复制指定字符串中指定数目的字符。strcpy比较简单，而使用strncpy可以适当地控制复制字符的数目，因此比strcpy更为安全。

	【34】4.26编写程序从标准输入设备读入一个string类型的字符串。考虑如何编程实现从标准输入设备读入一个C风格字符串。
	答：从标准输入设备读入一个string类型字符串的程序段：
		string str;
		cin>>str;
		从标准输入设备读入一个C风格的字符串可如下实现：
		const int str_size=80;
		char str[str_size];
		cin>>str;

	【35】4.32 编写程序用int型数组初始化vector对象
	答：#include<iostream>
		#include<vector>
		using namespace std;
		int main()
		{
			const size_t arr_size=8;
			int int_arr[arr_size];
			cout<<”Enter “<<arr_size<<”numbers:”<<endl;
			for(size_t ix=0;ix!=arr_size;++ix)
				cin>>int_arr[ix];
			vector<int> ivec(int_arr,int_arr+arr_size);
			return 0;
		}


第5章	表达式
	【36】5.4定义术语“溢出“的含义，并给出导致溢出的三个表达式
	答：溢出即表达式的求值结果超出了其类型的表示范围。
		如下表达式会导致溢出（假设int类型为16位:-32768~32767）：
		2000*1000
		32766+5
		3276*20
	
	【37】5.5解释逻辑与操作符、逻辑或操作符以及相等操作符的操作数在什么时候计算。
	答：逻辑与、或操作符采用称为“短路求值“的求值策略，即先计算左操作数，再计算右操作数，且只有当仅靠左操作数的值无法确定该逻辑运算的结果时，才会计算右操作数。
	相等操作符的左右操作数均需进行计算。
	
	【38】5.9假设有下面两个定义：
		unsigned long ul1=3,ul2=7;
		下列表达式的结果是什么：
		a) ul1&ul2 :3
        b) ul1&&ul2 :true
		c)ul1|ul2 :7
		d)ul1||ul2 :true

	【39】5.19 假设iter为vector<string>::iterator类型的变量，指出下面哪些表达式是合法的，并解释这些合法表达式的行为。
		a)*iter++; 合法，返回iter所指向的string对象，并使iter加1
		b)(*iter)++; 不合法
		c)*iter.empty(); 不合法
		d)iter->empty(); 合法，调用iter所指向的string对象的成员函数empty
		e)++*iter; 不合法
		f)iter++->empty(); 调用iter所指向的string对象的成员函数empty，并使iter加1.

	【40】5.33给定下列定义
		int ival;
		double dval;
		const string *ps;
		char *pc;
		void *pv;
		用命名的强制类型转换符号重写下列语句：
		a)pv=(void*)ps;
		b)ival=int(*pc);
		c)pv=&dval;
		d)pc=(char*)pv;
		答：a) pv=static_cast<void*>(const_cast<string*>(ps));
			b)ival=static_cast<int> (*pc);
			c)pv=static_cast<void*>(&dval);
			d)pc=static_cast<char*>(pv);

第6章	语句
	【41】6.10下面每段代码都暴露了一个常见的编程错误，请指出并修改之。
		a)switch(ival){
			case ‘a’:aCnt++;
			case ’e’:eCnt++;
			default:iouCnt++;
		}
		错误在于，各个case标号对应的语句块中缺少必要的break语句，从而当ival值为’a’时，aCnt、eCnt和iouCnt都会加1；ival值为’e’时，eCnt和iouCnt都会加1；

		b)switch(ival){
			case 1:
				int ix=get_value();
				ivec[ix]=ival;
				break;
			default:
				ix=ivec.size()-1;
				ivec[ix]=ival;
		}
		错误在于，在case 1标号之后，default标号之前定义了变量ix。因为，对于switch结构，只能在它的最后一个case标号或default标号后面定义变量，以避免出现代码跳过变量的定义和初始化的情况。
		
c)switch(ival){
			case 1,3,5,7,9:
				oddcnt++;
				break;
			case 2,4,6,8,10
				eventcnt++;	
				break;
		}
		错误在于，case标号中出现了多个值。因为一个case标号只能与一个值相关联。

		d)int ival=512, jval=1024, kval=4096;
		  int bufsize;
		  switch(swt){
			case ival:
				bufsize=ival*sizeof(int);
				break;
			case jval:
				bufsize=jval*sizeof(int);
				break;
			case kval:
				bufsize=kval*sizeof(int);
				break;	
		}
		错误在于，case标号中不能使用ival、jval和kval。因为case标号中的值只能使用常量表达式，而ival、jval和kval都是变量。
	
	【42】6.11 解释下面的循环，更正你发现的问题
		a)string bufString,word;
		  while(cin>>bufString>>word){…}
		每次读入两个string对象，直到遇到文件结束符；

		b)while(vector<int>::iterator iter!=ivec.end())
			{…}
		依次处理vector中的每个元素。此循环有错误，iter没有赋初值。
		c）while(ptr=0)
			ptr=find_a_value();
		调用find_a_value函数，讲返回值赋给ptr，直到ptr为0.此循环有错误，条件表达式中应使用比较操作符，而不是赋值操作符。
	
		d)while(bool status=find(word))
			{word=get_next_word();}
		  if(!status)
			cout<<Did not find any words\n”;
		每次调用get_next_word()获取一个word，然后调用find函数查找该word，直到找不到word为止。此循环有误，word没有赋初值。
	
	【43】6.12 编写一个小程序，从标准输入读入一系列string对象，寻找连续重复出现的单词。程序应该找出满足以下条件的单词的输入位置：该单词的后面紧跟着再次出现自己本身。跟踪重复次数最多的单词及其重复次数。输出重复次数的最大值，若没有单词重复则输出说明信息，例如输入是：how now now now brown cow cow 则输出应表明”now”这个单词出现了三次
	答：#include <iostream>
		#include<string>
		using namespace std;
		int main()
		{
			string preWord,currWord;
			string repWord;
			int currCnt=0,maxCnt=1;
			cout<<”Enter some words(Ctrl+z to end):”<<endl;
			while(cin>>currWord){
				if(currWord==preWord)  ++currCnt;
				else{
					if(currCnt>maxCnt){
						maxCnt=currCnt;
						repWord=preWord;
					}
					currCnt=1;
				}
				preWord=currWord;
			}
			if(maxCnt!=1)
				cout<<””<<repWord<<””
					<<”repeated for”<<maxCnt
					<<”times”<<endl;
			else
				cout<<”There is no repeated word.”<<endl;
			return 0;
		}
	
	【44】6.13详细解释下面语句是如何执行的
		*dest++ = *source++;
	答：执行过程如下，（1）指针dest加1；  （2）指针source加1；  （3）讲source原来指向的对象赋给dest原来指向的对象。
	
第7章	函数
	【45】7.1形参和实参有什么区别？
	答：形参在函数定义的形参表中进行定义，是一个变量，其作用域为整个函数。而实参出现在函数调用中，是一个表达式。进行函数调用时，用传递给函数的实参对形参进行初始化。

	【46】7.7解释下面两个形参声明的不同之处：
		void f(T);
		void f(T&);
	答：前者声明的事T类型的形参，在f中修改形参的值不会影响调用f时所传递的实参的值；
		后者声明的事T类型的引用形参。在f中修改形参的值实际上相当于修改调用f时所传递的实参的值。
	
	【47】7.8什么时候应该将形参定义为引用类型？
	答：如果希望通过函数调用修改实参的值，就应当将形参定义为引用类型；
		另外，在向函数传递大型对象时，为了避免复制实参以提高效率，以及使用无法复制的类类型（其复制构造函数为private的类类型）作为形参类型时，也应该将形参定义为引用类型。但这时使用形参的目的是为了避免复制实参，所以应该将形参定义为const引用。
	
	【48】7.9下面的程序虽然是合法的，但可用性还不够好，指出并改正该程序的局限。
		bool test(string& s){return s.empty();}
	答：其局限性在于，此处使用引用形参的唯一目的是避免复制实参，但没有将形参定义为const引用，从而导致不能使用字符串字面值来调用该函数（因为非const引用形参只能与完全相同类型的非const对象关联）。	
	
	【49】7.11何时应将引用形参定义为const对象？如果在需要const引用时将形参定义为普通引用，则会出现什么问题？
	答：如果使用引用形参的唯一目的是避免复制实参，则应将引用形参定义为const对象。
		如果在需要const引用时，将形参定义为普通引用，则会导致不能使用右值和const对象，以及需要进行类型转换的对象来调用该函数，从而不必要地限制了该函数的使用。

	【50】7.12什么时候应该使用指针形参，什么时候应该使用引用形参？解释两者的优点和缺点。
	答：当函数要处理数组且函数体不依赖于数组的长度时应使用指针形参，其他情况下应使用引用形参。
		指针形参的优点是可以明确地表示函数所操纵的是指向数组元素的指针，而不是数组本身，而且可以使用任意长度的实参数组来调用函数；其缺点是函数体不能依赖于数组的长度，否则容易造成数组内存的越界访问，从而产生错误的结果或者导致程序崩溃。
		引用形参的优点是在函数体中依赖数组的长度是安全的；其缺点是限制了可以传递的实参数组，只能使用长度匹配的实参数组来调用函数。
	
	【51】7.17什么时候返回引用是正确的？而什么时候返回const引用是正确的？
	答：返回指向函数调用之前已存在的对象的引用是正确的；
		当不希望返回的对象被修改时，返回const引用是正确的。

	【52】7.24如果有的话，指出下面哪些函数声明是错误的？为什么？
	a)int ff(int a,int b=0,int c=0);
	b)char *init(int ht=24,in wd,char bckgrnd);
	答：b)是错误的。因为在形参表中，具有默认实参的形参应该出现在形参表的末尾。

	【53】7.29 对于下面的声明和定义，你会将哪个放在头文件？哪个放在程序文本文件中呢？为什么？
	a)inline bool eq(const BigInt&,const BigInt&){…}
	b)void putValues(int *arr,int size);
	答：两者都应放在头文件中。
		b)是函数声明，适合放在头文件中；
		a)虽然是一个函数定义，但这是一个内联函数的定义，也应该放在头文件中。因为内联函数的定义对编译器而言必须是可见的，以便编译器在调用点内联展开该函数的代码，这样一来，仅有函数原型是不够的，而且内联函数有可能在程序中定义不止一次，这时必须保证在所有源文件中，其定义是完全相同的。把内联函数的定义放在头文件中，可以确保在调用函数时所使用的定义是相同的，并且保证在调用点该函数的定义对编译器是可见的。
	
	【54】7.35 下面提供了三组函数声明，解释每组中第二个声明的效果，并指出哪些（如果有的话）是不合法的。
	a) int calc(int,int);
	  int calc(const int,const int);
	  第二个声明将形参定义为const，这是对第一个声明的重复声明，因为对于非引用形参而言，是否定义为const没有本质区别；
	b)int get();
	  double get();
		第二个声明与第一个声明的区别仅仅在于返回类型不同，该声明是不合法的，因为不能仅仅基于不同的返回类型而实现函数重载。
	c)int *reset(int *);
	  double *reset(double *);
		第二个声明的效果是：声明了一个重载的reset函数。
	
	【55】7.39 解释以下每组声明中的第二个函数声明所造成的影响，并指出哪些不合法（如果有的话）
	a) int calc(int ,int);
	  int calc(const int&,const int&);
	b)int calc(char*,char*);
	  int calc(const char*,const char*);
	c)int calc(char*,char*)
	  int calc(char* const,char*const);
	答：a),b)中第二个声明的效果是：声明了一个重载的calc函数。
		c)中第二个声明是对第一个声明的重复声明。因为当形参以副本传递时，不能基于形参是否为const来实现函数重载。
	
	【56】7.40下面的函数调用是否合法？如果不合法，请解释原因。
		enum Stat{Fail,Pass};
		void test(Stat);
		test(0);
	答：该函数调用不合法。因为函数的形参为枚举类型Stat，函数调用的实参为int类型。枚举类型对象只能用同一枚举类型的另一对象或一个枚举成员进行初始化，因此不能将int类型的实参值传递给枚举类型的形参。

第8章	标准IO库
第9章	顺序容器
	【57】9.2 创建和初始化一个vector对象有四种方式，为每种方式提供一个例子，并解释每个例子生成的vector对象包含什么值。
	答：1）分配指定数目的元素，并对这些元素进行值初始化；
			vector<int> ivec(10); //ivec包含10个0值元素
		2）分配指定数目的元素，并将这些元素初始化为指定值：
			vector<int> ivec（10,1）;//ivec包含10个值为1的元素
		3）将vector对象初始化为一段元素的副本：
			int ia[10]={1,2,3,4,5,6,7,8,9,10};
			vector<int> ivec(ia,ia+10); //ivec包含10个元素，值分别为1~10；
		4）将一个vector对象初始化为另一个vector对象的副本：
			vector<int> ivec1（10,1）;
			vector<int> ivec2(ivec1);//ivec2包含10个值为1的元素
	
	【58】9.4定义一个list对象来存储deque对象，该deque对象存放int型元素。
	答：list< deque<int> > lst;
		注意必须用空格隔开两个相邻的>符号，否则系统会认为>>是单个符号，为右移操作符，从而导致编译时错误。

	【59】9.5为什么我们不可以使用容器来存储iostream对象？
	答：因为容器元素类型必须支持赋值操作及复制，而iostream类型不支持赋值和复制。
	
	【60】9.6假设有一个名为Foo的类，这个类没有定义默认构造函数，但提供了一个需要int型参数的构造函数。定义一个存放Foo的list对象，该对象有10个元素。
	答：list<Foo> fooList(10,1); //各元素均初始化为1
	
	【61】9.7下面的程序错在哪里？
	list<int> lst1;
	list<int>::iterator iter1=lst1.begin(),iter2=lst1.end();
	while(iter1<iter2){…}
	答：错误在于list容器的迭代器不支持关系操作，可更正为while(iter1!=iter2){…}
	
	【62】9.10下列迭代器的用法哪些（如果有）是错误的？
		const vector<int> ivec(10);
		vector<string> svec(10);
		list<int> ilist(10);
		a)vector<int>:;iterator it=ivec.begin();
			错误，因为返回的迭代器的类型为const vector<int>,不能用来对类型为vector<int>的迭代器it进行初始化。
		b)list<int>::iterator it=ilist.begin()+2;
			错误，因为list容器的迭代器不支持算术运算；
		c)vector<string>::iterator it=&svec[0];
		d)for(vector<string>::iterator it=svec.begin();it!=0;++it){…}
			错误，因为循环条件中迭代器it与0进行比较，导致运行时内存访问非法的错误，应该将it!=0改为it!=svec.end();
	
	【63】9.16 int型的vector容器应该使用什么类型的索引？
	答：int型的vector容器应该使用的索引类型为vector<int>::size_type
	
	【64】9.17读取存放string对象的list容器时，应该使用什么类型？
	答：list<string>::iterator和list<string>::const_iterator实现顺序读取；
		list<string>::reverse_iterator和list<string>::const_reverse_iterator实现逆序读取；

	【65】9.21假设c1和c2都是容器，下列用法给c1和c2的元素类型带来什么约束？
		if(c1<c2)
		(如果有的话)对c1和c2的约束又是什么？
		答：对c1和c2的元素类型的约束为：类型必须相同且都支持<操作。
			对c1和c2的约束为：类型必须相同且都支持<操作。

	【66】9.22已知容器vec存放了25个元素，那么vec.resize(100)操作实现了什么功能？若再做操作vec.resize(10)，实现的又是什么功能？
	答：vec.resize(100)操作使容器中包含100个元素：前25个元素保持原值，后75个元素采用值初始化。
		若再做操作vec.resize(10)，则使容器中包含10个元素，只保留前10个元素，后面的元素被删除。
	
	【67】9.23使用只带一个长度参数的resize操作对元素类型有什么要求（如果有的话）？
	答：因为只带有一个长度参数的resize操作对新添加的元素进行值初始化，所以元素类型如果是类类型，则该类必须显式提供默认构造函数，或者该类不显式提供任何构造函数以便使用编译器自动合成的默认构造函数。
	
	【68】9.27编写程序处理一个string类型的list容器，在该容器中寻找一个特殊值，如果找到，则将它删除。
	答：#include <iostream>
		#include<list>
		#include<string>
		using namespace std;

		int main()
		{
			list<string> alst;
			string str;	
			cout<<””Enter some strings:”<<endl;
			while(cin>>str)	slst.push_back(str);
			cin.clear();
			
			cout<<”Enter a string that you want to:”<<endl;
			cin>>str;
			for(list<string>:;iterator iter=slst.begin();iter!=slst.end();++iter)
			{
				if(*iter==str){
					iter=slst.erase(iter);
					--iter;
			}
			return 0;
		}
	注意，在删除元素后迭代器会失效，因此一定要对迭代器重新赋值。另外，erase函数返回一个迭代器，指向被删除元素的下一个元素。因为在for语句头中要对迭代器加1，所以在it语句中将迭代器减1，以免遗漏需处理的元素。
	
	【69】9.29解释vector的容量和长度之间的区别。为什么在连续存储元素的容器中需要支持“容量“的概念？而非连续的容器，如list，则不需要？
	答：vector的容量是指容器在必须分配新存储空间之前可以存储的元素总数，而长度是指容器当前拥有的元素个数。
	对于连续存储元素的容器而言，容器中的元素是连续存储的。当在容器内添加一个元素时，如果容器中已经没有空间容纳新的元素，则为了保持元素的连续性存储必须重新分配存储空间，用来存放原来的元素以及新添加的元素。首先将存放在旧存储空间中的元素复制到新存储空间里，接着插入新元素，最后撤销旧的存储空间。如果在每次添加新元素时，都要这样分配和撤销内存空间，其性能将会很慢。为了提高性能，连续存储元素的容器实际分配的容量要比当前所需的空间多一些，预留了一些额外的存储区，用于存放新添加的元素，使得不必为每个新元素重新分配容器。所以，在连续存储元素的容器中需要支持“容量“的概念。
	而对于不连续存储元素的容器，不存在这样的内存分配问题。例如，在list容器中添加一个元素，标准库只需创建一个新元素，然后将该新元素连接到已存在的链表中，不需要重新分配存储空间，也不必复制任何已存在的元素。所以，这类容器不需要支持“容量“的概念。
	
	【70】9.33对于下列程序任务，采用哪种容器实现最合适？
	a)从一个文件中读入未知数目的单词，以生成英文句子；
		因为单词数量未知，且需要以非确定的顺序处理这些单词，所以采用vector实现最合适，因为vector支持随机访问。
	b)读入固定数目的单词，在输入时将它们按字母顺序插入到容器中。
		采用list实现最合适，因为需要在容器的任意位置插入元素
	c)读入未知数目的单词，总是在容器尾部插入新单词，从容器首部删除下一个值。
		采用deque实现最合适，因为总是在容器尾部插入新单词，从容器首部删除下一个值
	d)从一个文件中读入未知数目的整数。对于这些整数排序，然后把它们输出到标准输出设备。
		如果一边输入一边排序，则采用list实现最合适。因为在读入时需要在容器的任意位置插入元素（从而实现排序）；如果先读入所有整数，再进行排序，则采用vector最合适，因为进行排序最好有随机访问能力。
	
	【71】9.36如何用vector<char>容器初始化string对象
	答：vector<char> cvec(10,’a’);
		string str(cvec.begin(),cvec.end());
		
	
第10章	关联容器
	【72】10.3描述关联容器和顺序容器的差别
	答：关联容器和顺序容器的本质差别在于：关联容器通过键（key）存储和读取元素，而顺序容器则通过元素在容器中的位置顺序存储和访问元素。
	
	【73】10.6可否定义一个map对象以vector<int>::iterator为键关联int型对象？如果以list<int>::iterator关联int型对象呢？或者，以pair<int,string>关联int?对于每种情况，如果不允许，请解释其原因。
	答：可以定义map对象以vector<int>::iterator和pair<int,string>为键关联int型对象。
		不能定义map对象以list<int>::iterator为键关联int型对象。因为键类型必须支持<操作，而list容器的迭代器类型不支持<操作。

	【74】10.7对于以int型对象为索引关联vector<int>型对象的map容器，它的mapped_type、key_type和value_type分别是什么？
	答：分别是，vector<int>、int和pair< const int,vector<int> >

	【75】10.8编写一个表达式，使用map的迭代器给其元素赋值。
	答：假设map的迭代器为iter，要赋给元素的值为val，则可以用iter->second=val;语句给map的元素赋值。（注意，键是不能修改的，所以只能给值成员赋值。）
	
	【76】10.10解释下面程序的功能：
		map<int,int> m;
		m[0]=1;
		比较上一程序和下面程序的行为：
		vector<int> v;
		v[0]=1;
		答：程序段map<int,int> m;  m[0]=1; 的功能是：首先创建一个空map容器m，然后在m中增加一个键为0的元素，并将其赋值为1.而程序段vector<int> v; v[0]=1;将出现运行时错误。因为vector容器v为空，其中下标为0的元素并不存在。

	【77】10.11哪些类型可用作map容器对象的下标？下标操作符返回的又是什么类型？给出一个具体例子说明，即定义一个map对象，指出哪些类型可用作其下标，以及下标操作符返回的类型。
	答：可用作map容器对象的下标的类型必须是支持<操作的类型；
		下标操作符返回的类型为map容器中定义的mapped_type类型。例如，对于如下定义的对象：
		map<string,int> wordCount;
		可用作其下标的类型为string以及C风格的字符串类型（包括字面值、数组名和指针）。下标操作符返回的类型为int；
	
	【78】10.14map容器的count和find运算有何区别？
	答：前者返回map容器中给定键k的出现次数，其返回值只能是0或1；后者在map容器中存在按给定键k索引的元素的情况下，返回指向该元素的迭代器，否则返回超出末端迭代器。
	
	【79】10.21解释map和set容器的差别，以及它们各自适用的情况。
	答：map容器和set容器的差别在于：map容器是键值对的集合，而set容器只是键的集合；map类型适用于需要了解键与值的对应的情况，例如字典，而set类型适用于只需要判断某值是否存在的情况，例如判断某人的名字是否在黑名单中。
	
	【80】10.22解释set和list容器的差别，以及它们各自适用的情况。
	答：set容器和list容器的主要差别在于：set容器中的元素不能修改，而list容器中的元素无此限制。set容器适用于保存元素值不变的集合，而list容器适用于保存会发生变化的元素。
	
第11章	泛型算法
【81】11.10标准库定义了一个find_if函数，与find一样，find_if函数带有一对迭代器形参，指定其操作的范围，与count_if一样，该函数还带有第三个形参，表明用于检查范围内每个元素的谓词函数。find_if返回一个迭代器，指向第一个使用谓词函数返回非零值的元素，如果这样的元素不存在，则返回第二个迭代器实参。使用find_if函数实现统计长度大于6的单词个数的程序部分；
答：vector<string>::iterator iter=words.begin();
	vector<string>::size_type wc=0;
	while((iter=find_if(iter,words.end(),GT6))!=words.end())
	{
		++wc;
		++iter;
	}

	【82】11.11你认为为什么算法不改变容器的大小？
	答：为了使得算法能够独立于容器，从而普适性更好，真正成为“泛型”算法，而且算法的概念更为清晰，设计也更为简单。
	
	【83】11.13解释三种插入迭代器的区别。
	答：三种插入迭代器的区别在于插入元素的位置不同：
		back_inserter,使用push_back实现在容器末端插入；
		front_inserter,使用push_front实现在容器前端插入；
		inserter,使用insert实现在容器中指定位置插入。
		因此，除了所关联的容器外，inserter还带有第三个实参——指向插入起始位置的迭代器。

第12章 类
	【84】12.27 下面的陈述中哪个是不正确的？为什么？
		a)类必须提供至少一个构造函数
		不正确，因为类也可以不提供构造函数，这时使用由编译器合成的默认构造函数；
		b)默认构造函数的形参列表中没有形参
		不正确，因为为所有形参都提供了默认实参的构造函数也定义了默认构造函数，而这样的构造函数形参列表中是有形参的；
		c)如果一个类没有有意义的默认值，则该类不应该提供默认构造函数
		不正确，因为如果一个类没有默认构造函数（指的是该类提供了构造函数，但是没有提供自己的默认构造函数），则在编译器需要隐式使用默认构造函数的环境中，该类就不能使用。所以，如果一个类定义了其他构造函数，则通常也应该提供一个默认构造函数；
		d)如果一个类没有定义默认构造函数，则编译器会自动生成一个，同时将每个数据成员初始化为相关类型的默认值。
		不正确，因为编译器合成的默认构造函数不是将每个数据成员初始化为相关类型的默认值，而是使用与变量初始化相同的规则来初始化成员：类类型的成员执行各自的默认构造函数进行初始化；内置和复合类型的成员，只对定义在全局作用域中的对象才初始化；

	【85】12.28将构造函数设置为explicit的好处是什么？缺点是什么？
	答：好处是可以避免因隐式类型转换而带来的错误；缺点是当用户的确需要进行相应的类型转换时，不能依靠隐式类型转换，必须显示地创建临时对象。

	【86】12.29解释在下面的定义中所发生的操作。
	string null_isbn=”0-999 9999 9”;
	首先调用接受一个C风格字符串形参的string构造函数，创建一个临时的string对象，然后调用string类的复制构造函数将null_isbn初始化为该临时对象的副本。
	Sales_item null(null_isbn);
	使用string对象null_isbn为实参，调用Sales_item类的构造函数创建Sales_item对象null。
	Sales_item null(“0-999 9999 9”);
	首先调用接受一个C风格字符串形参的string构造函数，创建一个临时的string对象，然后使用该临时对象为实参，调用Sales_item类的构造函数创建Sales_item对象null。
	
	【87】12.30编译如下代码：
		f(const_vector<int>&);
		int main(){
			vector<int> v2;
			f(v2); //ok!
			f(42); //error!
			return 0;
		}
		基于对f的第二个调用中出现的错误，我们可以对vector构造函数作出什么推断？如果该调用成功了，那么你能得出什么结论？
	答：可以用单个实参调用的构造函数定义从形参类型到该类类型的隐式转换，如果这样的构造函数被声明为explicit，则编译器不使用它作为转换操作符。函数f的形参为const vector<int>&类型，如果实参为vector<int>类型或能够隐式转换为vector<int>类型，则函数调用成功。因此，基于对f的第二个调用中出现的错误，我们可以对vector构造函数作出如下推断：vector中没有定义接受一个int型参数的构造函数，或者即使定义了接受一个int型参数的构造函数，该构造函数也被设置为explicit。
	如果该调用成功了，则说明vector中定义了接受一个int型参数的非explicit构造函数。

	【88】12.42 下面的static数据声明和定义中哪些是错误的？
		//example.h
		class Example{
		public:
			static double rate=6.5;
			static const int vecSize = 20;
			static vector<double> vec(vecSize);
		};
		
		//example.c
		#include “example.h”
		double Example::rate;
		vector<double> Example::vec;
	答：类的定义体中对static成员rate和vec的初始化是错误的。因为非const static成员的初始化必须放在定义体的外部。example.c文件中对static成员rate和vec的定义也是错误的，因为此处必须给出初始值。可更正为：
		//example.h
		class Example{
		public:
			static double rate;
			static const int vecSize = 20;
			static vector<double> vec;
		};
		
		//example.c
		#include “example.h”
		double Example::rate=6.5;
//const int Example::vecSize;
		vector<double> Example::vec(vecSize);
	
	
第13章 复制控制
	【89】13.1什么是复制构造函数？何时使用它？
	答：复制构造函数是具有如下特点的构造函数：只有单个形参，且形参是对本类类型对象的引用（常用const修饰）
	复制构造函数在下列情况下使用：
	根据另一个同类型的对象显式或隐式初始化一个对象。
	复制一个对象，将它作为实参传给一个函数。
	从函数返回时复制一个对象。
	初始化顺序容器中的元素。
	根据元素初始化式列表初始化数组元素。

	【90】13.2下面的第二个初始化不能编译。可以从vector的定义中得出什么推断？
		vector<int> v1(42);
		vector<int>v2=42; //error!
	答：vector容器类没有提供公有的复制构造函数。因为第二个初始化是复制初始化，创建v2时，编译器首先调用接受一个int型形参的vector构造函数，创建一个临时vector对象，然后，编译器需要使用vector复制构造函数将v2初始化为该临时vector对象的副本。
	
	【91】13.5哪个类定义可能需要一个复制构造函数？
		a)包含4个float成员的Point3w类
			不需要，因为该类中的数据成员都是内置类型的，没有指针成员，使用编译器提供的复制构造函数即可；
		b)Matrix类，其中，实际矩阵在构造函数中动态分配，在析构函数中删除；
			需要，因为需要涉及指针及内存的动态分配；
		c)PayRoll类，在这个类中为每个对象提供唯一ID。
			需要，因为在根据已存在的PayRoll对象创建其副本时，需要提供唯一的ID；
		d)Word类，包含一个string和一个以行列位置对为元素的vector。
			不需要，因为编译器会自动为其数据成员调用string和vector的复制构造函数；
	答：一般而言，如果一个类拥有指针成员，或者存在复制对象时有一些特定工作要做，则该类需要复制构造函数。
	
	【92】13.6复制构造函数的形参并不限制为const，但必须是一个引用。解释这个限制的基本原理，例如，解释为什么下面的定义不能工作？
	Sales_item::Sales_item(const Sales_item chs);
	答：上述定义之所以不能工作，是因为它试图以传值方式将实参传递给一个复制构造函数。但是，每当以传值方式传递参数时，会导致调用复制构造函数，因此，如果要使用以传值方式传递参数的复制构造函数，必须使用一个“不以传值方式传递参数“的复制构造函数，否则，就会导致复制构造函数的无穷递归调用。所以，复制构造函数的形参必须是一个引用，即以传址方式传递参数。

	【93】13.7类合适需要定义赋值操作符？
	答：一般而言，如果一个类需要定义复制构造函数，则该类也需要定义赋值操作符。具体而言，如果一个类中包含指针型数据成员，或者在进行赋值操作时有一些特定工作要做，则该类通常需要定义赋值操作符。
	
	【94】13.11什么是析构函数？合成析构函数有什么用？什么时候会合成析构函数？什么时候一个类必须定义自己的析构函数？
	答：析构函数是特殊的成员函数，其名字是在类名字前加上一个~。该函数没有返回值和形参，用于对象超出作用域或需要删除对象时来清除对象。
		合成析构函数的作用：按对象创建时的逆序撤销每个非static成员。对于类类型的成员，合成析构函数调用该成员的析构函数来撤销对象。
		编译器会为每个类合成析构函数。如果有些工作（如释放资源、执行特定操作等）需要析构函数完成，一个类就必须定义自己的析构函数。

	
第14章	重载操作符与转换
	【95】14.1在什么情况下重载操作符与内置操作符不同？在什么情况下重载操作符与内置操作符相同？
	答：重载操作符与内置操作符的不同之处在于：重载操作符必须具有至少一个类类型或枚举类型的操作数；重载操作符不保证操作数的求值顺序，例如，&&和||的重载版本就失去了“短路求值“特性，两个操作数都要进行求值，而且不规定操作数的求值顺序。
	重载操作符与内置操作符的相同之处在于：操作符的优先级、结合性及操作数数目均相同。
	
	【96】14.2为Sales_item编写输入、输出，加以及复合赋值操作符的重载声明
	答：Class Sales_item{
			friend std::istream& operator>> (std::istream&,Sales_item&);
			friend std::ostream& operator<< (std::ostream&,const Sales_item&);
		public:
			Sales_item& operator+=(const Sales_item&);
		};
		Sales_item operator+(const Sales_item&,const Sales_item&);
	其中，复合赋值操作符定义为public成员，输入和输出操作符需要访问Sales_item类成员，所以需定义为Sales_item类的友元。加操作符可以用public成员+=来实现，所以无需定义为Sales_item类的友元。

	【97】14.5列出必须定义为类成员的操作符
	答：赋值=、下标[]、调用()、成员访问箭头->等操作符必须定义为类成员；
	
	【98】14.6解释下面操作符是否应该为类成员，为什么？
	+、--、++、>、<<、&&、==、()
	答：+、<<和==操作符通常应定义为非成员函数，但<<操作符通常需要访问类的数据成员，所以一般应指定为类的友元；
	--和++会改变对象的状态，通常应定义为类成员；
	->和()必须定义为类成员，否则会出现编译错误；
	&&一般对类类型操作数没有意义，通常不进行重载；如果一定要重载，可重载为非成员函数；

	【99】14.8定义Date类的输出操作符
	答： ostream& operator << (ostream& out,const Date d)
		{
			out<<d.year<<”/”<<d,month<<”/”<<d,day;
			return out;
		}
	注意，应将<<操作符指定为Date类的友元。
	
	【100】14.14定义一个赋值操作符，将isbn复制给Sales_item对象
	答：Sales_item& Sales_item::operator(const string& str)
		{
			isbn=str;
			return *this;
		}
		注意，赋值操作符必须定义为类的成员函数，且一般应返回左操作数的引用。
	
	【101】14.17为CheckoutRecord类定义一个下标操作符，从等待列表中返回一个名字。
	答：pair<string,string>& CheckoutRecord::operator[](const vector< pair<string,string>* >::size_type index)
		{
			return *wait_list.at(index);  //使用at可检查下标是否越界
		}
		
		const pair<string,string>& CheckoutRecord::operator[](const vector< pair<string,string>* >::size_type index) const
		{
			return *wait_list.at(index);
		}
		注意，下标操作符必须定义为类成员函数，且返回引用以便可以用在赋值操作符的任意一边。类定义下标操作符时，一般需定义两个版本，即返回引用的非const成员及返回const引用的const成员，以便可以对const和非const对象使用下标；可以对下标是否越界进行检查（这与内置下标操作符的语义有所不同），以避免对内存的非法访问。

	【102】14.21定义一个类，该类保存一个指向ScreenPtr的指针。为该类定义一个重载的箭头操作符。
	答：class NoName{
		public:
			NoName(Screen *p):ptr(new ScreenPtr(p)){}
			ScreenPtr operator->()
			{
				return *ptr;
			}
			const ScreenPtr operator->() const
			{
				return *ptr;
			}

		private:
			ScreenPtr *ptr;
		};
		注意，箭头操作符必须定义为类成员函数，重载的箭头操作符不接受显式形参，且必须返回指向类类型的指针，或者返回定义了箭头操作符的类类型对象；需要定义箭头操作符的const和非const版本，以便可以对const和非const对象使用箭头操作符。
	
	【103】14.41解释这两个转换操作符之间的不同
	class integral{
	public:
		operator const int() const;
		operator int() const;
	}
	这两个转换操作符是否太严格了？如果是，怎样使得转换更通用一些？
	答：这两个转换操作符之间的不同之处在于：前者将对象转换为const int值（int型const变量），而后者将对象转换为int值（int型变量）；前者太严格了，只能用于可以使用const int值的地方，将前者去掉只保留后者，即可使得转换更为适用。（事实上，如果这两个转换操作符同时存在，则在既可使用int型const变量又可使用int型变量的情况下，会因编译器无法作出抉择而产生错误）。
	
	【104】14.44为下述每个初始化列出可能的类类型转换序列。每个初始化的结果是什么？
	class LongDouble{
	public:
		operator double();
		operator float();
	};
	LongDouble idObj;
	a)int ex1=idObj;    b)float ex2=idObj;
	答：a)有二义性，因为既可以先使用从LongDouble到double的转换操作，再使用从double到int的标准转换，也可以先使用从LongDouble到float的转换操作，再使用从float到int的标准转换，二者没有优劣之分。
	b)使用从LongDouble到float的转换操作，将idObj对象转换为float值用于初始化ex2；

	【105】14.45哪个calc()函数是如下函数调用的最佳可行函数？列出调用每个函数所需的转换序列，并解释为什么所选定的就是最佳可行函数？
	class LongDouble{
	public:
		LongDouble(double);
		……
	};
	void calc(int);
	void calc(LongDouble);
	double dval;
	calc(dval); //which function?
	答：最佳可行函数为void calc(int)。调用void calc(int)所需的转换为：将实参dval由double类型转换为int类型（标准转换）；
	调用void calc(LongDouble所需的转换为：将实参dval有double类型转换为LongDouble类型（使用LongDouble类的构造函数，为类类型转换）；
	因为标准转换优于类类型转换，所以void calc(int)为最佳可行函数。


第15章	面向对象编程
	【106】15.1哪些成员函数可以定义为虚成员？
	答：除了构造函数外，任意非static成员函数都可以为虚成员；

	【107】15.16对于下面的基类定义：	
		struct Base{
			Base(int val):id(val){}
		protected:
			int id;
		}
		解释为什么下述每个构造函数是非法的。
		a) struct C1:public Base{
			C1(int val):id(val){}
		  };
		没有在初始化列表中向基类构造函数传递实参；
		b) struct C2:public C1{
			C2(int val):Base(val),C1(val){}
		  };
		初始化列表中出现了非直接基类Base；
		c) struct C3:public C1{
			C3(int val):Base(val){}
		  };
		初始化列表中出现了非直接基类Base而没有出现直接基类C1；
		d)struct C4:public Base{
			C4(int val):Base(id+val){}
		  };
		初始化列表中使用了未定义的变量id；
		e) struct C5:public Base{
			C5(){}
		   };	
		缺少初始化列表：Base类没有默认构造函数，其派生类必须用初始化列表的构造函数传递实参；
	
	【108】15.17说明在什么情况下类应该具有虚析构函数？
	答：作为基类使用的类应该具有析构函数，以保证在删除（指向动态分配对象的）基类指针时，根据指针实际指向的对象所属的类型运行适当的析构函数。
	
	【109】15.23对于下面的基类和派生类定义：
		struct Base{
			foo(int);
		protected:
			int bar;
			double foo_bar;
		};
		struct Derived:public Base{
			foo(string);
			bool bar(Base *pb);
			void foobar();
		protected:
			string bar;
		};
	找出下述每个例子中的错误并说明怎样改正：
	a)Derived d;d.foo(1024);
	调用foo函数所给定的实参类型错误。通过Derived类对象d调用foo函数，调用到的是Derived类中定义的foo函数，应使用string类型的实参。
	b)void Derived::foobar(){ bar =1024;}
	用int型值1024对bar进行赋值错误。在Derived类中定义的数据成员bar屏蔽了基类Base中的同名成员，所以此处访问到的是Derived类中定义的bar，应赋以string类型的对象或C风格字符串。
	c)bool Derived::bar(Base *pb)
		{ return foo_bar==pb->foo_bar;}
	通过指向Base类对象的指针访问其受保护成员foo_bar错误。可改正为将pb定义为指向Derived类对象的指针。
	
	【110】15.25假定Derived继承Base，并且Base将下面的函数定义为虚函数；假定Derived打算定义自己的这个虚函数版本，确定在Derived中哪个声明是错误的，并指出为什么错。
	a)Base* Base::copy(Base*);
	  Base* Derived::copy(Derived*);
		单纯从语法上看并没有错，但Derived中声明的copy是一个非虚函数，而不是对Base中声明的虚函数copy的重定义，因为派生类中重定义了的虚函数必须具有与基类中虚函数相同的原型（唯一的例外是返回类型可以稍有不同）。而且Derived中定义的copy函数还屏蔽了基类Base的copy函数。
	b)Base* Base::copy(Base*);
	  Derived* Derived::copy(Base*);
	c)ostream& Base::print(int,ostream&=cout);
	  ostream& Derived::print(int,ostream&);
	d)void Base::eval() const;
	  void Derived::eval();
	
第16章	模板与泛型编程
	【111】16.2 编写一个函数模板，接受一个ostream引用和一个值，将该值写入流。用至少4种不同类型调用函数，通过写至cout、文件和stringstream来测试你的程序。
	答：#include <iostream>
		#include<string>
		#include<fstream>
		#include<sstream>
		using namespace std;
	
		template <typename T1,typename T2>
		T1& print(T1& s,T2 val)
		{
			s<<val;
			return s;
		}

		int main()
		{
			double dval=0.88;
			float fval=-12.3;
			string oristr=”this is a test”,desstr;
			ostringstream oss(desstr);
			ofstream outFile(“result.dat”);	

			//写至cout
			print(cout,-3)<<endl;
			print(cout,dval)<<endl;
			print(cout,fval)<<endl;
			print(cout,oristr)<<endl;
			
			//写至文件
			print(outFile,-3)<<endl;
			print(outFile,dval)<<endl;
			print(outFile,fval)<<endl;
			print(outFile,oristr)<<endl;
			outFile.close();

			//写至stringstream
			print(oss,-3)<<endl;
			print(oss,dval)<<endl;
			print(oss,fval)<<endl;
			print(oss,oristr)<<endl;
			
			//将stringstream中的字符输出到cout以进行验证
			cout<<oss.str()<<endl;
			return 0;
		}
			
	【112】16.3 当调用两个string对象的compare时，传递用字符串字面值初始化的两个string对象，如果编写以下代码会发生什么？
	compare(“hi”,”world”);
	答：该代码会出现编译错误。因为根据第一个实参”hi”可将模板形参T推断为char[3]，而根据第二个实参”world”可将模板形参推断为char[6]，T被推断为两个不同的类型，所以编译器无法使用函数模板compare进行适当的实例化以满足需求。
		
	【113】16.7解释下面每个函数模板的定义并指出是否有非法的。
	a)template <class T,U,  typename V> void f1(T,U,V);
		非法，模板类型形参前必须带有关键字typename或class，模板非类型形参前必须带有类型名，而这里U作为f1的形参类型使用，应该是一个类型形参，所以应在模板形参表中U的前面加上class或者typename；
	b)template<class T>T f2(int &T);
		非法，如果单纯从函数模板定义的语法来看，该定义是合法的。但是，模板形参T没有作为类型在模板函数的形参表中出现，因此将无法对其进行模板实参推断，所以，该模板函数的定义是错误的；
	c)inline template<class T> T foo(T,unsigned int*);
		非法，inline不能放在关键字template之前，应放在模板形参表之后，函数返回类型之前；
	d)template<class T> f4(T,T);
		在标准C++中非法；没有指定函数f4的返回类型。
	e)typedef char Ctype;
	 template <typename Ctype> Ctype f5(Ctype a);
		合法。定义了一个模板函数f5，该函数的返回类型与形参类型相同，均可绑定到任意类型。
	
	【114】16.10声明为typename的类型形参与声明为class的类型形参有区别吗？区别在哪里？
	答：在标准C++中，声明为typename的类型形参与声明为class的类型形参没有区别。但是，标准C++之前的系统有可能只支持使用关键字class来声明模板类型形参。
	
	【115】16.11何时必须使用typename？
	答：如果要在函数模板内部使用在类中定义的类型成员，必须在该成员名前加上关键字typename，以告知编译器将该成员当作类型。
	
	【116】16.15编写可以确定数组长度的函数模板
	答：可以使用非类型模板形参编写如下函数模板
		template <typename T,std::size_t N>
		std::size_t size(T (&arr)[N])
		{
			return N;
		}
	
	【117】16.20在模板实参推断期间发生什么？
	答：根据函数调用中给出的实参确定模板实参的类型和值；

	【118】16.21指出对模板实参推断中涉及的函数实参允许的类型转换。
	答：const转换：接受const引用或const指针的函数可以分别用非const对象的引用或指针来调用，无需产生新的实例化。如果函数接受非引用类型，形参类型和实参都忽略const，即无论传递const或非const对象给接受非引用类型的函数，都使用相同的实例化。
		数组或函数到指针的转换：如果模板形参不是引用类型，则对数组或函数类型的实参应用常规指针转换。数组实参将当做指向其第一个元素的指针，函数实参当做指向函数类型的指针。

	【119】16.22对于下面的模板：
		template <class Type>
		Typc calc(const Type* array,int size);
		
		template <class Type>
		Type fcn(Type p1,Type p2);
		下面这些调用有错吗？
		double dobj; float fobj; char cobj;
		int ai[5]={22,13,4,12,24};
		a) calc(cobj,’c’);
			实参cobj的类型为char，但是，不能使用函数模板calc产生第一个形参为非指针类型的函数实例；
		b) calc(dobj,fobj);
			实参dobj的类型为double，但是，不能使用函数模板calc产生第一个形参为非指针类型的函数实例；
		c) fcn(ai,cobj);
			函数模板fcn中两个形参的类型必须是相同的，而函数调用fcn(ai,cobj)中给出的两个实参类型不同，不能进行实例化。
	
	【120】16.23标准库函数max接受单个类型形参，可以传递int和double对象调用max吗？
	答：可以，只需使用强制类型转换将其中一个对象转换为int或double类型，使其与另一对象类型相同即可。
	
	【121】16.24对于具有单个模板类型形参的compare版本，传给它的实参必须完全匹配，如果想要用兼容类型如int和short调用该函数，可以使用显式模板实参指定int或short作为形参类型。编写程序使用具有一个模板形参的compare版本，使用允许你传递int和short类型实参的显式模板实参调用compare。
	答：#include <iostream>
		using namespace std;
		template <typename T>
		int compare(const T& v1,const T& v2)
		{
			if(va<v2) return -1;
			if(v2<v1) return 1;
			return 0;
		}
		
		int main()
		{
			short sval =123;
			int ival=1024;
			cout<<compare(static_cast<int>(sval),ival)<<endl;
			cout<<compare(sval,static_cast<short>(ival))<<endl;
			cout<<compare<short>(eval,ival)<<endl;
			cout<<compare<int>(sval,ival)<,endl;
			return 0;
		}
	
	【122】16.25使用显式模板实参，使得可以传递两个字符串字面值调用compare
	答：只需按如下方式使用显式模板实参：
		compare<std::string>(“mary”,”mac”);	
		亦可采用如下强制类型转换方式：
		compare(static_cast<std::string>(“mary”),static_cast<std::string>(“mac”))
		或 compare(std::string(“mary”),std::string(“mac”))

	
	【123】16.26对于下面的sum模板定义：
		template<class T1, class T2, class T3>T1 sum(T2,T3);
		解释下面的每个调用。如果有，指出哪些是错误的。
		double dobj1,dobj2; float fobj1,fobj2; char cobj1,cobj2;
		a)sum(dobj1,dobj2);
			错位，没有为模板类型形参T1指定相应的类型实参；
		b)sum<double,double,double>(fobj1,fobj2);
			正确，编译器将根据显式模板实参为该调用产生函数实例double sum(double,double),并将两个函数实参由float类型转换为double类型来调用该实例；
		c)sum<int>(cobj1,cobj2);
			正确，编译器将根据显式模板实参及函数实参为该调用产生函数实例int sum(char,char).
		d)sum<double, , double>(fobj2,dobj2);
			错位，只有最右边形参的显式模板实参可以省略，不能用”，”代替被省略的显式模板实参。
	
	【124】16.36每个带标号的语句，会导致实例化吗？
		template <class T> class Stack{};
		void f1(Stack<char>);  //a)
		class Exercise{
			Stack<double> &rsd;   //b)
			Stack<int> s1;    //c)
		};
		int main(){
			Stack<char> *sc;   //d)
			f1(*sc);   //e)
			int iObj=sizeof(Stack<string>);   //f)
		}
	答：a)不会导致实例化，函数声明不会导致实例化；
		b)不会导致实例化，定义引用不会导致实例化；
		c)会导致实例化，在创建Exercise对象时会实例化Stack<int>类及其默认构造函数（严格来说这样的实例化不是由语句c)直接导致的，而是由定义Exercise对象的语句导致的；
		d)不会导致实例化，定义指针不会导致实例化；
		e)会导致实例化，调用函数f1时需创建形参对象，此时导致实例化Stack<char>类及其默认构造函数。注意，题目此处有误，指针sc尚未赋值就使用了。
		f)会导致实例化，sizeof操作需要使用具体类，此时导致实例化Stack<string>.
	
	【125】16.37下面哪些模板实例化是有效的？解释为什么实例化无效。
		template <class T,int size> class Array{…};
		template<int i,int w>class Screen{…}
		a)const int i=40,w=80; Screen<i,w+32> sObj;
		b)const int arr_size =1024; Array<string , arr_size> a1;
		c)unsigned int asize=255;  Array<int,asize> a2;
		d)const double db=3.1222; Array<double,db> a3;
		答：有效的模板实例化包括a)和b);
			c)之所以无效，是因为非类型模板实参必须是编译时常量表达式，不能用变量asize作模板实参；
			d)之所以无效，是因为db是double型常量，而该模板实例化所需的非类型模板实参为int型常量。
	
	【126】16.42编写一个输入操作符，读入一个istream对象并将读取的值放入一个Queue对象中。
	答：template<class Type>
		istream& operator>> (istream &is,Queue<Type> &q)
		{
			Type val;
			while(is>>val)
				q.push(val);
			return is;
		}
	

第17章	用于大型程序的工具
	【127】17.1下面的throw语句中，异常对象的类型是什么？
	a)range_error r(“error”); throw r;
	b)exception *p=&r; throw *p;
	答：a)异常对象r的类型是range_error
		b)被抛出的异常对象是对指针p解引用的结果，其类型与p的静态类型相匹配，为exception。

	【128】17.2如果上题中第二个throw语句写成throw p，会发生什么情况？
	答：如果r是一个局部对象，则throw p抛出的p是指向局部对象的指针，那么，在执行对应异常处理代码时，有可能对象r已不存在，从而导致程序不能正确运行。所以，通常throw语句不应该抛出指针，尤其不应该抛出指向局部对象的指针。

	【129】17.3解释下面try块为什么不正确。
		try{
			…
		}catch(exception){
			…
		}catch(const runtime_error &re){
			…
		}catch(overflow_error eobj){…}
	答：该try块中使用的exception、runtime_error及overflow_error是标准库中定义的异常类，它们是因继承而相关的：runtime_error类继承exception类，overflow_error类继承runtime_error类，在使用来自继承层次的异常时，catch子句应该从最低派生类型到最高派生类型排序，以便派生类型的处理代码出现在基类类型的catch之前，所以，上述块中catch子句的顺序错误。
	
	【130】17.5对于下面的异常类型以及catch子句，编写一个throw表达式，该表达式创建一个可被每个catch子句捕获的异常对象。
	a)class exceptionType{};
	  catch(exceprionType *pet){}
	b)catch(…){};
	c)enum mathErr{overflow,underflow,zeroDivide};
	  catch(mathErr &ref){}
	d)typedef int EXCPTYPE;
	  catch(EXCPTYPE){}
	答：a)throw new exceptionType(); //
		b)throw 8; //
		c)throw overflow; //
		d)throw 10; //
	
	【131】17.6给定下面的函数，解释当发生异常时会发生什么？
		void exercise(int *b,int *e)
		{
			vector<int> v(b,e);
			int *p=new int(v.size());
			ifstream in(“ints”);
			//exception occurs here
		}
	答：在new操作之后发生的异常使得动态分配的数组没有被撤销。

	【132】17.7有两种方法可以使上面的代码是异常安全的，描述并实现它们。
	答：一种方法是将有可能发生异常的代码放在try块中，以便在异常发生时捕获异常：
		void exercise(int *b,int *e)
		{
			vector<int> v(b,e);
			int *p=new int(v.size());
			try{
				ifstream in(“ints”);
				//exception occurs here
			}
			catch{
				delete p;
			}
		}
		另一种方法是定义一个类来封装数组的分配和释放，以保证正确释放资源：
		class Resource{
		public:
			Resource(size_t sz):r(new int[sz]){}
			~Resource(){ if(r) delete r;}
		private:
			int *r;
		};
		函数exercise相应修改为：
		void exercise(int *b,int *e)
		{
			vector<int> v(b,e);
			Resource res(v.size())；
			ifstream in(“ints”);
			//exception occurs here
		}
	
	【133】17.8下面的auto_ptr声明中，哪些是不合法的或者可能导致随后的程序错误？解释每个声明的问题。
	int ix=1024,*pi=&ix,*pi2=new int(2048);
	typedef auto_ptr<int> IntP;
	a) IntP p0(ix);	
		不合法，必须向auto_ptr的构造函数传递一个由new操作返回的指针，ix不是指针；
	b) IntP p1(pi);
		可能导致随后的程序错误：auto_ptr只能用于管理从new操作返回的一个对象，p1是指向ix的指针，而ix是静态分配的对象；
	c) IntP p2(pi2);
		正确；
	d) IntP p3(&ix);
		可能导致随后的程序错误：auto_ptr只能用于管理从new操作返回的一个对象，此处传给auto_ptr构造函数的是静态分配的对象ix的地址；
	e) IntP p4(new int(2048));
		正确；
	f) IntP p5(p2.get());
		可能导致随后的程序错误：因为两个auto_ptr对象p2和p5拥有同一基础对象（保存相同的指针），会导致同一指针被delete两次。

	【134】17.10如果函数有形如throw()的异常说明，它能抛出什么异常？如果没有异常说明呢？
	答：如果函数有形如throw()的异常说明，则该函数不抛出任何异常
		如果函数没有异常说明，则该函数可以抛出任意类型的异常

	【135】17.11如果有，下面哪个初始化是错误的，为什么？
		void example() throw(string);
		a)void (*pf1)()=example;
		b)void (*pf2)() throw() example;
		答：b)是错误的，因为用另一个指针初始化带有异常说明的函数指针时，源指针的异常说明必须至少与目标指针一样严格。函数指针pf2的声明指出，pf2指向不抛出任何异常的函数，而example函数的声明指出它能抛出string类型的异常，example函数抛出的异常类型超过了pf2所指定的，所以，对pf2而言，example函数不是有效的初始化式，会引发编译时错误。
	
	【136】17.12下面的函数可以抛出哪些异常？
	a)void operator[] throw(logic_error);
	b)int op(int) throw(underflow_error,overflow error);
	c)char manip(string) throw();
	d)void process();
	答：异常说明指定，如果函数抛出异常，被抛出的异常将是包含在该说明中的一种，或者是从列出的异常类型中派生的类型，因此：
	a)可以抛出logic_error、domain_error等等异常；
	b)可以抛出underflow_error和overflow_error类型的异常；
	c)不抛出任何异常
	d)可以抛出任意类型的异常
	
	【137】17.18何时可以使用未命名的命名空间？
	答：通常，当需要声明局部于文件的实体时，可以使用未命名的命名空间（在文件的最外层作用域中定义未命名的命名空间。
	
	【138】17.25给定下面的类层次，其中每个类定义了一个默认构造函数：
	class X{…};
	class A{…};
	class B:public A{…};
	class C:private B{…};
	class D:public X,public C{…};
	如果有，下面转换中哪些是不允许的？
	D *pd =new D;
	a)X *px=pd;
	b)A *pa=pd;
	c)B *pb=pd;
	d)C *pc=pd;
	答：c)和b)是不允许的。
	因为C对B的继承是私有继承，使得在D中B的默认构造函数成为不可访问的，所以尽管存在从”D”到”B”以及从”D”到”A”的转换，但这些转换不可访问。
	
	【139】17.34有一种情况下派生类不必为虚基类提供初始化式，这种情况是什么？
	答：派生类不必为虚基类提供初始化式的情况是：虚基类具有默认构造函数；
		
	
第18章	特殊工具与技术
	【140】18.11解释下面的new和delete表达式中发生什么。
		struct Exercise{
			Exercise();
			~Exercise();
		};
		Exercise *pe=new Exercise[20];
		delete[] pe;
	答：new表达式动态分配包含20个Exercise对象元素的数组，并调用Exercise类的默认构造函数对数组元素进行初始化。
		delete表达式调用Exercise类的析构函数清除由new表达式动态分配的数组中的每个对象，并释放该数组所占用的内存。
	
	【141】18.13给定下面的类层次，其中每个类都定义了public默认构造函数和虚析构函数：
		class A{…};
		class B:public A{…};
		class C:public B{…};
		class D:public B,public A{…};
	如果有，下面哪些dynamic_cast失败？
		a)A *pa=new C;
		  B *pb=dynamic_cast<B*>(pa);
		b)B *pb=new B;
		  C *pc=dynamic_cast<C*>(pb);
		c) A *pa=new D;
		  B *pb=dynamic_cast<B*>(pa);
	答:使用dynamic_cast操作符时，如果运行时实际绑定到引用或指针的对象不是目标类型的对象（或其派生类的对象），则dynamic_cast失败；
		b)中dynamic_cast失败。因为目标类型为C，但pb实际指向的不是C类对象，而是一个B类（C的基类）对象。
		

	【142】18.16解释什么时候可以使用dynamic_cast代替虚函数。
	答：如果我们需要在派生类中增加新的成员函数（假设为函数f），但又无法取得基类的源代码，因而无法在基类中增加相应的虚函数，这时，可以在派生类中增加非虚成员函数。但这样一来，就无法用基类指针来调用f。如果在程序中需要通过基类指针（如果用该继承层次的某个类中所包含的指向基类对象的指针数据成员p）来调用f，则必须使用dynamic_cast将p转换为指向派生类的指针，才能调用f。也就是说，如果无法为基类增加虚函数，就可以使用dynamic_cast代替虚函数。
	
	【143】18.21普通数据指针或函数指针与数据成员指针或函数成员指针之间的区别是什么？
	答：区别在于，指定成员指针（数据成员指针及函数成员指针）的类型时，除了给出成员本身的类型之外，还必须给出所属类的类型（函数成员指针还要指明成员是否为const）。例如：
	指向int型数据的普通数据指针的类型为int*，而指向C类的int型数据成员的成员指针类型为int C::*;指向“不带参数并返回int型值的函数”的普通函数指针类型为int(*)()，而指向“C类的不带参数并返回int型值的const成员函数”的函数成员指针的类型为int (C::*)()const;
	
	【144】18.34解释下面这些声明，并指出它们是否合法：
		extern “C” int compute(int *,int);
		ectern “C” double compute(double *,double);
	答：第一个声明指出，compute是一个用C语言编写的函数，该函数接受一个int*类型及一个int类型的形参，返回int型值；
		第二个声明指出，compute是一个用C语言编写的函数，该函数接受一个double*类型及一个double类型的形参，返回double型值。
		如果这两个声明单独出现，则是合法的；如果二者同时出现，则是不合法的，因为这两个compute函数构成了函数重载，而C语言是不支持函数重载的。

2011-09-15 -2011-09-18
	
	
	
	
	
	
	
	
	
	
	








