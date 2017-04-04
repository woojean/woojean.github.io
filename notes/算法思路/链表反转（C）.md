# 链表反转（C）

```c++
struct Item{
    char c;
    Item *next;
};

Item *Reverse( Item *x ){
    Item *prev = NULL,*curr = x;
    while ( curr ){
        Item *next = curr->next;
        curr->next = prev;
        prev = curr;
        curr = next;
    }
    return prev;
}

int main(){
    Item *x,
        d = {"d", Null},
        c = {"c", &d},
        b = {"b", &c},
        a = {"a", &b};
    x = Reverse( &a );
}
```