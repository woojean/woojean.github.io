# 检查当前浏览器是否支持某个Javascript方法

直接调用不带括号的方法名，如果方法存在则返回true，否则为false：
if(!getElementById) return false;