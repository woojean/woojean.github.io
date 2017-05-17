# 配置JDK
/Library/Java/JavaVirtualMachines/jdk1.8.0_121.jdk/Contents/Home

sudo -i vi /etc/.bash_profile
```
JAVA_HOME=/Library/Java/JavaVirtualMachines/jdk1.8.0_121.jdk/Contents/Home/
CLASSPAHT=.:$JAVA_HOME/lib/dt.jar:$JAVA_HOME/lib/tools.jar
PATH=$JAVA_HOME/bin:$PATH:
export JAVA_HOME
export CLASSPATH
export PATH
```

source /etc/.bash_profile

java -version


# 安装maven
sudo mv apache-maven-3.5.0 /usr/local/apache-maven-3.5.0

/Users/wujian/extensions/apache-maven-3.5.0/bin

sudo -i vi /etc/.bash_profile
```
export M2_HOME=/usr/local/apache-maven-3.5.0
export M2=$M2_HOME/bin
export MAVEN_OPTS="-Xms256m -Xmx512m"
export PATH=$M2:$PATH
```
mvn --version


## 新建项目
cd /Users/wujian/projects/mvntest
mvn archetype:generate -DgroupId=woojean.demo -DartifactId=demoProject -DarchetypeArtifactId=maven-archetype-quickstart -DinteractiveMode=false

## 打包
mvn package

## 运行
java -cp demoProject-1.0-SNAPSHOT.jar woojean.demo.App
java -classpath demoProject-1.0-SNAPSHOT.jar woojean.demo.App


java -classpath demo.jar woojean.demo.App








