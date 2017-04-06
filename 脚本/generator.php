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
                        'name' => str_replace('.md', '', $file),
                        'path' => $absPath,
                        'size' => filesize($absPath)

                    ];

                    // record errorfile
                    if( strpos($file, ' ') 
                        || strpos($file, '+')
                        || strpos($file, '<')
                        || strpos($file, '>')
                        || strpos($file, '#')){

                    }
                }
            }
        }
        closedir($handle);
    }
    
    return $result;
}

//$data = scan('_drafts');
$data = scan('_drafts');


$html = '<html>
<head>
<title></title>
</head>

<body>
$BODY$
</body>
</html>';

$body = '<ul>';
foreach ($data['dirs'] as $dir => $value) {
    foreach ($value['files'] as $note) {
        $body .= '<li><a href="'.urlencode($note['path']).'">'.$note['name'].'</a></li>';
    }
}
$body .= '</ul>';
$html = str_replace('$BODY$', $body, $html);

file_put_contents('index.html', $html);

file_put_contents('_drafts.json', json_encode($data));





