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

//$drafts = scan('../_drafts/Web服务器');
//$post = '../_posts/2017-04-05-面试题-后端开发.md';

$content = '';
foreach ($drafts as $key => $draftPath) {
  $draftContent = file_get_contents($draftPath);
  $content .= $draftContent."\n\n";
}

file_put_contents($post, $content);

