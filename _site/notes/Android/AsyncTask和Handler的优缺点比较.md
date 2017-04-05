# AsyncTask和Handler的优缺点比较

Android之所以有Handler和AsyncTask，都是为了不阻塞主线程（UI线程），且UI的更新只能在主线程中完成，因此异步处理是不可避免的。Android为了降低这个开发难度，提供了AsyncTask。AsyncTask就是一个封装过的后台任务类，顾名思义就是异步任务。
AsyncTask实现的原理和适用的优缺点：AsyncTask,是android提供的轻量级的异步类,可以直接继承AsyncTask,在类中实现异步操作,并提供接口反馈当前异步执行的程度(可以通过接口实现UI进度更新),最后反馈执行的结果给UI主线程.
使用的优点:简单,快捷、过程可控      
使用的缺点:在使用多个异步操作和并需要进行Ui变更时,就变得复杂起来。

Handler异步实现的原理和适用的优缺点：在Handler 异步实现时,涉及到 Handler, Looper, Message,Thread四个对象，实现异步的流程是主线程启动Thread（子线程）运行并生成Message-Looper获取Message并传递给HandlerHandler逐个获取Looper中的Message，并进行UI变更。
使用的优点：结构清晰，功能定义明确；对于多个后台任务时，简单，清晰
使用的缺点：在单个后台异步处理时，显得代码过多，结构过于复杂（相对性）
Android的AsyncTask比Handler更轻量级一些（只是代码上轻量一些，而实际上要比handler更耗资源），适用于简单的异步处理。