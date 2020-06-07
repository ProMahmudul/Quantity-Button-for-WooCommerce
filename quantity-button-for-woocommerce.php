<?php
/**
 * Plugin Name:       Quantity Button for WooCommerce
 * Plugin URI:
 * Description:       A simple and very unique quantity field for WooCommerce. Add quantity field on the archive page.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Mahmudul Hassan
 * Author URI:        mahmudulhassan.me
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       qty-btn-wc
 * Domain Path:       /languages
 */

function qtwc_load_text_domain() {
	load_plugin_textdomain( 'qty-btn-wc', false, dirname( __FILE__ ) . "/languages" );
}

add_action( 'plugins_loaded', 'qtwc_load_text_domain' );

/**
 * Add quantity field on the archive page.
 */
function qtwc_custom_quantity_field_archive() {

	$product = wc_get_product( get_the_ID() );

	if ( ! $product->is_sold_individually() && 'variable' != $product->get_type() && $product->is_purchasable() ) {
		woocommerce_quantity_input( array(
			'min_value' => 1,
			'max_value' => $product->backorders_allowed() ? '' : $product->get_stock_quantity()
		) );
	}

}

add_action( 'woocommerce_after_shop_loop_item', 'qtwc_custom_quantity_field_archive', 0, 9 );


/**
 * Add requires JavaScript.
 */
function qtwc_custom_add_to_cart_quantity_handler() {

	wc_enqueue_js( '
		jQuery( "body .post-type-archive-product" ).on( "click", ".quantity input", function() {
			return false;
		});
		jQuery( "body" ).on( "change input", ".quantity .qty", function() {
			var add_to_cart_button = jQuery( this ).parents( ".product" ).find( ".add_to_cart_button" );
			// For AJAX add-to-cart actions
			add_to_cart_button.attr( "data-quantity", jQuery( this ).val() );
			// For non-AJAX add-to-cart actions
			add_to_cart_button.attr( "href", "?add-to-cart=" + add_to_cart_button.attr( "data-product_id" ) + "&quantity=" + jQuery( this ).val() );
		});
	' );

}

add_action( 'init', 'qtwc_custom_add_to_cart_quantity_handler' );