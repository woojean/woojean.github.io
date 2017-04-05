# event对象有哪些属性和方法？currentTarget和target有什么区别？

在触发 DOM 上的某个事件时，会产生一个事件对象 event（无论指定事件处理程序时使用什
么方法）
btn.onclick = function(event){
alert(event.type); //"click"
};

btn.addEventListener("click", function(event){
alert(event.type); //"click"
}, false);

event 对象包含与创建它的特定事件有关的属性和方法。触发的事件类型不一样，可用的属性和方
法也不一样。不过，有一些公共的成员:
bubbles  表明事件是否冒泡
cancelable   表明是否可以取消事件的默认行为
currentTarget   其事件处理程序当前正在处理事件的那个元素
defaultPrevented   为 true 表 示 已 经 调 用 了 preventDefault()
preventDefault()   取消事件的默认行为。如果 cancelable 是true ，则可以使用这个方法
stopImmediatePropagation()  取消事件的进一步捕获或冒泡，同时阻止任何事件处理程序被调用
stopPropagation()   取消事件的进一步捕获或冒泡。如果 bubbles为 true ，则可以使用这个方法
target   事件的目标
trusted   为 true 表示事件是浏览器生成的。为 false 表示 事 件 是 由 开 发 人 员 通 过 JavaScript 创 建 的
type   被触发的事件的类型
view   与事件关联的抽象视图。等同于发生事件的window 对象
eventPhase 事件当前正位于事件流的哪个阶段。捕获阶段eventPhase为1 ；事件处理程序处于目标对象上，为2 ；在冒泡阶段调用的事件处理程序，为3 。要注意的是，尽管“处于目标”发生在冒泡阶段，但 eventPhase 仍然一直等于 2 。


在事件处理程序内部，对象 this 始终等于 currentTarget 的值，而 target 则只包含事件的实际目标。如果直接将事件处理程序指定给了目标元素，则 this 、 currentTarget 和 target 包含相同的值：
var btn = document.getElementById("myBtn");
btn.onclick = function(event){
alert(event.currentTarget === this); //true
alert(event.target === this); //true
};
如果事件处理程序存在于按钮的父节点中（例如 document.body ），那么这些值是不相同的：
document.body.onclick = function(event){
alert(event.currentTarget === document.body); //true
alert(this === document.body); //true
alert(event.target === document.getElementById("myBtn")); //true
};
this 和 currentTarget 都等于 document.body ，因为事件处理程序是注册到这个元素上的。然而， target 元素却等于按钮元素，因为它是 click 事件真正的目标。由于按钮上并没有注册事件处理程序，结果 click 事件就冒泡到了 document.body ，在那里事件才得到了处理。