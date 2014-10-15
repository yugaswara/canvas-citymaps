jQuery(document).ready(function(jQuery){
 
jQuery('#image_button').click(function(e) {
		e.preventDefault();
		frame = wp.media({
			title : 'Add Image(s)',
			frame: 'post',
			multiple : true, // set to false if you want only one image
			library : { type : 'image'},
			button : { text : 'Add Image' },
		});
		frame.on('close',function(data) {
				var imageArray = [];
				images = frame.state().get('selection');
				images.each(function(image) {
					// console.log(image.attributes);
					imageArray.push(image.attributes.url); // want other attributes? Check the available ones with console.log(image.attributes);
				});
			 
				jQuery("#imageurls").val(imageArray.join(",")); // Adds all image URL's comma seperated to a text input
			});
	 	frame.open()
	});
});