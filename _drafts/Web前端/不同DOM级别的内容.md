# 不同DOM级别的内容

实际上，DOM0 级标准是不存在的；所谓 DOM0 级只是 DOM 历史坐标中的一个参照点而已。具体说来，DOM0 级指的是 Internet Explorer 4.0 和 Netscape Navigator 4.0最初支持的 DHTML。

DOM1 级由两个模块组成：DOM核心（DOM Core）和 DOM HTML。其中，DOM 核心规定的是如何映射基于 XML 的文档结构，以便简化对文档中任意部分的访问和操作。DOM HTML 模块则在 DOM 核心的基础上加以扩展，添加了针对HTML的对象和方法。

DOM2 级在原来 DOM 的基础上又扩充了（DHTML 一直都支持的）鼠标和用户界面事件、范围、遍历（迭代 DOM
文档的方法）等细分模块，而且通过对象接口增加了对 CSS（Cascading Style Sheets，层叠样式表）的支持。DOM1 级中的 DOM 核心模块也经过扩展开始支持 XML 命名空间。

DOM3 级则进一步扩展了 DOM，引入了以统一方式加载和保存文档的方法——在 DOM 加载和保存（DOM Load and Save）模块中定义；新增了验证文档的方法——在 DOM 验证（DOM Validation）模块中定义。DOM3 级也对 DOM 核心进行了扩展，开始支持 XML 1.0 规范，涉及 XML Infoset、XPath和 XML Base。