# 负载均衡实现方式之DNS

DNS完成域名到IP的映射，这种映射也可以是一对多的，这时DNS服务器便起到了负载均衡调度器的作用。
可以使用dig命令来查看指定域名DNS的A记录设置：

```
dig www.qq.com
```
可以看到有些域名可能有多个A记录设置，因而多次ping同一个域名IP可能变化。可以结合使用A记录和CNAME实现基于DNS服务器的，类似HTTP重定向的负载均衡：
```
www1.xxx.com IN A 10.0.1.1
www2.xxx.com IN A 10.0.1.2
www3.xxx.com IN A 10.0.1.3
www.xxx.com  IN CNAME www1.xxx.com
www.xxx.com  IN CNAME www2.xxx.com
www.xxx.com  IN CNAME www3.xxx.com
```
不用担心DNS服务器本身的性能，因为DNS记录可以被用户浏览器、互联网接入服务商的各级DNS服务器缓存。