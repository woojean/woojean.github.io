# 读文档笔记-《Redux文档》

Redux是JavaScript`状态容器`，提供`可预测化的状态管理`。所有的state都以一个对象树的形式储存在一个`单一的store`中。
惟一改变state的办法是触发action，一个描述发生什么的对象。
为了描述action如何改变state树，需要编写reducers。
只有一个单一的store和一个根级的reduce函数（reducer）。随着应用不断变大，应该把根级的reducer拆成多个小的reducers，分别独立地操作state树的不同部分，而不是添加新的stores。

## 动机
Redux试图让state的变化变得可预测。


## 三大原则
1.单一数据源
整个应用的state被储存在一棵object tree中，并且这个object tree只存在于唯一一个store中。

2.惟一改变state的方法就是触发action。
这样确保了视图和网络请求都不能直接修改state，相反它们只能表达想要修改的意图。
因为所有的修改都被集中化处理，且严格按照一个接一个的顺序执行，因此不用担心race condition的出现。

3.使用`纯函数`来执行修改
编写reducer函数来描述action如何改变state tree。


### 先前技术
Flux、Elm、Immutable、Baobab、Rx

### 生态系统
（略）

### Action
Action是把数据从应用传到store的有效载荷,是store数据的唯一来源。
Action本质上是JavaScript普通对象，如：
```
{
  type: ADD_TODO,
  text: 'Build my first Redux app'
}
```
应该尽量减少在action中传递的数据。比如传递index就比把整个任务对象传过去要好（不同实体或列表间通过ID相互引用数据）。
可以通过store.dispatch()将action传到store。但是多数情况下会使用react-redux 提供的connect()帮助器来调用。bindActionCreators()可以自动把多个`action创建函数`绑定到dispatch()方法上。
action创建函数就是生成action的方法，在Redux中的action创建函数只是简单的返回一个action:
```
function addTodo(text) {
  return {
    type: ADD_TODO,
    text
  }
}
```

### Reducer
reducer就是一个`纯函数`，接收旧的state和action，返回新的state：
(previousState, action) => newState
之所以称作reducer是因为它将被传递给`Array.prototype.reduce(reducer, ?initialValue)`方法。

要保持reducer纯净：
1.不要修改state，而是返回新对象；
2.不要执行有副作用的操作，如 API 请求和路由跳转；
3.不要调用非纯函数，如 Date.now() 或 Math.random()。
只要传入参数相同，返回计算得到的下一个 state 就一定相同。没有特殊情况、没有副作用，没有 API 请求、没有变量修改，单纯执行计算。


### 拆分Reducer
所有state的更新放在一个reducer中：
```
function todoApp(state = initialState, action) {
  switch (action.type) {
    case SET_VISIBILITY_FILTER:
      return Object.assign({}, state, {
        visibilityFilter: action.filter
      })
    case ADD_TODO:
      return Object.assign({}, state, {
        todos: [
          ...state.todos,
          {
            text: action.text,
            completed: false
          }
        ]
      })
    case TOGGLE_TODO:
      return Object.assign({}, state, {
        todos: state.todos.map((todo, index) => {
          if(index === action.index) {
            return Object.assign({}, todo, {
              completed: !todo.completed
            })
          }
          return todo
        })
      })
    default:
      return state
  }
}
```
可以开发一个函数来做为主 reducer，它调用多个子 reducer 分别处理 state 中的一部分数据，然后再把这些数据合成一个大的单一对象。主 reducer 并不需要设置初始化时完整的 state。初始时，如果传入 undefined, 子 reducer 将负责返回它们的默认值：
```
function todos(state = [], action) {
  switch (action.type) {
    case ADD_TODO:
      return [
        ...state,
        {
          text: action.text,
          completed: false
        }
      ]
    case TOGGLE_TODO:
      return state.map((todo, index) => {
        if (index === action.index) {
          return Object.assign({}, todo, {
            completed: !todo.completed
          })
        }
        return todo
      })
    default:
      return state
  }
}

function visibilityFilter(state = SHOW_ALL, action) {
  switch (action.type) {
    case SET_VISIBILITY_FILTER:
      return action.filter
    default:
      return state
  }
}

function todoApp(state = {}, action) {
  return {
    visibilityFilter: visibilityFilter(state.visibilityFilter, action),
    todos: todos(state.todos, action)
  }
}
```

Redux提供了`combineReducers()`工具类来做reducer合成，可以改写上面的todoApp为如下形式：
```
import { combineReducers } from 'redux';

const todoApp = combineReducers({
  visibilityFilter,
  todos
})

export default todoApp;
```
combineReducers()所做的只是生成一个函数，这个函数调用一系列reducer，每个reducer根据它们的key来筛选出state 中的一部分数据并处理，然后这个生成的函数再将所有reducer的结果合并成一个大的对象。没有任何魔法。

如上todoApp返回的数据的格式：
`Object{visibilityFilter:"SHOW_ALL",todos:Array[3]}`


### Store
Store 有以下职责：
1.维持应用的 state；
2.提供 getState() 方法获取 state；
3.提供 dispatch(action) 方法更新 state；
4.通过 subscribe(listener) 注册监听器;
5.通过 subscribe(listener) 返回的函数注销监听器。

创建store：
```
import { createStore } from 'redux'
import todoApp from './reducers'
let store = createStore(todoApp)
```

发起 Actions：
```
import { addTodo, toggleTodo, setVisibilityFilter, VisibilityFilters } from './actions'

// 每次state更新时，打印日志
let unsubscribe = store.subscribe(() =>
  console.log(store.getState())
)

store.dispatch(addTodo('Learn about actions'))
store.dispatch(addTodo('Learn about reducers'))
store.dispatch(addTodo('Learn about store'))
store.dispatch(toggleTodo(0))
store.dispatch(toggleTodo(1))
store.dispatch(setVisibilityFilter(VisibilityFilters.SHOW_COMPLETED))

// 停止监听state更新
unsubscribe();
```

### 数据流
严格的单向数据流是Redux架构的设计核心。这意味着应用中所有的数据都遵循相同的生命周期，这样可以让应用变得更加可预测且容易理解。

Redux应用中数据的生命周期遵循下面4个步骤：
1.调用 store.dispatch(action)
可以在任何地方调用 store.dispatch(action)，包括组件中、XHR 回调中、甚至定时器中。

2.Redux store调用传入的reducer函数
Store会把两个参数传入reducer：当前的state树和action。

3.根reducer应该把多个子reducer输出合并成一个单一的state树。

4.Redux store保存了根reducer返回的完整state树
这个新的树就是应用的下一个state！所有订阅store.subscribe(listener)的监听器都将被调用；监听器里可以调用store.getState()获得当前state。
现在，可以应用新的state来更新UI。


### 搭配 React
Redux的React绑定库（react-redux）包含了`容器组件和展示组件相分离`的开发思想：只在最顶层组件（如路由操作）里使用 Redux。其余内部组件仅仅是展示性的，所有数据都通过props传入。

只有顶层的容器组件知道Redux的存在，并从Redux中获取state数据赋值给展示组件的props。展示组件从props中获取顶层组件的回调函数，当更新操作发生时，调用回调函数，继而由顶层组件向Redux派发actions。


### 连接到 Redux
入口文件：
```
// index.js
import React from 'react'
import { render } from 'react-dom'
import { createStore } from 'redux'
import { Provider } from 'react-redux'
import App from './containers/App'
import todoApp from './reducers'

let store = createStore(todoApp);

let rootElement = document.getElementById('root')
render(
  <Provider store={store}>
    <App />
  </Provider>,
  rootElement
)
```

容器组件：
```
// containers/App.js

import React, { Component, PropTypes } from 'react';
import { connect } from 'react-redux';
import { addTodo, completeTodo, setVisibilityFilter, VisibilityFilters } from '../actions';
import AddTodo from '../components/AddTodo';
import TodoList from '../components/TodoList';
import Footer from '../components/Footer';

class App extends Component {
  render() {

    // 通过调用connect()绑定的组件都可以得到一个dispatch方法作为组件的props，并且可以获得select中输出的props
    const { dispatch, visibleTodos, visibilityFilter } = this.props
    return (
      <div>
        <AddTodo
          onAddClick={text =>
            dispatch(addTodo(text))
          } />
        <TodoList
          todos={this.props.visibleTodos}
          onTodoClick={index =>
            dispatch(completeTodo(index))
          } />
        <Footer
          filter={visibilityFilter}
          onFilterChange={nextFilter =>
            dispatch(setVisibilityFilter(nextFilter))
          } />
      </div>
    )
  }
}

App.propTypes = {
  // ...
}

function selectTodos(todos, filter) {
  switch (filter) {
  case VisibilityFilters.SHOW_ALL:
    return todos;
  case VisibilityFilters.SHOW_COMPLETED:
    return todos.filter(todo => todo.completed);
  case VisibilityFilters.SHOW_ACTIVE:
    return todos.filter(todo => !todo.completed);
  }
}

// select方法从Redux store接收到全局的state，然后返回组件中需要的props
function select(state) {
  return {
    visibleTodos: selectTodos(state.todos, state.visibilityFilter),
    visibilityFilter: state.visibilityFilter
  };
}

// 连接select方法和容器组件
export default connect(select)(App);
```


### 异步 Action

标准的做法是使用 Redux Thunk middleware。通过使用指定的 middleware，action creator 除了返回 action 对象外`还可以返回函数`。这时，这个 action creator 就成为了`thunk`。当 action creator 返回函数时，这个函数会被 Redux Thunk middleware 执行。这个函数并不需要保持纯净；它还可以带有副作用，包括执行异步 API 请求。这个函数还可以 dispatch action。

默认情况下，createStore() 所创建的 Redux store 没有使用 middleware，所以只支持 同步数据流。
可以使用`applyMiddleware() 来增强 createStore()`。虽然这不是必须的，但是它可以用简便的方式来描述异步的 action。

actions.js
```
import fetch from 'isomorphic-fetch'

// 同步action creater
export const REQUEST_POSTS = 'REQUEST_POSTS'
function requestPosts(subreddit) {
  return {
    type: REQUEST_POSTS,
    subreddit
  }
}

export const RECEIVE_POSTS = 'RECEIVE_POSTS'
function receivePosts(subreddit, json) {
  return {
    type: RECEIVE_POSTS,
    subreddit,
    posts: json.data.children.map(child => child.data),
    receivedAt: Date.now()
  }
}

// thunk action creator
export function fetchPosts(subreddit) {
  return function (dispatch) {

    // 更新应用的 state 来通知API 请求发起了。
    dispatch(requestPosts(subreddit))

    // thunk middleware 调用的函数可以有返回值，它会被当作 dispatch 方法的返回值传递。
    // 这里返回一个等待处理的 promise。
    return fetch(`http://www.subreddit.com/r/${subreddit}.json`)
      .then(response => response.json())
      .then(json =>
        // 可以多次 dispatch！
        // 这里，使用 API 请求结果来更新应用的 state。
        dispatch(receivePosts(subreddit, json))
      )

      // ...捕获网络请求的异常
  }
}

function fetchPosts(subreddit) {
  return dispatch => {
    dispatch(requestPosts(subreddit))
    return fetch(`http://www.reddit.com/r/${subreddit}.json`)
      .then(response => response.json())
      .then(json => dispatch(receivePosts(subreddit, json)))
  }
}

function shouldFetchPosts(state, subreddit) {
  const posts = state.postsBySubreddit[subreddit]
  if (!posts) {
    return true
  } else if (posts.isFetching) {
    return false
  } else {
    return posts.didInvalidate
  }
}

export function fetchPostsIfNeeded(subreddit) {
  return (dispatch, getState) => {
    if (shouldFetchPosts(getState(), subreddit)) {
      // 在 thunk 里 dispatch 另一个 thunk！
      return dispatch(fetchPosts(subreddit))
    } else {
      // 告诉调用代码不需要再等待。
      return Promise.resolve()
    }
  }
}
```

index.js
```
import thunkMiddleware from 'redux-thunk'
import createLogger from 'redux-logger'
import { createStore, applyMiddleware } from 'redux'
import { selectSubreddit, fetchPosts } from './actions'
import rootReducer from './reducers'

const loggerMiddleware = createLogger()

// 创建store的时候指定middleware，增强store，使其支持异步操作
const store = createStore(
  rootReducer,
  applyMiddleware(
    thunkMiddleware, // 允许dispatch()函数
    loggerMiddleware // 一个很便捷的 middleware，用来打印 action 日志
  )
)

store.dispatch(selectSubreddit('reactjs'))
store.dispatch(fetchPostsIfNeeded('reactjs')).then(() =>
  console.log(store.getState())
)
```
Thunk middleware并不是Redux处理异步action的唯一方式。也可以使用redux-promise或者redux-promise-middleware来dispatch Promise 替代函数。



像redux-thunk或redux-promise这样支持异步的middleware都包装了store的dispatch()方法，以此来dispatch一些除了action 以外的其他内容，例如：函数或者Promise。任何middleware都可以以自己的方式解析dispatch的任何内容，并继续传递actions给下一个 middleware。比如，支持Promise的middleware能够拦截Promise，然后为每个Promise异步地dispatch一对begin/end actions。

当middleware链中的最后一个middleware开始dispatch action时，这个action必须是一个普通对象。这是 同步式的Redux数据流开始的地方。


### Middleware
middleware最优秀的特性就是可以被链式组合。可以在一个项目中使用多个独立的第三方 middleware。
它提供的是位于 action 被发起之后，到达 reducer 之前的扩展点。

自定义middleware
```
const logger = store => next => action => {
  console.log('dispatching', action)
  let result = next(action)
  console.log('next state', store.getState())
  return result
}

const crashReporter = store => next => action => {
  try {
    return next(action)
  } catch (err) {
    console.error('Caught an exception!', err)
    Raven.captureException(err, {
      extra: {
        action,
        state: store.getState()
      }
    })
    throw err
  }
}
```

将它们引用到 Redux store 中:
```
import { createStore, combineReducers, applyMiddleware } from 'redux'
let createStoreWithMiddleware = applyMiddleware(logger, crashReporter)(createStore)
let todoApp = combineReducers(reducers)
let store = createStoreWithMiddleware(todoApp)
```
现在任何被发送到 store 的 action 都会经过 logger 和 crashReporter。
其中logger这个middleware的实现等同于：
```
function logger(store) {
  return function wrapDispatchToAddLogging(next) {
    return function dispatchAndLog(action) {
      console.log('dispatching', action)
      let result = next(action)
      console.log('next state', store.getState())
      return result
    }
  }
}
```
Redux middleware 就像一个链表。每个 middleware 方法既能调用 next(action) 传递 action 到下一个 middleware，也可以调用 dispatch(action) 重新开始处理，或者什么都不做而仅仅终止 action 的处理进程。












































