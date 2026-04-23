<?php
/**
 * Main index template.
 *
 * @package Chrysotile_Child
 */

get_header();
?>

<section class="chrysotile-archive">
	<div class="chrysotile-article-list">
		<?php if ( have_posts() ) : ?>
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article class="chrysotile-article-card">
					<a href="<?php the_permalink(); ?>">
						<?php if ( has_post_thumbnail() ) : ?>
							<?php the_post_thumbnail( 'medium_large' ); ?>
						<?php endif; ?>
					</a>
					<div>
						<a href="<?php the_permalink(); ?>">
							<h2><?php the_title(); ?></h2>
						</a>
						<?php chrysotile_child_primary_categories( get_the_ID() ); ?>
					</div>
				</article>
				<?php
			endwhile;

			the_posts_pagination(
				array(
					'mid_size'           => 1,
					'prev_text'          => __( 'Назад', 'chrysotile-child' ),
					'next_text'          => __( 'Далее', 'chrysotile-child' ),
					'screen_reader_text' => '',
				)
			);
			?>
		<?php else : ?>
			<p><?php esc_html_e( 'No news found yet.', 'chrysotile-child' ); ?></p>
		<?php endif; ?>
	</div>

	<aside>
		<?php if ( is_active_sidebar( 'chrysotile-sidebar' ) ) : ?>
			<?php dynamic_sidebar( 'chrysotile-sidebar' ); ?>
		<?php endif; ?>
	</aside>
</section>

<?php get_footer(); ?>
