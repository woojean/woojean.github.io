# 用AsyncTask更新进度条

功能实现主要基于这样的现实：AsyncTask类有一个运行在单独线程中的publishProgress方法和配套的运行在主线程中的onProgressUpdate方法。

```java
final ProgressBar progressBar = ...
progressBar.setMax(100);

// AsyncTask的三个类型参数分别是：输入、更新、返回
AsyncTask<Integer,Integer,Void> task = new AsyncTask<Integer,Integer,Void>(){
public void doInBackground(Integer ... params){
for(Integer progress:params){
publishProgress(progress);
Thread.sleep(1000);
}
}
public void onProgressUpdate(Integer...params){
int progress = params[0];
progressBar.setProgress(progress);
}
}
```
AsyncTask类是一个轻量级的多线程解决方案，主要应用于那些短暂且较少重复的任务。如果创建了大量的AsyncTask，或者长时间运行了AsyncTask，那么很可能是做出了错误的选择。此外，自Android3.2起，AsyncTask不再为每一个AsyncTask实例单独创建一个线程，而是使用一个Executor在单一的线程上运行所有的AsyncTask后台任务，意味着每个AsyncTask都需要排队逐个运行。