---
layout: post
title:  "使用XMLWriter输出XML"
date: 2017-04-26 00:00:01
categories: 开发总结
tags: XML PHP
excerpt: ""
---

* content
{:toc}

最终输出效果：
```xml
<?xml version="1.0" encoding="UTF-8"?>
<RootInfo>
  <NS:Item type="1" xmlns:NS="http://www.woojean.com/">
    <NS:id>01</NS:id>
    <name>woojean</name>
  </NS:Item>
</RootInfo>
```
基本覆盖了常用的XML特性：结点、属性、值、命名空间及命名空间前缀。

源码：
```php
<?php
$xml = new XMLWriter();

//$xml->openUri("php://output");
$xml->openURI('test.xml');

$xml->setIndentString('  ');
$xml->setIndent(true);

$xml->startDocument('1.0', 'utf-8');

$xml->startElement("RootInfo");
$xml->startElementNS("NS","Item",'http://www.woojean.com/');
//添加属性
$xml->writeAttribute("type", "1");

$xml->startElementNS("NS","id",null);
$xml->text("01");
$xml->endElement();

$xml->startElement("name");
$xml->text("woojean");
$xml->endElement();

$xml->endElement();
$xml->endElement();
$xml->endDocument();

//header("Content-type: text/xml");
//echo $xml->outputMemory();
```


























