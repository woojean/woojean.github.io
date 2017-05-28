---
layout: post
title:  "使用Thrift实现PHP调用Java程序"
date: 2017-05-18 00:00:01
categories: 开发总结
tags: PHP Java Thrift
excerpt: ""
---

* content
{:toc}

本文总结了Thrift的最基本应用。
[下载代码:https://github.com/woojean/demos/tree/master/thrift-php-java](https://github.com/woojean/demos/tree/master/thrift-php-java)


# 安装Thrift
略。

# 定义IDL文件
DemoService.thrift
```
namespace php Woojean.Rpc.Demo  // PHP项目的命名空间
namespace java com.woojean.rpc.demo  // Java项目的命名空间

// 异常定义
exception RequestException {
}

// 参数定义
struct Param
{
    1:required string s1,
    2:required string s2,
}


// 服务定义
service DemoService
{
	// 定义一个连接字符串的方法，用一个指定的分隔符连接Param的所有属性，并返回一个完整的字符串
    string joinString(1:required Param p, 2:required string sep) 
        throws (1:RequestException e);
}
```


# 生成文件
# 生成PHP文件
```
thrift -gen php:server DemoService.thrift
```
将会生成以下文件：
```
gen-php/Woojean/Rpc/Demo/DemoService.php  // 存放Rpc接口约定interface DemoServiceIf，以及PHP版的服务端处理程序、客户端class DemoServiceProcessor、class DemoServiceClient
gen-php/Woojean/Rpc/Demo/Types.php        // 存放自定的类型，本例是class RequestException和class Param 
```

# 生成Java文件
```
thrift --gen java DemoService.thrift
```
将会生成以下文件：
```
gen-java/com/woojean/rpc/demo/DemoService.java
gen-java/com/woojean/rpc/demo/Param.java
gen-java/com/woojean/rpc/demo/RequestException.java
```


# 编写Java服务端项目
## 服务端项目文件结构
服务端用maven构建，项目结构如下：
```
Service/
  lib/
    ... // thrift、log4j等jar包
  src/
    /main
      /java
        /com
          /woojean
            /rpc
              /demo
                DemoService.java
                Param.java
                RequestException.java
                RpcHandler.java  // 定义实际被调用的方法
                RpcServer.java   // 实现了一个简单的Server
      /resources
        /META-INF
          MANIFEST.MF  // 打包后使用的MANIFEST信息
  /target
  pom.xml
```

### pom.xml
```xml
<project xmlns="http://maven.apache.org/POM/4.0.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:schemaLocation="http://maven.apache.org/POM/4.0.0 http://maven.apache.org/maven-v4_0_0.xsd">
    <modelVersion>4.0.0</modelVersion>
    <groupId>com.woojean.rpc</groupId>
    <artifactId>demo</artifactId>
    <packaging>jar</packaging>
    <version>1.0</version>
    <name>aid</name>
    <url>http://maven.apache.org</url>

    <build>
        <plugins>
            <plugin>
                <groupId>org.apache.maven.plugins</groupId>
                <artifactId>maven-jar-plugin</artifactId>
                <configuration>
                    <archive>
                        <manifestFile>
                            src/main/resources/META-INF/MANIFEST.MF
                        </manifestFile>
                        <manifest>
                            <addClasspath>true</addClasspath>
                        </manifest>
                    </archive>
                </configuration>
            </plugin>
        </plugins>
    </build>
    <dependencies>
        <dependency>
            <groupId>junit</groupId>
            <artifactId>junit</artifactId>
            <version>3.8.1</version>
            <scope>test</scope>
        </dependency>
        <dependency>
            <groupId>org.apache.thrift</groupId>
            <artifactId>libthrift</artifactId>
            <version>0.10.0</version>
        </dependency>
        <dependency>
            <groupId>org.slf4j</groupId>
            <artifactId>slf4j-log4j12</artifactId>
            <version>1.7.5</version>
        </dependency>
    </dependencies>

</project>
```

### MANIFEST.MF
```
Manifest-Version: 1.0
Created-By: 1.8.0_121 (Oracle Corporation)
Main-Class: com.woojean.rpc.demo.RpcServer
Class-Path: lib/libthrift-0.10.0.jar lib/log4j-1.2.14.jar lib/slf4j-api-1.5.11.jar lib/slf4j-log4j12-1.5.11.jar
```

### RpcHandler.java
```java
package com.woojean.rpc.demo;

import org.apache.thrift.TException;

public class RpcHandler implements DemoService.Iface{

    @Override
    public String joinString(Param p,String sep) throws TException {
        return p.getS1() + sep + p.getS2();
    }

}
```


### RpcServer.java
```java
package com.woojean.rpc.demo;

import org.apache.thrift.TMultiplexedProcessor;
import org.apache.thrift.server.TThreadPoolServer;
import org.apache.thrift.transport.*;
import org.apache.thrift.server.TServer;

public class RpcServer {

    private void start() {
        try {
            TServerSocket serverTransport = new TServerSocket(9524);

            DemoService.Processor demoProcessor = new DemoService.Processor(new RpcHandler());

            TMultiplexedProcessor processor = new TMultiplexedProcessor();

            processor.registerProcessor("DemoService", demoProcessor);

            TServer server = new TThreadPoolServer(new TThreadPoolServer.Args(
                    serverTransport).processor(processor));
            System.out.println("Starting server on port 9524 ...");
            server.serve();

        } catch (TTransportException e) {
            e.printStackTrace();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public static void main(String args[]) {
        RpcServer srv = new RpcServer();
        srv.start();
    }
}
```

# 编写PHP客户端项目
## 客户端项目文件结构
```
Client/
  /Library
    /Thrift  // Thrift的PHP版库 
      ...
  /Woojean
    /Rpc
      /Demo
        DemoSevice.php
        Types.php
  DemoClient.php  // 实际的客户端代码
```

## DemoClient.php
```php
<?php
define('DIR_BACKEND', dirname(__DIR__) . '/Client');
var_dump(DIR_BACKEND);

// 用于自动寻找并加载Thrift库中的类
spl_autoload_register(function ($clientClass) {
    try {
        $class = str_replace('\\', '/', $clientClass);
        $filePath = DIR_BACKEND.'/Library/' . $class . '.php';
        require_once $filePath;
    } catch (\Exception $e) {
        echo $e->getMessage();
        var_dump($clientClass);
    }
});

// 引用Thrift生成的文件
require_once DIR_BACKEND .'/Woojean/Rpc/Demo/DemoService.php';
require_once DIR_BACKEND .'/Woojean/Rpc/Demo/Types.php';


// Demo
use \Thrift\Transport\TSocket;
use \Thrift\Transport\TBufferedTransport;
use \Thrift\Protocol\TBinaryProtocol;
use \Thrift\Protocol\TMultiplexedProtocol;
use \Thrift\Exception\TException;

use \Woojean\Rpc\Demo\DemoServiceClient;
use \Woojean\Rpc\Demo\Param;
use \Woojean\Rpc\Demo\RequestException;

try {
    // 注意端口号与服务端一致
    $socket = new TSocket('0.0.0.0', '9524', TRUE);  

    // 注意传输协议与服务端一致
    $transport = new TBufferedTransport($socket, 1024, 1024);
    $protocol = new TBinaryProtocol($transport);
    $protocol = new TMultiplexedProtocol($protocol, "DemoService");  // 注意服务名与服务端注册的一致
    
    // 构造参数
    $params = new \Woojean\Rpc\Demo\Param();
    $params->s1 = 'Hello';
    $params->s2 = 'World!';
    $sep = '+';

    // 构造客户端
    $client = new DemoServiceClient($protocol);
    $transport->open();

    // 调用Rpc方法
    $ret = $client->joinString($params, $sep);

    // 打印调用结果
    var_dump($ret);  // Hello+World!
    $transport->close();

} catch (RequestException $ex) {
    print 'RequestException: ' . $ex->getMessage() . "\n";
}

```


# 运行
## 启动服务端
```
cd Service/target/
java -jar demo-1.0.jar
```

## 运行客户端
输出：
```
Hello+World!
```


# Thrift生成文件简析
根据本例生成的文件，简单解析Thrift生成的文件及其作用。

## PHP 
PHP共生成2个文件：DemoService.php和Types.php，路径与在IDL中定义的命名空间一致（Woojean\Rpc\Demo）。其中DemoService.php的命名是由于在IDL文件中定义了：
```
// 服务定义
service DemoService
{
  // ...
```
而Types.php的命名是统一的，用于存放在IDL文件中自定义的各种类型。

### Types.php的内容
* class RequestException extends TException
* class Param  // 含s1和s2两个属性
Types.php中定义的每个类都实现了以下方法：getName()、read($input)、write($output)。

### DemoService.php的内容
* interface DemoServiceIf  // 定义了方法joinString(\Woojean\Rpc\Demo\Param $p, $sep)
* class DemoServiceClient implements \Woojean\Rpc\Demo\DemoServiceIf
* class DemoService_joinString_args  // Rpc方法joinString的参数（面向传输的封装：参数也需要传输）
* class DemoService_joinString_result // Rpc方法joinString的返回值（面向传输的封装：返回值也需要传输）
* class DemoServiceProcessor // PHP版的服务端处理类


## Java
Java共生成3个文件：
DemoService.java
Param.java
RequestException.java
因为Java中公共类必须与文件同名，所以每个自定义类型都单独是一个文件。
详略。

