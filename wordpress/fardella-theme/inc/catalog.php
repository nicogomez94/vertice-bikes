<?php
/**
 * Catálogo inicial basado en la maqueta aprobada.
 *
 * @package Fardella
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function fardella_seed_catalog() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return new WP_Error( 'woocommerce_missing', 'WooCommerce debe estar activo.' );
	}

	$categories = array(
		'bicicletas' => 'Bicicletas',
		'repuestos'  => 'Repuestos',
		'accesorios' => 'Accesorios',
	);

	foreach ( $categories as $slug => $name ) {
		if ( ! term_exists( $slug, 'product_cat' ) ) {
			wp_insert_term( $name, 'product_cat', array( 'slug' => $slug ) );
		}
	}

	$products = array(
		array(
			'name'        => 'Sicilia Dama R28',
			'slug'        => 'sicilia-dama-r28',
			'sku'         => 'BICSICILIA28DN',
			'category'    => 'bicicletas',
			'type'        => 'Bicicleta urbana',
			'brand'       => 'Battle / Philco',
			'wholesale'   => '156198.35',
			'image'       => 'sicilia-negra.jpg',
			'description' => 'Bicicleta de paseo Sicilia Dama rodado 28, de una velocidad, con guardabarros y portapaquete. Terminación negra.',
		),
		array(
			'name'        => 'Battle R29 24v',
			'slug'        => 'battle-r29-24v',
			'sku'         => 'BICBATTLE2924GR',
			'category'    => 'bicicletas',
			'type'        => 'Bicicleta MTB',
			'brand'       => 'Battle',
			'wholesale'   => '289256.20',
			'image'       => 'battle-gris.jpg',
			'description' => 'Bicicleta Battle rodado 29 con 24 velocidades, frenos hidráulicos, suspensión Suntour con bloqueo y transmisión Microshift.',
		),
		array(
			'name'        => 'Cubierta Cobra R29',
			'slug'        => 'cubierta-cobra-r29',
			'sku'         => 'PV699992',
			'category'    => 'repuestos',
			'type'        => 'Cubierta',
			'brand'       => 'Hutchinson',
			'wholesale'   => '38603.31',
			'image'       => 'cubierta-cobra.jpg',
			'description' => 'Cubierta Hutchinson Cobra rodado 29 × 2.10, construcción de 127 TPI.',
		),
		array(
			'name'        => 'Gafas Flash Azul',
			'slug'        => 'gafas-flash-azul',
			'sku'         => 'X1917230496',
			'category'    => 'accesorios',
			'type'        => 'Gafas',
			'brand'       => 'Accesorios',
			'wholesale'   => '14834.00',
			'image'       => 'gafas-flash.jpg',
			'description' => 'Gafas Flash en color azul, incluidas en la selección de accesorios del catálogo.',
		),
		array(
			'name'        => 'Guante Power Negro',
			'slug'        => 'guante-power-negro',
			'sku'         => 'X20122201005',
			'category'    => 'accesorios',
			'type'        => 'Guantes',
			'brand'       => 'Accesorios',
			'wholesale'   => '9257.12',
			'image'       => 'guante-power.jpg',
			'description' => 'Guante Power negro, disponible en talles M, L, XL y XXL según el catálogo.',
		),
	);

	foreach ( $products as $item ) {
		$existing = wc_get_product_id_by_sku( $item['sku'] );
		$product  = $existing ? wc_get_product( $existing ) : new WC_Product_Simple();

		$product->set_name( $item['name'] );
		$product->set_slug( $item['slug'] );
		$product->set_sku( $item['sku'] );
		$product->set_description( $item['description'] );
		$product->set_short_description( $item['description'] );
		$product->set_status( 'publish' );
		$product->set_catalog_visibility( 'visible' );
		$product->set_manage_stock( false );
		$product->set_stock_status( 'instock' );
		$product->update_meta_data( '_fardella_wholesale_price', $item['wholesale'] );
		$product->update_meta_data( '_fardella_type', $item['type'] );
		$product->update_meta_data( '_fardella_brand', $item['brand'] );
		$product_id = $product->save();

		$term = get_term_by( 'slug', $item['category'], 'product_cat' );
		if ( $term ) {
			wp_set_object_terms( $product_id, array( (int) $term->term_id ), 'product_cat' );
		}

		if ( ! get_post_thumbnail_id( $product_id ) ) {
			$source = get_template_directory() . '/assets/images/fardella/' . $item['image'];
			if ( file_exists( $source ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
				require_once ABSPATH . 'wp-admin/includes/media.php';
				require_once ABSPATH . 'wp-admin/includes/image.php';
				$upload = wp_upload_bits( $item['image'], null, file_get_contents( $source ) );
				if ( empty( $upload['error'] ) ) {
					$filetype      = wp_check_filetype( $upload['file'], null );
					$attachment_id = wp_insert_attachment(
						array(
							'post_mime_type' => $filetype['type'],
							'post_title'     => $item['name'],
							'post_status'    => 'inherit',
						),
						$upload['file'],
						$product_id
					);
					$metadata = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );
					wp_update_attachment_metadata( $attachment_id, $metadata );
					set_post_thumbnail( $product_id, $attachment_id );
				}
			}
		}
	}

	return count( $products );
}

