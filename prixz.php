<?php
/**
 * Plugin Name: Prixz
 * Plugin URI: https://www.prixz.com/
 * Description: Prueba Técnica Prixz
 * Author: Cristian Amador
 * Author URI: https://www.prixz.com/
 * Version: 1.0.0
 * Requires at least: 5.7.0
 * Tested up to: 5.8.0
 * Text Domain: prixz
 */

// En la inicialización registramos la taxonomía "Especie"

add_action( 'init', 'prixz_registrar_especie', 0 );

function prixz_registrar_especie()  {

    register_taxonomy( 
        'especie', 
        'product',
        array( 
            'labels' => array(
                'name' => 'Especies',
                'singular_name' => 'Especie',
                'menu_name' => 'Especies',
                'all_items' => 'Todas las Especies',
                'parent_item' => 'Especie Padre',
                'parent_item_colon' => 'Especie Padre:',
                'new_item_name' => 'Nombre de la Nueva Especie',
                'add_new_item' => 'Agregar Nueva Especie',
                'edit_item' => 'Editar Especie',
                'update_item' => 'Actualizar Especie',
                'separate_items_with_commas' => 'Separar Especies con comas',
                'search_items' => 'Buscar Especies',
                'add_or_remove_items' => 'Agregar o remover Especies',
                'choose_from_most_used' => 'Seleccionar las Especies más usadas',
            ),
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud' => true,
        )
    );

    // Una vez registrada la taxonomía agregamos el filtro para los productos relacionados
    add_filter( 'woocommerce_output_related_products_args', 'prixz_productos_relacionados_func' );

}

// Productos relacionados solo por especie

function prixz_productos_relacionados_func( $argumentos ) {

	global $post;

	$lista_terminos = get_the_terms( $post->ID, 'especie' );

    if ( ! empty( $lista_terminos ) ) {

        $terminos = array();

        foreach ( $lista_terminos as $termino ) {
            array_push( $terminos, $termino->slug );
        }

        $argumentos['tax_query'] = array(
            array(
                'taxonomy' => 'especie',
                'terms' => $terminos,
                'field' => 'slug',
            )
        );

    }

    return $argumentos;
}

// Creamos el shortcode que se encarga de mostrar los productos filtrados por especie

add_shortcode( 'prixz_filtro_especie', 'prixz_filtro_especie_func' );

function prixz_filtro_especie_func( $atts ) {
    global $atributos;

    $atributos = shortcode_atts( array(
		'columnas' => 3,
		'especie' => ''
	), $atts );

    // Validamos que 
    if ( $atributos['especie'] != '' ) {
        add_filter( 'woocommerce_shortcode_products_query', function( $query_args ) {
            global $atributos;
        
            $query_args['tax_query'] =  array( 
                array( 
                    'taxonomy' => 'especie', 
                    'field' => 'slug', 
                    'terms' => $atributos['especie']
                )
            );
        
            return $query_args;
        } );
    }
 
    $shortcode = sprintf( '[products columns="%s" ]', $atributos['columnas'] );

    return do_shortcode( $shortcode );
}