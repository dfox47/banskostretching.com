<?php
/**
 * Video Manager Class
 * Управление видео контентом для курсов
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class BanskoStretching_Video_Manager {
    
    /**
     * Singleton instance
     */
    private static $instance = null;
    
    /**
     * Get singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_shortcode('vimeo_video', array($this, 'vimeo_video_shortcode'));
        add_action('wp_ajax_verify_video_token', array($this, 'ajax_verify_video_token'));
        add_action('wp_ajax_nopriv_verify_video_token', array($this, 'ajax_verify_video_token'));
        add_action('init', array($this, 'handle_video_proxy'));
        add_action('wp_ajax_get_video_url', array($this, 'ajax_get_video_url'));
        add_action('wp_ajax_nopriv_get_video_url', array($this, 'ajax_get_video_url'));
    }
    
    /**
     * Проверяет есть ли у пользователя доступ к курсу
     */
    public function user_has_course_access($user_id, $product_id) {
        if (!$user_id || !$product_id) {
            return false;
        }
        
        // Получаем все заказы пользователя
        $customer_orders = wc_get_orders(array(
            'customer' => $user_id,
            'status' => array('wc-completed', 'wc-processing'),
            'limit' => -1
        ));
        
        // Проверяем есть ли нужный продукт в заказах
        foreach ($customer_orders as $order) {
            foreach ($order->get_items() as $item) {
                if ($item->get_product_id() == $product_id) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Генерирует временный токен для доступа к видео
     */
    public function generate_video_access_token($user_id, $vimeo_id, $product_id) {
        $timestamp = time();
        $expire_time = $timestamp + (2 * 60 * 60); // Токен действует 2 часа
        
        $data = array(
            'user_id' => $user_id,
            'vimeo_id' => $vimeo_id,
            'product_id' => $product_id,
            'timestamp' => $timestamp,
            'expire' => $expire_time,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        );
        
        // Создаем хэш для защиты от подделки
        $hash = wp_hash(serialize($data) . AUTH_SALT);
        $data['hash'] = $hash;
        
        return base64_encode(json_encode($data));
    }
    
    /**
     * Проверяет токен доступа к видео
     */
    public function verify_video_access_token($token) {
        $decoded = json_decode(base64_decode($token), true);
        
        if (!$decoded || !isset($decoded['hash'])) {
            return false;
        }
        
        // Проверяем срок действия
        if (time() > $decoded['expire']) {
            return false;
        }
        
        // Проверяем IP адрес (опционально)
        if ($decoded['ip'] !== ($_SERVER['REMOTE_ADDR'] ?? '')) {
            return false;
        }
        
        // Проверяем хэш
        $check_data = $decoded;
        unset($check_data['hash']);
        $expected_hash = wp_hash(serialize($check_data) . AUTH_SALT);
        
        if ($decoded['hash'] !== $expected_hash) {
            return false;
        }
        
        // Проверяем доступ пользователя
        if (!$this->user_has_course_access($decoded['user_id'], $decoded['product_id'])) {
            return false;
        }
        
        return $decoded;
    }
    
    /**
     * Отображает Vimeo видео с проверкой доступа
     */
    public function display_vimeo_video($vimeo_id, $product_id, $lesson_title = '', $use_security = false) {
        $user_id = get_current_user_id();
        
        // Проверяем доступ пользователя к курсу
        if (!$this->user_has_course_access($user_id, $product_id)) {
            $product = wc_get_product($product_id);
            $add_to_cart_url = '';
            
            if ($product && $product->is_purchasable()) {
                $add_to_cart_url = wc_get_cart_url() . '?add-to-cart=' . $product_id;
            }
            
            $output = '<div style="background: #ffe0e0; border: 2px solid #ffc0c0; padding: 20px; text-align: center; border-radius: 8px; margin: 20px 0;">
                        <h4>🔒 Доступ ограничен</h4>
                        <p>Для просмотра этого урока необходимо приобрести курс.</p>';
            
            if ($add_to_cart_url) {
                $output .= '<form method="post" style="display: inline-block; margin: 10px 5px;" class="video-add-to-cart-form">
                            <input type="hidden" name="add-to-cart" value="' . esc_attr($product_id) . '" />
                            <button type="submit" class="video-add-to-cart-btn" data-product-id="' . esc_attr($product_id) . '" style="background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">
                                🛒 Добавить в корзину
                            </button>
                        </form>';
            }
            
            $output .= '</div>';
            
            return $output;
        }
        
        
        // Если доступ есть, показываем видео
        if (empty($vimeo_id)) {
            return '<p>Видео временно недоступно.</p>';
        }
        
        $iframe_title = !empty($lesson_title) ? esc_attr($lesson_title) : 'Урок курса';
        
        // Параметры для встраивания Vimeo
        $vimeo_params = array(
            'badge' => 0,
            'autopause' => 0,
            'player_id' => 0,
            'app_id' => 58479,
            'title' => 0,
            'byline' => 0,
            'portrait' => 0,
            'transparent' => 0,
            'responsive' => 1
        );
        
        // Дополнительные параметры безопасности
        if ($use_security) {
            $vimeo_params = array_merge($vimeo_params, array(
                'dnt' => 1, // Do Not Track
                'pip' => 0, // Отключаем Picture-in-Picture
                'keyboard' => 0, // Отключаем горячие клавиши
                'referrer' => urlencode(get_site_url())
            ));
        }
        
        $vimeo_url = 'https://player.vimeo.com/video/' . esc_attr($vimeo_id) . '?' . http_build_query($vimeo_params);
        
        $security_attributes = '';
        $overlay = '';
        $watermark = '';
        
        if ($use_security) {
            // Генерируем токен доступа
            $access_token = $this->generate_video_access_token($user_id, $vimeo_id, $product_id);
            
            $security_attributes = 'data-token="' . esc_attr($access_token) . '" data-user="' . esc_attr($user_id) . '" oncontextmenu="return false;" data-no-copy="true"';
            
            // Защитный оверлей
            $overlay = '<div class="video-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 10; pointer-events: none;"></div>';
            
            // Водяной знак
            $current_user = wp_get_current_user();
            $watermark = '<div class="video-watermark" style="position: absolute; top: 10px; right: 10px; z-index: 15; 
                          background: rgba(0,0,0,0.7); color: white; padding: 5px 10px; font-size: 12px; 
                          border-radius: 3px; pointer-events: none;">
                         Лицензия: ' . esc_html($current_user->user_login) . '
                     </div>';
        }
        
        return '<div class="vimeo-container' . ($use_security ? ' protected-video' : '') . '" ' . $security_attributes . ' style="position: relative; width: 100%; height: 0; padding-bottom: 56.25%; margin: 20px 0;">
                    ' . $overlay . '
                    <iframe src="' . esc_url($vimeo_url) . '" 
                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
                            frameborder="0" 
                            allow="autoplay; fullscreen; picture-in-picture" 
                            allowfullscreen 
                            title="' . $iframe_title . '">
                    </iframe>
                    ' . $watermark . '
                </div>';
    }
    
    /**
     * Отображает все уроки курса
     */
    public function display_course_lessons($product_id, $use_security = false) {
        if (!function_exists('get_field')) {
            return '<p>ACF плагин не активен.</p>';
        }
        
        $course_lessons = get_field('course_lessons', $product_id);
        
        if (!$course_lessons || !is_array($course_lessons)) {
            return '<p>Уроки для этого курса пока не добавлены.</p>';
        }
        
        $output = '<div class="course-lessons"><h3>Уроки курса</h3>';
        
        foreach ($course_lessons as $index => $lesson) {
            if (isset($lesson['lesson_title']) && isset($lesson['vimeo_id'])) {
                $lesson_number = $index + 1;
                $output .= '<div class="lesson-item" style="margin-bottom: 30px;">';
                $output .= '<h4>Урок ' . $lesson_number . ': ' . esc_html($lesson['lesson_title']) . '</h4>';
                
                if (!empty($lesson['lesson_duration'])) {
                    $output .= '<p><em>Длительность: ' . esc_html($lesson['lesson_duration']) . '</em></p>';
                }
                
                $output .= $this->display_vimeo_video($lesson['vimeo_id'], $product_id, $lesson['lesson_title'], $use_security);
                $output .= '</div>';
            }
        }
        
        $output .= '</div>';
        return $output;
    }
    
    /**
     * Шорткод для вставки Vimeo видео
     */
    public function vimeo_video_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => '',
            'product_id' => get_the_ID(),
            'title' => '',
            'security' => 'false'
        ), $atts);
        
        if (empty($atts['id'])) {
            return '<p>Не указан ID видео.</p>';
        }
        
        $use_security = ($atts['security'] === 'true');
        
        return $this->display_vimeo_video($atts['id'], $atts['product_id'], $atts['title'], $use_security);
    }
    
    /**
     * AJAX обработчик для проверки токенов доступа
     */
    public function ajax_verify_video_token() {
        $token = $_POST['token'] ?? '';
        $user_id = $_POST['user_id'] ?? '';
        
        if (empty($token) || empty($user_id)) {
            wp_send_json_error('Недостаточно данных');
        }
        
        // Проверяем, что пользователь авторизован
        if (get_current_user_id() != $user_id) {
            wp_send_json_error('Неавторизованный доступ');
        }
        
        $token_data = $this->verify_video_access_token($token);
        
        if ($token_data) {
            wp_send_json_success('Токен действителен');
        } else {
            wp_send_json_error('Токен недействителен');
        }
    }
    
    /**
     * Логирование попыток несанкционированного доступа
     */
    public function log_security_violation($type, $details = '') {
        $log_entry = array(
            'timestamp' => current_time('mysql'),
            'user_id' => get_current_user_id(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'type' => $type,
            'details' => $details,
            'url' => $_SERVER['REQUEST_URI'] ?? ''
        );
        
        // Сохраняем в опции WordPress (можно заменить на отдельную таблицу)
        $existing_logs = get_option('video_security_logs', array());
        $existing_logs[] = $log_entry;
        
        // Оставляем только последние 100 записей
        if (count($existing_logs) > 100) {
            $existing_logs = array_slice($existing_logs, -100);
        }
        
        update_option('video_security_logs', $existing_logs);
    }
    
    /**
     * Получить безопасный URL для встраивания Vimeo
     */
    public function get_secure_vimeo_embed_url($vimeo_id, $additional_params = array()) {
        $default_params = array(
            'badge' => 0,
            'autopause' => 0,
            'player_id' => 0,
            'app_id' => 58479,
            'dnt' => 1,
            'title' => 0,
            'byline' => 0,
            'portrait' => 0,
            'pip' => 0,
            'speed' => 0, // Отключаем изменение скорости
            'transparent' => 0,
            'responsive' => 1,
            'referrer' => urlencode(get_site_url()),
            'controls' => 1,
            'keyboard' => 0, // Отключаем горячие клавиши
            'fullscreen' => 1,
            'playsinline' => 1
        );
        
        $params = array_merge($default_params, $additional_params);
        
        return 'https://player.vimeo.com/video/' . esc_attr($vimeo_id) . '?' . http_build_query($params);
    }
}

// Инициализируем класс
function banskostretching_video_manager() {
    return BanskoStretching_Video_Manager::get_instance();
}

// Глобальные функции для обратной совместимости
function user_has_course_access($user_id, $product_id) {
    return banskostretching_video_manager()->user_has_course_access($user_id, $product_id);
}

function display_vimeo_video($vimeo_id, $product_id, $lesson_title = '', $use_security = false) {
    return banskostretching_video_manager()->display_vimeo_video($vimeo_id, $product_id, $lesson_title, $use_security);
}

function display_course_lessons($product_id, $use_security = false) {
    return banskostretching_video_manager()->display_course_lessons($product_id, $use_security);
}
