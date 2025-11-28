<?php
add_filter('woocommerce_breadcrumb_defaults', function ($defaults) {
  return array(
    'delimiter'   => ' / ',
    'wrap_before' => '<nav class="breadcrumbs" itemprop="breadcrumb">',
    'wrap_after'  => '</nav>',
    'before'      => '',
    'after'       => '',
    'home'        => 'Home',
  );
});
