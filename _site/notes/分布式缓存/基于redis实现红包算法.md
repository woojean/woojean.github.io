# 基于redis实现红包算法

```php
<?php

function gen($totalMoney, $num, $min='0.01'){
  $ret = [];
  
  // 剩余红包金额
  $remainMoney = $totalMoney;  
  
  for ( $i = 1; $i < $num; $i++) {
    // 剩余红包数量
    $remainNum = $num-$i;  
    
    // 当前可领取的红包的最大值
    $remainMax = ($remainMoney-$remainNum*$min)/$remainNum;  
    
    $allocateMoney = mt_rand($min*100, $remainMax*100)/100;
    $remainMoney = $remainMoney-$allocateMoney;
    $ret[$i] = array(
      'allocation' => $allocateMoney,
      'remainder' => $remainMoney
    );
  }
  // 最后一个
  $ret[$num] = [
    'allocation'=>$remainMoney,
    'remainder'=>0
  ];
  
  return $ret;
}

$totalMoney = 10;  // 红包总金额
$num = 10;  // 红包总数

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

$mapStock = 'queue_stock';  // 库存队列
$mapGrab = 'queue_grab';   // 已抢队列
$listIndexs = 'list_indexs';  // 剩余红包索引


$allocated = $redis->hlen($mapGrab);
if($allocated == $num){
  echo '已抢光！';
  exit;
}

$inited = $redis->hlen($mapStock);
if( 0 == $inited ){ // 索引尚未生成
  // 生成红包库存
  $stock = gen($totalMoney,$num);  
  foreach($stock as $index => $hongbao){
    $redis->hset($mapStock, $index, json_encode($hongbao));
    $redis->lpush($listIndexs,$index);
  }
}

$uid = intval($_GET['uid']);
if( $uid < 1){
  echo '用户ID非法！';
  exit;
}

$participated = $redis->hexists($mapGrab,$uid);
if($participated){
  echo '不能重复参加！';
  exit;
}

// 原子操作
$index = $redis->lpop($listIndexs);
if(intval($index) < 1){
  echo '已抢光！';
  exit;
}

$hongbao = $redis->hget($mapStock, $index);
$redis->hset($mapGrab, $uid, json_encode($hongbao));

echo $uid.' -> ' . json_encode($hongbao);


// http://demo.com/redis_qianghongbao.php?uid=1
```