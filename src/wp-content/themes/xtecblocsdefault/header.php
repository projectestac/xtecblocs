<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ca" lang="ca">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="generator" content="Bluefish 1.0.7"/> <!-- leave this for stats -->
<title><?php bloginfo('name'); ?> <?php if ( is_single() ) { ?> &raquo; Blog Archive <?php } ?> <?php wp_title(); ?></title>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/prototype.js"></script>
<!--<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/XTECBlocs.js"></script>-->
<!--<script>var template_directory = "<?php echo bloginfo('template_directory');?>";</script>-->
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />



<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<!-- Here we use a conditional comment to give IE/5/6/Win the javascript hack that helps them do max-width -->
<!--[if IE ]>
<link title="default" href="<?php bloginfo('template_directory'); ?>/css/ie6.css" rel="stylesheet" type="text/css" media="screen" />
<![endif]--> 

<!--[if IE 7]>
<link title="default" href="<?php bloginfo('template_directory'); ?>/css/ie7.css" rel="stylesheet" type="text/css" media="screen" />
<![endif]-->

<!--[if IE ]>
<style type="text/css">
#sizer {
	width:expression(document.body.clientWidth > 1280 ? "280px" : "100%" );
}
</style>
<![endif]-->  

 
<?php wp_head(); ?>
</head>
<body>

<div id="sizer">
	<div id="expander">
		<div id="wrapper" class="clearfix">
			<div id="header">
				<span class="logoDepartament">&nbsp;</span>
				<h1 title="<?php echo get_option('home'); ?>"><a href="<?php echo get_option('home'); ?>"><acronym title="Xarxa Educativa Telemàtica Educativa de Catalunya">XTEC</acronym> Blocs <span></span></a></h1>
			</div> <!-- end of header -->
			<div id="nav">
					<span class="navrightcorner">&nbsp;</span>
					<span class="navleftcorner">&nbsp;</span>
					<ul class="list">
						<!-- check if user is login -->
						<?php if (!is_user_logged_in()){						
							?>
						   <li style="display: none;">Usuari anònim</li>			
						<?php }else{
							global $userdata;
							get_currentuserinfo();	
							?>
							<li class="login">T'has identificat com a [ <a href="<?php echo get_option('siteurl');?>/wp-admin/profile.php"><?php echo $userdata->user_login; ?></a> ]</li>  
						<?php } ?>
						<!-- end of check user -->
						<li><a href="<?php echo get_option('home');?>">Inici</a></li>
<!--XTEC ************ MODIFICAT - Canvi enllaç a Fòrum per Suport
2013.05.13 @author-->
						<li><a target="_blank" href="http://educat.xtec.cat/group/blocs-a-l-aula">Suport</a></li>
<!--************ ORIGINAL
						<li><a target="_blank" href="http://phobos.xtec.cat/forum/viewforum.php?f=45">Fòrum</a></li>
************ FI-->
						<li><a href="index.php?a=terms">Condicions d'ús</a></li>
						<li><a href="http://sites.google.com/a/xtec.cat/ajudaxtecblocs" target="_blank">Ajuda</a></li>
				<?php if (!is_user_logged_in()){?>
<!--XTEC ************ MODIFICAT - Substitució wp-content/themes/xtecblocsdefault/login.php
2014.10.31 @jmiro227-->
						<li><a id="surt" href="<?php echo get_option('home');?>/wp-login.php?redirect_to=<?php echo site_url() ?>">Entra</a></li>
<!--************ ORIGINAL
						<li><a id="surt" href="<?php echo get_option('home');?>/index.php?a=login">Entra</a></li>
************ FI-->
				<?php }else{?>
						<li><a href="<?php echo wp_logout_url(site_url()) ?>" title="Surt">Surt</a></li>
				<?php } ?>
						<li><a href="feed" class="rss"><img src="<?php bloginfo('template_directory'); ?>/css/img/rss.png" alt="RSS"/></a></li>
					</ul>
	
			</div>
			<!-- 
			-->
