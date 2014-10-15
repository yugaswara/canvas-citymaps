<?php

	add_action( 'admin_enqueue_scripts', 'scripts'  );

	add_action( 'kategori-lokasi_add_form_fields', 'add_thumbnail_field', 10, 1  );
	add_action( 'kategori-lokasi_edit_form_fields','edit_thumbnail_field' , 10, 2 );

	add_action( 'created_term', 'thumbnail_field_save' , 10, 3 );
	add_action( 'edit_term', 'thumbnail_field_save' , 10, 3 );


    /**
     * scripts function.
     *
     * @access public
     * @return void
     */
    function scripts() {
    	$screen = get_current_screen();

	    if ( in_array( $screen->id, array( 'edit-kategori-lokasi' ) ) ) {
			wp_enqueue_media();
		}
    }

	/**
	 * Category thumbnails
	 */
	function add_thumbnail_field($taxonomy) {
		$tax 	= get_taxonomy($taxonomy);
		?>
		<div class="form-field">
			<label><?php _e( 'Thumbnail', 'wc_brands' ); ?></label>
			<div id="product_cat_thumbnail" style="float:left;margin-right:10px;"><img src="<?php echo get_stylesheet_directory_uri(); ?>/placeholder.png" width="60px" height="60px" /></div>
			<div style="line-height:60px;">
				<input type="hidden" id="product_cat_thumbnail_id" name="product_cat_thumbnail_id" />
				<button type="submit" class="upload_image_button button"><?php _e('Upload/Add image', 'wc_brands'); ?></button>
				<button type="submit" class="remove_image_button button"><?php _e('Remove image', 'wc_brands'); ?></button>
			</div>
			<script type="text/javascript">
			var tax = '<?php echo $tax->label; ?>';
				jQuery(function(){

					 // Only show the "remove image" button when needed
					 if ( ! jQuery('#product_cat_thumbnail_id').val() )
						 jQuery('.remove_image_button').hide();

					// Uploading files
					var file_frame;

					jQuery(document).on( 'click', '.upload_image_button', function( event ){

						event.preventDefault();

						// If the media frame already exists, reopen it.
						if ( file_frame ) {
							file_frame.open();
							return;
						}

						// Create the media frame.
						file_frame = wp.media.frames.downloadable_file = wp.media({
							title: 'Pilih Foto',
							button: {
								text: 'Gunakan Foto',
							},
							multiple: false
						});

						// When an image is selected, run a callback.
						file_frame.on( 'select', function() {
							attachment = file_frame.state().get('selection').first().toJSON();

							jQuery('#product_cat_thumbnail_id').val( attachment.id );
							jQuery('#product_cat_thumbnail img').attr('src', attachment.url );
							jQuery('.remove_image_button').show();
						});

						// Finally, open the modal.
						file_frame.open();
					});

					jQuery(document).on( 'click', '.remove_image_button', function( event ){
						jQuery('#product_cat_thumbnail img').attr('src', '<?php echo get_stylesheet_directory_uri(); ?>/placeholder.png');
						jQuery('#product_cat_thumbnail_id').val('');
						jQuery('.remove_image_button').hide();
						return false;
					});
				});

			</script>
			<div class="clear"></div>
		</div>
		<?php
	}

	function edit_thumbnail_field( $term, $taxonomy ) {
		$tax 	= get_taxonomy($taxonomy);

		$image 			= '';
		$thumbnail_id 	= get_metadata( 'post', $term->term_id, 'thumbnail_id', true );
		
		if ($thumbnail_id) :
			$image = wp_get_attachment_url( $thumbnail_id );
		else :
			$image = get_stylesheet_directory_uri() . '/placeholder.png';
		endif;
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php _e('Thumbnail', 'wc_brands'); ?></label></th>
			<td>
				<div id="product_cat_thumbnail" style="float:left;margin-right:10px;"><img src="<?php echo $image; ?>" width="60px" height="60px" /></div>
				<div style="line-height:60px;">
					<?php 

		// $tax 	= get_taxonomy($taxonomy, ARRAY_A);
		// echo '<pre>';
		// print_r($tax);
		// echo '<hr>';
		// $term 	= get_term ($term->term_id, $taxonomy, ARRAY_A);
		// print_r($term);
		// echo '</pre>';
					 ?>
					<input type="hidden" id="product_cat_thumbnail_id" name="product_cat_thumbnail_id" value="<?php echo $thumbnail_id; ?>" />
					<button type="submit" class="upload_image_button button"><?php _e('Upload/Add image', 'wc_brands'); ?></button>
					<button type="submit" class="remove_image_button button"><?php _e('Remove image', 'wc_brands'); ?></button>
				</div>
				<script type="text/javascript">

					var tax = '<?php echo $tax->label; ?>';
					
					jQuery(function(){

						 // Only show the "remove image" button when needed
						 if ( ! jQuery('#product_cat_thumbnail_id').val() )
							 jQuery('.remove_image_button').hide();

						// Uploading files
						var file_frame;

						jQuery(document).on( 'click', '.upload_image_button', function( event ){

							event.preventDefault();

							// If the media frame already exists, reopen it.
							if ( file_frame ) {
								file_frame.open();
								return;
							}

							// Create the media frame.
							file_frame = wp.media.frames.downloadable_file = wp.media({
								title: 'Pilih Foto',
								button: {
									text: 'Gunakan Foto',
								},
								multiple: false
							});

							// When an image is selected, run a callback.
							file_frame.on( 'select', function() {
								attachment = file_frame.state().get('selection').first().toJSON();

								jQuery('#product_cat_thumbnail_id').val( attachment.id );
								jQuery('#product_cat_thumbnail img').attr('src', attachment.url );
								jQuery('.remove_image_button').show();
							});

							// Finally, open the modal.
							file_frame.open();
						});

						jQuery(document).on( 'click', '.remove_image_button', function( event ){
							jQuery('#product_cat_thumbnail img').attr('src', '<?php echo get_stylesheet_directory_uri(); ?>/placeholder.png');
							jQuery('#product_cat_thumbnail_id').val('');
							jQuery('.remove_image_button').hide();
							return false;
						});
					});

				</script>
				<div class="clear"></div>
			</td>
		</tr>
		<?php
	}

	function thumbnail_field_save( $term_id, $tt_id, $taxonomy ) {

		if ( isset( $_POST['product_cat_thumbnail_id'] ) )
			update_metadata( 'post', $term_id, 'thumbnail_id', $_POST['product_cat_thumbnail_id'], '' );
		
	}

