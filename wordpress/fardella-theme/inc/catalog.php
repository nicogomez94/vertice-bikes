<?php
/**
 * Importador idempotente del catálogo Fardella de septiembre de 2026.
 *
 * @package Fardella
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function fardella_catalog_group( $category_slug ) {
	if ( 'bicicletas-y-cuadros' === $category_slug ) {
		return 'bicicletas';
	}

	if ( in_array( $category_slug, array( 'accesorios', 'gafas', 'guantes' ), true ) ) {
		return 'accesorios';
	}

	return 'repuestos';
}

function fardella_catalog_brand( $name ) {
	$brands = array( 'Shimano', 'Hutchinson', 'Battle', 'Philco', 'Skin Red', 'Sunrace', 'KMC', 'Kenli', 'Neco' );
	foreach ( $brands as $brand ) {
		if ( false !== stripos( $name, $brand ) ) {
			return $brand;
		}
	}
	return 'Fardella';
}

function fardella_catalog_data() {
	$source = get_template_directory() . '/inc/catalog.json';
	if ( ! file_exists( $source ) ) {
		return new WP_Error( 'catalog_missing', 'No se encontró inc/catalog.json.' );
	}

	$data = json_decode( file_get_contents( $source ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( ! is_array( $data ) || 137 !== count( $data ) ) {
		return new WP_Error( 'catalog_invalid', 'El catálogo debe contener exactamente 137 artículos.' );
	}
	return $data;
}

function fardella_catalog_term( $name, $slug, $parent = 0 ) {
	$existing = term_exists( $slug, 'product_cat' );
	if ( $existing ) {
		return (int) ( is_array( $existing ) ? $existing['term_id'] : $existing );
	}

	$created = wp_insert_term(
		$name,
		'product_cat',
		array(
			'slug'   => $slug,
			'parent' => $parent,
		)
	);
	return is_wp_error( $created ) ? 0 : (int) $created['term_id'];
}

function fardella_catalog_image( $product_id, $item ) {
	if ( get_post_thumbnail_id( $product_id ) ) {
		return;
	}

	$source = get_template_directory() . '/assets/images/catalogo/' . $item['image'];
	if ( ! file_exists( $source ) ) {
		return;
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$upload = wp_upload_bits( $item['image'], null, file_get_contents( $source ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( ! empty( $upload['error'] ) ) {
		return;
	}

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
	if ( is_wp_error( $attachment_id ) ) {
		return;
	}

	$metadata = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );
	wp_update_attachment_metadata( $attachment_id, $metadata );
	set_post_thumbnail( $product_id, $attachment_id );
}

function fardella_seed_catalog() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return new WP_Error( 'woocommerce_missing', 'WooCommerce debe estar activo.' );
	}

	$items = fardella_catalog_data();
	if ( is_wp_error( $items ) ) {
		return $items;
	}

	$groups = array(
		'bicicletas' => 'Bicicletas',
		'repuestos'  => 'Repuestos',
		'accesorios' => 'Accesorios',
	);
	$group_ids = array();
	foreach ( $groups as $slug => $name ) {
		$group_ids[ $slug ] = fardella_catalog_term( $name, $slug );
	}

	$category_ids = array();
	foreach ( $items as $item ) {
		$category_slug = sanitize_title( $item['category_slug'] );
		if ( isset( $category_ids[ $category_slug ] ) ) {
			continue;
		}
		$group                          = fardella_catalog_group( $category_slug );
		$category_ids[ $category_slug ] = fardella_catalog_term(
			$item['category'],
			$category_slug,
			$group_ids[ $group ]
		);
	}

	foreach ( $items as $position => $item ) {
		$existing = wc_get_product_id_by_sku( $item['sku'] );
		$product  = $existing ? wc_get_product( $existing ) : new WC_Product_Simple();
		if ( ! $product ) {
			continue;
		}

		$description = sprintf(
			'%1$s. Código de artículo: %2$s. Precio mayorista de lista más IVA, según catálogo Fardella de septiembre de 2026.',
			$item['name'],
			$item['sku']
		);
		$product->set_name( $item['name'] );
		$product->set_slug( $item['slug'] );
		if ( ! $existing ) {
			$product->set_sku( $item['sku'] );
		}
		$product->set_description( $description );
		$product->set_short_description( $item['name'] );
		$product->set_status( 'publish' );
		$product->set_catalog_visibility( 'visible' );
		$product->set_menu_order( (int) $position );
		$product->set_manage_stock( false );
		$product->set_stock_status( 'instock' );
		$product->set_regular_price( wc_format_decimal( $item['wholesale'] ) );
		$product->update_meta_data( '_fardella_wholesale_price', wc_format_decimal( $item['wholesale'] ) );
		$product->update_meta_data( '_fardella_type', $item['category'] );
		$product->update_meta_data( '_fardella_brand', fardella_catalog_brand( $item['name'] ) );
		$product->update_meta_data( '_fardella_group', fardella_catalog_group( sanitize_title( $item['category_slug'] ) ) );
		$product->update_meta_data( '_fardella_catalog_page', (int) $item['page'] );
		$product_id = $product->save();

		$category_id = $category_ids[ sanitize_title( $item['category_slug'] ) ] ?? 0;
		if ( $category_id ) {
			wp_set_object_terms( $product_id, array( $category_id ), 'product_cat' );
		}
		fardella_catalog_image( $product_id, $item );
	}

	delete_transient( 'wc_term_counts' );
	wc_delete_product_transients();
	return count( $items );
}
