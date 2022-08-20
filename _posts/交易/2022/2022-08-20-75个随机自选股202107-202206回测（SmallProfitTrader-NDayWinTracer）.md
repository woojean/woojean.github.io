---
layout : post
title : "75个随机自选股202107-202206回测（SmallProfitTrader-NDayWinTracer）"
date : 2022-08-20 00:00:01
categories : 交易
tags : 回测记录
excerpt : ""
---

* content
{:toc}


# 参数
```php
$startDate = '2021-07-01';
$endDate = '2022-06-30';

$_isUseMaInTrend = true;        // notice 均线顺势
$_ma1 = 10;      // 小均线
$_ma2 = 20;     // 中均线（三均线）
$_ma3 = 30;     // 大均线
$_isMaStrongTrend = true;       // notice 均线同向

$_retractDays = 2;              // notice 最少缩回天数
$_isUseYinYang = true;          // notice 阴阳线

$_prevDaysIncrease = 20;        // notice 前期涨幅天数
$_prevDayIncreaseRate = 0.15;    // notice 前期涨幅

$_maxHoldDays = 5;              // notice 最大持有日期

$minBias = 0;                   // 最小乖离
$samplingRatio = 0;             // 随机交易
$monteCarlo = 0;                // 随机交易日
```



# 统计

 ![image](/images/trade/20220820-1.png)

 ![image](/images/trade/20220820-2.png)



