# babel用于浏览器环境

```
<script src="node_modules/babel-core/browser.js"></script>
<script type="text/babel">
// ES6 code
</script>
```
直接在浏览器中进行转码性能太差，可以配合browserify在服务器端把代码转换为浏览器可以直接执行的代码：
$ npm install --save-dev babelify babel-preset-es2015
$ browserify script.js -o bundle.js -t [ babelify --presets [ es2015 ] ]

可以在package.json中进行配置，这样就不用每次都在命令行输入参数了：
```
{
  "browserify": {
    "transform": [["babelify", { "presets": ["es2015"] }]]
  }
}
```