# 求二叉树的叶子节点数（C）

```c++
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