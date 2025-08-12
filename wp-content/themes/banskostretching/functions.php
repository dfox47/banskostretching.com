<?php // Remove p tags from simple content
remove_filter('the_content', 'wpautop');

// allow menus
add_theme_support('menus');

// allow thumbnails
add_theme_support('post-thumbnails');



function specialWidgets()
{
  register_sidebar(array(
    'after_widget'      => '',
    'before_widget'     => '',
    'class'             => '',
    'name'              => __('Search widget'),
    'id'                => 'search-widget',
  ));
}



// Short title at news pages | archive
add_filter('get_the_archive_title', function ($title) {
  if (is_category()) {
    $title = single_cat_title('', false);
  }

  return $title;
});



// Woocommerce
// Hide "read more" & "buy it" buttons at category page
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart');

// remove unused CSS
function remove_block_library_css()
{
  wp_dequeue_style('wp-block-library'); // Removes block library CSS
  wp_dequeue_style('wp-block-library-theme'); // Removes block library theme CSS
}

add_action('wp_enqueue_scripts', 'remove_block_library_css');

// support woocommerce
add_theme_support('woocommerce');
