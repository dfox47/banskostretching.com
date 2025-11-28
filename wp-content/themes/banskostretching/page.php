<?php get_header(); ?>

<?php
// Создаём массив классов
$main_classes = ['wrap', 'site-main', 'page-template'];

// Добавляем slug страницы
if (is_page()) {
  global $post;
  $main_classes[] = 'page-' . $post->post_name;
  $main_classes[] = 'page-id-' . get_the_ID();
}

// Если главная страница
if (is_front_page()) {
  $main_classes[] = 'front-page';
}

// Преобразуем массив в строку классов
$main_class = implode(' ', $main_classes);
?>

<main id="primary" class="<?= esc_attr($main_class); ?>">
  <?php while (have_posts()) :
    the_post();

    get_template_part('template-parts/content', 'page');

    if (comments_open() || get_comments_number()) :
      comments_template();
    endif;
  endwhile; ?>
</main>

<?php get_sidebar();
get_footer(); ?>