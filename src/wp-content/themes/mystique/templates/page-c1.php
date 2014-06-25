<?php

/*
 * @template  Mystique
 * @revised   October 30, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// Single column page layout.
// This is a custom page template.

/* Template Name: 1 column page (no sidebars) */
?>

<?php

  // force gettext parsers to include this string
  if(true === false)
    atom()->t('1 column page (no sidebars)');

  include 'page.php'; // same, because we only use the file name to decide the layout type to apply
