# AsyncTask的用法 

在开发Android应用时必须遵守单线程模型的原则：Android UI操作并不是线程安全的并且这些操作必须在UI线程中执行。在单线程模型中始终要记住两条法则： 

1. 不要阻塞UI线程 
2. 确保只在UI线程中访问Android UI工具包 
   当一个程序第一次启动时，Android会同时启动一个对应的主线程(Main Thread)，主线程主要负责处理与UI相关的事件，如：用户的按键事件，用户接触屏幕的事件以及屏幕绘图事件，并把相关的事件分发到对应的组件进行处理。所以主线程通常又被叫做UI线程。比如说从网上获取一个网页，在一个TextView中将其源代码显示出来，这种涉及到网络操作的程序一般都是需要开一个线程完成网络访问，但是在获得页面源码后，是不能直接在网络操作线程中调用TextView.setText()的.因为其他线程中是不能直接访问主UI线程成员 。
   android提供了几种在其他线程中访问UI线程的方法。 
```java
Activity.runOnUiThread( Runnable ) 
View.post( Runnable ) 
View.postDelayed( Runnable, long ) 
```
Handler 
这些类或方法同样会使你的代码很复杂很难理解。然而当你需要实现一些很复杂的操作并需要频繁地更新UI时这会变得更糟糕。为了解决这个问题，Android 1.5提供了一个工具类：AsyncTask，它使创建需要与用户界面交互的长时间运行的任务变得更简单。相对来说AsyncTask更轻量级一些，适用于简单的异步处理，不需要借助线程和Handler即可实现。 
AsyncTask是抽象类.AsyncTask定义了三种泛型类型 Params，Progress和Result。 
Params 启动任务执行的输入参数，比如HTTP请求的URL。 
Progress 后台任务执行的百分比。 
Result 后台执行任务最终返回的结果，比如String。 
AsyncTask的执行分为四个步骤，每一步都对应一个回调方法，这些方法不应该由应用程序调用，开发者需要做的就是实现这些方法。 
1) 子类化AsyncTask 
2) 实现AsyncTask中定义的下面一个或几个方法 
onPreExecute(), 该方法将在执行实际的后台操作前被UI thread调用。可以在该方法中做一些准备工作，如在界面上显示一个进度条。 
doInBackground(Params...), 将在onPreExecute 方法执行后马上执行，该方法运行在后台线程中。这里将主要负责执行那些很耗时的后台计算工作。可以调用 publishProgress方法来更新实时的任务进度。该方法是抽象方法，子类必须实现。 
onProgressUpdate(Progress...),在publishProgress方法被调用后，UI thread将调用这个方法从而在界面上展示任务的进展情况，例如通过一个进度条进行展示。 
onPostExecute(Result), 在doInBackground 执行完成后，onPostExecute 方法将被UI thread调用，后台的计算结果将通过该方法传递到UI thread. 

为了正确的使用AsyncTask类，以下是几条必须遵守的准则： 
1) Task的实例必须在UI thread中创建 
2) execute方法必须在UI thread中调用 
3) 不要手动的调用onPreExecute(), onPostExecute(Result)，doInBackground(Params...), onProgressUpdate(Progress...)这几个方法 
4) 该task只能被执行一次，否则多次调用时将会出现异常 
doInBackground方法和onPostExecute的参数必须对应，这两个参数在AsyncTask声明的泛型参数列表中指定，第一个为doInBackground接受的参数，第二个为显示进度的参数，第三个为doInBackground返回和onPostExecute传入的参数。
最后需要说明AsyncTask不能完全取代线程，在一些逻辑较为复杂或者需要在后台反复执行的逻辑就可能需要线程来实现了。