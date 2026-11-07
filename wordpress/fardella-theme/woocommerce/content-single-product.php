<?php
/**
 * Ficha de producto Fardella.
 *
 * @package Fardella
 */

defined( 'ABSPATH' ) || exit;

global $product;

do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	return;
}

$terms         = wp_get_post_terms( get_the_ID(), 'product_cat' );
$category_name = $terms ? $terms[0]->name : 'Catálogo Fardella';
$is_wholesale  = fardella_is_wholesale_customer();
$is_pending    = is_user_logged_in() && in_array( 'fardella_wholesale_pending', (array) wp_get_current_user()->roles, true );
$can_purchase  = $product->is_purchasable();
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'fardella-product', $product ); ?>>
	<section class="fardella-product-stage">
		<div class="fardella-product-gallery">
			<?php do_action( 'woocommerce_before_single_product_summary' ); ?>
		</div>

		<div class="fardella-product-summary">
			<p class="eyebrow dark"><?php echo esc_html( $category_name ); ?></p>
			<h1><?php the_title(); ?></h1>
			<div class="fardella-product-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
			<div class="fardella-product-description"><?php echo wp_kses_post( wpautop( $product->get_short_description() ) ); ?></div>

			<dl class="fardella-product-meta">
				<div><dt>Código</dt><dd><?php echo esc_html( $product->get_sku() ); ?></dd></div>
				<div><dt>Categoría</dt><dd><?php echo esc_html( $category_name ); ?></dd></div>
				<div><dt>Disponibilidad</dt><dd>Sujeta a confirmación</dd></div>
			</dl>

			<?php if ( $can_purchase ) : ?>
				<div class="fardella-buy-box">
					<p><i class="fa-solid fa-circle-check"></i> Compra online habilitada</p>
					<?php woocommerce_template_single_add_to_cart(); ?>
				</div>
			<?php elseif ( $is_pending ) : ?>
				<div class="fardella-access-box pending">
					<i class="fa-regular fa-clock"></i>
					<div><strong>Solicitud en revisión</strong><span>Te avisaremos por email cuando tu cuenta esté habilitada para comprar.</span></div>
				</div>
			<?php else : ?>
				<div class="fardella-access-box">
					<i class="fa-solid fa-shop-lock"></i>
					<div><strong>Ingresá para comprar</strong><span>Accedé con una cuenta aprobada para ver tu precio, elegir la cantidad y añadir el producto al carrito.</span></div>
					<div class="fardella-access-actions">
						<a class="button button-accent" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">Iniciar sesión <i class="fa-solid fa-arrow-right"></i></a>
						<a class="button button-outline-dark" href="<?php echo esc_url( home_url( '/mayoristas/' ) ); ?>">Solicitar cuenta</a>
					</div>
				</div>
			<?php endif; ?>

			<div class="fardella-product-assurances">
				<span><i class="fa-solid fa-truck-fast"></i> Envíos a todo el país</span>
				<span><i class="fa-solid fa-headset"></i> Atención personalizada</span>
			</div>
		</div>
	</section>

	<section class="fardella-product-details">
		<?php do_action( 'woocommerce_after_single_product_summary' ); ?>
	</section>
</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>
