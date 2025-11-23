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
  <div class="wrap">
    <header class="header">
      <div class="header__wrap">
        <button class="btn btn--secondary btn--burger js-sidebar-toggle">
          <span><?= __t('menu'); ?></span>
        </button>

        <nav class="nav">
          <a class="logo" href="/"></a>
        </nav>

        <div class="header__actions">
          <?php // cart link
          $lang       = get_bloginfo('language');
          $langShort  = 'en';

          if ($lang == 'ru-RU') {
            $langShort = 'ru';
          } else if ($lang == 'uk') {
            $langShort = 'ua';
          }

          // amount in cart
          $cartCount = WC()->cart->get_cart_contents_count(); ?>

          <a class="btn btn--cart cart_link" href="/<?= $langShort; ?>/cart">
            <span class="cart_link--icon js-img-scroll" data-src="<?= $iconsDir; ?>/cart.svg"></span>
            <?php if ($cartCount > 0) { ?>
              <span class="cart_link--count">
                <?= $cartCount; ?>
              </span>
            <?php } ?>
          </a>

          <?php include_once('template-parts/header_user_menu.php'); ?>
        </div>
      </div>
    </header>
  </div>

  <?php if (is_front_page()) { ?>
    <div class="hero js-img-bg" data-src="<?= $i; ?>/hero.jpg"></div>
  <?php } ?>