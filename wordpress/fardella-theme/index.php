<?php
/**
 * Plantilla de respaldo.
 *
 * @package Fardella
 */

get_header();
?>
<main class="wp-content-page section-pad">
	<header class="wp-page-heading"><p class="eyebrow dark">Fardella S.A.</p><h1><?php bloginfo( 'name' ); ?></h1></header>
	<div class="wp-post-list">
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class(); ?>><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><?php the_excerpt(); ?></article>
		<?php endwhile; ?>
	</div>
</main>
<?php get_footer(); ?>

