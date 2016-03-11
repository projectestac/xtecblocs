<?php 

function dateText($timestamp){
	$monthName=array('de gener','de febrer','de mar&ccedil;','d\'abril','de maig','de juny','de juliol','d\'agost','de setembre','d\'octubre','de novembre','de desembre');
	$dateText = 'el dia ' . date('d',$timestamp).' '.$monthName[floor(date('m',$timestamp))-1].' de '.date('Y',$timestamp);
	
	$today = strtotime(date('M j, Y'));
	
	$reldays = ($timestamp - $today)/86400;
	
	if ($reldays >= 0 && $reldays < 1) {
		return 'avui';
	} else if ($reldays >= -1 && $reldays < 0) {
		return 'ahir';
	}
	
	return $dateText;
}

function getBlogsNumber(){
	global $wpdb;
	$counter = 0;
	// get a list of blogs in order of most recent update
	$blogs = $wpdb->get_col("SELECT count(*) as number FROM $wpdb->blogs WHERE `deleted` = '0'");
	$blogsPrivate = $wpdb->get_col("SELECT count(*) as number FROM $wpdb->blogs WHERE `public`='0' AND `deleted` = '0'");
	
	$number = array('blogs'=>$blogs[0],'blogsPrivate'=>$blogsPrivate[0]);
	return $number;
}


function getWeekBlog(){
	global $wpdb;
	$counter = 0;
	$posts=array();
	$blog = $wpdb->get_col("SELECT blogName FROM wp_weekblog WHERE endPublish >= CURRENT_DATE() AND initPublish <= CURRENT_DATE() AND active=1 ORDER BY endPublish limit 0,1");
	$blogd = $wpdb->get_col("SELECT description FROM wp_weekblog WHERE endPublish >= CURRENT_DATE() AND initPublish <= CURRENT_DATE() AND active=1 ORDER BY endPublish limit 0,1");
	if($blog[0]==''){return $posts;}

	if(!file_exists(bloginfo('template_directory') . '/images/weekblog/'.$blog[0].'.png')){
		return $posts;
	}

	$blogId = $wpdb->get_results("SELECT blog_id,domain,path from $wpdb->blogs WHERE path like '". PATH_CURRENT_SITE ."$blog[0]/'");

	$blogOptionsTable = "wp_".$blogId[0]->blog_id."_options";
	$blogPostsTable = "wp_".$blogId[0]->blog_id."_posts";
	$options = $wpdb->get_results("SELECT option_value FROM $blogOptionsTable WHERE option_name IN ('siteurl','blogname') ORDER BY option_id, option_name DESC");	
	$thispost = $wpdb->get_results("SELECT post_title, guid, post_content, post_date, post_author " .
	                               "FROM $blogPostsTable " .
	                               "WHERE post_status = 'publish' " .
	                               "AND post_title<>'' " .
	                               "AND post_type = 'post' " .
	                               "ORDER BY $blogPostsTable.id DESC limit 0,3");
	
	$thisusername = get_userdata($thispost[0]->post_author)->user_login;
	
	if($thispost[1]->post_title == ''){return $posts;}
	$posts=array('title0'=>$thispost[0]->post_title,
			'title1'=>$thispost[1]->post_title,
			'guid0'=>$thispost[0]->guid,
			'guid1'=>$thispost[1]->guid,
			'blogTitle'=>$options[1]->option_value,
			'blogUrl'=>$options[0]->option_value,
			'imgName'=>$blog[0],
			'description'=>$blogd[0]);
	return $posts;
}

function getNewsList(){
	global $wpdb;	
	$sql="SELECT id,post_date,post_title FROM $wpdb->posts WHERE `post_type`='post' and `post_status`='publish' ORDER BY ID DESC";
	$news=$wpdb->get_results($sql);

	foreach ($news as $new){
		$posts[]=array('newId'=>$new->id,'new_title'=>$new->post_title,'post_date'=>$new->post_date);
	}	
	return $posts;	
}


/**
 *
 * @access public
 * @author Greg 'Adam Baum'
 * @since 1.13 - 2002/01/23
 * @param integer $startnum start iteam
 * @param integer $total total number of items present
 * @param string $urltemplate template for url, will replace '%%' with item number
 * @param integer $perpage number of links to display (default=10)
 */
function Pager($startnum, $total, $urltemplate, $perpage = 20)
{
	// Quick check to ensure that we have work to do
	if ($total <= $perpage) {
		return;
	}

	if (empty($startnum)) {
		$startnum = 1;
	}

	if (empty($perpage)) {
		$perpage = 10;
	}

	// Check that we are needed
	if ($total <= $perpage) {
		return;
	}

	$sortida='';
	
	// Show startnum link
	if ($startnum != 1) {
		$url = preg_replace('/%%/', 1, $urltemplate);
		$sortida.= 'P&agrave;gina <a href="'.$url.'"  style="text-decoration:none; color:#1E4588;"><<</a>';			
	} else {
		$sortida.= 'P&agrave;gina <<';
	}

	$sortida.= ' ';

	$pagenum = 1; 

	$sortida.= ' | ';

	for ($curnum = 1; $curnum <= $total; $curnum += $perpage) { 
		if (($startnum < $curnum -1) || ($startnum +1 > ($curnum + $perpage - 1))) { 
			if ((($pagenum%10)==0) // link if page is multiple of 10 
				|| ($pagenum==1) // link first page 
				|| (($curnum >($startnum-4*$perpage)) //link -3 and +3 pages 
				&&($curnum <($startnum+4*$perpage))) 
			) { 
				$url = preg_replace('/%%/', $curnum, $urltemplate); 
				$sortida.= '<a href="'.$url.'" style="text-decoration:none; color:#1E4588;">'.$pagenum.'</a>';
				$sortida.= ' | ';
			} 
		} else { 
			$sortida.='<strong><u>'.$pagenum.'</u></strong>  | ';
		} 
		$pagenum++; 
	}
	if (($curnum >= $perpage + 1) && ($startnum < $curnum - $perpage)) {
		$url = preg_replace('/%%/', $curnum - $perpage, $urltemplate);
		$curnum=$curnum - $perpage;
		$sortida.= '<a href="'.$url.'" style="text-decoration:none; color:#1E4588;">>></a>';			
	} else {
		$sortida.= '>>';
	}
	return $sortida;
}
