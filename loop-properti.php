<?php
/**
 * Loop - Property
 *
 * This is a custom loop file, containing the looping logic for use in the "properti" page template,
 * as well as the "kategori-properti" taxonomy archive screens. The custom query is only run on the page
 * template, as we already have the data we need when on the taxonomy archive screens.
 *
 * @package WooFramework
 * @subpackage Template
 */

global $woo_options;
global $more; $more = 0;

/* Setup parameters for this loop. */
/* Make sure we only run our custom query when using the page template, and not in a taxonomy. */

$thumb_width = 210;
$thumb_height = 157;

/* Make sure our thumbnail dimensions come through from the theme options. */
if ( isset( $woo_options['woo_properti_thumb_width'] ) && ( $woo_options['woo_properti_thumb_width'] != '' ) ) {
	$thumb_width = $woo_options['woo_properti_thumb_width'];
}

if ( isset( $woo_options['woo_properti_thumb_height'] ) && ( $woo_options['woo_properti_thumb_height'] != '' ) ) {
	$thumb_height = $woo_options['woo_properti_thumb_height'];
}

woo_loop_before();

if ( ! is_tax() ) {

$galleries = array();
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
$query_args = array(
				'post_type' => 'properti',
				'paged' => $paged,
				'posts_per_page' => -1
			);

/* Setup properti galleries navigation. */
$galleries = get_terms( 'kategori-properti' );

$exclude_str = '';
if ( isset( $woo_options['woo_properti_excludenav'] ) && ( $woo_options['woo_properti_excludenav'] != '' ) ) {
	$exclude_str = $woo_options['woo_properti_excludenav'];
}

// Allow child themes/plugins to filter here.
$exclude_str = apply_filters( 'woo_properti_gallery_exclude', $exclude_str );

/* Optionally exclude navigation items. */
if ( $exclude_str != '' ) {
	$to_exclude = explode( ',', $exclude_str );

	if ( is_array( $to_exclude ) ) {
		foreach ( $to_exclude as $k => $v ) {
			$to_exclude[$k] = str_replace( ' ', '', $v );
		}

		/* Remove the galleries to be excluded. */
		foreach ( $galleries as $k => $v ) {
			if ( in_array( $v->slug, $to_exclude ) ) {
				unset( $galleries[$k] );
			}
		}
	}
}

// print_r($galleries);
/* If we have galleries, make sure we only get items from those galleries. */
if ( count( $galleries ) > 0 ) {

$gallery_slugs = array();
foreach ( $galleries as $g ) { $gallery_slugs[] = $g->slug; }

$query_args['tax_query'] = array(
								array(
									'taxonomy' => 'kategori-properti',
									'field' => 'slug',
									'terms' => $gallery_slugs
								)
							);
}

/* The Query. */
query_posts( $query_args );

} // End IF Statement ( is_tax() )

/* The Loop. */
if ( have_posts() ) { $count = 0;
?>
<div id="portfolio">
<?php
	/* Display the gallery navigation (from theme-functions.php). */
	if ( is_page() || is_post_type_archive() ) { woo_properti_navigation( $galleries ); }
?>
	<div class="portfolio-items">
<?php
	while ( have_posts() ) { the_post(); $count++;

	/* Get the settings for this properti item. */
	$settings = woo_portfolio_item_settings( $post->ID );

	/* If the theme option is set to link to the single properti item, adjust the $settings. */
	if ( isset( $woo_options['woo_properti_linkto'] ) && ( $woo_options['woo_properti_linkto'] == 'post' ) ) {
		$settings['large'] = get_permalink( $post->ID );
		$settings['rel'] = '';
	}
?>
		<div <?php post_class( $settings['css_classes'] ); ?> style="max-width: <?php echo intval( $thumb_width ); ?>px;">
		<?php
			/* Setup image for display and for checks, to avoid doing multiple queries. */
			$image = woo_image( 'return=true&key=properti-image&width=' . $thumb_width . '&height=' . $thumb_height . '&link=img&alt=' . the_title_attribute( array( 'echo' => 0 ) ) . '' );

			if ( $image != '' ) {
		?>
			<a <?php echo $settings['rel']; ?> title="<?php echo esc_attr( $settings['caption'] ); ?>" href="<?php echo esc_url( $settings['large'] ); ?>" class="thumb">
				<?php echo $image; ?>
            </a>
			<h3 class="entry-title"><a href="<?php echo esc_url( get_permalink( get_the_ID() ) ); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>   (<?php echo rwmb_meta( 'KOKI_jenis' ); ?>)</h3>
		<?php
				// Output image gallery for lightbox
            	if ( ! empty( $settings['gallery'] ) && '' != $settings['rel'] ) {
                	foreach ( array_slice( $settings['gallery'], 1 ) as $img => $attachment ) {
                		if ( get_post_thumbnail_id() == $attachment['id'] ) { continue; } // avoid duplicated featured images
                		echo '<a ' . $settings['rel'] . ' title="' . $attachment['caption'] . '" href="' . $attachment['url'] . '" class="gallery-image"><img src="' . esc_url( $attachment['url'] ) . '" alt="' . esc_attr( $attachment['alt'] ) . '" width="0" height="0" /></a>' . "\n";
                	}
                }
			} // End IF Statement
		?>
		<span class="price"><?php echo bd_nice_number(rwmb_meta( 'KOKI_harga', array(), get_post()->ID )); ?></span>
		</div><!--/.group .post .properti-img-->
<?php
	} // End WHILE Loop
?>
	</div><!--/.properti-items-->
</div><!--/#properti-->
<?php
} else {
	get_template_part( 'content', 'noposts' );
} // End IF Statement

woo_loop_after();

rewind_posts();

woo_pagenav();
?>