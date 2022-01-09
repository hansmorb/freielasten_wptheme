<?php


function enqueue_itemGallery_styles(){
  wp_register_style('itemgallery-css', get_stylesheet_directory_uri() . '/inc/View/css/itemGallery.css', __FILE__);
  wp_enqueue_style('itemgallery-css');
  wp_register_script('itemgallery-js', get_stylesheet_directory_uri() . '/inc/View/js/itemGallery.js',array(),false,true); //enqueue script in footer
  wp_enqueue_script('itemgallery-js');
}

/*-------------------------------------------------------------------------------
 * START Create ItemGallery (100% fertig)
 * -------------------------------------------------------------------------------
 * Funktion erstellt aus einer get_posts Array eine responsive gallery
 * mit Standort und Verfügbarkeit
 * -------------------------------------------------------
 * Code for Card Meta based on "Responsive post grid with Masonry" by maximsv (https://codepen.io/maximsv/pen/MbaNMz)
 * Copyright (c) 2022 by maximsv (https://codepen.io/maximsv/pen/MbaNMz)
 * Fork of an original work Responsive post grid with Masonry (https://codepen.io/maximelafarie/pen/bVXMBR

 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 */

function cb_itemGallery($items,$galleryNo=0,$hideCardMeta=True,$css_class=''){
  require_once(get_stylesheet_directory() . '/inc/View/itemAvailability.php');
	enqueue_itemGallery_styles();
	$class = 'gallery' . $galleryNo;
	$cardMeta_class = 'card__meta card__meta--last';
	if ($hideCardMeta){
		$cardMeta_class = 'card__meta card__meta--last card__hidden';
	}
	if( $items ):
		$print = '<div class="slideshow-container'.$css_class.'">';
		foreach( $items as $item ):
			$itemID = $item->ID;
			$item_title = $item->post_title;
			$item_permalink = get_permalink($itemID);
			$itemThumbnailURL = get_the_post_thumbnail_url($itemID,'thumbnail');
			$itemLocAddress = cb_item_locAdress($itemID);
			$print .= '<div class="'.$class.' fade">';
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
		$print .= '<a class="prev" onclick="plusSlides(-1, ' . $galleryNo . ')">&#10094;</a>';
		$print .= '<a class="next" onclick="plusSlides(1, ' . $galleryNo . ')">&#10095;</a>';
		$print .= '</div>';
		$print .= '<div style="text-align:center">';
			for($i = 1; $i < count($items) + 1; $i++):
				$print .= '<span class="dots dot'.$galleryNo.'" onclick="currentSlide(' . $i . ',' . $galleryNo .')"></span>';
			endfor;
	endif;
	$print .= '</div>'; //slideshow-container
	$print .= '<style>';
	$print .= '.' . $class . '{display: none}';
	$print .= '</style>';
	return $print;
}

/*-------------------------------------------------------------------------------
 * ENDE Create ItemGallery
 * -------------------------------------------------------------------------------
 * Funktion erstellt aus einer get_posts Array eine responsive gallery
 * mit Standort und Verfügbarkeit
 * -------------------------------------------------------
*/
 ?>
