<?php if (is_user_logged_in()) : ?>
  <!-- Logged in user menu -->
  <div class="header-user-menu">
    <div class="header-user-menu__avatar js-header-user-menu-avatar">
    </div>

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
  <!-- Sign In button for non-logged users -->
  <a href="<?= wp_login_url(get_permalink()); ?>" class="b-btn b-btn--small">
    <span>Sign In</span>
  </a>
<?php endif; ?>