# EnumSet

EnumSet用以替代传统的基于int的位标志（更具表达性），EnumSet中的元素必须来自一个enum，其内部将一个long值作为比特向量（即用一个long值的不同位的状态来表示某个元素是否存在），所以EnumSet非常快速高效。

```java
public enum AlarmPoints {
  STAIR1, STAIR2, LOBBY, OFFICE1, OFFICE2, OFFICE3,
  OFFICE4, BATHROOM, UTILITY, KITCHEN
}

import java.util.*;
...
EnumSet<AlarmPoints> points =
  EnumSet.noneOf(AlarmPoints.class); // Empty set
points.add(BATHROOM);
print(points);
points.addAll(EnumSet.of(STAIR1, STAIR2, KITCHEN));
print(points);
points = EnumSet.allOf(AlarmPoints.class);
points.removeAll(EnumSet.of(STAIR1, STAIR2, KITCHEN));
print(points);
points.removeAll(EnumSet.range(OFFICE1, OFFICE4));
print(points);
points = EnumSet.complementOf(points);
print(points);
```