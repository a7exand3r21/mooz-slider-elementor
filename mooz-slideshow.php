<?php
/**
 * Plugin Name: Mooz Slideshow
 * Description: Advanced slideshow widget for Elementor with shape transitions. Based on work by @codrops
 * Version: 1.0.0
 * Author: Aleksandr Sharshavin
 * Text Domain: mooz-slideshow
 */

if (!defined('ABSPATH')) exit;

final class Mooz_Slideshow {
    const VERSION = '1.0.0';
    const MINIMUM_ELEMENTOR_VERSION = '3.0.0';
    const MINIMUM_PHP_VERSION = '7.0';

    private static $_instance = null;

    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        add_action('plugins_loaded', [$this, 'init']);
    }

    public function init() {
        // Check for required Elementor version
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', [$this, 'admin_notice_missing_elementor']);
            return;
        }

        if (!version_compare(ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_elementor_version']);
            return;
        }

        // Add Plugin actions
        add_action('elementor/widgets/widgets_registered', [$this, 'init_widgets']);
        add_action('elementor/frontend/after_enqueue_styles', [$this, 'widget_styles']);
        add_action('elementor/frontend/after_register_scripts', [$this, 'widget_scripts']);
    }

    public function init_widgets() {
        require_once(__DIR__ . '/widgets/mooz-slideshow-widget.php');
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \Mooz_Slideshow_Widget());
    }

    public function widget_styles() {
        wp_register_style('mooz-slideshow', plugins_url('assets/css/mooz-slideshow.css', __FILE__));
        wp_enqueue_style('mooz-slideshow');
    }

    public function widget_scripts() {
        wp_register_script('gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.7.1/gsap.min.js', [], null, true);
        wp_register_script('events', plugins_url('assets/js/events.js', __FILE__), [], self::VERSION, true);
        wp_register_script('mooz-slideshow-extensions', plugins_url('assets/js/mooz-slideshow-extensions.js', __FILE__), ['gsap'], self::VERSION, true);
        wp_register_script(
            'mooz-slideshow',
            plugins_url('assets/js/mooz-slideshow.js', __FILE__),
            ['jquery', 'elementor-frontend', 'gsap', 'events', 'mooz-slideshow-extensions'],
            self::VERSION,
            true
        );
    }
}

Mooz_Slideshow::instance(); 