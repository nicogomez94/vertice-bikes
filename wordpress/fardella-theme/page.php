<?php
/**
 * Plantilla estándar de páginas.
 *
 * @package Fardella
 */

get_header();
$commerce_page = function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() );
$eyebrow       = is_cart() ? 'Tu selección' : ( is_checkout() ? 'Finalizar pedido' : ( is_account_page() ? 'Portal mayorista' : 'Fardella S.A.' ) );
?>
<main class="wp-content-page section-pad<?php echo $commerce_page ? ' fardella-commerce-page' : ''; ?>">
	<?php while ( have_posts() ) : the_post(); ?>
		<header class="wp-page-heading"><p class="eyebrow dark"><?php echo esc_html( $eyebrow ); ?></p><h1><?php the_title(); ?></h1><?php if ( is_cart() ) : ?><p>Revisá cantidades y productos antes de continuar.</p><?php elseif ( is_checkout() ) : ?><p>Completá los datos para que Fardella confirme disponibilidad, pago y entrega.</p><?php endif; ?></header>
		<div class="wp-page-content"><?php the_content(); ?></div>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>

