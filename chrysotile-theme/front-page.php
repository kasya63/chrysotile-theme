<?php
/**
 * Front page template.
 *
 * @package Chrysotile_Child
 */

get_header();

$chrysotile_glavnye_term = get_term_by( 'slug', 'glavnye', 'category' );
$hero_query_args          = array(
	'posts_per_page'      => 5,
	'orderby'             => 'date',
	'order'               => 'DESC',
	'ignore_sticky_posts' => true,
	'post_status'         => 'publish',
	'no_found_rows'       => true,
);

if ( $chrysotile_glavnye_term instanceof WP_Term ) {
	$hero_query_args['cat'] = (int) $chrysotile_glavnye_term->term_id;
} else {
	$hero_query_args['post__in'] = array( 0 );
}

$hero_posts = new WP_Query( $hero_query_args );
?>

<?php if ( $hero_posts->have_posts() ) : ?>
	<?php
		$hero_posts_array = $hero_posts->posts;
		$center_post      = $hero_posts_array[0];
		$left_posts       = array_slice( $hero_posts_array, 1, 2 );
		$right_posts      = array_slice( $hero_posts_array, 3, 2 );
	?>
	<div class="chrysotile-front-stage">
		<header class="chrysotile-front-head">
			<h2 class="chrysotile-front-kicker"><?php esc_html_e( 'Главное сегодня', 'chrysotile-child' ); ?></h2>
		</header>
		<section class="chrysotile-front-hero" aria-label="<?php esc_attr_e( 'Главное сегодня', 'chrysotile-child' ); ?>">
			<div class="chrysotile-front-rail">
				<?php foreach ( $left_posts as $post ) : ?>
					<?php setup_postdata( $post ); ?>
					<article class="chrysotile-front-mini-card">
						<a class="chrysotile-front-mini-card__link" href="<?php the_permalink(); ?>">
							<span class="chrysotile-thumb-frame chrysotile-thumb-frame--mini">
								<?php if ( has_post_thumbnail() ) : ?>
									<?php the_post_thumbnail( 'medium_large' ); ?>
								<?php endif; ?>
								<time class="chrysotile-thumb-date" datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
							</span>
							<span class="chrysotile-front-mini-card__cap">
								<h3 class="chrysotile-front-mini-card__title"><?php the_title(); ?></h3>
							</span>
						</a>
					</article>
				<?php endforeach; ?>
			</div>

			<div class="chrysotile-front-main">
				<?php
				$post = $center_post;
				setup_postdata( $post );
				?>
				<article class="chrysotile-lead-card">
					<a class="chrysotile-lead-card__link" href="<?php the_permalink(); ?>">
						<span class="chrysotile-thumb-frame chrysotile-thumb-frame--lead">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'large' ); ?>
							<?php endif; ?>
							<time class="chrysotile-thumb-date chrysotile-thumb-date--lead" datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
						</span>
						<span class="chrysotile-lead-card__cap">
							<h1 class="chrysotile-lead-card__title"><?php the_title(); ?></h1>
						</span>
					</a>
				</article>
			</div>

			<div class="chrysotile-front-rail">
				<?php foreach ( $right_posts as $post ) : ?>
					<?php setup_postdata( $post ); ?>
					<article class="chrysotile-front-mini-card">
						<a class="chrysotile-front-mini-card__link" href="<?php the_permalink(); ?>">
							<span class="chrysotile-thumb-frame chrysotile-thumb-frame--mini">
								<?php if ( has_post_thumbnail() ) : ?>
									<?php the_post_thumbnail( 'medium_large' ); ?>
								<?php endif; ?>
								<time class="chrysotile-thumb-date" datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
							</span>
							<span class="chrysotile-front-mini-card__cap">
								<h3 class="chrysotile-front-mini-card__title"><?php the_title(); ?></h3>
							</span>
						</a>
					</article>
				<?php endforeach; ?>
			</div>
		</section>
	</div>
	<?php wp_reset_postdata(); ?>
<?php endif; ?>

<?php
$chrysotile_front_streams = array(
	array(
		'slug'       => 'gosudarstvo',
		'label'      => __( 'В стране', 'chrysotile-child' ),
		'posts'      => 20,
		'grid_class' => 'chrysotile-stream-grid chrysotile-stream-grid--cols-4',
	),
	array(
		'slug'       => 'region',
		'label'      => __( 'В регионе', 'chrysotile-child' ),
		'posts'      => 16,
		'grid_class' => 'chrysotile-stream-grid chrysotile-stream-grid--cols-4',
	),
	array(
		'slug'       => 'people',
		'label'      => __( 'Люди', 'chrysotile-child' ),
		'posts'      => 8,
		'grid_class' => 'chrysotile-stream-grid chrysotile-stream-grid--cols-4',
	),
);

foreach ( $chrysotile_front_streams as $stream ) :
	$category = get_term_by( 'slug', $stream['slug'], 'category' );
	if ( ! $category instanceof WP_Term ) {
		continue;
	}

	$stream_heading = ( ! empty( $stream['label'] ) ) ? $stream['label'] : chrysotile_child_category_nav_label( $category );
	$category_posts = new WP_Query(
		array(
			'cat'                 => (int) $category->term_id,
			'posts_per_page'      => (int) $stream['posts'],
			'orderby'             => 'date',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
			'post_status'         => 'publish',
			'no_found_rows'       => true,
		)
	);
	$chrysotile_stream_is_cols4 = ( false !== strpos( $stream['grid_class'], 'cols-4' ) );
	?>
	<section class="chrysotile-section<?php echo $chrysotile_stream_is_cols4 ? ' chrysotile-section--stream-cols4' : ''; ?>">
		<div class="chrysotile-section-header">
			<h2>
				<a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>">
					<?php echo esc_html( $stream_heading ); ?>
				</a>
			</h2>
			<a class="chrysotile-more-link" href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>"><?php esc_html_e( 'Смотреть все', 'chrysotile-child' ); ?></a>
		</div>
		<?php if ( $category_posts->have_posts() ) : ?>
			<?php if ( $chrysotile_stream_is_cols4 ) : ?>
				<div class="chrysotile-only-desktop-block">
					<div class="<?php echo esc_attr( $stream['grid_class'] ); ?>">
						<?php
						while ( $category_posts->have_posts() ) :
							$category_posts->the_post();
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
						$category_posts->rewind_posts();
						?>
					</div>
				</div>
				<div class="chrysotile-only-mobile-block">
					<div class="chrysotile-cat-list-view chrysotile-front-cols4-list" aria-label="<?php echo esc_attr( $stream_heading ); ?>">
						<?php
						$chrysotile_stream_mobile_i = 0;
						while ( $category_posts->have_posts() ) :
							$category_posts->the_post();
							++$chrysotile_stream_mobile_i;
							if ( 1 === $chrysotile_stream_mobile_i ) :
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
										<span class="chrysotile-cat-date-badge"><?php echo esc_html( get_the_date() ); ?></span>
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
				<div class="<?php echo esc_attr( $stream['grid_class'] ); ?>">
					<?php
					while ( $category_posts->have_posts() ) :
						$category_posts->the_post();
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
					wp_reset_postdata();
					?>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	</section>
<?php endforeach; ?>

<?php get_footer(); ?>
