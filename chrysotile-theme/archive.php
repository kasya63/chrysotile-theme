<?php
/**
 * Universal archive template in category-like layout.
 *
 * @package Chrysotile_Child
 */

get_header();

$view     = ( isset( $_GET['view'] ) && 'list' === $_GET['view'] ) ? 'list' : 'grid';
$grid_url = esc_url( add_query_arg( 'view', 'grid' ) );
$list_url = esc_url( add_query_arg( 'view', 'list' ) );

$heading      = wp_strip_all_tags( get_the_archive_title() );
$description  = '';
$empty_text   = __( 'В этом разделе пока нет новостей.', 'chrysotile-child' );
$popular_text = __( 'Популярное в разделе', 'chrysotile-child' );
$popular_args = array(
	'posts_per_page'      => 6,
	'orderby'             => 'comment_count',
	'order'               => 'DESC',
	'ignore_sticky_posts' => true,
	'post_status'         => 'publish',
);

if ( is_tag() ) {
	$current_tag = get_queried_object();
	$tag_id      = ( $current_tag instanceof WP_Term ) ? (int) $current_tag->term_id : 0;
	$heading     = single_tag_title( '', false );
	$description = tag_description();
	$empty_text  = __( 'По этой метке пока нет новостей.', 'chrysotile-child' );
	$popular_text = __( 'Популярное по метке', 'chrysotile-child' );
	if ( $tag_id > 0 ) {
		$popular_args['tax_query'] = array(
			array(
				'taxonomy' => 'post_tag',
				'field'    => 'term_id',
				'terms'    => $tag_id,
			),
		);
	}
} elseif ( is_author() ) {
	$author_obj = get_queried_object();
	$author_id  = isset( $author_obj->ID ) ? (int) $author_obj->ID : 0;
	$heading    = get_the_author_meta( 'display_name', $author_id );
	$description = wpautop( get_the_author_meta( 'description', $author_id ) );
	$empty_text  = __( 'У автора пока нет новостей.', 'chrysotile-child' );
	$popular_text = __( 'Популярное у автора', 'chrysotile-child' );
	if ( $author_id > 0 ) {
		$popular_args['author'] = $author_id;
	}
} else {
	$description = get_the_archive_description();
}
?>

<section class="chrysotile-section">
	<div class="chrysotile-section-header chrysotile-category-header">
		<div class="chrysotile-category-header-main">
			<h2><?php echo esc_html( $heading ); ?></h2>
			<?php if ( '' !== trim( wp_strip_all_tags( (string) $description ) ) ) : ?>
				<div class="chrysotile-meta chrysotile-category-desc"><?php echo wp_kses_post( $description ); ?></div>
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
				<div class="chrysotile-cat-list-view" aria-label="<?php esc_attr_e( 'Список материалов раздела', 'chrysotile-child' ); ?>">
					<?php
					while ( have_posts() ) :
						the_post();
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
					<?php endwhile; ?>
				</div>
			<?php else : ?>
				<div class="chrysotile-cat-grid" aria-label="<?php esc_attr_e( 'Материалы раздела', 'chrysotile-child' ); ?>">
					<?php
					while ( have_posts() ) :
						the_post();
						?>
						<article class="chrysotile-cat-grid-card">
							<a class="chrysotile-cat-grid-thumb" href="<?php the_permalink(); ?>">
								<?php if ( has_post_thumbnail() ) : ?>
									<?php the_post_thumbnail( 'medium_large' ); ?>
								<?php endif; ?>
								<span class="chrysotile-cat-date-badge"><?php echo esc_html( get_the_date() ); ?></span>
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
			<p><?php echo esc_html( $empty_text ); ?></p>
		<?php endif; ?>
	</div>

	<aside class="chrysotile-category-sidebar">
		<?php $popular_posts = new WP_Query( $popular_args ); ?>
		<?php if ( $popular_posts->have_posts() ) : ?>
			<section class="chrysotile-sidebar-box">
				<h3><?php echo esc_html( $popular_text ); ?></h3>
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
