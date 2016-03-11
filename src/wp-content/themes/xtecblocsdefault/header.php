<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ca" lang="ca">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="generator" content="Bluefish 1.0.7"/> <!-- leave this for stats -->
<title><?php bloginfo('name'); ?> <?php if ( is_single() ) { ?> &raquo; Blog Archive <?php } ?> <?php wp_title(); ?></title>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/prototype.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<!--[if IE 7]>
<link title="default" href="<?php bloginfo('template_directory'); ?>/css/ie7.css" rel="stylesheet" type="text/css" media="screen" />
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
						<li><a target="_blank" href="http://educat.xtec.cat/group/blocs-a-l-aula">Suport</a></li>
						<li><a href="index.php?a=terms">Condicions d'ús</a></li>
						<li><a href="http://sites.google.com/a/xtec.cat/ajudaxtecblocs" target="_blank">Ajuda</a></li>
				<?php if (!is_user_logged_in()){?>
						<li><a id="surt" href="<?php echo get_option('home');?>/wp-login.php?redirect_to=<?php echo site_url() ?>">Entra</a></li>
				<?php }else{?>
						<li><a href="<?php echo wp_logout_url(site_url()) ?>" title="Surt">Surt</a></li>
				<?php } ?>
						<li><a href="feed" class="rss"><img src="<?php bloginfo('template_directory'); ?>/css/img/rss.png" alt="RSS"/></a></li>
					</ul>
			</div>
