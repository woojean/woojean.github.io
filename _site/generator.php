<?php

 function scan($dir){
    $result = [];
    $handle = opendir($dir);
    if ( $handle ){
        while ( ( $file = readdir ( $handle ) ) !== false ){
            if ( $file != '.' && $file != '..' && $file != '.DS_Store'){
                $absPath = $dir . DIRECTORY_SEPARATOR . $file;
                if ( is_dir ( $absPath ) ){
                    $result['dirs'][$absPath] = scan( $absPath );
                }
                else{
                    $result['files'][] = [
                        'name' => explode('.', $file)[0],
                        'path' => $absPath,
                        'size' => filesize($absPath)

                    ];
                }
            }
        }
        closedir($handle);
    }
    return $result;
}

$ret = scan('notes');

file_put_contents('notes.json', json_encode($ret));