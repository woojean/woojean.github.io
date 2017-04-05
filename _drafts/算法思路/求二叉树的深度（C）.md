# 求二叉树的深度（C）

```c++
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