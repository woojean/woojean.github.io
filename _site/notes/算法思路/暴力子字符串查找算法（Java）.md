# 暴力子字符串查找算法（Java）

## 暴力子字符串查找算法一
```java
public static int violenceSearch(String pat,String txt){
		int M = pat.length();
		int N = txt.length();
		
		for(int i = 0; i <= N-M; i++){
			int j;
			for(j=0; j<M; j++)
				if(txt.charAt(i+j) != pat.charAt(j))
					break;
			if(j == M)
				return i;
		}
		return -1;
	}
```

## 暴力子字符串查找算法二
显式回退：
```java
public static int violenceSearch2(String pat,String txt){
		int j,M = pat.length();
		int i,N = txt.length();
		
		for(i=0,j=0; i<N && j<M; i++){
			if(txt.charAt(i) == pat.charAt(j))
				j++;
			else{
				i-=j;  // 虽然只有一个循环，但是循环指针存在回退操作
				j=0;
			}
		}
		if(j==M)
			return i-M;
		else
			return -1;
	}
```