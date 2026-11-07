<?php
/**
 * Contenedor WooCommerce.
 *
 * @package Fardella
 */

get_header();
?>
<main class="fardella-woocommerce<?php echo is_product() ? ' fardella-single-product' : ' fardella-shop'; ?>">
	<?php if ( ! is_product() ) : ?>
		<header class="fardella-shop-heading">
			<p class="eyebrow dark">Catálogo septiembre 2026</p>
			<h1><?php echo esc_html( woocommerce_page_title( false ) ); ?></h1>
			<p>Explorá bicicletas, componentes, repuestos y accesorios con precios mayoristas visibles y compra directa desde el carrito.</p>
		</header>
	<?php endif; ?>
	<?php woocommerce_content(); ?>
</main>
<?php get_footer(); ?>

