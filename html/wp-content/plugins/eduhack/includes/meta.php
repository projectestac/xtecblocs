<?php

defined('ABSPATH') || exit;

/**
 * Reading and writing of the metadata of the projects.
 *
 * The lists (team, facilitators, status and tags) have always been saved as a serialized array, which WordPress
 * serializes again because it is a string. They are kept in the same format, and they are read from the raw metadata
 * without allowing objects, as unserializing the data of a user with objects allows the injection of PHP objects.
 */

/**
 * Returns a list of the metadata of a project.
 *
 * @param array $meta The raw metadata of the project, as returned by get_post_meta() without a key.
 * @param string $key The key of the metadata.
 * @return string[] The values of the list, or an empty list if it doesn't exist or isn't valid.
 */
function eduhack_meta_list(array $meta, string $key): array
{
    $value = $meta[$key][0] ?? '';

    // The list is serialized twice: the first level is the serialized string, the second one is the list
    for ($level = 0; $level < 2 && is_string($value); $level++) {
        if (!is_serialized($value)) {
            return [];
        }
        $value = unserialize($value, ['allowed_classes' => false]);
    }

    if (!is_array($value)) {
        return [];
    }

    return array_values(array_map(static fn(mixed $item): string => is_scalar($item) ? (string)$item : '', $value));
}

/**
 * Returns a single value of the metadata of a project.
 *
 * @param array $meta The raw metadata of the project, as returned by get_post_meta() without a key.
 * @param string $key The key of the metadata.
 * @return string The value, or an empty string if it doesn't exist.
 */
function eduhack_meta_value(array $meta, string $key): string
{
    $value = $meta[$key][0] ?? '';

    return is_string($value) ? $value : '';
}

/**
 * Saves a list in the metadata of a project, in the same format as it has always been saved.
 *
 * @param int $post_id The ID of the project.
 * @param string $key The key of the metadata.
 * @param string[] $list The values of the list.
 */
function eduhack_save_meta_list(int $post_id, string $key, array $list): void
{
    update_post_meta($post_id, $key, serialize(array_values($list)));
}

/**
 * Returns the URL of an image of the media library.
 *
 * @param string $attachment_id The ID of the image.
 * @param string|int[] $size The size of the image.
 * @return string The URL, or an empty string if the image doesn't exist.
 */
function eduhack_image_url(string $attachment_id, string|array $size = 'thumbnail'): string
{
    $image = $attachment_id === '' ? false : wp_get_attachment_image_src((int)$attachment_id, $size);

    return $image ? $image[0] : '';
}
