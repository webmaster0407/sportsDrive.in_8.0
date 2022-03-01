<?php
$temp = Request::path() ;
$subpart = explode('/', $temp);

$SuperParentTitle="";
$ParentTitle ="";
if($ParentData != null){ $ParentTitle = $ParentData->page_title; }
if($SuperParentData != null){ $SuperParentTitle = $SuperParentData->page_title; }
?>

<li><a href="/administrator/home"><i class="fa fa-dashboard"></i> Dashboard</a></li>
<?php if(!isset($subpart[3])) {?>
<li class="active">CMS Pages</li>
<?php }else{ ?>
<li><a href="/administrator/list-pages">CMS Pages</a></li>
<?php }?>
<?php
if(isset($subpart[3])){
	if($subpart[3]== 2){?>
	<li><a href="/administrator/list-pages/<?php echo $linkPid;?>/<?php echo $linkLid;?>"> <?php echo $SuperParentTitle;?></a></li>
	<?php }?>
	<?php if($subpart[3]>=1){?>
	<li class="active"><?php echo $ParentTitle;?></li>
	<?php }
}
?>
