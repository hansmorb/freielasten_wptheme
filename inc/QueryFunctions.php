<?php

/* ---------------------------------------------------------------------------------
 * START Get Post by Category + Kupplung (100%fertig)
 * ---------------------------------------------------------------------------------
 * Diese Funktion gibt eine Post Liste von cb_items (Artikeln) zurück, die in $category enthalten sind und $kupplungen in meta_key kupplungen enthalten haben.
 * Nimmt absichtlich die Child Elemente der Kategorien mit.
 * Verwendung:
 * get_post_by_category_and_kupplung($category,array($kupplungen));
 * Als Array kann praktischerweise einfach das Kupplungsfeld benutzt werden, das passt sich an.
 * Optional kann der meta_key für das Kupplungsfeld verändert werden (Standard: Kupplungen)
 * bookableCheck prüft auch, ob der Meta Key _bookable existiert, der wird automatisch beim Speichern jeglicher Posts gesetzt (CB_Meta_Key.php)
 *
 *---------------------------------------------------------------------------------
 * START Get Post by Category + Kupplung
 * ---------------------------------------------------------------------------------
*/

function get_post_by_category_and_kupplung($cb_category,$kupplungen,$kupplung_meta_key='kupplungen',$bookableCheck=True){
	$tax = 'cb_items_category'; //Die Taxonomie der Kategorien die verwendet werden
	$term = get_term_by('slug', $cb_category, $tax);
	$termChildren = get_term_children($term->term_id, $tax); //Nimmt noch alle Children mit, dass dann bei der Kategorie "Fahrräder" auch alle angezeigt werden die in den Child Kategorien sind
	$kupplung_queries = array(	'relation' => 'OR'); //Alles anzeigen, was den gegebenen Kategorien entspricht
	foreach($kupplungen as $key => $value){
		$kupplung_query = array(
			'key' => $kupplung_meta_key,
			// Serialize the comparison value to be more exact
			'value' => serialize(strval($value)),
			'compare' => 'LIKE',
		);
		array_push($kupplung_queries,$kupplung_query);
	}
	$bookable_query = '';
	if ($bookableCheck){
		$bookable_query = array(
			'key' => '_bookable', //Der Meta Key der in der item_single.php gesetzt wird
			'value' => '1',
			'compare' => '=',
		);
	}
	//Generate $meta_queries
	$meta_queries = array(
	'relation' => 'AND',
	$kupplung_queries,
	$bookable_query,
	);
	// Generate $args array
	$args = array(
		'post_type' => 'cb_item',
		'numberposts' => -1,
		'tax_query' => array(
    	array(
     			'taxonomy' => $tax,
     			'field' => 'slug',
     			'terms' => $cb_category
    		)
		),
		'meta_query' => $meta_queries,
	);
	$items_list = get_posts($args);
	return $items_list;
}


/* ---------------------------------------------------------------------------------
 * ENDE Get Post by Category + Kupplung
 * ---------------------------------------------------------------------------------
*/

/* Return: WP Post object list */
function get_cb_items_by_category_and_location($cb_category='',$bookableCheck=True,$locationcat_slug=''){
	$tax = 'cb_items_category';
	if ($cb_category != '') {
		$term = get_term_by('slug', $cb_category, $tax);
		$termChildren = get_term_children($term->term_id, $tax);
	}
	$bookable_query = '';
	if ($bookableCheck){
		$bookable_query = array(
			'key' => '_bookable',
			'value' => '1', //Nimmt nur Artikel rein die 1 im _bookable meta key haben (wird über CommonsBooking erstellt)
			'compare' => '=',
		);
	}
	//Generate $meta_queries
	$meta_queries = array(
	$bookable_query,
	);
	//Generate $tax_queries
	$tax_query = '';
	if ($cb_category != ''){
		$tax_query =
			array(
				'taxonomy' => $tax,
				'field' => 'slug',
				'terms' => $cb_category
			);
	}
	$tax_queries = array($tax_query,);
	// Generate $args array
	$args = array(
		'post_type' => 'cb_item',
		'numberposts' => -1,
		'order' => 'ASC',
		'orderby' => 'title',
		'tax_query' => $tax_queries,
		'meta_query' => $meta_queries,
	);
	$items_list = get_posts($args);
  if ($locationcat_slug == ''){ //Wenn nicht nach Location gecheckt werden soll übergibt er die Liste einfach so
    return $items_list;
  }
  else {
    foreach ($items_list as $key => $item) {
      if (!cb_item_isItemInLoc($item->ID,$locationcat_slug))
        unset($items_list[$key]);
    }
    return $items_list;
  }
}


function cb_item_isBookable($cb_item_postID){
  $locations = \CommonsBooking\Repository\Location::getByItem( $cb_item_postID, true );
  if ( count($locations) ) {
    return true;
  }
  else {
    return false;
  }
}

function cb_item_isItemInLoc($cb_item_postID,$cb_location_loccat_slug)
{
	$locations = \CommonsBooking\Repository\Location::getByItem( $cb_item_postID, true );
	if ( count($locations) ) {
	return has_term($cb_location_loccat_slug,'cb_locations_category',$locations[0]->ID);
	}
	else {
		return false;
	}
}

function cb_item_locAdress($cb_item_postID)
{
	$locations = \CommonsBooking\Repository\Location::getByItem( $cb_item_postID, true );
	if ( count($locations) ) {
		return $locations[0]->formattedAddressOneLine();
	}
	else {
		return false;
	}
}

function shortcode_postGridfromCategory($atts){
	$atts = shortcode_atts( array(
		'itemcat' => '',
	  'locationcat' => '',
		'hideDefault' => 'false'
	),$atts);
	$atts['hideDefault'] = filter_var( $atts['hideDefault'], FILTER_VALIDATE_BOOLEAN );
	echo $atts['hideDefault'];
	$itemList = get_cb_items_by_category_and_location($atts['itemcat'],True,$atts['locationcat']);
	if ($itemList){
		return create_postgrid_from_posts($itemList,True); //Hide Default not working, that's why its to always true
	}
	else {
		return "no posts found";
	}
}

add_shortcode( 'cb_postgrid', 'shortcode_postGridfromCategory' );

function shortcode_itemGalleryfromCategory($atts){
	$atts = shortcode_atts( array(
		'itemcat' => '',
	  'locationcat' => '',
		'hideDefault' => 'false'
	),$atts);
	$atts['hideDefault'] = filter_var( $atts['hideDefault'], FILTER_VALIDATE_BOOLEAN );
	echo $atts['hideDefault'];
	$itemList = get_cb_items_by_category_and_location($atts['itemcat'],True,$atts['locationcat']);
	if ($itemList){
		return cb_itemGallery($itemList,False);
	}
	else {
		return "no posts found";
	}
}

add_shortcode( 'cb_itemgallery', 'shortcode_itemGalleryfromCategory' );

?>
