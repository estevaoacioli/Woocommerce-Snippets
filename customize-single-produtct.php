<?php
/*
***** Woocommerce Customize Single Product ******
*/

//Remove zoom effect - magnification on product image
function remove_image_zoom_support() {
	remove_theme_support( 'wc-product-gallery-zoom' );
}
add_action( 'wp', 'remove_image_zoom_support', 100 );

//Remove zoom effect - lightbox on product image
add_action( 'after_setup_theme', 'remove_wc_gallery_lightbox', 100 );
function remove_wc_gallery_lightbox() {
	remove_theme_support( 'wc-product-gallery-lightbox' );
}