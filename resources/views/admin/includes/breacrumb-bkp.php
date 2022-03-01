<?php $temp = Request::path() ;
$subpart = explode('/', $temp);
?> 
<li><a href="/administrator/home"><i class="fa fa-dashboard"></i> Dashboard</a></li>
<?php if($ParentData != null){?>
<li><a href="/administrator/list-pages/<?php echo $linkPid;?>/<?php echo $linkLid;?>">Back to <?php echo $ParentData->page_title;?></a></li>
<?php } ?>
<li class="active">List CMS Pages</li>