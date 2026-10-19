<?php

defined('ABSPATH') || exit;

/**
 * Copyright 2026 Departament d'Educació i Formació Professional
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Shows the history of the blogs deleted for inactivity.
 */
function xtec_maintenance_deleted_blogs_page(): void
{
    $table = new XtecMaintenance\DeletedBlogsTable();
    $table->prepare_items();
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Deleted blogs', 'xtec-maintenance'); ?></h1>
        <p>
            <?php
            esc_html_e(
                'History of the blogs deleted for inactivity, with the users that they had when they were deleted.',
                'xtec-maintenance'
            );
            ?>
        </p>
        <?php $table->display(); ?>
    </div>
    <?php
}
