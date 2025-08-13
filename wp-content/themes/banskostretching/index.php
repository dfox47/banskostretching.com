<?php // vars
$themeDir   = '/wp-content/themes/banskostretching';
$i          = $themeDir . '/i';
$iconsDir   = $i . '/icons'; ?>

<?php get_header(); ?>

<div class="hero js-img-bg" data-src="<?= $i; ?>/hero.jpg"></div>

<div class="wrap index.php">
  <h1>Bansko stretching</h1>

  <div class="content-area">
    <?php
    while (have_posts()) : the_post();
      the_content();
    endwhile;
    ?>
  </div>

  <div class="greetings">
    <p>Привет!<br>Меня зовут Неля и я готова стать твоим проводником в прекрасный мир стретчинга</p>

    <p><span class="js-img-scroll" data-src="<?= $iconsDir; ?>/favicon/favicon.svg"></span></p>
  </div>

  <?php // reviews
  include('template-parts/reviews.php'); ?>

  <?php // instagram plugin [START]
  ?>
  <div class="ig_wrap">
    <script src="//static.elfsight.com/platform/platform.js" data-use-service-core defer></script>
    <div class="elfsight-app-7bc4a5c0-741f-4916-b5ae-90bb8549b8fb" data-elfsight-app-lazy></div>
  </div>
  <?php // instagram plugin [END]
  ?>
</div>

<?php get_footer(); ?>