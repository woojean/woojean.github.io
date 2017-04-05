# Context相关类的继承关系

Context本身是抽象类，ContextWrapper和ContextImpl直接继承于Context类。ContextImpl真正实现了Context中的所有函数，应用程序中所调用的各种Context类的方法其实现均来自于该类。ContextWrapper的构造函数中必须包含一个真正的Context引用（ContextImpl），可以使用attachBaseContext()给ContextWrapper对象指定真正的Context对象，调用ContextWrapper的方法都会被转向其所包含的真正的Context对象。Service类直接继承自ContextWrapper，但是Activity类继承自ContextThemeWrapper，而ContextThemeWrapper则直接集成自ContextWrapper。
每一个应用程序在客户端都是从ActivityThread类开始的，创建Context对象也是在该类中完成，具体创建ContextImpl类的地方一共有7处：
1）在PackageInfo.makeApplication()中；
2）在performLaunchActivity()中；
3）在handleCreateBackupAgent()中；
4）在handleCreateService()中；
5）在handleBindApplication()中；（有2处）
6）在attach()方法中；该方法仅在Framework进程（system_server）启动时调用，应用程序运行时不会调用到该方法；

Application对应的Context
每个应用程序在第一次启动时都会首先创建一个Application对象，默认为应用程序的包名。程序第一次启动时会辗转调用到handleBindApplication()方法中，该方法中有两处创建了ContextImpl对象，但都是在if( data.instrumentationName!=null )条件中，如果不是测试工程的话，则调用makeApplication()方法：
```java	
Application app = data.info.makeApplication( data...
而在makeApplication()方法中，主要包含以下代码：
ContextImpl appContext = new ContextImpl();
appContext.init( this, null, mActivityThread );
// 参数this指的就是当前PackageInfo对象，该对象将赋值给ContextImpl类中的重要成员变量mPackageInfo
app = mActivityThread.mInstrumentation.newApp( ...appClass, appContext);
appContext.setOuterContext( app);
```

Activity对应的Context
启动Activity时，AmS会通过IPC调用到ActivityThread的scheduleLaunchActivity()方法，该方法包含的参数中包括一个ActivityInfo类型的参数，这是一个实现了Parcelable接口的数据类，意味着该对象是AmS创建的，并通过IPC传递到ActivityThread。scheduleLaunchActivity()方法中会构造一个本地ActivityRecord数据类，ActivityThread内部会为每一个Activity创建一个ActivityRecord对象，并使用这些数据来管理Activity。接着会调用handleLaunchActivity()，然后会调用performLaunchActivity()，该方法中创建ContextImpl的代码如下：
```java
ContextImpl appContext = new ContextImpl();
appContext.init( r.packageInfo, r.token, this );
appContext.setOuterContext( activity );
r.packageInfo对象的PackageInfo对象和Application对应的packageInfo对象是同一个。
```

Service对应的Context
启动Service时，AmS首先会通过IPC调用到ActivityThread的scheduleCreateService()方法，该方法也包含一个ServiceInfo类型的参数，该参数同样实现了Parcelable接口的数据类，意味着该对象由AmS创建，并通过IPC传递到ActivityThread内部。在scheduleCreateService()方法中会构造一个CreateServiceData()数据对象，并通过其来管理Service。接着会执行handleCreateService()方法，其中创建ContextImpl对象的代码如下：
```java
ContextImpl context = new ContextImpl();
context.init( packageInfo, null, this );
Application app = packageInfo.makeApplication( false,...
context.setOuterContext( service );
```

Context之间的关系
可见创建Context对象的过程基本上是相同的，包括代码的结构也十分类似，所不同的仅仅是针对Application、Activity、Service使用了不同的数据对象，不同Context子类中PackageInfo对象的来源总结为：
Application的远程数据类为ApplicationInfo，本地数据类为AppBindData，赋值方式是通过getPackageInfoNoCheck()；
Activity的远程数据类为ActivityInfo，本地数据类为ActivityRecord，赋值方式是通过getPackageInfo ()；
Service的远程数据类为ServiceInfo，本地数据类为CreateServiceData，赋值方式是通过getPackageInfo NoCheck() ()；
由此可见一个应用程序包含的Context个数为：
Context个数 = Service个数 + Activity个数 + 1；
因此，应用程序中包含多个ContextImpl对象，但其内部变量mPackageInfo却指向同一个PackageInfo对象，这种设计表明ContextImpl是一个轻量级类，而PackageInfo是一个重量级类，ContextImpl中的大多数重量级函数实际上都是转向了mPackageInfo对象相应的方法。