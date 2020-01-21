<?php
include('xtecfunc.php');

if(isset($_REQUEST['id'])){
	$id = $_REQUEST['id'];
	$post = get_post($id,'');

	if(isset($_REQUEST['msg']) && $_REQUEST['msg']=='newComment'){?>
		<p class="thanks">Gràcies per enviar un comentari nou. No estar&agrave; disponible fins que no sigui validat per un administrador/a del portal.</p>
	<?php
	} 

	// notícies	?>
	<br />
	<div class="box">
		<span class="contentboxheadright"></span>
		<span class="contentboxheadleft"></span>
		<h2 class="contentboxheadfons">Notícies</h2>
		<div class="article">
			<h3><?php echo $post->post_title;?></h3>
			<p class="data">Publicat <?php echo dateText(strtotime($post->post_date));?></p>
			<p><?php echo nl2br($post->post_content); ?></p>
			<?php if($post->comment_count>0){?>
				<p class="comentari">Aquesta notícia té <a href="index.php?id=<?php echo $id;?>"><?php echo nl2br($post->comment_count);?> Comentari/s</a></p>
			<?php }else{ ?>	
				<p class="comentari">Aquesta notícia <a href="index.php?id=<?php echo $id;?>">no té comentaris</a></p>
			<?php } ?>
		</div> <!--end of article -->	
	</div>
	<?php
	// comments	
	include_once(get_template_directory().'/comments.php');
}

else {

	if (function_exists('xtecweekblog_current_weekblog')){
		global $post;
		$weekblog = xtecweekblog_current_weekblog();
		if(is_array($weekblog) && (count($weekblog)>0) && xtecweekblog_validate($weekblog->ID)) {
	        $wb_name = get_post_meta($weekblog->ID, '_xtecweekblog-name', true);
	        $wb_url = get_blogaddress_by_name($wb_name);
	        $wb_id = get_id_from_blogname($wb_name);
	        $wb_blog_title = get_bloginfo('title');
	        $wb_description = get_post_meta($weekblog->ID, '_xtecweekblog-description', true);
	        ?>	
				<div id="weekblog-box" class="box">
					<span class="contentboxheadright"></span>
					<span class="contentboxheadleft"></span>
					<h2 class="contentboxheadfons">Bloc destacat</h2>
					<div id="bloc_destacat">
						<a href="<?php echo $wb_url;?>" target="_blank" title="<?php $wb_blog_title;?>"><?php echo get_the_post_thumbnail($weekblog->ID, 'xtecweekblog', array('alt' => 'Accedeix al bloc'));?></a>
						<p><?php echo $wb_description?></p>
						<ul>
							<li><a href="<?php echo $wb_url;?>" target="_blank">Accedeix al bloc</a></li>		
						</ul>
						<div class="clear"></div>
					</div>
				</div>
				<?php
		}
		else {
			?>
			<div id="weekblog-box" class="box">
				<span class="contentboxheadright"></span>
				<span class="contentboxheadleft"></span>
				<h2 class="contentboxheadfons"><?php _e('WeekBlog', 'xtecweekblog');?></h2>
				<div id="bloc_destacat">
					<img src="<?php bloginfo('template_directory'); ?>/images/weekblog/banner.jpg" alt="XTECBlocs" title="XTECBlocs" />
					<p><?php echo get_option('xtecweekblog_default_msg')?></p>
					<div class="clear"></div>
				</div>
			</div>
		<?php
		}
		?> <br /> <?php
	}
	if(isset($_REQUEST['msg']) && $_REQUEST['msg']=='newComment'){?>
		<p class="thanks">Gràcies per enviar un comentari nou. No estar&agrave; disponible fins que no sigui validat per un administrador/a del portal.</p>
	<?php
	}
	?>

		<!-- Inici de notícies -->
		
		<div id="news-box" class="box">
			<span class="contentboxheadright"></span>
			<span class="contentboxheadleft"></span>
			<h2 class="contentboxheadfons">Notícies</h2>
			<?php query_posts('showposts=2');
			while (have_posts()) : the_post();?>
				<div class="article">
					<h3><?php echo the_title();?></h3>
					<p class="data">Publicat <?php echo dateText(strtotime($post->post_date));?></p>
					<p><?php echo nl2br(the_content()); ?></p>
					<div class="clear"></div>
				</div> <!--end of article -->	
			<?php endwhile; ?>
			<div class="article">
			<p class="comentari"><a href="<?php echo get_option('home');?>/index.php?a=newsList">Més...</a></p>
			</div> 
		</div>
	<br />
	
<div id="latestposts-box" class="box">
	<span class="contentboxheadright"></span>
	<span class="contentboxheadleft"></span>
	<h2 class="contentboxheadfons">Darrers articles als blocs</h2>
	<div class="darreres">
	<?php
	$blogs = xtec_lastest_posts_lastest_posts(10,5,0);
	if( is_array( $blogs ) ) {
		foreach( $blogs as $blog ) {
			$desc = trim($blog['post_content']);
			// save the post date to a var
			$date = dateText(strtotime($blog['post_date']));
			// strip out html characters to allow for truncating
			$strippedDesc = strip_tags($desc);	
			// truncate post content to 120 words
			$numwords = 40;
			preg_match("/([\S]+\s*){0,$numwords}/", $strippedDesc, $regs);
			$shortDesc = trim($regs[0]);
			//$shortDesc = get_the_content($more_link_text, $stripteaser, $more_file);
			$shortDesc = apply_filters('the_content', $shortDesc);
			$shortDesc = str_replace(']]>', ']]&gt;', $shortDesc);
			//Show the content
			echo "<h3><a href=\"".$blog['blog_url']."\" style=\"color:#408DD4;\" >".stripslashes($blog['blog_title'])."</a>";
			//si el user se ha autentificado, mostrará el icono de favoritos
			//pasar a css si es posible! 
			if(is_user_logged_in()){
				echo "&nbsp;&nbsp;<a href='index.php?a=addPrefer&blogId=".$blog['blog_id']."' title='Preferit'><img src='";
				echo  bloginfo('template_directory');
				echo "/images/myblogs.gif' border='0' alt='Preferit'/></a>";
			} 	
			echo "</h3>"; 
			//dibuixem la caixa del darrer article	
			echo "<div class=\"darrerArticle\">";
			echo "<h4><a href=\"".$blog['guid']."\" style=\"color:#91beec;\">".$blog['post_title']."</a></h4>";
			//echo "<p>".nl2br($shortDesc);
			//if(strlen($desc)>strlen($shortDesc)){echo "<span class=\"allContentLink\">... <a href=\"".$blog['guid']."\" target=\"_blank\">[ Article complet ]</a></span>";}
			echo "<p class=\"data\">Publicat ".$date." per ".$blog['user_login']."</p>";
			echo "</div>";
			//end of caixa de darrer article					
		} //end of foreach
	} // end of if is_array  
	?>
	</div>
</div>
<?php
} //end of !isset($_REQUEST['id'] 
?>
			
