<?php
/*
* Aktuelle Shortcodes:
* [cb_postgrid itemcat,locationcat,hidedefault=False]
*
* [cb_itemgallery itemcat,locationcat,hideDefault=True]
*
*/


function shortcode_postGridfromCategory($atts){
	$atts = shortcode_atts( array(
		'itemcat' => '',
	  'locationcat' => '',
		'hidedefault' => 'false'
	),$atts);
	$atts['hidedefault'] = filter_var( $atts['hidedefault'], FILTER_VALIDATE_BOOLEAN );
	$itemList = get_cb_items_by_category_and_location($atts['itemcat'],True,$atts['locationcat'],True);
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
	$itemList = get_cb_items_by_category_and_location($atts['itemcat'],True,$atts['locationcat'],True);
	$itemList = sortItemsByAvailability($itemList);
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
