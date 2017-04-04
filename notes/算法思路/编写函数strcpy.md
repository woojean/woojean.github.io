# 编写函数strcpy

```c++
//若参数没有const属性，则需要考虑重叠的情况
char *strcpy(char *strDest, const char *strSrc) {
  if ( strDest == NULL || strSrc == NULL)
    return NULL ;

  if ( strDest == strSrc)
    return strDest ;
    
  char *tempptr = strDest ;
  while( (*strDest++ = *strSrc++) != '/0')
    ;
  
  return tempptr ;
}
```