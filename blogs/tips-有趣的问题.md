## 设计队列容器的数据结构，使得返回最大元素的操作时间复杂度尽可能的低
解法1：用传统方式来实现队列，采用一个数组或链表来存储队列的元素，利用两个指针分别指向队尾和队首。如果采用这种方法，那么取最大值的操作需要遍历队列的所有元素。时间复杂度为O(N)；
解法2：考虑用最大堆来维护队列中的元素。堆中每个元素都有指向它的后续元素的指针。这样，取最大值操作的时间复杂度为O(1)，而入队和出队操作的时间复杂度为O( logN )。
解法3：对于栈来讲，Push和Pop操作都是在栈顶完成的，所以很容易维护栈中的最大值，它的时间复杂度为O(1),实现代码如下：
```c++
	class stack
	{
	public:
		stack()
		{
			stackTop = -1;
			maxStackItemIndex = -1;
		}
		void Push( Type x)
		{
			stackTop++;
			if( stackTop >= MAXN ) // 溢出
				;
			else
			{
				stackItem[stackTop] = x;
				if( x > Max() ) // 当前插入值为最大值
				{
					link2NextMaxItem[stackTop] = maxStackItemIndex; 
						// 之前的最大值成为第二大的值，即当前值（最大值）的下一个最大值
					maxStackItemIndex = stackTop; // 最大值坐标指向当前值
				}
				else
					link2NextMaxItem[stackTop] = -1;
			}	
		}

		Type Pop()
		{
			Type ret;
			if( stackTop < 0 )
				ThrowException(); // 没有元素了
			else
			{
				ret = stackItem[ stackTop ];
				if( stackTop == maxStackItemIndex ) // 当前出栈的为最大值
					maxStackItemIndex = link2NextMaxItem[stackTop];	// 修改最大值坐标
				stackTop--;
			}
			return ret;
		}
		
		Type Max()
		{
			if( maxStackItemIndex >= 0 )
				return stackItem[ maxStackItemIndex];
			else 
				return –INF;
		}
		
	private:
		Type stackItem[MAXN];
		int stackTop;
		int link2NextMaxItem[MAXN]; // 维护一个最大值序列
		int maxStackItemIndex;
	}
```
如果能够用栈有效地实现队列，而栈的Max操作又很容易实现，那么队列的Max操作也就能有效地完成了。考虑使用两个栈A跟B来实现队列。
```c++
class Queue
{
public:
	Type MaxValue( Type x, Type y)
	{
		if( x > y )
			return x;
		else
			return y;
	}

	Type Queue::Max()
	{
		return MaxValue( stackA.Max(), stackB.Max() );
	}

	EnQueue( v )
	{
		stackB.push( v );
	}
	
	Type DeQueue()
	{
		if( stackA.empty() )
		{
			while( !stackB.empty() )
				stackA.push( stackB.pop() )
		}
		return stackA.pop();
	}

private:
	stack stackA;
	stack stackB;
}
```
从每个元素的角度来看，它被移动的次数最多可能有3次，这3次分别是：从B栈进入、当A栈为空时从B栈弹出并压入A栈、从A栈被弹出。相当于入队经过一次操作，出队经过两次操作。所以这种方法的平均时间复杂度是线性的。
























