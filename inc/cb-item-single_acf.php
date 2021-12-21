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
	wp_register_style('accordion', get_stylesheet_directory_uri() . '/inc/css/accordion.css', __FILE__);
	wp_enqueue_style('accordion');
	wp_enqueue_script('accordion-js', get_stylesheet_directory_uri() . '/inc/js/accordion.js');

}

function enqueue_gallery_styles(){
	wp_register_style('gallery-css', get_stylesheet_directory_uri() . '/inc/css/gallery.css', __FILE__);
	wp_enqueue_style('gallery-css');
	wp_enqueue_script('gallery-js', get_stylesheet_directory_uri() . '/inc/js/gallery.js');
}

function enqueue_postgrid_styles(){
	wp_register_style('postgrid-css', get_stylesheet_directory_uri() . '/inc/css/postgrid.css', __FILE__);
	wp_enqueue_style('postgrid-css');
	wp_enqueue_script('postgrid-js', get_stylesheet_directory_uri() . '/inc/js/postGrid.js', array('jquery','imagesloaded','masonry'));
}

 function enqueue_itemGallery_styles(){
	 wp_register_style('itemgallery-css', get_stylesheet_directory_uri() . '/inc/css/itemGallery.css', __FILE__);
 }

/*-------------------------------------------------------------------------------
 * ENDE Funktionen zum Stylesheet einreihen
 * -------------------------------------------------------------------------------
 * Reiht die CSS Files ein, damit die nur wenn nötig geladen werden.
 * ---------------------------------------------------------------------------------
 * ENDE Funktionen zum Stylesheet einreihen
 * ---------------------------------------------------------------------------------
*/

/*-------------------------------------------------------------------------------
 * START Create ACF Gallery + Thumbnail
 * -------------------------------------------------------------------------------
 * Funktion erstellt aus dem ACF Feld Gallery eine HTML Galerie die das Thumbnail des Posts als das erste Bild hat.
 * Basierend auf https://www.w3schools.com/howto/howto_js_slideshow.asp
 * NEU: Returnet jetzt eine $print String, die dann geechot werden muss
 * ---------------------------------------------------------------------------------
 * START Create ACF Gallery + Thumbnail
 * ---------------------------------------------------------------------------------
*/

function cb_acfgallery($gallery_slug = 'galerie'){
	enqueue_gallery_styles();
	$images = get_field($gallery_slug);
	$thumbnail_url = esc_url(get_the_post_thumbnail_url());
	$thumbnail_alt = esc_attr(get_post_meta(get_post_thumbnail_id(), "_wp_attachment_image_alt", true ));
	$print = '<div class="slideshow-container">';
	$print .= '<div class="itemgallery fade">'; # Fügt nochmal ein Extra div vor den anderen für das Thumbnail als erstes Foto ein!
	$print.= '<img src="' . $thumbnail_url . '" alt="' . $thumbnail_alt . '" style="width:100%" />';
	$print .= '</div>';
	if( $images ): //Wenn es mehr als das Artikelbild gibt
		foreach( $images as $image ):
			$galleryimage_url = esc_url($image['url']);
			$galleryimage_alt = esc_attr($image['alt']);
			$galleryimage_caption = esc_html($image['caption']);
			$print .= '<div class="itemgallery fade">';
			$print .= '<img src="' . $galleryimage_url . '" alt="' . $galleryimage_alt . '" style="width:100%" />';
			//$print .= '<div class="text">' . $galleryimage_caption . '</div>';
			$print .= '</div>'; //itemgallery fade
		endforeach;
		$print .= '<a class="prev" onclick="plusSlides(-1)">&#10094;</a>';
		$print .= '<a class="next" onclick="plusSlides(1)">&#10095;</a>';
		$print .= '</div>';
		$print .= '<div style="text-align:center">';
			for($i = 1; $i < count($images) + 2; $i++): #Erstellt die Anzahl der dots (Galerie) +2 count weil wir ja noch das extra div von dem Thumbnail haben
				$print .= '<span class="dot" onclick="currentSlide(' . $i . ')"></span>';
			endfor;
	endif;
	$print .= '</div>'; //slideshow-container
	return $print;
}
/*-------------------------------------------------------------------------------
 * ENDE Create ACF Gallery + Thumbnail
 * -------------------------------------------------------------------------------
 * Funktion erstellt aus dem ACF Feld Gallery eine HTML Galerie die das Thumbnail des Posts als das erste Bild hat.
 * Basierend auf https://www.w3schools.com/howto/howto_js_slideshow.asp
 * ---------------------------------------------------------------------------------
 * ENDE Create ACF Gallery + Thumbnail
 * ---------------------------------------------------------------------------------
*/

/*-------------------------------------------------------------------------------
 * START Create ItemGallery (0% fertig)
 * -------------------------------------------------------------------------------
 * Funktion erstellt aus einer get_posts Array eine responsive gallery
 * mit Standort und Verfügbarkeit
 * -------------------------------------------------------
*/

function cb_itemGallery($items,$hideCardMeta=True){
	enqueue_itemGallery_styles();
	$cardMeta_class = 'card__meta card__meta--last';
	if ($hideCardMeta){
		$cardMeta_class = 'card__meta card__meta--last card__hidden';
	}
	if( $items ):
		$print = '<div class="slideshow-container">';
		foreach( $items as $item ):
			$itemID = $item->ID;
			$item_title = $item->post_title;
			$item_permalink = get_permalink($itemID);
			$itemThumbnailURL = get_the_post_thumbnail_url($itemID);
			$itemLocAddress = cb_item_locAdress($itemID);

			$print .= '<div class="itemgallery fade">';
				$print .= '<div class="card" id="'.$itemID.'">';
					$print .= '<div class="card__image">';
						$print .= '<img src="'.esc_url($itemThumbnailURL).'" alt="'.$item_title.'">';
						$print .= '<div class="card__overlay card__overlay--blue">';
							$print .= '<div class="card__overlay-content">';
								$print .= '<a href="'.$item_permalink.'" class="card__title">'.$item_title.'</a>';
								$print .= '<ul class="'.$cardMeta_class.'">';
									$print .= '<li><a href="'.$item_permalink.'"><i class="fas fa-map-marker"></i>'.$itemLocAddress.'</a></li>';
									$print .= '<li>' . render_item_availability($itemID) . '</li>';
								$print .= '</ul>';
							$print .= '</div><!-- end:card__overlay-content -->';
						$print .= '</div><!-- end:card__image -->';
					$print .= '</div><!-- end:card__overlay -->';
				$print .= '</div><!-- end:card -->';
			//$print .= '<div class="text">' . $galleryimage_caption . '</div>';
			$print .= '</div>'; //itemgallery fade
		endforeach;
		$print .= '<a class="prev" onclick="plusSlides(-1)">&#10094;</a>';
		$print .= '<a class="next" onclick="plusSlides(1)">&#10095;</a>';
		$print .= '</div>';
		$print .= '<div style="text-align:center">';
			for($i = 1; $i < count($items) + 1; $i++):
				$print .= '<span class="dot" onclick="currentSlide(' . $i . ')"></span>';
			endfor;
	endif;
	$print .= '</div>'; //slideshow-container
	return $print;
}

/*-------------------------------------------------------------------------------
 * ENDE Create ItemGallery
 * -------------------------------------------------------------------------------
 * Funktion erstellt aus einer get_posts Array eine responsive gallery
 * mit Standort und Verfügbarkeit
 * -------------------------------------------------------
*/

/*-------------------------------------------------------------------------------
 * START Create PostGrid from Posts (90% fertig)
 * -------------------------------------------------------------------------------
 * Funktion erstellt aus einer get_posts Array eine responsive postgrid mithilfe der postgrid.css, Design basierend auf https://codepen.io/drralte/pen/NWxyezz.
 * Returnt $print
 * To-Do:
 * CB Buchungsliste integrieren
 * -------------------------------------------------------
*/

function create_postgrid_from_posts($items,$hideCardMeta=True) {
	$cardMeta_class = 'card__meta card__meta--last';
	if ($hideCardMeta){
		$cardMeta_class = 'card__meta card__meta--last card__hidden';
	}
	enqueue_postgrid_styles();
	if ( $items ) {
			$print = '<div class="grid__wrapper">';
			foreach ($items as $item) {
				$itemID = $item->ID;
				$item_title = $item->post_title;
				$item_permalink = get_permalink($itemID);
				$itemThumbnailURL = get_the_post_thumbnail_url($itemID);
				$itemLocAddress = cb_item_locAdress($itemID);

				$print .= '<div class="grid">';
					$print .= '<div class="card" id="'.$itemID.'">';
						$print .= '<div class="card__image">';
							$print .= '<img src="'.esc_url($itemThumbnailURL).'" alt="'.$item_title.'">';
							$print .= '<div class="card__overlay card__overlay--blue">';
								$print .= '<div class="card__overlay-content">';
									$print .= '<a href="'.$item_permalink.'" class="card__title">'.$item_title.'</a>';
									$print .= '<ul class="'.$cardMeta_class.'">';
										$print .= '<li><a href="'.$item_permalink.'"><i class="fas fa-map-marker"></i>'.$itemLocAddress.'</a></li>';
										$print .= '<li>' . render_item_availability($itemID) . '</li>';
									$print .= '</ul>';
								$print .= '</div><!-- end:card__overlay-content -->';
							$print .= '</div><!-- end:card__image -->';
						$print .= '</div><!-- end:card__overlay -->';
					$print .= '</div><!-- end:card -->';
				$print .= '</div><!-- end:grid -->';
			}
			$print .= '</div><!-- end:grid__wrapper -->';
			return $print;
		}
		else {
			return False;
		}

}

function render_item_availability($cb_item) {
	$print = '<div class="cb-postgrid-item-availability">';
	if (!is_object($cb_item)) {
		$cb_item = get_post($cb_item);
	}
	$cb_item_id = $cb_item->ID;
	$locationId = \CommonsBooking\Repository\Location::getByItem( $cb_item_id, true )[0];
	$days = 7; //Die Anzahl der Tage die im vorraus angezeigt werden soll
	$date  = new DateTime();
	$today = $date->format( "Y-m-d" );
	$days_display = array_fill( 0, $days, 'n' );
  $days_cols    = array_fill( 0, $days, '<col>' );
	$month        = date( "m" );
	$month_cols   = 0;
	$colspan      = $days;
	for ( $i = 0; $i < $days; $i ++ ) {
			$month_cols ++;
			$days_display[ $i ] = $date->format( 'd' );
			$days_dates[ $i ]   = $date->format( 'Y-m-d' );
			$days_weekday[ $i ] = $date->format( 'N' );
			$daysDM[ $i ]       = $date->format( 'j.n.' );
			if ( $date->format( 'N' ) >= 7 ) {
				$days_cols[ $i ] = '<col class="bg_we">';
			}
			$date->modify( '+1 day' );
			if ( $date->format( 'm' ) != $month ) {
				$colspan    = $month_cols;
				$month_cols = 0;
				$month      = $date->format( 'm' );
			}
	}
	$last_day = $days_dates[ $days - 1 ];
					// Get data for current item/location combination
					$calendarData = \CommonsBooking\View\Calendar::getCalendarDataArray(
						$cb_item_id,
						$locationId,
						$today,
						$last_day
					);

					$gotStartDate = false;
					$gotEndDate   = false;
					$dayIterator  = 0;
					foreach ( $calendarData['days'] as $day => $data ) {

						// Skip additonal days
						if ( ! $gotStartDate && $day !== $today ) {
							continue;
						} else {
							$gotStartDate = true;
						}

						if ( $gotEndDate ) {
							continue;
						}

						if ( $day == $last_day ) {
							$gotEndDate = true;
						}
						$day_days = date("d", strtotime($day));
						$day_month = date("m", strtotime($day));
						// Check day state
						if ( ! count( $data['slots'] ) ) {
							$print .= '<div class="cb-postgrid-item-availability-day no-timeframe">';
						} elseif ( $data['holiday'] ) {
							$print .= '<div class="cb-postgrid-item-availability-day location-closed">';
						} elseif ( $data['locked'] ) {
							if ( $data['firstSlotBooked'] && $data['lastSlotBooked'] ) {
								$print .= '<div class="cb-postgrid-item-availability-day booked">';
						} elseif ( $data['partiallyBookedDay'] ) {
								$print .= '<div class="cb-postgrid-item-availability-day partially-booked">';
						}
						} else {
							$print .= '<div class="cb-postgrid-item-availability-day available">';
						}
						$print.= '<div class="cb-postgrid-availability-days">' . $day_days .'.</div>' . '<div class="cb-postgrid-availability-month">' .$day_month.'.</div></div>';
					}
	$print .= '</div>'; /*END class="cb-postgrid-item-availability"*/
	return $print;
}

/*-------------------------------------------------------------------------------
 * ENDE Create PostGrid from Posts
 * -------------------------------------------------------------------------------
*/

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
	if ( has_term('lastenrad','cb_items_category') ){
			require_once (get_stylesheet_directory() . '/inc/Templates/lastenradTemplate.php');
    	cb_acfprintlara();
	}
	elseif ( has_term('anhaenger','cb_items_category') ) {
		require_once (get_stylesheet_directory() . '/inc/Templates/anhaengerTemplate.php');
		cb_acfprinttrailer();
	}
	elseif ( has_term('zubehoer','cb_items_category') ) {
		require_once (get_stylesheet_directory() . '/inc/Templates/zubehorTemplate.php');
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
