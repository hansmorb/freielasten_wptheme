<?php
function cb_acfprinttrailer(){
	$poweredby = get_field( 'bereitgestellt_von' );
	if ($poweredby) { echo '<i> Bereitgestellt von '. $poweredby . '</i>'; }
	$foerderlogo = get_field( 'foerderlogo');
	if ($foerderlogo) {
		echo "<br> Gefördert von: ";
		foreach ($foerderlogo as $logo) {
			echo '<img src="' . esc_url($logo['sizes']['thumbnail']) . '" title="' . esc_html($logo['caption']) . '" style="width:50px">';
		}
	}
	echo cb_acfgallery();
	?>
<div class="cb_item-top-infos">
<h3>
		<ul style="list-style-type:none;">
		<li>
			<i class="fas fa-info-circle"></i>
			<?php the_field( 'anhaenger_desc' ); ?>
		</li>
		<li>
			<i class="fas fa-link"></i>
			<?php
				$kupplungen_checked_values = get_field('kupplungen');
				foreach ($kupplungen_checked_values as $kupplungen_value){
					if ($kupplungen_value == 'weber')
					{ ?>
						<a href="<?php the_field('page_weber','option'); ?>">Weber-Kupplung </a>
					<?php }
					elseif ($kupplungen_value == 'haerry'){ ?>
						<a href="<?php the_field('page_haerry','option'); ?>">Haerry-Kupplung </a>
					<?php }
					elseif ($kupplungen_value == 'other'){
						the_field( 'alt_kupplung' );
					}
					if ($kupplungen_value == 'sensor'){
						echo " mit Carla Cargo Sensorset (für Elektrounterstützung)";
				}
				} ?>
		</li>
		<?php if ( get_field( 'elektrounterstuetzung' ) == 1 ) : ?>
			<li>
				<i class="fas fa-plug"></i>
				<?php echo "<b>Elektrounterstützung</b> im Pedelec Modus (mit unmotorisiertem Zugfahrrad)";?>
			</li>
		<?php endif;?>
		<?php if ( get_field( 'hwagen' ) == 1 ) {
			echo "<li>";
			echo '<i class="fas fa-hand-paper"></i>';
			echo ' Betrieb als ';
			if ( get_field( 'hwagen_elektro') == 1) {echo "<b> elektrischer </b>";}
			echo "<b>Handwagen</b> möglich";
			echo "</li>";
		} ?>
		<?php if(  get_field('extrainfo') ): ?>
			<li>
				<i class="fas fa-info-circle"></i>
				<?php the_field( 'extrainfo' ); ?>
			</li>
		<?php endif; ?>
		<?php if(  get_field('zuladung') ): ?>
			<li>
				<i class="fas fa-weight-hanging"></i>
				<b>Zuladung:</b> <?php the_field( 'zuladung' ); ?> kg
			</li>
		<?php endif; ?>
		<?php $zubehoer = get_field( 'zubehoer' ); ?>
		<?php if ( $zubehoer ) :
				global $post;
				$i = 0;
				$len = count($zubehoer);?>
				<li>
				<i class="fas fa-puzzle-piece"></i>
				Zubehör:
				<?php foreach ( $zubehoer as $current_post ) :
					$post = $current_post;
					setup_postdata( $post ); ?>
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					<?php
					if ($i != $len - 1) {
        				// not last element
        				echo ', ';
				    }

    				$i++; ?>
				<?php endforeach; ?>
				<?php wp_reset_postdata(); ?>
				<?php if(  get_field('text_zubehor') ): ?>
					<br>
					</h3><h6> <?php the_field( 'text_zubehor' ); ?></h6><h3>
				<?php endif; ?>
				</li>
		<?php endif; ?>

	</ul>
</h3>
</div> <!-- cb_item-top-infos -->

<?php enqueue_accordion_styles(); ?>

<button class="accordion"><b><i class="fas fa-bicycle" style="margin-right:5px;"></i> Kompatible Räder</b> </button>
<div class="panel">
	<p>
		<?php
		$kupplungen_checked_values = get_field( 'kupplungen' );
		if ( $kupplungen_checked_values ) :
			$currentItem = get_post();
			$currentLoc = \CommonsBooking\Repository\Location::getByItem( $currentItem->ID, true );
			$currentLoc = reset($currentLoc);
			$postgrid_items = get_post_by_category_and_kupplung('fahrrad',$kupplungen_checked_values);
			$splitResults = splitItemsByLoc($postgrid_items,$currentLoc->ID);
			$sameLoc = $splitResults["sameLoc"];
			$otherLocs = $splitResults["otherLocs"];
			$postGridsameLoc = create_postgrid_from_posts($sameLoc,itemListAvailabilities($sameLoc), ! wp_is_mobile() );
            $postGridotherLocs = create_postgrid_from_posts($otherLocs,itemListAvailabilities($otherLocs), ! wp_is_mobile() );

			if ($postGridsameLoc) { ?>
                <h2>Am selben Standort: </h2>
				<?php
				echo $postGridsameLoc;
			}
			if ( $postGridotherLocs ) { ?>
                <h2>An anderen Standorten: </h2>
                <?php
                echo $postGridotherLocs;
            }
            if ( ! $postGridsameLoc && ! $postGridotherLocs ) {
                echo "Keine kompatiblen Räder gefunden";
            }
		endif;
		?>
	</p>
</div>

<button class="accordion"><b><i class="fas fa-cogs"></i> Technische Infos</b> </button>
<div class="panel">
	<p>
	<?php if (get_field('zuladung') || get_field('hwagen_zuladung') || get_field('leergewicht') || get_field('stext_zuladung')){?>
		<h3>
			<i class="fas fa-weight-hanging"></i> Gewicht und Zuladung: <br>
		</h3>
		<ul style="list-style-type:none;">
			<?php if(  get_field('zuladung') ): ?>
				<li>
					<i class="fas fa-weight-hanging"></i> <b> Maximalzuladung: </b> <?php the_field( 'zuladung' ); ?> kg
				</li>
			<?php endif; ?>

			<?php if(  get_field('hwagen_zuladung') ): ?>
				<li>
					<i class="fas fa-weight-hanging"></i> <b> Maximalzuladung (Handwagen): </b> <?php the_field( 'hwagen_zuladung' ); ?> kg
				</li>
			<?php endif; ?>

			<?php if(  get_field('leergewicht') ): ?>
				<li>
					<i class="fas fa-weight"></i> <b> Leergewicht: </b> <?php the_field( 'leergewicht' ); ?> kg
				</li>
			<?php endif; ?>

			<?php if(  get_field('stext_zuladung') ): ?>
				<li>
					<i class="fas fa-weight"></i> <?php the_field( 'stext_zuladung' ); ?>
				</li>
			<?php endif; ?>
		</ul>
		<br>
	<?php } ?>
		<?php if (get_field('ladeflaeche_laenge') || get_field('laenge_fahrzeug') || get_field('ladevolumen') || get_field('stext_masse') ){?>
		<h3>
			 <i class="fas fa-ruler-combined"></i> Maße: <br>
		</h3>
		<ul style="list-style-type:none;">
			<?php if(  get_field('ladeflaeche_laenge') ): ?>
				<li>
					<i class="fas fa-ruler-vertical"></i> <b> Ladefläche: : </b> <?php the_field( 'ladeflaeche_laenge' ); ?>x<?php the_field( 'ladeflaeche_breite' ); ?>cm (LxB)
				</li>
			<?php endif; ?>

			<?php if(  get_field('laenge_fahrzeug') ): ?>
				<li>
					<i class="fas fa-ruler-horizontal"></i> <b> Fahrzeug: : </b> <?php the_field( 'laenge_fahrzeug' ); ?>x<?php the_field( 'breite_fahrzeug' ); ?>cm (LxB)
				</li>
			<?php endif; ?>

			<?php if(  get_field('ladevolumen') ): ?>
				<li>
					<i class="fas fa-truck-loading"></i> <b>  Ladevolumen: </b> <?php the_field('ladevolumen' ); ?>m³
				</li>
			<?php endif; ?>

			<?php if(  get_field('stext_masse') ): ?>
				<li>
					<i class="fas fa-ruler-combined"></i>
					<?php the_field( 'stext_masse' ); ?>
				</li>
			<?php endif; ?>
		</ul>
		<br>
	<?php }?>
	<?php if ( get_field( 'elektrounterstuetzung' ) == 1 || get_field( 'hwagen_elektro' ) == 1) : ?>
		<h3>
			<i class="fas fa-tachometer-alt"></i> Motorunterstützung:
		</h3>
		<ul style="list-style-type:none;">
		<?php if(  get_field('motortyp') ): ?>
				<li>
					<i class="fas fa-tachometer-alt"></i>
					<b> Motortyp: </b><?php the_field( 'motortyp' ); ?>
				</li>
		<?php endif; ?>

		<?php if(  get_field('akkugroesse') ): ?>
				<li>
					<i class="fas fa-battery-full"></i>
					<b>Kapazität (Akku) :</b> <?php the_field( 'akkugroesse' ); ?> Wh
				</li>
		<?php endif; ?>

		<?php if(  get_field('reichweite') ): ?>
				<li>
					<i class="fas fa-road"></i>
					<b>Reichweite</b> (Schätzwert): <?php the_field( 'reichweite' ); ?>km
				</li>
		<?php endif; ?>
		</ul>
		<br>
	<?php endif; ?>
	<h3>
		<i class="fas fa-file-alt"></i> Sonstiges:
	</h3>
	<ul style="list-style-type:none;">
		<?php if(  get_field('bremsen') ): ?>
			<li>
				<i class="fas fa-compact-disc"></i>
				<b>Bremsen: </b><?php the_field( 'bremsen' ); ?>
			</li>
		<?php endif; ?>

		<?php if(  get_field('modellname') ): ?>
			<li>
				<i class="far fa-sticky-note"></i>
				<b> Modell: </b><?php the_field( 'modellname' ); ?>
			</li>
		<?php endif; ?>

		<?php if(  get_field('redmine_link') ): ?>
			<li>
				<i class="fas fa-hands-helping"></i>
				<a href="<?php the_field( 'redmine_link' ); ?>" target="_blank">Internes</a>
			</li>
		<?php endif; ?>
	</ul>
	</p>
</div>


<?php
}
add_action( 'cb_acfprinttrailer', 'cb_acfprinttrailer');

/*---------------------------------------------------------------------------------
 * ENDE Print ACF Fields Anhänger (80% done)
 * TODO: Technische Infos printen, Accordion Größe fixen, Cleanup & Kommentare
 * Kupplungsfeld "sortenrein" machen, soll ermöglichen beliebigen Kupplungsnamen zu ergänzen der dann automatisch mit der passenden Kupplung aufgefüllt wird
 * -------------------------------------------------------------------------------
 * Outputtet die ACF Felder für Anhänger
 * ---------------------------------------------------------------------------------
 * ENDE Print ACF Fields Anhänger
 * ---------------------------------------------------------------------------------
*/
?>
