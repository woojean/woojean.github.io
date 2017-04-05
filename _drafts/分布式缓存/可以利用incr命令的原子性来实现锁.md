# 可以利用incr命令的原子性来实现锁

```c
$value = $redis->get($lock); 
if($value < 1 ){
  $redis->incr($lock,1);
  // ...
  $redis->decr($lock,1);
}
```

不使用incr：
```c
// 被WATCH的键会被监视，并会发觉这些键是否被改动过了。 如果有至少一个被监视的键在EXEC执行之前被修改了，那么整个事务都会被取消
WATCH mykey
  $val = GET mykey   // 乐观锁
  $val = $val + 1
MULTI
  SET mykey $val
EXEC
```