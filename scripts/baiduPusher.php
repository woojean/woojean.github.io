<?php
$urls = array(
    'http://www.woojean.com/index.html',
    'http://www.woojean.com/archive/index.html',
    'http://www.woojean.com/category/index.html',
    'http://www.woojean.com/tag/index.html',
    'http://www.woojean.com/aboutme/index.html',

);

$api = 'http://data.zz.baidu.com/urls?site=www.woojean.com&token=hfiWXkUWNFzBB2Ef';

$ch = curl_init();
$options =  array(
    CURLOPT_URL => $api,
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POSTFIELDS => implode("\n", $urls),
    CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
);
curl_setopt_array($ch, $options);
$result = curl_exec($ch);
echo $result;

