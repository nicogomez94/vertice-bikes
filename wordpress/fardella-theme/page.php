<?php
/**
 * Plantilla estándar de páginas.
 *
 * @package Fardella
 */

get_header();
?>
<main class="wp-content-page section-pad">
	<?php while ( have_posts() ) : the_post(); ?>
		<header class="wp-page-heading"><p class="eyebrow dark">Fardella S.A.</p><h1><?php the_title(); ?></h1></header>
		<div class="wp-page-content"><?php the_content(); ?></div>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>

