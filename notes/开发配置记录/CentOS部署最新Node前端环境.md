# CentOS部署最新Node前端环境

# node

## 安装

node的编译安装依赖巨多,甚至依赖python版本,所以直接使用二进制文件.

```
cd /data/extend/
wget https://nodejs.org/dist/v7.7.3/node-v7.7.3-linux-x64.tar.gz
tar -xvf node-v7.7.3-linux-x64.tar.gz

ln -s /data/extend/node-v7.7.3-linux-x64/bin/node /usr/bin/node
ln -s /data/extend/node-v7.7.3-linux-x64/bin/npm /usr/bin/npm
npm config set registry https://registry.npm.taobao.org  
```

## 输出

```
[root@iZuf6cbroi7rj1zjydjruoZ extend]# node -v
v7.7.3
[root@iZuf6cbroi7rj1zjydjruoZ extend]# npm -v
4.1.2
```

# webpack

## 安装

```
npm install webpack -g
ln -s /data/extend/node-v7.7.3-linux-x64/lib/node_modules/webpack/bin/webpack.js /usr/bin/webpack
```

## 输出

```
[root@iZuf6cbroi7rj1zjydjruoZ extend]# webpack -v
webpack 2.2.1
...
```

# bower

```
npm install -g bower
ln -s /data/extend/node-v7.7.3-linux-x64/lib/node_modules/bower/bin/bower /usr/bin/bower
```

## 输出

```
[root@iZuf6cbroi7rj1zjydjruoZ deploy]# bower -v
1.8.0
```

# 