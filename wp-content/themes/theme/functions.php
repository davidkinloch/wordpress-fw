<?php

// REMOVE EMOJI SCRIPT FROM HEAD TAG IN WORDPRESS
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_styles', 'print_emoji_styles');

function my_init()   
{  
    if (!is_admin())   
    {  
        wp_deregister_script('jquery');  
  
        // Load a copy of jQuery from the Google API CDN  
        // The last parameter set to TRUE states that it should be loaded  
        // in the footer.  
        wp_register_script('jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js', FALSE, '1.11.0', TRUE);  
        
        wp_enqueue_script('jquery');  
        wp_enqueue_script( 'production.min', get_template_directory_uri() . '/js/build/production.min.js', array(), '1.0.0', true );
    }  
}  
add_action('init', 'my_init');

//disable admin bar
add_filter('show_admin_bar', '__return_false');

// Menus
register_nav_menus( array(
    'primary' => __( 'Primary Menu',      'theacademygroup' ),
    'footer-links-left' => __( 'Footer Links Left',      'theacademygroup' ),
    'footer-links-right' => __( 'Footer Links Right',      'theacademygroup' )
) );



//add work cpt to cateorgy archives 
if ( ! function_exists( 'post_is_in_descendant_category' ) ) {
    function post_is_in_descendant_category( $cats, $_post = null ) {
        foreach ( (array) $cats as $cat ) {
            // get_term_children() accepts integer ID only
            $descendants = get_term_children( (int) $cat, 'category' );
            if ( $descendants && in_category( $descendants, $_post ) )
                return true;
        }
        return false;
    }
}


function namespace_add_custom_types( $query ) {
  if( is_archive() && (is_category('124') || is_category('125') || is_category('126') || is_category('127') || is_category('128') || is_category('129') || is_category('130') || is_category('131') || is_category('132') || is_category('133') || is_category('134') || is_category('137') || is_tag()) && empty( $query->query_vars['suppress_filters'] ) ) {
        $query->set( 'post_type', array(
                'work',
            ));
          return $query;
        }
}
add_filter( 'pre_get_posts', 'namespace_add_custom_types' );



// Add thumbnai to posts
add_theme_support( 'post-thumbnails' );

// srip inl dimensions on images
//add_filter( 'post_thumbnail_html', 'remove_thumbnail_dimensions', 10 );
//add_filter( 'image_send_to_editor', 'remove_thumbnail_dimensions', 10 );

//image sizes
add_image_size( 'thumbs', 320, 161 );
add_image_size( 'hero', 1600, 820 );

// check for a certain meta key on the current post and add a body class if meta value exists
add_filter('body_class','krogs_custom_field_body_class');
function krogs_custom_field_body_class( $classes ) {
    if ( get_post_meta( get_the_ID(), 'dark_header', true ) ) {
        $classes[] = 'dark';   
    }
    // return the $classes array
    return $classes;
}

// Replaces the excerpt "Read More" text by a link
function new_excerpt_more($more) {
       global $post;
    return ' <span class="section-thoughts__more"> &#8230; Read more</span>';
}
add_filter('excerpt_more', 'new_excerpt_more');

// add post featured image to RSS feed
function featuredtoRSS($content) {
global $post;
if ( has_post_thumbnail( $post->ID ) ){
$content = '<div>' . get_the_post_thumbnail( $post->ID, 'medium', array( 'style' => 'margin-bottom: 15px;' ) ) . '</div>' . $content;
}
return $content;
}
 
add_filter('the_excerpt_rss', 'featuredtoRSS');
add_filter('the_content_feed', 'featuredtoRSS');

//DISABLE STUFF

// Disable support for comments and trackbacks in post types
function df_disable_comments_post_types_support() {
    $post_types = get_post_types();
    foreach ($post_types as $post_type) {
        if(post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
}
add_action('admin_init', 'df_disable_comments_post_types_support');
// Close comments on the front-end
function df_disable_comments_status() {
    return false;
}
add_filter('comments_open', 'df_disable_comments_status', 20, 2);
add_filter('pings_open', 'df_disable_comments_status', 20, 2);
// Hide existing comments
function df_disable_comments_hide_existing_comments($comments) {
    $comments = array();
    return $comments;
}
add_filter('comments_array', 'df_disable_comments_hide_existing_comments', 10, 2);
// Remove comments page in menu
function df_disable_comments_admin_menu() {
    remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'df_disable_comments_admin_menu');
// Redirect any user trying to access comments page
function df_disable_comments_admin_menu_redirect() {
    global $pagenow;
    if ($pagenow === 'edit-comments.php') {
        wp_redirect(admin_url()); exit;
    }
}
add_action('admin_init', 'df_disable_comments_admin_menu_redirect');
// Remove comments metabox from dashboard
function df_disable_comments_dashboard() {
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
}
add_action('admin_init', 'df_disable_comments_dashboard');
// Remove comments links from admin bar
function df_disable_comments_admin_bar() {
    if (is_admin_bar_showing()) {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    }
}
add_action('init', 'df_disable_comments_admin_bar');

//disable posts
function remove_posts_menu() {
    remove_menu_page('edit.php');
}
add_action('admin_init', 'remove_posts_menu');

add_action('admin_head', 'my_custom_fonts');

function my_custom_fonts() {
  echo '<style>
  .acf-field-5a0c02d41fc4b {
    background: #DDD;
  }
    .acf-repeater .acf-row:nth-child(odd) > .acf-row-handle.order {
      background: #666;
    } 
  </style>';
}

//hide custom fields
add_filter('acf/settings/show_admin', '__return_false');

