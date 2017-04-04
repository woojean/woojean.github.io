# 外部js文件执行顺序

如果在<head>部分引用js文件，则会造成文档未加载，js无法操作DOM的情况。即使在</body>之前引用js，也不能确保js一定能够操作DOM，因为js加载时文档可能不完整。文档被加载到一个浏览器窗口里，document对象又是window对象的一个属性，因此当window对象触发onload事件时，document对象已经存在，所以为了确保js能够操作DOM，应该把相关方法绑定到window的onload事件上：
window.onload = foo;
function foo(){...}