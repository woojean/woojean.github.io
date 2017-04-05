# inotify

Linux的inotify模块基于Hash Tree（即一旦某个文件发生更新，就更新从它开始至根目录的整个路径上的所有目录）来监控文件的更改，以此提高文件更新的效率。原生提供C语言的API，PECL有相应的扩展。