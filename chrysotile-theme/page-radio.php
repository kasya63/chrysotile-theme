<?php
/**
 * Template Name: Radio Landing
 * Template Post Type: page
 *
 * @package Chrysotile_Child
 */

get_header();
?>

<section class="chrysotile-radio-hero">
	<h1><?php esc_html_e( 'Chrysotile Radio', 'chrysotile-child' ); ?></h1>
	<p>
		<?php
		esc_html_e(
			'Страница для будущего интернет-радио. Когда запустите поток, сюда можно добавить плеер, программу эфира, архив выпусков и сетку передач.',
			'chrysotile-child'
		);
		?>
	</p>
	<div class="chrysotile-single-tools">
		<button class="chrysotile-radio-btn" type="button"><?php esc_html_e( 'Live soon', 'chrysotile-child' ); ?></button>
		<a class="chrysotile-theme-toggle" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to news', 'chrysotile-child' ); ?></a>
	</div>
</section>

<section class="chrysotile-section">
	<?php if ( have_posts() ) : ?>
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article>
				<?php the_content(); ?>
			</article>
		<?php endwhile; ?>
	<?php endif; ?>
</section>

<?php get_footer(); ?>
