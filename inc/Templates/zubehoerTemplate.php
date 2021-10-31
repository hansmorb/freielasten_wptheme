<?php

/*---------------------------------------------------------------------------------
 * START Print Zubehör (70% done)
 * -------------------------------------------------------------------------------
 * To-Do:
 * Schön machen
 * Noch mehr Details vll?
 * ---------------------------------------------------------------------------------
 * START Print Zubehör
 * ---------------------------------------------------------------------------------
*/
function cb_acfprintzubehoer(){
	echo cb_acfgallery();
	the_field( 'zubehoer_desc' );
	$compatible_with = get_field( 'compatible_with' );
	if ( $compatible_with ) {
		$postGrid = create_postgrid_from_posts($compatible_with);
		if ($postGrid != False){
			echo '<br><h3> <i class="fas fa-link"></i> Kompatibel mit:</h3><br>';
			echo $postGrid;
		}
		else {
			echo "Es wurden leider keine kompatiblen Artikel gefunden";
		}
	}
}

/*---------------------------------------------------------------------------------
 * ENDE Print Zubehör
 * -------------------------------------------------------------------------------
*/
 ?>
