# Redis对Lua脚本的支持

通过在服务器中嵌入Lua环境，Redis客户端可以使用Lua脚本，直接在服务器端原子地执行多个Redis命令。

```
redis> EVAL "return 'hello world'" 0
"hello world"

redis> EVAL "return 1+1" 0
(integer) 2
```
详略。