<?php

/*
Plugin Name: SWS Gallery
Plugin URI: http://wordpress.org/extend/plugins/spyrowebz-gallery/
Description: A plugin which Create a galley, the gallery show in popup slider when click on a single image. use [sws_gallery] shortcode.
Version: 1.3.1
Author: Sultan
Author URI: https://web.facebook.com/sultan.semmai
License: GPLv2
*/
//plugin activation hock
register_activation_hook(__FILE__, 'sws_activate');
    function sws_activate() 
    {
        sws_register();
        flush_rewrite_rules();
    }
//plugin deactivation hock
register_deactivation_hook(__FILE__, 'sws_deactivate');
    function sws_deactivate() 
    {
        flush_rewrite_rules();
    }
// Register sws gallery post type
function sws_register()
{
    register_post_type( 'sws-gallery',
    array(
    'labels' => array
    (
    'name' => 'SWS Gallery',
    'singular_name' => 'SWS Gallery',
    'add_new' => 'Add New Gallery',
    'add_new_item' => 'Add New gallery',
    'edit' => 'Edit',
    'edit_item' => 'Edit gallery',
    'new_item' => 'New gallery',
    'view' => 'View',
    'view_item' => 'View gallery',
    'search_items' => 'Search gallerys',
    'not_found' => 'No gallery found',
    'not_found_in_trash' => 'No gallerys found in Trash',
    'parent' => 'Parent gallery',
    ),
    'public' => true,
    'menu_position' => 5,
    'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
    'menu_icon' =>  'dashicons-format-gallery',
    'has_archive' => false, // will add filter in feature 
    )
    );
}
add_action( 'init', 'create_gallery_taxonomies', 0 );
function create_gallery_taxonomies() 
{
    $labels = array
    (
    'name' => _x( 'gallery Types', 'taxonomy general name' ),
    'singular_name' => _x( 'gallery Type', 'taxonomy singular name' ),
    'search_items'  => __( 'Search gallery Type' ),
    'all_items'     => __( 'All gallery Type' ),
    'parent_item'   => __( 'Parent gallery Type' ),
    'parent_item_colon' => __( 'Parent gallery Type:' ),
    'edit_item'         => __( 'Edit gallery Type' ),
    'update_item'       => __( 'Update gallery Type' ),
    'add_new_item'      => __( 'Add New gallery Type' ),
    'new_item_name'     => __( 'New gallery Type Name' ),
    'menu_name'         => __( 'gallery Types' ),
    );
    $args = array
    (
    'hierarchical'      => true,
    'labels'            => $labels,
    'show_ui'           => true,
    'show_admin_column' => true,
    'query_var'         => true,
    'rewrite'           => array( 'slug' => 'sws-gallery-type' ),
    );
    register_taxonomy( '', array( 'sws-gallery' ), $args );
}
add_action('init', 'sws_register');
// Register custom JS scripts
add_action('wp_footer', 'sws_gallery_enqueue_scripts');
function sws_gallery_enqueue_scripts() 
{
    wp_register_script('popup', plugins_url('includes/js/magnific.min.js', __FILE__ )); 
    //wp_register_script('custom', plugins_url('includes/js/custom.js', __FILE__ )); 
    wp_enqueue_script('jquery');
    wp_enqueue_script('popup');
}
add_action('wp_footer', 'SWS_custum_script',100);
//custom script
function SWS_custum_script(){?>
<script>
// Lighbox gallery
jQuery('#popup-gallery').each(function () {
    jQuery(this).magnificPopup({
        delegate: 'a.popup-gallery-image',
        type: 'image',
        gallery: {
            enabled: true
        }
    });
});

</script>';
<?php }
//register styles
add_action('wp_footer', 'spyrowebs_gmap_enqueue_styles');
function spyrowebs_gmap_enqueue_styles() {
    wp_enqueue_style('boostrap', plugins_url('includes/css/boostrap.css', __FILE__ ));
    wp_enqueue_style('responsive', plugins_url('includes/css/boostrap_responsive.css', __FILE__ )); 
    wp_enqueue_style('font_awesome', plugins_url('includes/css/font_awesome.css', __FILE__ ));
    wp_enqueue_style('styles', plugins_url('includes/css/styles.css', __FILE__ ));
}
// shortcode
add_shortcode('sws_gallery' ,'sws_gallery');
function sws_gallery( $atts = array(), $content = '' ) 
{
    $output='';
    $defaults = array(
        'column' => '3',
        'posts_per_page' => '-1',
    );
    $atts = shortcode_atts( $defaults, $atts );
    extract( $atts );
    $column = $column;
    $posts_per_page = $posts_per_page;
    
    $output.='<div class="container"><div class="row row-wrap" id="popup-gallery">';
                $swsquery = new WP_Query( array('posts_per_page' => $posts_per_page, 'post_type' => 'sws-gallery',));
                if ( $swsquery->have_posts() ) 
                {
                    while ( $swsquery->have_posts() ) 
                    {
                        $swsquery->the_post();
                        $output.='<div class= "span' . $column . '" style="margin-bottom:30px;">';
                            $output.='<a class="img-hover popup-gallery-image" href="' . wp_get_attachment_url( get_post_thumbnail_id($post->ID) ) . '" data-effect="mfp-zoom-out">';
                                $output.='<img src="' .  wp_get_attachment_url( get_post_thumbnail_id($post->ID) ) . '" alt="Image Alternative text" title="'.get_the_title($post->ID).'" /><i class="icon-resize-full hover-icon"></i>';
                            $output.='</a>';
                        $output.='</div>';
                    }
                }
            $output.= '</div>
        </div>';
    return $output;
}
