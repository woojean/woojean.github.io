# MangoDB

## Mac安装MangoDB

```
brew update
brew install mongodb
mongod --config /usr/local/etc/mongod.conf

mongo
db.test.insert({'name':'test'}) WriteResult({ "nInserted" : 1 })
db.test.find()
```

