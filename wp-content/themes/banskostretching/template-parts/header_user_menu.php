<?php if (is_user_logged_in()) : ?>
  <div class="header-user-menu">
    <div class="header-user-menu__avatar js-header-user-menu-avatar"></div>

    <div class="header-user-menu__dropdown js-header-user-menu-dropdown">
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