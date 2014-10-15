<?php
/**
 * Template Name: List Lokasi
 * 
 */

$woo_options['woo_layout'] = 'two-col';
get_header();
global $woo_options;
$current_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
// print_r(get_query_var('kategori-lokasi'));
// print_r($_SERVER['QUERY_STRING']);
// $the_query = new WP_Query( $args );
//$woo_options['woo_layout'] = 'one-col';
?>
    <!-- #content Starts -->
	<?php woo_content_before(); ?>
    <div id="content" class="col-full">
    
    	<div id="main-sidebar-container">    

            <!-- #main Starts -->
            <?php woo_main_before(); ?>
            <section id="main">   
            <ul> 
            <?php 
            	foreach(get_terms( 'kategori-lokasi' ) AS $lokasi) { 
    			$lokasi = sanitize_term( $lokasi, 'kategori-lokasi' );    
    			$term_link = get_term_link( $lokasi, 'kategori-lokasi' );
    		?>
            <li><a href="<?php echo $term_link; ?>" data-load="<?php echo $lokasi->slug; ?>" id="g0" title="<?php echo $lokasi->name; ?>"><?php echo $lokasi->name; ?> (<?php echo $lokasi->count; ?>)</a> </li>
            <?php } ?>
            </ul>
<?php
	// Load property gallery	
	// get_template_part( 'loop', 'properti' );
	// print_r(get_query_var('cat'));
?>
            </section><!-- /#main -->
            <?php woo_main_after(); ?>
    
            <?php get_sidebar(); ?>

		</div><!-- /#main-sidebar-container -->         

		<?php get_sidebar( 'alt' ); ?>

    </div><!-- /#content -->
	<?php woo_content_after(); ?>

<?php get_footer(); ?>