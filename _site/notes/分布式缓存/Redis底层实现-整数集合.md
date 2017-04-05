# Redis底层实现-整数集合

当一个集合只包含整数值元素，并且这个集合的元素数量不多时，Redis就会使用整数集合作为底层实现。

```
redis> SADD numbers 1 3 5 7 9
(integer) 5
redis> OBJECT ENCODING numbers
"intset"
```

## 整数集合的实现
```
typedef struct intset {    
  uint32_t encoding;  // 编码方式  
  uint32_t length;    // 集合包含的元素数量 
  int8_t contents[];  // 保存元素的数组，按值大小有序排列且不重复
} intset;
```

## 升级
虽然intset结构将contents属性声明为int8_t类型的数组，但实际上contents数组并不保存任何int8_t类型的值，contents数组的真正类型取决于encoding属性的值。
每当要将一个新元素添加到整数集合里面，并且新元素的类型比整数集合现有所有元素的类型都要长时，整数集合需要先进行升级，然后才能将新元素添加到整数集合里面。

升级过程（主要就是内存的重新分配），详略。

升级的好处：
1.提升整数集合的灵活性：可以随意地将int16_t、int32_t或者int64_t类型的整数添加到集合中，而不必担心出现类型错误；
2.尽可能地节约内存：升级操作只会在有需要的时候进行；

## 降级
整数集合不支持降级操作，一旦对数组进行了升级，编码就会一直保持升级后的状态。