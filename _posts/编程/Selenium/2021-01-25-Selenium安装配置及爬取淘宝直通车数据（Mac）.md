---
layout: post
title:  "Selenium安装配置及爬取淘宝直通车数据（Mac）"
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

:%s/win_/jjj_/g
```




# 其他
## kill chromedriver --port=***
```
pgrep chromedriver |xargs kill -9
```


# 高级示例（爬取淘宝直通车数据）
```python
import time
from selenium import webdriver
from selenium.webdriver.common.action_chains import ActionChains
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support.wait import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.common.exceptions import TimeoutException
import sys
import datetime

def getYesterday(): 
	today=datetime.date.today() 
	oneday=datetime.timedelta(days=1) 
	yesterday=today-oneday  
	return yesterday

# yesterdayStr = getYesterday()
# print(yesterdayStr)
# sys.exit(0)

options = webdriver.ChromeOptions()

options.add_argument('User-Agent=M*****) ***32 MQ**')

browser = webdriver.Chrome(options = options)  # 创建一个浏览器对象

# 隐藏window.navigator.webdriver
browser.execute_cdp_cmd("Page.addScriptToEvaluateOnNewDocument", {
  "source": """
	Object.defineProperty(navigator, 'webdriver', {
	  get: () => undefined
	})
  """
})

url = "https://login.taobao.com/member/login.jhtml"
browser.get(url)

print(browser.current_url)

# 填写用户名密码
user = '********'
password = ''********''

elem = browser.find_element_by_id("fm-login-id")
elem.send_keys(user)

elem = browser.find_element_by_id("fm-login-password")
elem.send_keys(password)

time.sleep(3)
print(browser.current_url)

# 登录
ActionChains(browser).key_down(Keys.ENTER).perform()
print('登录成功\n')


# time.sleep(10)

# 加载左侧导航菜单
wait = WebDriverWait(browser, 30) #等待的最大时间
try:
	# 获取搜索点击按钮
	wait.until(
		# EC.presence_of_element_located((By.ID, 'magix_vf_root'))
		# elems = browser.find_elements_by_class_name("qn-aside-childMenuItemName")
		EC.presence_of_element_located((By.CSS_SELECTOR, '.qn-aside-childMenuItemName'))
	)

	print('菜单出现')

	elems = browser.find_elements_by_class_name("qn-aside-childMenuItemName")
	for item in elems:
		elemText = item.text
		print(elemText)
		if elemText == '直通车':
			item.click()
			break

except TimeoutException:
	print('未出现菜单')
	sys.exit(0)



time.sleep(10)

browser.switch_to.window(browser.window_handles[1])
print(browser.current_url)
browser.get(browser.current_url)
print('done')

# wrapperEle = browser.find_element_by_id('magix_vf_root')
# print(wrapperEle)

#wrapperEle = browser.find_element_by_id('J_xiaomi_dialog')
#print(wrapperEle)
#ActionChains(browser).move_to_element_with_offset(wrapperEle).click().perform()


wait = WebDriverWait(browser, 30) #等待的最大时间
try:
	# 获取搜索点击按钮
	wrapperEle = wait.until(
		EC.presence_of_element_located((By.ID, 'magix_vf_root'))
	)


	wrapperEle = browser.find_element_by_id('magix_vf_root')
	print(wrapperEle)

	'''
	for x in xrange(1,10):
		print(1)
		ActionChains(browser).move_to_element_with_offset(wrapperEle,50, 50).click().perform()
		time.sleep(10)
	'''
except TimeoutException:
	print('无弹窗')


'''
elems = browser.find_elements_by_class_name("today mt10")
print(elems)
for item in elems:
	elemText = item.text
	print(elemText)
'''
# vframe = browser.find_element_by_id('J_default_view')
# print(vframe)


yesterdayStr = str(getYesterday())

url = "https://subway.simba.tmall.hk/index.jsp#!/report/bpreport/index"
url += '?start='+yesterdayStr
url += '&end='+yesterdayStr
url += '?page=1'

browser.get(url)


wait = WebDriverWait(browser, 30) #等待的最大时间
try:
	wait.until(
		EC.presence_of_element_located((By.ID, 'J_report_total_J_report_bpreport_table_list_impression'))
	)

	print("\n===================================================\n")

	ele = browser.find_element_by_id('J_report_total_J_report_bpreport_table_list_impression')
	print('展现量:'+ele.text)

	ele = browser.find_element_by_id('J_report_total_J_report_bpreport_table_list_click')
	print('点击量:'+ele.text)

	ele = browser.find_element_by_id('J_report_total_J_report_bpreport_table_list_cost')
	print('花费:'+ele.text)

	ele = browser.find_element_by_id('J_report_total_J_report_bpreport_table_list_ctr')
	print('点击率:'+ele.text)

	ele = browser.find_element_by_id('J_report_total_J_report_bpreport_table_list_cpc')
	print('平均点击花费:'+ele.text)

	ele = browser.find_element_by_id('J_report_total_J_report_bpreport_table_list_transactionshippingtotal')
	print('总成交笔数:'+ele.text)

	ele = browser.find_element_by_id('J_report_total_J_report_bpreport_table_list_carttotal')
	print('总购物车数:'+ele.text)

	ele = browser.find_element_by_id('J_report_total_J_report_bpreport_table_list_coverage')
	print('点击转化率:'+ele.text)
	
except TimeoutException:
	print('无弹窗')
```


