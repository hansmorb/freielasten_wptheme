<?php
use CommonsBooking\CB\CB;
use CommonsBooking\Model\CustomPost;
use CommonsBooking\Model\Day;
use CommonsBooking\Model\Week;
use CommonsBooking\Plugin;
use CommonsBooking\Wordpress\CustomPostType\Item;
use CommonsBooking\Wordpress\CustomPostType\Location;
use CommonsBooking\Wordpress\CustomPostType\Timeframe;


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

function itemGetCalendarData($cb_item,$days=7){
	if (!is_object($cb_item)) {
		$cb_item = get_post($cb_item);
	}
	$cb_item_id = $cb_item->ID;
	$locationId = \CommonsBooking\Repository\Location::getByItem( $cb_item_id, true )[0];
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
	/*
	echo "<pre>";
	print_r($calendarData);
	echo "</pre>";
	*/
	return [$calendarData,$last_day];
}


function shortcode_postGridfromCategory($atts){
	$atts = shortcode_atts( array(
		'itemcat' => '',
	  'locationcat' => '',
		'hidedefault' => 'false'
	),$atts);
	$atts['hidedefault'] = filter_var( $atts['hidedefault'], FILTER_VALIDATE_BOOLEAN );
	$itemList = get_cb_items_by_category_and_location($atts['itemcat'],True,$atts['locationcat']);
	if ($itemList){
		return create_postgrid_from_posts($itemList,$atts['hidedefault']); //Hide Default not working, that's why its to always true
	}
	else {
		return "no posts found";
	}
}

add_shortcode( 'cb_postgrid', 'shortcode_postGridfromCategory' );

$galleryIterator = 0;

function shortcode_itemGalleryfromCategory($atts){
	global $galleryIterator;
	$atts = shortcode_atts( array(
		'itemcat' => '',
	  'locationcat' => '',
		'hidedefault' => 'true'
	),$atts);
	$atts['hidedefault'] = filter_var( $atts['hidedefault'], FILTER_VALIDATE_BOOLEAN );
	$itemList = get_cb_items_by_category_and_location($atts['itemcat'],True,$atts['locationcat']);
	if ($itemList){
		$gallery_html = cb_itemGallery($itemList,$galleryIterator,$atts['hidedefault']);
		$galleryIterator = $galleryIterator + 1;
		?>
		<script>
		var galleryIterator = <?php echo json_encode($galleryIterator); ?>;
		</script>
		<?php
		return $gallery_html;
	}
	else {
		return "no posts found";
	}
}

add_shortcode( 'cb_itemgallery', 'shortcode_itemGalleryfromCategory' );
?>
