<?php
/**
 * Single post template — modern news article layout.
 *
 * @package Chrysotile_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$post_id = get_the_ID();

	$categories             = get_the_category();
	$default_cat_id         = (int) get_option( 'default_category' );
	$generic_category_slugs = array( 'vse-novosti', 'vse-rubriki', 'all-news', 'all-categories', 'uncategorized','glavnye' );
	$generic_category_names = array( 'Все новости', 'Все рубрики', 'Uncategorized', 'Главные новости' );
	$display_categories     = array();

	if ( is_array( $categories ) ) {
		foreach ( $categories as $category ) {
			if ( ! $category instanceof WP_Term ) {
				continue;
			}

			$is_default = $default_cat_id > 0 && (int) $category->term_id === $default_cat_id;
			$is_generic = in_array( (string) $category->slug, $generic_category_slugs, true ) || in_array( (string) $category->name, $generic_category_names, true );

			if ( ! $is_default && ! $is_generic ) {
				$display_categories[] = $category;
			}
		}
	}

	$is_video_post = false;
	if ( is_array( $categories ) ) {
		foreach ( $categories as $category ) {
			if ( $category instanceof WP_Term && 'video' === (string) $category->slug ) {
				$is_video_post = true;
				break;
			}
		}
	}

	$reading_min        = function_exists( 'chrysotile_single_reading_time_minutes' ) ? chrysotile_single_reading_time_minutes( $post_id ) : 1;
	$view_count         = function_exists( 'chrysotile_get_post_views' ) ? chrysotile_get_post_views( $post_id ) : 0;
	$subtitle           = function_exists( 'chrysotile_get_article_subtitle' ) ? chrysotile_get_article_subtitle( $post_id ) : '';
	$article_numeric_id = function_exists( 'chrysotile_single_article_identifier' ) ? chrysotile_single_article_identifier( $post_id ) : (int) $post_id;
	$content_raw        = get_the_content();
	$content_html       = apply_filters( 'the_content', $content_raw );
	$parsed             = function_exists( 'chrysotile_single_content_with_toc' ) ? chrysotile_single_content_with_toc( $content_html ) : array(
		'content' => $content_html,
		'toc'     => array(),
	);
	$article_body       = $parsed['content'];
	$toc_items          = $parsed['toc'];
	$permalink          = get_permalink();
	$enc_url            = rawurlencode( $permalink );
	$enc_title          = rawurlencode( wp_strip_all_tags( get_the_title() ) );
	$share_telegram     = 'https://t.me/share/url?url=' . $enc_url . '&text=' . $enc_title;
	$share_x            = 'https://twitter.com/intent/tweet?url=' . $enc_url . '&text=' . $enc_title;
	$share_facebook     = 'https://www.facebook.com/sharer/sharer.php?u=' . $enc_url;
	$share_whatsapp     = 'https://api.whatsapp.com/send?text=' . rawurlencode( wp_strip_all_tags( get_the_title() ) . ' ' . $permalink );
	$share_threads      = 'https://www.threads.net/intent/post?text=' . rawurlencode( wp_strip_all_tags( get_the_title() ) . ' ' . $permalink );
	$share_post_icons_uri = trailingslashit( get_stylesheet_directory_uri() ) . 'assets/post/';
	$post_tags            = get_the_tags( $post_id );
	$author_id          = (int) get_post_field( 'post_author', $post_id );
	$author_name        = get_the_author_meta( 'display_name', $author_id );
	$author_name        = is_string( $author_name ) ? trim( $author_name ) : '';
	$sidebar_q          = new WP_Query(
		array(
			'posts_per_page'      => 10,
			'post__not_in'        => array( $post_id ),
			'post_status'         => 'publish',
			'orderby'             => 'date',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
		)
	);
	$related_q          = function_exists( 'chrysotile_single_related_most_viewed' ) ? chrysotile_single_related_most_viewed( $post_id, 3 ) : null;
	?>

	<main id="primary" class="news-single-site-main">
		<div class="news-single-wrap">
			<div class="news-single-layout">
				<article <?php post_class( 'news-single-article' ); ?> itemscope itemtype="https://schema.org/Article">
					<meta itemprop="identifier" content="<?php echo esc_attr( (string) $article_numeric_id ); ?>">

					<header class="news-single-header">
						<h1 class="news-single-title" itemprop="headline"><?php the_title(); ?></h1>

						<?php if ( '' !== $subtitle ) : ?>
							<p class="news-single-lead"><?php echo esc_html( $subtitle ); ?></p>
						<?php endif; ?>

						<div class="news-single-meta news-single-meta--primary">
							<?php
							$meta_parts   = array();
							$meta_parts[] = '<time class="news-single-date" datetime="' . esc_attr( get_the_date( 'c' ) ) . '" itemprop="datePublished">' . esc_html( get_the_date() ) . '</time>';

							if ( 1 !== $author_id && 3 !== $author_id && '' !== $author_name ) {
								$meta_parts[] = '<span class="news-single-author" itemprop="author" itemscope itemtype="https://schema.org/Person"><a class="news-single-author-link" href="' . esc_url( get_author_posts_url( $author_id ) ) . '" itemprop="url"><span itemprop="name">' . esc_html( $author_name ) . '</span></a></span>';
							}

							$meta_parts[] = '<span class="news-single-readtime">' . sprintf( esc_html__( '~%d мин чтения', 'chrysotile-child' ), (int) $reading_min ) . '</span>';
							$meta_parts[] = '<span class="news-single-views" aria-label="' . esc_attr__( 'Просмотры', 'chrysotile-child' ) . '">' . sprintf( esc_html__( 'Просмотров: %s', 'chrysotile-child' ), esc_html( number_format_i18n( $view_count ) ) ) . '</span>';

							echo implode( '<span class="news-single-meta-sep" aria-hidden="true"> · </span>', $meta_parts ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
						</div>

						<?php if ( ! empty( $display_categories ) ) : ?>
							<div class="news-single-cats news-single-cats--header">
								<span class="news-single-cats-label"><?php esc_html_e( 'Рубрики:', 'chrysotile-child' ); ?></span>
								<span class="news-single-cat-links">
									<?php
									$cat_links = array();
									foreach ( $display_categories as $category ) {
										if ( $category instanceof WP_Term ) {
											$cat_links[] = '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '">' . esc_html( $category->name ) . '</a>';
										}
									}
									echo implode( '<span class="news-single-cat-sep"> </span>', $cat_links ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									?>
								</span>
							</div>
						<?php endif; ?>
					</header>

					<?php if ( ! $is_video_post && has_post_thumbnail() ) : ?>
						<?php
						$featured_attachment_id = (int) get_post_thumbnail_id( $post_id );
						$featured_caption       = $featured_attachment_id ? wp_get_attachment_caption( $featured_attachment_id ) : '';
						?>
						<figure class="news-single-featured">
							<div class="news-single-featured-inner">
								<?php
								the_post_thumbnail(
									'full',
									array(
										'class'    => 'news-single-featured-img',
										'loading'  => 'eager',
										'decoding' => 'async',
										'itemprop' => 'image',
										'sizes'    => '(min-width: 1101px) min(800px, 100vw), 100vw',
									)
								);
								?>
							</div>
							<?php if ( '' !== trim( (string) $featured_caption ) ) : ?>
								<figcaption class="news-single-featured-caption"><?php echo wp_kses_post( $featured_caption ); ?></figcaption>
							<?php endif; ?>
						</figure>
					<?php endif; ?>

					<div class="news-single-body">
						<?php if ( ! empty( $toc_items ) ) : ?>
							<nav class="news-single-toc" aria-label="<?php esc_attr_e( 'Содержание статьи', 'chrysotile-child' ); ?>">
								<p class="news-single-toc-title"><?php esc_html_e( 'Содержание', 'chrysotile-child' ); ?></p>
								<ol class="news-single-toc-list">
									<?php foreach ( $toc_items as $item ) : ?>
										<li>
											<a href="#<?php echo esc_attr( $item['id'] ); ?>"><?php echo esc_html( $item['text'] ); ?></a>
										</li>
									<?php endforeach; ?>
								</ol>
							</nav>
						<?php endif; ?>

						<div class="news-single-content entry-content" itemprop="articleBody">
							<?php echo $article_body; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php
							wp_link_pages(
								array(
									'before' => '<div class="news-single-page-links"><span class="news-single-page-links-label">' . esc_html__( 'Страницы:', 'chrysotile-child' ) . '</span>',
									'after'  => '</div>',
								)
							);
							?>
						</div>

						<?php
						$single_tag_links = array();
						if ( is_array( $post_tags ) ) {
							foreach ( $post_tags as $tag ) {
								if ( ! $tag instanceof WP_Term ) {
									continue;
								}
								$tag_url           = apply_filters( 'chrysotile_single_tag_link', get_tag_link( $tag ), $tag, $post_id );
								$single_tag_links[] = '<a href="' . esc_url( $tag_url ) . '">' . esc_html( $tag->name ) . '</a>';
							}
						}
						if ( ! empty( $single_tag_links ) ) :
							?>
							<div class="news-single-tags news-single-tags--pre-share">
								<span class="news-single-tags-label"><?php esc_html_e( '', 'chrysotile-child' ); ?></span>
								<span class="news-single-tag-list">
									<?php echo implode( '<span class="news-single-tag-sep">, </span>', $single_tag_links ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</span>
							</div>
						<?php endif; ?>

						<div class="news-single-share news-single-share--bottom" aria-label="<?php esc_attr_e( 'Поделиться записью', 'chrysotile-child' ); ?>">
							<span class="news-single-share-label"><?php esc_html_e( 'Поделиться записью', 'chrysotile-child' ); ?></span>
							<ul class="news-single-share-list">
								<li>
									<a class="news-single-share-btn news-single-share-tg" href="<?php echo esc_url( $share_telegram ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Telegram', 'chrysotile-child' ); ?>">
										<img class="news-single-share-img" src="<?php echo esc_url( $share_post_icons_uri . 'post_tg.svg' ); ?>" alt="" width="48" height="48" decoding="async" />
									</a>
								</li>
								<li>
									<a class="news-single-share-btn news-single-share-x" href="<?php echo esc_url( $share_x ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'X (Twitter)', 'chrysotile-child' ); ?>">
										<img class="news-single-share-img" src="<?php echo esc_url( $share_post_icons_uri . 'post_x.svg' ); ?>" alt="" width="48" height="48" decoding="async" />
									</a>
								</li>
								<li>
									<a class="news-single-share-btn news-single-share-fb" href="<?php echo esc_url( $share_facebook ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Facebook', 'chrysotile-child' ); ?>">
										<img class="news-single-share-img" src="<?php echo esc_url( $share_post_icons_uri . 'post_fb.svg' ); ?>" alt="" width="48" height="48" decoding="async" />
									</a>
								</li>
								<li>
									<a class="news-single-share-btn news-single-share-wa" href="<?php echo esc_url( $share_whatsapp ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'WhatsApp', 'chrysotile-child' ); ?>">
										<img class="news-single-share-img" src="<?php echo esc_url( $share_post_icons_uri . 'post_wa.svg' ); ?>" alt="" width="48" height="48" decoding="async" />
									</a>
								</li>
								<li>
									<a class="news-single-share-btn news-single-share-threads" href="<?php echo esc_url( $share_threads ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Threads', 'chrysotile-child' ); ?>">
										<img class="news-single-share-img" src="<?php echo esc_url( $share_post_icons_uri . 'post_thr.svg' ); ?>" alt="" width="48" height="48" decoding="async" />
									</a>
								</li>
								<li>
									<button type="button" class="news-single-share-btn news-single-share-copy" data-url="<?php echo esc_attr( $permalink ); ?>" aria-label="<?php esc_attr_e( 'Копировать ссылку', 'chrysotile-child' ); ?>">
										<img class="news-single-share-img" src="<?php echo esc_url( $share_post_icons_uri . 'post_link.svg' ); ?>" alt="" width="48" height="48" decoding="async" />
									</button>
								</li>
							</ul>
						</div>
						<p class="news-single-copy-feedback" aria-live="polite" role="status"></p>

						<footer class="news-single-footer">
							<?php if ( 3 !== $author_id && get_the_author_meta( 'description' ) ) : ?>
								<div class="news-single-footer-meta">
									<p class="news-single-author-bio"><?php echo wp_kses_post( wpautop( get_the_author_meta( 'description' ) ) ); ?></p>
								</div>
							<?php endif; ?>
						</footer>
					</div>

					<?php if ( comments_open() || get_comments_number() ) : ?>
						<section class="news-single-comments">
							<?php comments_template(); ?>
						</section>
					<?php endif; ?>

					<?php if ( $related_q instanceof WP_Query && $related_q->have_posts() ) : ?>
						<section class="news-single-related" aria-label="<?php esc_attr_e( 'Рекомендуем', 'chrysotile-child' ); ?>">
							<h2 class="news-single-related-title"><?php esc_html_e( 'Популярное', 'chrysotile-child' ); ?></h2>
							<ul class="news-single-related-list">
								<?php
								while ( $related_q->have_posts() ) :
									$related_q->the_post();
									?>
									<li class="news-single-related-item">
										<a href="<?php the_permalink(); ?>" class="news-single-related-link">
											<span class="news-single-related-thumb">
												<?php
												if ( has_post_thumbnail() ) {
													the_post_thumbnail(
														'chrysotile_related',
														array(
															'class'   => 'news-single-related-img',
															'loading' => 'lazy',
															'alt'     => '',
														)
													);
												} else {
													echo '<span class="news-single-related-thumb-placeholder" aria-hidden="true"></span>';
												}
												?>
											</span>
											<span class="news-single-related-text">
												<span class="news-single-related-item-title"><?php the_title(); ?></span>
												<time class="news-single-related-date news-single-related-date--below" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
											</span>
										</a>
									</li>
								<?php endwhile; ?>
							</ul>
							<?php wp_reset_postdata(); ?>
						</section>
					<?php endif; ?>
				</article>

				<aside class="news-single-sidebar" aria-label="<?php esc_attr_e( 'Последние публикации', 'chrysotile-child' ); ?>">
					<h2 class="news-single-sidebar-title"><?php esc_html_e( 'Последние новости', 'chrysotile-child' ); ?></h2>
					<?php if ( $sidebar_q->have_posts() ) : ?>
						<ul class="news-single-sidebar-list">
							<?php
							while ( $sidebar_q->have_posts() ) :
								$sidebar_q->the_post();
								?>
								<li class="news-single-sidebar-item">
									<a href="<?php the_permalink(); ?>" class="news-single-sidebar-link">
										<span class="news-single-sidebar-thumb">
											<?php
											if ( has_post_thumbnail() ) {
												the_post_thumbnail(
													'thumbnail',
													array(
														'class'   => 'news-single-sidebar-img',
														'loading' => 'lazy',
														'alt'     => '',
													)
												);
											} else {
												echo '<span class="news-single-sidebar-thumb-placeholder" aria-hidden="true"></span>';
											}
											?>
										</span>
										<span class="news-single-sidebar-text">
											<span class="news-single-sidebar-item-title"><?php the_title(); ?></span>
											<time class="news-single-sidebar-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
												<?php echo esc_html( get_the_date() ); ?>
											</time>
										</span>
									</a>
								</li>
							<?php endwhile; ?>
						</ul>
						<?php wp_reset_postdata(); ?>
					<?php else : ?>
						<p class="news-single-sidebar-empty"><?php esc_html_e( 'Нет других записей.', 'chrysotile-child' ); ?></p>
					<?php endif; ?>
				</aside>
			</div>
		</div>
	</main>

	<?php
endwhile;

get_footer();
