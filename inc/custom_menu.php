<?php

/* Sorry für den unsauberen Coden: Beschwerden an mein Assistenten! (Der sie für mich verbrennt).
 * Funktionen: ACF_populate_menu (100% done)
 * 		Erstellt aus den ACF Feldern im "Menü" ein passendes Hauptmenü (der Ausleihen Menüpunkt)
 */

/* Input: Post Liste, Farbe des Menüs
* Return <li> Element mit Icon, Name und Link
*/

// Returnt eingebundene und eingefärbte SVG
function returnsvg_html($svg_url,$svg_color){
	return '<div style="display:flex;"><svg class="menuicon" data-src='.$svg_url.' fill="'.$svg_color.'"></svg></div>';
}
/* RETURN: Array mit Term und Icon, dadrunter die Items als Post Object*/

function menu_create_itemlines ($cb_item_postlist,$menucolor) {
	$itemlines_html = '<ul class="sub-menu">';
	foreach ($cb_item_postlist as $cb_item_post) {
		$cb_item_post_ID = $cb_item_post->ID;
		$cb_item_post_title = $cb_item_post->post_title;
		$cb_item_post_permalink = get_permalink($cb_item_post_ID);
		$cb_item_post_ico = get_field("icon",$cb_item_post_ID);
		$cb_item_post_ico_html = returnsvg_html($cb_item_post_ico,$menucolor);

		$itemlines_html .= '<li class="menu-item"> <a href="'.$cb_item_post_permalink.'">'. $cb_item_post_ico_html .'<span> '.$cb_item_post_title.'</span></a></li>';
	}
	$itemlines_html .= '</ul>';
	return $itemlines_html;
}

function acf_populate_menu($items, $args){
	// get menu
	$menu = wp_get_nav_menu_object($args->menu);
	// modify primary only
	if( $args->theme_location == 'primary' ) {
		//general vars

		$html_caret = '<div class="caret-wrap 1" tabindex="0"><span class="caret"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M207.029 381.476L12.686 187.132c-9.373-9.373-9.373-24.569 0-33.941l22.667-22.667c9.357-9.357 24.522-9.375 33.901-.04L224 284.505l154.745-154.021c9.379-9.335 24.544-9.317 33.901.04l22.667 22.667c9.373 9.373 9.373 24.569 0 33.941L240.971 381.476c-9.373 9.372-24.569 9.372-33.942 0z"></path></svg></span></div>';
		$sub_html_caret = '<div class="caret-wrap 2" tabindex="0"><span class="caret"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M207.029 381.476L12.686 187.132c-9.373-9.373-9.373-24.569 0-33.941l22.667-22.667c9.357-9.357 24.522-9.375 33.901-.04L224 284.505l154.745-154.021c9.379-9.335 24.544-9.317 33.901.04l22.667 22.667c9.373 9.373 9.373 24.569 0 33.941L240.971 381.476c-9.373 9.372-24.569 9.372-33.942 0z"></path></svg></span></div>'; //das caret Element für die Untermenüs. Keinen Plan wieso aber die div class ist anders, geht bestimmt eleganter zu lösen
		$setting_menucolor = get_field('menu_color', $menu);
		$setting_fallbackloctext = get_field('fallback_standort', $menu);
		$setting_fallbacktextico = get_field('fallback_icon', $menu);


		// parentItem_vars
		$parentItem_logo = get_field('icon_ausleihemenue', $menu); //das Icon vor "Ausleihen"
		$parentItem_name = get_field( 'name_ausleihmenu', $menu); //der Titel des "Ausleihen" Menüs
		$parentItem_url = get_field( 'dazugehorige_seite_ausleihmenu', $menu ); //die URL der Hauptausleihseite
		$parentItem_logo_html = returnsvg_html($parentItem_logo["url"],$setting_menucolor);
		$parentItem_html = '<li class="menu_ausleihen menu-item menu-item-has-children">';
		$parentItem_html .= '<a href="'.esc_url($parentItem_url).'">';
			$parentItem_html .= '<span class="menu-item-title-wrap dd-title">';
				$parentItem_html .= $parentItem_logo_html.$parentItem_name;
				$parentItem_html .= '</span>';
				$parentItem_html .= $html_caret;
		$parentItem_html .= '</a>';


		$subMenu_html = '<ul class="sub-menu">';
		if (have_rows('kategorien_fur_die_ausleihe',$menu)){ //Da sollten eigentlich immer Kategorien drin sein(deshalb auch kein else)
			while( have_rows('kategorien_fur_die_ausleihe',$menu) ) : the_row(); //Iteratet durch die definierten Kategorien (Repeater Feld)
				//vars aus dem Repeater Feld
				$subMenu_tax = get_sub_field('kategorie'); //Taxonomie ID von der definierten Kategorie (die aufgefüllt werden soll)
				$subMenu_tax_slug = get_term($subMenu_tax)->slug; // Wandelt die tax id in den Slug um (zur Übergabe an meine FUnktionen)
				$subMenu_ico = get_sub_field('cat_ico'); //Icon für die entsprechende Kategorie
				$subMenu_ico_url = $subMenu_ico['url']; //Wandelt Icon Element in URL um
				$subMenu_ico_html = returnsvg_html($subMenu_ico_url,$setting_menucolor);
				$subMenu_name = get_sub_field('cat_name'); //Klarname der entsprechenden Kategorie
				$subMenu_linkedPage = get_sub_field('linkingpage'); //Seite über die Kategorie
				$subMenu_bookable_only = boolval(get_sub_field('bookable_only'));
				$subMenu_sortbylocation = boolval(get_sub_field('display_location'));
				$subMenu_itemlist = get_cb_items_by_category($subMenu_tax_slug,$subMenu_bookable_only); //Checkt erstmal nur, ob für die Kategorie auch Items da sind um welche anzuzeigen
				if ($subMenu_itemlist){ //Nur Element hinzufügen wenn es auch items in der Kategorie gibt
					$subMenu_html .= '<li class="menu-item-has-children"> <a href="'.$subMenu_linkedPage.'">'. $subMenu_ico_html .'<span> '.$subMenu_name.'</span>' . $sub_html_caret . '</a>'; //Menüpunkt für Kategorie hinzufügen
					if ($subMenu_sortbylocation) {
						$subMenu_html .= '<ul class="sub-menu">';
						while( have_rows('standortkategorien',$menu) ) : the_row(); //Iteriert durch sämtliche Standorte für eine Kategorie

						  //vars
							$itemLocation_tax = get_sub_field('standort_cat');
							$itemLocation_tax_slug = get_term($itemLocation_tax)->slug;
							$itemLocation_tax_name = get_term($itemLocation_tax)->name;
							$itemLocation_tax_url = get_term_link(get_term($itemLocation_tax)) . '?itemcat=' . $subMenu_tax; //Fügt zusätzliche Variable hinzu, wird von taxonomy-cb_locations_category.php abgerufen

							$itemLocation_ico = get_sub_field('standort_icon');
							$itemLocation_itemList = filterPostsByLocationCategory($subMenu_itemlist, $itemLocation_tax_slug);
							$itemLocation_html = '';
							if ($itemLocation_itemList) {
								$itemLocation_html = '<li class="menu-item-has-children"> <a href="'.$itemLocation_tax_url.'">'.$itemLocation_ico.'<span> '.$itemLocation_tax_name. '</span>' . $sub_html_caret . '</a>';
								$itemLocation_html .= menu_create_itemlines($itemLocation_itemList,$setting_menucolor);
								$itemLocation_html .= '</li>'; //Liste für Submenü Location schließen + Listenpunkt schließen
							}
							$subMenu_html .= $itemLocation_html; //Item Location code appenden
							if ($subMenu_itemlist){ //Wenn überhaupt noch Elemente in der Itemlist sind
								$subMenu_itemlist = array_udiff($subMenu_itemlist,$itemLocation_itemList, function($obj_a, $obj_b){ return $obj_a->ID - $obj_b->ID;}); //Entfernt die schon eingetragenen Arrays aus der Itemlist
							}
						endwhile; // Ende iterieren durch Standorte für Kategorie
						if ($subMenu_itemlist){ //falls jetzt noch items übrig sein sollten werden die in den fallbackpunkt eingeordnet
							$subMenu_html .= '<li class="menu-item-has-children"><a>' . $setting_fallbacktextico . '<span> '.$setting_fallbackloctext . '</span>' . $sub_html_caret . '</a>';
							$subMenu_html .= menu_create_itemlines($subMenu_itemlist,$setting_menucolor);
							$subMenu_html .= '</li>';
						}
						$subMenu_html .= '</ul>';
					}

					else {
						$subMenu_html .= menu_create_itemlines($subMenu_itemlist,$setting_menucolor);
					}
					$subMenu_html .= '</li>'; //Listenpunkt für jeweilige Kategorie schließen
				} //Ende If sub_itemlist hat Items
			endwhile; //Ende iterieren durch Kategorien
			$parentItem_html .= $subMenu_html; //Fügt SubItems dem ParentItem hinzu
			$parentItem_html .= '</ul>';	//Gesamten Ausleihen Unterpunkt schließen
			$parentItem_html .= '</li>' ; //Schließt Listenpunkt vom parent Item
			$parentItem_css = '<style> .menu_ausleihen a{color:'.$setting_menucolor.'!important;}.menu_ausleihen a::after{ background-color: '.$setting_menucolor.'!important; }</style>'; //Fügt einfach so ultra random den Style Ende an und macht sich auch noch selber super important, ähnlich wie der Sack der das geschrieben hat, you are welcome!
			//change color of menu items
			$menu_css = color_menu_items($items);
			// append html
			$items = $parentItem_html . $parentItem_css .$items . $menu_css; //HTML Elemente zusammenführen -> wird returnt
		} //endif have_rows kategorien fuer ausleihe
	// return
	return $items;

	}
}

add_filter('wp_nav_menu_items', 'acf_populate_menu', 10, 2);


function menuobject_icons( $items, $args ) {

	// loop
	foreach( $items as &$item ) {

		// vars
		$icon = get_field('icon-menu_obj', $item);

		// prepend icon
		if( $icon ) {

			$item->title = ' <i class="fa fa-'.$icon.' menuicon"></i>' . $item->title;

		}

	}


	// return
	return $items;

}

add_filter('wp_nav_menu_objects', 'menuobject_icons', 10, 2);

function color_menu_items($items) {
    preg_match_all('/menu-item-([0-9]{1,10})/ ', $items, $matches);
		$st = '';
		$nav_menu = '#nv-primary-navigation-top';
		if (wp_is_mobile()){
			$nav_menu = '#nv-primary-navigation-sidebar';
		}
    if (isset($matches[0]) && isset($matches[1])) {
				$st = '<style type="text/css">';
        foreach ($matches[0] as $k => $repl) {
            $post_id = $matches[1][$k];
						if($text_color = get_field('color-menu_obj', $post_id)){
                $st .= $nav_menu . ' li.' . $repl . ' a { color:' . $text_color . ';}';
								$st .= $nav_menu . ' li.' . $repl . ' a::after { background-color:' . $text_color . ';}';
        		}
    		}
				$st .= $nav_menu . ' .menuicon {
								max-height: 50%;
  							width: 50px;
  							margin: 5px;
								}';
				$st .= '</style>';
		}
    return $st;
}

//changes default used ACF Palettes to freielasten palette

function set_acf_color_picker_default_palettes() {
?>
<script>
let setDefaultPalette = function() {
    acf.add_filter('color_picker_args', function( args, $field ){

        // Find the field key
        let targetFieldKey = $field[0]['dataset']['key'];

        args.palettes = [ '#3155a1', '#801622', '#00848b', '#009fe3', '#ffcb1d', '#79b50d', '#000000', '#ffffff' ];

        // Return
        return args;
    });
}
setDefaultPalette();
</script>
<?php
}
add_action('acf/input/admin_footer', 'set_acf_color_picker_default_palettes');


?>
