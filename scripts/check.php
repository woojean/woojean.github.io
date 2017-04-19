<?php

function scan($dir){
    $result = [];
    $handle = opendir($dir);
    if ( $handle ){
        while ( ( $file = readdir ( $handle ) ) !== false ){
            if ( $file != '.' && $file != '..' && $file != '.DS_Store'){
                $absPath = $dir . DIRECTORY_SEPARATOR . $file;
                if ( is_dir ( $absPath ) ){
                    $result = array_merge($result,scan($absPath));
                }
                else{
                    $result[] = $absPath;
                }
            }
        }
        closedir($handle);
    }
    
    return $result;
}

$host = 'http://blog.woojean.com';
$urls = [];
$posts = scan('../_posts');


foreach ($posts as $key => $postPath) {
    $postPath = str_replace('.md', '', $postPath);
    $postPath = str_replace('【', '', $postPath);
    $postPath = str_replace('】', '-', $postPath);
    $postPath = str_replace('《', '', $postPath);
    $postPath = str_replace('——', '', $postPath);
    $postPath = str_replace('》', '', $postPath);
    $postPath = str_replace('（', '', $postPath);
    $postPath = str_replace('）', '', $postPath);
    $postPath = str_replace('，', '-', $postPath);
    $postPath = str_replace('、', '-', $postPath);
    //$postPath = str_replace('-', '/', $postPath);
    $postPath = str_replace('../_posts', '', $postPath);
    $date = substr($postPath, 0,12);
    $postPath = str_replace($date, '', $postPath);
    $date = str_replace('-', '/', $date);
    $postPath = $date.$postPath;


    $postPath .= '/index.html';
    $postPath = $host . $postPath;

    $urls[] = $postPath;
}



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


foreach ($urls as $key => $value) {
    # code...
}

 