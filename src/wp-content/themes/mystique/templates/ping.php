<?php

/*
 * @template  Mystique
 * @revised   October 30, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// Renders a single ping/trackback. Pings are handled within the meta.php template.
// Note that the "screenshot" class will enable a link preview on mouse over.
// This is a template part.

?>

<!-- ping/trackback entry -->
<li>
  <a class="screenshot" id="ping-<?php comment_ID(); ?>" href="<?php comment_author_url();?>" rel="nofollow"><?php comment_author(); ?></a>

<?php // </li> is added by WP ?>