# HTML5带来的安全问题

## 新标签的XSS
`<video>、<audio>`等，可能绕过站点现有的XSS Filter。

## iframe的sandbox属性
iframe被新增一个sandbox属性，使用这个属性后加载的内容将被视为一个独立的源，其中的脚本将被禁止执行，表单被禁止提交，插件被禁止加载，指向其他浏览器对象的链接也会被禁止。
```
<iframe sandbox="allow-same-origin allow-forms allow-scripts" src="..." ></iframe>
```

## noreferrer
`<a>和<area>`标签定义了新的名为noreferrer的Link Types，标签指定该值后，浏览器在请求该标签指定的地址时将不再发送Referer：
```
<a href="xxx" rel="noreferrer">test</a>
```

## Canvas
利用Canvas可以识别简单的图片验证码。

## postMessage
HTML5中新的API，运行每一个window对象往其他的窗口发送文本消息，从而实现跨窗口的消息传递，且这个功能不受同源策略限制，因此需要自己做安全判断。

## Web Storage
Web Storage分为Session Storage和Local Storage，前者在关闭浏览器时就会失效，后者会一直存在。Web Storage也受到同源策略的约束。
当Web Storage中保存敏感信息时，也会成为XSS的攻击目标。