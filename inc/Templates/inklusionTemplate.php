<?php
/*---------------------------------------------------------------------------------
 * START Print Inklusionsraeder (80% done)
 * -------------------------------------------------------------------------------
 * To-Do:
 * -Mehr Details könnten netter sein -> soll sich in der Praxis zeigen
 * ---------------------------------------------------------------------------------
 * START Print Inklusionsraeder
 * ---------------------------------------------------------------------------------
*/

function cb_acfprintinklusion(){
	echo cb_acfgallery();
	the_field( 'inklusion_desc' );
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
 ?>
