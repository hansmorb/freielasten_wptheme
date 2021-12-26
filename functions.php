<?php
/**
 * --------------------------------------------------------------------------------------
* github.com/hansmorb/freielasten_wptheme                                                                                                                             ~~~
* Child theme stylesheet einbinden in Abhängigkeit vom Original-Stylesheet
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! function_exists( 'neve_child_load_css' ) ):
	/**
	 * Load CSS file.
	 */
	function neve_child_load_css() {
		wp_enqueue_style( 'neve-child-style', trailingslashit( get_stylesheet_directory_uri() ) . 'style.css', array( 'neve-style' ), NEVE_VERSION );
		wp_enqueue_script('svg-loader_min', get_stylesheet_directory_uri() . '/inc/js/svg-loader_min.js'); //Lädt SVG Loader (wird von custom_menu.php verwendet um SVG einzufärben)

	}
endif;
add_action( 'wp_enqueue_scripts', 'neve_child_load_css', 20 );
require_once(get_stylesheet_directory() . '/inc/acf_field_groups.php'); //ACF Feldgruppen (generiert von ACF)
require_once (get_stylesheet_directory() . '/inc/cb-item-single_acf.php'); //ACF Integrierung in Commonsbooking
require_once (get_stylesheet_directory() . '/inc/um_honeypot.php'); //UltimateMember Honeypot (Registrierungsfeld)
require_once (get_stylesheet_directory() . '/inc/change_default_wp_login.php'); //WP Standard LOGIN deaktivieren, URLS zu UM URLS ändern
require_once (get_stylesheet_directory() . '/inc/custom_menu.php'); //Custom Menü
require_once (get_stylesheet_directory() . '/inc/AdminOptions.php'); //ACF Feld für Optionsmenü
require_once (get_stylesheet_directory() . '/inc/CB_Meta_Key.php'); //ACF Feld für Optionsmenü
require_once (get_stylesheet_directory() . '/inc/QueryFunctions.php'); //Alle Custom Funktionen um posts nach bestimmten Kriterien abzufragen
require_once (get_stylesheet_directory() . '/inc/Shortcodes.php'); //Fügt Shortcodes hinzu

?>
