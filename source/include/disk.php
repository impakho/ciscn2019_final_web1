<?php
if (!isset($_SESSION['user'])||strlen($_SESSION['user'])<=0){
    ob_end_clean();
    header('Location: /hotload.php?page=login&err=1');
    die();
}

include 'NoSQLite/NoSQLite.php';
include 'NoSQLite/Store.php';

$nsql=new NoSQLite\NoSQLite($_GLOBALS['dbfile']);
$music=$nsql->getStore('music');
$res=$music->get($_SESSION['user']);

if ($res===null||strlen((string)$res)<=0){
    $res=array();
}else{
    $res=array_slice(array_reverse(json_decode($res,TRUE)),0,15);
}
?>
<script>nav_active('disk');nav_user('<?php echo @$_SESSION['user']; ?>');</script>
<style>
table{
    table-layout: fixed;
}
th, td{
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    text-align: center;
}
</style>
        <div>
<div class="container" style="margin-top:30px">
    <h3 style="margin-bottom:15px;">我的音乐云盘</h3><span>只显示最新的 15 首歌曲</span>
    <table class="table table-hover">
        <tr>
            <th>音乐名</th>
            <th>歌手</th>
            <th>专辑</th>
        </tr>
<?php foreach ($res as $song){ ?>        <tr>
            <td><?php echo base64_decode($song[0]); ?></td>
            <td><?php echo base64_decode($song[1]); ?></td>
            <td><?php echo base64_decode($song[2]); ?></td>
        </tr><?php } ?>
    </table>
<?php if (count($res)<=0) echo '<span>你的音乐云盘里还没有歌曲，赶紧去 <a href="#upload" onclick="loadHash(\'upload\');">添加歌曲</a> 吧！</span>'; ?>
</div>
            <p style="visibility: hidden">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Animi aspernatur beatae commodi dolorem in praesentium quia quis sit ullam. Aut facere nihil non soluta temporibus. Modi molestias suscipit voluptate. A?
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquid beatae consequatur deserunt earum eligendi ex, illum iure nostrum nulla obcaecati pariatur placeat quae reiciendis repellat similique tenetur totam vel voluptatum?
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aut autem consectetur cum ex expedita id incidunt inventore ipsa laudantium maiores nihil quia quo quod rem, reprehenderit repudiandae sunt unde voluptatibus?
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusantium ad adipisci animi, commodi cumque doloribus ducimus eaque eveniet illo iste, maxime, molestiae molestias neque nostrum odio officiis reiciendis rem voluptates?
            </p>
        </div>