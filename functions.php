<?php
$warna = array('red', 'orange', 'blue', 'green', 'teal');

function mycustomfeed_cpt_feed( $query ) {
        if ( $query->is_feed() )
            $query->set( 'post_type', array( 'post', 'lokasi' ) ); 
        return $query;
}
add_filter( 'pre_get_posts', 'mycustomfeed_cpt_feed' );

function my_enqueue($hook) {
    // if( 'edit.php' != $hook )
    //     return;
    wp_enqueue_script( 'my_custom_script', get_stylesheet_directory_uri() . '/addons/js/myscript.js' );
    // wp_register_style( 'custom_wp_admin_css', get_template_directory_uri() . '/addons/css/mystyle.css', false, '1.0.0' );
    // wp_enqueue_style( 'custom_wp_admin_css' );
}
add_action( 'admin_enqueue_scripts', 'my_enqueue' );

function KOKI_enqueue($hook) {
	wp_localize_script('peta_js','petajs',array('ajaxurl'=>admin_url('admin-ajax.php')));
    wp_register_style( 'KOKI_wp_css', get_stylesheet_directory_uri() . '/addons/css/mystyle.css', false, '1.0.0' );
    wp_enqueue_style( 'KOKI_wp_css' );
}
add_action( 'wp_enqueue_scripts', 'KOKI_enqueue' );

function get_pin($term_id) {

		$thumbnail_id 	= get_metadata( 'post', $term_id, 'thumbnail_id', true );
		
		if ($thumbnail_id) :
			return wp_get_attachment_url( $thumbnail_id );
		else :
			return get_stylesheet_directory_uri() . '/pins/pin2.png';
		endif;
}

function get_peta() {

	$q = $_GET['q'];

	$p = (isset($_GET['p']) && '' != $_GET['p'] ) ? $_GET['p'] : 'lokasi';

	$k = (isset($_GET['p']) && '' != $_GET['p'] ) ? 'kategori-'.$_GET['p'] : 'kategori-lokasi';

	if ($q=='bdg')
	{
		$output = array('title' => 'PetaBDG.com', 'type' => 'marker', 'count' => 1, 'controls' => false);
		$output['points'][0] = array(
		        'title' => 'PetaBDG.com',
		        'lat' => '-6.914744',
		        'lon' => '107.609811',
		        //'html' =>
		        //	'<p><img src="http://www.petabdg.com/wp-content/uploads/2014/05/petabdg-150x150.png" width="150" height="150" /></p>',
		        'zoom' => 12,
		        'icon' => get_stylesheet_directory_uri() .'/pin_petabdg.png',
		        'draggable' => true
		        // 'images' => $images
		    );
	}
	elseif ($q=='drop')
	{
		$output = array('title' => 'PetaBDG.com', 'type' => 'marker', 'count' => 1, 'controls' => false);
		$output['points'][0] = array(
		        'title' => 'PetaBDG.com',
		        'lat' => '-6.914744',
		        'lon' => '107.609811',
		        'html' =>
		        	'<p><img src="http://www.petabdg.com/wp-content/uploads/2014/05/petabdg-150x150.png" width="150" height="150" /></p>',
		        'zoom' => 12,
		        'icon' => get_stylesheet_directory_uri() .'/pin_bdg_flag.png',
		        'draggable' => true
		    );
	}
	else
	{
		// should be able to use -1 to get all posts, rather than 9999
		$args = array('post_type' => $p, 'nopaging' => true, 'orderby'=>'title', 'order'=> 'ASC');
		if($q!='all')
		$args['tax_query'] = array(
									array(
										'taxonomy' => $k,
										'field' => 'slug',
										'terms' => $q
									)
								);

		$myposts = get_posts($args);

		$term = get_term_by('slug',$q,$k);
		// print_r($term->term_id);
		// die;
		if($q=='all') $q = 'Semua Lokasi';

		$output = array('title' => strtoupper($q), 'type' => 'marker', 'count' => count($myposts), 'controls' => ((count($myposts)==1) ? false : true ));
		$zoom = (count($myposts)==1) ? 13 : 15;
		foreach( $myposts as $post ) {
			if (!isset($term->term_id) && $term->term_id =='' ){
				$term_list = wp_get_post_terms($post->ID, $k, array("fields" => "ids"));
				$term_id = $term_list[0];
			} else {
				$term_id = $term->term_id;
			}
			$latlong = get_post_meta($post->ID, 'KOKI_map', TRUE);
			$p_images = array_shift(rwmb_meta( 'KOKI_foto', $args = array('type'=>'image', 'size'=>'thumbnail'), $post->ID ));
			// echo $post->ID;
			// echo "<br/>";
			if(isset($p_images) && ($p_images!='')){
				$image = '<p><img src="' . $p_images['url'] . '" /></p>';
			}
			else
			{
				$image = '';
			}
			// print_r($p_images);
			// echo "<hr/>";
			// $images = '';
			// print_r($p_images);
			// foreach($p_images AS $key=>$image) {
			// 	$images .= '<img src="'. $image['url'] . '" />';
			// }
			// print_r($latlong['latitude']);
		    $cat_output[] = array(
		        'title' => get_the_title($post->ID),
		        'lat' => $latlong['latitude'],
		        'lon' => $latlong['longitude'],
		        'html' =>
		        	'<h3>' . get_the_title($post->ID) . '</h3>
		        	<p>' . get_post_meta($post->ID, 'KOKI_alamat', TRUE) . '</p>
		        	<p><a href="'.get_permalink($post->ID).'">link</a><p>' .
		        	$image,
		        'zoom' => 15,
		        // 'icon' => get_pin($term_id)
		        'icon' => get_stylesheet_directory_uri() .'/pin_bdg_flag.png'
		        // 'images' => $images
		    );
		}
		$output['points'] = $cat_output;
	}
	header("Content-type: application/json");

	die(json_encode($output));
}
add_action('wp_ajax_GETPETA', 'get_peta' ); 
add_action('wp_ajax_nopriv_GETPETA', 'get_peta' ); 


function peta_lapor() {
	// header("Content-type: application/json");
	// $output = array();
	// die(json_encode($output));
	flush_rewrite_rules();
}

add_action('wp_ajax_LAPOR', 'peta_lapor' ); 
add_action('wp_ajax_nopriv_LAPOR', 'peta_lapor' ); 

///////////////////////////////////////////
// CSS admin
///////////////////////////////////////////
function admin_stylesheet() { ?>
    <style type="text/css">
    #wp-admin-bar-wp-logo { display: none ! important;}
    </style>
<?php }
add_action( 'admin_enqueue_scripts', 'admin_stylesheet' );

add_action('init', 'register_peta_custom');
function register_peta_custom() {
	remove_post_type_support('post', 'author');
	register_post_type('lokasi', array(
		'label' => 'Lokasi',
		'description' => 'Lokasi',
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'capability_type' => 'post',
		'map_meta_cap' => true,
		'hierarchical' => false,
		'rewrite' => array('slug' => 'lokasi', 'with_front' => false),
		'query_var' => true,
		'supports' => array('title','editor','thumbnail','publicize'),
		// 'supports' => array('title','editor'),
		'labels' => array (
			'name' => 'Lokasi',
			'singular_name' => 'Lokasi',
			'menu_name' => 'Lokasi',
			'add_new' => 'Tambah',
			'add_new_item' => 'Tambah Lokasi',
			'edit' => 'Edit',
			'edit_item' => 'Edit Lokasi',
			'new_item' => 'Lokasi Baru',
			'view' => 'Lihat Lokasi',
			'view_item' => 'Lihat Lokasi',
			'search_items' => 'Cari Lokasi',
			'not_found' => 'Tidak Ditemukan',
			'not_found_in_trash' => 'Tidak Ditemukan Di Tempat Sampah',
			'parent' => 'Lokasi Induk',
		)
	));

	$args = array(
		'hierarchical' => true,
		'labels' => array(
			'name' => _x( 'Kategori Lokasi', 'taxonomy general name', 'woothemes' ),
			'singular_name' => _x( 'Kategori Lokasi', 'taxonomy singular name','woothemes' ),
			'search_items' =>  __( 'Cari Kategori Lokasi', 'woothemes' ),
			'all_items' => __( 'Semua Kategori Lokasi', 'woothemes' ),
			'parent_item' => __( 'Parent Kategori Lokasi', 'woothemes' ),
			'parent_item_colon' => __( 'Parent Kategori Lokasi:', 'woothemes' ),
			'edit_item' => __( 'Edit Kategori Lokasi', 'woothemes' ),
			'update_item' => __( 'Update Kategori Lokasi', 'woothemes' ),
			'add_new_item' => __( 'Tambah Kategori Lokasi', 'woothemes' ),
			'new_item_name' => __( 'Tambah Kategori Lokasi', 'woothemes' ),
			'menu_name' => __( 'Kategori Lokasi', 'woothemes' )
		),
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'kategori-lokasi' )
	);

	register_taxonomy( 'kategori-lokasi', array( 'lokasi' ), $args );


	register_post_type('laporan', array(
		'label' => 'Laporan Warga',
		'description' => 'Laporan',
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'capability_type' => 'post',
		'map_meta_cap' => true,
		'hierarchical' => false,
		'rewrite' => array('slug' => 'laporan-warga', 'with_front' => false),
		'query_var' => true,
		'has_archive' => true,
		'supports' => array('title','editor','thumbnail','publicize'),
		// 'supports' => array('title','editor'),
		'labels' => array (
			'name' => 'Laporan Warga',
			'singular_name' => 'Laporan Warga',
			'menu_name' => 'Laporan Warga',
			'add_new' => 'Tambah',
			'add_new_item' => 'Tambah Laporan',
			'edit' => 'Edit',
			'edit_item' => 'Edit Laporan',
			'new_item' => 'Laporan Baru',
			'view' => 'Lihat Laporan',
			'view_item' => 'Lihat Laporan',
			'search_items' => 'Cari Laporan',
			'not_found' => 'Tidak Ditemukan',
			'not_found_in_trash' => 'Tidak Ditemukan Di Tempat Sampah',
			'parent' => 'Laporan Induk',
		)
	));

	$args = array(
		'hierarchical' => true,
		'labels' => array(
			'name' => _x( 'Kategori Laporan', 'taxonomy general name', 'woothemes' ),
			'singular_name' => _x( 'Kategori Laporan', 'taxonomy singular name','woothemes' ),
			'search_items' =>  __( 'Cari Kategori Laporan', 'woothemes' ),
			'all_items' => __( 'Semua Kategori Laporan', 'woothemes' ),
			'parent_item' => __( 'Parent Kategori Laporan', 'woothemes' ),
			'parent_item_colon' => __( 'Parent Kategori Laporan:', 'woothemes' ),
			'edit_item' => __( 'Edit Kategori Laporan', 'woothemes' ),
			'update_item' => __( 'Update Kategori Laporan', 'woothemes' ),
			'add_new_item' => __( 'Tambah Kategori Laporan', 'woothemes' ),
			'new_item_name' => __( 'Tambah Kategori Laporan', 'woothemes' ),
			'menu_name' => __( 'Kategori Laporan', 'woothemes' )
		),
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'kategori-laporan' )
	);

	register_taxonomy( 'kategori-laporan', array( 'laporan' ), $args );
    //flush_rewrite_rules();
}

function KOKI_append_to_content( $content ) {	
	global $post;

	// if ( is_singular() && is_main_query() && easy_image_gallery_allowed_post_type() ) {
	if ( is_singular() && is_main_query()  ) {
		//$new_content = easy_image_gallery();
		//$content .= get_post_meta( $post->ID, '_KOKI_lokasi', true );
		// $content .= rwmb_meta( 'KOKI_textarea', array(), get_post()->ID );
		//$content .= '<pre>';
		//$content .= rwmb_meta( 'KOKI_map', array('type'=>'map', 'zoom'=>17, 'info_window'  => '', 'width' => '640px', 'height' => '480px' ), $post->ID );
		//$content .= '</pre>';
		//$the_xxx = array_shift(array_values(rwmb_meta( 'KOKI_foto', $args = array('type'=>'image'), $post->ID )));
		//print_r(rwmb_meta( 'KOKI_foto', $args = array('type'=>'image'), $post->ID ));
		//$content .= rwmb_meta( 'KOKI_foto', $args = array(), $post->ID );
	}

	return $content;

}
//add_filter( 'the_content', 'KOKI_append_to_content' );

include 'inc/metabox.php';


//if ( function_exists( 'add_theme_support' ) ) {
//add_theme_support( 'post-thumbnails' ); 

function easy_add_thumbnail($post) {
	$already_has_thumb = has_post_thumbnail();
	$first_ID = rwmb_meta( 'KOKI_foto', $args = array(), $post->ID );
	// print_r($already_has_thumb);
	// echo "<hr/>";
	// print_r($first_ID);
	// echo "<hr/>";
	// die;
	if ((!$already_has_thumb) AND isset($first_ID) AND ($first_ID!=""))  {
		// echo 123;
		// die;
		add_post_meta($post->ID, '_thumbnail_id', $first_ID, true);
	}elseif (isset($first_ID) AND ($first_ID!="")){
		// echo 321;
		// die;
		delete_post_meta( $post->ID, '_thumbnail_id' );     
		add_post_meta($post->ID, '_thumbnail_id', $first_ID, true);
	}
}

  // add_action('the_post', 'easy_add_thumbnail');
 
  // // hooks added to set the thumbnail when publishing too
  // add_action('new_to_publish', 'easy_add_thumbnail');
  // add_action('draft_to_publish', 'easy_add_thumbnail');
  // add_action('pending_to_publish', 'easy_add_thumbnail');
  // add_action('future_to_publish', 'easy_add_thumbnail');


//}

    //add_action( 'woo_header_inside', 'woo_custom_add_home', 10 );
     
    function woo_custom_add_home () {
        echo '<div id="nav-search" class="nav-search fl"> ' . "
    ";
    ?>
		<ul id="main-nav" class="nav fl">
		<li id="menu-item-2171" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2171">

		    <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img height="19px" alt="<?php echo get_bloginfo( 'name' ); ?>" src="<?php echo get_stylesheet_directory_uri() . '/kb.png'; ?>"/></a>

		</li>
		<li id="menu-item-2172" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2171">

		    <a href="#"><?php echo get_bloginfo( 'description' ); ?></a>

		</li>
		</ul>
    <?php
        //get_template_part( 'search', 'form' );
        echo '</div><!--/#nav-search .nav-search fr-->' . "
    ";
    } // End woo_custom_add_searchform()

    //require_once 'theme-customizer/theme-customizer-demo.php';

function woo_custom_slider_autoheight( $auto ) {
return false;
}
add_filter( 'woo_slider_autoheight', 'woo_custom_slider_autoheight' );

function woo_custom_slider_height( $height ) {
return 400;
}
add_filter( 'woo_slider_height', 'woo_custom_slider_height' ); 

/*-----------------------------------------------------------------------------------*/
/* Optional Top Navigation (WP Menus)  */
/*-----------------------------------------------------------------------------------*/
register_nav_menus( array( 'top-menu-right' => __( 'Top Menu - Right', 'woothemes' ) ) );
	
if ( ! function_exists( 'woo_top_navigation' ) ) {
function woo_top_navigation() {
	if ( function_exists( 'has_nav_menu' ) && has_nav_menu( 'top-menu' ) ) {
?>
	<div id="top">
		<div class="col-full">
			<?php
				echo '<h3 class="top-menu">' . woo_get_menu_name( 'top-menu' ) . '</h3>';
				wp_nav_menu( array( 'depth' => 6, 'sort_column' => 'menu_order', 'container' => 'ul', 'menu_id' => 'top-nav', 'menu_class' => 'nav top-navigation fl', 'theme_location' => 'top-menu' ) );
			?>
			<ul id="top-nav" class="nav top-navigation fr">
			<?php if (of_get_option('telepon') != of_get_option('whatsapp')) { ?>
				<?php if (of_get_option('telepon') && of_get_option('telepon') != '') { ?>
				<li id="menu-item-2163" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2163"><a href="tel:<?php echo of_get_option('telepon', '' ); ?>">
				Telp: <?php echo of_get_option('telepon', '' ); ?>
				</a></li>
				<?php } ?>
				<?php if (of_get_option('whatsapp') && of_get_option('whatsapp') != '') { ?>
				<li id="menu-item-2164" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2164"><a href="wa:<?php echo of_get_option('whatsapp', '' ); ?>">
				WA: <?php echo of_get_option('whatsapp', '' ); ?></a></li>
				<?php } ?>
			<?php } else { ?>
				<?php if (of_get_option('telepon') && of_get_option('telepon') != '' && (of_get_option('whatsapp') && of_get_option('whatsapp') != '')) { ?>
				<li id="menu-item-2163" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2163"><a href="tel:<?php echo of_get_option('telepon', '' ); ?>">
				Telp/SMS/WA: <?php echo of_get_option('telepon', '' ); ?>
				</a></li>
				<?php } ?>
			<?php } ?>
			<?php if (of_get_option('pin_bb') && of_get_option('pin_bb') != '') { ?>
			<li id="menu-item-2165" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2165"><a href="bbm:<?php echo of_get_option('pin_bb', '' ); ?>">BBM: <?php echo of_get_option('pin_bb', '' ); ?></a></li>
			<?php } ?>
			</ul>
		</div>
	</div><!-- /#top -->
<?php
	}
} // End woo_top_navigation()
}

	


function bd_nice_number($n) {
    // first strip any formatting;
    $n = (0+str_replace(",","",$n));
   
    // is this a number?
    if(!is_numeric($n)) return false;
   
    // now filter it;
    if($n>1000000000000) return round(($n/1000000000000),1).' triliun';
    else if($n>1000000000) return round(($n/1000000000),1).' milyar';
    else if($n>1000000) return round(($n/1000000),1).' juta';
    else if($n>1000) return round(($n/1000),1).' ribu';
   
    return number_format($n);
}


add_action('optionsframework_after_validate', 'woo_options_mod');
function woo_options_mod( $atts ) {
	// $options = get_option( 'woocommerce_paypal_settings' );
	// foreach ( $options as $option ) {
	// 	$options['enabled'] = $atts['pp_aktifkan'];
	// 	$options['email'] = $atts['pp_email'];
	// 	$options['receiver_email'] = $atts['email'];
	// 	$options['testmode'] = $atts['pp_testmode'];//testmode
	// }
	// update_option('woocommerce_paypal_settings', $options );

	update_option('blogname', $atts['nama_toko'] );
	update_option('blogdescription', $atts['tagline'] );
	update_option('woocommerce_email_from_name', $atts['nama_toko'] );
	update_option('admin_email', $atts['email'] );
	update_option('woocommerce_email_from_address', $atts['email'] );

	update_option('woo_contact_title', $atts['lokasi'] );
	update_option('woo_contact_number', $atts['telepon'] );
	update_option('woo_contact_fax', $atts['fax'] );
	update_option('woo_contact_address', $atts['alamat'] );
	update_option('woo_connect_twitter', $atts['twitter'] );
	update_option('woo_connect_facebook', $atts['facebook'] );
	update_option('woo_connect_instagram', $atts['instagram'] );
	update_option('woo_connect_youtube', $atts['youtube'] );
	update_option('woo_contactform_email', $atts['email'] );
	update_option('woo_full_header_bg_image', $atts['header_bg'] );

	$woo_options_mod = get_option( 'woo_options' );
	foreach ( $woo_options_mod as $woo_option ) {
		$woo_options_mod['woo_full_header_bg_image'] = $atts['header_bg'] ;
		$woo_options_mod['woo_contact_title'] = $atts['lokasi'] ;
		$woo_options_mod['woo_contact_number'] = $atts['telepon'] ;
		$woo_options_mod['woo_contact_fax'] = $atts['fax'] ;
		$woo_options_mod['woo_contact_address'] = $atts['alamat'] ;
		$woo_options_mod['woo_connect_twitter'] = $atts['twitter'];
		$woo_options_mod['woo_connect_facebook'] = $atts['facebook'] ;
		$woo_options_mod['woo_connect_instagram'] = $atts['instagram'] ;
		$woo_options_mod['woo_connect_youtube'] = $atts['youtube'] ;
		$woo_options_mod['woo_contactform_email'] = $atts['email'] ;
	}
	update_option('woo_options', $woo_options_mod );
}


class tentang_widget extends WP_Widget
{
  function tentang_widget()
  {
    $widget_ops = array(
		'classname' => 'tentang_widget', 
		'description' => 'Tentang PetaBDG.com' 
	);
    $this->WP_Widget('tentang_widget', 'ADA > Tentang PetaBDG.com', $widget_ops);
  }
 
  function widget( $args, $instance ) 
  {
	extract( $args, EXTR_SKIP );
	echo $before_widget;

	// WIDGET CODE GOES HERE

	echo '<h3>Tentang PetaBDG.com</h3>';
	if (of_get_option('tentang') && of_get_option('tentang') != '')
	echo str_ireplace("\r\n", "<br/>" , of_get_option('tentang'));

	echo $after_widget;
  }
 
}

class kategori_lokasi_widget extends WP_Widget
{
  function kategori_lokasi_widget()
  {
    $widget_ops = array(
		'classname' => 'kategori_lokasi_widget', 
		'description' => 'Tentang PetaBDG.com' 
	);
    $this->WP_Widget('kategori_lokasi_widget', 'PetaBDG.com > Kategori Lokasi ', $widget_ops);
  }
 
  function widget( $args, $instance ) 
  {
	extract( $args, EXTR_SKIP );
	echo $before_widget;

	// WIDGET CODE GOES HERE

	// echo '<h3>Tentang PetaBDG.com</h3>';
	foreach(get_terms( 'kategori-lokasi' ) AS $lokasi) {
	$lokasi = sanitize_term( $lokasi, 'kategori-lokasi' );    
	$term_link = get_term_link( $lokasi, 'kategori-lokasi' );
   		echo '<a href="'.$term_link.'" title="'.$lokasi->name.'">'.$lokasi->name.' ('.$lokasi->count.')</a> ';
    }

	echo $after_widget;
  }
 
}

class hubungi_widget extends WP_Widget
{
  function hubungi_widget()
  {
    $widget_ops = array(
		'classname' => 'hubungi_widget', 
		'description' => 'Hubungi PetaBDG.com' 
	);
    $this->WP_Widget('hubungi_widget', 'ADA > Hubungi PetaBDG.com', $widget_ops);
  }
 
  function widget( $args, $instance ) 
  {
	extract( $args, EXTR_SKIP );
	echo $before_widget;

	// WIDGET CODE GOES HERE

	echo '<h3>Hubungi PetaBDG.com</h3>';
	if (of_get_option('alamat') && of_get_option('alamat') != '')
	echo 'Alamat:<br/><strong>' . str_ireplace("\r\n", "<br/>" , of_get_option('alamat')) . '</strong><br/>';
	if (of_get_option('telepon') && of_get_option('telepon') != '')
	echo 'Telepon:<br/><strong>' . of_get_option('telepon') . '</strong><br/>';
	if (of_get_option('whatsapp') && of_get_option('whatsapp') != '')
	echo 'WhatsApp:<br/><strong>' . of_get_option('whatsapp') . '</strong><br/>';
	if (of_get_option('pin_bb') && of_get_option('pin_bb') != '')
	echo 'Pin BB:<br/><strong>' . of_get_option('pin_bb') . '</strong><br/>';
	if (of_get_option('email') && of_get_option('email') != '')
	echo 'Email:<br/><strong>' . of_get_option('email') . '</strong><br/>';

	echo $after_widget;
  }
 
}

function KOKI_register_widget() 
{
	register_widget( 'tentang_widget' );
	register_widget( 'hubungi_widget' );
	register_widget( 'kategori_lokasi_widget' );

}
add_action( 'widgets_init', 'KOKI_register_widget' );



/*-----------------------------------------------------------------------------------*/
/* Woo Portfolio, get image dimensions based on layout and website width settings. */
/*-----------------------------------------------------------------------------------*/

function woo_lokasi_image_dimensions ( $layout = 'one-col', $width = '960' ) {
	$dimensions = array( 'width' => 520, 'height' => 0, 'thumb_width' => 150, 'thumb_height' => 150 );

	// One Column.
	if ( 'one-col' == $layout ) {
		$dimensions['width'] = intval( $width );
	}

	// Two Column.
	if ( 'two-col' == substr( $layout, 0, 7 ) ) {
		$dimensions['width'] = 800;
	}

	// Three Column.
	if ( 'three-col' == substr( $layout, 0, 9 ) ) {
		$dimensions['width'] = 680;
	}

	// Allow child themes/plugins to filter these dimensions.
	$dimensions = apply_filters( 'woo_lokasi_gallery_dimensions', $dimensions );

	return $dimensions;
} // End woo_post_gallery_dimensions()


/*-----------------------------------------------------------------------------------*/
/* Add layout to body_class output */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'woo_layout_body_class' ) ) {
	function woo_layout_body_class( $classes ) {
		global $post, $wp_query, $woo_options;
		$layout = '';

		// Single post layout
		if ( is_singular() ) {

			// Get layout setting from single post Custom Settings panel
			$layout = get_post_meta( $post->ID, 'layout', true );

			// Set to single post layout if selected
			if ( $layout != '' ) {
				$woo_options['woo_layout'] = $layout;

			// Portfolio single post layout option.
			} elseif ( get_post_type() == 'portfolio' OR get_post_type() == 'lokasi' OR get_post_type() == 'laporan' ) {
				$portfolio_single_layout = get_option( 'woo_portfolio_layout_single' );
				if ( $portfolio_single_layout != '' ) {
					$layout = $portfolio_single_layout;
					$woo_options['woo_layout'] = $portfolio_single_layout;
				}

			}
		}

		// Set default global layout
		if ( $layout == '' ) {
			$layout = get_option( 'woo_layout' );
				if ( $layout == '' )
					$layout = 'two-col-left';
		}

		// Portfolio gallery layout option.
		if ( is_tax( 'portfolio-gallery' ) || is_post_type_archive( 'portfolio' ) || is_page_template( 'template-portfolio.php' ) ) {
			$portfolio_gallery_layout = get_option( 'woo_portfolio_layout' );
			if ( $portfolio_gallery_layout != '' ) {
				$layout = $portfolio_gallery_layout;
			}
		}

		if ( is_tax( 'portfolio-gallery' ) || is_post_type_archive( 'portfolio' ) || is_page_template( 'template-portfolio.php' ) || ( is_singular() && get_post_type() == 'portfolio' ) ) {
			$classes[] = 'portfolio-component';
		}

		// Lokasi gallery layout option.
		if ( is_tax( 'kategori-lokasi' ) || is_post_type_archive( 'lokasi' ) || is_page_template( 'template-lokasi.php' ) ) {
			$portfolio_gallery_layout = get_option( 'woo_portfolio_layout' );
			if ( $portfolio_gallery_layout != '' ) {
				$layout = 'two-col-left';
			}
		}

		if ( is_tax( 'kategori-lokasi' ) || is_post_type_archive( 'lokasi' ) || is_page_template( 'template-lokasi.php' ) || ( is_singular() && get_post_type() == 'lokasi' ) ) {
			$classes[] = 'lokasi-component';
		}

		// WooCommerce Layout
		if ( class_exists( 'woocommerce' ) && function_exists( 'is_shop' )  && function_exists( 'is_cart' ) && function_exists( 'is_checkout' ) && function_exists( 'is_account_page' ) && function_exists( 'is_product' ) && function_exists( 'is_order_received_page' ) && ( is_woocommerce() || is_shop() || is_cart() || is_checkout() || is_account_page() || is_product() || is_order_received_page() ) ) {

			// Set defaul layout
			$woocommerce_layout = get_option( 'woo_wc_layout' );
			if ( $woocommerce_layout != '' ) {
				$layout = $woocommerce_layout;
				$woo_options['woo_layout'] = $woocommerce_layout;
			}

			// WooCommerce single post/page
			if ( is_singular() ) {

				// Get layout setting from single post Custom Settings panel
				$single_layout = get_post_meta( $post->ID, 'layout', true );

				// Set to single post layout if selected
				if ( $single_layout != '' ) {
					$woo_options['woo_layout'] = $single_layout;
					$layout = $single_layout;
				}
			}

		}

		// Specify site width
		$width = intval( str_replace( 'px', '', get_option( 'woo_layout_width', '960' ) ) );

		// Add classes to body_class() output
		$classes[] = $layout;
		$classes[] = 'width-' . $width;
		$classes[] = $layout . '-' . $width;
		return $classes;
	} // End woo_layout_body_class()
}

include "inc/add_thumbnail_field.php";
