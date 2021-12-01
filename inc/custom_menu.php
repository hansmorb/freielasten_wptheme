<?php

/* Sorry für den unsauberen Coden: Beschwerden an mein Assistenten! (Der sie für mich verbrennt).
 * Funktionen: ACF_populate_menu (100% done)
 * 		Erstellt aus den ACF Feldern im "Menü" ein passendes Hauptmenü (der Ausleihen Menüpunkt)
*  get_icon_for_cb_item (100%done)
* 		Gibt das Icon für eine CB Post ID zurück
*       Wird auch von taxonomy-cb_locations_category.php verwendet um Postliste per Location zu erstellen.
 */

/* Input: Post Liste, Farbe des Menüs
* Return <li> Element mit Icon, Name und Link
*/

// Returnt eingebundene und eingefärbte SVG
function returnsvg_html($svg_url,$svg_color){
	return '<div style="display:flex; margin-right:10px;"><svg class="menuicon" data-src='.$svg_url.' fill="'.$svg_color.'" width="2.50em"></svg></div>';
}
/* RETURN: Array mit Term und Icon, dadrunter die Items als Post Object*/

function menu_create_itemlines ($cb_item_postlist,$menucolor) {
	$itemlines_html = '<ul class="sub-menu">';
	foreach ($cb_item_postlist as $cb_item_post) {
		$cb_item_post_ID = $cb_item_post->ID;
		$cb_item_post_title = $cb_item_post->post_title;
		$cb_item_post_permalink = get_permalink($cb_item_post);
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
		$setting_ShowBookable =  boolval(get_field('show_bookableonly',$menu)); //bool ob buchbare gezeigt werden sollen
		$setting_SortByLocation = boolval(get_field('artikelstandort_anzeigen',$menu)); //bool ob standort berücksichtigt wird



		// parentItem_vars
		$parentItem_logo = get_field('icon_ausleihemenue', $menu); //das Icon vor "Ausleihen"
		$parentItem_name = get_field( 'name_ausleihmenu', $menu); //der Titel des "Ausleihen" Menüs
		$parentItem_url = get_field( 'dazugehorige_seite_ausleihmenu', $menu ); //die URL der Hauptausleihseite
		$parentItem_logo_html = returnsvg_html($parentItem_logo["url"],$setting_menucolor);
		$parentItem_html = '<li class="menu_ausleihen menu-item menu-item-has-children"><a href="'.esc_url($parentItem_url).'">'.$parentItem_logo_html.$parentItem_name.$html_caret.'</a>';


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

				$subMenu_itemlist = get_cb_items_by_category_and_location($subMenu_tax_slug,$setting_ShowBookable); //Checkt erstmal nur, ob für die Kategorie auch Items da sind um welche anzuzeigen
				if ($subMenu_itemlist){ //Nur Element hinzufügen wenn es auch items in der Kategorie gibt
					$subMenu_html .= '<li class="menu-item-has-children"> <a href="'.$subMenu_linkedPage.'">'. $subMenu_ico_html .'<span> '.$subMenu_name.'</span>' . $sub_html_caret . '</a>'; //Menüpunkt für Kategorie hinzufügen
					if ($setting_SortByLocation) {
						$subMenu_html .= '<ul class="sub-menu">';
						while( have_rows('standortkategorien',$menu) ) : the_row(); //Iteriert durch sämtliche Standorte für eine Kategorie

						  //vars
							$itemLocation_tax = get_sub_field('standort_cat');
							$itemLocation_tax_slug = get_term($itemLocation_tax)->slug;
							$itemLocation_tax_name = get_term($itemLocation_tax)->name;
							$itemLocation_tax_url = get_term_link(get_term($itemLocation_tax)) . '?itemcat=' . $subMenu_tax; //Fügt zusätzliche Variable hinzu, wird von taxonomy-cb_locations_category.php abgerufen

							$itemLocation_ico = get_sub_field('standort_icon');
							$itemLocation_itemList = get_cb_items_by_category_and_location($subMenu_tax_slug, $setting_ShowBookable, $itemLocation_tax_slug);
							$itemLocation_html = '';
							if ($itemLocation_itemList) {
								$itemLocation_html = '<li class="menu-item-has-children"> <a href="'.$itemLocation_tax_url.'">'.$itemLocation_ico.'<span> '.$itemLocation_tax_name. '</span>' . $sub_html_caret . '</a>';
								$itemLocation_html .= menu_create_itemlines($itemLocation_itemList,$setting_menucolor);
								$itemLocation_html .= '</li>'; //Liste für Submenü Location schließen + Listenpunkt schließen
							}
							$subMenu_html .= $itemLocation_html; //Item Location code appenden
						endwhile; // Ende iterieren durch Standorte für Kategorie
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
			// append html
			$items = $parentItem_html . $parentItem_css .$items; //HTML Elemente zusammenführen -> wird returnt
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

		//debug

		echo "<pre>";
		print_r($item);
		echo "</pre>";

		// prepend icon
		if( $icon ) {

			$item->title = $item->title .  ' <i class="fa fa-'.$icon.'"></i>';

		}

	}


	// return
	return $items;

}

add_filter('wp_nav_menu_objects', 'menuobject_coloredicons', 10, 2);


?>
