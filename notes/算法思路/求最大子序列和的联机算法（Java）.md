# 求最大子序列和的联机算法（Java）

```java
public class MaxSubsequenceSum {
	
	public static int maxSubsequenceSum(int[] a){
		int  thisSum, maxSum, i;
		int N = a.length;
		thisSum = maxSum = 0;

// 只用一次循环
		for( i=0; i<N; i++ ){ 
			thisSum += a[i];
			if( thisSum > maxSum )
				maxSum = thisSum;
			else if( thisSum < 0 )  // 当当前累加和为负数时，和置0
				thisSum = 0;
		}
		return maxSum;
	}

    public static void main(String[] args) { 
		int[] a = {1,2,3,-4,4,-2,-9,9,9,9,-100,-7,9,9,9,-50,8,8,-8};  //
		System.out.println(maxSubsequenceSum(a));
	}
}
```