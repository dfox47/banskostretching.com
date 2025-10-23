<?php // vars
$themeDir   = '/wp-content/themes/banskostretching';
$i          = $themeDir . '/i';
$iconsDir   = $i . '/icons'; ?>

<!DOCTYPE html>

<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta content="width=device-width, user-scalable=yes, maximum-scale=5" name="viewport">

  <title>Bansko stretching by Nelya</title>

  <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

  <?php wp_head(); ?>

  <?php // favicon
  include_once($themeDir . '/template-parts/favicon.php'); ?>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Lilita+One&family=Ubuntu+Condensed&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="<?= $themeDir; ?>/styles.min.css?v<?= (date("YmdHis")); ?>">

  <meta name="description" content="Discover the benefits of stretching in Bansko city! Enhance your flexibility, improve your health, and enjoy guided sessions in the stunning mountain surroundings. Join our community for expert tips, routines, and wellness advice.">
</head>

<body <?php body_class(); ?>>
  <div class="wrap <?php echo $current_lang = get_locale(); ?>">
    <header class="header">
      <nav class="nav">
        <a class="logo" href="/"></a>

        <?php wp_nav_menu(array(
          'theme_location' => 'primary',
          'menu_class'     => 'nav__menu',
          'container'      => false,
          'fallback_cb'    => false
        )); ?>
      </nav>

      <div class="header_right">
        <a class="button cart_link" href="/cart"><span class="js-img-scroll" data-src="<?= $iconsDir; ?>/cart.svg"></span></a>

        <?php // language change
        include_once('template-parts/lang_change.php'); ?>
      </div>
    </header>
  </div>

  <?php if (is_front_page()) { ?>
    <div class="hero js-img-bg" data-src="<?= $i; ?>/hero.jpg"></div>
  <?php } ?>