# 图的邻接表的形式说明及其建表算法（C）

对图的每个顶点建立一个单链表（n个顶点建立n个单链表），第i个单链表中的结点包含顶点Vi的所有邻接顶点。又称链接表。
（1）邻接表的形式说明  

```c++
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
```c++
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