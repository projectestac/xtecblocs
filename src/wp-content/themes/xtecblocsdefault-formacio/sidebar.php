<!-- start of sidebar -->
	<div id="sidebar">
		<h2>Informació lateral</h2>
		<div id="blau">
			<span class="blaurightcorner">&nbsp;</span>
			<span class="blauleftcorner">&nbsp;</span>
			<p class="creabloc"><a href="index.php?a=new">Crea el teu bloc<span></span></a></p>
		</div>

<!--
		<div id="cel">
			<span class="celrightcorner">&nbsp;</span>
			<span class="celleftcorner">&nbsp;</span>
			<p class="espaicursos"><a id="espaicursoslink" target="_blank" href="http://blocs.xtec.cat/blocs_formacio">Espai de proves<span id="espaicursos"></span></a></p>		
		</div>
-->

		<script>
		 changeImage();
		</script>
		<!-- start of sideleft -->		
		<div id="sideleft">
				<?php include("sideleft.php");?> 
		</div> <!-- end of sideleft -->
			<!-- start of sideright -->
			<div id="sideright">
					<?php include("sideright.php");?> 
			</div> <!-- end of sideright -->
		<div id="sidefooter">
			<?php $numberBlogs=getBlogsNumber();?>
			<p>En aquests moments hi ha <?php echo $numberBlogs['blogs']; ?> blocs creats dels quals un <?php echo round($numberBlogs['blogsPrivate']*100/$numberBlogs['blogs'],2);?> % són privats.</p>
			<h3>Avís legal</h3>
			<p>XTECBlocs ha estat desenvolupat amb <a href="http://wordpress.org/" target="_blank">WordPress</a>. Articles (<a href="feed">RSS</a>) i Comentaris (<a href="comments/feed">RSS</a>).</p>
			<p class="logogencat">Logo <acronym title="Generalitat de Catalunya"><a href="http://www.gencat.cat/educacio">GENCAT<span></span></a></acronym></p>
		</div>
	</div> <!-- end of sidebar -->
