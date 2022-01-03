<?php
/**
 * The template for taxonomy cb_locations_category
 *
 * @package Neve
 * @since   1.0.0
 */
$container_class = apply_filters( 'neve_container_class_filter', 'container', 'single-page' );

get_header();

?>
<div class="<?php echo esc_attr( $container_class ); ?> single-page-container">
	<div class="row">
		<?php do_action( 'neve_do_sidebar', 'single-page', 'left' ); ?>
		<div class="nv-single-page-wrap col">
			<?php
			/**
			 * Executes actions before the page header.
			 *
			 * @since 2.4.0
			 */
			do_action( 'neve_before_page_header' );

			/**
			 * Executes the rendering function for the page header.
			 *
			 * @param string $context The displaying location context.
			 *
			 * @since 1.0.7
			 */

			/**
			 * Executes actions before the page content.
			 *
			 * @param string $context The displaying location context.
			 *
			 * @since 1.0.7
			 */
			do_action( 'neve_before_content', 'single-page' );
			if ( have_posts() ) {
				while ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/content', 'page' );
				}
			} else {
				get_template_part( 'template-parts/content', 'none' );
			}
			$tax = $_GET['itemcat'];
			$tax_slug = get_term($tax)->slug;
			$tax_name = get_term($tax)->name;
			$currentTerm_slug = get_query_var('term');
			$currentTerm_name = get_term_by( 'slug', $currentTerm_slug, get_query_var('taxonomy') )->name;
			$itemlist_for_loc = get_cb_items_by_category($tax_slug);
			$itemlist_for_loc = filterPostsByLocation($itemlist_for_loc,$currentTerm_slug);
			$itemlist_for_loc = sortItemsByAvailability($itemlist_for_loc);
			?>
			<div class="nv-page-title-wrap nv-big-title">
				<div class="nv-page-title ">
				<h1><?php echo $tax_name;?> in <?php echo $currentTerm_name;?></h1>
				</div><!--.nv-page-title-->
			</div><?php
			if (wp_is_mobile()){
				if ($itemlist_for_loc){
					echo do_shortcode("[cb_itemgallery itemcat='".$tax_slug."' locationcat='".$currentTerm_slug."' hidedefault='false']");
				}
			}
			else {
				enqueue_postgrid_styles();
				if ( $itemlist_for_loc ) {
					echo create_postgrid_from_posts($itemlist_for_loc);
				}
			}
			/**
			 * Executes actions after the page content.
			 *
			 * @param string $context The displaying location context.
			 *
			 * @since 1.0.7
			 */
			do_action( 'neve_after_content', 'single-page' );
			?>
		</div>
		<?php do_action( 'neve_do_sidebar', 'single-page', 'right' ); ?>
	</div>
</div>
<?php get_footer(); ?>
