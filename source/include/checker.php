<?php
/******************************
IGNORE THIS FILE 请忽略该文件，该文件不在题目考察范围内
For checker, DO NOT modify or delete this file, otherwise maybe checking won't pass
checker机需要此文件，请不要修改或删除，否则可能不能通过check
******************************/

include 'init.php';

$checker_public=file_get_contents('checker.public');

$admin_password=write_config(init_config('.passwd'));

openssl_public_encrypt(str_pad(time(),16,'0',STR_PAD_LEFT).$admin_password, $encrypted, $checker_public);

ob_end_clean();

die(base64_encode($encrypted));
?>