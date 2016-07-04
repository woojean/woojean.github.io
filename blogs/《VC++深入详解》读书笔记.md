# 《VC++深入详解》读书笔记

## 第1章 Windows程序内部运行机制
1.API与SDK：Windows操作系统提供了各种各样的函数用来方便进行Windows应用程序开发，API函数就是指系统提供的函数，所有主要的Windows函数都在Windows.h头文件中进行了声明。Windows操作系统提供了1000多种API函数。
	SDK实际上就是进行程序开发所需的资源的一个集合，Win32 SDK就是Windows 32位平台下的软件开发包，包括了API函数、帮助文档、一些微软提供的辅助开发工具。

2.窗口与句柄：窗口可以分为客户区和非客户区，客户区是窗口的一部分，应用程序通常在客户区中显示文字或者绘制图形。标题栏、菜单栏、系统菜单、最小化框和最大化框、可调边框统称为窗口的非客户区。非客户区由Windows系统来管理，而应用程序则主要管理客户区的外观及操作。
	桌面也是一个窗口，称为桌面窗口，它由Windows系统创建和管理。
	在Windows程序中有各种各样的资源，如窗口、图标、光标等。系统在创建这些资源时会为它们分配内存，并返回标识这些资源的标识号，即为句柄（HANDLE）。窗口是通过窗口句柄（HWND）来标识的。

3.消息和消息队列：Windows程序设计是一种事件驱动方式的程序设计模式，主要是基于消息的。当用户的行为使操作系统感知到了某个事件，操作系统会将这个事件包装成一个消息投递到应用程序的消息队列中，然后应用程序从消息队列中取出消息并进行相应。
	在Windows程序中，消息是由MSG结构体来表示的，该结构体定义如下：
	typedef struct tagMSG {
    	HWND   hwnd; 		//表示消息所属的窗口
    	UINT   message; 	//消息的标识符。由于数值不便于记忆，Windows将消息对应的数值定义								//为形如WM_XXX的宏。
    	WPARAM wParam; 	
    	LPARAM lParam;		// wParam和lParam用于指定消息的附加信息
							// WPARAM实际上是unsigned int类型，LPARAM实际上是long类型
    	DWORD  time; 		//消息投递到消息队列中的时间
    	POINT  pt; 			//鼠标的当前位置
	} MSG, *PMSG;
	消息队列：每一个Windows应用程序开始执行后，系统都会为该程序创建一个消息队列，这个消息队列用来存放该应用程序创建的窗口的消息。Windows将产生的消息依次放到消息队列中，而应用程序则通过一个消息循环不断地从消息队列中取出消息，并进行响应，这种消息机制就是Windows程序运行的机制。
	“进队消息”和“不进队消息”：Windows程序中的消息可以分为“进队消息”和“不进队消息”。进队的消息将由系统放入到应用程序的消息队列中，然后由应用程序取出并发送。不进队的消息在系统调用窗口过程时直接发送给窗口。不管是进队消息还是不进队消息，最终都由操作系统通过调用窗口过程函数对消息进行处理。

4.WinMain函数
	WinMain函数的声明原型如下：
	int WINAPI WinMain(
  		HINSTANCE hInstance, 	// 该程序当前运行的实例的句柄，一个应用程序可以运行多个实例
  		HINSTANCE hPrevInstance,	// 当前实例的前一个实例的句柄，在Win32下总为NULL
  		LPSTR lpCmdLine,      	// 指定传递给应用程序的命令行参数
  		int nCmdShow        	// 指定程序的窗口应该如何显示，如最大化、最小化等
	);
	注：修饰符WINAPI其实就是_stdcall._stdcall 与_cdecl是两种不同的函数调用约定，区别在于函数参数入栈的顺序，由调用函数还是被调用函数将参数弹出栈，以及产生函数修饰名的方法。对于参数可变的函数如printf，使用的是_cdecl调用约定，Win32的API函数都遵循_stdcall调用约定。在VC++开发环境中，默认的编译选项是_cdecl，对于那些需要_stdcall调用约定的函数，必须显式的加上_stdcall
	窗口的创建：创建一个完整的窗口，需要经过下面几个操作步骤：
	（1）设计一个窗口类
		一个完整的窗口具有许多特征，包括光标、图标、背景色等等。窗口的特征由WNDCLASS结构体来定义，该结构体定义如下：
	typedef struct _WNDCLASS { 
    	UINT		style; 			//指定窗口的样式。以CS_开头的可选类样式都是只有某一位为1									//的16位常量，可以用位运算符来组合使用这些位标识符
    	WNDPROC 	lpfnWndProc; 	//指向窗口过程函数的函数指针，窗口过程函数是一个回调函数
    	int        	cbClsExtra; 		//类附加内存，一般设为0
    	int        	cbWndExtra; 		//窗口附加内存，一般设为0
    	HINSTANCE	hInstance; 		//指定包含窗口过程的程序的实例句柄
    	HICON    	hIcon; 			//图标句柄，可使用LoadIcon函数加载图标资源
    	HCURSOR 	hCursor; 		//光标句柄
    	HBRUSH   	hbrBackground; 	//背景画刷句柄，也可以指定为一个标准的系统颜色值；
									//可以使用GetStockObject函数来获取画刷、画笔、字体等句柄；
    	LPCTSTR  	lpszMenuName; 	//指定菜单资源的名字
									//如果使用ID号，则需使用MAKEINTRESOURCE宏进行转换
									//菜单并不是一个窗口
    	LPCTSTR  	lpszClassName; 	//窗口类的名字
	} WNDCLASS, *PWNDCLASS;	
	注：①可以使用位或操作来组合两种窗口的样式，如：style=CS_HREDRAW|CS_VREDRAW;假如有一个变量具有多个样式，而并不清楚该变量具有哪些样式，现在如果想去掉该变量具有的某个样式，可以通过先对该样式标识符取反，然后再与该变量进行与操作来实现，如去掉style变量所具有的CS_VREDRAW样式：style=style&~CS_VREDRAW.
	②回调函数不是由该函数的实现方直接调用，而是在特定的事件或者条件发生时由另外一方调用的，用于对该事件或条件进行相应。提供函数实现的一方在初始化的时候将回调函数的函数指针注册给调用者，当特定的事件或者条件发生时，调用者使用函数指针调用回调函数对事件进行处理。
	③一个Windows应用程序可以包含多个窗口过程函数，一个窗口过程总是与一个特定的窗口相关联，基于该窗口类创建的窗口使用同一个窗口过程。窗口过程函数被调用的过程如下：
	<1>在设计窗口类的时候，将窗口过程函数的地址赋值给lpfnWndProc成员变量；
	<2>调用RegsiterClass(&wndclass)注册窗口类，这样系统就有了窗口过程函数的地址；
	<3>当应用程序接收到某一窗口的消息时，调用DispatchMessage(&msg)将消息回传给系统，系统则利用先前注册窗口类时得到的函数指针调用窗口过程函数对消息进行处理。
	④WNDPROC实际上是函数指针类型，其定义为：
		typedef LRESULT (CALLBACK* WNDPROC)(HWND,UNIT,WPARAM,LPARAM);
		LRESULT实际为long，CALLBACK实际为_stdcall.
		窗口过程函数的格式必须与WNDPROC相同。
	⑤在VC++中，对于自定义的各种资源都保存在扩展名为.rc的资源脚本文件中，文件本身是文本格式。资源是通过ID来标识的，同一个ID可以标识多个不同的资源。资源的ID实质上是一个整数，在”Resource.h”中定义为一个宏。如菜单资源IDM_XXX,图标资源IDI_XXX；
	（2）注册窗口类
		设计完窗口类后需要调用RegisterClass函数对其进行注册，注册成功后才可以创建该类型的窗口，注册函数声明如下：
		ATOM RegisterClass(CONST WNDCLASS *lpWndClass);
	（3）创建窗口
	HWND CreateWindow(
  		LPCTSTR lpClassName,  	//指定窗口类的名称，产生窗口的过程由操作系统完成如果没有注册									//过指定名称的窗口类，将创建失败
  		LPCTSTR lpWindowName, 	//指定窗口的名字，如果窗口样式指定了标题栏，则改名字将显示在									//标题栏上
  		DWORD dwStyle,        	//指定创建的窗口的样式
  		int x,                	//窗口左上角的x坐标
  		int y,                		//窗口左上角的y坐标
  		int nWidth,           	//窗口的宽度
  		int nHeight,          		//窗口的高度
  		HWND hWndParent,      	//指定被创建窗口的父窗口句柄，子窗口必须具有WS_CHILD样式；								//对父窗口的操作同时也会影响到子窗口
  		HMENU hMenu,          //指定窗口菜单的句柄
  		HINSTANCE hInstance,  	//指定窗口所属的应用程序实例的句柄
  		LPVOID lpParam        	//作为WM_CREAT消息的附加参数lParam传入的数据指针
						//在创建多文档界面的客户窗口时，lParam必须指向CLIENTCREATESTRUCT
	);
	如果窗口创建成功，CreateWindow函数将返回系统为该窗口分配的句柄，否则，返回NULL。注意，在创建窗口之前应先定义一个窗口句柄变量来接收创建窗口之后返回的句柄值。
	注：①参数dwStyle指定某个具体的窗口的样式，区别于WNDCLASS中的style成员，style指定窗口类的样式，基于该窗口类创建的窗口都具有这些样式；style的位标志以CS_开头，而dwStyle的位标志以WS_开头。
	②如果参数x被设为CW_USERDEFAULT那么系统为窗口选择默认的左上角坐标并忽略y参数；如果nWidth被设为CW_USEDEFAULT，那么系统为窗口选择默认的宽度和高度，参数nHeight被忽略。
	③对父窗口的操作对子窗口的影响：
	<1>销毁：在父窗口被销毁之前销毁；
	<2>隐藏：在父窗口被隐藏之前隐藏，子窗口只有在父窗口可见时可见
	<3>移动：跟随父窗口客户区一起移动
	<4>在父窗口显示之后显示
	（4）显示及更新窗口
	显示窗口调用函数：
	BOOL ShowWindow(
  		HWND hWnd,   	//要显示的窗口的句柄
  		int nCmdShow   	//窗口显示的状态，SW_开头的位标志
	);
	更新窗口调用函数：
	BOOL UpdateWindow(
  		HWND hWnd   	// handle to window
	);
	UpdateWindow函数通过发送一个WM_PAINT消息来刷新窗口，它将WM_PAINT消息直接发送给了窗口过程函数进行处理，而没有放到消息队列当中。
	消息循环：要从消息队列中取出消息，需要调用GetMessage函数：
	BOOL GetMessage(
  		LPMSG lpMsg,    	//指向一个消息结构体；GetMessage从线程的消息队列中取出的消息将保								//存在该结构体对象中
  		HWND hWnd,     	//指定接收属于哪一个窗口的消息；设置为NULL则接收属于调用线程的								//所有窗口的窗口消息
  		UINT wMsgFilterMin, 	//指定要获取的消息的最小值，通常设为0
  		UINT wMsgFilterMax 	//指定要获取的消息的最大值
	);
	GetMessage函数接收到除WM_QUIT外的消息均返回非零值。对于WM_QUIT消息，该函数返回零。如果出现了错误，该函数返回-1.要特别注意返回-1时退出消息循环的情况。
	注：如果wMsgFilterMin和wMsgFilterMax参数同时设为0，则接收所有消息。
	通常编写的消息循环代码如下：
	MSG msg;
	while(GetMessage(&msg,NULL,0,0))
	{
		TranslateMessage(&msg);
		DispatchMessage(&msg);
	}
	TranslateMessage函数用于将虚拟键消息转换为字符消息，即将WM_KEYDOWN和WM_KEYUP消息的组合转换为一条WM_CHAR消息，并将转换后的新消息投递到调用线程的消息队列中，当下一次调用GetMessage函数时被取出。TranslateMessage函数并不会修改原有的消息，它只是产生新的消息并投递到消息队列中。
	DispatchMessage函数分派一个消息到窗口过程，由窗口过程函数对消息进行处理。DispatchMessage实际上是将消息回传给操作系统，由操作系统调用窗口过程函数对消息进行处理。
	Windows应用程序的消息处理过程：	
	（1）操作系统接收到应用程序的窗口消息，将消息投递到该应用程序的消息队列中；
	（2）应用程序在消息循环中调用GetMessage函数从消息队列中取出一条一条的消息。取出消息后，应用程序可以对消息进行一些预处理，例如放弃对某些消息的响应，或者调用TranslateMessage产生新的消息；
	（3）应用程序调用DispatchMessage将消息回传给操作系统。消息是由MSG结构体对象表示的，其中就包含了接收消息的窗口的句柄，因此DispatchMessage函数总能进行正确的投递；
	（4）系统利用WNDCLASS结构体的lpfnWndProc成员保存的窗口过程函数的指针调用窗口过程对消息进行处理；
	注：①从消息队列中获取消息还可以使用PeekMessage函数，该函数可以指定在获取消息后是否将消息从消息队列中移除。
	②发送消息可以使用SendMessage和PostMessage函数。SendMessage将消息直接发送给窗口，并调用该窗口的窗口过程进行处理，在窗口过程对消息处理完毕后该函数才返回，即发送不进队消息。PostMessage函数将消息放入与创建窗口的线程相关联的消息队列后立即返回。
	此外，PostThreadMessage函数用于向线程发送消息。
	编写窗口过程函数：
	窗口过程函数的声明形式：
	LRESULT CALLBACK WindowProc(
  		HWND hwnd,      	// handle to window
  		UINT uMsg,      	// message identifier
  		WPARAM wParam,  	// first message parameter
  		LPARAM lParam   	// second message parameter
	);
	系统通过窗口过程函数的地址来调用窗口过程函数，而不是名字。因此窗口过程函数的名字可以随便取，但是函数定义的形式是确定的。
	在窗口过程函数内部通常对uMsg参数使用switch/case语句来确定窗口过程接收的是什么消息，以及如何对这个消息进行处理。
源码：WinMain.cpp
#include <windows.h>
#include <stdio.h>
LRESULT CALLBACK WinSunProc(HWND	 hwnd, UINT uMsg, WPARAM wParam, LPARAM lParam);
int WINAPI WinMain(HINSTANCE hInstance, HINSTANCE hPrevInstance, LPSTR lpCmdLine, int  nCmdShow)
{	
	WNDCLASS wndcls;		//设计一个窗口
	wndcls.cbClsExtra = 0;
	wndcls.cbWndExtra = 0;
	wndcls.hbrBackground = (HBRUSH) GetStockObject(BLACK_BRUSH);
	wndcls.hIcon = LoadIcon (NULL,IDI_ERROR);
	wndcls.hCursor = LoadCursor (NULL,IDC_CROSS);
	wndcls.hInstance = hInstance;
	wndcls.lpfnWndProc = WinSunProc; 	//指定窗口过程函数
	wndcls.lpszClassName = "myWnd"; 	//自定义窗口类的名称
	wndcls.style = CS_HREDRAW | CS_VREDRAW;
	wndcls.lpszMenuName = NULL;

	RegisterClass(&wndcls);	//注册窗口类

	HWND hwnd;
	hwnd = CreateWindow ("myWnd","标题栏文字", WS_OVERLAPPEDWINDOW, 0, 0, 600, 400, NULL, NULL, hInstance, NULL );
	ShowWindow (hwnd, SW_SHOWNORMAL);
	UpdateWindow (hwnd);

	MSG  msg;
	while (GetMessage (&msg, NULL, 0, 0 ))
	{
		TranslateMessage (&msg);
		DispatchMessage (&msg);
	}
	return 0;
}

LRESULT CALLBACK WinSunProc(HWND hwnd, UINT uMsg, WPARAM wParam, LPARAM lParam)
{
	switch(uMsg)
	{
	case WM_CHAR:	//
		char szChar[20];
		sprintf (szChar , "char is %d",wParam);
		MessageBox(hwnd,szChar,"对话框标题",0);
		break;
	case WM_LBUTTONDOWN:
		MessageBox(hwnd,"mouse clicked","对话框标题",0);
		HDC hdc;
		hdc = GetDC(hwnd);
		TextOut (hdc,0,50,"点击了鼠标左键",strlen("点击了鼠标左键"));
		ReleaseDC (hwnd,hdc);
		break;
	case WM_PAINT:
		HDC hDC;
		PAINTSTRUCT ps;
		hDC = BeginPaint(hwnd,&ps);
		TextOut(hDC,0, 0, "默认绘制文字",strlen("默认绘制文字"));
		EndPaint(hwnd,&ps);
		break;
	case WM_CLOSE:
		if(IDYES == MessageBox(hwnd,"是否真的结束?","要退出第一个写的程序吗？",MB_YESNO))
		{
			DestroyWindow(hwnd);
		}
		break;
	case WM_DESTROY:
		PostQuitMessage(0);
		break;
	default:
		return DefWindowProc(hwnd,uMsg, wParam, lParam);
	}
	return 0;
}//end of WinSunProc()
注：①在响应WM_PAINT消息的代码中，要得到窗口的DC，必须调用BeginPaint函数。BeginPaint函数也只能在WM_PAINT消息的响应代码中使用。在其他地方只能使用GetDC来得到DC的句柄。
	②DestroyWindow函数在销毁窗口后会向窗口过程发送WM_DESTROY消息。此时窗口虽然销毁了，但是应用程序并没有退出。所以不应在WM_DESTROY消息的响应代码中提示用户是否退出，因为此时窗口已经销毁了，即使用户选择不退出也没有什么意义。如果要控制程序是否退出，应该在WM_CLOSE消息的响应代码中完成。对WM_CLOSE消息的响应并不是必须的，如果应用程序没有对该消息进行响应，系统将把这条消息传给DefWindowProc函数，而DefWindowProc函数则调用DestroyWindow函数来响应这条WM_CLOSE消息。
	GetMessage函数只有在收到WM_QUIT消息时才返回0，此时消息循环才结束，程序退出。要想让程序正常退出，必须响应WM_DESTROY消息，并在消息响应代码中调用PostQuitMessage向应用程序的消息队列中投递WM_QUIT消息。
	③DefWindowProc函数调用默认是窗口过程，对应用程序没有处理的其他消息提供默认处理。对于大多数的消息，应用程序都可以直接调用DefWindowProc函数进行处理。
	在编写窗口过程函数时，应该将DefWindowProc函数的调用放到default语句中，并将该函数的返回值作为窗口过程函数的返回值。
	④在用GetMessage函数接收指定窗口的消息时，要注意返回值为-1导致死循环的情况。改进代码如下：
	BOOL bRet;
	while( (bRet = GetMessage(&msg,hwnd,0,0))!=0)
	{
		if(bRet == -1) return -1;
		else{
			TranslateMessage (&msg);
			DispatchMessage (&msg);
		}
	}
	…
第3章MFC框架程序剖析
1.基于MFC的程序框架剖析
对于一个单文档应用程序，使用MFC AppWizard将自动生成5个类（假设工程名为Test）：
①CAboutDlg←CDialog←CWnd←CCmdTarget←CObject;
②CMainFrame←CFrameWnd←CWnd←CCmdTarget←CObject;
③CTestApp←CWinApp←CWinThread←CCmdTarget←CObject;
④CTestDoc←CDocument←CCmdTarget←CObject;
⑤CTestView←CView←CWnd←CCmdTarget←CObject;

在CTestApp的源文件Test.cpp中定义有一个CTestApp类型的全局对象theApp：CTestApp theApp;
对于MFC程序来说，通过产生一个应用程序类的对象来标识应用程序的实例。每一个MFC程序有且仅有一个从应用程序类CWinApp派生的类。每一个MFC程序实例有且仅有一个该派生类的实例化对象，也就是theApp全局对象。该对象就表示了应用程序本身。
	根据C++的特性：无论全局变量还是全局对象，程序在运行时，在加载main函数之前就已经为全局变量或全局对象分配了内存空间。对于一个全局对象来说，此时就会调用该对象的构造函数进行初始化工作。又因为CTestApp派生于CWinApp，因此theApp对象的构造函数CTestApp在调用之前会调用其父类CWinApp的构造函数，从而就把自己创建的类同MFC提供的基类关联起来了。CWinApp的构造函数完成程序运行时的一些初始化工作。
CWinApp类定义的源文件：\Microsoft Visual Studio\VC98\MFC\SRC\APPCORE.CPP
在CWinApp的构造函数中有如下语句：
CWinApp::CWinApp(LPCTSTR lpszAppName)
{
	…
	pModuleState->m_pCurrentWinApp = this;
	…
}
根据C++继承性的原理，这里this对象代表的是子类CTestApp的对象，即theApp。又，在CWinApp的定义文件中可以发现构造函数的参数lpszAppName具有默认值：CWinApp(LPCTSTR lpszAppName=NULL);
，所以在调用CWinApp的构造函数时就不用显式地去传递这个参数的值。
当程序调用了CWinApp类的构造函数，并执行了CTestApp类的构造函数，且产生了theApp对象之后，接下来就进入主函数。
MFC程序的主函数所在文件：\Microsoft Visual Studio\VC98\MFC\SRC\ APPMODUL.CPP
extern "C" int WINAPI
_tWinMain(HINSTANCE hInstance, HINSTANCE hPrevInstance, LPTSTR lpCmdLine, int nCmdShow)
{
	// call shared/exported WinMain
	return AfxWinMain(hInstance, hPrevInstance, lpCmdLine, nCmdShow);
}
_tWinMain实际上是一个宏： #define _tWinMain WinMain
Afx前缀的函数代表应用程序框架函数。应用程序框架实际上是一套辅助生成应用程序的框架模型。在MFC中以Afx为前缀的函数都是全局函数，可以在程序的任何地方调用它们。
AfxWinMain函数所在文件：\Microsoft Visual Studio\VC98\MFC\SRC\WINMAIN.CPP
int AFXAPI AfxWinMain(HINSTANCE hInstance, HINSTANCE hPrevInstance,
	LPTSTR lpCmdLine, int nCmdShow)
{
	…
	CWinThread* pThread = AfxGetThread();
	CWinApp* pApp = AfxGetApp();
	…
	// App global initializations (rare)
	if (pApp != NULL && !pApp->InitApplication())
		goto InitFailure;

	// Perform specific initializations
	if (!pThread->InitInstance())
	{
		if (pThread->m_pMainWnd != NULL)
		{
			TRACE0("Warning: Destroying non-NULL m_pMainWnd\n");
			pThread->m_pMainWnd->DestroyWindow();
		}
		nReturnCode = pThread->ExitInstance();
		goto InitFailure;
	}
	nReturnCode = pThread->Run();
	InitFailure:
	…
}
注：①AfxGetThread函数返回的就是AfxGetApp函数的结果，且CWinApp派生于CWinThread因此pThread和pApp这两个指针是一致的。AfxGetApp函数的返回结果为：AfxGetModuleState()->m_pCurrentWinApp.
由CWinApp的构造函数中代码：pModuleState->m_pCurrentWinApp = this;可知pThread和pApp所指向的都是CTestApp类的对象，即theApp全局对象。
	②InitApplication、InitInstance、Run这三个函数用来完成Win32程序所需要的几个步骤：设计窗口类、注册窗口类、创建窗口、显示窗口、更新窗口、消息循环，以及窗口过程函数； 
	③InitApplication完成MFC内部管理方面的工作，包括窗口类的注册、创建，窗口的显示和更新；而InitInstance函数在CWinApp中定义为一个虚函数，同时在CTestApp中也有一个InitInstance函数，根据多态性原理可知AfxWinMain实际调用的是CTestApp的InitInstance函数：
	BOOL CTestApp::InitInstance()
	{
		…
		CSingleDocTemplate* pDocTemplate;
		pDocTemplate = new CSingleDocTemplate(
		IDR_MAINFRAME,
		RUNTIME_CLASS(CTestDoc),
		RUNTIME_CLASS(CMainFrame),       // main SDI frame window
		RUNTIME_CLASS(CTestView));
		AddDocTemplate(pDocTemplate);
		…
		m_pMainWnd->ShowWindow(SW_SHOW);
		m_pMainWnd->UpdateWindow();
		return TRUE;
}

2.MFC框架窗口
①设计和注册窗口：
CMainFrame类的对象代表应用程序框架窗口。该类有一个PreCreateWindow函数，该函数在窗口产生之前被调用，其实现代码如下：
	BOOL CMainFrame::PreCreateWindow(CREATESTRUCT& cs)
	{
		if( !CFrameWnd::PreCreateWindow(cs) )
			return FALSE;
		…
	}
可见其调用了父类的PreCreateWindow函数。
CFrameWnd实现所在文件：：
	C:\Program Files\Microsoft Visual Studio\VC98\MFC\SRC\WINFRM.CPP
BOOL CFrameWnd::PreCreateWindow(CREATESTRUCT& cs)
{
	if (cs.lpszClass == NULL)
	{
		VERIFY(AfxDeferRegisterClass(AFX_WNDFRAMEORVIEW_REG));
		cs.lpszClass = _afxWndFrameOrView;  // COLOR_WINDOW background
	}
	… 
}
AfxDeferRegisterClass其实是一个宏：
	#define AfxDeferRegisterClass(fClass) AfxEndDeferRegisterClass(fClass)
AfxEndDeferRegisterClass即是用来完成窗口类的注册，该函数定义位于WINCORE.CPP文件中：
BOOL AFXAPI AfxEndDeferRegisterClass(LONG fToRegister)
{
	…
	WNDCLASS wndcls;
	memset(&wndcls, 0, sizeof(WNDCLASS));   // start with NULL defaults
	wndcls.lpfnWndProc = DefWindowProc;
	wndcls.hInstance = AfxGetInstanceHandle();
	wndcls.hCursor = afxData.hcurArrow;
	…
	if (fToRegister & AFX_WND_REG)
	{
		…
		if (AfxRegisterClass(&wndcls))
			fRegisteredClasses |= AFX_WND_REG;
	}
	if (fToRegister & AFX_WNDOLECONTROL_REG)
	{
		…
		if (AfxRegisterClass(&wndcls))
			fRegisteredClasses |= AFX_WNDOLECONTROL_REG;
	}
	…
}
AfxEndDeferRegisterClass函数首先判断窗口类的类型，然后赋予其相应的类名，这些类名都是MFC预先定义的，之后调用AfxRegisterClass函数注册窗口类。该函数也位于WINCORE.CPP文件中：
BOOL AFXAPI AfxRegisterClass(WNDCLASS* lpWndClass)
{
	…
	if (!::RegisterClass(lpWndClass))
	{
		TRACE1("Can't register window class named %s\n",
			lpWndClass->lpszClassName);
		return FALSE;
	}
	…
}
可见在AfxRegisterClass函数内部最终还是通过RegisterClass实现窗口类的注册。
注：wndcls.lpfnWndProc = DefWindowProc;指定了一个默认的窗口过程。但是MFC并不是把所有消息都交给DefWindowProc这一默认窗口过程来处理，而是采用了一种称之为“消息映射”的机制来处理各种消息。
②创建窗口：
	在CFrameWnd类的LoadFrame函数中会调用CFrameWnd::Create函数，而在Create函数内部则调用CWnd::CreateEx函数（CWnd::CreateEx函数不是虚函数）实现窗口创建功能。该函数实现代码位于WINCORE.CPP文件中。
	在CreateEx函数实现中又再次调用了PreCreateWindow函数，因为PreCreateWindow是一个虚函数，所以这里实际上调用的是子类即CMainFrame类的函数。之所以在这里再次调用这个函数，主要是为了在产生窗口之前让程序员有机会修改窗口外观。
③显示窗口和更新窗口
	m_pMainWnd->ShowWindow(SW_SHOW);
	m_pMainWnd->UpdateWindow();
④消息循环
CWinThread类的Run函数用来完成消息循环这一任务。在AfxWinMain中调用如下：pThread->Run();
Run函数定义位于THRDCORE.CPP文件中：
int CWinThread::Run()
{
	…
	for (;;)
	{
		// phase1: check to see if we can do idle work
		while (bIdle &&!::PeekMessage(&m_msgCur, NULL, NULL, NULL, PM_NOREMOVE))
		{…}
		// phase2: pump messages while available
		do
		{
			// pump message, but quit on WM_QUIT
			if (!PumpMessage())
				return ExitInstance();
			…
		} while (::PeekMessage(&m_msgCur, NULL, NULL, NULL, PM_NOREMOVE));
	}
	…
}
该函数的主要结构是一个for循环，该循环在接收到一个WM_QUIT消息时退出。在此循环中调用了PumpMessage函数，该函数实现如下：
BOOL CWinThread::PumpMessage()
{
	ASSERT_VALID(this);
	if (!::GetMessage(&m_msgCur, NULL, NULL, NULL))
	{
		…
		return FALSE;
	}
	…
	if (m_msgCur.message != WM_KICKIDLE && !PreTranslateMessage(&m_msgCur))
	{
		::TranslateMessage(&m_msgCur);
		::DispatchMessage(&m_msgCur);
	}
	return TRUE;
}
可见其内部实现与Win32 SDK编程的消息处理代码是一致的。
文档/视图结构：MFC程序除了主框架窗口以外，还有一个窗口是视类窗口，对应的类是CView类。MFC提供了一个文档/视图结构，其中文档就是指CDocument类，视图就是指CView类。数据的存储和加载由文档类来完成，数据的显示和修改则由视类来完成，从而把数据管理和显示方法分离开来。

3.窗口类、窗口类对象、窗口
窗口类对象与窗口并不是一回事，它们之间唯一的关系是窗口类内部定义了一个窗口句柄变量，保存了与这个窗口类对象相关的那个窗口的句柄。窗口销毁时与之对应的窗口类对象销毁与否要看其生命周期是否结束，但窗口类对象销毁时，与之相关的窗口也将销毁。

第4章 简单绘图
1.MFC消息映射机制
通过MFC提供的向导添加消息响应函数后，ClassWizard会在所选类的头文件和源文件中添加三处信息：
以在CDrawView视类中通过类向导添加鼠标左键按下这一消息为例：
①消息响应函数原型
在CDrawView类的头文件中添加如下代码：
	class CDrawView : public CView
	{
	// Generated message map functions
	protected:
		//{{AFX_MSG(CDrawView)
		afx_msg void OnLButtonDown(UINT nFlags, CPoint point);
		//}}AFX_MSG
		DECLARE_MESSAGE_MAP()
	};
在两个AFX_MSG注释宏之间添加一个函数原型OnLButtonDown，该函数声明的前部有一个afx_msg限定符，这也是一个宏，该宏表明这个函数是一个消息响应函数的声明。
②消息映射宏
在CDrawView类的源文件中添加ON_WM_LBUTTONDOWN消息映射宏：
BEGIN_MESSAGE_MAP(CDrawView, CView)
	//{{AFX_MSG_MAP(CDrawView)
	ON_WM_LBUTTONDOWN()
	//}}AFX_MSG_MAP
	// Standard printing commands
	ON_COMMAND(ID_FILE_PRINT, CView::OnFilePrint)
	ON_COMMAND(ID_FILE_PRINT_DIRECT, CView::OnFilePrint)
	ON_COMMAND(ID_FILE_PRINT_PREVIEW, CView::OnFilePrintPreview)
END_MESSAGE_MAP()
BEGIN_MESSAGE_MAP和END_MESSAGE_MAP这两个宏之间定义了CDrawView类的消息映射表，其中有一个ON_WM_LBUTTONDOWN消息映射宏，这个宏的作用就是把鼠标左键按下的消息（WM_LBUTTONDOWN）与一个消息响应函数关联起来
③消息响应函数的定义
在CDrawView类的源文件中可以看到OnLButtonDown函数的定义：
	void CDrawView::OnLButtonDown(UINT nFlags, CPoint point) 
	{
		// TODO: Add your message handler code here and/or call default
		CView::OnLButtonDown(nFlags, point);
	}
只要遵照上述步骤定义了与消息有关的三处信息后，就可以实现消息的响应处理。MFC中采用的这种消息处理机制称为MFC消息映射机制。
	MFC消息映射机制的具体实现方法是：在每个能接收和处理消息的类中定义一个消息和消息函数静态对照表，即消息映射表。在消息映射表中，消息与对应的消息处理函数指针是成对出现的。某个类能处理的所有消息及其对应的消息处理函数的地址都列在这个类所对应的静态表中。当有消息需要处理时，程序只要搜素该消息静态表，查看表中是否含有该消息，就可以知道该类能否处理此消息。如果能处理该消息，则同样依照静态表能很容易找到并调用对应的消息处理函数。

2.绘制线条
定义一个CPoint类型的成员变量m_ptOrigin，在OnLButtonDown消息响应函数中对其赋值，保存下鼠标按下点的信息：
	void CDrawView::OnLButtonDown(UINT nFlags, CPoint point) 
	{
		// TODO: Add your message handler code here and/or call default
		m_ptOrigin = point;
		CView::OnLButtonDown(nFlags, point);
	}
在OnLButtonUp消息响应函数中实现绘制：
①利用SDK全局函数实现画线功能
void CDrawView::OnLButtonUp(UINT nFlags, CPoint point) 
{
	HDC hdc;
	hdc = ::GetDC(m_hWnd);//获得当前窗口的设备描述表
	MoveToEx(hdc, m_ptOrigin.x, m_ptOrigin.y,NULL);
	LineTo(hdc, point.x,point.y);
	::ReleaseDC(m_hWnd,hdc);
	
	CView::OnLButtonUp(nFlags, point);
}
②利用MFC的CDC类实现画线功能
	CDC *pDC = GetDC();//此处调用的是CWnd的GetDC成员函数
	pDC->MoveTo(m_ptOrigin);
	pDC->LineTo(point);
	ReleaseDC(pDC);
	CDC类封装了所有与绘图相关的操作，该类提供了一个数据成员m_hDc用来保存与CDC类相关的DC句柄。
③利用MFC的CClientDC类实现画线功能
	CClientDC dc(this);
	dc.MoveTo(m_ptOrigin);
	dc.LineTo(point);
	CClientDC类派生于CDC类，并且在构造时调用GetDC函数，在析构时调用ReleaseDC函数。
若将上述代码中构造CClientDC对象的部分替换为如下代码：CClientDC dc(this);即构造了一个与视图类的父窗口，也就是框架窗口相关的CClientDC对象，这是就可以在框架窗口的客户区进行绘图了，也就是说这时可以在工具栏上进行绘图了。因为工具栏属于框架窗口的客户区而非视图类的客户区，因此，与视图类相关的CClientDC不能在工具栏上绘图，同时，因为菜单栏是框架窗口的非客户区，所以即使是与框架窗口相关的CClientDC也无法实现在框架菜单栏上进行绘图。
④利用MFC的CWindowDC类实现画线功能
	CWindowDC dc(GetParent());	
	dc.MoveTo(m_ptOrigin);
	dc.LineTo(point);
CWindowDC也派生于CDC类，使用CWindowDC对象可以访问整个窗口区域，包括框架窗口的非客户区和客户区。通常都是在客户区中绘图，但是如果利用CWindowDC类，就可以实现在工具栏和菜单上绘图。
⑤在桌面窗口中画线
CWnd类的GetDesktopWindow成员函数可以获得Windows桌面窗口的句柄，使用如下代码可以实现在桌面窗口中画线：
	CWindowDC dc(GetDesktopWindow());
⑥绘制彩色线条
可以利用MFC提供的CPen来创建画笔对象，该类封装了与画笔相关的操作。在程序中，当构造一个GDI对象后，该对象并不会立即生效，必须通过SelectObject函数将其选入设备描述表，它才会在以后的绘制操作中生效，并且该函数返回先前被选对象的指针。
	CPen pen(PS_SOLID,1,RGB(25,23,142));
	CClientDC dc(this);
 	CPen *pOldPen=dc.SelectObject(&pen);
	dc.MoveTo(m_ptOrigin);
	dc.LineTo(point);
	dc.SelectObject(pOldPen);

3.使用画刷绘图
MFC提供了一个CBrush类，可以用来创建画刷对象，画刷通常用来填充一块区域。
①简单画刷
	CBrush brush(RGB(255,0,0)); //创建一个红色画刷
	CClientDC dc(this); //创建并获得设备描述表
	dc.FillRect(CRect(m_ptOrigin,point),&brush); //利用红色画刷填充鼠标拖曳过程中形成的矩形区域
	FillRect函数将用指定的画刷填充全部的矩形，包括矩形的左边和上部边界，但不填充右边和底部边界。
②位图画刷
	CBrush类有如下构造函数CBrush(CBitmap* pBitmap);CBitmap类是位图类，仅调用CBitmap的构造函数并不能得到一个有用的位图对象，还需要调用一个初始化函数来初始化这个位图对象。CBitmap类提供了多个初始化函数，如LoadBitmap、CreateBitmap、CreateBitmapIndirect等。LoadBitmap函数有如下声明：
	BOOL LoadBitmap( LPCTSTR lpszResourceName ); //
	BOOL LoadBitmap( UINT nIDResource );
	lpszResourceName:Points to a null-terminated string that contains the name of the bitmap resource.
	nIDResource:Specifies the resource ID number of the bitmap resource.
假设已经创建了一个名为IDB_BITMAP1的位图资源，则可如下使用位图画刷：
	CBitmap bitmap;
	bitmap.LoadBitmap(IDB_BITMAP1);
	CBrush brush(&bitmap);
	CClientDC dc(this);
	dc.FillRect( CRect(m_ptOrigin,point),&brush);
③透明画刷
	设备描述表中有一个默认的白色画刷，在绘图时它会利用这个画刷来填充矩形内部，所以当位置存在重叠时，后绘制的矩形就会把先前绘制的矩形遮挡住。
	CBrush类并没有创建透明画刷的方法，可以通过将GetStockObject函数的参数取值为NULL_BRUSH来获取一个空画刷的句柄，然后使用CBrush类提供的静态函数FromHandle来实现画刷句柄至画刷对象的转换，FromHandle函数的声明如下：static CBrush* PASCAL FromHandle( HBRUSH hBrush );
	CClientDC dc(this);
	CBrush *pBrush=CBrush::FromHandle((HBRUSH)GetStockObject(NULL_BRUSH));
	CBrush *pOldBrush=dc.SelectObject(pBrush);

4.绘制连续线条
void CDrawView::OnMouseMove(UINT nFlags, CPoint point) 
{
	CClientDC dc(this);
	if (m_bDraw==true){ // m_bDraw成员变量在鼠标按下时为true，在鼠标弹起时为false
		dc.MoveTo(m_ptOrigin);
		dc.LineTo(point);
		m_ptOrigin = point; //绘制连续线条，此刻的终点便是下一刻的起点
	}
	
	CView::OnMouseMove(nFlags, point);
}

第5章 文本编程
1.插入符
可以利用CWnd类的CreateSolidCaret函数来创建插入符，该函数的原型如下：
	void CreateSolidCaret( int nWidth, int nHeight );
插入符的创建应该在窗口创建之后进行，可以在WM_CREATE消息的响应函数OnCreate中（在创建窗口的代码之后）添加创建插入符的代码。
为了能够让插入符适合当前字体的大小，首先就需要得到设备描述表中当前字体的信息，也就是文本信息，然后根据字体的信息来调整插入符的大小。调用CDC的GetTextMetrics成员函数可以得到设备描述表中当前字体的度量信息，该函数的原型声明如下：
	BOOL GetTextMetrics( LPTEXTMETRIC lpMetrics ) const;
如下代码演示了如何创建合适的插入符：
	CClientDC dc(this);
	TEXTMETRIC tm;
	dc.GetTextMetrics(&tm);
	CreateSolidCaret(tm.tmAveCharWidth/8, tm.tmHeight); //字体平均宽度除以8是一个经验值
	ShowCaret();
此外，void CreateCaret( CBitmap* pBitmap );函数可以用来创建图形插入符。
2.窗口重绘
Windows程序运行时，如果窗口大小发生变化，窗口会发生重绘，如果希望输入的内容始终保留在窗口上，就要在响应WM_PAINT消息的函数中将内容再次输出。WM_PAINT消息的响应函数为OnDraw：
	void CTextView::OnDraw(CDC* pDC)
	{
		CTextDoc* pDoc = GetDocument();
		ASSERT_VALID(pDoc);
		…
	}
CString类：MFC提供了一个CString类，这个类没有基类。一个CString对象由一串可变长度的字符组成。CString类同时重载了多个操作符，并且提供了多种构造方法。
3.路径
在MFC中创建路径层是利用CDC类提供的BeginPath和EndPath这两个函数来实现的，首先调用BeginPath，该函数的作用是在设备描述表中打开一个路径层；然后利用图形设备接口提供的绘图函数进行绘图操作，在绘图操作完成之后，应用程序通过调用EndPath函数关闭这个路径层。
裁剪区域：可以理解为一个绘图区域，其大小可以控制。CDC类提供了一个SelectClipPath函数，该函数的作用是把当前设置的路径层和设备描述表中已有的裁剪区域按照一种指定的模式进行一个互操作：
	BOOL SelectClipPath( int nMode );
如指定新的裁剪区域包含当前裁剪区域，但是排除当前路径层区域：
		CSize sz =pDC->GetTextExtent(str);
		pDC->TextOut(250,150,str);
		pDC->BeginPath();//打开一个路径层
		pDC->Rectangle(250,150,250+sz.cx, 150+sz.cy); //画一个矩形，并将此矩形做为路径层
		pDC->EndPath();//关闭一个路径层
		pDC->SelectClipPath(RGN_XOR);
4.字符输入	让CTextView类捕获WM_CHAR消息，并为该类定义一个CString类型的成员变量：m_strLine专门用来存储输入的字符串，除此之外还须考虑如下问题：
	①程序应当在当前插入符的位置输出字符，并且当在屏幕上输出字符时，插入符的位置也应发生改变；	CWnd:SetCaretPos(POINT point)
	②回车、退格键的处理；CDC:setTextColor、CDC:GetBkColor、CString:Left
	③字体；CFont类
定时器：
	CWnd :SetTimer
	UINT SetTimer( UINT nIDEvent, UINT nElapse, void (CALLBACK EXPORT* lpfnTimer)(HWND, UINT, 		UINT, DWORD) );
	SetTimer(1,100,NULL);
	void CTextView::OnTimer(UNIT nIDEvent) 
		// nIDEvent为定时器的标识，可以通过对其进行判断来解决多个定时器冲突的问题 
	{
		…
		CView::OnTimer(nIDEvent);
	}
MFC提供了CEditView和CRichEditView两个类来帮助实现功能强大的字处理程序。

第6章 菜单
1.MFC中把设置为Pop-up类型的菜单称为弹出式菜单，Visual C++默认顶层菜单为弹出式菜单。这种菜单不能响应命令。
2.菜单命令的路由
程序类对菜单命令的响应顺序依次是：视类、文档类、框架类、应用程序类；
Windows消息的分类：
①标准消息（窗口消息）：除WM_COMMAND之外，所有以WM_开头的消息都是标准消息。从CWnd派生的类都可以接收到这类消息。
②命令消息：来自菜单、加速键或者工具栏按钮的消息。这类消息都以WM_COMMAND形式呈现。在MFC中，通过菜单项的标识（ID）来区分不同的命令消息；在SDK中，通过消息的wParam参数识别。从CCmdTarget派生的类都可以接收到这类消息。
③通告消息（控件消息）：由控件产生的消息，目的是向其父窗口（通常是对话框）通知事件的发生。这类消息也是以WM_COMMAND形式呈现的。从CCmdTarget派生的类都可以接收到这类消息。
因为CWnd派生于CCmdTarget类，也就是说，凡是从CWnd派生的类，它们既可以接收标准消息，也可以接收命令消息和通告消息，而对于那些从CCmdTarget派生的类，则只能接收命令消息和通告消息，不能接收标准消息。
菜单命令消息路由的具体过程：当点击某个菜单项时，最先收到这个菜单命令消息的是框架类。框架类将把这个消息交给视类，视类首先进行处理。视类根据命令消息映射机制查找自身是否对此消息进行了响应，如果响应了就调用相应响应函数对这个消息进行处理，消息路由结束；如果视类没有对此命令消息做出响应，就交由文档类，文档类同样查找消息映射，若进行了响应则消息路由结束，否则将这个消息交还给视类，视类再将该消息交还给框架类。框架类查找自身有无消息响应函数，如果没有则将该命令消息交给应用程序类。
3.基本菜单操作：
如果要访问某个菜单项，既可以通过该菜单项的标识ID，也可以通过其位置索引来实现访问。但对于子菜单来说，只能通过索引号进行访问，因为子菜单是没有标识号的。分隔栏在子菜单中是占据索引位置的。
因为程序的主菜单属于框架窗口，所以需要在框架类窗口创建完成之后再去访问菜单对象。可以在框架类CMainFrame的OnCreate函数的最后（但一定要在return语句之前）添加实现这个功能的语句。
①标记菜单
	GetMenu()->GetSubMenu(0)->CheckMenuItem(ID_FILE_NEW,MF_BYCOMMAND | 					MF_UNCHECKED);
CWnd::GetMenu函数返回一个指向CMenu类对象的指针。
CMenu::GetSubMenu函数返回一个由索引指定的子菜单的CMenu指针。
CMenu::CheckMenuItem函数为菜单项添加一个标记，或者移除菜单项的标记。第一个参数指定需要处理的菜单项，它的取值取决于第二个参数的取值。
②默认菜单项
GetMenu()->GetSubMenu(0)->SetDefaultItem(1,TRUE);
CMenu::SetDefaultItem
BOOL SetDefaultItem( UINT uItem, BOOL fByPos = FALSE );
一个子菜单只能有一个默认菜单项。
③图形标记菜单
	m_bitmap.LoadBitmap(IDB_BITMAP1);
	GetMenu()->GetSubMenu(0)->SetMenuItemBitmaps(0,MF_BYPOSITION,&m_bitmap,&m_bitmap);
CMenu::SetMenuItemBitmaps 
BOOL SetMenuItemBitmaps( UINT nPosition, UINT nFlags, const CBitmap* pBmpUnchecked, const CBitmap* pBmpChecked );
	图形标记菜单上显示的位图的尺寸有固定的标准，通过int GetSystemMetrics(int nIndex)函数可以得到位图的尺寸：
	int x=GetSystemMetrics(SM_XMENUCHECK);	
	int y=GetSystemMetrics(SM_CYMENUCHECK);	
④禁用菜单项
	GetMenu()->GetSubMenu(0)->EnableMenuItem(ID_FILE_NEW,MF_BYCOMMAND | MF_DISABLED);
	MFC为菜单提供了一种命令更新的机制，程序在运行时根据此机制去判断哪个菜单可以使用，哪个菜单不能够使用，然后显示其相应的状态。默认情况下所有菜单项的更新都是由MFC的命令更新机制完成的。如果想自己更改菜单项的状态，就必须把m_bAutoMenuEnable变量设置为FALSE,之后我们自己对菜单项的状态更新才能起作用。
⑤移除和装载菜单
CWnd:: BOOL SetMenu( CMenu* pMenu );
SetMenu(NULL); //移除菜单

CMenu menu;
menu.LoadMenu(IDR_MAINFRAME);
SetMenu(&menu);//装载菜单
menu.Detach();
在设置窗口菜单时，如果定义的是局部菜单对象，则一定要在调用SetMenu函数设置窗口菜单后立即调用菜单对象的Detach函数将菜单句柄与菜单对象分离。
CMenu:: HMENU Detach( );
Return Value:The handle, of type HMENU, to a Windows menu, if successful; otherwise NULL.
Remarks:Detaches a Windows menu from a CMenu object and returns the handle. The m_hMenu data member is set to NULL.
⑥MFC菜单命令更新机制
如果要在程序中设置某个菜单项的状态，首先通过ClassWizard为这个菜单项添加UPDATE_COMMAND_UI消息响应函数，然后在这个函数中进行状态的设置即可，如为“新建”菜单项添加UPDATE_COMMAND_UI消息响应函数：
	void CMainFrame::OnUpdateFileNew(CCmdUI* pCmdUI) 
	{
		pCmdUI->Enable(FALSE); //使“新建”菜单项失效	
	}
UPDATE_COMMAND_UI消息的响应只能应用于菜单项，不能应用于永久显示的顶级菜单，即弹出式菜单项目。
如果要把工具栏上的一个工具按钮与菜单栏中的某个菜单项相关联，只要将它们的ID设置为同一个标识就可以了。因为菜单项和工具按钮的位置索引计算方式不同，为了保持二者状态一致，最好采用菜单项标识或工具栏按钮标识的方式来进行设置。
⑦快捷菜单
插入快捷菜单：【Project】->【Add To Project】->【Components and Controls…】->Pop-up Menu；
插入快捷菜单之后添加了两处内容：
	第一处，在ResourceView选项卡的Menu分支下多了一个标识为CG_IDR_POPUP_MENU_VIEW的菜单资源，这个菜单只有一个顶层菜单项：_POPUP_，其下有Cut、Copy、Paste三个菜单项。
	第二处，为CMenuView类添加了一个函数：OnContextMenu。在程序运行时，当用鼠标右键单击窗口时，程序就会调用这个函数，该函数内部使用TrackPopupMenu函数来显示快捷菜单。
由此可以在鼠标右击响应函数中定义实现自己的快捷菜单：
void CMenuView::OnRButtonDown(UINT nFlags, CPoint point) 
{
	// TODO: Add your message handler code here and/or call default
	CMenu menu;
	menu.LoadMenu(IDR_MENU1);
	CMenu *pPopup = menu.GetSubMenu(0);
	ClientToScreen(&point); //将一个坐标点或矩形区域坐标转换成屏幕坐标。
	pPopup->TrackPopupMenu(TPM_LEFTALIGN|TPM_RIGHTBUTTON,
		point.x, point.y,this);//在指定位置以指定的方式显示弹出菜单。

	CView::OnRButtonDown(nFlags, point);
}
对于快捷菜单，只有将其拥有者窗口设置为框架类窗口，框架类窗口才有机会获得对该快捷菜单中菜单项命令的响应，否则就只能由视类窗口做出响应。
4.动态菜单操作
①添加菜单项目
	CMenu menu;	//创建一个菜单
	menu.CreatePopupMenu();	
	GetMenu()->AppendMenu(MF_POPUP,(UINT)menu.m_hMenu,"AppednMenu");
		//MF_POPUP创建一个弹出菜单，(UINT)menu.m_hMenu 是菜单句柄
	menu.Detach();	//将句柄与CMenu对象断开
②插入菜单项目
	GetMenu()->InsertMenu(2,MF_BYPOSITION|MF_POPUP,
		(UINT)menu.m_hMenu,"InsertMenu");	//插入一个子菜单
	menu.AppendMenu(MF_STRING,IDM_HELLO/*ID号*/,"Hello");
	menu.AppendMenu(MF_STRING,112,"Weixin");
	menu.Detach();	
	GetMenu()->GetSubMenu(0)->InsertMenu(ID_FILE_OPEN,
		MF_BYCOMMAND | MF_STRING,115,"维新(&p)	Ctrl+P");
		//在“文件->打开”之前插入一个叫“维新”的菜单项
③删除菜单
	GetMenu()->DeleteMenu(1,MF_BYPOSITION); //删除“编辑”子菜单
	GetMenu()->GetSubMenu(0)->DeleteMenu(ID_FILE_PRINT, MF_BYCOMMAND); //删除“文件->打印”
④动态添加的菜单项的命令响应
	在Header Files目录下的Resource.h文件中定义了当前程序使用的一些资源ID，可以手工为菜单项添加一个ID，然后遵照MFC消息映射机制，即需要添加三处代码来实现命令消息的响应。

第7章 对话框（一）
1.对话框基本知识
	在MFC中所有的控件类都是由CWnd类派生来的，因此控件实际上也是窗口，控件通常是作为对话框的子窗口而创建的。另外控件也可以出现在视类窗口、工具栏和状态条中。控件是独立的小部件，在对话框与用户的交互过程中担任着主要角色。
对话框的种类：
	①模态对话框：当其显示时程序会暂停执行，直到关闭这个模态对话框后，才能继续执行程序中的其他任务；
	②非模态对话框：非模态对话框显示时允许程序转而执行其他任务，而不用关闭这个对话框。
2.对话框的创建和显示
对话框作为资源插入到项目中。在MFC中，对资源的操作通常都是通过一个与资源相关的类来完成的，对话框资源也有一个相应的基类：CDialog，他派生自CWnd类。可以通过类向导为对话框资源新建相关联的类。例如为资源ID号为IDD_DIALOG1的对话框新建了一个CTestDlg类，则在该类中有两个成员函数，其一为构造函数：
	CTestDlg::CTestDlg(CWnd* pParent /*=NULL*/)
		: CDialog(CTestDlg::IDD, pParent)
	{
		//{{AFX_DATA_INIT(CTestDlg)
			//NOTE:the ClassWizard will add member initialization here
		//}}AFX_DATA_INIT
	}
由CTestDlg的头文件中可知，IDD即为IDD_DIALOG1：
		//{{AFX_DATA(CTestDlg)
			enum { IDD = IDD_DIALOG1 };
		//}}AFX_DATA
另一个函数为DoDataExchange，主要用来完成对话框数据的交换和校验：	
	void CTestDlg::DoDataExchange(CDataExchange* pDX)
	{
		CDialog::DoDataExchange(pDX);
		//{{AFX_DATA_MAP(CTestDlg)
			//NOTE:the ClassWizard will add DDX and DDV calls here
		//}}AFX_DATA_MAP
	}
模态对话框的创建：
	CTestDlg dlg;
	dlg.DoModal();
	dlg.EndDialog();
非模态对话框的创建：
	CTestDlg *pDlg=new CTestDlg;
	pDlg->Create(IDD_DIALOG1,this);
	pDlg->ShowWindow(SW_SHOW);
在创建非模态对话框时不能把对话框对象定义为局部变量，解决方法有两种：一是把对话框变量定义为视类的成员变量；第二种方法是将其定义为指针，在堆上分配内存，在堆上分配的内存与程序的整个生命周期是一致的。对于模态对话框则不存在这个问题，因为在调用DoModal函数显示时，程序会暂时停止执行，直到模态对话框关闭以后程序才继续向下执行。
当点击对话框上的OK按钮时，对于模态对话框而言，此时对话框窗口对象被销毁了，但是对于非模态对话框而言，对话框窗口对象并未被销毁，只是隐藏起来了。因此对于非模态对话框来说，必须重写响应的按钮响应函数，并在重写的函数中调用DestroyWindow函数，同时注意不要再调用基类的相应函数。
控件的访问
为了使一个静态文本控件能够响应鼠标单击事件，需要进行两个特殊的步骤：首先改变它的ID，使其不为默认的IDC_STATIC，其次在它的属性对话框中选中Notify。
对话框控件访问的七种方式
①GetDlgItem(ID)->Get(Set)WindowText()
②GetDlgItemText()/SetDlgItemText()
③GetDlgItemInt()/SetDlgItemInt()
④将控件和整型变量（Value）相关联：将编辑框与对话框的成员变量相关联，然后通过成员变量来检索和设置编辑框的文本。
	当为ID为IDC_EDIT1的编辑框控件添加了一个关联的int型成员变量m_num1后，ClassWizard添加了以下内容：
	1）在CTestDlg类的头文件中两个AFX_DATA注释宏之间增加了一个成员变量：
		// Dialog Data
		//{{AFX_DATA(CTestDlg)
			enum { IDD = IDD_DIALOG1 };
			int		m_num1;
		//}}AFX_DATA
	2）在CTestDlg类的构造函数中对其进行了初始化，赋值为0：
		CTestDlg::CTestDlg(CWnd* pParent /*=NULL*/)
			: CDialog(CTestDlg::IDD, pParent)
		{
			//{{AFX_DATA_INIT(CTestDlg)
				m_num1 = 0;
			//}}AFX_DATA_INIT
		}
	3）在DoDataExchange函数内部实现了对话框控件与类成员变量的关联：
		void CTestDlg::DoDataExchange(CDataExchange* pDX)
		{// CDataExchange提供了DDX、DDV运行所需的上下文信息
			CDialog::DoDataExchange(pDX);
			//{{AFX_DATA_MAP(CTestDlg)
				DDX_Text(pDX, IDC_EDIT1, m_num1);
			//}}AFX_DATA_MAP
		}
	DoDataExchange函数由程序框架调用，以完成对话框数据的交换和校验。DDX_Text函数的功能就是将ID指定的控件与特定的类成员变量相关联。MFC提供了许多以DDX_为前缀的函数，这些函数分别用于不同控件的数据交换。在程序中从来不直接调用DoDataExchange函数，而是通过CWnd类的另一个函数UpdateData来调用。UpdateData函数有一个BOOL参数，如果其值为TRUE，则说明该函数正在获取对话框的数据，如果其值为FALSE，则说明该函数正在初始化对话框的控件，参数默认值为TRUE。
	可以在类向导窗口中设置关联变量的取值范围，此时ClassWizard将自动添加数据校验函数。
⑤将控件和控件变量（Control）相关联
	1）头文件:
		//{{AFX_DATA(CTestDlg)
			enum { IDD = IDD_DIALOG1 };
			CEdit	m_edit1;
	2）DoDataExchange函数:
		DDX_Control(pDX, IDC_EDIT1, m_edit1);
	然后就可以像操纵控件本身一样来使用控件变量了。
⑥SendMessage()
	Windows程序都是基于消息的系统，在Windows系统中获取窗口文本的消息是WM_GETTEXT、设置窗口文本的消息是WM_SETTEXT。可以利用SDK提供的SendMessage函数发送WM_GETTEXT消息获取文本，再发送WM_SETTEXT消息设置文本：
	void CTestDlg::OnBtnAdd() 
	{	
		int num1,num2,num3;
		char ch1[10],ch2[10],ch3[10];
		SendMessage(GetDlgItem(IDC_EDIT1)->m_hWnd,WM_GETTEXT,10,(LPARAM)ch1);
		SendMessage(GetDlgItem(IDC_EDIT2)->m_hWnd,WM_GETTEXT,10,(LPARAM)ch2);
		num1 = atoi(ch1);
		num2 = atoi(ch2);
		num3 = num1 + num2;
		itoa(num3,ch3,10);
		::SendMessage(GetDlgItem(IDC_EDIT3)->m_hWnd,WM_SETTEXT,0,(LPARAM)ch3);
	}//ENDOF CTestDlg::OnBtnAdd() 
	字符串与数值的转换：C语言提供了这样的转换函数atoi可以将一个由数字组成的字符串转换为响应的数值；itoa函数可以将一个数值转换为文本。
⑦SendDlgItemMessage()：直接向对话框的子控件发送消息；
	SendDlgItemMessage(IDC_EDIT1,WM_GETTEXT,10,(LPARAM)ch1); 
获取窗口的大小和位置：GetWindowRect；
设置窗口的大小和位置：SetWindowPos；
当一个窗口已经创建之后，可以通过SetWindowLong函数来修改该窗口以指定的过程函数。当对话框及其上的子控件创建完成，将要显示之前会发送一个消息：WM_INITDIALOG。因此，在此消息的响应函数中修改窗口过程比较合适。SetWindowLong可用来修改窗口的诸多属性。
输入焦点的传递。。。

第8章 对话框（二）
属性表单和向导的创建
	为了创建一个属性表单，首先需要创建一个CPropertySheet对象，接下来，在此对象中为每一个属性页创建一个对象（CPropertyPage），并调用AddPage函数添加每一个属性页，然后调用DoModal函数显示一个模态属性表单，或者调用Create函数创建一个非模态属性表单。CPropertyPage从CDialog派生而来。
	创建一个向导类型的对话框应该遵循创建一个标准属性表单的步骤来实现，但在调用属性表单对象的DoModal函数之前，应该先调用SetWizardMode这一函数。CPropertySheet提供了SetWizardButtons函数来设置向导对话框上的按钮。一般在属性页的OnSetActive函数中调用SetWizardButtons。


第9章 定制应用程序外观
既可以在应用程序窗口创建之前修改窗口外观，也可以在该窗口创建之后进行。如果希望在应用程序窗口创建之前修改它的大小和外观，就应该在CMainFrame类的PreCreateWindow成员函数中进行：
	BOOL CMainFrame::PreCreateWindow(CREATESTRUCT& cs)
	{
		if( !CFrameWnd::PreCreateWindow(cs) )
			return FALSE;
		// TODO: Modify the Window class or styles here by modifying
		//  the CREATESTRUCT cs
	return TRUE;
}
只需要修改CREATESTRUCT结构体中相关成员变量的值就可以了。
	如果想在窗口创建之后改变其外观，可以在框架类的OnCreate函数中通过调用SetWindowLong函数实现。对应的GetWindowLong函数用来获取指定窗口的信息。

第12章 文件和注册表操作
1.const char*和char* const
指向常量的指针const char*:也可将const放到char后面，但是要在*号前面，即也可以写为：char const*；该类型定义的常量指针不能修改其指向的内存中的内容，但可以修改其所指向的内存地址。
指针常量char* const：必须在其定义的同时赋值。指针常量表示指针本身是常量。
2.C语言对文件操作的支持（头文件：stdio.h）
①文件的打开：FILE *fopen( const char *filename, const char *mode );
②文件的写入：size_t fwrite( const void *buffer, size_t size, size_t count, FILE *stream );
	C语言对文件的操作使用了缓冲文件系统。
③文件的关闭：int fclose( FILE *stream );
④在不关闭文件的情况下将缓冲区数据写入磁盘文件：int fflush( FILE *stream );
⑤文件指针定位：int fseek( FILE *stream, long offset, int origin );
⑥文件的读取：size_t fread( void *buffer, size_t size, size_t count, FILE *stream );
	应该在读取到的数据之后添加’\0’字符，以便作为字符串的结尾，有以下方法：
	1）在写入数据时多写入一个内容为’\0’的字节；
	2）在定义字符数组之后，利用C语言的memset函数将这个字符数组中所有数据都设置为0：
		char ch[100];
		memset(ch,0,100);
		fread(ch,1,100,pFile);
	3)对于事先不知道将要读取的文件大小的情况，可以利用C语言的ftell及fseek函数来得到文件的长度。ftell函数返回文件指针当前的位置，因此可以先利用fseek函数将文件指针移动到文件的结尾处，然后利用ftell函数就可以得到文件指针的当前位置，也就是文件的长度。再根据此长度构造数组，并采取置0操作：
		FILE *pFile=fopen(“1.txt”,”r”);
		char *pBuf;
		fseek(pFile,0,SEEK_END);
		int len=ftell(pFile);
		pBuf=new char[len+1];
		rewind(pFile); //将文件指针移动到文件开始处
		fread(pBuf,1,len,pFile);
		pBuf[len]=0;
		fclose(pFile);
3.C++对文件操作的支持（头文件：fstream.h）
①ofstream类用来向文件中写入数据；
②ifstream类用来读取文件；
4.Win32 API对文件操作的支持：CreateFile、ReadFile、WriteFile；
5.MFC对文件操作的支持：
	MFC提供的支持文件操作的基类是CFile，该类提供了没有缓存的二进制格式的磁盘文件输入输出功能；
	CFileDialog类用来实现一个具有“打开文件”或者“另存为”对话框的功能。

第13章 文档与串行化
	可以利用CArchive类将对象数据保存到永久设备（如磁盘）上，这样即使应用程序关闭，再次重启后仍可以从磁盘中读取对象数据，然后在内存中重新构建响应的对象。让对象数据持久性的过程就称之为串行化（或称为序列化）。
	CArchive类没有基类，可以认为一个CArchive对象就是一种二进制流。就像一个输入输出流一样，一个CArchive对象与一个文件相关，并允许带缓冲机制的数据写入和读取。与输入输出流不同的是，后者处理的是ASCII码字符序列，而一个CArchive对象以一种有效的、非冗余的格式处理二进制对象数据。
	使用CArchive的注意事项：
	①在创建CArchive对象之前必须先创建一个CFile类或其派生类的对象。因为存档对象既可以用来加载数据，也可以用来保存数据，所以必须确保这个CFile类对象的打开方式与该存档对象的加载\保存状态相一致。
	②一个文件（即CFile对象）只能与一个活动的存档对象相关联；
	③CArchive对象不仅可以处理基本类型的数据，还可以处理CObject类的派生类对象；
	④CArchive类重载了>>和<<操作符，>>用来从存档对象加载CObject类型和基本类型的数据；<<操作符用来将CObject类型和基本类型的数据存储到存档对象中；
	CFile file(“1.txt”,CFile::modeCreate | CFile::modeWrite);
	CArchive ar(&file,CArchive::store);
	int i=4;
	char ch=’a’;
	float f=1.3f;
	CString str(“abcdefghi”);
	ar<<i<<ch<<f<<str;//保存数据
	⑤对象保存的顺序和提取的顺序必须一致；
	
可串行化的类：如果要使一个类可串行化，可以经过以下五个步骤来实现：
	①从CObject或其子类派生类；
	②重写Serialize成员函数；
	③使用DECLARE_SERIAL宏（在类声明中），即在类定义的头文件内部添加语句
		DECLARE_SERIAL(class_name)
	④定义不带参数的构造函数；	
	⑤为类在实现文件中使用IMPLEMENT_SERIAL宏：
		IMPLEMENT_SERIAL(class_name,base_class_name,wSchema)
	为了使用CArchive保存对象，首先需要将这个对象所属的类定义成可串行化的类。
CObArray：CObArray类支持CObject指针数组。这些对象数组近似于C中的数组，但不同的是它们可以动态地增减。数组索引总是开始于位置0。你可以决定是否固定数组上界，或当增加元素超过了当前边界后，是否允许数组扩展。CObArray对象引入了IMPLEMENT_SERIAL宏，以支持其元素的串行化与转储。
Document/View结构：将一个应用程序所需要的“数据处理与显示”的函数空壳都设计好，这些函数都是虚函数，可以在派生类中重写这些函数。有关文件读写的操作在CDocument的Serialize函数中进行，有关数据和图形显示的操作在CView的OnDraw函数中进行。在其派生类中只需要去关注Serialize和OnDraw函数就可以了，其他的细节不用去理会，程序就可以良好地运行。

第14章 网络编程
Windows Sockets的实现：Windows Sockets是Windows的网络程序设计接口，它是从Berkeley Sockets扩展而来的，以动态链接库的形式提供使用。它与Berkeley Sockets都基于TCP/IP协议，它们中很多函数都是一致的，如果采用双方共有的这些函数编写网络程序，那么这些网络程序将会很容易地移植到其他系统下。
套接字的类型：
	①流式套接字（SOCK_STREAM）：提供面向连接、可靠的数据传输服务，数据无差错、无重复的发送，且按发送顺序接收。流式套接字实际上是基于TCP协议实现的；
	②数据报式套接字（SOCK_DGRAM）：提供无连接服务。数据包以独立包形式发送，不提供无错保证，数据可能丢失或重复，并且接收顺序混乱。数据报式套接字实际上是基于UDP协议实现的。
	③原始套接字（SOCK_RAW）

基于TCP（面向连接）的socket编程流程：
	服务器端：
	①创建套接字；
	②将套接字绑定到一个本地地址和端口上；
	③将套接字设为监听模式，准备接收客户请求；
	④等待客户请求到来；当请求到来后接受连接请求，返回一个新的对应于此次连接的套接字；
	⑤用返回的套接字和客户端进行通信；
	⑥返回，等待另一客户请求；
	⑦关闭套接字；
	客户端：
	①创建套接字；
	②向服务器发出连接请求；
	③和服务器端进行通信；
	④关闭套接字；
基于UDP（面向无连接）的socket编程流程：
	服务器端：
	①创建套接字；
	②将套接字绑定到一个本地地址和端口上；
	③等待接收数据
	④关闭套接字；
	客户端：
	①创建套接字；
	②向服务器发送数据；
	③关闭套接字；

代码解析：
基于TCP（面向连接）的socket编程流程：
服务器端：
#include <Winsock2.h>
#include <stdio.h>
void main()
{
	///加载套接字库
	WORD wVersionRequested;
	WSADATA wsaData;
	int err;
	wVersionRequested = MAKEWORD( 1, 1 );
				// MAKEWORD(x,y)宏，x、y分别为高低位字节； 
	err = WSAStartup( wVersionRequested, &wsaData );
				// WSAStartup函数用来加载套接字库并进行套接字库的版本协商
	if ( err != 0 ) return;	
	//判断是否我们请求的winsocket版本，如果不是则调用WSACleanup终止winsocket的使用并返回
	if ( LOBYTE( wsaData.wVersion ) != 1 ||HIBYTE( wsaData.wVersion ) != 1 ) 
	{
		WSACleanup( );
		return; 	
	}
	
	///创建套接字并绑定到本地地址与端口上
	SOCKET sockSrv = socket(AF_INET, //对于TCP/IP协议的套接字，地址族只能是AF_INET
		SOCK_STREAM,  //创建流式套接字
		0);  //零表示自动选择协议
	SOCKADDR_IN addrSrv;   //定义一个地址结构体的变量；SOCKADDR_IN结构体中除了sa_family								//成员以外，其他成员都是按网络字节顺序表示的
	addrSrv.sin_addr.S_un.S_addr = htonl(INADDR_ANY);
		//用htonl()方法将INADDR_ANY转换为网络字节序；将地址指定为INADDR_ANY将允许套接字向任何分配给本地机器的IP地址发送或接收数据
	addrSrv.sin_family = AF_INET;
	addrSrv.sin_port = htons(22222); //指定端口号；为所编写的网络程序指定端口号时，要使用1024以上的端口号；htons把u_short类型从主机字节序转换为网络字节序
	bind(sockSrv,(SOCKADDR *) &addrSrv,sizeof(SOCKADDR));
		//SOCKADDR大小写是一样的
	
	///设置监听
	listen(sockSrv,5);
		// listen函数将指定的套接字设置为监听模式，第二个参数是等待连接队列的最大长度
	SOCKADDR_IN addrClient;//用来接收连接实体的地址
	int len = sizeof(SOCKADDR);
	while(1)
	{	SOCKET sockConn = accept(sockSrv, (SOCKADDR *) &addrClient,&len);
			//返回一个用于当次通信的SOCKET
		char sendBuf[100];
		sprintf (sendBuf,"Welcome %s to \
			http://blog.csdn.net/teshorse",
			inet_ntoa(addrClient.sin_addr));
				// inet_ntoa函数将一个in_addr结构体类型的参数转换成以点分十进制格式表示的IP地址字符串，相反功能的函数为inet_addr；
		send(sockConn,sendBuf,strlen(sendBuf)+1,0);
		char recvBuf[100];
		recv(sockConn,recvBuf,100,0);
		printf("%s\n",recvBuf);
		closesocket(sockConn);
		…
	}
	WSACleanup( );
}
客户端：
void main()
{	
	///加载套接字库…

	///创建套接字并连接服务器端，无须绑定
	SOCKET sockClient = socket (AF_INET, SOCK_STREAM, 0);
	SOCKADDR_IN addrSrv;
	addrSrv.sin_addr.S_un.S_addr = inet_addr("127.0.0.1");
		//设定服务器端IP地址，"127.0.0.1"是本地回路地址
		//不管本地主机上有没有网卡，都可以用这个IP测试网络
	addrSrv.sin_family = AF_INET;
	addrSrv.sin_port = htons(22222); //端口号要与服务器端保持一致
	connect(sockClient,(SOCKADDR *)&addrSrv,sizeof(SOCKADDR));

	///接收服务器端发送的数据,并且向服务器端发送数据
	char recvBuf[100];
	recv(sockClient,recvBuf,100,0);
	printf("%s \n", recvBuf);
	getchar();
	send(sockClient,"I'm teshorse",strlen("I'm teshorse")+1,0);

	///关闭套接字，释放资源,并且终止对套接字库的使用
	closesocket(sockClient);
	WSACleanup();
}//endof main()

基于UDP（面向无连接）的socket编程流程：
服务器端：
#include <Winsock2.h>
#include <stdio.h>
void main()
{	
	///加载套接字库…
	///创建套接字并在本地地址与端口绑定
	SOCKET sockSrv = socket (AF_INET, 
		SOCK_DGRAM/*数据报套接字*/, 0);
	SOCKADDR_IN addrSrv;
	addrSrv.sin_addr.S_un.S_addr = htonl(INADDR_ANY);
	addrSrv.sin_family = AF_INET;
	addrSrv.sin_port = htons(6000);
	bind(sockSrv, (SOCKADDR *)&addrSrv,sizeof(SOCKADDR));
	///接收数据
	SOCKADDR_IN addrClient;
	int len=sizeof(SOCKADDR);
	char recvBuf[100];
	recvfrom(sockSrv,recvBuf,100,0,
		(SOCKADDR*)&addrClient,&len);
	printf("%s\n",recvBuf);
	closesocket(sockSrv);
	WSACleanup();
}
客户端：
	///加载套接字库…
	///创建套接字并发送消息
	SOCKET sockClient = socket(AF_INET,SOCK_DGRAM,0);
	SOCKADDR_IN addrSrv;
	addrSrv.sin_addr.S_un.S_addr = inet_addr("127.0.0.1");
	addrSrv.sin_family = AF_INET;
	addrSrv.sin_port = htons(6000);
	sendto(sockClient,"hello",strlen("hello")+1,0,
		(SOCKADDR*)&addrSrv,sizeof(SOCKADDR));
	closesocket(sockClient);
	WSACleanup();

第15章 多线程
程序与进程的关系：程序是计算机指令的集合，它以文件的形式存储在磁盘上，而进程通常被定义为一个正在运行的程序的实例，是一个程序在其自身的地址空间中的一次执行活动。一个程序可以对应多个进程，同时，在一个进程中也可以同时访问多个程序。
	进程是资源申请、调度和独立运行的单位，因此它使用系统中的运行资源。程序不能申请系统资源，不能被系统调度，也不能作为独立运行的单位，因此它不占用系统的运行资源。
进程与线程的关系：进程从来不执行任何东西，它只是线程的容器。若要使进程完成某项操作，它必须拥有一个在它的环境中运行的线程，此线程负责执行包含在进程的地址空间中的代码。也就是说，进程实际上是线程的执行环境。
	单个进程可能包含若干个线程，这些线程都“同时”（时间片）执行进程地址空间中的代码。每个进程至少拥有一个线程。当创建一个进程时，操作系统会自动创建这个进程的第一个线程，称为主线程，也就是执行主函数的线程。此后主线程可以创建其他线程。
	线程只有一个内核对象和一个栈，保留的记录很少，因此所需要的内存也很少。此外当进程间切换时，需要交换整个地址空间，而线程之间的切换只是执行环境的改变，因此效率比较高，因此在编程中经常采用多线程来解决编程问题，而尽量避免创建新的进程。
进程地址空间：系统赋予每个进程独立的虚拟地址空间，对于32位进程来说，这个地址空间是4GB。其中2GB是内核方式分区，供内核代码、设备驱动程序、设备IO高速缓存、非页面内存池的分配和进程页面表等使用，而用户方式分区使用的地址空间约为2GB，这个分区是进程的私有地址空间所在的地方。
互斥对象：一般来说，对多线程程序，如果这些线程需要访问共享资源，就需要进行线程间的同步处理。互斥对象属于内核对象，它能够确保线程拥有对单个资源的互斥访问权。互斥对象包含一个使用数量，一个线程ID和一个计数器。其中ID用于标识系统中哪个线程当前拥有互斥对象，计数器用于指明该线程拥有互斥对象的次数。使用CreateMutex和ReleaseMutex函数来创建和释放互斥对象。线程必须主动请求共享对象的使用权才有可能获得该所有权，这可以通过WaitForSingleObject函数来实现，在调用WaitForSingleObject函数后，该函数会一直等待，这样就会暂停线程的执行，只有在两种情况下才会返回：
	1）指定的对象变成有信号状态；
	2）指定的等待时间间隔已过；
	可以通过其返回值判断引起函数返回的事件。
同一个线程多次拥有互斥对象的情况：操作系统通过互斥对象内部的计数器来维护同一个线程请求到该互斥对象的次数，当调用WaitForSingleObject请求互斥对象时，操作系统需要判断当前请求互斥对象的线程的ID是否与互斥对象当前拥有者的线程ID相等，如果相等，即使该互斥对象处于未通知状态，调用线程任然能够获得其所有权。操作系统通过互斥对象内部的计数器来维护同一个线程请求到该互斥对象的次数，如果多次在同一个线程中请求同一个互斥对象，则需要相应的多次调用ReleaseMutex函数释放该互斥对象。
代码分析：模拟售票系统
#include <windows.h>
#include <iostream.h>
	//声明线程入口函数原型，线程入口函数名称可变，但函数类型必须遵照以下形式
DWORD WINAPI Fun1Proc(  LPVOID lpParameter ); 
DWORD WINAPI Fun2Proc(  LPVOID lpParameter );
int tickets = 100; //要销售的票数还剩下100张，这100张票由Fun1Proc与Fun2Proc两个线程负责销售

HANDLE hMutex;//互斥对象句柄；typedef void *HANDLE;

void main()
{	
	HANDLE  hThread1,hThread2;
	hThread1 = CreateThread(
		NULL,	//使用缺省的安全性
		0,		//指定初始提交栈的大小
		Fun1Proc,//指定线程入口函数地址
		NULL,	//传递给线程的参数
		0,		//附加标记，0表示线程创建后立即运行
		NULL);	//线程ID，在Win98/95中不能设置为NULL
	CloseHandle(hThread1); 
		// 调用CloseHandle函数并没有终止新创建的线程，只是表示在主线程中对新创建的线程的引用不感兴趣，因此将它关闭；当不再需要线程句柄时，应将其关闭，以便系统及时释放资源；
	hThread2=CreateThread(NULL,0,Fun2Proc,NULL,0,NULL);
	CloseHandle(hThread2);
	hMutex = CreateMutex(NULL, TRUE,"MuName");
		//创建一个命名的互斥对象；第二个参数为真，指定了创建这个对象的线程，即主线程获得该对象的所有权，如果主线程不释放该互斥对象，则该对象将处于无信号状态
	if(hMutex)
	{//判断是不是第一次创建的互斥对象
		if(ERROR_ALREADY_EXISTS == GetLastError())
		{//如果为真，说明互斥对象已经创建
			cout<<"已经有一个实例在运行了，只能有一个实例运行同时运行。"<<endl;
			return;
		}
	}
	WaitForSingleObject(hMutex,INFINITE);
		//因为请求的互斥对象线程ID与拥有互斥对象线程ID相同，
		//可以再次请求成功，计数器加1
	ReleaseMutex(hMutex);  //第一次释放，计数器减1，但仍有信号
	ReleaseMutex(hMutex);  //再一次释放，计数器为零

	while(tickets>0)	{ Sleep(4000);}
}
/*------------实现线程入口函数Fun1Proc---------------*/
DWORD WINAPI Fun1Proc(  LPVOID lpParameter )
{	
	while(TRUE)
	{
		WaitForSingleObject(hMutex,INFINITE); //请求互斥对象
		if(tickets>0)
		{Sleep(1);
			cout<<"thread 1 sell ticket: ";
		}
		else  
			break;
		ReleaseMutex(hMutex);

	}
	return 0;
}	///endof Fun1Proc()

/*-----------实现线程入口函数Fun2Proc--------------*/
DWORD WINAPI Fun2Proc(  LPVOID lpParameter )
{	
	while(TRUE)
	{
		WaitForSingleObject(hMutex,INFINITE);
		if(tickets>0)
		{Sleep(1);
			cout<<"thread 2 sell ticket: ";
		}
		else
			break;
		ReleaseMutex(hMutex);
	}
	return 0;
}	
	如果某个线程得到其所需的互斥对象的所有权，完成其代码的运行，但是没有释放互斥对象的所有权就退出之后，操作系统一旦发现该线程已终止，就会将线程所拥有的互斥对象的ID设为0，并将其计数归零。

第16章 线程同步与异步套接字编程
1.事件对象
	事件对象与互斥对象一样也属于内核对象。事件对象有两种不同的类型：
	①人工重置的事件对象：当人工重置的事件对象得到通知时，等待该事件对象的所有线程均变为可调度线程。当线程等待到该对象的所有权之后，需要显示地调用ResetEvent函数手动将该事件对象设为无信号状态；
	②自动重置的事件对象：当一个自动重置的事件对象得到通知时，等待该事件对象的线程中只有一个线程变为可调度线程。当线程得到该对象的所有权之后，系统会自动将该对象设置为无信号状态。
	为了实现线程间的同步，不应该使用人工重置的事件对象，而应该使用自动重置的事件对象。
代码分析：
#include <windows.h>
#include <iostream.h>

int ticket=100;
HANDLE g_hEvent;//保存事件对象句柄

DWORD WINAPI Fun1Proc(LPVOID lpParameter);
DWORD WINAPI Fun2Proc(LPVOID lpParameter);

void main()
{
	HANDLE thread1=CreateThread(NULL,0,Fun1Proc,NULL,0,NULL);
	HANDLE thread2=CreateThread(NULL,0,Fun2Proc,NULL,0,NULL);
	
	CloseHandle(thread1);
	CloseHandle(thread2);
	//创建事件对象,可用命名事件对象来控制只运行一个实例
	g_hEvent=CreateEvent(NULL,
		FALSE,	//TRUE人工重置,FALSE 自动重置
		FALSE,	//初始化状态，TURE信号状态，FALSE非信号状态
		"tickets");	//事件对象命名，NULL表示匿名
	if(g_hEvent)
	{
		if(ERROR_ALREADY_EXISTS == GetLastError())
		{
			cout<<"Only one instance can run!"<<endl;
			return;
		}
	}
	SetEvent(g_hEvent);//将事件设置为有信号状态
	Sleep(4000);
	CloseHandle(g_hEvent);
	
}
DWORD WINAPI Fun1Proc(LPVOID lpParameter)
{
	WaitForSingleObject(g_hEvent,INFINITE);
	while(ticket)
	{
		cout<<"thread1 sells : "<<ticket--<<endl;
		Sleep(1);
		SetEvent(g_hEvent);
	}
	return 0;
}
DWORD WINAPI Fun2Proc(LPVOID lpParameter)
{
	WaitForSingleObject(g_hEvent,INFINITE);
	while(ticket)
	{
		cout<<"thread2 sells : "<<ticket--<<endl;
		Sleep(1);
		SetEvent(g_hEvent);
	}
	return 0;
}

2.关键代码段
	关键代码段也称为临界区，工作在用户方式下。它是指一个小代码段，在代码能够执行前，它必须独占对某些资源的访问权。通常把多线程中访问同一种资源的那部分代码当做关键代码段。
代码分析：
#include "windows.h"
#include "iostream.h"

int ticket=100;
//HANDLE g_hEvent;

DWORD WINAPI Fun1Proc(LPVOID lpParameter);
DWORD WINAPI Fun2Proc(LPVOID lpParameter);

CRITICAL_SECTION  g_cs;//临界区对象
void main()
{
	HANDLE thread1=CreateThread(NULL,0,Fun1Proc,NULL,0,NULL);
	HANDLE thread2=CreateThread(NULL,0,Fun2Proc,NULL,0,NULL);
	
	CloseHandle(thread1);
	CloseHandle(thread2);
//初始化一个临界区对象
	InitializeCriticalSection(&g_cs);
	
	Sleep(4000);
// 释放一个没有被占有的临界区对象的所有资源
	DeleteCriticalSection(&g_cs);
	
}
DWORD WINAPI Fun1Proc(LPVOID lpParameter)
{ 
	while(TRUE)
	{
		EnterCriticalSection(&g_cs);//等待临界区对象的所有权
			//当调用线程赋予所有权进，本函数返回
			// 如果一直没能等待到，那么导致线程暂停
		if(ticket>0)
		{
			cout<<"thread1 sells : "<<ticket--<<endl;
			Sleep(1);
		}
		else break;
		LeaveCriticalSection(&g_cs);//释放指定临界区对象所有权
	}
	return 0;
}

DWORD WINAPI Fun2Proc(LPVOID lpParameter)
{ 
	while(TRUE)
	{
		EnterCriticalSection(&g_cs);
		if(ticket>0)
		{
			cout<<"thread2 sells : "<<ticket--<<endl;
			Sleep(1);
		}
		else break;
		LeaveCriticalSection(&g_cs);
	}
	return 0;
}
死锁：如果线程1拥有了临界区对象A，等待临界区对象B的拥有权，线程二拥有了临界区对象B，等待临界区对象A的拥有权，这样就造成了死锁。

互斥对象、事件对象与关键代码段的比较：
1.互斥对象和事件对象都属于内核对象，利用内核对象进行线程同步时，速度较慢，但利用互斥对象和事件对象这样的内核对象，可以在多个进程中的各个线程间进行同步。
2.关键代码段工作在用户方式下，同步速度较快，但在使用关键代码段时，很容易进入死锁状态，因为在等待进入关键代码段时无法设定超时值。

基于消息的异步套接字：
Windows套接字在两种模式下执行IO操作：阻塞模式和非阻塞模式。Windows Sockets为了支持Windows消息驱动机制，使应用程序开发者能够方便地处理网络通信，它对网络事件采用了基于消息的异步存储策略，即采用非阻塞方式实现网络应用程序。异步选择函数WSAAsyncSelect提供了消息机制的网络事件选择，当使用它登记的网络事件发生时，Windows应用程序响应的窗口函数将收到一个消息，消息中指示了发生的网络事件以及一些相关信息。

第17章 进程间通信
1.剪贴板
剪贴板实际上是系统维护管理的一块内存区域。如果某个程序已经打开了剪贴板，则其他应用程序将不能修改剪贴板，知道前者调用了CloseClipboard函数。并且，只有在调用了EmptyClipboard函数之后，打开剪贴板的当前窗口才拥有剪贴板。
数据发送：
void CClipboardDlg::OnBtnSend() 
{
	if(OpenClipboard())
	{
		CString str;
		HANDLE hClip; //内存对象句柄；内存是会移动的（操作系统移动）只能用句柄标识
		char *pBuf;
		EmptyClipboard();//清空剪贴板并释放剪贴板中数据的句柄，然后将剪贴板的所有权分配给当前打开剪贴板的窗口
		GetDlgItemText(IDC_EDIT_SEND,str);
		//分配一个内存对象
		hClip = GlobalAlloc(GMEM_MOVEABLE, str.GetLength()+1);
		//对一个内存地址加锁并返回内存地址
		pBuf = (char *) GlobalLock(hClip); //将内存对象句柄转换为指针
		//将数据拷贝到内存中
		strcpy(pBuf,str);
		GlobalUnlock(hClip);
		SetClipboardData(CF_TEXT,hClip); //
		CloseClipboard();//记住要关闭剪贴板
	}
}
注：一般情况下在编程的时候，给应用程序分配的内存都是可以移动的或者是可以丢弃的，这样能使有限的内存资源充分利用，所以，在某一个时候我们分配的那块内存的地址是不确定的，因为他是可以移动的，所以得先锁定那块内存块，这样应用程序才能存取这块内存。使用GlobalLock的目的是为了保证内存管理时真的是用内存而不是“虚拟内存的磁盘镜像”，否则效率会降低。
数据接收：
void CClipboardDlg::OnBtnRecv() 
{
	if(OpenClipboard())
	{//因为在接收端只需从剪贴板中得到数据，而不用向剪贴板中写入数据，所以不要调用EmptyClipboard；
		if(IsClipboardFormatAvailable(CF_TEXT))//检查剪贴板中是否有想要的特定格式的数据
		{
			HANDLE hClip;
			hClip = GetClipboardData(CF_TEXT);
			char *pBuf;
			pBuf = (char*)GlobalLock(hClip);
			GlobalUnlock(hClip);//将内存解锁
			SetDlgItemText(IDC_EDIT_RECV,pBuf);
			CloseClipboard();
		}
	}
}

2.匿名管道
匿名管道是一个未命名的单向管道，通常用来在一个父进程和一个子进程之间传输数据。匿名管道只能实现本地机器上两个进程间的通信，而不能实现跨网络的通信。因为匿名管道只能在父子进程之间进行通信，子进程如果想要获得匿名管道的句柄，只能从父进程继承而来。当一个子进程从其父进程继承了匿名管道的句柄之后，这两个进程就可以通过该句柄进行通信了。

父进程的实现：
定义两个HANDLE变量：m_hRead, m_hWrite
1.创建匿名管道
void CParentView::OnPipeCreate() 
{
	SECURITY_ATTRIBUTES sa;//定义安全属性结构体
	sa.bInheritHandle = TRUE; //子进程可以继承父进程创建的匿名管道的读写句柄
	sa.lpSecurityDescriptor = NULL;
	sa.nLength = sizeof(SECURITY_ATTRIBUTES);
	if(!CreatePipe(&m_hRead,&m_hWrite,&sa,0))
	{
		MessageBox ("创建匿名管道失败！");
		return ;
	}
//匿名管道创建成功则启动子进程并将其读、写句柄传递给子进程
	STARTUPINFO sui;//用来指定新进程主窗口如何出现的结构体
	PROCESS_INFORMATION pi;//进程信息结构体
	ZeroMemory(&sui,sizeof(STARTUPINFO));//结构体所有成员置为0，防止未设值的属性拥有随机值
	sui.cb = sizeof(STARTUPINFO);//指定结构体大小
	sui.dwFlags = STARTF_USESTDHANDLES;
	sui.hStdInput = m_hRead;//将标准读取句柄设置为管道读取句柄
	sui.hStdOutput =m_hWrite;//标准写入句柄设置为管道写入句柄
	sui.hStdError = GetStdHandle(STD_ERROR_HANDLE);
		//通过GetStdHandle返回一个父进程的标准错误句柄
	if(!CreateProcess("..\\Child\\Debug\\Child.exe", //启动子进程，并将子进程的标准输入输出句柄设置为匿名管道的读、写句柄
		NULL,//传递命令行参数
		NULL,//进程安全属性
		NULL,//线程安全属性
		TRUE,// handle inheritance flag
		0,	 //创建标记
		NULL,//环境块
		NULL,//当前路径，NULL让子进程与父进程有相同路径
		&sui,//指定新进程主窗口如何出现
		&pi))//用来接收关于新的进程的标识信息
	{//如果创建子进程失败
		CloseHandle (m_hRead);
		CloseHandle (m_hWrite);
		m_hRead=NULL;
		m_hWrite=NULL;
		MessageBox ("创建子进程失败！");
		return;
	}///...endof if 
	else
	{
		/*在创建一个新进程时，系统会为该进程建立一个进程内核对象和一个线程内核对象，而内核对象都有一个使用计数，系统会将这两个对象的使用计数赋初值为1.在CreateProcess函数返回之前会在其内部打开这两个对象，保存某些句柄值，这样这两个内核对象的使用计数就都变成2.*/
		CloseHandle (pi.hProcess);  //关闭所返回的子进程句柄
		CloseHandle (pi.hThread);  //关闭子进程中主线程句柄
	}
}
注：为了让子进程从众多继承的句柄中区分出管道的读写句柄，就必须将子进程的特殊句柄设置为管道的读写句柄。这里将子进程的标准输入输出句柄分别设置为管道的读、写句柄，这样在子进程中，只要得到了标准输入和标准输出句柄，就相当于得到了这个管道的读写句柄。
2.读取数据：
void CParentView::OnPipeRead() 
{
	char buf[100];
	DWORD dwRead;
	if(!ReadFile(m_hRead,buf,100,&dwRead,NULL))
	{
		MessageBox ("读取数据失败！");
		return ;
	}
	else MessageBox (buf);
}
3.写入数据：
void CParentView::OnPipeWrite() 
{
	char buf[]="http://blog.csdn.net/teshorse";
	DWORD dwWrite;
	if(!WriteFile(m_hWrite,buf,strlen(buf)+1,
		&dwWrite,NULL))
	{
		MessageBox ("写入数据失败");
		return ;
	} 
}
对于管道的读取和写入实际上是通过调用ReadFile和WriteFile这两个函数完成的。

子进程的实现：
定义两个HANDLE变量：hRead, hWrite
1.获得管道的读取和写入句柄
void CChildView::OnInitialUpdate() //当窗口成功调用之后第一个创造的函数
{
	CView::OnInitialUpdate();
	//获取子进程的标准输入输出句柄.
	m_hRead =GetStdHandle(STD_INPUT_HANDLE);
	m_hWrite = GetStdHandle(STD_OUTPUT_HANDLE);
}
2.读取数据
void CChildView::OnPipeRead() 
{
	char buf[100];
	DWORD dwRead;
	if(!ReadFile(m_hRead,buf,100,&dwRead,NULL))
	{
		MessageBox ("读取数据失败！");
		return ;
	}
	else MessageBox (buf);
}
3.写入数据
void CChildView::OnPipeWrite() 
{
	char buf[]="匿名管道测试程序";
	DWORD dwWrite;
	if(!WriteFile(m_hWrite,buf,strlen(buf)+1,
		&dwWrite,NULL))
	{
		MessageBox ("写入数据失败");
		return ;
	} 
}
注：匿名管道只能在父子进程之间通信。两个进程如果想要具有父子关系，必须由父进程通过调用CreateProcess函数去启动子进程。因为匿名管道没有名称，所有只能在父进程中调用CreateProcess函数创建子进程时将管道的读、写句柄传递给子进程。
	利用匿名管道也可以实现在同一个进程内读取和写入数据。

3.命名管道
基础知识：
	①命名管道通过网络来完成进程间的通信，它屏蔽了底层的网络协议细节。
	②命名管道充分利用了Windows内建的安全机制，可以指定用户权限。所以区别于Sockets编写网络应用，使用命名管道无需编写验证用户身份的代码。
	③命名管道实际上是建立了一个CS通信体系，并在其中可靠地传输数据。命名管道是围绕Windows文件系统设计的一种机制，采用“命名管道文件系统”接口，因此，客户机和服务器可利用标准Win32文件系统函数来进行数据的收发。
	④命名管道服务器和客户机的区别在于：服务器是唯一一个有权创建命名管道的进程，也只有它才能接受管道客户机的连接请求。而客户机只能同一个现成的命名管道服务器建立连接。命名管道服务器只能在WindowsNT、2000等系统上创建。
	⑤命名管道提供了两种基本通信模式：字节模式和消息模式。在消息模式下通过一系列不连续的数据单位进行数据发送。
	⑥对同一个命名管道的实例来说，在某一时刻，它只能和一个客户端进行通信。
实现过程：在服务器端调用CreateNamePipe创建命名管道之后，调用ConnectNamedPipe函数让服务器端进程等待客户端进程连接到该命名管道的实例上。在客户端首先调用WwaitNamePipe函数判断当前是否有可以利用的命名管道实例，如果有，就调用CreateFile函数打开该命名管道的实例，并建立一个连接。
4.邮槽
	邮槽是基于广播通信体系设计出来的，它采用无连接的不可靠的数据传输。邮槽是一种单向通信机制，创建邮槽的服务器进程读取数据，打开邮槽的客户机进程写入数据。邮槽适用于开发一对多的广播通信系统。




