<?php

/*
访问所有文件，查看jekyll的后端报错
*/

function doGet($url){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($curl);
    $httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE); 
    curl_close($curl); 
    return $httpCode;
}


$jsonData = file_get_contents('_drafts.json');
$data = json_decode($jsonData,true);
$errorFiles = [];

foreach ($data['dirs'] as $dir => $value) {
    foreach ($value['files'] as $note) {
        $url = 'http://127.0.0.1:4000/'.urlencode($note['path']);
        $ret = doGet($url);
        var_dump($ret);

        if( false == $ret || 400==$ret ){
            $errorFiles[] = $note['path'];
        }
    }
}

file_put_contents('errorFiles.txt', json_encode($errorFiles));



// $code = doGet('http://127.0.0.1:4000/'.urlencode('_drafts/数据库/MySQL复制的工作过程.md'));
// var_dump($code);



