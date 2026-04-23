<?php
/**
 * Category template — grid/list for leaf rubrics; parent rubrics with children show child sections (3×3 each).
 *
 * @package Chrysotile_Child
 */

get_header();

$current_category = get_queried_object();
$is_parent_hub    = $current_category instanceof WP_Term
	&& isset( $current_category->taxonomy )
	&& 'category' === $current_category->taxonomy
	&& chrysotile_child_category_is_parent_archive_hub( $current_category );

if ( $is_parent_hub ) {
	$child_terms = chrysotile_child_category_get_visible_child_terms( $current_category );
	?>
	<section class="chrysotile-section">
		<div class="chrysotile-section-header chrysotile-category-header">
			<?php /* Заголовок родительской рубрики: размеры — в style.css, поиск по «Родительская рубрика — заголовок». */ ?>
			<div class="chrysotile-category-header-main chrysotile-category-header-main--parent-hub">
				<h2><?php echo esc_html( single_cat_title( '', false ) ); ?></h2>
				<?php if ( category_description() ) : ?>
					<div class="chrysotile-meta chrysotile-category-desc"><?php echo wp_kses_post( category_description() ); ?></div>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<section class="chrysotile-category-layout chrysotile-category-layout--parent-hub">
		<div class="chrysotile-category-main">
			<?php foreach ( $child_terms as $child_term ) : ?>
				<?php
				$child_posts = new WP_Query(
					array(
						'cat'                 => (int) $child_term->term_id,
						'posts_per_page'      => 9,
						'orderby'             => 'date',
						'order'               => 'DESC',
						'ignore_sticky_posts' => true,
						'post_status'         => 'publish',
						'no_found_rows'       => true,
					)
				);
				$child_link = get_category_link( $child_term->term_id );
				?>
				<section class="chrysotile-section chrysotile-category-child-hub" aria-label="<?php echo esc_attr( $child_term->name ); ?>">
					<div class="chrysotile-section-header">
						<h2>
							<a href="<?php echo esc_url( $child_link ); ?>"><?php echo esc_html( $child_term->name ); ?></a>
						</h2>
						<a class="chrysotile-more-link" href="<?php echo esc_url( $child_link ); ?>"><?php esc_html_e( 'Смотреть все', 'chrysotile-child' ); ?></a>
					</div>
					<?php if ( $child_posts->have_posts() ) : ?>
						<div class="chrysotile-only-desktop-block">
							<div class="chrysotile-stream-grid" aria-label="<?php esc_attr_e( 'Материалы рубрики', 'chrysotile-child' ); ?>">
								<?php
								while ( $child_posts->have_posts() ) :
									$child_posts->the_post();
									?>
									<article class="chrysotile-stream-card">
										<a class="chrysotile-stream-card__link" href="<?php the_permalink(); ?>">
											<span class="chrysotile-thumb-frame chrysotile-thumb-frame--stream">
												<?php if ( has_post_thumbnail() ) : ?>
													<?php the_post_thumbnail( 'medium_large' ); ?>
												<?php endif; ?>
											</span>
											<span class="chrysotile-stream-card__cap">
												<h3 class="chrysotile-stream-card__title"><?php the_title(); ?></h3>
											</span>
										</a>
									</article>
									<?php
								endwhile;
								$child_posts->rewind_posts();
								?>
							</div>
						</div>
						<div class="chrysotile-only-mobile-block">
							<div class="chrysotile-cat-list-view chrysotile-cat-list-view--parent-hub-child" aria-label="<?php esc_attr_e( 'Материалы рубрики', 'chrysotile-child' ); ?>">
								<?php
								$chrysotile_parent_hub_mobile = 0;
								while ( $child_posts->have_posts() && $chrysotile_parent_hub_mobile < 6 ) :
									$child_posts->the_post();
									++$chrysotile_parent_hub_mobile;
									if ( 1 === $chrysotile_parent_hub_mobile ) :
										?>
										<article class="chrysotile-lead-card chrysotile-front-stream-mobile-lead">
											<a class="chrysotile-lead-card__link" href="<?php the_permalink(); ?>">
												<span class="chrysotile-thumb-frame chrysotile-thumb-frame--lead">
													<?php if ( has_post_thumbnail() ) : ?>
														<?php the_post_thumbnail( 'large' ); ?>
													<?php endif; ?>
													<time class="chrysotile-thumb-date chrysotile-thumb-date--lead" datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
												</span>
												<span class="chrysotile-lead-card__cap">
													<h3 class="chrysotile-lead-card__title"><?php the_title(); ?></h3>
												</span>
											</a>
										</article>
										<?php
									else :
										?>
										<article class="chrysotile-cat-list-item">
											<a class="chrysotile-cat-list-thumb" href="<?php the_permalink(); ?>">
												<?php if ( has_post_thumbnail() ) : ?>
													<?php the_post_thumbnail( 'medium_large' ); ?>
												<?php endif; ?>
												<span class="chrysotile-cat-date-badge">
													<?php echo esc_html( get_the_date() ); ?>
												</span>
											</a>
											<div class="chrysotile-cat-list-body">
												<a href="<?php the_permalink(); ?>">
													<h2><?php the_title(); ?></h2>
												</a>
											</div>
										</article>
										<?php
									endif;
								endwhile;
								wp_reset_postdata();
								?>
							</div>
						</div>
					<?php else : ?>
						<p class="chrysotile-category-child-hub-empty"><?php esc_html_e( 'В этой рубрике пока нет материалов.', 'chrysotile-child' ); ?></p>
					<?php endif; ?>
				</section>
			<?php endforeach; ?>
		</div>

		<aside class="chrysotile-category-sidebar">
			<?php
			$popular_posts = new WP_Query(
				array(
					'posts_per_page'      => 6,
					'orderby'             => 'comment_count',
					'order'               => 'DESC',
					'ignore_sticky_posts' => true,
					'post_status'         => 'publish',
					'no_found_rows'       => true,
					'tax_query'           => array(
						array(
							'taxonomy'         => 'category',
							'field'            => 'term_id',
							'terms'            => (int) $current_category->term_id,
							'include_children' => true,
						),
					),
				)
			);
			?>
			<?php if ( $popular_posts->have_posts() ) : ?>
				<section class="chrysotile-sidebar-box">
					<h3><?php esc_html_e( 'Популярное в разделе', 'chrysotile-child' ); ?></h3>
					<ul class="chrysotile-sidebar-popular">
						<?php
						while ( $popular_posts->have_posts() ) :
							$popular_posts->the_post();
							?>
							<li class="chrysotile-sidebar-popular-item">
								<a class="chrysotile-sidebar-popular-thumb" href="<?php the_permalink(); ?>">
									<?php if ( has_post_thumbnail() ) : ?>
										<?php the_post_thumbnail( 'thumbnail' ); ?>
									<?php endif; ?>
								</a>
								<div class="chrysotile-sidebar-popular-body">
									<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
									<div class="chrysotile-meta"><?php echo esc_html( get_the_date() ); ?></div>
								</div>
							</li>
							<?php
						endwhile;
						wp_reset_postdata();
						?>
					</ul>
				</section>
			<?php endif; ?>

			<?php if ( is_active_sidebar( 'chrysotile-sidebar' ) ) : ?>
				<?php dynamic_sidebar( 'chrysotile-sidebar' ); ?>
			<?php endif; ?>
		</aside>
	</section>
	<?php
	get_footer();
	return;
}

$view     = ( isset( $_GET['view'] ) && 'list' === $_GET['view'] ) ? 'list' : 'grid';
$grid_url = esc_url( add_query_arg( 'view', 'grid' ) );
$list_url = esc_url( add_query_arg( 'view', 'list' ) );
?>

<section class="chrysotile-section">
	<div class="chrysotile-section-header chrysotile-category-header">
		<div class="chrysotile-category-header-main">
			<h2><?php echo esc_html( single_cat_title( '', false ) ); ?></h2>
			<?php if ( category_description() ) : ?>
				<div class="chrysotile-meta chrysotile-category-desc"><?php echo wp_kses_post( category_description() ); ?></div>
			<?php endif; ?>
			<div class="chrysotile-cat-view-toggle" aria-label="<?php esc_attr_e( 'Режим отображения списка новостей', 'chrysotile-child' ); ?>">
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
				<div class="chrysotile-cat-list-view" aria-label="<?php esc_attr_e( 'Список материалов рубрики', 'chrysotile-child' ); ?>">
					<?php
					while ( have_posts() ) :
						the_post();
						?>
						<article class="chrysotile-cat-list-item">
							<a class="chrysotile-cat-list-thumb" href="<?php the_permalink(); ?>">
								<?php if ( has_post_thumbnail() ) : ?>
									<?php the_post_thumbnail( 'medium_large' ); ?>
								<?php endif; ?>
								<span class="chrysotile-cat-date-badge">
									<?php echo esc_html( get_the_date() ); ?>
								</span>
							</a>
							<div class="chrysotile-cat-list-body">
								<a href="<?php the_permalink(); ?>">
									<h2><?php the_title(); ?></h2>
								</a>
							</div>
						</article>
					<?php endwhile; ?>
				</div>
			<?php else : ?>
				<div class="chrysotile-cat-grid" aria-label="<?php esc_attr_e( 'Материалы рубрики', 'chrysotile-child' ); ?>">
					<?php
					while ( have_posts() ) :
						the_post();
						?>
						<article class="chrysotile-cat-grid-card">
							<a class="chrysotile-cat-grid-thumb" href="<?php the_permalink(); ?>">
								<?php if ( has_post_thumbnail() ) : ?>
									<?php the_post_thumbnail( 'medium_large' ); ?>
								<?php endif; ?>
								<span class="chrysotile-cat-date-badge">
									<?php echo esc_html( get_the_date() ); ?>
								</span>
							</a>
							<a class="chrysotile-cat-grid-title" href="<?php the_permalink(); ?>">
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
			<p><?php esc_html_e( 'В этой рубрике пока нет новостей.', 'chrysotile-child' ); ?></p>
		<?php endif; ?>
	</div>

	<aside class="chrysotile-category-sidebar">
		<?php
		$popular_posts = new WP_Query(
			array(
				'cat'                 => isset( $current_category->term_id ) ? (int) $current_category->term_id : 0,
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
				<h3><?php esc_html_e( 'Популярное в рубрике', 'chrysotile-child' ); ?></h3>
				<ul class="chrysotile-sidebar-popular">
					<?php
					while ( $popular_posts->have_posts() ) :
						$popular_posts->the_post();
						?>
						<li class="chrysotile-sidebar-popular-item">
							<a class="chrysotile-sidebar-popular-thumb" href="<?php the_permalink(); ?>">
								<?php if ( has_post_thumbnail() ) : ?>
									<?php the_post_thumbnail( 'thumbnail' ); ?>
								<?php endif; ?>
							</a>
							<div class="chrysotile-sidebar-popular-body">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
								<div class="chrysotile-meta"><?php echo esc_html( get_the_date() ); ?></div>
							</div>
						</li>
						<?php
					endwhile;
					wp_reset_postdata();
					?>
				</ul>
			</section>
		<?php endif; ?>

		<?php if ( is_active_sidebar( 'chrysotile-sidebar' ) ) : ?>
			<?php dynamic_sidebar( 'chrysotile-sidebar' ); ?>
		<?php endif; ?>
	</aside>
</section>

<?php get_footer(); ?>
