# BM字符串查找算法（Java）

BM算法的效率比KMP高，是一种从后往前扫描的算法。当某个字符不匹配时（坏字符），检查该主串中的不匹配的字符是否出现在匹配字符串中的其他位置，然后根据如下公式计算下一次匹配的位移数：
后移位数 = 坏字符的位置 - 搜索词中的上一次出现位置
如果"坏字符"不包含在搜索词之中，则上一次出现位置为 -1。
假定字符串为"HERE IS A SIMPLE EXAMPLE"，搜索词为"EXAMPLE"：

首先，"字符串"与"搜索词"头部对齐，从尾部开始比较。发现坏字符为‘S’，后移位数 = 坏字符S在搜索词中的当前匹配位置6（从0开始） - S在搜索词中的上一次出现的位置-1，为7.因此后移7位后继续匹配：


发现"P"与"E"不匹配，所以"P"是"坏字符"。但是，"P"包含在搜索词"EXAMPLE"之中。所以，后移位数 = 坏字符位置6 - 在搜索词中上一次出现的位置4，为2，因此后移2位再继续匹配：

"MPLE"与"MPLE"匹配。我们把这种情况称为"好后缀"（good suffix），即所有尾部匹配的字符串。注意，"MPLE"、"PLE"、"LE"、"E"都是好后缀。
发现"I"与"A"不匹配。所以，"I"是"坏字符"，但此时因为有好后缀的存在，所以不按之前的公式，而是按如下公式来计算位移：
　后移位数 = 好后缀的位置 - 搜索词中的上一次出现位置

这个规则有三个注意点：
（1）"好后缀"的位置以最后一个字符为准。假定"ABCDEF"的"EF"是好后缀，则它的位置以"F"为准，即5（从0开始计算）。
　　（2）如果"好后缀"在搜索词中只出现一次，则它的上一次出现位置为 -1。比如，"EF"在"ABCDEF"之中只出现一次，则它的上一次出现位置为-1（即未出现）。
　　（3）如果"好后缀"有多个，则除了最长的那个"好后缀"，其他"好后缀"的上一次出现位置必须在头部。比如，假定"BABCDAB"的"好后缀"是"DAB"、"AB"、"B"，请问这时"好后缀"的上一次出现位置是什么？回答是，此时采用的好后缀是"B"，它的上一次出现位置是头部，即第0位。这个规则也可以这样表达：如果最长的那个"好后缀"只出现一次，则可以把搜索词改写成如下形式进行位置计算"(DA)BABCDAB"，即虚拟加入最前面的"DA"。

回到上文的这个例子。此时，所有的"好后缀"（MPLE、PLE、LE、E）之中，只有"E"在"EXAMPLE"还出现在头部，所以后移 6 - 0 = 6位。

可以看到，"坏字符规则"只能移3位，"好后缀规则"可以移6位。所以，Boyer-Moore算法的基本思想是，每次后移这两个规则之中的较大值。更巧妙的是，这两个规则的移动位数，只与搜索词有关，与原字符串无关。因此，可以预先计算生成《坏字符规则表》和《好后缀规则表》。使用时，只要查表比较一下就可以了。

继续从尾部开始比较，"P"与"E"不匹配，因此"P"是"坏字符"。根据"坏字符规则"，后移 6 - 4 = 2位。

从尾部开始逐位比较，发现全部匹配，于是搜索结束。如果还要继续查找（即找出全部匹配），则根据"好后缀规则"，后移 6 - 0 = 6位，即头部的"E"移到尾部的"E"的位置。
```java
public class BoyerMoore {
    private final int R;     // the radix
    private int[] right;     // the bad-character skip array
    private String pat;      // or as a string

    // pattern provided as a string
    public BoyerMoore(String pat) {
        this.R = 256;
        this.pat = pat;

        // position of rightmost occurrence of c in the pattern
        right = new int[R];
        for (int c = 0; c < R; c++)
            right[c] = -1;
        for (int j = 0; j < pat.length(); j++)
            right[pat.charAt(j)] = j;
    }
	
    // return offset of first match; N if no match
    public int search(String txt) {
        int M = pat.length();
        int N = txt.length();
        int skip;
        for (int i = 0; i <= N - M; i += skip) {
            skip = 0;
            for (int j = M-1; j >= 0; j--) {
                if (pat.charAt(j) != txt.charAt(i+j)) {
                    skip = Math.max(1, j - right[txt.charAt(i+j)]);
                    break;
                }
            }
            if (skip == 0) return i;    // found
        }
        return N;                       // not found
    }

    public static void main(String[] args) {
        String pat = "WINSTON";
        String txt = "0123456789WINSTONdsiyghkadfadfafhdg";
    
        BoyerMoore bm = new BoyerMoore(pat);
        int offset = bm.search(txt);
		System.out.println(offset);
    }
}
```