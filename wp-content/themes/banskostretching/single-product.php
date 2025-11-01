<?php get_header(); ?>

<main class="wrap single-product">
  <?php while (have_posts()) : the_post(); ?>
    <?php global $product;

    if (!$product || !is_a($product, 'WC_Product')) {
      $product = wc_get_product(get_the_ID());
    } ?>

    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
      <?php the_title('<h1>', '</h1>'); ?>

      <div class="entry-content">
        <?php if (has_post_thumbnail()) : ?>
          <div class="product-image">
            <?php the_post_thumbnail('large'); ?>
          </div>
        <?php endif; ?>

        <?php global $product;
        if ($product) : ?>
          <p class="product_price"><?= __t('price'); ?>: <?= $product->get_price_html(); ?></p>
        <?php endif; ?>

        <div class="product-description">
          <?php the_content(); ?>
        </div>

        <?php // Отображаем ACF поля курса
        if (function_exists('get_field')) :
          $course_duration = get_field('course_duration');

          if ($course_duration) : ?>
            <div class="course-duration">
              <h3><?= __t('course_duration'); ?></h3>

              <p><?= esc_html($course_duration); ?></p>
            </div>
          <?php endif; ?>

          <?php // Course level
          $course_level = get_field('course_level');

          if ($course_level) :
            $level_labels = array(
              'advanced' => __t('course_level_advanced'),
              'all_levels' => __t('course_level_all_levels'),
              'beginner' => __t('course_level_beginner'),
              'intermediate' => __t('course_level_intermediate')
            );

            $level_text = isset($level_labels[$course_level]) ? $level_labels[$course_level] : $course_level; ?>

            <div class="course-level">
              <h3><?= __t('course_level'); ?></h3>

              <p><?= esc_html($level_text); ?></p>
            </div>
          <?php endif; ?>

          <?php // Что изучите в курсе
          $what_you_learn = get_field('what_you_learn');

          if ($what_you_learn && is_array($what_you_learn)) : ?>
            <div class="what-you-learn">
              <h3>Что вы изучите в курсе</h3>

              <ul>
                <?php foreach ($what_you_learn as $item) : ?>
                  <?php if (isset($item['learning_point']) && !empty($item['learning_point'])) : ?>
                    <li><?= esc_html($item['learning_point']); ?></li>
                  <?php endif; ?>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <?php // Отображаем уроки курса с защищенными видео
          echo display_course_lessons(get_the_ID()); ?>
        <?php endif; // end ACF check
        ?>

        <?php if ($product && $product->is_purchasable()) : ?>
          <div class="product-purchase">
            <form class="cart" method="post">
              <button class="btn btn--primary" type="submit" name="add-to-cart" value="<?= esc_attr($product->get_id()); ?>">
                <?= __t('add_to_cart'); ?>
              </button>
            </form>
          </div>
        <?php endif; ?>
      </div>
    </article>
  <?php endwhile; ?>
</main>

<?php get_sidebar();
get_footer(); ?>