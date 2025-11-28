<?php defined('ABSPATH') || exit;

get_header();

// current category
$term = get_queried_object(); ?>

<div class="wrap category">
  <div class="category__header">
    <h1 class="category__title"><?= esc_html($term->name); ?></h1>

    <?php if (category_description($term)) : ?>
      <div class="category__description"><?= category_description($term); ?></div>
    <?php endif; ?>
  </div>

  <?php
  // Query products in this category (include subcategories)
  $args = [
    'post_type'      => 'product',
    'posts_per_page' => 12,
    'tax_query'      => [
      [
        'taxonomy' => 'product_cat',
        'field'    => 'term_id',
        'terms'    => $term->term_id,
        'include_children' => true, // Include subcategory products
      ],
    ],
  ];

  $products = new WP_Query($args); ?>

  <div class="products_list">
    <?php if ($products->have_posts()) : ?>
      <?php while ($products->have_posts()) : $products->the_post();
        global $product; ?>

        <div class="products_list__item">
          <a href="<?php the_permalink(); ?>">
            <?php if (has_post_thumbnail()) : ?>
              <div class="product-thumb"><?php the_post_thumbnail('medium'); ?></div>
            <?php endif; ?>

            <h2 class="product-title"><?php the_title(); ?></h2>

            <?php if ($product->get_price()) : ?>
              <div class="product_price"><?= $product->get_price_html(); ?></div>
            <?php endif; ?>
          </a>

          <div class="product_add_to_cart">
            <?php woocommerce_template_loop_add_to_cart(); ?>
          </div>
        </div>
      <?php endwhile; ?>
      <?php wp_reset_postdata(); ?>
    <?php else : ?>
      <p>No products found in this category.</p>
    <?php endif; ?>
  </div>

  <?php
  // Pagination
  the_posts_pagination([
    'prev_text' => __('Prev', 'banskostretchingcom'),
    'next_text' => __('Next', 'banskostretchingcom'),
  ]); ?>
</div>

<?php get_footer(); ?>