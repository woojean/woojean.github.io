# 层序遍历二叉树（C）

```c++
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