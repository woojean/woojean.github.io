#《React文档》阅读摘要.md

## 使用react、react-dom、babel在线转换JSX并执行：
```
// 使用<script type="text/babel">实现在线转换、执行JSX
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>Hello React!</title>
    <script src="build/react.js"></script>
    <script src="build/react-dom.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/babel-core/5.8.23/browser.min.js"></script>
  </head>
  <body>
    <div id="example"></div>
    <script type="text/babel">
      ReactDOM.render(
        <h1>Hello, world!</h1>,
        document.getElementById('example')
      );
    </script>
  </body
</html>
```

## 离线转换JSX并执行
npm install -g react-tools

在src/目录下新建helloworld.js文件，内容为JSX：
ReactDOM.render(
  <h1>Hello, world!</h1>,
  document.getElementById('example')
);

执行 jsx --watch src/ build/

这样，当src/helloworld.js文件发生变化时，将自动生成原生js的build/helloworld.js文件。



## 深入理解 React
通过一个示例阐述了使用React开发应用的思路或者说设计风格：
1.将用户界面（UI原型图）拆分成组件树；（单一功能原则：理想状态下一个组件应该只做一件事）
2.组件树中的父组件可以重用子组件；
3.通过props从父组件向子组件传值；
4.识别出最小的、但是完整的代表UI的state集合；为了判断一个数据是否应该设为state，可以问以下3个问题：
  （1）是否是从父级通过props传入的？如果是，可能不是state。
  （2）是否会随着时间改变？如果不是，可能不是state。
  （3）能根据组件中其它state数据或者props计算出来吗？如果是，就不是state。
5.指出哪个组件会改变或者说拥有这个state数据模型；对于应用中的每一个state数据：
  （1）找出每一个基于那个state渲染界面的组件；
  （2）找出共同的祖先组件（某个单个的组件，在组件树中位于需要这个state的所有组件的上面）；
  （3）要么是共同的祖先组件，要么是另外一个在组件树中位于更高层级的组件应该拥有这个state；
  （4）如果找不出拥有这个state数据模型的合适的组件，创建一个新的组件来维护这个state，然后添加到组件树中，层级位于所有共同拥有者组件的上面；
6.基于state的一系列生命周期函数实现当state改变时相应的UI变化；
7.使用回调函数实现反向的（从子组件流向父组件）数据流动；


## React数据呈现
响应式更新：React通常不会操作DOM，它用一种更快的内置仿造的`虚拟DOM`来操作差异（基于React Diff算法）。
组件的属性是不可直接改变的，也就是说this.props是只读的。

React认为标签和生成它的代码是紧密相连的，展示逻辑通常是很复杂的，使用“模板引擎”和“展示逻辑”会产生大量代码，使问题变得复杂。React认为解决这个问题最好的方案是通过JavaScript直接生成模板，为此做了一个非常简单、`可选的`、类似HTML语法 ，通过函数调用即可生成模板的编译器，称为JSX。

React可以渲染HTML标签(strings)或React组件(classes)。
HTML标签在JSX里约定以小写字母开头：
```
var myDivElement = <div className="foo" />;
React.render(myDivElement, document.body);
```
React组件在JSX里约定以大写字母开头：
```
var MyComponent = React.createClass({/*...*/});
var myElement = <MyComponent someProperty={true} />;
React.render(myElement, document.body);
```
由于JSX就是JavaScript，一些标识符像 class 和 for 不建议作为 XML 属性名。作为替代，React DOM 使用 className 和 htmlFor 来做对应的属性。

JSX 把类 XML 的语法转成纯粹 JavaScript，XML 元素、属性和子节点被转换成 React.createElement 的参数。
var Nav;
输入(JSX)：
var app = <Nav color="blue" />;   
输出(JS):
var app = React.createElement(Nav, {color:"blue"});

要使用JavaScript表达式作为属性值，只需把这个表达式用一对大括号 ({}) 包起来（不要用引号 ("")）：
输入 (JSX):
var person = <Person name={window.isLoggedIn ? window.name : ''} />;
输出 (JS):
var person = React.createElement(
  Person,
  {name: window.isLoggedIn ? window.name : ''}
);

JavaScript 表达式也可用于描述子结点：
输入 (JSX):
var content = <Container>{window.isLoggedIn ? <Nav /> : <Login />}</Container>;
输出 (JS):
var content = React.createElement(
  Container,
  null,
  window.isLoggedIn ? React.createElement(Nav) : React.createElement(Login)
);


不应该修改props，这样是反模式，因为 React 不能帮你检查属性类型（propTypes）。这样即使你的 属性类型有错误也不能得到清晰的错误提示。
```  
  var component = <Component />;
  component.props.foo = x; // 不好
```

JSX支持延展属性：
```
  var props = {};
  props.foo = x;
  props.bar = y;
  var component = <Component {...props} />;
```

## React富交互性的动态用户界面的实现
React里只需把事件处理器（event handler）以骆峰命名（camelCased）形式当作组件的props传入即可，就像使用普通HTML那样。React 内部创建一套`合成事件系统`来使所有事件在IE8和以上浏览器表现一致。即，React知道如何冒泡和捕获事件。

在幕后，React 做了一些操作来让代码高效运行且易于理解。
Autobinding: 在 JavaScript 里创建回调的时候，为了保证 this 的正确性，一般都需要显式地绑定方法到它的实例上。有了 React，`所有方法被自动绑定到了它的组件实例上`。React 还缓存这些绑定方法，所以 CPU 和内存都是非常高效。

事件代理 ： React 实际并没有把事件处理器绑定到节点本身。当 React 启动的时候，它在最外层使用唯一一个事件监听器处理所有事件。当组件被加载和卸载时，只是在内部映射里添加或删除事件处理器。当事件触发，React 根据映射来决定如何分发。当映射里处理器时，会当作空操作处理。

React `把用户界面当作简单状态机`。把用户界面想像成拥有不同状态然后渲染这些状态，React 来决定如何最高效地更新 DOM,可以轻松让用户界面和数据保持一致。

常用的通知 React 数据变化的方法是调用 setState(data, callback)。这个方法会`合并`（merge） data 到 this.state，并重新渲染组件。渲染完成后，调用可选的 callback 回调。

### 复合组件
`拥有者`就是给其它组件设置 props 的那个组件，被拥有组件与其拥有者之间是从属关系，这种从属关系与DOM标签的父子关系是不同的。
拥有者能通过专门的 this.props.children props 读取子级。this.props.children 是一个不透明的数据结构： 通过 React.Children 工具类 来操作。

### 子级校正（Reconciliation）
校正就是每次 render 方法调用后 React 更新 DOM 的过程。 一般情况下，子级会根据它们被渲染的顺序来做校正。例如，下面代码描述了两次渲染的过程：
```
// 第一次渲染
<Card>
  <p>Paragraph 1</p>
  <p>Paragraph 2</p>
</Card>
```
```
// 第二次渲染
<Card>
  <p>Paragraph 2</p>
</Card>
```
直观来看，只是删除了<p>Paragraph 1</p>。`事实上，React 先更新第一个子级的内容，然后删除最后一个组件`。React 是根据子级的顺序来校正的。
对于使用 this.state 来在多次渲染过程中里维持数据的状态化组件，这样做潜在很多问题。多数情况下，可以通过隐藏组件而不是删除它们来绕过这些问题。

### 动态子级
如果子组件位置会改变（如在搜索结果中）或者有新组件添加到列表开头（如在流中）情况会变得更加复杂。`如果子级要在多个渲染阶段保持自己的特征和状态，可以通过给子级设置惟一标识的 key 来区分`。当 React 校正带有 key 的子级时，它会确保它们被重新排序（而不是破坏）或者删除（而不是重用）。

### 可复用组件
React.PropTypes 提供很多验证器 (validator) 来验证传入数据的有效性。当向 props 传入无效数据时，JavaScript 控制台会抛出警告。`注意为了性能考虑，只在开发环境验证 propTypes`：
```
React.createClass({
  propTypes: {
    // 可以声明 prop 为指定的 JS 基本类型。默认
    // 情况下，这些 prop 都是可传可不传的。
    optionalArray: React.PropTypes.array,
    optionalBool: React.PropTypes.bool,
    optionalFunc: React.PropTypes.func,
    ...
```

React 支持以声明式的方式来定义 props 的默认值。
```
var ComponentWithDefaultProps = React.createClass({
  getDefaultProps: function() {
    return {
      value: 'default value'
    };
  }
  /* ... */
});
```

### Mixins
组件是 React 里复用代码最佳方式，但是有时一些复杂的组件间也需要共用一些功能。有时会被称为`跨切面关注点`。React 使用 mixins 来解决这类问题。

如果一个组件使用了多个 mixin，并且有多个 mixin 定义了同样的生命周期方法（如：多个 mixin 都需要在组件销毁时做资源清理操作），所有这些生命周期方法都保证会被执行到。方法执行顺序是：首先按 mixin 引入顺序执行 mixin 里方法，最后执行组件内定义的方法。



## React在浏览器中的工作原理
React在内存中维护一个快速响应的DOM描述。render()方法返回一个DOM的描述，React能够利用内存中的描述来快速地计算出差异，然后更新浏览器中的DOM。

另外，React实现了一个完备的虚拟事件系统，尽管各个浏览器都有自己的怪异行为，React确保所有事件对象都符合W3C规范，并且持续冒泡，用一种高性能的方式跨浏览器（and everything bubbles consistently and in a performant way cross-browser）。甚至可以在IE8中使用一些HTML5的事件！

### React调用DOM API
可以调用React.findDOMNode(component)函数来获取该组件的DOM结点。findDOMNode()仅在挂载的组件上有效（也就是说，组件已经被放进了DOM中）

可以使用`this`来得到当前的React组件，或者使用`refs`来指向一个当前组件拥有的组件:
React.findDOMNode(this.refs.myTextInput).focus();


### React组件生命周期

从 render() 中返回的内容并不是实际渲染出来的子组件实例。从 render() 返回的仅仅是子组件层级树实例在特定时间的一个描述：
```
// counterexample: DO NOT DO THIS!
  render: function() {
    var myInput = <input />;          // I'm going to try to call methods on this
    this.rememberThisInput = myInput; // input at some point in the future! YAY!
    return (
      <div>
        <div>...</div>
        {myInput}
      </div>
    );
  }
```
在这个反例中，`<input />` 仅仅是一个 `<input /> `组件的描述。该描述用于创建一个真正的 `<input />` 的支撑实例（ backing instance ）。

React支持一种非常特殊的属性：`ref`，可以用来绑定到 render() 输出的任何组件上去。这个特殊的属性允许引用 render()返回的相应的支撑实例。这样就可以确保在任何时间总是拿到正确的实例:
```
 <input ref="myInput" />
```
在其它代码中（典型地事件处理代码），通过 this.refs 获取支撑实例（ backing instance ），就像这样：
```
this.refs.myInput
```
可以通过调用 this.refs.myInput.getDOMNode() 直接获取到组件的 DOM 节点。从而冲出“本地”组件的限制.

绝不要在任何组件的 render 方法中访问 refs,或者在某个组件的 render 方法正在调用堆栈中运行的时候。


## React API
### 顶层API
ReactClass createClass(object specification)

ReactElement createElement(
  string/ReactClass type,
  [object props],
  [children ...]
)

factoryFunction createFactory(
  string/ReactClass type
)

ReactComponent render(
  ReactElement element,
  DOMElement container,
  [function callback]
)

boolean unmountComponentAtNode(DOMElement container)

string renderToString(ReactElement element)

string renderToStaticMarkup(ReactElement element)

boolean isValidElement(* object)

React.DOM.xxx

React.PropTypes

initializeTouchEvents(boolean shouldUseTouch)

React.Children.map

React.Children.forEach

React.Children.count

React.Children.only


### 组件API
setState(object nextState[, function callback])

replaceState(object nextState[, function callback])

forceUpdate([function callback])

DOMElement getDOMNode()

bool isMounted()

setProps(object nextProps[, function callback])

replaceProps(object nextProps[, function callback])


### 组件的详细说明和生命周期

ReactComponent render()

object getInitialState()       // 在组件被挂载之前调用，状态化的组件应该实现这个方法，`返回初始的state数据`

object getDefaultProps()  // 在组件类创建的时候调用一次，然后返回值被缓存下来

object propTypes

array mixins

object statics

string displayName

componentWillMount()    // 在挂载发生之前立即被调用
componentDidMount()     // 在挂载结束之后马上被调用，`需要DOM节点的初始化操作应该放在这里`

componentWillReceiveProps(object nextProps)  //当一个挂载的组件`接收到新的props的时候`被调用。该方法应该用于比较this.props和nextProps，然后使用this.setState()来改变state

shouldComponentUpdate(object nextProps, object nextState):   // boolean当组件做出是否要更新DOM的决定的时候被调用。实现该函数，优化this.props和nextProps，以及this.state和nextState的比较，`如果不需要React更新DOM，则返回false`

componentWillUpdate(object nextProps, object nextState)  // 在更新发生之前被调用，可以在这里调用this.setState()

componentDidUpdate(object prevProps, object prevState)  // 在更新发生之后调用

componentWillUnmount()  // 在组件移除和销毁之前被调用，`清理工作应该放在这里`



## React事件系统
事件处理器将会传入虚拟事件对象的实例，一个对浏览器本地事件的跨浏览器封装。它有和浏览器本地事件相同的属性和方法，包括 stopPropagation() 和 preventDefault()，但是没有浏览器兼容问题。如果因为一些因素，需要底层的浏览器事件对象，只要使用 nativeEvent 属性就可以获取到它了。

### 虚拟事件对象的属性
每一个虚拟事件对象都有下列的属性：
boolean          bubbles
boolean          cancelable
DOMEventTarget   currentTarget
boolean          defaultPrevented
number           eventPhase
boolean          isTrusted
DOMEvent         nativeEvent
void             preventDefault()
void             stopPropagation()
DOMEventTarget   target
number           timeStamp
string           type

### 支持的虚拟事件
如下的事件处理器在事件冒泡阶段触发。要在捕获阶段触发某个事件处理器，在事件名字后面追加 Capture 字符串；例如，使用 onClickCapture 而不是 onClick 来在捕获阶段处理点击事件：

onCopy onCut onPaste          // 剪贴板事件
onKeyDown onKeyPress onKeyUp  // 键盘事件
onFocus onBlur                // 焦点事件
onChange onInput onSubmit     // 表单事件
onClick onDoubleClick onDrag  // 鼠标事件
onTouchCancel onTouchEnd onTouchMove onTouchStart  // 触摸事件
onScroll  // UI 事件
onWheel  // 鼠标滚轮滚动事件

### 与DOM的差异
React 为了性能和跨浏览器的原因，实现了一个独立于浏览器的事件和 DOM 系统。利用此功能，可以屏蔽掉一些浏览器的 DOM 的粗糙实现。

### React中特殊的非 DOM属性
React 也提供了一些 DOM 里面不存在的属性：
key  // 可选的唯一的标识器。当组件在渲染过程中被各种打乱的时候，由于差异检测逻辑，可能会被销毁后重新创建。给组件绑定一个 key，可以`持续确保组件还存在 DOM 中`。
ref
dangerouslySetInnerHTML：提供插入纯 HTML 字符串的功能，主要为了能和生成 DOM 字符串的库整合。







 














