<?php
# For sharing files in /media directory, do not delete, you can modify
# 分享功能，用来分享/media文件夹下的文件。不可删除，可以按需修改。
ini_set('display_errors','Off');
error_reporting(0);
header('Content-Type: application/octet-stream');
$filepath=base64_decode($_SERVER['QUERY_STRING']);
if (strlen($filepath)<=0) exit();
$file=fopen($filepath,"rb");
if ($file==FALSE) exit();
ob_clean();
while(!feof($file))
{
    print(fread($file,1024*8));
    ob_flush();
    flush();
}