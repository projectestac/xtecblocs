<div class="login">

<?php
switch($_GET['a']){
	case "new":
		$redirect=site_url('?a=new');?>
		<h3>Creeu un bloc en dues passes</h3> 
		<ol>
		<li><img src="<?php bloginfo('template_directory'); ?>/images/step11.png" align="absmiddle" /> Valideu-vos al portal</li>
		<li class="step"><img src="<?php bloginfo('template_directory'); ?>/images/step12.png" align="absmiddle" /> Doneu un nom i un t&iacute;tol al bloc nou</li>
		</ol><?php
		break;
	case "login":
		$redirect=site_url();
		break;
	default:
		$redirect="";
}
?>
		
	<h3>Validació</h3> 
	<div class="loginForm">
		<p>Per validar-vos utilitzeu el nom d'usuari/ària de la XTEC o de l'edu365.</p>
		<?php if(isset($_REQUEST['error']) && $_REQUEST['error']==1) {?>
			<p class="error">Identificació incorrecta.</p>
		<?php } ?>
		
		<form name="loginform" id="loginform" action="<?php echo site_url('wp-login.php', 'login_post') ?>" method="post">
			<p>
				<label>Nom d'usuari/ària<br />
				<input type="text" name="log" id="user_login" class="input" value="<?php echo $user_login; ?>" size="20" tabindex="10" /></label>
			</p>
			<p>
				<label>Contrasenya<br />
				<input type="password" name="pwd" id="user_pass" class="input" value="" size="20" tabindex="20" /></label>
			</p>
		<?php do_action('login_form'); ?>
			<p class="forgetmenot"><label><input name="rememberme" type="checkbox" id="rememberme" value="forever" tabindex="90" /> Recorda'm</label></p>
			<p class="submit">
				<input type="submit" name="wp-submit" id="wp-submit" value="Entra" tabindex="100" />
				<?php $redirect_to =site_url('?a=new');?>
				<input type="hidden" name="redirect_to" value="<?php echo $redirect ?>" />
				<input type="hidden" name="testcookie" value="1" />
			</p>
		</form>
	</div>
	<!--<p>Si no sou usuari/ària de la XTEC podeu <a href="index.php?a=newuser">crear-vos un compte</a>, però, no podreu crear blocs nous.</p>-->
</div>
