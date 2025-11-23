<?php defined('ABSPATH') || exit;

do_action('woocommerce_auth_page_header'); ?>

<h1>
  <?php
  /* translators: %s: app name */
  printf(esc_html__('%s would like to connect to your store', 'woocommerce'), esc_html($app_name));
  ?>
</h1>

<?php wc_print_notices(); ?>

<p>
  <?php
  /* translators: %1$s: app name, %2$s: URL */
  echo wp_kses_post(sprintf(__('To connect to %1$s you need to be logged in. Log in to your store below, or <a href="%2$s">cancel and return to %1$s</a>', 'woocommerce'), esc_html(wc_clean($app_name)), esc_url($return_url)));
  ?>
</p>

<form method="post" class="wc-auth-login">
  <p class="form-row form-row-wide">
    <label for="username"><?php esc_html_e('Username or email address', 'woocommerce'); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e('Required', 'woocommerce'); ?></span></label>
    <input type="text" class="input-text" name="username" id="username" value="<?php echo (! empty($_POST['username'])) ? esc_attr($_POST['username']) : ''; ?>" required aria-required="true" />
  </p>

  <p class="form-row form-row-wide">
    <label for="password"><?php esc_html_e('Password', 'woocommerce'); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e('Required', 'woocommerce'); ?></span></label>
    <input class="input-text" type="password" name="password" id="password" required aria-required="true" />
  </p>

  <p class="wc-auth-actions">
    <?php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce'); ?>

    <button type="submit" class="button button-large button-primary wc-auth-login-button<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>" name="login" value="<?php esc_attr_e('Login', 'woocommerce'); ?>"><?php esc_html_e('Login', 'woocommerce'); ?></button>
    <input type="hidden" name="redirect" value="<?= esc_url($redirect_url); ?>" />
  </p>
</form>

<?php do_action('woocommerce_auth_page_footer'); ?>