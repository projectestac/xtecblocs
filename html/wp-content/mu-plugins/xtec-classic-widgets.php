<?php

/**
 * Plugin Name:       XTEC Classic Widgets
 * Description:       Uses the classic editor of widgets in the page of the widgets and in the customizer.
 * Version:           1.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Toni Ginard
 * License:           GPL v2
 * License URI:       https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

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

defined('ABSPATH') || exit;

// WordPress uses the block editor of widgets since version 5.8, unless this filter says otherwise
add_filter('use_widgets_block_editor', '__return_false');
