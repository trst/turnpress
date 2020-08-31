<?php
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
 * Creates an interface to add a redirection url, so anyone who ends up
 * at this headless instance of WP will get turned back to the proper
 * site, e.g., a static generated site on Netlify.
 *
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
 * Tie all the functions above in a neat bow.
 *
 * @return void
 */
function trst_display_theme_options_fields() {
	add_settings_section("section", "Theme Options", null, "trst-theme-options");
	
  add_settings_field("redirect_url", "Redirect URL", "trst_display_redirect_url_field", "trst-theme-options", "section");

  register_setting("section", "redirect_url");
}

add_action("admin_init", "trst_display_theme_options_fields");
