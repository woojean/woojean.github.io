# 判断二叉树是否相等（C）

```c++
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
```
由于需要比较的状态是两棵树的任意状态，而二叉树上的每一个节点的左右子节点都可以交换，因此一共需要对比2^n种状态。算法复杂度是O（2^n）