<?php
/*
* Aktuelle Shortcodes:
* [cb_postgrid itemcat,locationcat,hidedefault=False]
*
* [cb_itemgallery itemcat,locationcat,hideDefault=True]
*
*/


function shortcode_postGridfromCategory($atts){
	require_once(get_stylesheet_directory() . '/inc/View/postGrid.php');
	$atts = shortcode_atts( array(
		'itemcat' => '',
	  'locationcat' => '',
		'class' => '',
		'hidedefault' => 'false',
    'sortbyavailability' => 'true',
		'kupplung' => '',
		'mobile' => 'true' //If the element should be shown on mobile
	),$atts);
	$atts['hidedefault'] = filter_var( $atts['hidedefault'], FILTER_VALIDATE_BOOLEAN );
  $atts['sortbyavailability'] = filter_var( $atts['sortbyavailability'], FILTER_VALIDATE_BOOLEAN);
  $atts['mobile'] = filter_var($atts['mobile'], FILTER_VALIDATE_BOOLEAN );
  if (wp_is_mobile() && !$atts['mobile']){
	  return ""; // does not execute when site is on mobile and mobile is disabled
  }

	/* Mies dreckiger Workaround bis es die Funktion filterPostsByKupplung gibt*/
	if ($atts['kupplung'] != '') {
		$itemList = get_post_by_category_and_kupplung($atts['itemcat'],array($atts['kupplung']));
	}
	else {
		$itemList = get_cb_items_by_category($atts['itemcat']);
	}
  if ($atts['locationcat'] != '') {
    $itemList = filterPostsByLocationCategory($itemList, $atts['locationcat']);
  }
  $itemAvailabilities = itemListAvailabilities($itemList);
  if ($atts['sortbyavailability']){
    $itemList = sortItemsByAvailability($itemList,$itemAvailabilities);
  }


	if ($itemList){
		return create_postgrid_from_posts($itemList,$itemAvailabilities,$atts['hidedefault'],$atts['class']);
	}
	else {
		return "no posts found";
	}
}

add_shortcode( 'cb_postgrid', 'shortcode_postGridfromCategory' );

$galleryIterator = 0;

function shortcode_itemGalleryfromCategory($atts){
	require_once(get_stylesheet_directory() . '/inc/View/itemGallery.php');
	global $galleryIterator;
	$atts = shortcode_atts( array(
		'itemcat' => '',
	  'locationcat' => '',
		'class' => '',
		'hidedefault' => 'true',
    'sortbyavailability' => 'true',
	'mobile' => 'true' //If the element should be shown on mobile
	),$atts);
	$atts['hidedefault'] = filter_var( $atts['hidedefault'], FILTER_VALIDATE_BOOLEAN );
  $atts['sortbyavailability'] = filter_var( $atts['sortbyavailability'], FILTER_VALIDATE_BOOLEAN);
  $atts['mobile'] = filter_var($atts['mobile'], FILTER_VALIDATE_BOOLEAN );
  if (wp_is_mobile() && !$atts['mobile']){
	  return ""; // does not execute when site is on mobile and mobile is disabled
  }
	$itemList = get_cb_items_by_category($atts['itemcat']);

  if ($atts['locationcat'] != '') {
    $itemList = filterPostsByLocationCategory($itemList, $atts['locationcat']);
  }
  $itemAvailabilities = itemListAvailabilities($itemList);
  if ($atts['sortbyavailability']){
    $itemList = sortItemsByAvailability($itemList,$itemAvailabilities);
  }

	if ($itemList){
		$gallery_html = cb_itemGallery($itemList,$itemAvailabilities,$galleryIterator,$atts['hidedefault'],$atts['class']);
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

function shortcode_locationCats($atts){
	$atts = shortcode_atts( array(
		'itemcat' => '',
	),$atts);
	$html = '';
	$itemcat_url = '';
	$itemTerms = get_terms(array('taxonomy' => 'cb_locations_category'));
	if ($atts['itemcat'] != ''){
		$itemterm = get_term_by('slug',$atts['itemcat'],'cb_items_category');
		$itemterm_id = $itemterm -> term_id;
		$itemcat_url = '?itemcat=' . $itemterm_id;

		foreach ($itemTerms as $key => $term){
			$itemsForTerm = get_cb_items_by_category($atts['itemcat']); //nimmt alle buchbaren Items der entsprechenden Kategorie
			$itemsForTerm = filterPostsByLocationCategory($itemsForTerm,$term->slug); //entfernt alle Items, die nicht in der Location sind

			if (!$itemsForTerm) { //entfernt alle Terms die nicht items der entsprechenden Kategorie haben
				unset($itemTerms[$key]);
			}

		}
	}

	foreach ($itemTerms as $key => $term) {
		$html .= '<a href="'.esc_url( get_term_link( $term ) . $itemcat_url ).'">' . $term->name . '</a>';
		if ($key != array_key_last($itemTerms)) {
			$html .= ', '; //adds seperator when not last item
		}
	}
	return $html;
}

add_shortcode( 'cb_locationcats', 'shortcode_locationCats' );

function shortcode_shortbook($atts) {
	$atts = shortcode_atts( array(
		'item_id' => '',
        'location_id' => ''
	),$atts);
    $item = get_post($atts['item_id']);
    $now = time();
    $end = new \DateTime();
    $end->modify('+1 year');
    $end = $end->getTimestamp();
	try {
		$bookings = \CommonsBooking\Repository\Booking::getByTimerange( $now,
			$end,
			$atts['location_id'],
			$atts['item_id'],
        [],
        ['confirmed']);
	}
	catch ( Exception $e ) {
		return '<p>Keine Buchungen gefunden</p>';
	}
    if (empty($bookings)){
        return '<p>Keine Buchungen gefunden</p>';
    }
	$html = '<div class="cb-shortbook">';
    $html .= '<table class="cb-shortbook-table">';
    $html .= '<tr>';
    $html .= '<th>Datum</th>';
    $html .= '<th>Tour</th>';
    $html .= '<th>Abholort</th>';
    $html .= '<th>Radelnde</th>';
    $html .= '</tr>';
    $row = '';
    foreach ($bookings as $booking){
        try {
	        $row .= '<tr>';
	        $row .= '<td>' . date('d.m.Y', $booking->getStartDate()) . '</td>';
	        $row .= '<td>' . $booking->getItem()->title() . '</td>';
	        $row .= '<td>' . $booking->getLocation()->post_title . '</td>';
	        $row .= '<td>' . $booking->getUserData()->user_nicename . '</td>';
	        $row .= '</tr>';
        }
        catch ( Exception $e ) {
            $row = '';
        }
        $html .= $row;
    }
    $html .= '</table>';
    $html .= '</div>';
    return $html;

}

add_shortcode( 'cb_shortbook', 'shortcode_shortbook' );


 ?>
