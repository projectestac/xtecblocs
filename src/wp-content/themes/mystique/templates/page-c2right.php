<?php

/*
 * @template  Mystique
 * @revised   October 30, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// Two columns - right sidebar page layout.
// This is a custom page template.

/* Template Name: 2 column page (right sidebar) */
?>

<?php

  // force gettext parsers to include this string
  if(true === false)
    atom()->t('2 column page (right sidebar)');

  include 'page.php'; // same, because we only use the file name to decide the layout type to apply

