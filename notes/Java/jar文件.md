# jar文件

jar文件实际是Zip文件。
一个jar文件由一组压缩文件构成，同时还有一张描述了所有这些文件的文件清单。

生成jar文件：
```
jar [options] destination [manifest] inputfiles

jar cmf myJarFile.jar myManifestFile.mf *.class
```
不能对已有的jar文件进行添加或更新操作（区别于zip）。