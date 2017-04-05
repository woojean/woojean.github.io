# 使用 Smarty、APC、XCache等非分布式缓存工具实现PHP动态内容缓存的基本形式

## Smarty缓存
```
require '../libsmarty/Smarty.class.php';
$this->smarty = new Smarty();
$this->smarty->caching = true;

$this->template_page = 'place_posts.html';
$this->cache_id = $this->marker_id;

if( $this->smarty->is_cached($this->template_page, $this->cache_id) ){
   $this->smarty->display( $this->template_page, $this->cache_id ); 
   exit(0);
}
do_some_db_query();
...
$this->smarty->display( $this->template_page, $this->cache_id ); 
```

缓存保存、查找、过期检查等，主要是Smarty的API，略。

## APC
Smarty的存储基于磁盘，而APC基于内存。

```
$this->key = $this->template_page . $this->cache_id;
$html = apc_fetch( $this->key );
if( $html !== false ){
    echo $html;
    exit(0);
}
do_some_db_query();
$html = $this->smarty->fetch($this->template_page , $this->cache_id);
apc_add($this->key, $html, $this->smarty->cache_lifetime);
echo $html;
```

## XCache
和APC类似，略。



## 局部无缓存
基本实现方式都是自定义一组标签，然后在模板中将局部无缓存的内容用标签包含，比如Smarty：
```
function smarty_block_dynamic( $params, $content, &$smarty){
    return $content;
}
$this->smarty->register_block('dynamic', 'smarty_block_dynamic', false);

{dynamic}
$user->user_nick;
{dynamic}
```