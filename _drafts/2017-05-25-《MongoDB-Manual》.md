---
layout: post
title:  "《MongoDB Manual》"
date: 2017-05-25 00:00:02
categories: 技术书籍文档
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

# The mongo Shell


























































