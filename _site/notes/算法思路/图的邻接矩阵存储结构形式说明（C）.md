# 图的邻接矩阵存储结构形式说明（C）

```c++
#define MaxVertexNum l00
typedef struct{
  char vexs[MaxVertexNum];   // 顶点表
  int edges[MaxVertexNum][MaxVertexNum]; // 邻接矩阵，可看作边表
  int n,e; // 图中当前的顶点数和边数
}MGragh;
```
