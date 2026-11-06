<?php
/**
 * Encabezado principal.
 *
 * @package Fardella
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$theme_uri  = get_template_directory_uri();
$cart_count = function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
$catalog_categories = array();
if ( function_exists( 'fardella_catalog_data' ) ) {
	$catalog_items = fardella_catalog_data();
	if ( ! is_wp_error( $catalog_items ) ) {
		foreach ( $catalog_items as $catalog_item ) {
			$catalog_categories[ $catalog_item['category_slug'] ] = $catalog_item['category'];
		}
	}
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( is_front_page() ? 'fardella-site' : 'fardella-site inner-page' ); ?>>
<?php wp_body_open(); ?>
<div class="announcement">
	<span>FARDELLA S.A.</span>
	<span>Distribuidor mayorista desde 1939</span>
	<span>Envíos a todo el país</span>
</div>
<header class="site-header">
	<button class="icon-button menu-toggle" aria-label="Abrir menú" aria-expanded="false"><span></span><span></span></button>
	<a class="wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Fardella S.A., inicio">
		<img src="<?php echo esc_url( $theme_uri . '/assets/images/fardella/logo-fardella-official.png' ); ?>" alt="Fardella S.A.">
	</a>
	<nav class="desktop-nav" aria-label="Navegación principal">
		<a href="<?php echo esc_url( home_url( '/#categorias' ) ); ?>">Categorías</a>
		<div class="catalog-dropdown">
			<button class="catalog-dropdown-toggle" type="button" aria-expanded="false" aria-controls="catalog-dropdown-menu">Productos <i class="fa-solid fa-chevron-down"></i></button>
			<div class="catalog-dropdown-menu" id="catalog-dropdown-menu" aria-hidden="true">
				<div class="catalog-dropdown-heading"><span>Catálogo Fardella</span><strong>Elegí por tipo de producto</strong></div>
				<div class="catalog-dropdown-grid">
					<?php foreach ( $catalog_categories as $category_slug => $category_name ) : ?>
						<a href="<?php echo esc_url( home_url( '/product-category/' . $category_slug . '/' ) ); ?>"><i class="fa-solid fa-angle-right"></i><span><strong><?php echo esc_html( $category_name ); ?></strong><small>Ver productos</small></span></a>
					<?php endforeach; ?>
				</div>
				<a class="catalog-dropdown-all" href="<?php echo esc_url( home_url( '/tienda/' ) ); ?>">Ver los 137 artículos <i class="fa-solid fa-arrow-right"></i></a>
			</div>
		</div>
		<a href="<?php echo esc_url( home_url( '/#marcas' ) ); ?>">Marcas</a>
		<a class="wholesale-nav" href="<?php echo esc_url( home_url( '/mayoristas/' ) ); ?>">Mayoristas</a>
	</nav>
	<div class="header-actions">
		<button class="icon-button search-toggle" aria-label="Buscar"><i class="fa-solid fa-magnifying-glass"></i></button>
		<a class="icon-button account-link" href="<?php echo esc_url( home_url( '/mi-cuenta/' ) ); ?>" aria-label="Mi cuenta"><i class="fa-regular fa-user"></i></a>
		<a class="icon-button cart-link" href="<?php echo esc_url( function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/carrito/' ) ); ?>" aria-label="Ver carrito"><i class="fa-solid fa-bag-shopping"></i><span class="cart-count" data-cart-count><?php echo esc_html( $cart_count ); ?></span></a>
	</div>
</header>
<aside class="mobile-menu" aria-hidden="true">
	<div class="mobile-menu-top"><span>MENÚ</span><button class="icon-button menu-close" aria-label="Cerrar menú"><i class="fa-solid fa-xmark"></i></button></div>
	<nav>
		<a href="<?php echo esc_url( home_url( '/#categorias' ) ); ?>">Categorías</a>
		<div class="mobile-catalog">
			<button class="mobile-catalog-toggle" type="button" aria-expanded="false" aria-controls="mobile-catalog-panel">Productos <i class="fa-solid fa-chevron-down"></i></button>
			<div class="mobile-catalog-panel" id="mobile-catalog-panel" aria-hidden="true">
				<?php foreach ( $catalog_categories as $category_slug => $category_name ) : ?>
					<a href="<?php echo esc_url( home_url( '/product-category/' . $category_slug . '/' ) ); ?>"><i class="fa-solid fa-angle-right"></i> <?php echo esc_html( $category_name ); ?></a>
				<?php endforeach; ?>
				<a href="<?php echo esc_url( home_url( '/tienda/' ) ); ?>"><i class="fa-solid fa-border-all"></i> Ver los 137 artículos</a>
			</div>
		</div>
		<a href="<?php echo esc_url( home_url( '/#marcas' ) ); ?>">Marcas</a>
		<a href="<?php echo esc_url( home_url( '/mayoristas/' ) ); ?>">Mayoristas</a>
		<a href="<?php echo esc_url( home_url( '/#consulta' ) ); ?>">Consultá</a>
		<a href="<?php echo esc_url( home_url( '/mi-cuenta/' ) ); ?>">Mi cuenta</a>
	</nav>
	<p>Todo para disfrutar del ciclismo.</p>
</aside>
<div class="menu-backdrop"></div>
<div class="search-panel" aria-hidden="true">
	<button class="search-close" aria-label="Cerrar búsqueda"><i class="fa-solid fa-xmark"></i></button>
	<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<label for="site-search">Buscar productos</label>
		<div class="search-field"><input id="site-search" name="s" type="search" placeholder="Bicicletas, cubiertas, guantes..." autocomplete="off"><i class="fa-solid fa-magnifying-glass"></i></div>
		<input type="hidden" name="post_type" value="product">
	</form>
</div>

