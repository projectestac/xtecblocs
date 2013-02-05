<?php get_header();?>
<?php if($_REQUEST['a']=='help'){?>	
	<div id="content" style="width:95%; ">
		<?php include("evalcontent.php");?> 
	</div> <!-- end of content -->
<?php }else{?>
	<div id="content">
		<?php include("evalcontent.php");?> 
	</div> <!-- end of content -->
<?php }
if($_REQUEST['a']!='help'){	
	get_sidebar();
} ?>				

<?php get_footer(); ?>