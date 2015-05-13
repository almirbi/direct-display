<?php
/**
 * Created by PhpStorm.
 * User: almir
 * Date: 27/12/14
 * Time: 18:08
 */

require_once( 'includes/Slide/SlidePostType.php' );
require_once( 'includes/Display/DisplayPostType.php' );

add_theme_support('post-thumbnails');

$slidePost = new SlidePostType();
$display = new DisplayPostType();

function enqueue_admin_scripts() {
    wp_enqueue_style('jquery_ui_css_smooth', '//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css');
    wp_enqueue_style('style-admin', get_template_directory_uri() . '/assets/admin.css');

    wp_enqueue_script( 'mousewheel',
                       'https://cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.12/jquery.mousewheel.min.js',
                       array('jquery'),
                       '1.0.0',
                       true );
    wp_enqueue_script( 'globalize',
                       'https://cdnjs.cloudflare.com/ajax/libs/globalize/0.1.1/globalize.min.js',
                       array(),
                       '1.0.0',
                       true );
    wp_enqueue_script( 'globalize_cult',
                       'https://cdnjs.cloudflare.com/ajax/libs/globalize/0.1.1/cultures/globalize.culture.de-DE.min.js',
                       array(),
                       '1.0.0',
                       true );
    wp_enqueue_script( 'jquery_ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.js', array(), '1.0.0', true );
}

add_action( 'admin_enqueue_scripts', 'enqueue_admin_scripts' );


function frontend_enqueue_scripts() {

    wp_enqueue_style('clock_widget_css', get_template_directory_uri() . '/assets/clock.css');

    wp_enqueue_script( 'fronend-custom',
                       get_template_directory_uri() . '/assets/frontend.custom.js',
                       array(),
                       '1.0.0',
                       true );
    $ajax_vars = array( 'updateDisplayUrl' => get_site_url() );
    wp_localize_script( 'fronend-custom', 'ajax_vars', $ajax_vars );
}

add_action( 'wp_enqueue_scripts', 'frontend_enqueue_scripts' );

add_filter('show_admin_bar', '__return_false');