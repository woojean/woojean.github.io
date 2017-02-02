# GitLab University

# 1. GitLab Beginner
## 1.1. Version Control and Git 

### Version Control Systems
1.Local Version Control Systems
perhaps a time-stamped directory,etc.

2.Centralized Version Control Systems:CVS,Subversion,Perforce
whenever you have the entire history of the project in a single place, you risk losing everything.

3.Distributed Version Control Systems:Git,Mercurial,Bazaar,Darcs
Every clone is really a full backup of all the data.

### Operating Systems and How Git Works
无字幕视频，略

### Code School: An Introduction to Git
Code School学习，略


## 1.2. GitLab Basics

### An Overview of GitLab.com
2分钟短视频，略

### Why Use Git and GitLab
功能简介，略。

### GitLab Basics - Article
新建并切换到新的分支：
```
git checkout -b NAME-OF-BRANCH
```

Delete all changes in the Git repository, but leave unstaged things :
```
git checkout .     # 会修改本地文件
```

Delete all changes in the Git repository, including untracked files :
```
git clean -f
```
reset只影响被track过的文件, 所以需要clean来删除没有track过的文件. 结合使用这两个命令能让工作目录完全回到一个指定的commit的状态.比如先使当前版本与origin master一致，然后在本地添加一个文件，再reset --hard origin/master，会发现新添加的文件仍然存在，这时执行git clean -f将会删除该文件。
git clean默认不会删除.gitignore中指定的文件（或文件夹），-xf选项会删除。

可以执行以下命令查看将要被删除的文件：
```
git clean -n
```

### Create and add your SSH Keys



















































