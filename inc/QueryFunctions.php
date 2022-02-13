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
 * DIESE FUNKTION SOLL ERSETZT WERDEN DURCH filter_by_kupplung (sobald ich das implementiert kriege)
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
function get_cb_items_by_category($cb_category='',$bookableCheck=True){
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
	return $items_list;
}

/* Filtert sämtliche Artikel heraus die nicht in der entsprechenden Location Kategorie sind*/
function filterPostsByLocation($post_list, $locationcat_slug){
	if ($post_list && $locationcat_slug){
		foreach ($post_list as $key => $item){
			if (!cb_item_isItemInLocCat($item->ID,$locationcat_slug)){
				unset($post_list[$key]);
			}
		}
		return $post_list;
	}
	else {
		return false;
	}
}

function itemListAvailabilities($cb_itemlist) {
	$cb_itemlist_availabilities = array();
	foreach ($cb_itemlist as $itemList_item) {
		$cb_itemlist_availabilities[$itemList_item->ID] = itemGetCalendarData($itemList_item->ID) ;
	}
	return $cb_itemlist_availabilities;
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

function cb_item_isItemInLocCat($cb_item_postID,$cb_location_loccat_slug)
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
	$locationId = \CommonsBooking\Repository\Location::getByItem( $cb_item_id, true )[0]->ID;
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
	$startDay = new Day( $today);
	$endDay = new Day ( $last_day);
	$calendarData = \CommonsBooking\View\Calendar::prepareJsonResponse(
		$startDay,
		$endDay,
		[ $locationId ],
		[ $cb_item_id] 
	);
	return $calendarData;
}

//returns next available day for cb_item, returns day element
function getNextAvailableDay($cb_item,$cb_item_availability){
	$calendarData = $cb_item_availability;
	$last_day = $calendarData['endDate'];
	$date  = new DateTime();
	$today = $date->format( "Y-m-d" );
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
			continue;
		} elseif ( $data['holiday'] ) {
			//echo $cb_item->ID . " holiday";
			continue;
		} elseif ( $data['locked'] ) {
			if ( $data['firstSlotBooked'] && $data['lastSlotBooked'] ) {
				continue;
		} elseif ( $data['partiallyBookedDay'] ) {
				continue;
		}
		} else {
			return $day;
		}
	}
	return "2500-1-1"; //gibt sehr spätes Datum zurück wenn nix verfügbar ist
}

function sortItemsByAvailability($cb_items,$cb_items_availabilities){
	usort($cb_items, function($a,$b) use ($cb_items_availabilities){
		$a_item_availability = $cb_items_availabilities[$a->ID];
		$b_item_availability = $cb_items_availabilities[$b->ID];
		$a_time = strtotime(getNextAvailableDay($a,$a_item_availability));
		$b_time = strtotime(getNextAvailableDay($b,$b_item_availability));
		if ($a_time > $b_time) {
			return 1;
		}
		elseif ($b_time > $a_time) {
			return -1;
		}
		else { //randomizes items when time is equal (prevents same items from always showing in front)
			return rand(0, 1);
		}
		//return strtotime(getNextAvailableDay($a)) <=> strtotime(getNextAvailableDay($b));
	});
	return $cb_items;
}
?>
