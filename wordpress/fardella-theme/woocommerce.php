<?php
/**
 * Contenedor WooCommerce.
 *
 * @package Fardella
 */

get_header();
?>
<main class="fardella-woocommerce section-pad">
	<?php woocommerce_content(); ?>
</main>
<?php get_footer(); ?>

