<?php
/**
 * Default page template.
 *
 * @package Chrysotile_Child
 */

get_header();
?>

<section class="chrysotile-section">
	<?php if ( have_posts() ) : ?>
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article>
				<h1><?php the_title(); ?></h1>
				<div><?php the_content(); ?></div>
			</article>
		<?php endwhile; ?>
	<?php endif; ?>
</section>

<?php get_footer(); ?>
