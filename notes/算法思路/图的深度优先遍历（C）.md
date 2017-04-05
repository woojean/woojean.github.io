# 图的深度优先遍历（C）

（1）深度优先遍历算法

```c++
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
```c++
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
```c++
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