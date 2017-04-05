# View, surfaceView, GLSurfaceView有什么区别

View是最基础的，必须在UI主线程内更新画面，速度较慢。
SurfaceView 是view的子类，类似使用双缓机制，在新的线程中更新画面所以刷新界面速度比view快
GLSurfaceView 是SurfaceView的子类，openGL 专用的