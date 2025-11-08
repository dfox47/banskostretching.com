<?php // vars
$themeDir   = '/wp-content/themes/banskostretching';
$i          = $themeDir . '/i';
$iconsDir   = $i . '/icons'; ?>

<aside class="sidebar js-sidebar">
  <div class="sidebar__overlay js-sidebar-close"></div>

  <div class="sidebar__content">
    <div class="sidebar__header">
      <button class="sidebar__close btn btn--close js-sidebar-close"></button>
    </div>

    <nav class="sidebar__nav">
      <?php wp_nav_menu(array(
        'theme_location' => 'primary',
        'menu_class'     => 'nav__menu',
        'container'      => false,
        'fallback_cb'    => false
      )); ?>
    </nav>
  </div>
</aside>