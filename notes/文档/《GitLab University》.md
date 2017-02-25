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
git checkout .     # 放弃修改（会修改本地文件）
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
略。

### Create a project
略。

### Create a group
Projects in GitLab can be organized in 2 different ways: under your own namespace for single projects, such as your-name/project-1 or under groups.
略。

## Create a branch
略。

### Fork a project
略。

### Add a file
略。

### Add an image
略。

### Create an issue
略。

### Create a merge request
可以指定Source branch和Target branch。
详略。

## 1.3. Your GitLab Account
### Create a GitLab Account
在线视频教程，略。

### Create and Add your SSH key to GitLab
略。

## 1.4. GitLab Projects
### Repositories, Projects and Groups
略。

### Creating a Project in GitLab
略。

### How to Create Files and Directories
略。

### GitLab Todos
gitlab中的评论可以@用户，被@的用户将会受到通知，在Todos列表中可以看到所有的@。
详略。

### GitLab's Work in Progress (WIP) Flag
Work In Progress, is what you prepend to a merge request in GitLab, preventing your work from being merged. 
方便进行Code Review，以免大量修改后Code Review不方便。
Push something out early to receive feedback and to be steered in the right direction early on.

以WIP或[WIP]为前缀的commit将会当做是WIP处理，即不会被合并（不会被GitLab CI合并）。


## 1.5. Migrating from other Source Control
略。

## 1.6. GitLab Inc.
略。

## 1.7 Community and Support
略。

## 1.8 GitLab Training Material


# 2. GitLab Intermediate

## 2.1 GitLab Pages
pages服务，略。

## 2.2. GitLab Issues
### Markdown in GitLab
GitLab uses "GitLab Flavored Markdown" (GFM). It extends the standard Markdown in a few significant ways to add some useful functionality. 
You can use GFM in the following areas:
1.comments
2.issues
3.merge requests
4.milestones
5.snippets (the snippet must be named with a .md extension)
6.wiki pages
7.markdown documents inside the repository

功能不是很稳定，已有的MarkDown语法也够用了，亮点是有一些简写方式可以调到issue、merge request、milestone等等，用到再看，略。

### Issues and Merge Requests
视频，略。

### Due Dates and Milestones for GitLab Issues
Using milestones `across multiple projects` can help you manage all of the work your team and other teams are doing.
Once the milestone is created, you can add issues to it from any project.

### How to Use GitLab Labels
Labels provide an easy way to categorize the issues or merge requests based on `descriptive titles` like bug, documentation or any other text you feel like. They can have `different colors`, a description, and are visible throughout the issue tracker or inside each issue individually.

可以设置标签的优先级排序，用于issues的排序、过滤等。

可以订阅label，这样每当label被应用时就会收到通知。

### Applying GitLab Labels Automatically
webhook server，需要编程写回调函数实现，略。

### GitLab Issue Board
介绍gitlab issue操作面板的短视频，略。

### An Overview of GitLab Issue Board
gitlab的工作流理念与实现简介。

### Designing GitLab Issue Board
organize issues，略。

### From Idea to Production with GitLab
视频，略。

## 2.3. Continuous Integration
略。

## 2.4. Workflow
### GitLab Flow
略。

### GitLab Flow vs Forking in GitLab
略。

### GitLab Flow Overview
The GitLab flow that integrates the git workflow with an issue tracking system.It offers a simple, transparent and effective way to work with git.
Git Flow , GitHub Flow , GitLab Flow.
详略。

## 2.5. GitLab Comparisons
略。


# 3. GitLab Advanced 
略，



