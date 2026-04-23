<?php
/**
 * Страница «не найдено» (404).
 *
 * @package Chrysotile_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$chrysotile_posts_page_id = (int) get_option( 'page_for_posts' );
$chrysotile_posts_url     = $chrysotile_posts_page_id ? get_permalink( $chrysotile_posts_page_id ) : '';
$chrysotile_home_url      = home_url( '/' );
$chrysotile_show_feed_btn  = $chrysotile_posts_url && untrailingslashit( $chrysotile_posts_url ) !== untrailingslashit( $chrysotile_home_url );
?>

<section class="chrysotile-wrap chrysotile-section chrysotile-error-page">
	<article class="chrysotile-error-card" aria-labelledby="chrysotile-error-404-title">
		<p class="chrysotile-error-code" aria-hidden="true">404</p>
		<h1 id="chrysotile-error-404-title" class="chrysotile-error-title">Страница не найдена</h1>
		<p class="chrysotile-error-lead">
			Такой адрес на сайте не существует или материал был перенесён. Проверьте ссылку или вернитесь на главную.
		</p>
		<div class="chrysotile-error-actions">
			<a class="chrysotile-error-btn chrysotile-error-btn--primary" href="<?php echo esc_url( $chrysotile_home_url ); ?>">На главную</a>
			<?php if ( $chrysotile_show_feed_btn ) : ?>
				<a class="chrysotile-error-btn chrysotile-error-btn--ghost" href="<?php echo esc_url( $chrysotile_posts_url ); ?>">Лента новостей</a>
			<?php endif; ?>
		</div>
		<div class="chrysotile-error-search-wrap">
			<?php get_search_form(); ?>
		</div>
	</article>
</section>

<?php
get_footer();
