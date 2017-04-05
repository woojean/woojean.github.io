# 完成一个trim函数，将一个字符串两端的空格、回车、tab符号去掉

```c++
void trim( char *str){
  int i, j;
  assert( str != NULL); // <assert.h>
  
  /*find the first non-space char's position */
  for (i = 0; (str[i] == ' ' || str[i] == '\t') && str[i] != '\0'; i++)
    ;
      
  /*find the last non-space char's position */
  for (j = strlen(str) - 1; (str[j] == ' ' || str[j] == '\t') && j; j--)
    ;
  
  memmove(str, str + i, j - i); // < String.h >
  str[j + 1] = '\0';
}
```
memmove用于从src拷贝count个字节到dest，如果目标区域和源区域有重叠的话，memmove能够保证源串在被覆盖之前将重叠区域的字节拷贝到目标区域中。但复制后src内容会被更改。当目标区域与源区域没有重叠则和memcpy函数功能相同。