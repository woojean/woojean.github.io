# JavaScript中的DOM节点有哪些类型？DOM节点有哪些属性和方法？

JavaScript 中的所有节点类型都继承自Node 类型，因此所有节点类型都共享着相同的基本属性和方法。每个节点都有一个 nodeType 属性，用于表明节点的类型。节点类型由在 Node 类型中定义的下列12 个数值常量来表示（即一共有12种节点类型）：
Node.ELEMENT_NODE (1)；
Node.ATTRIBUTE_NODE (2)；
Node.TEXT_NODE (3)；
Node.CDATA_SECTION_NODE (4)；
Node.ENTITY_REFERENCE_NODE (5)；
Node.ENTITY_NODE (6)；
Node.PROCESSING_INSTRUCTION_NODE (7)；
Node.COMMENT_NODE (8)；
Node.DOCUMENT_NODE (9)；
Node.DOCUMENT_TYPE_NODE (10)；
Node.DOCUMENT_FRAGMENT_NODE (11)；
Node.NOTATION_NODE (12)。
并不是所有节点类型都受到 Web 浏览器的支持。

nodeType // 节点类型
nodeName // 元素的标签名
nodeValue //
childNodes // 保存着一个NodeList对象（类数组对象，有length属性，可以用[]访问，但是不是数组）。NodeList 对象的独特之处在于，它实际上是基于 DOM 结构动态执行查询的结果，因此 DOM 结构的变化能够自动反映在 NodeList 对象中。父节点的 firstChild 和 lastChild属性分别指向其 childNodes 列表中的第一个和最后一个节点。
一般来说，应该尽量减少访问 NodeList 的次数。因为每次访问 NodeList ，都会运行一次基于文档的查询。所以，可以考虑将从 NodeList 中取得的值缓存起来。

parentNode //
ownerDocument // 指向表示整个文档的文档节点

hasChildNodes()
appendChild() // 向 childNodes 列表的末尾添加一个节点。如果传入到 appendChild() 中的节点已经是文档的一部分了，那结果就是将该节点从原来的位置转移到新位置。任何 DOM 节点不能同时出现在文档中的多个位置上。因此，如果在调用 appendChild() 时传入了父节点的第一个子节点，那么该节点就会成为父节点的最后一个子节点。
insertBefore()
replaceChild()
removeChild()
cloneNode() //接受一个布尔值参数，表示是否执行深复制。不会复制添加到 DOM 节点中的 JavaScript 属性，例如事件处理程序等。这个方法只复制特性、（在明确指定的情况下也复制）子节点，其他一切都不会复制。
normalize() // 处理文档树中的文本节点，由于解析器的实现或 DOM 操作等原因，可能会出现文本节点不包含文本，或者接连出现两个文本节点的情况。当在某个节点上调用这个方法时，就会在该节点的后代节点中查找上述两种情况。如果找到了空文本节点，则删除它；如果找到相邻的文本节点，则将它们合并为一个文本节点。