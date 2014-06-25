<?php

/*
 * @template  Mystique
 * @revised   December 20, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 *
 *
 * This is a module template for "Social Media Icons"
 * The icon data is stored in the $icons variable, which is exposed by default inside this template
 *
 * Note: the $icon->meta property is unescaped and can contain HTML code if it was added trough the dashboard
 */

?>


<ul class="media nudge" data-dir="top" data-amt="10">
  <?php foreach(array_reverse($icons) as $icon): ?>
  <li class="<?php echo $icon->ID; ?>">
    <a href="<?php echo $icon->URI; ?>" class="icon" title="<?php echo $icon->label; ?>"><span><?php echo $icon->label; ?></span></a>
  </li>
  <?php endforeach; ?>
</ul>




