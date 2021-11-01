<?php
/**
 * --------------------------------------------------------------------------------------
 *  / ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ \
|  /~~\                                                                                                                                                                                             /~~\  |
|\ \   |  Freie Lasten Theme 0.6 (31.10.21 12:17) auf den Schultern des Neve Themes  (https://github.com/hansmorb/freielasten_wptheme)                                                                                                             |   / /|
| \   /|  Funktionen: - ACF mit cb-item-single.php Template verbinden (inc/cb-items-single_acf.php) 80%done
|\   / |              -- Automatische Post Grids die Möglichkeit der Buchbarkeit eines Artikels berücksichtigen 100% done
|  ~~  |              - WP Default Login deaktivieren (inc/disable_wp_login.php) 100% done                                                                                                          |  ~~  |
|      |              - Angepasstes Menü um automatisch cb_items einzugliedern (inc/custom_menu.php) 99% done                                                                                    |      |					-neustes Feature: Unterkategorisierung nach Städten, einziges To-Do: Fallback Kategorie einfügen
|      |              - Ultimate Member Honeypot Custom Validierung (inc/um_honeypot.php) 100% done                                                                                                |      |
|      |              -Eigenes Options Feld für Anpassungen (inc/AdminOptions.php) 20% done
|      |					-fügt nur das Feld für die Verwendung in ACF hinzu, der Rest passiert woanders. Kartenvorlage für Kategorienseiten noch nicht implementiert.
|      |              -                                                                                                 |      |
|      |  Autor: Hans Morbach (hansmorbach@posteo.de)                                                                                                                                              |      |
|      |  https://github.com/hansmorb
|      |
|      |  Aktuelle Fehler: Nichts bekanntes                                                                                                                                                                                    |      |
|      |  Alle Funktionen werden über die functions.php aus dem inc Ordner geladen. Bitte bei weiteren Funktionen eine neue PHP Datei erstellen und nichts in die functions.php direkt einbinden  |      |
|      |  Dokumentation unter https://freieraeder.systemausfall.org/issues/1922                                                                                                                    |      |   Vorläufige To-Do Liste: https://pad.riseup.net/p/NeueSeite-ToDo-keep
|      |                                                                                                                                                                                           |      |  Den Ultimate-Member Ordner erstellt UM selbstständig für seine Templates.
 \     |~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~|     /
  \   /                                                                                                                                                                                             \   /
   ~~~                                                                                                                                                                                               ~~~
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
require_once (get_stylesheet_directory() . '/inc/cb-item-single_acf.php'); //ACF Integrierung in Commonsbooking
require_once (get_stylesheet_directory() . '/inc/um_honeypot.php'); //UltimateMember Honeypot (Registrierungsfeld)
require_once (get_stylesheet_directory() . '/inc/change_default_wp_login.php'); //WP Standard LOGIN deaktivieren, URLS zu UM URLS ändern
require_once (get_stylesheet_directory() . '/inc/custom_menu.php'); //Custom Menü
require_once (get_stylesheet_directory() . '/inc/AdminOptions.php'); //ACF Feld für Optionsmenü
require_once (get_stylesheet_directory() . '/inc/CB_Meta_Key.php'); //ACF Feld für Optionsmenü
require_once (get_stylesheet_directory() . '/inc/QueryFunctions.php'); //Alle Custom Funktionen um posts nach bestimmten Kriterien abzufragen
?>
