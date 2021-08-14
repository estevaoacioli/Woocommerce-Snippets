<?php
/*
*******************************************************
********************* Banner Produto ******************
*******************************************************
*/

add_action( 'admin_init', 'framework_meta_box_add_img_poc' );
 
function framework_meta_box_add_img_poc() {
    add_meta_box('framework_img_poc', // meta box ID
        'Banner Produto', // meta box title
        'framework_image_uploader_field_img_poc', // callback function that prints the meta box HTML
        'product', // post type where to add it
        'normal', // priority
        'default' ); // position
}
 
function framework_image_uploader_field_img_poc() {
    global $post; 
    wp_nonce_field( 'framework_feat_img_poc_nonce', 'framework_feat_img_poc_nonce' );
	$attachment_id = get_post_meta($post->ID, 'framework_img_ids', true);
	
	$image = 'Upload Image';
    $button = 'button';
    $image_size = 'full'; // it would be better to use thumbnail size here (150x150 or so)
    $display = 'none'; // display state of the "Remove image" button
     
    ?>     
    <p><i>Selecione uma imagem para o banner do produto</i><br>
	<i style="color:red">Atenção: A imagem será exibida no seu tamanho real no frontend!</i></p> 
    <div class="img-screenshot clear">
        <?php        
            
            $img = wp_get_attachment_image_src($attachment_id, 'thumbnail');
			//var_dump($img);
			if($img){
            echo '<div class="screen-thumb" id="attachment_id_'.$attachment_id.'"><img src="' . esc_url($img[0]) . '" /><span class="remove-attachment dashicons dashicons-trash" data-id="'.$attachment_id.'" title="Remover imagem"></span></div>';
            }
        
        ?>		
    </div>
	<input id="framework_img_ids" type="hidden" name="framework_img_ids" class="img_values" value="<?php echo esc_attr($attachment_id); ?>">
    <input id="edit-img" class="button upload_img_button" type="button"  value="<?php esc_html_e('Adicionar Imagem', 'framework') ?>"/> 
	<h3>Como usar</h3>
	<p>Cole o Shortcode <b>[show-banner-produto]</b> em qualquer lugar da página do produto</p>
	
    
   
<?php   
}
 
/*
 * Save Meta Box data
 */
add_action('save_post', 'framework_img_poc_save');
 
function framework_img_poc_save( $post_id ) {
     
    if ( !isset( $_POST['framework_feat_img_poc_nonce'] ) ) {
        return $post_id;
    }
     
    if ( !wp_verify_nonce( $_POST['framework_feat_img_poc_nonce'], 'framework_feat_img_poc_nonce') ) {
        return $post_id;
    } 
     
    if ( isset( $_POST[ 'framework_img_ids' ] ) ) {
        update_post_meta( $post_id, 'framework_img_ids', esc_attr($_POST['framework_img_ids']) );
    } else {
        update_post_meta( $post_id, 'framework_img_ids', '' );
    }
     
}

/*
* Ajax Show Galeria *
*/
function framework_img_show_poc(){
	//$list = 'fa fas far fal fad fab ';	
	$attachment_id = $_POST['attachment_id'];
	
	
	if( !empty($attachment_id) ){
		
		$img = wp_get_attachment_image_src($attachment_id, 'thumbnail'); 
		
        echo '<div class="screen-thumb" id="attachment_id_'.$attachment_id.'"><img src="' . esc_url($img[0]) . '" /><span class="remove-attachment dashicons dashicons-trash" data-id="'.$attachment_id.'" title="Remover imagem"></span></div>';
		
	}      
		
	exit;
}

add_action('wp_ajax_framework_img_show_poc', 'framework_img_show_poc'); // for logged in user
add_action('wp_ajax_framework_img_show_poc', 'framework_img_show_poc'); // if user not logged in

/* CSS and JS */
function framework_img_css_stuff() {
    global $pagenow;
	global $typenow ;
    $currentScreen = get_current_screen();
    $var = $typenow;
    if ( $var === 'product' ) {
	?>

    <style type="text/css">
		.gallery-screenshot{
			display: block;
			padding: 20px 0;
			position: relative;
		}
		.screen-thumb{
			display: inline-block;
			margin: 10px;
			position: relative;
		}
		.remove-attachment{
			color: #ff6565;
			position: absolute;
			top: 5px;
			left: 5px;
			cursor: pointer;
			background: #fff;
			padding: 5px;
			display: none;
		}
		.screen-thumb:hover .remove-attachment{
			display: inline-block;
		}
		.input-large{
			width: 100%;
			max-width: 600px;
		}
    </style>

    <?php
	}
}
add_action( 'admin_head', 'framework_img_css_stuff' );

// Function to render 
function framework_img_js_stuff() {
?>    
    <script type="text/javascript">
		jQuery(document).ready(function ($) {			
			var formfield;    
			var divpreview;    
			var frame;    
			$('body').on('click', '.upload_img_button', function () {     console.log('aaa');  
				/* event.preventDefault(); */        
				formfield = $(this).prev('input'); /* The input field that will hold the uploaded file url */        
				divpreview = $(this).prev('.divpreview');        /* If the media frame already exists, reopen it. */        
				if (frame) {   frame.open();   return;  }        
				/*  Create a new media frame */  
				frame = wp.media({            
					title: 'Select an image',            
					button: {                
						text: 'Use this media'            
						},            
					multiple: false, /*  Set to true to allow multiple files to be selected */            
					editable: true,
					library: {                
						type: 'image'  /* HERE IS THE MAGIC. Set your own post ID var                        
						uploadedTo : wp.media.view.settings.post.id */            }        
					});        /* When an image is selected in the media frame... */        
				
				frame.on('select', function () {    /* Get media attachment details from the frame state */            
					var attachment = frame.state().get('selection').first().toJSON();            
						console.log(attachment);		
					var attachment_id = attachment.id;						
					if(attachment_id){
						
						$.ajax({					   
							 url: ajaxurl,					   
							 type:'POST',					   
							 data: {							
								'action': 'framework_img_show_poc',							
								'attachment_id': attachment_id	
							 },							
							 success:function(html){	
									$(formfield).val(attachment_id);
									$(".img-screenshot").empty();									
									$(".img-screenshot").append(html);																										
									}					  					 
							 });					
							 return false;							
					}						     
				});        /*  Finally, open the modal on click */        
				frame.open();    
			});	
			/*  Remover imagem da galeria */	
			$('body').on('click', '.remove-attachment', function () {		
				var removeImg = $(this).data('id'); 	
				$(".img-screenshot").empty();
				$("#framework_img_ids").val('');
				alert('Para efetivar esta alteração você deve salvar o post novamente!');
			});	
		});
    </script>   
<?php
}
add_action( 'admin_footer', 'framework_img_js_stuff' ); // For back-end

function framework_show_banner_produto() { 
	global $post;
    ob_start();	
	$attachment_id = get_post_meta($post->ID, 'framework_img_ids', true);	
	
	if($attachment_id){
		$img = wp_get_attachment_image_src($attachment_id, 'full');
		$image_url = esc_url($img[0]);
?>
	
	<div id="banner-master-product" class="banner-product" style="background-image: url('<?php echo $image_url; ?>');">	
	</div>
	<?php
	} else {
		echo '<!--  Banner Not Found -->';
	}
	
/* Get the buffered content into a var */
    $retorno = ob_get_contents();
    /* Clean buffer */
    ob_end_clean();
    /* Return the content as usual */
    return $retorno;   
}
add_shortcode('show-banner-produto', 'framework_show_banner_produto');
