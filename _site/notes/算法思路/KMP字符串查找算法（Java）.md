# KMP字符串查找算法（Java）

此算法通过运用对这个词在不匹配时本身就包含足够的信息来确定下一个匹配将在哪里开始的发现，从而避免重新检查先前匹配的字符。
具体而言就是针对搜索词，算出一张《部分匹配表》（Partial Match Table），当发生不匹配时，通过查表并结合如下公式来计算出需要后移的下一个匹配位置：
移动位数 = 已匹配的字符数 - 对应的部分匹配值

部分匹配表的生成算法：
首先，要了解两个概念："前缀"和"后缀"。 "前缀"指除了最后一个字符以外，一个字符串的全部头部组合；"后缀"指除了第一个字符以外，一个字符串的全部尾部组合。
"部分匹配值"就是"前缀"和"后缀"的最长的共有元素的长度。以"ABCDABD"为例：
－　"A"的前缀和后缀都为空集，共有元素的长度为0；
　　－　"AB"的前缀为[A]，后缀为[B]，共有元素的长度为0；
　　－　"ABC"的前缀为[A, AB]，后缀为[BC, C]，共有元素的长度0；
　　－　"ABCD"的前缀为[A, AB, ABC]，后缀为[BCD, CD, D]，共有元素的长度为0；
　　－　"ABCDA"的前缀为[A, AB, ABC, ABCD]，后缀为[BCDA, CDA, DA, A]，共有元素为"A"，长度为1；
　　－　"ABCDAB"的前缀为[A, AB, ABC, ABCD, ABCDA]，后缀为[BCDAB, CDAB, DAB, AB, B]，共有元素为"AB"，长度为2；
　　－　"ABCDABD"的前缀为[A, AB, ABC, ABCD, ABCDA, ABCDAB]，后缀为[BCDABD, CDABD, DABD, ABD, BD, D]，共有元素的长度为0。
最终的部分匹配表内容：

KMP匹配过程举例：
发生不匹配：	

已知空格与D不匹配时，前面六个字符"ABCDAB"是匹配的。查表可知，最后一个匹配字符B对应的"部分匹配值"为2，因此移动的位数 = 已匹配的字符数6 - 部分匹配值2 ，为4，即将匹配开始位置后移4位后继续匹配：

因为空格与Ｃ不匹配，搜索词还要继续往后移。这时，已匹配的字符数为2（"AB"），对应的"部分匹配值"为0。所以，移动位数 = 2 - 0，结果为 2，于是将搜索词向后移2位继续匹配：

因为空格与A不匹配，继续后移一位：

逐位比较，直到发现C与D不匹配。于是，移动位数 = 6 - 2，继续将搜索词向后移动4位，继续匹配：

逐位比较，直到搜索词的最后一位，发现完全匹配，于是搜索完成。如果还要继续搜索（即找出全部匹配），移动位数 = 7 - 0，再将搜索词向后移动7位（7为已匹配的字符数，这里就是匹配字符串的总长度）。

```java
public class KMP {
    private final int R;    // the radix
    private int[][] dfa;    // the KMP automoton
    private String pat;    // or the pattern string

    // create the DFA from a String
    public KMP(String pat) {
        this.R = 256;
        this.pat = pat;

        // build DFA from pattern
        int M = pat.length();
        dfa = new int[R][M]; 
        dfa[pat.charAt(0)][0] = 1; 
        for (int X = 0, j = 1; j < M; j++) {
            for (int c = 0; c < R; c++) 
                dfa[c][j] = dfa[c][X];   // Copy mismatch cases. 
            dfa[pat.charAt(j)][j] = j+1;   // Set match case. 
            X = dfa[pat.charAt(j)][X];   // Update restart state. 
        } 
    } 

    // return offset of first match; N if no match
    public int search(String txt) {
        // simulate operation of DFA on text
        int M = pat.length();
        int N = txt.length();
        int i, j;
        for (i = 0, j = 0; i < N && j < M; i++) {
            j = dfa[txt.charAt(i)][j];
        }
        if (j == M) return i - M;    // found
        return N;                // not found
    }

    public static void main(String[] args) {
        String pat = "WINSTON";
        String txt = "0123456789WINSTONdsiyghkadfadfafhdg";
    
        KMP kmp = new KMP(pat);
        int offset = kmp.search(txt);
		System.out.println(offset);
    }
}
```