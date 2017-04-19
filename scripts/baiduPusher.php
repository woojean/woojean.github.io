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



// $urls = array(
//     'http://www.woojean.com/index.html',
//     'http://www.woojean.com/archive/index.html',
//     'http://www.woojean.com/category/index.html',
//     'http://www.woojean.com/tag/index.html',
//     'http://www.woojean.com/aboutme/index.html',

// );

$api = 'http://data.zz.baidu.com/urls?site=blog.woojean.com&token=hfiWXkUWNFzBB2Ef';

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

$dataUrls = '';
foreach ($urls as $key => $value) {
	$dataUrls .= $value."/n";
}

file_put_contents('data_urls', json_encode($dataUrls));






blog.woojean.com/index.html
blog.woojean.com/archive/index.html
blog.woojean.com/category/index.html
blog.woojean.com/tag/index.html
blog.woojean.com/aboutme/index.html












