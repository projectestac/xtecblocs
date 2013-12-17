<!-- start of sidebar -->
	<div id="sidebar">
		<h2>Informació lateral</h2>
		<div id="blau">
			<span class="blaurightcorner">&nbsp;</span>
			<span class="blauleftcorner">&nbsp;</span>
			<?php
                        // XTEC ***** MODIFICAT - We change the link so we go directly to the wanted page instead of going to a page who opens a iframe
                        // 2013.12.13 Marc Espinosa Zamora <marc.espinosa.zamora@upcnet.es>
                        // CODI ORIGINAL
                        // <p class="creabloc"><a href="index.php?a=new">Crea el teu bloc<span></span></a></p>
                        // CODI MODIFICAT
                        ?>
			<p class="creabloc"><a href="wp-signup.php">Crea el teu bloc<span></span></a></p>
			<?php	
			// ***** FI
			?>
		</div>

		<div id="cel">
			<span class="celrightcorner">&nbsp;</span>
			<span class="celleftcorner">&nbsp;</span>
			<!-- <p class="espaicursos"><a id="espaicursoslink" target="_blank" href="http://hipolit2.xtec.cat/blocs">Espai de cursos<span id="espaicursos"></span></a></p>-->
			<p class="espaicursos"><a id="espaicursoslink" target="_blank" href="http://blocs.xtec.cat/blocs_formacio">Espai de proves<span id="espaicursos"></span></a></p>		
		</div>

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
		<?php
			// XTEC ***** ELIMINAT - Removed blogs visibility statistics
			// ***** CODI ORIGINAL
			//$numberBlogs=getBlogsNumber();
			/*<p>En aquests moments hi ha <?php echo $numberBlogs['blogs'];?> blocs creats dels quals un <?php echo round($numberBlogs['blogsPrivate']*100/$numberBlogs['blogs'],2);?> % són privats.</p>*/
			// ***** FI
		?>
			<h3>Avís legal</h3>
			<p>XTECBlocs ha estat desenvolupat amb <a href="http://wordpress.org/" target="_blank">WordPress</a>. Articles (<a href="feed">RSS</a>) i Comentaris (<a href="comments/feed">RSS</a>).</p>
			<p class="logogencat">Logo <acronym title="Generalitat de Catalunya"><a href="http://www.gencat.cat/educacio">GENCAT<span></span></a></acronym></p>
		</div>
	</div> <!-- end of sidebar -->
