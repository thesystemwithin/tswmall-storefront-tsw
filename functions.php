<?php

function tsw_init() {
	add_action('wp_enqueue_scripts', 'tsw_enqueue_styles');
	add_filter('loop_shop_columns', 'woocommerce_product_list_columns', 999);
	
	woocommerce_product_list_reorganize();
	woocommerce_product_details_reorganize();
	storefront_products_pagination_reorganize();
}
add_action('init', 'tsw_init');

function tsw_enqueue_styles() {
	wp_enqueue_style('dashicons');
	wp_enqueue_style('storefront', get_template_directory_uri() . '/style.css');
	wp_enqueue_style('child-style',
		get_stylesheet_directory_uri() . '/style.css',
		array('storefront'),
		wp_get_theme()->get('Version')
	);
}

function woocommerce_product_list_columns() {
	return 5;
}

function woocommerce_product_list_reorganize() {
	/*add_action('woocommerce_before_shop_loop_item', 'tsw_product_show_auction_timer', 20);
	add_action('woocommerce_after_shop_loop_item', 'tsw_product_show_auction_placebid', 20);
	//add_action('woocommerce_after_add_to_cart_button', 'tsw_product_show_auction_timer', 20);*/

	add_action('woocommerce_after_shop_loop_item', 'tsw_product_show_auction_small', 20);
}

function woocommerce_product_details_reorganize() {
	//Things we will reorder
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );       //Price
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 ); //Add to cart
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );     //Short description
	
	//The new order (sorted by priority)
	add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 10 );     //Short description
	add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 15 );       //Price
	add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 20 ); //Add to cart
	add_action( 'woocommerce_single_product_summary', 'tsw_product_show_auction', 25 );                //TSWBid auction
}

function storefront_products_pagination_reorganize(){
	//Remove some duplicated pagination actions, where sorting remains at the top and
	//pagination remains at the bottom.
	remove_action('woocommerce_after_shop_loop', 'woocommerce_catalog_ordering', 10);
	remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
	remove_action('woocommerce_before_shop_loop', 'storefront_woocommerce_pagination', 30);
}

function tsw_product_get() {
	return wc_get_product();
}

function tsw_product_get_slug() {
	return tsw_product_get()->get_slug();
}

function tsw_product_get_tswbid() {
	return "https://tswbid.com/auction-product/" . tsw_product_get_slug();
}

function tsw_product_get_tswbid_embed() {
	return tsw_product_get_tswbid() . '/embed';
}

function tsw_product_get_tswbid_embed_small() {
	return tsw_product_get_tswbid() . '/embed-small';
}

function tsw_product_get_tswbid_embed_timer() {
	return tsw_product_get_tswbid() . '/embed-timer';
}

function tsw_product_get_tswbid_embed_placebid() {
	return tsw_product_get_tswbid() . '/embed-placebid';
}

function tsw_show_embed($tag, $url, $width, $height) {
	$tag .= '-' . tsw_product_get_slug();
	//echo '<iframe sandbox="allow-storage-access-by-user-activation allow-scripts allow-same-origin" src="' . $url . '" frameborder=0 style="max-width: ' . $width . ' !important; height: ' . $height . ' !important;"></iframe>';
	echo do_shortcode('[auto-iframe tag="' . $tag . '" link="' . $url . '" width=' . $width . ' height=' . $height . ' autosize=no]');
}

function tsw_product_show_auction() {
	tsw_show_embed("auction", tsw_product_get_tswbid_embed(), "100%", "230px");
}

function tsw_product_show_auction_small() {
	tsw_show_embed("small", tsw_product_get_tswbid_embed_small(), "100%", "80px");
}

function tsw_product_show_auction_timer() {
	tsw_show_embed("timer", tsw_product_get_tswbid_embed_timer(), "100%", "30px");
}

function tsw_product_show_auction_placebid() {
	tsw_show_embed("placebid", tsw_product_get_tswbid_embed_placebid(), "100%", "40px");
}

//Adjust the credits in the footer.
function storefront_credit() {
	?>
	<div class="site-info">
		<p>
			<img src="/wp-content/uploads/2024/10/thesystemwithin-1.png" />
			The System Within <i class="fa-regular fa-copyright"></i> <?php echo gmdate('Y'); ?>
		</p>
		<p>
			<a class="privacy-policy-link" href="/privacy" rel="privacy-policy">Privacy policy</a>
			<span role="separator" aria-hidden="true"></span>
			<a class="privacy-policy-link" href="/terms-conditions" rel="contact">Terms & conditions</a>
			<span role="separator" aria-hidden="true"></span>
			<a class="privacy-policy-link" href="/frequently-asked-questions" rel="contact">FAQ</a>
			<span role="separator" aria-hidden="true"></span>
			<a class="privacy-policy-link" href="/contact" rel="contact">Contact us</a>
		</p>
	</div><!-- .site-info -->
	<?php
}
