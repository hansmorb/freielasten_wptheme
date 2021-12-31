<?php
/* Adds the meta key "_bookable" to cb_items when they are bookable, called on save post when a new timeframe is created
*/

function cb_items_checkmeta_key( $post_id ) {
    $args = array(
      'post_type' => 'cb_item',
      'numberposts' => -1,
      'order' => 'ASC',
      'orderby' => 'title'
    );
    $items_list = get_posts($args);
    if ($items_list) {
      foreach ($items_list as $cb_item) {
        if (cb_item_isBookable($cb_item->ID)) {
          update_post_meta($cb_item->ID,'_bookable','1');
        }
        else {
          update_post_meta($cb_item->ID,'_bookable','0');
        }
      }
      return;
    }
}
add_action( 'save_post', 'cb_items_checkmeta_key' );

add_action('acfe/fields/button/name=update_bookablemeta', 'cb_items_checkmeta_key', 10, 2); //Adds handler for button in options page

 ?>
