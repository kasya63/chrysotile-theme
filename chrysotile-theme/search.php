<?php
/**
 * Search results template in category-like layout.
 *
 * @package Chrysotile_Child
 */

get_header();

$view     = ( isset( $_GET['view'] ) && 'list' === $_GET['view'] ) ? 'list' : 'grid';
$grid_url = esc_url( add_query_arg( 'view', 'grid' ) );
$list_url = esc_url( add_query_arg( 'view', 'list' ) );
$query    = get_search_query();
?>

<section class="chrysotile-section">
	<div class="chrysotile-section-header chrysotile-category-header">
		<div class="chrysotile-category-header-main">
			<h2><?php echo esc_html( sprintf( __( 'Поиск: %s', 'chrysotile-child' ), $query ) ); ?></h2>
			<div class="chrysotile-cat-view-toggle" aria-label="<?php esc_attr_e( 'Режим отображения найденных материалов', 'chrysotile-child' ); ?>">
				<a href="<?php echo $grid_url; ?>" class="chrysotile-cat-view-btn <?php echo ( 'list' !== $view ) ? 'is-active' : ''; ?>">
					<?php esc_html_e( 'Плитка', 'chrysotile-child' ); ?>
				</a>
				<a href="<?php echo $list_url; ?>" class="chrysotile-cat-view-btn <?php echo ( 'list' === $view ) ? 'is-active' : ''; ?>">
					<?php esc_html_e( 'Список', 'chrysotile-child' ); ?>
				</a>
			</div>
		</div>
	</div>
</section>

<section class="chrysotile-category-layout chrysotile-category-layout--<?php echo esc_attr( $view ); ?>">
	<div class="chrysotile-category-main">
		<?php if ( have_posts() ) : ?>
			<?php if ( 'list' === $view ) : ?>
				<div class="chrysotile-cat-list-view" aria-label="<?php esc_attr_e( 'Список найденных материалов', 'chrysotile-child' ); ?>">
					<?php
					while ( have_posts() ) :
						the_post();
						?>
					<article class="chrysotile-cat-list-item<?php if ( ! has_post_thumbnail() ) echo ' no-thumb'; ?>">
						<?php if ( has_post_thumbnail() ) : ?>
							<a class="chrysotile-cat-list-thumb" href="<?php the_permalink(); ?>">
								<?php the_post_thumbnail( 'medium_large' ); ?>
								<span class="chrysotile-cat-date-badge"><?php echo esc_html( get_the_date() ); ?></span>
							</a>
						<?php endif; ?>
						<div class="chrysotile-cat-list-body">
							<a href="<?php the_permalink(); ?>">
								<?php if ( ! has_post_thumbnail() ) : ?>
									<span class="chrysotile-no-thumb-date"><?php echo esc_html( get_the_date() ); ?></span>
								<?php endif; ?>
								<h2><?php the_title(); ?></h2>
							</a>
						</div>
					</article>
					<?php endwhile; ?>
				</div>
			<?php else : ?>
				<div class="chrysotile-cat-grid" aria-label="<?php esc_attr_e( 'Плитка найденных материалов', 'chrysotile-child' ); ?>">
					<?php
					while ( have_posts() ) :
						the_post();
						?>
					<article class="chrysotile-cat-grid-card<?php if ( ! has_post_thumbnail() ) echo ' no-thumb'; ?>">
						<?php if ( has_post_thumbnail() ) : ?>
							<a class="chrysotile-cat-grid-thumb" href="<?php the_permalink(); ?>">
								<?php the_post_thumbnail( 'medium_large' ); ?>
								<span class="chrysotile-cat-date-badge"><?php echo esc_html( get_the_date() ); ?></span>
							</a>
						<?php endif; ?>
						<a class="chrysotile-cat-grid-title" href="<?php the_permalink(); ?>">
							<?php if ( ! has_post_thumbnail() ) : ?>
								<span class="chrysotile-no-thumb-date"><?php echo esc_html( get_the_date() ); ?></span>
							<?php endif; ?>
							<h2><?php the_title(); ?></h2>
						</a>
					</article>
					<?php endwhile; ?>
				</div>
			<?php endif; ?>

			<?php
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
			<p><?php esc_html_e( 'По вашему запросу ничего не найдено.', 'chrysotile-child' ); ?></p>
		<?php endif; ?>
	</div>

	<aside class="chrysotile-category-sidebar">
		<?php
		$popular_posts = new WP_Query(
			array(
				's'                   => $query,
				'posts_per_page'      => 6,
				'orderby'             => 'comment_count',
				'order'               => 'DESC',
				'ignore_sticky_posts' => true,
				'post_status'         => 'publish',
			)
		);
		?>
		<?php if ( $popular_posts->have_posts() ) : ?>
			<section class="chrysotile-sidebar-box">
				<h3><?php esc_html_e( 'Популярное по запросу', 'chrysotile-child' ); ?></h3>
				<ul class="chrysotile-sidebar-popular">
					<?php
					while ( $popular_posts->have_posts() ) :
						$popular_posts->the_post();
						?>
					<li class="chrysotile-sidebar-popular-item<?php if ( ! has_post_thumbnail() ) echo ' no-thumb'; ?>">
						<?php if ( has_post_thumbnail() ) : ?>
							<a class="chrysotile-sidebar-popular-thumb" href="<?php the_permalink(); ?>">
								<?php the_post_thumbnail( 'thumbnail' ); ?>
							</a>
						<?php endif; ?>
						<div class="chrysotile-sidebar-popular-body">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							<div class="chrysotile-meta"><?php echo esc_html( get_the_date() ); ?></div>
						</div>
					</li>
					<?php endwhile; ?>
					<?php wp_reset_postdata(); ?>
				</ul>
			</section>
		<?php endif; ?>

		<?php if ( is_active_sidebar( 'chrysotile-sidebar' ) ) : ?>
			<?php dynamic_sidebar( 'chrysotile-sidebar' ); ?>
		<?php endif; ?>
	</aside>
</section>

<?php get_footer(); ?>
