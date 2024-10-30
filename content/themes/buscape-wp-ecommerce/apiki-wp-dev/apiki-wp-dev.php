<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$apiki_wp_theme_dev_file = dirname( __FILE__ ) . '/apiki-wp-theme-dev.php';
$apiki_wp_plugin_dev_file = dirname( __FILE__ ) . '/apiki-wp-plugin-dev.php';

if ( file_exists( $apiki_wp_theme_dev_file ) )
    require_once( $apiki_wp_theme_dev_file );

if ( file_exists( $apiki_wp_plugin_dev_file ) )
    require_once( $apiki_wp_plugin_dev_file );

/**
 * Description of apiki-wp-dev
 *
 * @author eu
 */
class Apiki_Wp_Dev {
    
    /**
     * Assina o filtro get_terms para sempre que for invocado com o argumento
     * 'fields' = 'slugs' retornar somente os slugs dos termos da taxonomia.
     *
     * @param array|object $terms Objeto com os termos originais.
     * @param string|array $taxonomies String ou array contendo as taxonomias para buscar os termos.
     * @param array $args Argumentos para recuperar os termos.
     * @return array Array contendo apenas o slug de cada temrmo encontrado.
     */
    public static function terms_by_slug( $terms )
    {
        if ( !isset( $terms ) ) return $terms;

        $new_terms = array();
        $i = 0;

        foreach ( $terms as $term ) {
            $new_terms[$i] = $term->slug;
            $i++;
        }

        return $new_terms;
    }
}

