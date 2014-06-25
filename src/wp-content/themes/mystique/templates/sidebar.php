<?php

/*
 * @template  Mystique
 * @revised   December 20, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// This is a template part that handles template handles the primary and secondary sidebars.
//
// If there are no widgets active, the page will revert to the next appropriate layout (1-col or 2-col-x)
// (empty sidebars are visible in design preview mode only)
//
// Notes:
//
// - sidebar-2 depends on sidebar-1, eg. if sidebar-1 is empty and sidebar-2 has visible widgets, the layout will still revert to col-1;
//   use the "visibility options" for each widget to emulate multiple independent sidebars
//
// - $app()->isAreaActive() below can return boolean FALSE (no preview mode & no active widgets),
//   but it can also return a non-boolean value which evaluates as FALSE like 0 (preview mode & no active widgets).
//   To avoid confusion we use the !== operator.
//
// - "splitter" widgets are not taken into account by isAreaActive()
//
// - widgets that don't output any relevant data are considered inactive

?>

<?php if(($count = atom()->isAreaActive('sidebar1')) !== false): // make sure we have visible widgets ?>

<!-- 1st sidebar -->
<div id="sidebar">
  <?php atom()->action('sidebar1_start'); ?>
  <ul class="blocks count-<?php echo $count; ?>">
    <?php if($count > 0 && atom()->Widgets('sidebar1') !== false):  /* show it */ else: // we're in preview mode and sidebar is empty ?>
    <li class="block">
      <div class="error box">
       <?php atom()->te('%s is empty. Add widgets here, otherwise this area will not be visible in the frontend', atom()->t('Primary Sidebar')); ?>
      </div>
    </li>
    <?php endif; ?>
  </ul>
  <?php atom()->action('sidebar1_end'); ?>
</div>
<!-- /1st sidebar -->

<?php if(($count = atom()->isAreaActive('sidebar2')) !== false): ?>
<!-- 2nd sidebar -->
<div id="sidebar2">
  <?php atom()->action("sidebar2_start"); ?>
  <ul class="blocks count-<?php echo $count; ?>">
    <?php if($count > 0 && atom()->Widgets('sidebar2') !== false):  /* show it */ else: // we're in preview mode and sidebar is empty  ?>
    <li class="block">
      <div class="error box">
       <?php atom()->te('%s is empty. Add widgets here, otherwise this area will not be visible in the frontend', atom()->t('Secondary Sidebar')); ?>
      </div>
    </li>
    <?php endif; ?>
  </ul>
  <?php atom()->action('sidebar2_end'); ?>
</div>
<!-- /2nd sidebar -->
<?php endif; ?>

<?php endif; ?>