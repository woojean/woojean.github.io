# CentOS基于Jenkins搭建PHP项目持续集成



## 安装/检查JDK

```
sudo yum install java-1.8.0-openjdk
java -version
```



## 安装Jenkins

```
wget -O /etc/yum.repos.d/jenkins.repo http://pkg.jenkins-ci.org/redhat/jenkins.repo
rpm --import http://pkg.jenkins-ci.org/redhat/jenkins-ci.org.key
sudo yum install jenkins
```

如果不能更新源，也可以直接下载rpm包：

```
wget http://pkg.jenkins-ci.org/redhat-stable/jenkins-2.7.3-1.1.noarch.rpm
rpm -ivh jenkins-2.7.3-1.1.noarch.rpm
```



## 配置Jenkins的端口　

```
vi /etc/sysconfig/jenkins
JENKINS_PORT="8888"
```



## 启动Jenkins

```
service jenkins start
```

初始密码：

```
cat /var/lib/jenkins/secrets/initialAdminPassword
```



第一次访问，通过Web页面修改Administrator的密码。



## 修改Jenkins密码

```
vi /var/lib/jenkins/secrets/initialAdminPassword
```



## 配置Git账户

```
git config --global user.name "wsnbackend"
git config --global user.email wsnbackend@gegolab.com
git config --list

root@iZuf6a6ydemt9fvgy22rcqZ ~]# ssh-keygen -t rsa -C "wsnbackend@gegolab.com"
Generating public/private rsa key pair.
Enter file in which to save the key (/root/.ssh/id_rsa): 【回车】
Created directory '/root/.ssh'.
Enter passphrase (empty for no passphrase): 【回车】
Enter same passphrase again: 【回车】
Your identification has been saved in /root/.ssh/id_rsa.
Your public key has been saved in /root/.ssh/id_rsa.pub.
The key fingerprint is:
...

cat ~/.ssh/id_rsa.pub
[需要将部署账户添加到项目]
[登录wsnbackend，配置公钥]

ssh -T git@gegolab.com
```







## 修改Jenkins为root权限运行

```
gpasswd -a root jenkins # 将jenkins用户添加到root组
vi /etc/default/jenkins
JENKINS_USER=root
JENKINS_GROUP=root

service jenkins restart
```

## 为jenkins用户添加sudo免密码：

```
vi /etc/sudoers
jenkins ALL = NOPASSWD:ALL
```



# 添加项目

## 源码管理

Git git@git.gegolab.com:backend/sms.git

**Add Credentials**

```
SSH Username with private key:
wsnbackend@gegolab.com
private key 直接输入：
cat ~/.ssh/id_rsa  # gitlab用户wsnbackend@gegolab.com的私钥
```

**submodule**

Additional Behaviours -> 勾选Recursively update submodules





## Jenkin构建后指定Shell

```
#mkdir /data/www/sms
sudo chown -R jenkins:jenkins /data/www/sms
\cp -rf /var/lib/jenkins/workspace/sms /data/www/
cd /data/www/sms
sudo composer install
sudo chmod -R 777 /data/www/sms
sudo chown -R www:www /data/www/sms
```





 

















