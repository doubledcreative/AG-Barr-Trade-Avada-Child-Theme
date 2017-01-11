<?php

function theme_enqueue_styles() {
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'avada-stylesheet' ) );
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );

function avada_lang_setup() {
	$lang = get_stylesheet_directory() . '/languages';
	load_child_theme_textdomain( 'Avada', $lang );
}
add_action( 'after_setup_theme', 'avada_lang_setup' );
    
////////////////////////////////////////////////////////////////////////////////////////////////////

/* Load LESS */

function childtheme_scripts() {

wp_enqueue_style('less', get_stylesheet_directory_uri() .'/css/style.less');
add_filter('style_loader_tag', 'my_style_loader_tag_function');

wp_enqueue_script('less', get_stylesheet_directory_uri() .'/scripts/less.min.js', array('jquery'),'2.5.0');

}
add_action('wp_enqueue_scripts','childtheme_scripts', 150);

function my_style_loader_tag_function($tag){   
  return preg_replace("/='stylesheet' id='less-css'/", "='stylesheet/less' id='less-css'", $tag);
}

////////////////////////////////////////////////////////////////////////////////////////////////////

/* Remove Date from Yoast SEO */

add_filter( 'wpseo_show_date_in_snippet_preview', false);

////////////////////////////////////////////////////////////////////////////////////////////////////

add_shortcode( 'divider', 'shortcode_insert_divider' );
function shortcode_insert_divider( ) {
return '<div class="divider"></div>';
}

////////////////////////////////////////////////////////////////////////////////////////////////////


/* Remove Dates from SEO on Pages */


function wpd_remove_modified_date(){
    if( is_page() ){
        add_filter( 'the_time', '__return_false' );
        add_filter( 'the_modified_time', '__return_false' );
        add_filter( 'get_the_modified_time', '__return_false' );
        add_filter( 'the_date', '__return_false' );
        add_filter( 'the_modified_date', '__return_false' );
        add_filter( 'get_the_modified_date', '__return_false' );
    }
}
add_action( 'template_redirect', 'wpd_remove_modified_date' );


////////////////////////////////////////////////////////////////////////////////////////////////////


/* Remove Query String */


function _remove_script_version( $src ){
  $parsed = parse_url($src);

  if (isset($parsed['query'])) {
    parse_str($parsed['query'], $qrystr);
    if (isset($qrystr['ver'])) {
      unset($qrystr['ver']); 
    }
    $parsed['query'] = http_build_query($qrystr);
  }
  // return http_build_url($parsed); // elegant but not always available

  $src = '';
  $src .= (!empty($parsed['scheme'])) ? $parsed['scheme'].'://' : '';
  $src .= (!empty($parsed['host'])) ? $parsed['host'] : '';
  $src .= (!empty($parsed['path'])) ? $parsed['path'] : '';
  $src .= (!empty($parsed['query'])) ? '?'.$parsed['query'] : '';

  return $src;
}
add_filter( 'script_loader_src', '_remove_script_version', 15, 1 );
add_filter( 'style_loader_src', '_remove_script_version', 15, 1 );

////////////////////////////////////////////////////////////////////////////////////////////////////


/* Add Field Visibility Section to Gravity Forms */		

		
add_filter( 'gform_enable_field_label_visibility_settings', '__return_true' );

add_filter("gform_init_scripts_footer", "init_scripts");
function init_scripts() {
return true;
}

////////////////////////////////////////////////////////////////////////////////////////////////////


/* Membership 2 */		

/*function wpmu_ms_form_placeholder() {
    ?>
    <script type="text/javascript">
    jQuery(function($) {
         $('#first_name').attr("placeholder", "First Name");
	     $('#last_name').attr("placeholder", "Surname");
	     $('#username').attr("placeholder", "Username");
	     $('#email').attr("placeholder", "Your Email Address");
	     $('#password').attr("placeholder", "Password");
	     $('#password2').attr("placeholder", "Verify Password");

    });
    </script>
    <?php
}
add_action('wp_footer', 'wpmu_ms_form_placeholder');*/

add_filter('ms_frontend_handle_registration', '__return_false');

add_action( 'gform_user_registered', 'pi_gravity_registration_autologin', 10, 4 );

	function pi_gravity_registration_autologin( $user_id, $user_config, $entry, $password ) {
	$user = get_userdata( $user_id );
	$user_login = $user->user_login;
	$user_password = $password;
	
	wp_signon( array(
	'user_login' => $user_login,
	'user_password' => $user_password,
	'remember' => false
	) );
}