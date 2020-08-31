<?php

require('inc/setup.php');
require('inc/acf.php');
require('inc/redirection.php');

/**
 * ======================
 * Get SEO_Framework Meta
 * ======================
 * Retrieves an item's metadata provided by the SEO Framework plugin
 * and returns an array of normalized data; used in conjunction with
 * the WP REST API
 *
 * @param  { Object } $post
 * @param  { String } $field_name
 * @param  { Object } $request
 *
 * @return { Array }
 */
function trst_get_seo_framework_meta($post, $field_name, $request) {
  $metadata = the_seo_framework()->get_post_meta($post['id']);

  return array(
    'title' => $metadata['_genesis_title'],
    'description' => $metadata['_genesis_description'],
    'noindex' => $metadata['_genesis_noindex'],
    'nofollow' => $metadata['_genesis_nofollow'],
    'noarchive' => $metadata['_genesis_noarchive'],
  );
}

/**
 * =========================
 * Get Thumbnails (REST API)
 * =========================
 * Calling posts from the API doesn't bring the featured image with it.
 * This function fixes that and returns a multi-level array with all
 * the necessary info to setup srcset simply.
 *
 * @param  { Int } $id
 * @return { Array } of image urls/sizes for srcset via the API
 */
function trst_get_thumbnail_object($id) {
    $sizes = get_intermediate_image_sizes(); // Array of registered thumbnail sizes
    $thumbnail = array();

    foreach ($sizes as $size) {
      $imageDetails = wp_get_attachment_image_src(get_post_thumbnail_id($id), $size);

      if ($imageDetails == false) {
        $thumbnail['img'] = false;
        break;
      }

      $thumbnail['img'][$size] = array();
      $thumbnail['img'][$size]['url'] = $imageDetails[0];
      $thumbnail['img'][$size]['width'] = $imageDetails[1];
      $thumbnail['img'][$size]['height'] = $imageDetails[2];
    }

    if ($thumbnail['img'] != false) {
      $thumbnail['alt'] = get_post_meta(get_post_thumbnail_id($id), '_wp_attachment_image_alt', true);
    }

    return $thumbnail;
}


function trst_get_thumbnails($object, $field_name, $request) {
    return trst_get_thumbnail_object($object['id']);
}

/**
 * ======================
 * Add Fields to REST API
 * ======================
 * Modifies default REST response for public post_types by adding
 * SEO_Framework provided metadata to JSON response
 */
function trst_add_fields_to_rest_api() {
  // Get all publicly queriable post types as an array of name strings
  $postTypes = get_post_types(
    array('public' => true), // Post query
    'names', // Return name string
    'and' // Search operator
  );  

  // Register `seo` in REST API
  register_rest_field(
    $postTypes, // For all post_types
    'seo', // Accessible as post['seo']
    array('get_callback' => 'trst_get_seo_framework_meta')
  );

  // Register `thumbnail` in REST API
  register_rest_field(
    $postTypes, // For all post_types
    'thumbnail',
    array('get_callback' => 'trst_get_thumbnails')
  );
}
add_action('rest_api_init', 'trst_add_fields_to_rest_api');
