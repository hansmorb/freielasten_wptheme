<?php

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
 * START Create PostGrid from Posts (90% fertig)
 * TODO: Design verbessern
 * -------------------------------------------------------------------------------
 * Funktion erstellt aus einer get_posts Array eine responsive postgrid mithilfe der postgrid.css, Design basierend auf https://codepen.io/drralte/pen/NWxyezz.
 * Returnt $print
 * To-Do:
 * Schöner machen
 * -------------------------------------------------------
*/

function create_postgrid_from_posts($items) {
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
					$print .= '<div class="card">';
						$print .= '<div class="card__image">';
							$print .= '<img src="'.esc_url($itemThumbnailURL).'" alt="'.$item_title.'">';
							$print .= '<div class="card__overlay card__overlay--blue">';
								$print .= '<div class="card__overlay-content">';
									$print .= '<a href="'.$item_permalink.'" class="card__title">'.$item_title.'</a>';
									$print .= '<ul class="card__meta card__meta--last">';
										$print .= '<li><a href="'.$item_permalink.'"><i class="fas fa-map-marker"></i>'.$itemLocAddress.'</a></li>';
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
