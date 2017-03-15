# 《React Router文档》阅读摘要

## Introduction

React Router保持UI与URL同步。

使用React Router需要安装history，因为React Router依赖history：
$ npm install history react-router@latest


## 简介

一个反例：不使用React Router，借助url的hash部分（#后面的内容）和React组件的state实现前端路由（即url改变引起页面的相应改变）。

### 使用React Router重构bad case
```
import React from 'react'
import { render } from 'react-dom'
import { Router, Route, Link } from 'react-router'

const App = React.createClass({
  render() {
    return (
      <div>
        <h1>App</h1>
        {/* 使用<Link>替代<a> */}
        <ul>
          <li><Link to="/about">About</Link></li>
          <li><Link to="/inbox">Inbox</Link></li>
        </ul>

        {/* react router会自动找到children*/}
        {this.props.children}
      </div>
    )
  }
})

// 来渲染 <Router>
React.render((
  <Router>
    <Route path="/" component={App}>
      <Route path="about" component={About} />
      <Route path="inbox" component={Inbox} />
    </Route>
  </Router>
), document.body)
```

`React Router知道如何搭建嵌套的UI`，因此不用手动找出需要渲染哪些<Child>组件。
例如，对于上面的/about路径，React Router会搭建出`<App><About/></App>`。


### 新增UI并获取URL参数
添加指向Message组件的路由配置：
```
React.render((
  <Router>
    <Route path="/" component={App}>
      <Route path="about" component={About} />
      <Route path="inbox" component={Inbox}>
        {/* 添加一个路由，嵌套进我们想要嵌套的 UI 里 */}
        <Route path="messages/:id" component={Message} />
      </Route>
    </Route>
  </Router>
), document.body)
```

现在访问`inbox/messages/Jkei3c32`将会匹配到一个新的路由，并且它指向App->Inbox->Message这个UI的分支：
```
<App>
  <Inbox>
    <Message params={ {id: 'Jkei3c32'} } />
  </Inbox>
</App>
```

当渲染组件时，React Router会自动向Route组件中注入一些有用的信息，尤其是`路径中动态部分的参数`（名为`params`）：
```
const Message = React.createClass({
  componentDidMount() {
    const id = this.props.params.id; // 来自url路径中的参数

    // 根据参数从后台加载数据
    fetchMessage(id, function (err, message) {
      this.setState({ message: message })
    })
  },

  // ...

})
```

也可以访问url参数，例如对于`/foo?bar=baz`，可以通过访问this.props.location.query.bar从Route组件中获得值"baz"。


## 基础

### 路由配置
React Router会自动将组件中的`this.props.children`加载为被嵌套的子组件：
```
import React from 'react'
import { Router, Route, Link } from 'react-router'

const App = React.createClass({
  render() {
    return (
      <div>
        <h1>App</h1>
        <ul>
          <li><Link to="/about">About</Link></li>
          <li><Link to="/inbox">Inbox</Link></li>
        </ul>
        {this.props.children}
      </div>
    )
  }
})

const About = React.createClass({
  render() {
    return <h3>About</h3>
  }
})

const Inbox = React.createClass({
  render() {
    return (
      <div>
        <h2>Inbox</h2>
        {this.props.children || "Welcome to your Inbox"}
      </div>
    )
  }
})

const Message = React.createClass({
  render() {
    return <h3>Message {this.props.params.id}</h3>
  }
})

React.render((
  <Router>
    <Route path="/" component={App}>
      <Route path="about" component={About} />
      <Route path="inbox" component={Inbox}>
        <Route path="messages/:id" component={Message} />
      </Route>
    </Route>
  </Router>
), document.body)
```

### IndexRoute 
当URL为`/`时，App的render中的`this.props.children`还是 undefined。这种情况可以使用IndexRoute来设置一个默认页面：
```
import { IndexRoute } from 'react-router'

const Dashboard = React.createClass({
  render() {
    return <div>Welcome to the app!</div>
  }
})

React.render((
  <Router>
    <Route path="/" component={App}>
      <IndexRoute component={Dashboard} />
      <Route path="about" component={About} />
      <Route path="inbox" component={Inbox}>
        <Route path="messages/:id" component={Message} />
      </Route>
    </Route>
  </Router>
), document.body)
```
之所以使用IndexRoute，而不是直接在App的render中写渲染元素，是为了方便根路径对应的组件参与到比如 onEnter hook 这些路由机制中来。

### Redirect
使用绝对路径将/inbox/messages/:id改为/messages/:id，同时，使用Redirect将旧的/inbox/messages/:id重定向到新的路径：
```
import { Redirect } from 'react-router'

React.render((
  <Router>
    <Route path="/" component={App}>
      <IndexRoute component={Dashboard} />
      <Route path="about" component={About} />
      <Route path="inbox" component={Inbox}>
        <Route path="/messages/:id" component={Message} />
        <Redirect from="messages/:id" to="/messages/:id" />
      </Route>
    </Route>
  </Router>
), document.body)
```


### onEnter和onLeave 
在路由跳转过程中，onLeave会在所有将离开的路由中触发，从最里层的子路由开始直到最外层父路由结束。onEnter会从最外层的父路由开始直到最里层子路由结束。
上例中，如果一个用户点击链接，从/messages/5跳转到/about，下面是这些hook的执行顺序：
1. /messages/:id 的 onLeave
2. /inbox的onLeave
3. /about的onEnter



### 路由匹配原理
1.React Router使用路由嵌套的方式来定义view的嵌套集合，会`深度优先`遍历整个路由配置来寻找与给定URL匹配的路由；
2.当一个给定的URL被调用时，整个集合中命中的部分都会被渲染；
3.路由路径是匹配一个或一部分URL的一个字符串模式，除了正常的字面量外，还包含以下模式：
（1）`:paramName` 匹配一段位于 /、? 或 # 之后的 URL。命中的部分将被作为一个参数（params）
  <Route path="/hello/:name">         // 匹配 /hello/michael 和 /hello/ryan
（2）`()` 在它内部的内容被认为是可选的
  <Route path="/hello(/:name)">       // 匹配 /hello, /hello/michael 和 /hello/ryan
（3）`*`  匹配任意字符（非贪婪的）直到命中下一个字符或者整个 URL 的末尾，并创建一个 splat 参数
  <Route path="/files/*.*">           // 匹配 /files/hello.jpg 和 /files/path/to/hello.jpg
4.如果一个路由使用了相对路径，那么完整的路径将由它的`所有祖先节点`的路径和自身指定的相对路径拼接而成。`使用绝对路径可以使路由匹配行为忽略嵌套关系`。
5.路由算法会`根据定义的顺序自顶向下`匹配路由。因此，当拥有两个兄弟路由节点配置时，必须确认前一个路由不会匹配后一个路由中的路径。


### Histories
React Router是建立在history之上的。
通过状态管理的API，能够`在不加载新页面（不刷新）的情况下改变浏览器的URL`。
一个 history 知道如何去监听浏览器地址栏的变化， 并解析这个URL转化为location对象， 然后router使用它匹配到路由，最后正确地渲染对应的组件。

常用的 history 有三种形式，:
1.createHashHistory
它使用URL中的hash（#）部分去创建形如example.com/#/some/path的路由。
不推荐使用。

2.createBrowserHistory
是用React Router创建浏览器应用推荐的history。
它在浏览器中被创建用于处理URL，新建一个像这样真实的URL：example.com/some/path
需要后端服务器的配合，即后端需要知道哪些路由属于`前端路由`，在接收到这样的路由请求时，交给前端去处理。
例如在nginx中使用try_files：
```
server {
  ...
  location / {
    try_files $uri /index.html
  }
}
```
即，当直接访问前端路由时，后端会把它交给前端页面处理。
但是在前端页面已加载的情况下，点击跳转其他页面的url状态的改变将会被浏览器监听到，并加载相应的前端组件，这时不会有发往后端的请求（页面组件自身的数据加载请求除外）。


3.createMemoryHistory
Memory history 不会在地址栏被操作或读取。这就解释了如何实现服务器渲染。同时它也非常适合测试和其他的渲染环境（像 React Native ）。


### IndexLink 
在这个App中使用 <Link to="/">Home</Link> , 它会一直处于激活状态，因为所有的 URL 的开头都是 / 。 
如果仅仅希望在Home组件被渲染后，激活并链接到它，需要使用IndexLink：
<IndexLink to="/">Home</IndexLink>


## 高级用法

### 动态路由

程序应当只加载当前渲染页所需的 JavaScript。
“代码分拆” — 将所有的代码分拆成多个小包，在用户浏览过程中按需加载。路由是个非常适于做代码分拆的地方：它的责任就是配置好每个 view。

React Router里的路径匹配以及组件加载都是异步完成的，不仅允许延迟加载组件，并且可以延迟加载路由配置。在首次加载包中只需要有一个路径定义，路由会自动解析剩下的路径。

Route可以定义getChildRoutes，getIndexRoute和getComponents这几个函数。它们都是异步执行，并且只有在需要时才被调用。
```
const CourseRoute = {
  path: 'course/:courseId',

  getChildRoutes(location, callback) {
    require.ensure([], function (require) {
      callback(null, [
        require('./routes/Announcements'),
        require('./routes/Assignments'),
        require('./routes/Grades'),
      ])
    })
  },

  getIndexRoute(location, callback) {
    require.ensure([], function (require) {
      callback(null, require('./components/Index'))
    })
  },

  getComponents(location, callback) {
    require.ensure([], function (require) {
      callback(null, require('./components/Course'))
    })
  }
}
```

### Lifecycle mixin
可以在route组件中引入Lifecycle mixin来安装routerWillLeave钩子。这使得React组件可以拦截正在发生的跳转，或在离开route前提示用户。
routerWillLeave 返回值有以下两种：
1.return false 取消此次跳转
2.return 返回提示信息，在离开route前提示用户进行确认。

```
import { Lifecycle } from 'react-router'

const Home = React.createClass({

  mixins: [ Lifecycle ],

  routerWillLeave(nextLocation) {
    if (!this.state.isSaved)
      return 'Your work is not saved! Are you sure you want to leave?'
  },

  // ...

})
```

### RouteContext mixin
如果想在一个深层嵌套的组件中使用routerWillLeave钩子，只需在route组件中引入RouteContext mixin，这样就会把route放到context中,使得当前组件的子组件都可以获得route（这样routerWillLeave才可用）：
```
import { Lifecycle, RouteContext } from 'react-router'

const Home = React.createClass({

  mixins: [ RouteContext ],

  render() {
    return <NestedForm />
  }

})

const NestedForm = React.createClass({

  mixins: [ Lifecycle ],

  routerWillLeave(nextLocation) {
    if (!this.state.isSaved)
      return 'Your work is not saved! Are you sure you want to leave?'
  },

  // ...

})
```


### 服务端渲染
（略）


### 组件生命周期
路由配置：
```
<Route path="/" component={App}>
  <IndexRoute component={Home}/>
  <Route path="invoices/:invoiceId" component={Invoice}/>
  <Route path="accounts/:accountId" component={Account}/>
</Route>
```

路由切换时，组件生命周期的变化情况：
1.打开'/'页面
App	     ->  componentDidMount
Home	 ->  componentDidMount
Invoice	 ->  N/A
Account	 ->  N/A

2.从'/'跳转到'/invoice/123'
App	     ->  componentWillReceiveProps, componentDidUpdate
Home	 ->  componentWillUnmount
Invoice	 ->  componentDidMount
Account	 ->  N/A

3.当用户从'/invoice/123'跳转到'/invoice/789'
App	     ->  componentWillReceiveProps, componentDidUpdate
Home	 ->  N/A
Invoice	 ->  componentWillReceiveProps, componentDidUpdate
Account	 ->  N/A
所有的组件之前都已经被挂载， 所以只是从router更新了props

4.从'/invoice/789'跳转到'/accounts/123'
App	     ->  componentWillReceiveProps, componentDidUpdate
Home	 ->  N/A
Invoice	 ->  componentWillUnmount
Account	 ->  componentDidMount


### 在组件外部使用导航
在组件内部可以使用this.props.history，或者引入History mixin来实现导航。

有时需要再组件的外部使用导航，比如在flux中。可以如下实现：
1.// history.js 输出history对象
import createBrowserHistory from 'history/lib/createBrowserHistory'
export default createBrowserHistory()

2.// index.js 将history对象关联到router
import history from './history'
React.render(<Router history={history}/>, el)

3.// actions.js（flux action文件）使用history对象
import history from './history'
history.replaceState(null, '/some/path')




