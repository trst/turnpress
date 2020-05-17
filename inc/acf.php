<?php

namespace Theme;

/**
 * Setup custom local ACF JSON directory
 *
 * TODO: Move ACF to site-specific plugin.
 *
 * @link https://www.advancedcustomfields.com/resources/local-json/
 */
add_filter('acf/settings/save_json', function () {
    return get_theme_file_path('/fields');
});

add_filter('acf/settings/load_json', function ($paths) {
    $paths[] = get_theme_file_path('/fields');

    return $paths;
});

