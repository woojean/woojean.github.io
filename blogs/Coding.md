## 写内存拷贝
```
void * memcpy (void * dst, const void * src, size_t count){
  
  void * ret = dst;
  
  while (count--) {
    *(char *)dst = *(char *)src;
    dst = (char *)dst + 1;
    src = (char *)src + 1;
  }
  return(ret);
}
```

## 选择排序
首先找到最小元素置于起始位置，再从剩余元素中继续寻找最小者放到已排序序列末尾，依次类推（不稳定排序）
```
void SelectionSort(int arr[],int len){
  
  int i,j,min,tmp;

  for(i = 0; i < len; i++){
    min = i;
    for(j = i; j < len; j++){
      if(arr[j] < arr[min]){
        min = j;
      }
    }
    tmp = arr[i];
    arr[i] = arr[min];
    arr[min] = tmp;
  }
}
```

## 插入排序
对于未排序数据在已排序序列中从后向前扫描，找到相应位置并插入（稳定排序）
```
void InsertionSort(int arr[],int len){
  
  int i,j,tmp;

  for(i = 1; i < len; i++){
    tmp = arr[i];
    for(j = i; j>0 && arr[j-1]>tmp; j--){
      arr[j] = arr[j-1];
      arr[j-1] = tmp;
    }
  }
}
```

## 快速排序
（不稳定排序）
```
void QuickSort(int a[],int low,int high){
  
  int i = low;
  int j = high;  
  int temp = a[i]; 
  
  if( low < high){          
    while(i < j)  // 若条件为i<=j，则将所有判定都加等号则会发生死循环
    {
      while((a[j] >= temp) && (i < j)){ 
        j--; 
      }
      a[i] = a[j];

      while((a[i] <= temp) && (i < j)){
        i++; 
      }  
      a[j]= a[i];  
    }
    a[i] = temp;
    QuickSort(a,low,i-1);
    QuickSort(a,j+1,high);
  }
}
```


## 冒泡排序
（稳定排序）
```
void BubbleSort(int arr[],int len){
  
  int i,j,tmp;

  for(i = 0; i < len; i++){
    for(j = i+1; j < len; j++){
      if(arr[j] < arr[i]){
        tmp = arr[j];
        arr[j] = arr[i];
        arr[i] =tmp;
      }
    }
  }
}
```


## 希尔排序
先取一个正整数d1 < N，把所有相隔d1的元素放一组，共d1组，组内进行直接插入排序，再取d2 < d1，重复上述步骤，直至d=1.`只要最终步长为1，任何步长序列都可以`，当步长为1时，算法即为插入排序。（不稳定排序）
```
void ShellSort(int arr[],int len){
  
  int i,j,incr,tmp;
  
  // 14,7,3,1
  for(incr = len/2; incr > 0; incr /= 2){
    for(i = incr; i < len; i++){
      tmp = arr[i];
      for(j = i; j >= incr; j -= incr){
        if(tmp < arr[j-incr]){
          arr[j] = arr[j-incr];
        }
        else{
          break;
        }
      }
      arr[j] = tmp;
    }
  }
}
```

## 归并排序
（稳定排序）
```
void  Merge( int arr[], int tmpArray[], int lBegin, int rBegin, int rEnd )
{
  int i, lEnd, len, tmpPos;
  lEnd = rBegin - 1;
  tmpPos = lBegin;
  len = rEnd - lBegin + 1;
  
  /* main loop */
  while( lBegin <= lEnd && rBegin <= rEnd ){
    if( arr[ lBegin ] <= arr[ rBegin ] ){
      tmpArray[ tmpPos++ ] = arr[ lBegin++ ];
    }
    else{
      tmpArray[ tmpPos++ ] = arr[ rBegin++ ];
    }
  }
  while( lBegin <= lEnd ){
    tmpArray[ tmpPos++ ] = arr[ lBegin++ ];
  }

  while( rBegin <= rEnd ){
    tmpArray[ tmpPos++ ] = arr[ rBegin++ ];
  }

  for( i = 0; i < len; i++, rEnd-- ){
    arr[ rEnd ] = tmpArray[ rEnd ];
  }
}


void MSort( int arr[ ], int tmpArray[ ], int left, int right )
{
  int mid;
  if( left < right )
  {
    mid = ( left + right ) / 2;
    MSort( arr, tmpArray, left, mid );
    MSort( arr, tmpArray, mid + 1, right );
    Merge( arr, tmpArray, left, mid + 1, right );
  }
}
       
       
void  MergeSort( int arr[ ], int len )
{
  int *tmpArray;
  tmpArray = malloc( len * sizeof( int ) );
  if( tmpArray != NULL )
  {
    MSort( arr, tmpArray, 0, len - 1 );
    free( tmpArray );
  }
  else
    printf( "No space for tmp array!!!" );
}
```

## 堆排序
（不稳定排序）
```
#define LeftChild(i)  (2*(i) + 1)

//对数组A中以下标为i的元素作为根，大小为len的元素序列构成的堆进行堆调整，使该根节点放到合适的位置
void Sink(int arr[],int i,int len){
  int child,tmp;

  for(tmp = arr[i]; LeftChild(i)<len; i=child){
    child = LeftChild(i);

    if( child != len-1 && arr[child+1] > arr[child]){
      child++;
    }

    if( tmp < arr[child] ){
      arr[i] = arr[child];
    }
    else{
      break;
    }
  }
  arr[i] = tmp;
}

void HeapSort(int arr[],int len){
  int i,tmp;
  for( i = len/2; i >= 0; i-- ){  // BuildHeap从下往上建堆
    Sink( arr, i, len );
  }
  
  for( i = len - 1; i > 0; i-- ){ 
    /* DeleteMax */
    tmp = arr[0];
    arr[0] = arr[i];
    arr[i] = tmp;
    Sink( arr, 0, i );
  }

}
```

## 计数排序
（稳定排序）
```
void CountingSort(int arr[],int len){
  
  int i, min, max;
  min = max = arr[0];

  // 找出范围
  for(i = 1; i < len; i++) {
    if (arr[i] < min)
      min = arr[i];
    else if (arr[i] > max)
      max = arr[i];
  }
  int range = max - min + 1;
  
  int *count = (int*)malloc(range * sizeof(int));
  for(i = 0; i < range; i++){
    count[i] = 0;
  }
  for(i = 0; i < len; i++){
    count[ arr[i] - min ]++;
  }
  
  int j, z = 0;
  for(i = min; i <= max; i++){
    for(j = 0; j < count[ i - min ]; j++){
      arr[z++] = i;
    }
  }
  free(count);
}
```

## 层序遍历二叉树
```
#define MAX_NODE  50
void LevelorderTraverse( BTNode  *T){  
  BTNode  *Queue[MAX_NODE] ,*p=T ;
  int  front=0 , rear=0 ;
  if (p!=NULL){  
    Queue[++rear]=p;    // 根结点入队
    
    // 当队列不为空时
    while (front < rear){  
      p = Queue[++front];
      visit( p->data );
      if (p->Lchild!=NULL){
        Queue[++rear]=p;  // 左结点入队
      }                  
      if (p->Rchild!=NULL){
        Queue[++rear]=p;  // 右结点入队
      }          
    }
  }
}
```

## 求二叉树的深度
```
#define MAX_NODE 50
int search_depth( BTNode  *T){  
  BTNode  *Stack[MAX_NODE] ,*p=T;
  int  front=0 , rear=0, depth=0, level ;
  
  // level总是指向访问层的最后一个结点在队列的位置
  if (T!=NULL){  
    Queue[++rear]=p;    // 根结点入队
    level=rear ;    // 根是第1层的最后一个节点
    while (front < rear){  
      p=Queue[++front]; 
      if (p->Lchild != NULL){
        Queue[++rear]=p;    // 左结点入队
      }                  
      if (p->Rchild!=NULL){
        Queue[++rear]=p;    // 右结点入队
      }              
      if (front == level){  
        // 正访问的是当前层的最后一个结点
        depth++ ;  
        level=rear ;  
      }
    }
  }
}
```


## 前序遍历二叉树
遍历二叉树的算法中基本操作是访问结点，因此，`无论是哪种次序的遍历，对有n个结点的二叉树，其时间复杂度均为O(n)` 。
递归算法:
```
void PreorderTraverse(BTNode  *T){  
  if( T!=NULL ){  
    visit(T->data) ;       // 访问根结点
    PreorderTraverse(T->Lchild) ;
    PreorderTraverse(T->Rchild) ;     
  }
}
```

非递归算法:
设T是指向二叉树根结点的指针变量，非递归算法是：
若二叉树为空，则返回；否则，令p=T；
⑴ 访问p所指向的结点；
⑵ q=p->Rchild ，若q不为空，则q进栈；
⑶ p=p->Lchild ，若p不为空，转(1)，否则转(4)；
⑷ 退栈到p ，转(1)，直到栈空为止。
```
#define  MAX_NODE  50
void PreorderTraverse( BTNode  *T){  
  BTNode *Stack[MAX_NODE] , *p=T,  *q ;
  int  top=0 ;
  if  (T==NULL){  
    printf(“ Binary Tree is Empty!\n”) ;
  }
  else {  
    do{  
      visit( p-> data ) ;   
      q=p->Rchild ; 
      if ( q!=NULL ){  
        Stack[++top]=q ;
      }          
      p=p->Lchild ; 
      if (p==NULL){ 
        p=Stack[top--] ;  
      }  
    }
    while (p!=NULL) ;
  }
}
```

## 中序遍历二叉树
递归算法
```
void  InorderTraverse(BTNode  *T){  
  if (T!=NULL){  
    InorderTraverse(T->Lchild) ;
    visit(T->data) ;       // 访问根结点
    InorderTraverse(T->Rchild) ;
  }
}   
```
中序遍历二叉树（非递归算法）
设T是指向二叉树根结点的指针变量，非递归算法是：
若二叉树为空，则返回；否则，令p=T
⑴ 若p不为空，p进栈， p=p->Lchild ；否则(即p为空)，退栈到p，访问p所指向的结点；
⑵ p=p->Rchild ，转(1)；
直到栈空为止。
```
#define MAX_NODE  50
void  InorderTraverse( BTNode  *T){  
  BTNode  *Stack[MAX_NODE] ,*p=T ;
  int top=0 , bool=1 ;
  if (T==NULL){  
    printf(“ Binary Tree is Empty!\n”) ;
  }  
  else{ 
    do{ 
      while (p!=NULL){  
        stack[++top]=p ;    
        p=p->Lchild ;   
      }
      if (top==0){  
        bool=0 ;
      }
      else{  
        p=stack[top--] ;  
        visit( p->data ) ;  
        p=p->Rchild ; 
      }
    } while (bool!=0) ;
  }
 }
```

## 后序遍历二叉树
递归算法
```
void  PostorderTraverse(BTNode  *T){  
  if (T!=NULL) {  
    PostorderTraverse(T->Lchild) ;
    PostorderTraverse(T->Rchild) ; 
    visit(T->data) ;   // 访问根结点 
  }
}   
```
设T是指向根结点的指针变量，后序遍历二叉树的非递归算法是：
若二叉树为空，则返回；否则，令p=T；
⑴ 第一次经过根结点p，不访问： p进栈S1 ， tag 赋值0，进栈S2，p=p->Lchild 。
⑵ 若p不为空，转(1)，否则，取状态标志值tag；
⑶ 若tag=0：对栈S1，不访问，不出栈；修改S2栈顶元素值(tag赋值1) ，取S1栈顶元素的右子树，即p=S1[top]->Rchild ，转(1)；
⑷ 若tag=1：S1退栈，访问该结点；
直到栈空为止。


## 完成一个trim函数，将一个字符串两端的空格、回车、tab符号去掉。
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
memmove用于从src拷贝count个字节到dest，如果目标区域和源区域有重叠的话，memmove能够保证源串在被覆盖之前将重叠区域的字节拷贝到目标区域中。但复制后src内容会被更改。当目标区域与源区域没有重叠则和memcpy函数功能相同。


## 链表反转
struct Item{
    char c;
    Item *next;
};

Item *Reverse( Item *x ){
    Item *prev = NULL,*curr = x;
    while ( curr ){
        Item *next = curr->next;
        curr->next = prev;
        prev = curr;
        curr = next;
    }
    return prev;
}

int main(){
    Item *x,
        d = {"d", Null},
        c = {"c", &d},
        b = {"b", &c},
        a = {"a", &b};
    x = Reverse( &a );
}

## 求最大的子序列和的联机算法
```
int  MaxSubSequenceSum(const int arr[],int len){
  int  tmpSum, maxSum, j;
  tmpSum = maxSum = 0;
  
  for( j=0; j<len; j++ ){
    tmpSum += arr[j];
    if(tmpSum > maxSum){
      maxSum = tmpSum;
    }
    else if(tmpSum < 0){
      tmpSum = 0;
    }
  }
  return maxSum;
}
```

## 编写函数strcpy
```
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

## 写出斐波那契数列的递归与迭代代码，并分析时间和空间复杂度。
斐波那契数列指的是这样一个数列：1、1、2、3、5、8、13、21、……     
用数学公式表示出来就是：
  F（1）= 1，F（2）=1     (n=1,2)
  F(n)=F(n-1)+ F(n-2)      (n>2)
递归法：
  Fib(1) = 1 [基本情况]  
  Fib(2) = 1 [基本情况] 
  对所有n > 1的整数：Fib(n) = (Fib(n-1) + Fib(n-2)) [递归定义]
关键代码：
```
if(n == 1|| n== 2){
  return 1;
}
else{
  return fib(n - 1) + fib(n - 2);
}
```

迭代法:
```
int f(int n){
  int i, f1 = 1, f2 = 1, f3;
  if(n<=0){
    printf("输入错误.\n");
  }
  else if( n==1 || n==2 ){
    printf("1");
  }
  else{
    for( i=0; i < n-2; i++ ){
      f3 = f1+f2;           // f1 表示当前的值
      f2=f1;
      f1=f3;
    }   
    printf("%d\n",f1);
  }
}
```

## 合并有序链表
递归算法所体现的“重复”一般有三个要求： 
一是每次调用在规模上都有所缩小(通常是减半)； 
二是相邻两次重复之间有紧密的联系，前一次要为后一次做准备(通常前一次的输出就作为后一次的输入)； 
三是在问题的规模极小时必须用直接给出解答而不再进行递归调用，因而每次递归调用都是有条件的(以规模未达到直接解答的大小为条件)，无条件递归调用将会成为死循环而不能正常结束。

如何设计递归算法
1.确定递归公式 
2.确定边界(终了)条件递归实现：

算法思想：
递归终止条件：若head1为空，返回head2指针（head）；若head2为空，返回head1指针（head）
递归过程：
1.若head1->data > head2->data; head 指针应该指向head2所指向的节点，而且head->next应该指向head1和head2->next两个链表的合成序列的头指针；
2.否则head 指针应该指向head1所指向的节点，而且head->next应该指向head->next和head2两个链表的合成序列的头指针；

实现代码：  
```
#include <iostream>
using namespace std;
    
// 节点的类定义
class Node{
  public:
  int data;
  Node * next;
  Node(int data){
    this->data=data;
  }
};

// 链表的类定义
class LinkedList{
  public:
    Node * head;
    
    // 用一个整形数组作为参数的构造函数
    LinkedList(int array[]){
      head=new Node(array[0]);
      Node * temp = head;
      int i;
      for(i=1;i<3;i++){
        temp->next=new Node(array[i]);
        temp=temp->next;
      }
      temp->next=NULL;
    }
};

// 递归的合并两个有序链表
Node * mergeLinkedList(Node * head1, Node * head2){   
  Node *p=NULL;   
  if(head1==NULL && head2==NULL){   
    return p;   
  }
  else if( head1==NULL ){   
    return head2;
  }   
  else if( head2==NULL ){   
    return head1;
  }   
  else{   
    if( head1->data < head2->data ){   
      p = head1;   
      p->next = mergeLinkedList( head1->next,head2 );   
    }   
    else{
      p = head2;   
      p->next = mergeLinkedList( head1,head2->next );   
    }   
    return p;   
  }   
} 

// 打印链表的所有元素
void printList(Node * head){
  Node * temp=head;
  while(temp!=NULL){
    cout<<temp->data<<"  ";
    temp=temp->next;
  }
}

int main(){
  int array1[3]={2,5,8};
  int array2[3]={1,6,7};

  // 构造两个有序链表--list1和list2
  LinkedList list1(array1);
  LinkedList list2(array2);

  // 递归的将这两个有序链表合并成一个有序链表
  Node * new_head = mergeLinkedList(list1.head, list2.head);
    
  // 打印有序链表
  printList(new_head);
  return 0;
}
```

## 把两个链表合并成一个新的有序链表，返回头指针
递归终止条件：若head1为空，返回head2指针（head）；若head2为空，返回head1指针（head）
递归过程：
（1）若head1->data > head2->data; head 指针应该指向head2所指向的节点，而且head->next应该指向head1和head2->next两个链表的合成序列的头指针；
（2）否则head 指针应该指向head1所指向的节点，而且head->next应该指向head->next和head2两个链表的合成序列的头指针；

实现代码（C++）： 
```   
#include <iostream>
using namespace std;
    
/*节点的类定义*/
class Node{
public:
  int data;
  Node * next;
  Node(int data){
    this->data=data;
  }
};

/*链表的类定义*/
class LinkedList{
public:
  Node * head;
  /*用一个整形数组作为参数的构造函数*/
  LinkedList(int array[]){
    head=new Node(array[0]);
    Node * temp=head;
    int i;
    for(i=1;i<3;i++){
      temp->next=new Node(array[i]);
      temp=temp->next;
    }
    temp->next=NULL;
  }
};

/*递归的合并两个有序链表*/
Node * mergeLinkedList(Node * head1,Node * head2){   
  Node *p=NULL;   
  if(head1==NULL && head2==NULL){
            return p;
  }   
  else if(head1==NULL){   
    return head2;
  }   
  else if(head2==NULL){
    return head1;
  }   
  else{   
    if(head1->data < head2->data){   
      p = head1;   
      p->next = mergeLinkedList(head1->next,head2);   
    }   
    else{
      p = head2;   
      p->next = mergeLinkedList(head1,head2->next);   
    }   
    return p;   
  }   
} 

/*打印链表的所有元素*/
void printList(Node * head){
  Node * temp=head;
  while(temp!=NULL){
    cout<<temp->data<<"  ";
    temp=temp->next;
  }
}

int main(){
  int array1[3]={2,5,8};
  int array2[3]={1,6,7};

  /*构造两个有序链表--list1和list2*/
  LinkedList list1(array1);
  LinkedList list2(array2);

  /*递归的将这两个有序链表合并成一个有序链表*/
  Node * new_head=mergeLinkedList(list1.head,list2.head);
    
  /*打印有序链表*/
  printList(new_head);
  return 0;
}
```

## 二分查找法
```
int HalfSearch(int arr[], int low, int high, int num){
  int mid;
  mid = (low+high) / 2;
  if( (low>=high) && (arr[mid]!=num) ){
    return -1;
  }
  else{
    if( arr[mid]==num ){
      return mid;
    }
    else if( arr[mid]>num ){
      high = mid-1;
    }
    else{ 
      low = mid+1;
    }
    return HalfSearch(arr,low,high,num);
  }
}
```

## 求二叉树的叶子节点数
```
#define  MAX_NODE  50
int search_leaves( BTNode  *T){  
  BTNode  *Stack[MAX_NODE] ,*p=T;
  int top=0, num=0;
  if(T!=NULL){  
    stack[++top]=p ; 
    while( top > 0 ){  
      p = stack[top--] ;
      if( p->Lchild==NULL && p->Rchild==NULL ){
        num++ ;
      }   
      if( p->Rchild != NULL ){
        stack[++top]=p->Rchild; 
      }  
      if(p->Lchild != NULL ){
        stack[++top]=p->Lchild; 
      } 
    }
  }
  return(num) ;
}
```

## 判断二叉树是否相等
typedef struct _TreeNode{
  char c;
  TreeNode *leftchild;
  TreeNode *rightchild;
}TreeNode;

// A、B两棵树相等当且仅当RootA->c==RootB-->c,而且A和B的左右子树相等或者左右互换相等。
int CompTree(TreeNode* tree1,TreeNode* tree2){
  if( tree1==NULL && tree2 == NULL ){
    Return 0;
  }
  if( tree1 == NULL || tree2 == NULL ){
    return 1;
  }
  if( tree1->c != tree2->c){
    return 1;
  }
  
  if( CompTree(tree1->leftchild, tree2->leftchild) == 0  && CompTree(tree1->rightchild, tree2->rightchild) == 0 ){
    return 0;
  }

  if( CompTree(tree1->leftchild, tree2->rightchild) == 0 && CompTree(tree1->rightchild, tree2->leftchild) == 0 ){
    return 0;
  }
}
由于需要比较的状态是两棵树的任意状态，而二叉树上的每一个节点的左右子节点都可以交换，因此一共需要对比2^n种状态。算法复杂度是O（2^n）


## 图的邻接矩阵存储结构形式说明
```
#define MaxVertexNum l00
typedef struct{
  char vexs[MaxVertexNum];   // 顶点表
  int edges[MaxVertexNum][MaxVertexNum]; // 邻接矩阵，可看作边表
  int n,e; // 图中当前的顶点数和边数
}MGragh;
```

## 如果收到一个字符串型的浮点数，比如“1234.56”，如何变成浮点数
```
double atof(char s[]){
  double val,power;
  int i,sign;

  for( i=0; isspace(s[i]); i++) //跳过空白
    ;
  
  sign=(s[i]=='-') ? -1 : 1;    //判断符号
  
  if( s[i]=='+' || s[i]=='-' ){
    i++;
  }
  
  for( val=0.0; isdigit(s[i]); i++){
    val = 10.0*val + (s[i]-'0');  // 此步骤也可用于求解“将一个字符串的整数变成整数”
  }  
  
  if(s[i]=="."){
    i++;
  }

  for(power=1.0;isdigit(s[i]);i++){
    val=10.0*val+(s[i]-'0');
    power*=10.0;
  }
  return sign*val/power;
}
```

## 设计队列容器的数据结构，使得返回最大元素的操作的时间复杂度尽可能的低
解法1：用传统方式来实现队列，采用一个数组或链表来存储队列的元素，利用两个指针分别指向队尾和队首。如果采用这种方法，那么取最大值的操作需要遍历队列的所有元素。时间复杂度为O(N)；
解法2：考虑用最大堆来维护队列中的元素。堆中每个元素都有指向它的后续元素的指针。这样，取最大值操作的时间复杂度为O(1)，而入队和出队操作的时间复杂度为O( logN )。
解法3：对于栈来讲，Push和Pop操作都是在栈顶完成的，所以很容易维护栈中的最大值，它的时间复杂度为O(1),实现代码如下：
```
class stack{
public:
  stack(){
    stackTop = -1;
    maxStackItemIndex = -1;
  }
  
  void Push( Type x){
    stackTop++;
    if( stackTop >= MAXN ){ // 溢出
      ;
    }
    else{
      stackItem[stackTop] = x;
      if( x > Max() ){ // 当前插入值为最大值
        link2NextMaxItem[stackTop] = maxStackItemIndex; 
        // 之前的最大值成为第二大的值，即当前值（最大值）的下一个最大值
        maxStackItemIndex = stackTop; // 最大值坐标指向当前值
      }
      else
        link2NextMaxItem[stackTop] = -1;
      }   
  }

  Type Pop(){
    Type ret;
    if( stackTop < 0 ){
      ThrowException(); // 没有元素了
    }
    else{
      ret = stackItem[ stackTop ];
      if( stackTop == maxStackItemIndex ){ // 当前出栈的为最大值
        maxStackItemIndex = link2NextMaxItem[stackTop]; // 修改最大值坐标
        stackTop--;
      }
      return ret;
    }
        
  Type Max(){
    if( maxStackItemIndex >= 0 ){
      return stackItem[ maxStackItemIndex];
    }
    else{
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
```
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

## 图的邻接表的形式说明及其建表算法
对图的每个顶点建立一个单链表（n个顶点建立n个单链表），第i个单链表中的结点包含顶点Vi的所有邻接顶点。又称链接表。
（1）邻接表的形式说明  
```
// 边表结点
typedef struct node{ 
  int adjvex; // 邻接点域
  struct node *next; // 链域
  // 若要表示边上的权，则应增加一个数据域
}EdgeNode;

// 顶点表结点
typedef struct vnode{  
  int vertex; // 顶点域
  EdgeNode *firstedge;  // 边表头指针
}VertexNode;

typedef VertexNode AdjList[MaxVertexNum]; //AdjList是邻接表类型
```

（2）建立无向图的邻接表算法
```
// 建立无向图的邻接表表示
void CreateALGraPh(ALGrahp *G){
  int i,j,k;
  EdgeNode *s；
  scanf("％d％d"，&G->n，&G->e); // 读入顶点数和边数
  for(i=0;i<G->n;i++){
    //建立顶点表
    G->adjlist[i].vertex=getchar(); //读入顶点信息
    G->adjlist[i].firstedge=NULL;//边表置为空表
  }
      
  for(k=0; k<G->e; k++){
    //建立边表
    scanf("％d％d",&i,&j);否则读入边(vi，vj)的顶点对序号
    s=(EdgeNode *)malloc(sizeof(EdgeNode));  //生成边表结点
    s->adjvex=j; //邻接点序号为j
    s->next=G->adjlist[i].firstedge;
    G->adjlist[i].firstedge=s; //将新结点*s插入顶点vi的边表头部
    s=(EdgeNode *)malloc(sizeof(EdgeNode));
    s->adjvex=i; //邻接点序号为i
    s->next=G->adjlist[j].firstedge;
    G->adjlistk[j].firstedge=s; //将新结点*s插入顶点vj的边表头部
  }//end for 
}CreateALGraph
```

## 图的广度优先遍历
(1)邻接表表示图的广度优先搜索算法
```
// 以vk为源点对用邻接表表示的图G进行广度优先搜索
void BFS(ALGraph*G，int k){
  int i;
  CirQueue Q;    //须将队列定义中DataType改为int
  EdgeNode *p;
  InitQueue(&Q); //队列初始化
  printf("visit vertex：％e",G->adjlist[k].vertex); //访问源点vk
  visited[k]=TRUE; 
  EnQueue(&Q，k); //vk已访问，将其人队。（实际上是将其序号人队）
  while(!QueueEmpty(&Q)){ //队非空则执行
    i=DeQueue(&Q); //相当于vi出队
    p=G->adjlist[i].firstedge; //取vi的边表头指针
    while(p){ //依次搜索vi的邻接点vj(令p->adjvex=j)
      if(!visited[p->adivex]){ //若vj未访问过
        printf("visitvertex：％c",C->adjlistlp->adjvex].vertex); //访问vj
        visited[p->adjvex]=TRUE; 
        EnQueue(&Q，p->adjvex);//访问过的vj人队
      }//endif
      p=p->next; //找vi的下一邻接点
    }//endwhile
  }//endwhile
}//end of BFS
```

（2）邻接矩阵表示的图的广度优先搜索算法
```
// 以vk为源点对用邻接矩阵表示的图G进行广度优先搜索
void BFSM(MGraph *G，int k){
  int i,j;
  CirQueue Q;
  InitQueue(&Q);
  printf("visit vertex:％c",G->vexs[k]); //访问源点vk
  visited[k]=TRUE;
  EnQueue(&Q,k);
  while(!QueueEmpty(&Q)){
    i=DeQueue(&Q); //vi出队
    //依次搜索vi的邻接点vj
    for(j=0;j<G->n;j++){
      if(G->edges[i][j]==1&&!visited[j]){ //vi未访问
        printf("visit vertex:％c"，G->vexs[j]);//访问vi
        visited[j]=TRUE;
        EnQueue(&Q,j);//访问过的vi人队
      }
    }
  }//endwhile
}//BFSM
```
对于具有n个顶点和e条边的无向图或有向图，每个顶点均入队一次。广度优先遍历(BFSTraverse)图的时间复杂度和DFSTraverse算法相同。
当图是连通图时，BFSTraverse算法只需调用一次BFS或BFSM即可完成遍历操作，此时BFS和BFSM的时间复杂度分别为O(n+e)和0(n2)。

## 大数相加问题
实现A+B=C,其中A、B位数超过100位 
算法思想：大数使用字符串存储，每一个单元存储操作数的每一位，之后执行位相加。
基本思路：字符串反转、字符变数字、位运算、反序输出
C语言代码：
```
#include <stdio.h>   
#include<string.h>   
#define Max 101   

void print(char sum[], int result_len);  
int bigNumAdd(char a[],char b[],char sum[]);  
  
int main()  {  
  char a[Max]={0};  
  char b[Max]={0};  
  char sum[Max]={0};  
  puts("input a:");  
  gets(a);             /*  char* gets(char*buffer); 头文件stdio.h .gets(s)函数与scanf("%s",s)相似，但不完全相同，使用scanf("%s",s) 函数输入字符串时存在一个问题，就是如果输入了空格会认为字符串结束，空格后的字符将作为下一个输入项处理，但gets()函数将接收输入的整个字符串直到遇到换行为止 */
  puts("input b:");  
  gets(b);  
  print(sum, bigNumAdd(a,b,sum));  
  return 0;  
}  
  
int bigNumAdd(char a[], char b[], char sum[]){  
  int i=0;  
  int c=0;  // 表示进位   
  
  char m[Max]={0};  
    char n[Max]={0};  
    memset(sum,0,Max*sizeof(char));  // 重要
  
    // 字符串反转且字符串变数字   
    int lenA=strlen(a);  
    int lenB=strlen(b);  
      
    int result_len = (lenA > lenB)?lenA:lenB;  
    for (i=0;i<lenA;i++){  
        m[i]=a[lenA-i-1]-'0';  
    }  

    for (i=0;i<lenB;i++){  
        n[i]=b[lenB-i-1]-'0';  
    }  
  
    // 按位运算   
    for (i=0;i<lenA||i<lenB;i++){  
        sum[i]=(m[i]+n[i]+c)%10+'0';  // 得到末位   
        c=(m[i]+n[i]+c)/10;  // 得到进位   
    }  
  
    if (c){  
        result_len++;// 最后一次有进位，长度+1   
    }  
    return result_len;  
}  
  
void print(char sum[], int result_len){  
  int j=0;  
  int i=0;  
  
  for(j=result_len-1; j>=0; j--){  
    i++;  
    printf("%c",sum[j]);  
  }  
  puts("\n");  
} 
```

## 图的深度优先遍历
（1）深度优先遍历算法
```
typedef enum{FALSE，TRUE} Boolean;  // FALSE为0，TRUE为1
Boolean visited[MaxVertexNum]; // 访问标志向量是全局量
    
// 深度优先遍历以邻接表表示的图G，而以邻接矩阵表示G时，算法完全与此相同
void DFSTraverse(ALGraph *G){ 
  int i;
  for(i=0;i<G->n;i++){
    visited[i]=FALSE; //标志向量初始化
  }
    
  for(i=0; i<G->n; i++){
    if(!visited[i]){ //vi未访问过
      DFS(G，i)； //以vi为源点开始DFS搜索
    }
  }
}
```

（2）邻接表表示的深度优先搜索算法
```
void DFS(ALGraph *G，int i){ 
  // 以vi为出发点对邻接表表示的图G进行深度优先搜索
  EdgeNode *p;
  printf("visit vertex：％c"，G->adjlist[i].vertex);  //访问顶点vi
  visited[i]=TRUE; //标记vi已访问
  p=G->adjlist[i].firstedge; // 取vi边表的头指针
  while(p){ //依次搜索vi的邻接点vj，这里j=p->adjvex
    if (!visited[p->adjvex]){// 若vi尚未被访问
      DFS(G，p->adjvex);//则以Vj为出发点向纵深搜索
    }
    p=p->next; // 找vi的下一邻接点
  }
}
```

（3）邻接矩阵表示的深度优先搜索算法
```
void DFSM(MGraph *G，int i){ 
  // 以vi为出发点对邻接矩阵表示的图G进行DFS搜索，设邻接矩阵是0,l矩阵
  int j;
  printf("visit vertex：％c"，G->vexs[i]); //访问顶点vi
  visited[i]=TRUE;
  for(j=0;j<G->n;j++){ //依次搜索vi的邻接点
    if(G->edges[i][j]==1&&!visited[j]){
        DFSM(G，j)//(vi，vj)∈E，且vj未访问过，故vj为新出发点
    }
  }
}
```
对于具有n个顶点和e条边的无向图或有向图，遍历算法DFSTraverse对图中每顶点至多调用一次DFS或DFSM。从DFSTraverse中调用DFS(或DFSM)及DFS(或DFSM)内部递归调用自己的总次数为n。
当访问某顶点vi时，DFS(或DFSM)的时间主要耗费在从该顶点出发搜索它的所有邻接点上。用邻接矩阵表示图时，其搜索时间为O(n)；用邻接表表示图时，需搜索第i个边表上的所有结点。因此，对所有n个顶点访问，在邻接矩阵上共需检查n2个矩阵元素，在邻接表上需将边表中所有O(e)个结点检查一遍。
所以，DFSTraverse的时间复杂度为O(n2) （调用DFSM）或0(n+e)（调用DFS）。