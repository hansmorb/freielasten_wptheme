<?php

use CommonsBooking\Model\Day;


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
	//Generate $meta_queries
	$meta_queries = array(
	'relation' => 'AND',
	$kupplung_queries
	);
	// Generate $args array
	$args = array(
		'category_slug' => $cb_category,
		'meta_query' => $meta_queries,
	);
	$items_list = \CommonsBooking\Repository\Item::get( $args ,$bookableCheck);
	return $items_list;
}


/* ---------------------------------------------------------------------------------
 * ENDE Get Post by Category + Kupplung
 * ---------------------------------------------------------------------------------
*/

/* Return: WP Post object list */
function get_cb_items_by_category($cb_category='',$bookableCheck=True){
	// Generate $args array
	$args = array(
		'orderby'			=> 'title',
		'order'				=> 'ASC',
		'category_slug' => $cb_category,
	);
	$items_list = \CommonsBooking\Repository\Item::get( $args ,$bookableCheck);
	return $items_list;
}

/* Filtert sämtliche Artikel heraus die nicht in der entsprechenden Location Kategorie sind*/
function filterPostsByLocationCategory($post_list, $locationcat_slug){
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

/**
 * Filtert sämtliche Artikel heraus, die nicht am entsprechenden Standort sind
 *
 * @param   \CommonsBooking\Model\Item[]  $items
 * @param          $loc_id
 *
 * @return array [sameLoc, otherLocs]
 */
function splitItemsByLoc(array $items, $loc_id){
	$result = [
		"sameLoc" => [],
		"otherLocs" => []
	];
	if (empty ($items) || empty ($loc_id)) {
		return $result;
	}
	foreach ($items as $key => $item){
		$locations = \CommonsBooking\Repository\Location::getByItem( $item->ID, true );
		$locIDs = array_map(function($location) {
			return $location->ID;
		}, $locations);
		if (in_array($loc_id,$locIDs)){
			$result["sameLoc"][] = $item;
		}
		else {
			$result["otherLocs"][] = $item;
		}
	}
	return $result;
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
	if ( count($locations) && ( reset($locations)->post_type == 'cb_location') ) {
	return has_term($cb_location_loccat_slug,'cb_locations_category',reset($locations)->ID);
	}
	else {
		return false;
	}
}

function cb_item_locAdress($cb_item_postID)
{
	$locations = \CommonsBooking\Repository\Location::getByItem( $cb_item_postID, true );
	if ( count($locations) ) {
		return reset($locations)->formattedAddressOneLine();
	}
	else {
		return false;
	}
}

function itemGetCalendarData($cb_item,$days=7){
	if (!is_object($cb_item)) {
		$cb_item = get_post($cb_item);
	}
	$cb_item_id   = $cb_item->ID;
	$locArray        = \CommonsBooking\Repository\Location::getByItem( $cb_item_id,
		TRUE );
	$locationId   = reset( $locArray )->ID;
	$date         = new DateTime();
	$today        = $date->format( "Y-m-d" );
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
		// Check day state
		if ( ! count( $data['slots'] ) ) {
			continue;
		} elseif ( $data['holiday'] ) {
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
	});
	return $cb_items;
}
?>
