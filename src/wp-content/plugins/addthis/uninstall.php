<?php
    $option = get_option('addthis_settings');
    if ($option && isset($option['addthis_wordpress_version'])) {
        unset($option['addthis_wordpress_version']);
        update_option('addthis_settings', $option);
    }