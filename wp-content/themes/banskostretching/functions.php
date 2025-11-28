<?php

/**
 * banskostretching functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package banskostretching
 */

if (! defined('_S_VERSION')) {
  // Replace the version number of the theme on each release.
  define('_S_VERSION', '2.0.0');
}

// Подключаем модули
require_once get_template_directory() . '/includes/class-video-manager.php';

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function banskostretching_setup()
{
  /*
    * Make theme available for translation.
    * Translations can be filed in the /languages/ directory.
    * If you're building a theme based on banskostretching, use a find and replace
    * to change 'banskostretching' to the name of your theme in all the template files.
    */
  load_theme_textdomain('banskostretching', get_template_directory() . '/languages');

  // Add default posts and comments RSS feed links to head.
  add_theme_support('automatic-feed-links');

  /*
    * Let WordPress manage the document title.
    * By adding theme support, we declare that this theme does not use a
    * hard-coded <title> tag in the document head, and expect WordPress to
    * provide it for us.
    */
  add_theme_support('title-tag');

  /*
    * Enable support for Post Thumbnails on posts and pages.
    *
    * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
    */
  add_theme_support('post-thumbnails');

  // This theme uses wp_nav_menu() in one location.
  register_nav_menus(
    array(
      'primary' => esc_html__('Primary', 'banskostretching'),
      'header-user' => esc_html__('Header User Menu', 'banskostretching'),
      'footer_menu' => __('Footer Menu', 'banskostretching'),
    )
  );

  /*
    * Switch default core markup for search form, comment form, and comments
    * to output valid HTML5.
    */
  add_theme_support(
    'html5',
    array(
      'search-form',
      'comment-form',
      'comment-list',
      'gallery',
      'caption',
      'style',
      'script',
    )
  );

  // Set up the WordPress core custom background feature.
  add_theme_support(
    'custom-background',
    apply_filters(
      'banskostretching_custom_background_args',
      array(
        'default-color' => 'ffffff',
        'default-image' => '',
      )
    )
  );

  // Add theme support for selective refresh for widgets.
  add_theme_support('customize-selective-refresh-widgets');

  /**
   * Add support for core custom logo.
   *
   * @link https://codex.wordpress.org/Theme_Logo
   */
  add_theme_support(
    'custom-logo',
    array(
      'height'      => 250,
      'width'       => 250,
      'flex-width'  => true,
      'flex-height' => true,
    )
  );

  // Add WooCommerce support
  add_theme_support('woocommerce');

  // Поддержка PayPal плагина
  add_theme_support('wc-product-gallery-zoom');
  add_theme_support('wc-product-gallery-lightbox');
  add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'banskostretching_setup');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function banskostretching_content_width()
{
  $GLOBALS['content_width'] = apply_filters('banskostretching_content_width', 640);
}
add_action('after_setup_theme', 'banskostretching_content_width', 0);



/**
 * Enqueue scripts and styles.
 */
function banskostretching_scripts()
{
  // wp_enqueue_style('banskostretching-style', get_stylesheet_uri(), array('tailwindcss'), _S_VERSION);
  wp_style_add_data('banskostretching-style', 'rtl', 'replace');

  // wp_enqueue_script('banskostretching-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true);

  // Подключаем WooCommerce скрипты для корзины
  if (class_exists('WooCommerce')) {
    wp_enqueue_script('wc-add-to-cart');
    wp_enqueue_script('woocommerce');
    wp_enqueue_script('wc-cart-fragments');
  }

  if (is_singular() && comments_open() && get_option('thread_comments')) {
    wp_enqueue_script('comment-reply');
  }
}
add_action('wp_enqueue_scripts', 'banskostretching_scripts');

// TODO | front | ref
/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if (defined('JETPACK__VERSION')) {
  require get_template_directory() . '/inc/jetpack.php';
}

// Проверка WooCommerce
function check_woocommerce_status()
{
  if (is_admin() && current_user_can('manage_options')) {
    if (!class_exists('WooCommerce')) {
      add_action('admin_notices', function () {
        echo '<div class="notice notice-error">
                        <p><strong>Ошибка:</strong> WooCommerce не активен!</p>
                      </div>';
      });
    }
  }
}
add_action('admin_init', 'check_woocommerce_status');

// Функции для работы с видео перенесены в класс BanskoStretching_Video_Manager
// Глобальные функции для обратной совместимости подключаются автоматически



/**
 * Отключение стилей WooCommerce
 */
function banskostretching_disable_woocommerce_styles()
{
  // Отключаем основные стили WooCommerce
  wp_dequeue_style('woocommerce-general');
  wp_dequeue_style('woocommerce-layout');
  wp_dequeue_style('woocommerce-smallscreen');

  // Опционально: отключить стили для блоков WooCommerce
  // wp_dequeue_style('wc-blocks-style');
}

add_action('wp_enqueue_scripts', 'banskostretching_disable_woocommerce_styles', 100);

/**
 * Изменение классов body для страницы магазина WooCommerce
 */
function banskostretching_custom_shop_body_class($classes)
{
  // Проверяем, что это страница WooCommerce
  if (is_shop() || is_product_category() || is_product_tag()) {
    // Удаляем стандартные классы WooCommerce
    $classes = array_diff($classes, ['woocommerce', 'woocommerce-page']);

    // Добавляем свои классы
    $classes[] = 'products-page';
  }

  if (is_product()) {
    // Удаляем стандартные классы WooCommerce
    $classes = array_diff($classes, ['woocommerce', 'woocommerce-page']);

    // Добавляем свои классы
    $classes[] = 'product-page';
  }

  return $classes;
}
add_filter('body_class', 'banskostretching_custom_shop_body_class');


/**
 * Удаление поля billing_address_2 из всех форм WooCommerce
 */
function banskostretching_remove_billing_address_2($fields)
{
  // Удаляем поле адреса 2 (квартира/офис)
  unset($fields['billing_address_2']);

  return $fields;
}
add_filter('woocommerce_billing_fields', 'banskostretching_remove_billing_address_2');

/**
 * Удаление поля billing_address_2 из чекаута
 */
function banskostretching_remove_checkout_address_2($fields)
{
  // Удаляем из billing
  unset($fields['billing']['billing_address_2']);

  // Также можно удалить из shipping, если нужно
  // unset($fields['shipping']['shipping_address_2']);

  return $fields;
}
add_filter('woocommerce_checkout_fields', 'banskostretching_remove_checkout_address_2');

// Add custom class to loop add-to-cart buttons | category page
add_filter('woocommerce_loop_add_to_cart_args', function ($args, $product) {
  if (!isset($args['class'])) $args['class'] = '';

  $args['class'] .= ' btn btn--primary';

  return $args;
}, 10, 2);

include_once('functions-parts/breadcrumbs.php');
include_once('functions-parts/languages.php');
include_once('functions-parts/sidebar.php');
