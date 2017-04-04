# js对象遍历顺序问题

```javascript
var a = {
  b:'a',
  10: "vv",
  1:"a",
  a:''
}
console.log(a);
```
Object:
 1:"a"
 10:"vv"
 a:""
 b:"a"