# SUID与SGID

`UID`代表用户代号，`GID`则是群组代号。

```
[test@test test]$ ls -l /usr/bin/passwd
-r-s--x--x 1 root root 13476 Aug 7 2001 /usr/bin/passwd
```
在原来x的位置有一个s属性，这个就是所谓的`SUID`。如果是-r-xr-s--x，那么s就成为所谓的`SGID`。当一个文件具有SUID时，同时others群组具有可执行权限，那么当others群组执行该程序时，others将拥有该文件的owner权限。Set UID（SUID）的主要功能是在某个文件执行其间具有文件拥有者的权限，因此，s可以替代上面提到的x可执行属性的位置。