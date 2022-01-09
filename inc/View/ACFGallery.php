<?php

function enqueue_gallery_styles(){
	wp_register_style('gallery-css', get_stylesheet_directory_uri() . '/inc/View/css/gallery.css', __FILE__);
	wp_enqueue_style('gallery-css');
	wp_enqueue_script('gallery-js', get_stylesheet_directory_uri() . '/inc/View/js/gallery.js');
}

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

 ?>
