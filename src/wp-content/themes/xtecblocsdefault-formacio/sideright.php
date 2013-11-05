<!-- Els meus blocs -->
<?php include_once('xtecfunc.php');?>

<?php
global $current_user;
if (is_user_logged_in()){
	$blogs = get_blogs_of_user($current_user->ID);?>

<div class="sidebox"><span class="sideboxright">&nbsp;</span> <span
	class="sideboxleft">&nbsp;</span>
<h3 class="noticies">Els meus blocs</h3>
<div class="sidecontent">
<ul>
<?php if (!empty($blogs) ) {
	foreach ( $blogs as $blog ){

		$value='wp_'.$blog->userblog_id.'_user_level';
		$level = $current_user->$value;
		switch ($level){
			case '':
				$image = 'subscrip';
				$text = 'Entra';
				break;
			case 10:
				$image = 'admin';
				$text = 'Administra';
				break;
			default:
				$image = 'edit';
				$text = 'Escriu';
		}

		$number = xtec_descriptors_count_bloc_descriptors($blog->userblog_id);
		
		//XTEC ************ AFEGIT 
		//2013.10.30 @jmeler 
		// if blog's titles is empty, compose title from url
		// ex: http://agora/blocs/elspinguins/ --> elspinguins
		
		if (empty($blog->blogname))	
			$blog->blogname="Bloc ".substr(trim($blog->path,"/"),strpos(trim($blog->path,"/"),"/")+1);

		//FI ************
		
		if($number>0 || $level!=10){
			?>
	<li><a href='http://<?php echo $blog->domain . $blog->path;?>'
		target="_blank" title="Entra al bloc"><?php echo stripslashes($blog->blogname);?></a><a
		href='http://<?php echo $blog->domain . $blog->path;?>wp-admin/'
		target="_blank" title="<?php echo $text;?>"><img
		src="<?php bloginfo('template_directory'); ?>/images/<?php echo $image;?>.gif"
		border="0" alt="<?php echo $text;?>" class="myicon" /> </a> <?php if ($image == 'admin' && $blog->userblog_id != 1) { ?>
	<a
		href='http://<?php echo $blog->domain . $blog->path;?>wp-admin/ms-delete-site.php'
		target="_blank" title="Elimina el bloc"> <img
		src="<?php bloginfo('template_directory'); ?>/images/delete.gif" border="0"
		alt="Elimina el Bloc" class="myicon" /> </a> <?php } ?></li>

		<?php } else {
			$notHave=true; ?>
	<li><a style="color: red;"
		href='http://<?php echo $blog->domain . $blog->path;?>'
		target="_blank" title="Entra al bloc"><?php echo stripslashes($blog->blogname);?>
	</a> <a
		href='http://<?php echo $blog->domain . $blog->path;?>wp-admin/'
		target="_blank" title="<?php echo $text;?>"> <img
		src="<?php bloginfo('template_directory'); ?>/images/<?php echo $image;?>.gif"
		border="0" alt="<?php echo $text;?>" class="myicon" /> </a> <?php if ($image == 'admin' && $blog->userblog_id != 1) { ?>
	<a
		href='http://<?php echo $blog->domain . $blog->path;?>wp-admin/ms-delete-site.php'
		target="_blank" title="Elimina el bloc"> <img
		src="<?php bloginfo('template_directory'); ?>/images/delete.gif" border="0"
		alt="Elimina el Bloc" class="myicon" /> </a> <?php } ?></li>
		<?php }
	}
}?>
</ul>

</div>
</div>
<?php if($notHave){ ?>
<p class="warningbox">En els blocs escrits en vermell <strong>no has
definit els descriptors</strong>. Defineix els descriptors d'aquests
blocs per fer desaparèixer l'avís.</p>
<?php } ?>

<!-- Els meus preferits -->
<?php $blogs = xtec_favorites_get_user_preferred_blogs();?>
<?php if(count($blogs)>0){?>

<div class="sidebox"><span class="sideboxright">&nbsp;</span> <span
	class="sideboxleft">&nbsp;</span>
<h3 class="noticies">El meus preferits</h3>
<div class="sidecontent">
<ul>

	
<?php if (!empty($blogs) ) {

	foreach ( $blogs as $blog ){

		//XTEC ************ AFEGIT 
		//2013.10.30 @jmeler 
		// if blog's titles is empty, compose title from url
		// ex: http://agora/blocs/elspinguins/ --> elspinguins

		$titolBlog=get_blog_option($blog, 'blogname');

		if ( empty( $titolBlog ) ){
			$urlBlog=get_blog_option($blog, 'siteurl');
			$titolBlog = "Bloc ".substr($urlBlog, strrpos( trim($urlBlog,"/") ,'/')+1); 
		}else		
			$titolBlog = stripslashes(get_blog_option($blog, 'blogname'));

		//FI ************ 

?>
	<li><a href='<?php echo get_blogaddress_by_id($blog);?>'
	target="_blank" title="Entra al bloc"><?php echo $titolBlog;?></a>&nbsp;<a
	href="index.php?a=delPrefer&blogId=<?php echo $blog?>"
	title="Esborra"><img src="<?php bloginfo('template_directory'); ?>/images/delete.gif"
	border="0" alt="Esborra" /></a></li>

<?php } 
}?>
</ul>
</div>
</div>
<?php } ?>
<?php } ?>

<!-- Els blocs més actius -->

<div class="sidebox"><span class="sideboxright">&nbsp;</span> <span
	class="sideboxleft">&nbsp;</span>
<h3 class="noticies">Els blocs més actius</h3>
<div class="sidecontent">
<ul>
<?php
$mostActive=xtec_lastest_posts_most_active_blogs();
if(count($mostActive)>0){

	foreach ( $mostActive as $active ){
	//XTEC ************ AFEGIT - if blog's title is empty, get url
	//2013.10.30 @jmeler
		if (empty($active['blog_title']))	
			$titolBlog=substr($active['blog_url'],strrpos($active['blog_url'],'/')+1); 
		else		
			$titolBlog=stripslashes($active['blog_title']);
	//************ FI	
	?>
	
	<li><a href='<?php echo $active['blog_url'];?>' target="_blank"
		title="Entra al bloc"><?php echo $titolBlog;?></a><?php if(is_user_logged_in()){?>
	<a href="index.php?a=addPrefer&blogId=<?php echo $active['blogId']?>"
		title="Preferit"><img src="<?php bloginfo('template_directory'); ?>/images/myblogs.gif"
		border="0" alt="Preferit" /></a><?php };?></li>
		<?php
	}
}?>
</ul>


<ul id="cloudtags">
	<li id="mes"><a
		href="<?php echo get_option('home');?>/index.php?a=mostActive">Més...</a></li>
</ul>
</div>
</div>

<!-- Els darrers blocs creats -->

<div class="sidebox"><span class="sideboxright">&nbsp;</span> <span
    class="sideboxleft">&nbsp;</span>
<h3 class="noticies">Els darrers blocs creats</h3>
<div class="sidecontent"><?php
$blogs = xtec_api_lastest_blogs(5,3000,'registered');
if( is_array( $blogs ) ) {?>
<ul>
<?php foreach( $blogs as $blog ) {

	//XTEC ************ AFEGIT 
	//2013.10.30 @jmeler 
	// if blog's titles is empty, compose title from url
	// ex: http://agora/blocs/elspinguins/ --> elspinguins

	// TODO: error: xtec_api_lastest_blogs returns empty items
	if ( !empty($blog['blog_url']) ){	
		if ( empty($blog['blog_title']) )	
			$titolBlog = "Bloc ".substr($blog['blog_url'], strrpos( trim($blog['blog_url'],"/") ,'/')+1,-1); 
		else		
			$titolBlog = stripslashes($blog['blog_title']);
	//************ FI	

?>
    <li><a href="<?php echo $blog['blog_url']; ?>" target="_blank"><?php echo $titolBlog; ?></a><?php if(is_user_logged_in()){?>
    <a href="index.php?a=addPrefer&blogId=<?php echo $blog['blog_id']?>"
        title="Preferit"><img src="<?php bloginfo('template_directory'); ?>/images/myblogs.gif"
        border="0" alt="Preferit" /></a><?php };?></li>
        
<?php 
}
}?>
</ul>
<ul id="cloudtags">
    <li id="mes"><a
        href="<?php echo get_option('home');?>/index.php?a=lastCreated">Més...</a></li>
</ul>
        <?php }?></div>
</div>



