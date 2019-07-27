<?php
if (!isset($_SESSION['user'])||strlen($_SESSION['user'])<=0){
    ob_end_clean();
    header('Location: /hotload.php?page=login&err=1');
    die();
}
if ($_SESSION['role']!='admin'){
    $padding='Lorem ipsum dolor sit amet, consectetur adipisicing elit.';
    for($i=0;$i<10;$i++) $padding.=$padding;
    die('<div><div class="container" style="margin-top:30px"><h3 style="color:red;margin-bottom:15px;">只有管理员权限才能访问！</h3></div><p style="visibility: hidden">'.$padding.'</p></div>');
}

if (isset($_FILES["file_data"])){
    if ($_FILES["file_data"]["error"] > 0||$_FILES["file_data"]["size"] > 1024*1024*1){
        ob_end_clean();
        die(json_encode(array('status'=>0,'info'=>'上传出错，固件文件最大支持 1MB。')));
    }else{
        mt_srand(time());
        $firmware_filename=md5(mt_rand().$_SESSION['user']);
        $firmware_filename=__DIR__."/../uploads/firmware/".$firmware_filename.".elf";
        if (time()-$_SESSION['timestamp']<3){
            ob_end_clean();
            die(json_encode(array('status'=>0,'info'=>'操作太快了，请稍后再上传。')));
        }
        $_SESSION['timestamp']=time();
        move_uploaded_file($_FILES["file_data"]["tmp_name"], $firmware_filename);
        $handle = fopen($firmware_filename, "rb");
        if ($handle==FALSE){
            ob_end_clean();
            die(json_encode(array('status'=>0,'info'=>'上传失败，未知原因。')));
        }
        $flags = fread($handle, 4);
        fclose($handle);
        if ($flags!=="\x7fELF"){
            unlink($firmware_filename);
            ob_end_clean();
            die(json_encode(array('status'=>0,'info'=>'上传失败，不是有效的 ELF 文件。')));
        }
        ob_end_clean();
        die(json_encode(array('status'=>1,'info'=>'上传成功！')));
    }
}else{
    if (isset($_SERVER['CONTENT_TYPE'])){
        if (stripos($_SERVER['CONTENT_TYPE'],'form-data')!=FALSE){
            ob_end_clean();
            die(json_encode(array('status'=>0,'info'=>'上传出错，音乐文件最大支持 1MB。')));
        }
    }
}

@$path=$_POST['path'];

function clean_string($str){
    $str=str_replace("\\","",$str);
    $str=str_replace("/","",$str);
    $str=str_replace(".","",$str);
    $str=str_replace(";","",$str);
    return substr($str,0,32);
}

if (isset($path)){
    $path=clean_string(trim((string) $path));
    if (strlen($path)<=0||strlen($path)>64){
        ob_end_clean();
        die(json_encode(array('status'=>0,'info'=>'输入格式或长度不符合规定！')));
    }else{
        $firmware_filename=__DIR__."/../uploads/firmware/".$path.".elf";
        if (!file_exists($firmware_filename)){
            ob_end_clean();
            die(json_encode(array('status'=>0,'info'=>'固件文件不存在！')));
        }else{
            try{
                $elf = FFI::cdef("
                    extern char * version;
                ", $firmware_filename);
                $version=(string) FFI::string($elf->version);
                ob_end_clean();
                die(json_encode(array('status'=>1,'info'=>'固件版本号：'.$version)));
            }catch(Error $e){
                ob_end_clean();
                die(json_encode(array('status'=>0,'info'=>'加载固件文件时发生错误！')));
            }
        }
    }
}
?>
<script>nav_active();nav_user('<?php echo @$_SESSION['user']; ?>');</script>
    <link rel="stylesheet" href="css/fileinput.min.css">
    <link rel="stylesheet" href="css/fileinput-rtl.min.css">
    <script src="js/fileinput.min.js"></script>
    <script src="js/locales/zh.js"></script>
        <div>
<div class="container" style="margin-top:30px">
    <h3 style="margin-bottom:15px;">固件更新</h3>
    <input id="upload" type="file" class="file" data-preview-file-type="text" >
    <div style="height:30px"></div>
    <div id="info1" class="alert" role="alert" style='display: none;'></div>
    <script>
        $("#upload").fileinput({language: 'zh',uploadUrl: '/hotload.php?page=firmware'});
        $("#upload").on("fileuploaded", function (event, data, previewId, index) {
            var data = data.response;
            if (data.status==1){
                $('#info1').removeClass('alert-danger');
                $('#info1').addClass('alert-success');
            }else{
                $('#info1').removeClass('alert-success');
                $('#info1').addClass('alert-danger');
            }
            $('#info1').html(data.info);
            $('#info1').show();
        });
    </script>
    <hr/><h4 style="text-align:left;margin-bottom:15px;color:gray;">调试模式（读取固件版本号)</h4>
    <div class='form-container'>
        <div class='form-group'>
            <input type="text" class="form-control" placeholder="固件文件" id="path">
        </div>
        <div class="form-group">
            <button class="btn btn-info btn-block" onclick="submit()">读取</button>
        </div>
    <div id="info2" class="alert" role="alert" style="display: none;"></div>
    </div>
</div>
<script>
function submit(){
    $('#info2').hide();
    $.ajax({
        type: 'POST',
        url: "/hotload.php?page=firmware",
        data: {path: $('#path').val()},
        dataType: "json",
        success: function(data) {
            if (data.status==1){
                $('#info2').removeClass('alert-danger');
                $('#info2').addClass('alert-success');
            }else{
                $('#info2').removeClass('alert-success');
                $('#info2').addClass('alert-danger');
            }
            $('#info2').html(data.info);
            $('#info2').show();
        }
    });
}
</script>
            <p style="visibility: hidden">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Animi aspernatur beatae commodi dolorem in praesentium quia quis sit ullam. Aut facere nihil non soluta temporibus. Modi molestias suscipit voluptate. A?
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquid beatae consequatur deserunt earum eligendi ex, illum iure nostrum nulla obcaecati pariatur placeat quae reiciendis repellat similique tenetur totam vel voluptatum?
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aut autem consectetur cum ex expedita id incidunt inventore ipsa laudantium maiores nihil quia quo quod rem, reprehenderit repudiandae sunt unde voluptatibus?
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusantium ad adipisci animi, commodi cumque doloribus ducimus eaque eveniet illo iste, maxime, molestiae molestias neque nostrum odio officiis reiciendis rem voluptates?
            </p>
        </div>