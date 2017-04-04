# document对象的类型是什么？有哪些属性和方法？

document 对象是 HTMLDocument （继承自Document类型）的一个实例，表示整个 HTML 页面。而且， document 对象是 window 对象的一个属性，因此可以将其作为全局对象来访问。
nodeType 的值为 9
nodeName 的值为 "#document"
nodeValue 的值为 null
parentNode 的值为 null
documentElement // 该属性始终指向 HTML 页面中的 <html> 元素
body // 直接指向 <body> 元素
doctype // 取得对<!DOCTYPE>的引用
title //包含着<title> 元素中的文本，修改 title 属性的值不会改变 <title>元素
URL // 页面完整的 URL
domain // 页面的域名
当页面中包含来自其他子域的框架或内嵌框架时，能够设置 document.domain 就非常方便了。由
于 跨 域 安 全 限 制 ， 来 自 不 同 子 域 的 页 面 无 法 通 过 JavaScript 通 信 。 而 通 过 将 每 个 页 面 的document.domain 设置为相同的值，这些页面就可以互相访问对方包含的 JavaScript 对象了。例如，假设有一个页面加载自 www.wrox.com，其中包含一个内嵌框架，框架内的页面加载自 p2p.wrox.com。由于 document.domain 字符串不一样，内外两个页面之间无法相互访问对方的 JavaScript 对象。但如果将这两个页面的 document.domain 值都设置为 "wrox.com" ，它们之间就可以通信了。
referrer // 链接到当前页面的那个页面的 URL
document.anchors // 文档中所有带 name 特性的 <a> 元素
document.forms // 文档中所有的 <form> 元素，与 document.getElementsByTagName("form")得到的结果相同
getElementById() // 如果页面中多个元素的 ID 值相同，只返回文档中第一次出现的元素
document.links // 包含文档中所有带 href 特性的 <a> 元素
implementation // 检测浏览器实现了 DOM 的哪些部分
getElementsByTagName() // 返回一个 HTMLCollection 对象，作为一个“动态”集合，该对象与 NodeList 非常类似
getElementsByName()
createElement()

所有 HTML 元素都是由 HTMLElement 或者其更具体的子类型来表示的。如：
A HTMLAnchorElement
BODY HTMLBodyElement
BUTTON HTMLButtonElement
DIV HTMLDivElement