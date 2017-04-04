# 如何为SELECT语句添加一个自动增加的列？

```
set @N = 0;
SELECT @N := @N +1 AS number, name, surname FROM gbtags_users;
```

