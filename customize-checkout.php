<?php
/*
***** Woocommerce Customize Checkout ******
*/

// Remove load embedded terms from checkout
function eas_code_woocommerce_checkout_terms_and_conditions() {
  remove_action( 'woocommerce_checkout_terms_and_conditions', 'wc_terms_and_conditions_page_content', 30 );
}
add_action( 'wp', 'eas_code_woocommerce_checkout_terms_and_conditions' );