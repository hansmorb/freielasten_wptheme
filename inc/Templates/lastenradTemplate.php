<?php

function cb_acfprintlara(){

$poweredby = get_field( 'bereitgestellt_von' );
if ($poweredby) { echo '<i> Bereitgestellt von '. $poweredby . '</i>'; } 

$foerderlogo = get_field( 'foerderlogo');
if ($foerderlogo) {
	echo "<br> Gefördert von: ";
	foreach ($foerderlogo as $logo) {
		echo '<img src="' . esc_url($logo['sizes']['thumbnail']) . '" title="' . esc_html($logo['caption']) . '" style="width:50px">';
	}
}
?>
<p>
<?php echo cb_acfgallery('galerie'); ?>
</p>
<br>
<div class="cb_item-top-infos">
<h3>
	<ul style="list-style-type:none;">

		<li>
			<i class="fas fa-tachometer-alt"></i>
			<?php if ( get_field( 'elektrounterstuetzung' ) == 1 ) : ?>
			Elektrounterstützung bis <?php the_field( 'unterstutzung_bis_kmh' ); ?> km/h
			<?php else : ?>
				Keine Elektrounterstützung
			<?php endif; ?>
		</li>

		<li>
			<i class="fas fa-weight"></i>
			Zuladung: <?php the_field( 'zuladung' ); ?> kg
		</li>

		<?php if( get_field('besonderheiten') ): ?>
			<li>
				<i class="fas fa-exclamation-circle"></i>
				<?php the_field('besonderheiten'); ?>
			</li>
		<?php endif; ?>

		<?php if(  get_field('extrainfo') ): ?>
			<li>
				<i class="fas fa-info-circle"></i>
				<?php the_field( 'extrainfo' ); ?>
			</li>
		<?php endif; ?>

	</ul>

</h3>

</div> <!-- cb_item-top-infos -->

<?php enqueue_accordion_styles(); ?> <!-- Start der Akkordion Elemente -->
<button class="accordion"><b><i class="fas fa-cogs"></i> Technische Infos</b> </button>
<div class="panel">
	<p>
	<ul style="list-style-type:none;">

		<li>
			<?php if ( get_field('spuren') == 'zweirad' ): ?>
			<i class="fas fa-info-circle"></i> <b>Zweispuriges</b> Lastenrad
			<?php else: ?>
			<i class="fas fa-info-circle"></i> <b>Dreispuriges</b> Lastenrad
			<?php endif; ?>
		</li>

		<?php if(  get_field('motortyp') ): ?>
			<li>
				<i class="fas fa-bolt"></i> <b>Motortyp:</b> <?php the_field( 'motortyp' ); ?>
			</li>
		<?php endif; ?>

		<?php if(  get_field('akkugroesse') ): ?>
			<li>
				<i class="fas fa-battery-full"></i> <b>Akkukapazität:</b> <?php the_field( 'akkugroesse' ); ?> wH
			</li>
		<?php endif; ?>

		<?php if(  get_field('ersatzakkus') && get_field('ersatzakkus') >= 1 ): ?>
			<li>
				<i class="fas fa-battery-full"></i> <b>Verfügbare Ersatzakkus: </b> <?php the_field( 'ersatzakkus' ); ?>
			</li>
		<?php endif; ?>

		<?php if(  get_field('schaltung') ): ?>
			<li>
				<i class="fas fa-cogs"></i> <b>Schaltungstyp:</b> <?php the_field( 'schaltung' ); ?>
			</li>
		<?php endif; ?>

		<?php if(  get_field('gesamtbelastung') ): ?>
			<li>
				<i class="fas fa-weight"></i> <b>Maximale Belastung (inkl. Fahrer*in):</b> <?php the_field( 'gesamtbelastung' ); ?> kg
			</li>
		<?php endif; ?>
		<li>

		<?php if(  get_field('leergewicht') ): ?>
			<li>
				<i class="fas fa-weight"></i> <b>Leergewicht:</b> <?php the_field( 'leergewicht' ); ?> kg
			</li>
		<?php endif; ?>
		<li>
			<i class="fas fa-arrows-alt-v"></i>
			<?php if ( get_field( 'sattelstange_verstellbar' ) == 1 ) : ?>
			Sattelstange verstellbar
			<?php else : ?>
			Sattelstange <b>nicht</b> verstellbar
			<?php endif; ?>
		</li>
		<li>
			<i class="fas fa-arrows-alt-v"></i>
			<?php if ( get_field( 'lenkerstange_verstellbar' ) == 1 ) : ?>
				 Lenkerstange verstellbar
			<?php else : ?>
			Lenkerstange <b>nicht</b> verstellbar
			<?php endif; ?>
		</li>
		<?php if(  get_field('breite') ): ?>
			<li>
				<i class="fas fa-ruler"></i> <b>Breite (Fahrrad):</b> <?php the_field( 'breite' ); ?> cm
			</li>
		<?php endif; ?>
		<?php if(  get_field('breite_velo') ): ?>
			<li>
				<i class="fas fa-ruler"></i> <b>Breite (Fahrrad):</b> <?php the_field( 'breite_velo' ); ?> cm
			</li>
		<?php endif; ?>
		<?php if(  get_field('laenge_velo') ): ?>
			<li>
				<i class="fas fa-ruler-vertical"></i> <b>Länge (Fahrrad):</b> <?php the_field( 'laenge_velo' ); ?> cm
			</li>
		<?php endif; ?>
		<?php if(  get_field('stext_masse') ): ?>
			<li>
				<i class="fas fa-ruler-combined"></i> <?php the_field( 'stext_masse' ); ?>
			</li>
		<?php endif; ?>
		<?php if(  get_field('ladeflache') ): ?>
			<li>
				<i class="fas fa-truck-loading"></i> <b> Ladefläche: </b><?php the_field( 'ladeflache' ); ?>
			</li>
		<?php endif; ?>
		<?php if(  get_field('modell') ): ?>
			<li>
				<i class="far fa-sticky-note"></i>
				<b> Modell: </b><?php the_field( 'modell' ); ?>
			</li>
		<?php endif; ?>
		<?php if(  get_field('redmine_link') ): ?>
			<li>
				<i class="fas fa-info-circle"></i>
				<a href="<?php the_field( 'redmine_link' ); ?>" target="_blank">Internes</a>
			</li>
		<?php endif; ?>
	</ul>
	</p>
</div>
<!-- Start der Zubehör und Kupplungen Accordions, zeigt nur values an wenn Zubehör oder Kupplungen gefunden wurden -->
<?php $kupplungen_checked_values = get_field( 'kupplungen' );
if ( $kupplungen_checked_values ) :
$postgrid_items = get_post_by_category_and_kupplung('anhaenger',$kupplungen_checked_values);
$currentItem = get_post();
$currentLoc = \CommonsBooking\Repository\Location::getByItem( $currentItem->ID, true );
$currentLoc = reset($currentLoc);
$splitResults = splitItemsByLoc($postgrid_items,$currentLoc->ID);
$sameLoc = $splitResults["sameLoc"];
$otherLocs = $splitResults["otherLocs"];
if (! empty($sameLoc) ) {
    $postGridSameLoc = create_postgrid_from_posts($sameLoc,itemListAvailabilities($sameLoc), ! wp_is_mobile() ); //Zeigt PostMeta by default an, wenn die mobile Seite aktiv ist
}
$postGridOtherLocs = create_postgrid_from_posts($otherLocs,itemListAvailabilities($otherLocs),(wp_is_mobile() ? False : True )); //Zeigt PostMeta by default an, wenn die mobile Seite aktiv ist
if ($postGridOtherLocs || $postGridSameLoc ) {?>
<button class="accordion"><b><i class="fas fa-link"></i> Passende Anhänger</b> </button>
<div class="panel">
    <?php if (! empty ($postGridSameLoc) ) { ?>
    <p>
        <h3>
            Anhänger am selben Standort: <br>
        </h3>
    </p>
        <?php
        echo $postGridSameLoc; } ?>
		<?php if ($postGridOtherLocs) { ?>
        <p>
				<h3>
					Anhänger an anderen Standorten: <br>
				</h3>
				<?php
				echo $postGridOtherLocs; ?>
	</p>
            <?php } ?>
</div>
<?php
  } endif;
}
/*-------------------------------------------------------------------------------
 * ENDE Print ACF Fields Lastenrad
 * -------------------------------------------------------------------------------
 * Outputtet die ACF Felder für Lastenräder
 * ---------------------------------------------------------------------------------
 * ENDE Print ACF Fields Lastenrad
 * ---------------------------------------------------------------------------------
*/
?>
