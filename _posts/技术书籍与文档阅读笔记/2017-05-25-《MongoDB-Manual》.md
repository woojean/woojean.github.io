---
layout: post
title:  "《MongoDB Manual》(未完)"
date: 2017-05-25 00:00:02
categories: 技术书籍与文档阅读笔记
tags: MongoDB
excerpt: ""
---

* content
{:toc}

[MongoDB Manual](https://docs.mongodb.com/manual/)

# Getting Started with MongoDB (MongoDB Shell Edition)
## Introduction to MongoDB
MongoDB是一种基于文档（Document）的数据库，Document的结构类似JSON对象（实际使用BSON对象，具有比JSON更好的查找性能，且能直接存储二进制数据）。
Document保存在集合（Collections）中，Document对应关系数据库中的一行记录，Collections对应关系数据库中的表。区别在于，关系数据库表中的每一行记录都是同构的，而同一个Collections的不同Document可以异构。
每一个Document都必须包含一个名为`_id`的字段，其作用类似关系数据库的主键。

## Install MongoDB
略

## MongoDB Shell (mongo)
略

## Insert Data with the mongo Shell
如果往一个不存在的Collection中添加新的Document，MongoDB会自动创建该Collection。

```
use test

db.restaurants.insert(
   {
      "address" : {
         "street" : "2 Avenue",
         "zipcode" : "10075",
         "building" : "1480",
         "coord" : [ -73.9557413, 40.7720266 ]
      },
      "borough" : "Manhattan",
      "cuisine" : "Italian",
      "grades" : [
         {
            "date" : ISODate("2014-10-01T00:00:00Z"),
            "grade" : "A",
            "score" : 11
         },
         {
            "date" : ISODate("2014-01-16T00:00:00Z"),
            "grade" : "B",
            "score" : 17
         }
      ],
      "name" : "Vella",
      "restaurant_id" : "41704620"
   }
)
```
输出：
```
WriteResult({ "nInserted" : 1 })
```
如果通过insert插入的记录中没有指明_id字段，Mongo Shell会自动生成并添加该字段。

## Find or Query Data with the mongo Shell
在MongoDB中，所有的查询的作用域都限制为单个的Collection。
查询返回的是一个游标（cursor）形式的结果，游标可以用来获取实际的Document。
```
db.restaurants.find()  // 查询集合中的所有文档
db.restaurants.find( { "borough": "Manhattan" } )        // 使用文档的一级字段进行过滤查询
db.restaurants.find( { "address.zipcode": "10075" } )    // 使用文档的嵌套（二级）字段进行过滤查询
db.restaurants.find( { "grades.grade": "B" } )           // 使用文档的数组字段进行过滤查询
db.restaurants.find( { "grades.score": { $gt: 30 } } )   // Greater Than
db.restaurants.find( { "grades.score": { $lt: 10 } } )   // Less Than
db.restaurants.find( { "cuisine": "Italian", "address.zipcode": "10075" } ) // AND

// OR
db.restaurants.find(
   { $or: [ { "cuisine": "Italian" }, { "address.zipcode": "10075" } ] }
)

db.restaurants.find().sort( { "borough": 1, "address.zipcode": 1 } )   // 1是正序，2是倒序
```
条件字段和条件值都需要使用引号。

## Update Data with the mongo Shell
不能更新_id字段。
```
// 更新文档的一级字段
db.restaurants.update(
    { "name" : "Juni" },
    {
      $set: { "cuisine": "American (New)" },      // $set操作符，设置字段的值，如果字段不存在则新建
      $currentDate: { "lastModified": true }      // $currentDate操作符，将字段的值设为当前的日期
    }
)

// 更新文档的嵌套字段
db.restaurants.update(
  { "restaurant_id" : "41156888" },
  { $set: { "address.street": "East 31st Street" } }
)

// 更新多个文档
// update()方法默认只更新单个文档，可以使用multi选项来指定更新所有符合过滤条件的文档
db.restaurants.update(
  { "address.zipcode": "10016", cuisine: "Other" },
  {
    $set: { cuisine: "Category To Be Determined" },
    $currentDate: { "lastModified": true }
  },
  { multi: true}
)

// 更新整个文档
// 新的文档可以和旧文档结构不一样
// 可以不传_id字段，如果传了，必须保证_id和旧文档的_id一样
db.restaurants.update(
   { "restaurant_id" : "41704620" },
   {
     "name" : "Vella 2",
     "address" : {
              "coord" : [ -73.9557413, 40.7720266 ],
              "building" : "1480",
              "street" : "2 Avenue",
              "zipcode" : "10075"
     }
   }
)
```

使用upsert选项可以实现如果被更新的记录不存在，则新增一条记录。

MongoDB中在同一个文档上的写操作都是原子性的。如果一个更新操作将修改多个文档，该更新操作可以和其他的写操作交错进行。

## Remove Data with the mongo Shell
```
db.restaurants.remove( { "borough": "Manhattan" } )  // 删除所有符合条件的文档 
db.restaurants.remove( { "borough": "Queens" }, { justOne: true } )  // 只删除1个文档
db.restaurants.remove( { } )  // 删除condition中的所有文档
db.restaurants.drop()  // 删除condition
```

## Data Aggregation with the mongo Shell
```
// 使用文档的borough字段作为分组的键
// 使用累加器$sum来计算每个分组的计数
db.restaurants.aggregate(
   [
     { $group: { "_id": "$borough", "count": { $sum: 1 } } }
   ]
);
```
输出：
```
// The output documents only contain the identifier field and, if specified, accumulated fields.
{ "_id" : "Staten Island", "count" : 969 }
{ "_id" : "Brooklyn", "count" : 6086 }
{ "_id" : "Manhattan", "count" : 10259 }
{ "_id" : "Queens", "count" : 5656 }
{ "_id" : "Bronx", "count" : 2338 }
{ "_id" : "Missing", "count" : 51 }
```

在进行聚合操作时，也可以指定过滤条件：
```
db.restaurants.aggregate(
   [
     { $match: { "borough": "Queens", "cuisine": "Brazilian" } },
     { $group: { "_id": "$address.zipcode" , "count": { $sum: 1 } } }
   ]
);
```

## Indexes with the mongo Shell
在创建Collection时，MongoDB会自动在_id字段上创建一个索引。
只有当索引不存在时，才会创建成功。
```
db.restaurants.createIndex( { "cuisine": 1 } )  // 创建一个单列索引
db.restaurants.createIndex( { "cuisine": 1, "address.zipcode": -1 } )  // 创建一个多列索引
```
创建索引后的返回类似：
```
{
  "createdCollectionAutomatically" : false,
  "numIndexesBefore" : 1,
  "numIndexesAfter" : 2,
  "ok" : 1
}
```

## Drivers
略


# Introduction to MongoDB
基于文档、高性能、富查询语言、高可用、原生水平扩展、支持多种存储引擎。

## Databases and Collections
MongoDB用BSON保存文档，档保存在集合中，集合保存在数据库中。

在保存数据时，如果数据库和集合不存在，MongoDB会自动创建数据库和集合：
```
use myNewDB   // 尚不存在的新的数据库
db.myNewCollection1.insertOne( { x: 1 } )
```
也可以使用db.createCollection()来显式地创建集合，使用这种方式创建可以指定更多的选项，比如文档数量的上限。

默认情况下集合的文档可以异构，从3.2版本开始，可以在更新和插入操作时进行文档规则校验。

### Views
从3.4版本开始，可以为已存在的集合或视图创建只读的视图。
详略。

### Capped Collections
封顶集合是尺寸固定的集合，能够有效提高对集合的读写吞吐率。其工作方式类似循环缓冲区，当集合满时，新的文档将覆盖最旧的文档。

通过在调用db.createCollection()时指定capped为true来创建封顶集合：
```
db.createCollection("log", { capped : true, size : 5242880, max : 5000 } )
```
插入一个没有索引的capped collection速度非常接近存储在文件系统。为了最大化性能，如果只有读操作，建议不要在capped collection上创建索引。当然如果有更新capped collection的需要，最好创建索引以避免对collection的遍历。

不能删除capped collection的文档，但是可以drop capped collection。

对capped collection的查询操作默认按插入顺序返回记录。使用自然排序（natural ordering）可以快速地查询最新插入的记录（类似tail操作）:
```
db.cappedCollection.find().sort( { $natural: -1 } )
```

其他操作：
```
// 判断一个collection是否是capped collection
db.collection.isCapped()

// 将一个普通collection转换为capped collection（会造成全局写锁）
db.runCommand({"convertToCapped": "mycoll", size: 100000});
```

**MongoDB的操作日志文件oplog.rs就是利用Capped Collection来实现的。**

## Documents
文档字段的类型可以是任意BSON Type，比如：
```
var mydoc = {
  _id: ObjectId("5099803df3f4948bd2f98391"),
  name: { first: "Alan", last: "Turing" },
  birth: new Date('Jun 23, 1912'),
  death: new Date('Jun 07, 1954'),
  contribs: [ "Turing machine", "Turing test", "Turingery" ],
  views : NumberLong(1250000)
}
```

字段名的约束：
* _id被保留作为主键使用，不能为array
* 不能以$开始
* 不能包含.
* 不能包含null

尽管BSON本身支持同名字段，但是MongoDB的Driver实现大多不支持。

可以以索引值的方式访问数组的元素，如对于：
```
{
   ...
   contribs: [ "Turing machine", "Turing test", "Turingery" ],
   ...
}
```
可以这样访问其第3个元素：
```
contribs.2
```

BSON文档的最大尺寸为16M字节

_id的值的约束，略。

## BSON Types
略。

## Comparison/Sort Order
当对不同的BSON类型的数据进行比较时，会遵循一定的顺序，比如Object在Array之前，详略。


## MongoDB Extended JSON
JSON只能表示BSON类型的子集，为了保留类型信息，MongoDB对JSON格式进行了如下扩展：
* 严格模式（Strict mode）：将一些BSON类型转换为JSON形式，任何JSON解析器都可以解析该形式，但是只有MongoDB内置的JSON解析器才可以识别出这些类型信息。
* mongo shell mode；

例如Binary类型在两种模式下分别如下表示：
```
// 严格模式
{ "$binary": "<bindata>", "$type": "<t>" }

// Shell Mode
BinData ( <t>, <bindata> )
```
其中：
* <bindata>是二进制字符串的base64编码表示；
* <t>是一个字节的数据，用来表示数据的类型；

详略。


# Install MongoDB
略。

# The Mongo Shell
**.mongorc.js文件**
可以把自己写的js代码保存在某个地方，让MongoDB加载它，然后就可以在Mongo Shell的命令行里使用它们。
Mongodb Shell默认会尝试加载~/.mongorc.js文件。

如果collection的命名包含特殊字符，比如空格或者以数字开始，可以使用一些变通的方法来引用collection：
```
db.3test.find()    // SyntaxError
db["3test"].find()
db.getCollection("3test").find()
```

find()方法返回指向文档集合的路由，但是当在Shell中执行时，在没有将返回路由复制给变量的情况下（通过var关键字赋值），MongoDB Shell将会自动迭代并展示前20个文档信息。

**格式化输出**
* db.myCollection.find().pretty()  // json格式化输出
* print()  // 不带格式输出
* print(tojson(<obj>))  // json格式化输出
* printjson() // json格式化输出

**跨行操作**
当某一行以括号((、[、{)结尾时，可以跨行操作：
```
> var x = 1;
> if(x>0){
... print(x);
... }

1
```
如果连续输入两个空行，将会结束跨行操作模式。
Mongo Shell是一个JavaScript接口，所以可以直接执行JavaScript代码。

## Configure the mongo Shell
可以通过设置prompt变量来自定义Mongo Shell的提示符。可以将设置prompt变量的逻辑放在.mongorc.js文件中。
举例：
```
1>
test@myHost1$
Uptime:5897 Documents:6 >
```

可以设置当在Mongo Shell中要编辑代码时的编辑器：
```
export EDITOR=vim
mongo
```
详略。

可以设置Mongo Shell自动解析find()结果的数量（默认为20条）：
```
DBQuery.shellBatchSize = 10;
```

## Access the mongo Shell Help
略

## Write Scripts for the mongo Shell
就是在mongo Shell中编写并执行JavaScript代码。略。

## Data Types in the mongo Shell
Mongo Shell对BSON扩展类型的支持。略。

## mongo Shell Quick Reference
在Mongo Shell中可以打开并使用另一个db：
```
db = connect("<host><:port>/<dbname>")
db....
```
详略。

# MongoDB CRUD Operations
## Insert Documents
```
db.collection.insertOne()
db.collection.insertMany()
db.collection.insert()
db.collection.save()
db.collection.bulkWrite()
```
当使用`upsert:true`选项时，如下的方法也会插入新的文档：
```
db.collection.update()
db.collection.updateOne()
db.collection.updateMany()
db.collection.findAndModify()
db.collection.findOneAndUpdate()
db.collection.findOneAndReplace()
```

## Query Documents
```
db.inventory.find( {} )  // 查找所有文档（传一个空的过滤条件）
db.inventory.find( { status: "D" } )  // 按条件查询
db.inventory.find( { status: { $in: [ "A", "D" ] } } )  // 使用查询操作符
db.inventory.find( { status: "A", qty: { $lt: 30 } } )  // AND
db.inventory.find( { $or: [ { status: "A" }, { qty: { $lt: 30 } } ] } )  // OR

// AND OR
db.inventory.find( {
     status: "A",
     $or: [ { qty: { $lt: 30 } }, { item: /^p/ } ]
} )
```

### Query on Embedded/Nested Documents
```
db.inventory.find( { size: { h: 14, w: 21, uom: "cm" } } )  // 注意这种方式查询，字段的顺序也要与字段被插入时一致

db.inventory.find( { "size.uom": "in" } )  // 使用.操作符

db.inventory.find( { "size.h": { $lt: 15 } } )  // 使用运算符
```

### Query an Array
```
db.inventory.find( { tags: ["red", "blank"] } )  // tags字段必须是一个数组，且该数组的字段顺序要与查询条件一致

db.inventory.find( { tags: { $all: ["red", "blank"] } } )  // 数组元素的顺序可以不一致，只要都有就行

db.inventory.find( { tags: "red" } )  // 数组中必须包含red元素

db.inventory.find( { dim_cm: { $gt: 25 } } )  // 数组中必须包含大于等于25的元素

db.inventory.find( { dim_cm: { $gt: 15, $lt: 20 } } )  // dim_cm数组必须存在满足2个条件的元素，比如1个元素满足条件1，另一个满足条件2

db.inventory.find( { dim_cm: { $elemMatch: { $gt: 22, $lt: 30 } } } )  // 必须存在同时满足2个条件的元素

db.inventory.find( { "dim_cm.1": { $gt: 25 } } )  // 根据数组指定位置元素进行查询

db.inventory.find( { "tags": { $size: 3 } } )  // 根据数组大小进行查询
```

### Query an Array of Embedded Documents
```
// { item: "journal", instock: [ { warehouse: "A", qty: 5 }, { warehouse: "C", qty: 15 } ] },

db.inventory.find( { "instock": { warehouse: "A", qty: 5 } } )    // 顺序要一致
db.inventory.find( { 'instock.0.qty': { $lte: 20 } } )  // 同时使用数组索引和嵌套字段 
db.inventory.find( { "instock.qty": { $gt: 10,  $lte: 20 } } )  // 存在满足条件1的元素，且存在满足条件2的元素
db.inventory.find( { "instock": { $elemMatch: { qty: { $gt: 10, $lte: 20 } } } } )  // 存在同时满足条件1和2的元素
```
总之，当对数组字段继续过滤查询时，不使用$elemMatch操作符，则意味着只要每个查询条件都能够在数组中找到至少一个满足条件的元素即可。如果使用$elemMatch操作符，则意味着数组中至少存在一个同时满足所有条件的元素。

### Project Fields to Return from Query
```
// { item: "journal", status: "A", size: { h: 14, w: 21, uom: "cm" }, instock: [ { warehouse: "A", qty: 5 } ] },

db.inventory.find( { status: "A" }, { item: 1, status: 1 } )  // 只返回_id，item和status字段
db.inventory.find( { status: "A" }, { item: 1, status: 1, _id: 0 } )   // 只返回item和status字段
db.inventory.find( { status: "A" }, { status: 0, instock: 0 } )  // 返回除了status和instock之外的所有字段

// 指定嵌套文档中应该返回的字段
db.inventory.find(
   { status: "A" },
   { item: 1, status: 1, "size.uom": 1 }
)

// 排除嵌套文档中指定的字段
db.inventory.find(
   { status: "A" },
   { "size.uom": 0 }
)

db.inventory.find( { status: "A" }, { item: 1, status: 1, "instock.qty": 1 } )  // instock数组中的元素只返回qty字段

db.inventory.find( { status: "A" }, { name: 1, status: 1, instock: { $slice: -1 } } )  // 只返回instock数组的最后一个元素
```

### Query for Null or Missing Fields
```
/*
{ _id: 1, item: null },
{ _id: 2 }
*/

db.inventory.find( { item: null } )  // item字段为null或者不存在item字段
db.inventory.find( { item : { $type: 10 } } )  // item字段为null（BSON Type Null (i.e. 10) ）
db.inventory.find( { item : { $exists: false } } )  // item字段不存在
```

### Iterate a Cursor in the mongo Shell
在Mongo Shell中，当把find()的返回结果（游标）通过var赋值给一个变量时，find()返回的结果不会自动迭代。
```
var myCursor = db.users.find( { type: 2 } );

// 自动遍历20次
myCursor  

// 手动遍历
while (myCursor.hasNext()) {
   print(tojson(myCursor.next()));
}

// 通过forEach遍历
myCursor.forEach(printjson);

// 转成数组后进行操作
var documentArray = myCursor.toArray();
var myDocument = documentArray[3];
```

游标的其他操作，比如设置不过期，多个查询的游标隔离，统计当前打开的游标数量及状态等等，详略。

## Update Documents
```
// { item: "canvas", qty: 100, size: { h: 28, w: 35.5, uom: "cm" }, status: "A" },

db.inventory.updateOne(
   { item: "paper" },  // 过滤条件
   {
     $set: { "size.uom": "cm", status: "P" },
     $currentDate: { lastModified: true }   // 将lastModified字段设为当前时间，如果字段不存在则新建
   }
)

db.inventory.updateMany(
   ...
)


// 当使用replace操作时，不能带update操作符
db.inventory.replaceOne(
   { item: "paper" },
   { item: "paper", instock: [ { warehouse: "A", qty: 60 }, { warehouse: "B", qty: 40 } ] }
)
```

一旦值被设置，_id字段的值就不可改变，也不能通过带有新的_id值的文档来覆盖旧的文档。


### Update Methods
```
db.collection.updateOne()
db.collection.updateMany()
db.collection.replaceOne()
db.collection.update()
```

## Delete Documents
```
db.collection.deleteMany()
db.collection.deleteOne()
db.collection.remove()  // When talking about node.js drivers, remove has been deprecated
```

## Bulk Write Operations
db.collection.bulkWrite()方法支持批量地插入、更新、删除操作。在进行批量操作时，支持排序行为。默认采用排序方式，当采用排序方式操作时，所有操作要逐个进行，发生错误时，后续操作终止。当采用非排序方式操作时，所有操作可并发进行，失败的操作不影响其他并行操作。
```
try {
   db.characters.bulkWrite(
      [
         { insertOne :
            {
               "document" :
               {
                  "_id" : 4, "char" : "Dithras", "class" : "barbarian", "lvl" : 4
               }
            }
         },
         { insertOne :
            {
               "document" :
               {
                  "_id" : 5, "char" : "Taeln", "class" : "fighter", "lvl" : 3
               }
            }
         },
         { updateOne :
            {
               "filter" : { "char" : "Eldon" },
               "update" : { $set : { "status" : "Critical Injury" } }
            }
         },
         { deleteOne :
            { "filter" : { "char" : "Brisbane"} }
         },
         { replaceOne :
            {
               "filter" : { "char" : "Meldane" },
               "replacement" : { "char" : "Tanys", "class" : "oracle", "lvl" : 4 }
            }
         }
      ]
   );
}
catch (e) {
   print(e);
}
```

**Strategies for Bulk Inserts to a Sharded Collection**
略。

## SQL to MongoDB Mapping Chart
略。

## Read Concern*
readConcern选项用于控制从查询中返回的数据。有以下几个等级：
* local 默认值，所读的数据不保证会被最终写入（可能被回滚）
* majority 返回最新的，已确认写入的数据
* linearizable 返回不可能被回滚的数据

可用于find、aggregate、distinct、count、parallelCollectionScan、geoNear、geoSearch等操作。
```
db.restaurants.find( { _id: 5 } ).readConcern("linearizable").maxTimeMS(10000)
```

详略。


## Write Concern*
略。

## MongoDB CRUD Concepts
### Atomicity and Transactions
当同时更新多个文档时，可以使用$isolated操作符来将多个写操作组合在一起，并与其它的写操作隔离。在这些写操作执行期间，其它的对相同文档的写操作请求将被阻止。
不过，当被隔离的请求中发生错误时，错误之前的操作并不会回滚，即隔离操作仅起到对一组资源进行写锁的功能，并不提供回滚功能。

### Read Isolation, Consistency, and Recency
* Read Uncommitted
使用local级别的readConcern的客户端可以读取其他客户端的尚未被持久化的写操作的结果（可能会被回滚）。
* Read Uncommitted And Single Document Atomicity
写操作在单个文档上是原子的，即不可能读取到只有部分更新的单一文档。
* Read Uncommitted And Multiple Document Write
$isolated operator，略。
* Cursor Snapshot
略。

### Distributed Queries
sharded clusters，分片集群可以实现以对应用程序透明的方式将数据集分散存储到不同的Mongo Server（集群）中。
指向分片数据集（被分片处理的Collection的其中一个分片）的查询需要带上一个分片键（shard key），mongoDB会通过Config Database中配置的集群元数据（cluster metadata）来将查询定向到指定的分片数据集。
如果查询中没有带上shard key，那么Mango会将查询定向到集群中所有的分片，这是一种低效行为。

Read Operations to Replica Sets，略。

### Distributed Write Operations
略。

### Perform Two Phase Commits
因为MongoDB的文档支持嵌套，所以单文档可以解决大多数的关系型数据库所面临的跨表操作事务问题。
因为MongoDB对于单文档的操作都是原子性的，所以two-phase commits只能提供类似事务的操作，而非真正的事务操作。

```
db.accounts.insert(
   [
     { _id: "A", balance: 1000, pendingTransactions: [] },
     { _id: "B", balance: 1000, pendingTransactions: [] }
   ]
)

// 每一笔账户变动都需要插入一条记录
db.transactions.insert(
    { _id: 1, source: "A", destination: "B", value: 100, state: "initial", lastModified: new Date() }
)


var t = db.transactions.findOne( { state: "initial" } )
db.transactions.update(
    { _id: t._id, state: "initial" },
    {
      $set: { state: "pending" },   // 更新转账记录的状态为处理中
      $currentDate: { lastModified: true }
    }
)

// 扣减转出账户
db.accounts.update(
   { _id: t.source, pendingTransactions: { $ne: t._id } },  // 防止重复执行
   { $inc: { balance: -t.value }, $push: { pendingTransactions: t._id } }
)

// 增加转入账户
db.accounts.update(
   { _id: t.destination, pendingTransactions: { $ne: t._id } },
   { $inc: { balance: t.value }, $push: { pendingTransactions: t._id } }
)

// 更新转账记录
db.transactions.update(
   { _id: t._id, state: "pending" },
   {
     $set: { state: "applied" },   // 更新转账记录状态为已执行
     $currentDate: { lastModified: true }
   }
)


// 删除转出账户中的待处理操作
db.accounts.update(
   { _id: t.source, pendingTransactions: t._id },
   { $pull: { pendingTransactions: t._id } }
)

// 删除转入账户中的待处理操作
db.accounts.update(
   { _id: t.destination, pendingTransactions: t._id },
   { $pull: { pendingTransactions: t._id } }
)

// 更新转账操作为已完成
db.transactions.update(
   { _id: t._id, state: "applied" },
   {
     $set: { state: "done" },
     $currentDate: { lastModified: true }
   }
)
```

根据lastModified来判断操作是否超时，进而判断操作失败，并进行回滚操作:
```
var dateThreshold = new Date();
dateThreshold.setMinutes(dateThreshold.getMinutes() - 30);

var t = db.transactions.findOne( { state: "pending", lastModified: { $lt: dateThreshold } } );
```

总之就是自己维护一致性的逻辑，具体的恢复操作，略。

### Linearizable Reads via findAndModify*
略。

### Query Plans
查询优化器生成查询计划，并缓存。

可以使用db.collection.explain()或cursor.explain()来查看指定查询所生成的查询计划。

详略。

### Query Optimization
#### 创建索引
```
db.inventory.createIndex( { type: 1 } )
```

#### 覆盖索引
```
db.inventory.createIndex( { type: 1, item: 1 } )
db.inventory.find(
   { type: "food", item:/^c/ },
   { item: 1, _id: 0 }   // 必须要排除_id，因为它没有包含在组合索引中
)
```

如果一个用于创建索引的字段是数组，则该索引将会转化为一个多键数组，而且不能支持覆盖索引查询。
如果一个用于创建索引的字段属于嵌套文档，则不能支持覆盖索引：
数据结构：
```
{ _id: 1, user: { login: "tester" } }
```
索引：
```
{ "user.login": 1 }
```
如下的查询将不能使用覆盖索引（不过仍然可以使用该索引来匹配查询）：
```
db.users.find( { "user.login": "tester" }, { "user.login": 1, _id: 0 } )
```

对于sharded collection，当查询未包含shard key时，将不能使用覆盖索引。_id除外，当查询条件中仅仅设置了根据_id查询，且查询结果也仅返回_id字段时，_id索引将可以覆盖此查询。

#### Evaluate Performance of Current Operations
db.currentOp()可以用来评估当前连接的客户端的操作的性能。

#### Optimize Query Performance
如果索引是BinData类型，且满足以下条件时，效率将更高：
* 值在0~7或者128~135之间；
* 数组的长度为：0，1，2，3，4，5，6，7，8，10，12，14，16，20，24，32

*限制返回的数量*
```
db.posts.find().sort( { timestamp : -1 } ).limit(10)
```

*只返回需要的字段*
```
db.posts.find( {}, { timestamp : 1 , title : 1 , author : 1 , abstract : 1} ).sort( { timestamp : -1 } )
```

*使用$hint来设置想要使用的索引*
*使用$inc操作符来执行数字增加操作*

#### Write Operation Performance
索引会增加写操作的开销。

MMAPv1存储引擎，略。

为了提供高可用性而对写操作做的日志也会增加开销，详略。

#### Explain Results*
略，用到再看。

### Analyze Query Performance
分析查询：
```
// { "_id" : 1, "item" : "f1", type: "food", quantity: 500 }

db.inventory.find(
   { quantity: { $gte: 100, $lte: 200 } }
).explain("executionStats")
```
输出：
```
{
   "queryPlanner" : {
         "plannerVersion" : 1,
         ...
         "winningPlan" : {
            "stage" : "COLLSCAN",  // COLLSCAN说明执行了collection scan
            ...
         }
   },
   "executionStats" : {
      "executionSuccess" : true,
      "nReturned" : 3,  // 匹配到并返回了3条记录
      "executionTimeMillis" : 0,
      "totalKeysExamined" : 0,
      "totalDocsExamined" : 10,  // scan了10条记录
      "executionStages" : {
         "stage" : "COLLSCAN",   
         ...
      },
      ...
   },
   ...
}
```

创建索引：
```
db.inventory.createIndex( { quantity: 1 } )
```
winningPlan的stage变为IXSCAN（Index Scan），totalDocsExamined变为3。

#### Compare Performance of Indexes
```
db.inventory.createIndex( { quantity: 1, type: 1 } )
db.inventory.createIndex( { type: 1, quantity: 1 } )

// 使用hint设置要使用的索引
db.inventory.find(
   { quantity: { $gte: 100, $lte: 300 }, type: "food" }
).hint({ quantity: 1, type: 1 }).explain("executionStats")
```
输出：
```
{
   "queryPlanner" : {
      ...
      "winningPlan" : {
         "stage" : "FETCH",
         "inputStage" : {
            "stage" : "IXSCAN",
            "keyPattern" : {
               "quantity" : 1,
               "type" : 1
            },
            ...
            }
         }
      },
      "rejectedPlans" : [ ]
   },
   "executionStats" : {
      "executionSuccess" : true,
      "nReturned" : 2,
      "executionTimeMillis" : 0,
      "totalKeysExamined" : 5,   // 共扫描了5个索引
      "totalDocsExamined" : 2,
      "executionStages" : {
      ...
      }
   },
   ...
}
```
换个索引：
```
db.inventory.find(
   { quantity: { $gte: 100, $lte: 300 }, type: "food" }
).hint({ type: 1, quantity: 1 }).explain("executionStats")
```
totalKeysExamined的值变为2。说明对于该查询，显然第2个索引效率更高。

### Tailable Cursors
当写操作量比较大时，通过建索引来提高查询效率的做法是不实际的。对于Capped Collection，可以使用tailable cursors来实现tail最新插入的记录。MongoDB的操作日志同步就是采用这种方式。

对于建有索引的collection，应该使用常规的coursor，并记录每次查询得到的最新的索引的值以便以后查询使用：
```
db.<collection>.find( { indexedField: { $gt: <lastvalue> } } )
```

# Aggregation

To be continue...


































