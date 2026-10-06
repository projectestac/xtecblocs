<?php
/**
 * The template for displaying search forms.
 *
 * @package Reddle
 * @since Reddle 1.0
 */
?>
<form method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="s" class="assistive-text"><?php _e( 'Search', 'reddle' ); ?></label>
	<input type="text" class="field" name="s" id="s" placeholder="<?php esc_attr_e( 'Search', 'reddle' ); ?>" />
	<input type="submit" class="submit" name="submit" id="searchsubmit" value="<?php esc_attr_e( 'Search', 'reddle' ); ?>" />
</form>
