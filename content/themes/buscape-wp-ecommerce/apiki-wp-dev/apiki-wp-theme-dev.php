<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of apiki-wp-theme-dev
 *
 * @author eu
 */
class Apiki_Wp_Theme_Dev extends Apiki_Wp_Dev
{

    public static $excerpt_length = 25;

    public static function the_page_title()
    {
        global $page, $paged;

        // Print the current page title, except the home
        wp_title( '|', true, 'right' );

        // Add the site name
        bloginfo( 'name' );

        // Add the site description for the home and the front page.
        $site_description = get_bloginfo( 'description', 'display' );
        if ( $site_description and ( is_home() || is_front_page() ) )
            printf( ' | %s', $site_description );

        // Add the page number, if necessary.
        if ( $paged >= 2 or $page >= 2 )
            printf( ' | %s ', sprintf( __( 'Page %s', 'apiki-wp-dev' ), max( $paged, $page ) ) );
        
    }

    public static function the_breadcrumbs()
    {
        $output = '<ul>';

        if ( is_search() ) :
            $output .= sprintf( '<li>%s</li>', __( 'Search results', 'apiki-wp-dev' ) );
            $output .= '<li>></li>';
            $output .= sprintf( '<li class="current">%s</li>', get_search_query() );
        endif;

        if ( is_category() ) :
            $output .= sprintf( '<li>%s</li>', __( 'Category', 'apiki-wp-dev' ) );
            $output .= '<li>></li>';
            $output .= sprintf( '<li class="current">%s</li>', single_cat_title( '', false ) );
        endif;

        if ( is_tag() ) :
            $output .= sprintf( '<li>%s</li>', __( 'Tag', 'apiki-wp-dev' ) );
            $output .= '<li>></li>';
            $output .= sprintf( '<li class="current">%s</li>', single_tag_title( '', false ) );
        endif;

        $output .= '</ul>';

        echo $output;
    }

    public static function the_breadcrumb()
    {
        self::the_breadcrumbs();
    }

    public static function define_excerpt_length( $length )
    {
        self::$excerpt_length = $length;
        add_filter( 'excerpt_length', array( 'Apiki_Wp_Theme_Dev', '_define_excerpt_length' ) );
    }

    public static function _define_excerpt_length()
    {
        return self::$excerpt_length;
    }

    public static function define_security_environment()
    {
        remove_action( 'wp_head', 'wp_generator' );
    }

    public static function is_subpage( $is_subpage_of_parent_slug = 0 )
    {
        global $post;

        $is_subpage = false;

        if ( $is_subpage_of_parent_slug ) :
            $page_parent = get_page_by_path( $is_subpage_of_parent_slug );
            $is_subpage = ( is_page() and $post->post_parent == $page_parent->ID ) ? true : false;
        else :
            $is_subpage = ( is_page() and $post->post_parent ) ? true : false;
        endif;

        return $is_subpage;
    }

    /**
     * Retrieve the post ID by their slug.
     *
     * Based in http://erikt.tumblr.com/post/278953342/get-a-wordpress-page-id-with-the-slug
     *
     * @param string $slug The slug to search for. If it's a hierarchical post,
     * like a page, must be passed the parent, like as "parent_page/my_page"
     * @param string $post_type The post type. The default value is "page".
     * @return integer|null Return the page ID if found or null if not.
     */
    static public function get_id_by_path( $path, $post_type = 'page' )
    {
       $object = get_page_by_path( $path, OBJECT, $post_type );
       if ( $object )
           return $object->ID;

       return null;
    }

    /**
     * Retrieve the full permalink for a post by their slug.
     *
     * @param string $slug The slug to search for. If it's a hierarchical post,
     * like a page, must be passed the parent, like as "parent_page/my_page"
     * @param string $post_type The post type. The default value is "page".
     * @return string Return the full permalink if found or null if not.
     */
    static public function get_permalink_by_path( $path, $post_type = 'page' )
    {
        return get_permalink( self::get_id_by_path( $path, $post_type ) );
    }

    /**
     * Overwrite the image size defaults of WordPress
     *
     * @param array $args The new sizes for the images
     */
    static public function overwrite_default_images_size( $args )
    {
        $defaults = array( 
            'thumbnail_size_w'      => get_option( 'thumbnail_size_w' ),
            'thumbnail_size_h'      => get_option( 'thumbnail_size_h' ),
            'thumbnail_crop'        => get_option( 'thumbnail_crop' ),
            'medium_size_w'         => get_option( 'medium_size_w' ),
            'medium_size_h'         => get_option( 'medium_size_h' ),
            'large_size_w'          => get_option( 'large_size_w' ),
            'large_size_h'          => get_option( 'large_size_h' )
        );

        $r = wp_parse_args( $args, $defaults );
        
        foreach ( $r as $key => $arg )
            update_option( $key, $arg );
    }
    
}