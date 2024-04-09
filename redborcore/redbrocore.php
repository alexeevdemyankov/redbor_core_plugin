<?php
/*
Plugin Name: RedBroCore
Plugin URI:  https://redbro.pro
Description: RedBro Theme core Plugin
Version: 1.9.0
Author: Redbro
Author URI:  https://redbro.pro
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/


define('CT_TEXT_DOMAIN', 'redbrocore');
define('CT_PATH', plugin_dir_path(__FILE__));
define('CT_URL', plugin_dir_url(__FILE__));


/**
 * install and unistall
 */

register_activation_hook(__FILE__, 'redbro_core_install');

function redbro_core_install()
{

}

register_uninstall_hook(__FILE__, 'redbro_core_uninstall');
function redbro_core_uninstall()
{

}

class  Case_Theme_Core extends RedborCore
{
}


class RedborCore
{


    public static function pre($array)
    {
        echo "<pre>";
        print_r($array);
        echo "</pre>";
    }


    public static function registerLib($regName, $dev, $ext)
    {
        $regDev = ($dev == 1) ? date('his') : 1;
        $filePatch = get_theme_root() . '/redbropro/elementor/' . $ext . '/rb-' . $regName . '.' . $ext;
        if (is_file($filePatch)) {
            if ($ext == 'css') {
                wp_enqueue_style('rb-' . $regName . '-css', get_template_directory_uri() . '/elementor/css/rb-' . $regName . '.css?v=' . $regDev, array());
            }
            if ($ext == 'js') {
                wp_register_script('rb-' . $regName . '-js', get_template_directory_uri() . '/elementor/js/rb-' . $regName . '.js?v=' . $regDev, ['jquery'], '1');
            }
        }
    }


    const CT_CATEGORY_TITLE = 'RedBroCore';
    const CT_CATEGORY_NAME = 'case-theme-core';
    const LAYOUT_CONTROL = 'layoutcontrol';
    const LIST_CONTROL = 'ct_lists';
    const CT_TAB_NAME = 'RedBroCore';
    const CT_TAB_TITLE = 'RedBroCore';

    private static $_instance = null;

    public static function instance()
    {

        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;

    }

    public function __construct()
    {

        require_once(__DIR__ . '/inc/helpers/resize-image.php');
        require_once(__DIR__ . '/inc/helpers/common.php');
        require_once(__DIR__ . '/inc/helpers/widget.php');

        add_action('elementor/elements/categories_registered', [$this, 'add_elementor_widget_categories']);
        add_action('elementor/widgets/widgets_registered', [$this, 'init_widgets']);
        add_action('elementor/controls/controls_registered', [$this, 'init_controls']);
        add_action('elementor/elements/elements_registered', [$this, 'elements_registered']);
    }

    public function elements_registered()
    {
        require_once(__DIR__ . '/inc/elementor/section.php');
        require_once(__DIR__ . '/inc/elementor/column.php');

        $elements_manager = \Elementor\Plugin::$instance->elements_manager;
        $elements_manager->unregister_element_type('section');
        $elements_manager->unregister_element_type('column');
        $elements_manager->register_element_type(new \Elementor\CT_Element_Section());
        $elements_manager->register_element_type(new \Elementor\CT_Element_Column());
    }

    public function init_widgets()
    {
        require_once(__DIR__ . '/inc/widgets/abstract-class-widget-base.php');
        require_once get_template_directory() . '/elementor/core/elementor.php';
    }

    public function init_controls()
    {
        require_once(__DIR__ . '/inc/controls/class-control-layout.php');
        $controls_manager = \Elementor\Plugin::$instance->controls_manager;
        $controls_manager->register_control(self::LAYOUT_CONTROL, new Case_Theme_Core_Layout_Control());
        $controls_manager->add_tab(self::CT_TAB_NAME, esc_html__(self::CT_TAB_TITLE, CT_TEXT_DOMAIN));
    }


    function add_elementor_widget_categories($elements_manager)
    {
        $categories = apply_filters('ct_add_custom_categories', array(
            array(
                'name' => self::CT_CATEGORY_NAME,
                'title' => __(self::CT_CATEGORY_TITLE, CT_TEXT_DOMAIN),
                'icon' => 'fa fa-plug',
            ),
        ));

        foreach ($categories as $cat) {
            $elements_manager->add_category(
                $cat['name'],
                array(
                    'title' => $cat['title'],
                    'icon' => $cat['icon'],
                )
            );
        }
    }


}

RedborCore::instance();





