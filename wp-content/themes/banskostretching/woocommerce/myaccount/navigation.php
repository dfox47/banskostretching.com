<?php defined('ABSPATH') || exit;

do_action('woocommerce_before_account_navigation'); ?>

<nav class="account_nav woocommerce-MyAccount-navigation" aria-label="<?php esc_html_e('Account pages', 'woocommerce'); ?>">
  <ul class="account_nav__list">
    <?php foreach (wc_get_account_menu_items() as $endpoint => $label) : ?>
      <li class="account_nav__item">
        <a class="btn btn--link <?= wc_is_current_account_menu_item($endpoint) ? 'active' : ''; ?>" href="<?= esc_url(wc_get_account_endpoint_url($endpoint)); ?>" <?= wc_is_current_account_menu_item($endpoint) ? 'aria-current="page"' : ''; ?>>
          <?= esc_html($label); ?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</nav>

<?php do_action('woocommerce_after_account_navigation'); ?>