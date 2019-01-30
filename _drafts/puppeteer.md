

阻止自动下载chromium：
```
set PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=1 
```

安装puppeteer：
```
npm i --save puppeteer --ignore-scripts
```

手工下载chromium：

[https://download-chromium.appspot.com/](https://download-chromium.appspot.com/)

安装chromium，略。


其他
```
npm install sleep
```


centos（CentOS release 6.8 (Final)）安装chromium：
```
yum -y install google-chrome-stable-27.0.1453.110-202711.x86_64.rpm
```

sudo -i
cd /etc/yum.repos.d
wget http://people.centos.org/hughesjr/chromium/6/chromium-el6.repo
yum install chromium



wget http://install.linux.ncsu.edu/pub/yum/itecs/public/chromium/rhel7/noarch/chromium-release-2.2-1.noarch.rpm
yum localinstall chromium-release-2.2-1.noarch.rpm

wget -O /etc/yum.repos.d/chromium-el6.repo http://people.centos.org/hughesjr/chromium/6/chromium-el6.repo



http://li.nux.ro/download/nux/dextop/el6/x86_64/chrome-deps-stable-3.11-1.x86_64.rpm

chmod -R 777 chrome-deps-stable-3.11-1.x86_64.rpm

rpm -i chrome-deps-stable-3.11-1.x86_64.rpm



执行：
google-chrome-stable

报错：
/usr/bin/google-chrome-stable: error while loading shared libraries: libXss.so.1: cannot open shared object file: No such file or directory


wget http://www.openmamba.org/pub/openmamba/devel/RPMS.x86_64/libXScrnSaver-1.2.2-2mamba.x86_64.rpm

chmod -R 777 libXScrnSaver-1.2.2-2mamba.x86_64.rpm

rpm -ivh libXScrnSaver-1.2.2-2mamba.x86_64.rpm

报错：
libXdmcp.so.6()(64bit) is needed by libXScrnSaver-1.2.2-2mamba.x86_64
libbsd.so.0()(64bit) is needed by libXScrnSaver-1.2.2-2mamba.x86_64

wget ftp://ftp.pbone.net/mirror/www.startcom.org/AS-6.0.0/os/x86_64/Packages/openssl098e-0.9.8e-17.SEL6.x86_64.rpm
rpm -ivh openssl098e-0.9.8e-17.SEL6.x86_64.rpm



























