---
layout: post
title:  "Selenium安装配置（Mac）"
date: 2021-01-25 00:00:01
categories: 编程
tags: Selenium
excerpt: ""
---

* content
{:toc}



# 下载：chromedriver
[http://chromedriver.storage.googleapis.com/index.html](http://chromedriver.storage.googleapis.com/index.html)

```
sudo mv ~/Downloads/chromedriver ~/extensions/chromedriver
```

```
# chromedriver
export PATH="/Users/wujian/extensions/:$PATH"
```



# 升级python3
```
brew install python
brew upgrade python
```


# vi .zshrc
```
alias python='python3'
alias pip3='pip3'
```


# 安装selenium的python模块：
```
sudo pip3 install selenium
```


# 第一个demo（python）
```python
from selenium import webdriver
import time
 
wd=webdriver.Chrome()  # 打开 Chrome 浏览器
## 打开百度浏览器
wd.get('https://www.baidu.com')
# 定位输入框并输入关键字
wd.find_element_by_id('kw').send_keys('selenium')
# 点击[百度一下]搜索
wd.find_element_by_id('su').click()
time.sleep(5)
wd.quit()   #关闭浏览器
```



# 改chromedriver，反嗅探
```
vim chromedriver
:%s/cdc_/win_/g
```
















