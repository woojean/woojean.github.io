---
layout: post
title:  "将一个GitHub项目发布为Composer包的过程"
date: 2018-03-05 00:00:01
categories: 编程
tags: Composer PHP
excerpt: ""
---

* content
{:toc}


# 按psr-0标准开发并发布Github项目
主要就是命名空间、类名、文件路径的映射关系要正确，详略。

这里使用的项目是：[https://github.com/woojean/RedisQueue](https://github.com/woojean/RedisQueue)

在项目中创建一个文件composer.json，内容如下：
```

```


# 注册Composer项目

到[packagist.org](https://packagist.org)注册账号，详略。


点击`Submit`，输入项目地址：
![image](/images/tech/composer_1.png)


# 设置代码自动更新
![image](/images/tech/composer_2.png)

To do so you can:
* Go to your GitHub repository
* Click the "Settings" button  （是项目的setting，而不是Github的setting）
* Click "Integrations & services"
* Add a "Packagist" service, and configure it with your API token, plus your Packagist username (API token随便写)
* Check the "Active" box and submit the form

You can then hit the "Test Service" button to trigger it and check if Packagist removes the warning about the package not being auto-updated.

![image](/images/tech/composer_3.png)



