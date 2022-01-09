<?php

use CommonsBooking\CB\CB;
use CommonsBooking\Model\CustomPost;
use CommonsBooking\Model\Day;
use CommonsBooking\Model\Week;
use CommonsBooking\Plugin;
use CommonsBooking\Wordpress\CustomPostType\Item;
use CommonsBooking\Wordpress\CustomPostType\Location;
use CommonsBooking\Wordpress\CustomPostType\Timeframe;



/*-------------------------------------------------------------------------------
 * START Funktionen zum Stylesheet einreihen
 * -------------------------------------------------------------------------------
 * Reiht die CSS Files ein, damit die nur wenn nötig geladen werden.
 * ---------------------------------------------------------------------------------
 * START Funktionen zum Stylesheet  + JS einreihen
 * ---------------------------------------------------------------------------------
*/
function enqueue_accordion_styles(){
	wp_register_style('accordion', get_stylesheet_directory_uri() . '/inc/View/css/accordion.css', __FILE__);
	wp_enqueue_style('accordion');
	wp_enqueue_script('accordion-js', get_stylesheet_directory_uri() . '/inc/View/js/accordion.js');

}

/*-------------------------------------------------------------------------------
 * START cb-item-single Hook
 * -------------------------------------------------------------------------------
 * Diese Funktion wird direkt von der commonsbooking/templates/item-single.php aufgerufen und leitet, je nach Kategorie, zu den verschiedenen Funktionen weiter die dann die
 * Felder im Frontend darstellen
 * ---------------------------------------------------------------------------------
 * START cb-item-single Hook
 * ---------------------------------------------------------------------------------
*/

function cb_item_single_hook(){
	require_once(get_stylesheet_directory() . '/inc/View/ACFGallery.php');
	require_once(get_stylesheet_directory() . '/inc/View/postGrid.php');
	if ( has_term('lastenrad','cb_items_category') ){
			require_once (get_stylesheet_directory() . '/inc/Templates/lastenradTemplate.php');
    	cb_acfprintlara();
	}
	elseif ( has_term('anhaenger','cb_items_category') ) {
		require_once (get_stylesheet_directory() . '/inc/Templates/anhaengerTemplate.php');
		cb_acfprinttrailer();
	}
	elseif ( has_term('zubehoer','cb_items_category') ) {
		require_once (get_stylesheet_directory() . '/inc/Templates/zubehoerTemplate.php');
		cb_acfprintzubehoer();
	}
	elseif ( has_term('inklusion','cb_items_category') ) {
		require_once (get_stylesheet_directory() . '/inc/Templates/inklusionTemplate.php');
		cb_acfprintinklusion();
	}
}
add_action( 'cb_item_single_hook', 'cb_item_single_hook');

/*-------------------------------------------------------------------------------
 * ENDE cb-item-single Hook
 * -------------------------------------------------------------------------------
 * Diese Funktion wird direkt von der commonsbooking/templates/item-single.php aufgerufen und leitet, je nach Kategorie, zu den verschiedenen Funktionen weiter die dann die
 * Felder im Frontend darstellen
 * ---------------------------------------------------------------------------------
 * ENDE cb-item-single Hook
 * ---------------------------------------------------------------------------------
*/
?>
