<?php
/**
 * Template Name: PetaBDG.com
 *
 * Peta Lokasi Keseluruhan
 *
 * @author: Wisya Yugaswara J
 */

function peta_script($hook) {
    // if( 'edit.php' != $hook )
    //     return;
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'gmap_script', 'http://maps.google.com/maps/api/js?sensor=false&libraries=geometry&v=3.7' );
    wp_enqueue_script( 'peta_script', get_stylesheet_directory_uri() . '/addons/js/maplace.min.js' );
    // wp_enqueue_script( 'map_icons_script', get_stylesheet_directory_uri() . '/map-icons/js/map-icons.js' );
    // wp_register_style( 'map_icons_css', get_stylesheet_directory_uri() . '/map-icons/css/map-icons.css', false, '1.0.0' );
    // wp_enqueue_script( 'gmaps_script', get_stylesheet_directory_uri() . '/addons/js/gmaps.js' );
    // wp_register_style( 'gmaps_css', get_stylesheet_directory_uri() . '/addons/css/gmaps.css', false, '1.0.0' );
    // wp_enqueue_style( 'gmaps_css' );
}
add_action( 'wp_enqueue_scripts', 'peta_script' );
 global $woo_options; 
 get_header();

$kategori_lokasi = get_terms( 'kategori-lokasi' );
?>
<style>
div.wrap_controls {
  background: none repeat scroll 0% 0% #FFF;
  padding: 5px;
  border: 1px solid #717B87;
  box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.4);
  max-height: 520px;
  min-width: 100px;
  overflow-y: auto;
  overflow-x: hidden;
  margin: 5px;
}
</style>
	<script type="text/javascript">
    jQuery( document ).ready( function() {
      target_offset = jQuery('#lokasi').offset(),
      target_top = target_offset.top;
      jQuery('html, body').animate({
          scrollTop: target_top
      }, 800);
	    var mixed = new Maplace({
	      map_div: '#lokasi',
	      controls_applycss: false,
        controls_type: 'dropdown'
	      // controls_cssclass: 'wrap_controls'
	    });
	    function showGroup(index) {
	      // var el = $('#g'+index);
	      jQuery.getJSON('<?php echo admin_url('admin-ajax.php'); ?>?action=GETPETA', { q: index }, function(data) {
	        mixed.Load({
	          locations: data.points,
	          view_all_text: data.title,
	          type: data.type,
	          force_generate_controls: data.controls,
	        });
	      });
	    }
	    jQuery('#link-lokasi a').click(function(e) {
    	  jQuery('div.gmap_controls').remove();
        jQuery('a.bold').removeClass('bold');
        jQuery(this).addClass('bold');
	      e.preventDefault();
	      var index = jQuery(this).attr('data-load');
	      showGroup(index);
	    });
    showGroup('bdg');
	});
</script>
    <!-- #content Starts -->
	<?php woo_content_before(); ?>
    <div id="content" class="col-full">
    
    	<div id="main-sidebar-container">    

            <!-- #main Starts -->
            <?php woo_main_before(); ?>
            <section id="main">  
            <?php
              woo_loop_before();
              
              if (have_posts()) { $count = 0;
                while (have_posts()) { the_post(); $count++;
                  
                  the_content();
                }
              }
            ?> 
            <div class="lokasi gmap" id="lokasi" style="width:100%;height:500px;max-width: none;"></div>
            <div id="link-lokasi">
            <strong>Pilihan kategori: </strong>
            <a href="javascript:void(0)" data-load="all" id="g0" class="" title="Semua Lokasi">Semua Lokasi</a>, 
            <?php 
            foreach($kategori_lokasi AS $lokasi) {
            $lokasi = sanitize_term( $lokasi, 'kategori-lokasi' );    
            $term_link = get_term_link( $lokasi, 'kategori-lokasi' ); 
            ?>
            <a href="<?php echo $term_link; ?>" data-load="<?php echo $lokasi->slug; ?>" id="g0" title="<?php echo $lokasi->name; ?>"><?php echo $lokasi->name; ?> (<?php echo $lokasi->count; ?>)</a>, 
            <?php } ?>
            </div>
            <?php //echo rwmb_meta( 'KOKI_map', array('type'=>'map', 'zoom'=>50, 'info_window'  => '<h3>' . get_the_title() . '</h3><p>' . rwmb_meta( 'KOKI_alamat' ) . '</p>', 'width' => '520px', 'height' => '320px' ), 90 ); ?>
  
            </section><!-- /#main -->
            <?php woo_main_after(); ?>
    
            <?php get_sidebar(); ?>

		</div><!-- /#main-sidebar-container -->         

		<?php get_sidebar( 'alt' ); ?>

    </div><!-- /#content -->
	<?php woo_content_after(); ?>

<?php get_footer(); ?>
