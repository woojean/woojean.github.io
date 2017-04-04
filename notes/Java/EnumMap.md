# EnumMap

EnumMap要求其中的键必须来自一个enum，其在内部通过数组实现，所以速度很快。

```java
interface Command { void action(); }
...
EnumMap<AlarmPoints,Command> em =
  new EnumMap<AlarmPoints,Command>(AlarmPoints.class);
em.put(KITCHEN, new Command() {
  public void action() { print("Kitchen fire!"); }
});
...

for(Map.Entry<AlarmPoints,Command> e : em.entrySet()) {
  printnb(e.getKey() + ": ");
  e.getValue().action();
}

try { // If there's no value for a particular key:
  em.get(UTILITY).action();
} catch(Exception e) {
  print(e);
}
```