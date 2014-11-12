<div class="sidebox">
	<span class="sideboxright">&nbsp;</span>
	<span class="sideboxleft">&nbsp;</span>
	<h3 class="noticies">Cerca</h3>
	<div class="sidecontent">
<!-- XTEC ************ MODIFICAT - Cerca amb Google
2014.05.05 @jmiro227 -->
		<form method="POST">
			<input type="text" id="paraulaCerca" name="paraulaCerca" style="width:117px;float:left;" onkeydown="if (event.keyCode == 13) {document.getElementById('botoCerca').focus();document.getElementById('botoCerca').click();event.returnValue=false}"/>
			<button type="button" id="botoCerca" onclick="open('https://www.google.com/search?q=site:'+'<?php echo DOMAIN_CURRENT_SITE ?>'+'+'+document.getElementById('paraulaCerca').value,'','');" value="Cerca">Cerca</button>
		</form>

<!-- ************ ORIGINAL
		<form action="<?php echo get_option('home');?>/index.php" method="get" enctype="text/plain" class="cerca">
		<input type="hidden" value="search" name="a" />
		<input type="submit" value="Cerca" class="botocerca"/>
		<input type="text" name="word" maxlength="18" size="15" />

		</form>
************ FI -->

		<!-- 
		end of Cerca 
		-->
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

<!-- XTEC ************ ELIMINAT - Bloc d'exemples ja no és necessari i els blocs enllaçats no estan actualitzats
2013.10.29 @jmeler
<div class="sidebox">
	<span class="sideboxright">&nbsp;</span>
	<span class="sideboxleft">&nbsp;</span>
	<h3 class="noticies">Blocs d'exemple</h3>
	<div class="sidecontent">
		<ul>
		<li><a href="maquinaenigma" target="_blank">La màquina enigma</a></li>
		<li><a href="pissarramagica" target="_blank">La pissarra màgica</a></li>
		<li><a href="primatic" target="_blank">Les TIC a primària</a></li>
		</ul>
	</div>
</div>
************ FI -->




