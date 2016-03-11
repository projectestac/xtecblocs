<div class="sidebox">
	<span class="sideboxright">&nbsp;</span>
	<span class="sideboxleft">&nbsp;</span>
	<h3 class="noticies">Cerca</h3>
	<div class="sidecontent">
		<form method="POST">
			<input type="text" id="paraulaCerca" name="paraulaCerca" style="width:117px;float:left;" onkeydown="if (event.keyCode == 13) {document.getElementById('botoCerca').focus();document.getElementById('botoCerca').click();event.returnValue=false}"/>
			<button type="button" id="botoCerca" onclick="open('https://www.google.com/search?q=site:'+'<?php echo DOMAIN_CURRENT_SITE ?>'+'+'+document.getElementById('paraulaCerca').value,'','');" value="Cerca">Cerca</button>
		</form>
	</div>
</div>
<div class="sidebox">
	<span class="sideboxright"></span>
	<span class="sideboxleft"></span>
	<h3 class="noticies">Notícies</h3>
	<div class="sidecontent">
		<ul>
		<?php query_posts('showposts=5');	
		if (have_posts()) : while (have_posts()) : the_post(); ?>
		<li><a href="index.php?id=<?php the_id();?>"><?php the_title();?></a></li>
		<?php endwhile; ?><?php endif; ?>
		</ul>
		<ul class="cloudtags">
			<li class="mes"><a href="<?php echo get_option('home');?>/index.php?a=newsList">Més...</a></li>
		</ul>
	</div>
</div>


<div class="sidebox">
	<span class="sideboxright">&nbsp;</span>
	<span class="sideboxleft">&nbsp;</span>
	<h3 class="noticies">Descriptors</h3>
	<div class="sidecontent">
		<ul class="cloudtags">
        <?php $cloudArray = xtec_descriptors_get_descriptors_cloud(25,12,25);
        foreach ($cloudArray as $cloud){?>
            <li><a style="font-size:<?php echo $cloud['size'];?>px; color:#1E4588;" class="tag_cloud" href="<?php echo get_option('home');?>/index.php?a=list&amp;desc=<?php echo $cloud['tag'];?>">
                <?php echo $cloud['tag'];?>
            </a></li>
        <?php } ?>
		<li class="mes"><a href="<?php echo get_option('home');?>/index.php?a=allDescriptors">Més...</a></li>
		</ul>
		<!-- 
		end of els descriptors 
		-->
	</div>
</div>
