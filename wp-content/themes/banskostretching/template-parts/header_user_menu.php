<?php
// lang link
$lang       = get_bloginfo('language');
$langShort  = '';

if ($lang == 'en-US') {
  $langShort = 'en/';
} else if ($lang == 'uk') {
  $langShort = 'ua/';
}

if (is_user_logged_in()) : ?>
  <div class="header-user-menu">
    <button class="header-user-menu__avatar js-menu-toggle"></button>

    <div class="header-user-menu__dropdown js-menu-toggle">
      <?php wp_nav_menu(array(
        'theme_location' => 'header-user',
        'menu_class'     => 'nav__menu',
        'container'      => false,
        'fallback_cb'    => false
      )); ?>
    </div>
  </div>
<?php else : ?>
  <a href="/<?= $langShort; ?>my-account/" class="btn btn--secondary"><?= __t('sign_in'); ?></a>
<?php endif; ?>