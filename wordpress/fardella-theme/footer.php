<?php
/**
 * Pie del sitio.
 *
 * @package Fardella
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$theme_uri = get_template_directory_uri();
?>
<footer class="footer">
	<div class="footer-main">
		<a class="wordmark footer-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( $theme_uri . '/assets/images/fardella/logo-fardella-official.png' ); ?>" alt="Fardella S.A."></a>
		<p>Bicicletas, repuestos y accesorios para cada forma de rodar.</p>
		<nav class="footer-links" aria-label="Navegación del pie">
			<a href="<?php echo esc_url( home_url( '/#categorias' ) ); ?>">Categorías</a>
			<a href="<?php echo esc_url( home_url( '/#productos' ) ); ?>">Productos</a>
			<a href="<?php echo esc_url( home_url( '/#marcas' ) ); ?>">Marcas</a>
			<a href="<?php echo esc_url( home_url( '/mayoristas/' ) ); ?>">Acceso mayorista</a>
			<a href="<?php echo esc_url( home_url( '/#consulta' ) ); ?>">Contacto</a>
		</nav>
		<div class="footer-contact"><a href="mailto:info@fardella.com.ar">info@fardella.com.ar</a><a href="tel:+541160204963">11-6020-4963</a></div>
	</div>
	<div class="footer-bottom"><span>© <?php echo esc_html( gmdate( 'Y' ) ); ?> Fardella S.A.</span><span>Buenos Aires, Argentina</span></div>
	<div class="footer-credit">Hecho por <a href="https://zigodev.com.ar" target="_blank" rel="noopener noreferrer">zigodev</a></div>
</footer>
<div class="toast" role="status" aria-live="polite"></div>
<?php wp_footer(); ?>
</body>
</html>

