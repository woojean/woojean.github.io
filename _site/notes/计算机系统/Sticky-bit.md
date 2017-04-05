# Sticky bit

具有sticky bit属性的目录，其下的文件或目录只有文件拥有者及root才有权删除。

```
[test@test test]$ ls -l /
drwxrwxrwt 2 root root 4096 Jul 18 13:08 tmp
最末位用t取代x
```