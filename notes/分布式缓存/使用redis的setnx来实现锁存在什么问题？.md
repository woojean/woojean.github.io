# 使用redis的setnx来实现锁存在什么问题？

SETNX，是「SET if Not eXists」的缩写，也就是只有不存在的时候才设置。

```c
// 缓存过期时通过SetNX获取锁，如果成功了就更新缓存，然后删除锁
$ok = $redis->setNX($key, $value);
if ($ok) {
    $cache->update();
    $redis->del($key);
}
```
存在问题：如果请求执行因为某些原因意外退出了，导致创建了锁但是没有删除锁，那么这个锁将一直存在，以至于以后缓存再也得不到更新。

因此需要给锁加一个过期时间以防不测。
```php
// 加锁
$redis->multi();
$redis->setNX($key, $value);
$redis->expire($key, $ttl);
$redis->exec();
```
存在问题：当多个请求到达时，虽然只有一个请求的SetNX可以成功，但是任何一个请求的Expire却都可以成功，如此就意味着即便获取不到锁，也可以刷新过期时间，如果请求比较密集的话，那么过期时间会一直被刷新，导致锁一直有效。

从 2.6.12 起，SET涵盖了SETEX的功能，并且SET本身已经包含了设置过期时间的功能：
```php
$ok = $redis->set($key, $value, array('nx', 'ex' => $ttl));
if ($ok) {
    $cache->update();
    $redis->del($key);
}
```