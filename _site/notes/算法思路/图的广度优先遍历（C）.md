# 图的广度优先遍历（C）

(1)邻接表表示图的广度优先搜索算法

```c++
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
```c++
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