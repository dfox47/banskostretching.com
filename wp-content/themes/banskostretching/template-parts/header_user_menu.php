<?php if (is_user_logged_in()) : ?>
  <div class="header-user-menu">
    <button class="header-user-menu__avatar js-menu-toggle"></button>

    <div class="header-user-menu__dropdown">
      <?php wp_nav_menu(array(
        'theme_location' => 'header-user',
        'menu_class'     => 'nav__menu',
        'container'      => false,
        'fallback_cb'    => false
      )); ?>
    </div>
  </div>
<?php else : ?>
  <a href="/my-account/" class="btn btn--secondary"><?= __t('sign_in'); ?></a>
<?php endif; ?>