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

// Maximum number of rows read from the template, and of rows and bytes of each insertion in the target blog
const XTEC_SITE_CLONER_ROWS_PER_QUERY = 500;
const XTEC_SITE_CLONER_MAX_INSERT_SIZE = 1048576;

// Options of the target blog that are kept instead of being copied from the template
const XTEC_SITE_CLONER_PRESERVED_OPTIONS = [
    'siteurl',
    'home',
    'blogname',
    'admin_email',
    'upload_path',
    'upload_url_path',
    'fileupload_url',
    'WPLANG',
];

/**
 * Copies a template blog into an existing blog, whose content is replaced: its tables, its files and its visibility.
 * The options of XTEC_SITE_CLONER_PRESERVED_OPTIONS of the target blog are kept.
 *
 * Each query names a single table, so that the database router sends it to the database of its blog, and the rows are
 * copied by PHP instead of INSERT ... SELECT, as the databases of the two blogs may be in different servers.
 *
 * @param int $template_id The ID of the template blog.
 * @param int $target_id The ID of the target blog.
 * @return WP_Error|null The error, or null if the copy was successful.
 */
function xtec_site_cloner_copy(int $template_id, int $target_id): ?WP_Error
{
    global $wpdb;

    $template = get_site($template_id);
    $target = get_site($target_id);
    $error = xtec_site_cloner_validate_copy($template, $target);
    if ($error !== null) {
        return $error;
    }

    $template_prefix = $wpdb->get_blog_prefix($template_id);
    $target_prefix = $wpdb->get_blog_prefix($target_id);

    $tables = xtec_site_cloner_get_tables($template_id);
    if (!in_array($template_prefix . 'options', $tables, true)) {
        return new WP_Error(
            'xtec_site_cloner_no_tables',
            __('The tables of the template blog have not been found.', 'xtec-site-cloner')
        );
    }

    /**
     * Filters the options of the target blog that are kept instead of being copied from the template.
     *
     * @param string[] $options The names of the options.
     * @param int $template_id The ID of the template blog.
     * @param int $target_id The ID of the target blog.
     */
    $preserved = (array)apply_filters(
        'xtec_site_cloner_preserved_options',
        XTEC_SITE_CLONER_PRESERVED_OPTIONS,
        $template_id,
        $target_id
    );
    $saved_options = xtec_site_cloner_get_options($target_prefix . 'options', $preserved);

    /**
     * Fires before copying the template blog into the target blog.
     *
     * @param int $template_id The ID of the template blog.
     * @param int $target_id The ID of the target blog.
     */
    do_action('xtec_site_cloner_before_copy', $template_id, $target_id);

    // The folders of the files are got before copying, as the options of the target blog are replaced after
    $template_uploads = xtec_site_cloner_get_uploads($template_id);
    $target_uploads = xtec_site_cloner_get_uploads($target_id);
    $patterns = xtec_site_cloner_get_replacements($template, $target, $template_uploads, $target_uploads);

    foreach ($tables as $table) {
        $suffix = substr($table, strlen($template_prefix));
        $error = xtec_site_cloner_copy_table($table, $target_prefix . $suffix, $suffix, $patterns);
        if ($error !== null) {
            return new WP_Error(
                'xtec_site_cloner_table',
                /* translators: 1: the name of the table, 2: the error of the database */
                sprintf(__('Could not copy the table %1$s: %2$s', 'xtec-site-cloner'), $table, $error)
            );
        }
    }

    $error = xtec_site_cloner_restore_options($target_prefix . 'options', $preserved, $saved_options);
    if ($error !== null) {
        return new WP_Error(
            'xtec_site_cloner_options',
            /* translators: %s: the error of the database */
            sprintf(__('Could not keep the options of the blog: %s', 'xtec-site-cloner'), $error)
        );
    }

    // The visibility is copied too, which is kept in the table of the blogs as well as in the options
    wp_update_site($target_id, ['public' => (int)$template->public]);

    xtec_site_cloner_flush_cache($target_id);

    $error = xtec_site_cloner_copy_files($template_uploads['basedir'], $target_uploads['basedir']);
    if ($error !== null) {
        return new WP_Error('xtec_site_cloner_files', $error);
    }

    /**
     * Fires after copying the template blog into the target blog.
     *
     * @param int $template_id The ID of the template blog.
     * @param int $target_id The ID of the target blog.
     */
    do_action('xtec_site_cloner_after_copy', $template_id, $target_id);

    return null;
}

/**
 * Checks that a blog can be copied into another one.
 *
 * @param WP_Site|null $template The template blog.
 * @param WP_Site|null $target The target blog.
 * @return WP_Error|null The error, or null if the copy can be done.
 */
function xtec_site_cloner_validate_copy(?WP_Site $template, ?WP_Site $target): ?WP_Error
{
    if ($template === null) {
        return new WP_Error('xtec_site_cloner_template', __('The template blog does not exist.', 'xtec-site-cloner'));
    }

    if ($target === null) {
        return new WP_Error('xtec_site_cloner_target', __('The target blog does not exist.', 'xtec-site-cloner'));
    }

    // The tables of the main blog have the prefix of the network, which the global tables have too
    if (is_main_site($template->id) || is_main_site($target->id)) {
        return new WP_Error(
            'xtec_site_cloner_main_site',
            __('The main blog cannot be copied nor replaced.', 'xtec-site-cloner')
        );
    }

    if ($template->id === $target->id) {
        return new WP_Error(
            'xtec_site_cloner_same_site',
            __('The template and the target blogs must be different.', 'xtec-site-cloner')
        );
    }

    return null;
}

/**
 * Returns the tables of a blog, including the ones of the plugins.
 *
 * @param int $blog_id The ID of the blog.
 * @return string[] The names of the tables.
 */
function xtec_site_cloner_get_tables(int $blog_id): array
{
    global $wpdb;

    // The prefix is escaped because _ is a wildcard of LIKE: wp_10_% would also match the tables of the blog 100
    $prefix = $wpdb->get_blog_prefix($blog_id);
    $tables = $wpdb->get_col($wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($prefix) . '%'));

    return array_values(array_filter($tables, static fn(string $table): bool => str_starts_with($table, $prefix)));
}

/**
 * Returns some options from a table of options, with all their fields, to save them again later.
 *
 * @param string $table The table of options.
 * @param string[] $names The names of the options.
 * @return array The rows of the options that exist.
 */
function xtec_site_cloner_get_options(string $table, array $names): array
{
    global $wpdb;

    if (!$names) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($names), '%s'));

    return $wpdb->get_results(
        $wpdb->prepare(
            "SELECT option_name, option_value, autoload FROM `$table` WHERE option_name IN ($placeholders)",
            $names
        ),
        ARRAY_A
    );
}

/**
 * Saves again some options in a table of options. The options that didn't exist are removed.
 *
 * @param string $table The table of options.
 * @param string[] $names The names of the options.
 * @param array $options The rows of the options that existed.
 * @return string|null The error of the database, or null if the options were saved.
 */
function xtec_site_cloner_restore_options(string $table, array $names, array $options): ?string
{
    global $wpdb;

    if (!$names) {
        return null;
    }

    $placeholders = implode(',', array_fill(0, count($names), '%s'));
    $wpdb->query($wpdb->prepare("DELETE FROM `$table` WHERE option_name IN ($placeholders)", $names));
    if ($wpdb->last_error !== '') {
        return $wpdb->last_error;
    }

    foreach ($options as $option) {
        if ($wpdb->insert($table, $option, ['%s', '%s', '%s']) === false) {
            return $wpdb->last_error;
        }
    }

    return null;
}

/**
 * Returns the replacements that adapt the data of the template blog to the target blog:
 * - Its address and the address of its files, without the protocol, so that they are replaced with any of them.
 * - The folder of its files, relative to WordPress, so that it is replaced in absolute and relative paths.
 * - The prefix of its tables, which is in the names of some options.
 *
 * The addresses and the folder aren't replaced inside longer ones, like the address of another blog whose name starts
 * with the name of the template or the folder of the files of the blog 23 for the blog 2, and they are also replaced
 * with the slashes escaped, as they are in JSON. The addresses without the domain, like /template/page, aren't
 * replaced.
 *
 * @param WP_Site $template The template blog.
 * @param WP_Site $target The target blog.
 * @param array $template_uploads The folder and the address of the files of the template blog, as returned by
 *                                xtec_site_cloner_get_uploads().
 * @param array $target_uploads The folder and the address of the files of the target blog.
 * @return array The regular expressions and their replacements.
 */
function xtec_site_cloner_get_replacements(
    WP_Site $template,
    WP_Site $target,
    array $template_uploads,
    array $target_uploads
): array {
    global $wpdb;

    $before_address = '(?<![\w.-])';
    $after_path = '(?![\w-])';
    $patterns = [];

    xtec_site_cloner_add_pattern(
        $patterns,
        untrailingslashit($template->domain . $template->path),
        untrailingslashit($target->domain . $target->path),
        $before_address,
        $after_path
    );
    xtec_site_cloner_add_pattern(
        $patterns,
        xtec_site_cloner_remove_protocol($template_uploads['baseurl']),
        xtec_site_cloner_remove_protocol($target_uploads['baseurl']),
        $before_address,
        $after_path
    );
    xtec_site_cloner_add_pattern(
        $patterns,
        xtec_site_cloner_relative_path($template_uploads['basedir']),
        xtec_site_cloner_relative_path($target_uploads['basedir']),
        '',
        $after_path
    );
    xtec_site_cloner_add_pattern(
        $patterns,
        $wpdb->get_blog_prefix($template->id),
        $wpdb->get_blog_prefix($target->id),
        '(?<!\w)',
        ''
    );

    return $patterns;
}

/**
 * Adds a replacement to a list of replacements, also with the slashes escaped as in JSON. The replacement is literal.
 *
 * @param array $patterns The list of regular expressions and their replacements.
 * @param string $search The text that is replaced.
 * @param string $replace The new text.
 * @param string $before The regular expression of what can't be before the text.
 * @param string $after The regular expression of what can't be after the text.
 */
function xtec_site_cloner_add_pattern(
    array &$patterns,
    string $search,
    string $replace,
    string $before,
    string $after
): void {
    if ($search === $replace) {
        return;
    }

    $variants = [$search => $replace];
    if (str_contains($search, '/')) {
        $variants[str_replace('/', '\/', $search)] = str_replace('/', '\/', $replace);
    }

    foreach ($variants as $text => $new_text) {
        // The backslashes and the dollar signs of the replacement mustn't be references to the matches
        $patterns['/' . $before . preg_quote($text, '/') . $after . '/'] = addcslashes($new_text, '\\$');
    }
}

/**
 * Removes the protocol of an address: https://example.com becomes example.com.
 *
 * @param string $url The address.
 * @return string The address without the protocol.
 */
function xtec_site_cloner_remove_protocol(string $url): string
{
    return (string)preg_replace('#^[a-z]+://#i', '', $url);
}

/**
 * Returns a path relative to the folder of WordPress, or the same path if it is outside it.
 *
 * @param string $path The absolute path.
 * @return string The relative path.
 */
function xtec_site_cloner_relative_path(string $path): string
{
    return str_starts_with($path, ABSPATH) ? substr($path, strlen(ABSPATH)) : $path;
}

/**
 * Copies a table of the template blog into the target blog, replacing the table if it exists.
 *
 * @param string $source The table of the template blog.
 * @param string $target The table of the target blog.
 * @param string $suffix The name of the table without the prefix of the blog, like options or posts.
 * @param array $patterns The replacements of xtec_site_cloner_get_replacements().
 * @return string|null The error of the database, or null if the table was copied.
 */
function xtec_site_cloner_copy_table(string $source, string $target, string $suffix, array $patterns): ?string
{
    global $wpdb;

    $create = $wpdb->get_row("SHOW CREATE TABLE `$source`", ARRAY_N);
    if (!isset($create[1])) {
        return $wpdb->last_error ?: __('The structure of the table could not be read.', 'xtec-site-cloner');
    }

    $wpdb->query("DROP TABLE IF EXISTS `$target`");
    if ($wpdb->last_error !== '') {
        return $wpdb->last_error;
    }

    $wpdb->query(preg_replace(
        '/^CREATE TABLE `' . preg_quote($source, '/') . '`/',
        'CREATE TABLE `' . $target . '`',
        $create[1],
        1
    ));
    if ($wpdb->last_error !== '') {
        return $wpdb->last_error;
    }

    // The rows are read by pages in the order of the primary key, so that no row is skipped nor repeated
    $keys = $wpdb->get_col("SHOW KEYS FROM `$source` WHERE Key_name = 'PRIMARY'", 4);
    $order = $keys ? ' ORDER BY `' . implode('`, `', $keys) . '`' : '';
    $offset = 0;

    do {
        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM `$source`$order LIMIT %d OFFSET %d",
                XTEC_SITE_CLONER_ROWS_PER_QUERY,
                $offset
            ),
            ARRAY_A
        );
        if ($wpdb->last_error !== '') {
            return $wpdb->last_error;
        }
        $offset += count($rows);

        $error = xtec_site_cloner_insert_rows($target, $suffix, $rows, $patterns);
        if ($error !== null) {
            return $error;
        }
    } while (count($rows) === XTEC_SITE_CLONER_ROWS_PER_QUERY);

    return null;
}

/**
 * Inserts rows of the template blog into a table of the target blog, with the replacements, grouped in insertions of
 * several rows of limited size.
 *
 * @param string $target The table of the target blog.
 * @param string $suffix The name of the table without the prefix of the blog.
 * @param array $rows The rows, by the names of the columns.
 * @param array $patterns The replacements of xtec_site_cloner_get_replacements().
 * @return string|null The error of the database, or null if the rows were inserted.
 */
function xtec_site_cloner_insert_rows(string $target, string $suffix, array $rows, array $patterns): ?string
{
    global $wpdb;

    $values = [];
    $size = 0;
    $columns = '';

    foreach ($rows as $row) {
        /**
         * Filters if a row of the template blog is copied. The transients aren't copied.
         *
         * @param bool $copy If the row is copied.
         * @param array $row The row, by the names of the columns.
         * @param string $suffix The name of the table without the prefix of the blog, like options or posts.
         */
        if (!apply_filters('xtec_site_cloner_copy_row', !xtec_site_cloner_is_transient($suffix, $row), $row, $suffix)) {
            continue;
        }

        $columns = '`' . implode('`, `', array_keys($row)) . '`';
        $sql_values = array_map(
            static fn(mixed $value): string => $value === null
                ? 'NULL'
                : "'" . esc_sql((string)xtec_site_cloner_replace((string)$value, $patterns)) . "'",
            $row
        );
        $value = '(' . implode(', ', $sql_values) . ')';
        $values[] = $value;
        $size += strlen($value);

        if ($size >= XTEC_SITE_CLONER_MAX_INSERT_SIZE) {
            $wpdb->query("INSERT INTO `$target` ($columns) VALUES " . implode(', ', $values));
            if ($wpdb->last_error !== '') {
                return $wpdb->last_error;
            }
            $values = [];
            $size = 0;
        }
    }

    if ($values) {
        $wpdb->query("INSERT INTO `$target` ($columns) VALUES " . implode(', ', $values));
        if ($wpdb->last_error !== '') {
            return $wpdb->last_error;
        }
    }

    return null;
}

/**
 * Checks if a row is a transient, which is temporary data that the blog generates again.
 *
 * @param string $suffix The name of the table without the prefix of the blog.
 * @param array $row The row, by the names of the columns.
 * @return bool True if the row is a transient.
 */
function xtec_site_cloner_is_transient(string $suffix, array $row): bool
{
    if ($suffix !== 'options' || !isset($row['option_name'])) {
        return false;
    }

    return str_starts_with($row['option_name'], '_transient_')
        || str_starts_with($row['option_name'], '_site_transient_');
}

/**
 * Applies the replacements to a value. The serialized values are replaced inside, so that the lengths of their strings
 * are updated, and they are serialized again only if they have changed.
 *
 * @param mixed $value The value.
 * @param array $patterns The replacements of xtec_site_cloner_get_replacements().
 * @return mixed The value with the replacements.
 */
function xtec_site_cloner_replace(mixed $value, array $patterns): mixed
{
    if (is_string($value)) {
        if (is_serialized($value)) {
            // The objects of other classes are restored as incomplete objects, which are serialized again unchanged
            $data = unserialize($value, ['allowed_classes' => ['stdClass']]);
            if ($data !== false || $value === serialize(false)) {
                $replaced = xtec_site_cloner_replace($data, $patterns);

                return $replaced === $data ? $value : serialize($replaced);
            }
        }

        return preg_replace(array_keys($patterns), array_values($patterns), $value) ?? $value;
    }

    if (is_array($value)) {
        foreach ($value as $key => $item) {
            $value[$key] = xtec_site_cloner_replace($item, $patterns);
        }

        return $value;
    }

    if ($value instanceof stdClass) {
        $copy = clone $value;
        foreach (get_object_vars($copy) as $key => $item) {
            $copy->$key = xtec_site_cloner_replace($item, $patterns);
        }

        return $copy == $value ? $value : $copy;
    }

    return $value;
}

/**
 * Removes the cached data of the target blog, whose options have been replaced in the database.
 *
 * @param int $target_id The ID of the target blog.
 */
function xtec_site_cloner_flush_cache(int $target_id): void
{
    if (wp_cache_supports('flush_group')) {
        wp_cache_flush_group('options');
    } else {
        wp_cache_flush();
    }

    clean_blog_cache($target_id);
}

/**
 * Copies the files of the template blog to the folder of the target blog.
 *
 * @param string $source The folder of the files of the template blog.
 * @param string $target The folder of the files of the target blog.
 * @return string|null The error, or null if the files were copied.
 */
function xtec_site_cloner_copy_files(string $source, string $target): ?string
{
    if (!is_dir($source)) {
        return null;
    }

    if ($source === $target) {
        return sprintf(
            /* translators: %s: the folder of the files */
            __('The template and the target blogs have the same folder of files: %s', 'xtec-site-cloner'),
            $source
        );
    }

    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($items as $item) {
        $path = $target . substr($item->getPathname(), strlen($source));
        $copied = $item->isDir() ? wp_mkdir_p($path) : wp_mkdir_p(dirname($path)) && copy($item->getPathname(), $path);
        if (!$copied) {
            /* translators: %s: the path of the file */
            return sprintf(__('Could not copy the file %s', 'xtec-site-cloner'), $item->getPathname());
        }
    }

    return null;
}

/**
 * Returns the folder and the address of the files of a blog.
 *
 * @param int $blog_id The ID of the blog.
 * @return string[] The folder (basedir) and the address (baseurl), without the final slash.
 */
function xtec_site_cloner_get_uploads(int $blog_id): array
{
    switch_to_blog($blog_id);
    $uploads = wp_upload_dir(null, false, true);
    restore_current_blog();

    return [
        'basedir' => untrailingslashit($uploads['basedir']),
        'baseurl' => untrailingslashit($uploads['baseurl']),
    ];
}
