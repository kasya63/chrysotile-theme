<?php
/**
 * Chrysotile-style footer adapted for Chrysotile.
 *
 * @package Chrysotile_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$footer_columns = chrysotile_child_footer_category_columns();
$footer_col_count = max( 1, min( 3, count( array_filter( $footer_columns ) ) ) );
$radio_url        = chrysotile_child_get_radio_url();
$rules_material_url = function_exists( 'chrysotile_child_get_rules_material_url' ) ? chrysotile_child_get_rules_material_url() : home_url( '/' );
?>
</main>

<footer class="chrysotile-footer" role="contentinfo">
	<section class="chrysotile-footer-mega" aria-label="<?php esc_attr_e( 'Разделы сайта', 'chrysotile-child' ); ?>">
		<div class="chrysotile-wrap">
			<div class="chrysotile-footer-search-wrap">
				<form class="chrysotile-footer-search-form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<label class="screen-reader-text" for="chrysotile-footer-search"><?php esc_html_e( 'Поиск по сайту', 'chrysotile-child' ); ?></label>
					<input id="chrysotile-footer-search" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Поиск по сайту…', 'chrysotile-child' ); ?>">
					<button type="submit" class="chrysotile-footer-search-submit" aria-label="<?php esc_attr_e( 'Поиск', 'chrysotile-child' ); ?>">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
					</button>
				</form>
			</div>

			<?php if ( ! empty( array_filter( $footer_columns ) ) ) : ?>
				<div class="chrysotile-footer-columns chrysotile-footer-columns--cols-<?php echo (int) $footer_col_count; ?>">
					<?php foreach ( $footer_columns as $column ) : ?>
						<?php if ( empty( $column ) ) : ?>
							<?php continue; ?>
						<?php endif; ?>
						<div class="chrysotile-footer-col">
							<?php foreach ( $column as $term ) : ?>
								<div class="chrysotile-footer-col-group">
									<a class="chrysotile-footer-rubric" href="<?php echo esc_url( get_category_link( $term->term_id ) ); ?>"><?php echo esc_html( chrysotile_child_footer_category_label( $term ) ); ?></a>
									<ul class="chrysotile-footer-posts">
										<?php
										$footer_posts = chrysotile_child_get_footer_category_posts( $term->term_id, 4 );
										foreach ( $footer_posts as $footer_post ) :
											?>
											<li>
												<a href="<?php echo esc_url( get_permalink( $footer_post ) ); ?>"><?php echo esc_html( get_the_title( $footer_post ) ); ?></a>
											</li>
										<?php endforeach; ?>
									</ul>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<div class="chrysotile-footer-divider" aria-hidden="true"></div>

	<div class="chrysotile-footer-bar">
		<div class="chrysotile-wrap chrysotile-footer-bar-inner">
			<div class="chrysotile-footer-bar-left">
				<a class="chrysotile-footer-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/logo_dark_mode.svg' ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" loading="lazy">
				</a>
				<div class="chrysotile-footer-contacts">
					<strong class="chrysotile-footer-contacts-title"><?php esc_html_e( 'Контакты', 'chrysotile-child' ); ?></strong>
					<span class="chrysotile-footer-contact-k"><?php esc_html_e( 'Телефон', 'chrysotile-child' ); ?></span>
					<span class="chrysotile-footer-contact-v"><a href="tel:+77143522599">+7 7143 52-25-99</a></span>
					<span class="chrysotile-footer-contact-k"><?php esc_html_e( 'Почта', 'chrysotile-child' ); ?></span>
					<span class="chrysotile-footer-contact-v"><a href="mailto:editor@km.kz">editor@km.kz</a></span>
					<span class="chrysotile-footer-contact-k"><?php esc_html_e( 'Адрес', 'chrysotile-child' ); ?></span>
					<span class="chrysotile-footer-contact-v"><?php esc_html_e( 'г. Житикара, Управление АО КМ, 3 этаж, 35 кабинет', 'chrysotile-child' ); ?></span>
				</div>
				<div class="chrysotile-footer-social-right">
					<span class="chrysotile-footer-follow-label"><?php esc_html_e( 'Мы в соцсетях', 'chrysotile-child' ); ?></span>
					<div class="chrysotile-footer-social chrysotile-footer-social-inline">
						<?php foreach ( chrysotile_child_get_social_links() as $network => $url ) : ?>
							<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( ucfirst( $network ) ); ?>">
								<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/social/' . $network . '.svg' ); ?>" alt="" width="22" height="22" loading="lazy">
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
			<div class="chrysotile-footer-bar-util">
				<a href="<?php echo esc_url( $radio_url ); ?>"><?php esc_html_e( 'Радио', 'chrysotile-child' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Главная', 'chrysotile-child' ); ?></a>
			</div>
		</div>
	</div>

	<div class="chrysotile-footer-copy">
		<div class="chrysotile-wrap">
			<small class="chrysotile-footer-copy-inner">
				<span class="chrysotile-footer-copy-text">
					<?php
					printf(
						/* translators: 1: year 2: site name */
						esc_html__( '© %1$s %2$s. Все права защищены.', 'chrysotile-child' ),
						esc_html( wp_date( 'Y' ) ),
						esc_html( get_bloginfo( 'name' ) )
					);
					?>
				</span>
				<a class="chrysotile-footer-rules-link" href="<?php echo esc_url( $rules_material_url ); ?>"><?php esc_html_e( 'Правила использования материалов', 'chrysotile-child' ); ?></a>
			</small>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
