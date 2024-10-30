<?php
/**
 * Exibe o breadcrumbs de acordo com a navegação do usuário.
 * @global <type> $post
 * @global <type> $s
 * @global <type> $term
 * @global <type> $taxonomy
 */
function apiki_breadcrumbs() 
{

    global $post, $s, $term, $taxonomy, $category, $tag, $author;
    
    $output = apiki_breadcrumbs_anchor( get_site_url( 1 ), 'Home', 'no-active', true );

    if ( !is_main_site() )
        $output .= apiki_breadcrumbs_anchor ( get_site_url(), get_bloginfo( 'name' ), ( is_home() ) ? 'active' : 'no-active' , ( !is_home() ) ? true : false );

    if ( is_page() )
    {
        $output .= apiki_breadcrumbs_pages( $post );
        return apiki_display_breadcrumbs( $output );
    }
    if ( is_search() )
    {
        $output .= apiki_breadcrumbs_anchor( '' , 'Resultado da busca', 'active' );
        return apiki_display_breadcrumbs( $output );
    }
    if ( is_404() )
    {
        $output .= apiki_breadcrumbs_anchor( '', 'Página não encontrada', 'active' );
        return apiki_display_breadcrumbs( $output );
    }
    if ( is_tag() || is_category() || is_tax() )
    {
        $_taxonomy  = $taxonomy;
        $_term      = $term;

        if ( is_category () ) :
            $_taxonomy   =   'category';
            $_term       =   sanitize_title( single_cat_title( '', false ) );
        elseif ( is_tag () ) :            
            $_taxonomy   =   'post_tag';
            $_term       =   $tag;
        endif;

        $output .= apiki_breadcrumbs_taxonomies( $_term, $_taxonomy, null );
        return apiki_display_breadcrumbs( $output );
    }

    if ( is_single () )
    {
        if ( is_main_site () ) :
            $post_id = is_attachment() ? get_post_field( 'post_parent' , $post->ID ) : $post->ID;
            $all_terms = get_the_terms( $post_id, BWEC_TAX_CATEGORY );
            $term = ( !empty( $all_terms ) ) ? array_shift( $all_terms ) : null ;
            $output .= apiki_breadcrumbs_taxonomies( $term->slug, BWEC_TAX_CATEGORY, get_post( $post_id ), 'no-active', true );
        endif;
        $output .= apiki_breadcrumbs_anchor( get_permalink( $post_id ), get_the_title( $post_id ), 'active' );
        return apiki_display_breadcrumbs( $output );
    }

    if ( is_author () )
    {
        $output .= apiki_breadcrumbs_anchor( get_author_posts_url( get_the_author_meta( 'ID' ) ), get_author_name( $author ), 'active' );
        return apiki_display_breadcrumbs( $output );
    }

    return apiki_display_breadcrumbs( $output );

}

/**
 * Monta o breadcrumbs para as taxonomias de custom post type.
 * @param string $term Slug do termo.
 * @param string $taxonomy Slug da taxonomia.
 * @param object $post
 * @param string $cpt_name Nome da taxonomia.
 * @return string Breadcrumbs montado.
 */
function apiki_breadcrumbs_taxonomies( $term, $taxonomy, $post, $class = 'active', $sep = false )
{
    $output = '';
    if ( isset( $term ) ) {
        $the_term = get_term_by( 'slug', $term, $taxonomy );
        if ( isset( $the_term->parent ) && $the_term->parent > 0 ) {
            $output = apiki_breadcrumbs_taxonomies( get_term_field( 'slug', $the_term->parent, $taxonomy ), $taxonomy, null, 'no-active', true );
        }
     
        $output .= apiki_breadcrumbs_anchor( get_term_link( $term, $taxonomy ), $the_term->name, $class, $sep );
    }

    return $output;
}

/**
 * Monta o breadcrumbs para as páginas. Efetua lógica recursiva caso a página
 * seja filha de outra.
 * @param object $post Objeto post contendo os dados da página.
 * @return string Breadcrumbs montado.
 */
function apiki_breadcrumbs_pages( $post, $class = 'active', $sep = false )
{
    if ( isset( $post->post_parent ) && $post->post_parent > 0 ) {
        $output = apiki_breadcrumbs_pages( get_post( $post->post_parent ), 'no-active', true );
    }

    $output .= apiki_breadcrumbs_anchor( get_permalink( $post->ID ), $post->post_title, $class, $sep );

    return $output;
}

/**
 * Monta a tag HTML com o link do breadcrumbs.
 * @param string $href URL.
 * @param string $title Título a ser exibido.
 * @param bool $sep Default: true. Define se será mostrado separador antes da tag HTML.
 * @return string Tag HTML com o link formado.
 */
function apiki_breadcrumbs_anchor( $href, $title, $class, $sep = false )
{
    if ( $class == 'active' )
        return sprintf( '<span class="last">%s</span>', esc_html( $title ) );
    else
        return sprintf( '<a href="%2$s" title="%3$s">%4$s</a><span class="next">%1$s</span>', ( $sep ) ? ' &raquo; ' : '', $href, esc_html( $title ), $title );
}

/**
 * Função criada para resolver problema na página 404.
 * @param <type> $text Text para exibição no breadcrumbs
 */
function apiki_display_breadcrumbs( $text ) 
{
    printf( '<div class="breadcrumbs">%s</div><!-- / breadcrumbs -->', $text );
}