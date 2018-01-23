<?php
header ('Content-type: text/html; charset=utf-8'); 
$limit = 5;
 
$group_id = '143498529659602';
$url1 = 'https://graph.facebook.com/'.$group_id;
$des = json_decode(file_get_contents($url1));
 
 
echo '<pre>';
print_r($des);
echo '</pre>';
 
 
$url2 = "https://graph.facebook.com/{$group_id}/feed";
$data = json_decode(file_get_contents($url2));
?>
<style type="text/css">
 .wrapper {
 width:300px;
 border:1px solid #ccc;
 font-family: "lucida grande",tahoma,verdana,arial,sans-serif;
 float:left;
 }
 
 .top {
 margin:5px;
 border-bottom:2px solid #e1e1e1;
 float: left;
 width:290px;
 }
 
 .single {
 margin:5px;
 border-bottom:1px dashed #e1e1e1;
 float:left;
 }
 
 .img {
 float:left;
 width:60px;
 text-align:center;
 margin:5px 5px 5px 0px;
 border-right:1px dashed #e1e1e1;
 }
 
 .text {
 width:220px;
 float:left;
 font-size:10px;
 }
 
 a {
 text-decoration: none;
 color: #3b5998;
 }
</style>
 
 
<div>
 <div>
 <a href='http://www.facebook.com/home.php?sk=group_<?=$group_id?>&ap=1'>
<?=$des->name?></a>
 <div style="width:100%; margin: 5px">
 <?=$des->description?>
 </div>
 </div>
 <?
 $counter = 0;
  
 foreach($data->data as $d) {
 if($counter==$limit)
 break;
 ?>
 <div>
 <div>
 <a href="http://facebook.com/profile.php?id=<?=$d->from->id?>">
    <img border="0" alt="<?=$d->from->name?>" src="https://graph.facebook.com/<?=$d->from->id?>/picture"/>
 </a>
 </div>
 <div>
 <span style="font-weight:bold"><a href="http://facebook.com/profile.php?id=<?=$d->from->id?>">
<?=$d->from->name?></a></span><br/>
 <span style="color: #999999;">on <?=date('F j, Y H:i',strtotime($d->created_time))?></span>
 <br/>
 <?=$d->message?>
 </div>
 </div>
 <?
 $counter++;
 }
 ?>
</div>