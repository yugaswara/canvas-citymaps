<?php
/**
 * Single Laporan Item Template
 *
 * This template is the default Laporan item template. It is used to display content when someone is viewing a
 * singular view of a portfolio item ('Laporan' post_type).
 * @link http://codex.wordpress.org/Post_Types#Post
 *
 * @package WooFramework
 * @subpackage Template
 */
include('inc/lib_autolink.php');
$woo_options['woo_layout'] = 'one-col';
get_header();
global $woo_options;
//$woo_options['woo_layout'] = 'one-col';
?>
       
    <!-- #content Starts -->
	<?php woo_content_before(); ?>
    <div id="content" class="col-full">
    
    	<div id="main-sidebar-container">    

            <!-- #main Starts -->
            <?php woo_main_before(); ?>
            <section id="main">                       
<?php
	woo_loop_before();
	
	if ( have_posts() ) { $count = 0;
		while ( have_posts() ) { the_post(); $count++;
		echo '<div id="post-gallery" class="portfolio-img">';
		echo rwmb_meta( 'KOKI_map', array('type'=>'map', 'zoom'=>50, 'info_window'  => '<h4>' . get_the_title() . '</h4><p>' . rwmb_meta( 'KOKI_alamat' ) . '</p>', 'width' => '520px', 'height' => '320px' ),  get_post()->ID );
		echo '</div>';

		$thumbnail 	= woo_image( 'noheight=true&return=true&key=portfolio-image&width=520&class=portfolio-img' );
		
		if(isset($thumbnail) && $thumbnail!='')
		echo '<div id="post-gallery" class="portfolio-img">' . $thumbnail . '</div><!--/#post-gallery .portfolio-img-->' . "\n";
		
		locate_template( array( 'Laporan-gallery.php' ), true );
			
			
	//echo '</div>';
	//echo '<div class="twocol-one last">';
?>
	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<h2 class="entry-title"><?php the_title(); ?></h2>
		<?php //woo_post_meta(); ?>
		<div class="post-meta">Diinput pada: <?php the_time('l, j F Y'); ?>, Pukul <?php the_time('H:i'); ?></div>
    	<section class="entry">	
		<?php
			/* Portfolio item extras (testimonial, website button, etc). */
			//woo_portfolio_item_extras( $post_settings );
			//print_r(rwmb_meta('KOKI_foto'));
		?>
		<?php if (rwmb_meta( 'KOKI_alamat' ) && rwmb_meta( 'KOKI_alamat' ) != '') { ?>
		<div>Alamat:  
		<strong><?php echo rwmb_meta( 'KOKI_alamat' ); ?></strong>
		</div>
		<?php } ?>
		<?php if (rwmb_meta( 'KOKI_telepon' ) && rwmb_meta( 'KOKI_telepon' ) != '') { ?>
		<div>Telepon:  
		<strong><?php echo rwmb_meta( 'KOKI_telepon' ); ?></strong>
		</div>
		<?php } ?>
		<?php if (rwmb_meta( 'KOKI_website' ) && rwmb_meta( 'KOKI_website' ) != '') { ?>
		<div>Website:  
		<strong><?php echo autolink(rwmb_meta( 'KOKI_website' )); ?></strong>
		</div>
		<?php } ?>
		<?php if (rwmb_meta( 'KOKI_pengirim' ) && rwmb_meta( 'KOKI_pengirim' ) != '') { ?>
		<div>Pengirim: 
		<strong><?php echo rwmb_meta( 'KOKI_pengirim' ); ?> 
		<?php if (rwmb_meta( 'KOKI_twitter_pengirim' ) && rwmb_meta( 'KOKI_twitter_pengirim' ) != '') { 
		echo '('. preg_replace('/(?<=^|\s)@([a-z0-9_]+)/i',
                      '<a href="http://www.twitter.com/$1">@$1</a>',
                      rwmb_meta( 'KOKI_twitter_pengirim' )) . ')';
		} ?></strong>
		</div>
		<?php } ?>
		<hr/>
		<h3>Informasi/Deskripsi</h3>
    	<?php the_content(); ?>
   		</section><!--/.entry-->
   	</div><!--/#post-->
<?php	
	//echo '</div>';
		}
	}
	
	woo_loop_after();
?>     
            </section><!-- /#main -->
            <?php woo_main_after(); ?>
    
            <?php get_sidebar(); ?>

		</div><!-- /#main-sidebar-container -->         

		<?php get_sidebar( 'alt' ); ?>

    </div><!-- /#content -->
	<?php woo_content_after(); ?>

<?php get_footer(); ?>
