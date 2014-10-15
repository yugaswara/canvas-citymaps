<?php
/**
 * Template Name: Form PetaBDG.com
 * 
 * The Form Template
 *
 * @author Wisya Yugaswara J
 */
function tambah_lokasi_redirect($url)
{
   header('Location: ' . $url);
   die();
}
function tambah_lokasi_enqueue($hook) {
	wp_enqueue_script( 'googlemap', 'http://maps.google.com/maps/api/js?sensor=false&libraries=places&v=3.7', array(), '', true  );
	//wp_enqueue_script( 'googlemap', 'https://maps.google.com/maps/api/js?sensor=false', array(), '', true );
	//wp_enqueue_script( 'rwmb-map', RWMB_JS_URL . 'map.js', array( 'jquery', 'jquery-ui-autocomplete', 'googlemap' ), RWMB_VER, true );
	wp_enqueue_script( 'jquery_validate', get_stylesheet_directory_uri() . '/jquery.validate.min.js', array( 'jquery', 'jquery-ui-autocomplete', 'googlemap' ), RWMB_VER, true );
    wp_enqueue_script( 'pw_google_maps_init', get_stylesheet_directory_uri() . '/addons/js/script.js', array( 'jquery', 'jquery-ui-autocomplete', 'googlemap' ), RWMB_VER, true );
    wp_enqueue_style( 'pw_google_maps_css', get_stylesheet_directory_uri() . '/addons/css/style.css', array(), null );
    wp_enqueue_style( 'KOKI_wp_css' );
}
add_action( 'wp_enqueue_scripts', 'tambah_lokasi_enqueue' );

function koki_handle_attachment($file_handler,$post_id,$set_thu=false) {
    // check to make sure its a successful upload
    if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) __return_false();
 
    require_once(ABSPATH . "wp-admin" . '/includes/image.php');
    require_once(ABSPATH . "wp-admin" . '/includes/file.php');
    require_once(ABSPATH . "wp-admin" . '/includes/media.php');
 
    $attach_id = media_handle_upload( $file_handler, $post_id );
 
         // If you want to set a featured image frmo your uploads.
    if ($set_thu) set_post_thumbnail($post_id, $attach_id);
    return $attach_id;
}

// function do_insert() {	
if( 'POST' == $_SERVER['REQUEST_METHOD'] || wp_verify_nonce( $_POST['lokasi-baru'] ) ) { // Check what the post type is here instead
    
    if(isset($_POST['bukalah']) && ($_POST['bukalah']=='022') &&
       isset($_POST['title']) && isset($_POST['map']) &&
       isset($_POST['pengirim'])) {

        if (isset ($_POST['title'])) { $title = $_POST['title']; } else { echo ''; }
        if (isset ($_POST['description'])) { $description = $_POST['description']; } else { echo ''; }

        $post = array(
        	'post_title'	=> $title,
        	'post_content'	=> $description,
        	'tax_input'	=> array('kategori-lokasi'=>$cat), 
        	// 'tags_input'	=> $tags,
        	'post_status'	=> 'draft', // Choose: publish, preview, future, etc.
        	'post_type'	=> 'lokasi' // Set the post type based on the IF is post_type X
        );

        $insert_lokasi = wp_insert_post($post); // = post_ID

        add_post_meta( $insert_lokasi, 'KOKI_map', $_POST['map'], false );
        add_post_meta( $insert_lokasi, 'KOKI_alamat', $_POST['alamat'], false );
        add_post_meta( $insert_lokasi, 'KOKI_website', $_POST['website'], false );
        add_post_meta( $insert_lokasi, 'KOKI_telepon', $_POST['telepon'], false );
        add_post_meta( $insert_lokasi, 'KOKI_pengirim', $_POST['pengirim'], false );
        add_post_meta( $insert_lokasi, 'KOKI_telepon_pengirim', $_POST['telepon_pengirim'], false );
        add_post_meta( $insert_lokasi, 'KOKI_email_pengirim', $_POST['email'], false );
        add_post_meta( $insert_lokasi, 'KOKI_twitter_pengirim', $_POST['twitter'], false );
        if(isset($_POST['kategori_lainnya']) && $_POST['kategori_lainnya']!='')
        	add_post_meta( $insert_lokasi, 'KOKI_kategori_lainnya', $_POST['kategori_lainnya'], false );

        if ( $_FILES ) {
            $files = $_FILES["koki_handle_attachment"];  
            foreach ($files['name'] as $key => $value) {             
                if ($files['name'][$key]) {
                    $file = array(
                        'name' => $files['name'][$key],
                        'type' => $files['type'][$key],
                        'tmp_name' => $files['tmp_name'][$key],
                        'error' => $files['error'][$key],
                        'size' => $files['size'][$key]
                    );
                    $_FILES = array ("koki_handle_attachment" => $file);
                    foreach ($_FILES as $file => $array) {                
                        $newupload = koki_handle_attachment($file,$insert_lokasi, true);
                        // print_r($newupload);
                        // echo "<hr/>";
                    }
                }
            }
        }   

    	$preview_posts = get_option('simple_preview');
    	$preview_posts[$insert_lokasi] = true;
    	update_option('simple_preview', $preview_posts); 
    	tambah_lokasi_redirect('http://www.petabdg.com/tambah-lokasi/?nuhun=' . $insert_lokasi);
    }
}

 get_header();
 global $woo_options;
?>
<script type="text/javascript">
jQuery(document).ready(function(){
    // jQuery("#tambah_lokasi").validate({
    //     rules: {
    //         title: "required",
    //         title: "required",
    //         map: "required",
    //         pengirim: "required"
    //     },
    //     messages: {
    //         nama: "Nama Lokasi belum diisi",
    //         bukalah: "Anda belum mengisi kode kunci (kode area bandung)",
    //         map: "Tentukan lokasi pada peta",
    //         pengirim: "Nama Pengirim belum diisi"
    //     }
    // });
    var container = jQuery('div.container');
    // validate the form when it is submitted
    var validator = jQuery("#tambah_lokasi").validate({
        errorContainer: container,
        errorLabelContainer: jQuery("ol", container),
        wrapper: 'li'
    });
    <?php 
    if('POST' == $_SERVER['REQUEST_METHOD'] && (!isset($_POST['bukalah']) || ($_POST['bukalah']!='022'))) {
    echo 'jQuery("div#peringatan").show();';
    }
    ?>
	jQuery("select.cat").change(function () { 
        var value = jQuery(this).val();
			if(value=='16')
				jQuery('.kategori_lainnya').prop('disabled', false);
			else
				jQuery('.kategori_lainnya').val('').prop('disabled', true);
		});
	});
</script>
<style type="text/css">
#contact-page ol.forms textarea {
    height: 100px!important;
    width: 40%!important;
}
#contact-page ol.forms label {
    width: 110px!important;
}
label.error {
    display: none!important;
}
div.container { display: none }
div.container {
    background-color: #eee;
    border: 1px solid red;
    margin: 5px;
    padding: 5px;
}
div.container ol li {
    list-style-type: disc;
    margin-left: 20px;
}
div.container { display: none }
.container label.error {
    display: inline;
}
input.error, textarea.error {
    border: 1px dotted #FF0000 !important;
}
</style>
    <!-- #content Starts -->
	<?php woo_content_before(); ?>
    <div id="content" class="col-full">

    	<div id="main-sidebar-container">

            <!-- #main Starts -->
            <?php woo_main_before(); ?>

            <section id="main" class="col-left">
            <div id="peringatan" style="color:red;font-weight:bold;text-align:center;border: 2px solid #F00;display:none;padding:10px;margin-bottom:20px">Anda belum mengisi kode kunci (kode area bandung, yaitu: 022)</div>
			<?php 
			if(isset($_GET['nuhun']) AND $_GET['nuhun']!='') {
				echo do_shortcode('[box]<p style="text-align:center">Terimakasih atas informasi lokasi yang telah anda tambah.<br/>
				Akan segera kami publish setelah diverifikasi.<br/>
				Preview lokasi bisa dicek di link ini:<br/>
				( <a href="'.get_permalink($_GET['nuhun']).'&preview=true" target="_blank" style="font-weight:bold">'.get_the_title($_GET['nuhun']).'</a> )<br/>
				Bila anda akan menambah lokasi lainnya, silakan isi form ini kembali.<br/>
				(^_^)</p>[/box]'); 
			}
			?>
			
            <div id="contact-page" class="page">
			<?php //get_template_part( 'loop', 'blog' ); ?>
			<form action="" id="tambah_lokasi" method="post" enctype="multipart/form-data" name="front_end_upload" >
 
			
			 <ol class="forms">
			 	<li><label>Cari Lokasi</label>
					<div class="cmb-type-pw_map" >

					<input type="text" size="60" class="map-search" />
					<div class="map"  id="map"></div>
					<input type="hidden" class="latitude" name="map[latitude]" value="" />
					<input type="hidden" class="longitude" name="map[longitude]" value="" />
					
					</div>
                </li>
				<li><label for="koki_file">Foto</label><input type="file" id="koki_file" name="koki_handle_attachment[]"  ></li>

                <li><label for="title">Nama Lokasi</label>
                    <input type="text" name="title" id="title" value="" class="txt requiredField" minlength="2" required />
                </li>

                <li><label>Kategori</label><?php wp_dropdown_categories( 'tab_index=10&taxonomy=kategori-lokasi&hide_empty=0&hide_if_empty=0&orderby=SLUG&order=ASC&class=cat' ); ?> 
                <input type="text" name="kategori_lainnya" id="kategori_lainnya" value="" class="kategori_lainnya txt requiredField" disabled="true" /></li>

                <li class="textarea"><label for="alamat">Alamat</label>
                    <textarea name="alamat" id="alamat" rows="10" cols="20" class="requiredField" required></textarea>
                </li>

                <li><label for="website">Website</label>
                    <input type="text" name="website" id="website" value="" class="txt requiredField" />
                </li>

                <li><label for="telepon">Telepon</label>
                    <input type="text" name="telepon" id="telepon" value="" class="txt requiredField" />
                                                </li>

                <li class="textarea"><label for="description">Informasi Lainnya</label>
                    <textarea name="description" id="description" rows="10" cols="20" class="requiredField"></textarea>
                </li>
                

                <!-- <li><label for="mathCheck">Solve: 3 + 6</label>
                    <input type="text" name="mathCheck" id="mathCheck" value="" class="txt requiredField math" />
                                                </li> -->
				

                <li><label for="pengirim">Nama Pengirim</label>
                    <input type="text" name="pengirim" id="pengirim" value="" class="txt requiredField" required />
                                                </li>


                <li><label for="email">Email Pengirim</label>
                    <input type="text" name="email" id="email" value="" class="txt requiredField" required />
                                                </li>

                <li><label for="telepon_pengirim">Telepon Pengirim</label>
                    <input type="text" name="telepon_pengirim" id="telepon_pengirim" value="" class="txt requiredField" required />
                                                </li>

                <li><label for="twitter">Twitter Pengirim</label>
                    <input type="text" name="twitter" id="twitter" value="" class="txt requiredField" />
                                                </li>

                <li><label for="bukalah">Kode Kunci</label>
                    <input type="text" name="bukalah" id="bukalah" value="" class="txt requiredField" required/>
                     <small>Kode kunci wajib diisi dengan kode telp area Kota Bandung.</small>
                                                </li>
                <li>
            <div class="container">
                <h4>Ada form yang belum diisi!</h4>
            </div></li>
                <li class="screenReader"><label for="checking" class="screenReader">If you want to submit this form, do not enter anything in this field</label><input type="text" name="checking" id="checking" class="screenReader" value="" /></li>
                <li class="buttons"><input type="hidden" name="submitted" id="submitted" value="true" /><input class="submit button" type="submit" value="Submit" /></li>

				<?php wp_nonce_field( 'lokasi-baru' ); ?>
            </ol>
			</form>
            </div>
            </section><!-- /#main -->
            <?php woo_main_after(); ?>

            <?php get_sidebar(); ?>

		</div><!-- /#main-sidebar-container -->

		<?php get_sidebar( 'alt' ); ?>

    </div><!-- /#content -->
	<?php woo_content_after(); ?>

<?php get_footer(); ?>