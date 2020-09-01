<?php

require('inc/setup.php');
require('inc/acf.php');

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
 * Get Thumbnails (REST API)
 * @TODO document
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

function trst_frontpage_check($object, $field_name, $request) {
  if ($object['id'] == get_option('page_on_front')) return "frontpage";

  return false;
}

function trst_archive_check($object, $field_name, $request) {
  if ($object['id'] == get_option('page_for_posts')) return "archivepage";

  return false;
}

/**
 * @TODO Setup endpoint for front page
 * @TODO Setup endpoint for archive page
 * @TODO Setup endpoint for pages sans front & archive pages
 */

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

  // Register `listing_page` in REST API
  // Check whether page is 
  register_rest_field(
    $postTypes, // For all post_types
    'archivepage',
    array('get_callback' => 'trst_archive_check')
  );

  // Register `thumbnail` in REST API
  register_rest_field(
    $postTypes, // For all post_types
    'frontpage',
    array('get_callback' => 'trst_frontpage_check')
  );

  // Register `thumbnail` in REST API
  register_rest_field(
    $postTypes, // For all post_types
    'thumbnail',
    array('get_callback' => 'trst_get_thumbnails')
  );
}
add_action('rest_api_init', 'trst_add_fields_to_rest_api');



function trst_get_frontpage() {
  $pageID = get_option("page_on_front");
  $frontpage = get_post($pageID);
  $returnedObject = array();

  $returnedObject['title'] = $frontpage->title;
  $returnedObject['thumbnail'] = trst_get_thumbnail_object($pageID);

  return $returnedObject;
}

function trst_create_endpoints() {
  register_rest_route(
    'trst/v1',
    'frontpage',
    array(
      'methods' => 'GET',
      'callback' => 'trst_get_frontpage'
    )
  );
}

add_action('rest_api_init', 'trst_create_endpoints', 20);



/**
 * ===============================
 * Add Menu Item for Theme Options
 * ===============================
 * @return void
 */
function trst_add_theme_menu_item() {
	add_menu_page("Redirect URL", "Redirect URL", "manage_options", "trst-theme-options", "trst_theme_settings_page", null, 3);
}
add_action("admin_menu", "trst_add_theme_menu_item");


/**
 * ========================
 * Setup Theme Options Page
 * ========================
 * @return string
 */
function trst_theme_settings_page() {
  ?>
    <div class="wrap">
      <h1>Redirect URL</h1>
      <p>Add the URL of the static site that consumes this API. If someone tries to directly visit WordPress, they will be redirected to the proper site.</p>
      <form method="post" action="options.php">
          <?php
              settings_fields("section");

              do_settings_sections("trst-theme-options");      

              submit_button(); 
          ?>          
      </form>
    </div>
  <?php
}

/**
 * ==========================
 * Display Redirect URL Field
 * ==========================
 * For use on Theme Options Page; Should contain a URL. Not adding
 * validation or sanitation. Presumably, this will only be set by
 * someone who knows what they're doing, given the nature of this
 * project, but happy to take a pull request.
 *
 * @return string
 */
function trst_display_redirect_url_field() {
  ?>
    <input type="text" name="redirect_url" id="redirect_url" value="<?php echo get_option('redirect_url'); ?>" />
  <?php
}


/**
 * =====================================
 * Display Fields for Theme Options Page
 * =====================================
 * @return void
 */
function trst_display_theme_options_fields() {
	add_settings_section("section", "Theme Options", null, "trst-theme-options");
	
  add_settings_field("redirect_url", "Redirect URL", "trst_display_redirect_url_field", "trst-theme-options", "section");

  register_setting("section", "redirect_url");
}

add_action("admin_init", "trst_display_theme_options_fields");
