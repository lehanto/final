<?php
 
if ( $_FILES['filename']['error'] == UPLOAD_ERR_OK )
{
    // アップロード先とファイル名を付与
    $upload_file = "./upload/" . $_FILES["filename"]["name"] ;

    // アップロードしたファイルを指定のパスへ移動
    if ( move_uploaded_file( $_FILES["filename"]['tmp_name'], $upload_file ) )
    {
        // パーミッションを変更
        // Read and write for owner, read for everybody
        chmod($upload_file, 0644);
    }
}

// ディレクトリハンドルの取得
$dir_h = opendir( "./upload/" ) ;
// ファイル・ディレクトリの一覧を $file_list に
while (false !== ($file_list[] = readdir($dir_h))) ;
// ディレクトリハンドルを閉じる
closedir( $dir_h ) ;
 
//ディレクトリ内のファイル名を１つずつを取得
foreach ( $file_list as $file_name )
{
　//ファイルのみを表示
　if( is_file( "./upload/" . $file_name) )
　{
　　$p = pathinfo("./upload/" . $file_name);
　　if ( $p["extension"] == "jpg" )
　　{
　　　print $file_name  ;
　　}
　}
}
 
?>
