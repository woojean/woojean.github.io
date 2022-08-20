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

| 指数     | 涨幅   |
| -------- | ------ |
| 上证指数 | 13.87% |
| 中证1000 | -1.24% |
| 中证100  | 5.01%  |

| 交易日     | 486     |
| ---------- | ------- |
| 总交易数   | 231     |
| 日均交易数 | 0.4753  |
| 总收益率   | 167.62% |
| 有交易日数 | 127     |
| 日均收益率 | 0.36%   |
| 负收益日数 | 31      |
| 正收益日数 | 96      |
| 按日胜率   | 75.59%  |




