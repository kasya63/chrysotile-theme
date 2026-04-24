<?php
/**
 * Chrysotile-like header.
 *
 * @package Chrysotile_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php
$radio_url                 = chrysotile_child_get_radio_url();
$chrysotile_header_infobar = chrysotile_child_get_header_infobar_data();
$chrysotile_assets_uri      = trailingslashit( get_stylesheet_directory_uri() ) . 'assets/';

$chrysotile_nav_v_strane = chrysotile_child_get_category_archive_url_for_slugs( array( 'gosudarstvo' ) );
$chrysotile_nav_v_region = chrysotile_child_get_category_archive_url_for_slugs( array( 'region' ) );
$chrysotile_nav_people   = chrysotile_child_get_category_archive_url_for_slugs( array( 'people' ) );
$chrysotile_nav_society  = chrysotile_child_get_category_archive_url_for_slugs( array( 'society' ) );
$chrysotile_nav_special  = chrysotile_child_get_category_archive_url_for_slugs( array( 'special_projects', 'special-projects' ) );
$chrysotile_nav_kontakty = chrysotile_child_get_page_url_by_slug( 'kontakty' );
//$chrysotile_nav_zhmp     = chrysotile_child_get_category_archive_url_for_slugs( array( 'zhmp', 'nashi-lyudi' ) );
$chrysotile_nav_fallback = home_url( '/' );
?>

<header class="chrysotile-header">
	<nav class="chrysotile-main-nav" aria-label="<?php esc_attr_e( 'Primary menu', 'chrysotile-child' ); ?>">
		<div class="chrysotile-wrap chrysotile-main-nav-inner">
			<button
				class="chrysotile-nav-burger"
				type="button"
				aria-controls="chrysotile-nav-drawer"
				aria-expanded="false"
			>
				<span class="chrysotile-nav-burger-lines" aria-hidden="true"></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Открыть меню', 'chrysotile-child' ); ?></span>
			</button>
			<a class="chrysotile-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Go to homepage', 'chrysotile-child' ); ?>">
				<img
					id="chrysotile-logo"
					src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/logo.svg' ); ?>"
					data-logo-light="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/logo.svg' ); ?>"
					data-logo-dark="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/logo_dark_mode.svg' ); ?>"
					alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
				>
			</a>

			<div id="chrysotile-nav-drawer" class="chrysotile-nav-drawer" aria-hidden="true">
				<div class="chrysotile-nav-drawer-scrim" aria-hidden="true"></div>
				<div class="chrysotile-nav-drawer-panel" role="dialog" aria-modal="true" aria-labelledby="chrysotile-nav-drawer-title">
					<div class="chrysotile-nav-drawer-header">
						<span id="chrysotile-nav-drawer-title" class="chrysotile-nav-drawer-title"><?php esc_html_e( 'Меню', 'chrysotile-child' ); ?></span>
						<button type="button" class="chrysotile-nav-drawer-close" aria-label="<?php echo esc_attr__( 'Закрыть меню', 'chrysotile-child' ); ?>">×</button>
					</div>
					<div class="chrysotile-nav-drawer-body">
						<form class="chrysotile-search-form chrysotile-search-form--drawer" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
							<label class="screen-reader-text" for="chrysotile-search-drawer"><?php esc_attr_e( 'Поиск:', 'chrysotile-child' ); ?></label>
							<input
								id="chrysotile-search-drawer"
								class="chrysotile-search-form--drawer-input"
								type="search"
								name="s"
								value="<?php echo esc_attr( get_search_query() ); ?>"
								placeholder="<?php esc_attr_e( 'Поиск по сайту…', 'chrysotile-child' ); ?>"
							>
							<button type="submit"><?php esc_html_e( 'Поиск', 'chrysotile-child' ); ?></button>
						</form>
						<div class="chrysotile-nav-drawer-tools">
							<a class="chrysotile-radio-btn chrysotile-radio-btn--icon-only" href="<?php echo esc_url( $radio_url ); ?>" aria-label="<?php esc_attr_e( 'Радио', 'chrysotile-child' ); ?>">
								<img src="<?php echo esc_url( $chrysotile_assets_uri . 'headphones-icon.svg' ); ?>" alt="" width="22" height="22" class="chrysotile-radio-btn-icon" decoding="async" />
							</a>
							<button class="chrysotile-theme-toggle chrysotile-theme-toggle--icon" type="button" data-theme-toggle aria-pressed="false" data-label-light="<?php echo esc_attr__( 'Включить светлую тему', 'chrysotile-child' ); ?>" data-label-dark="<?php echo esc_attr__( 'Включить тёмную тему', 'chrysotile-child' ); ?>" aria-label="<?php esc_attr_e( 'Включить тёмную тему', 'chrysotile-child' ); ?>">
								<img src="<?php echo esc_url( $chrysotile_assets_uri . 'theme-moon.svg' ); ?>" alt="" width="22" height="22" class="chrysotile-theme-icon chrysotile-theme-icon--moon" data-theme-icon-moon decoding="async" />
								<img src="<?php echo esc_url( $chrysotile_assets_uri . 'theme-sun.svg' ); ?>" alt="" width="22" height="22" class="chrysotile-theme-icon chrysotile-theme-icon--sun" data-theme-icon-sun hidden decoding="async" />
							</button>
						</div>
						<div class="chrysotile-nav-drawer-social" aria-label="<?php esc_attr_e( 'Мы в соцсетях', 'chrysotile-child' ); ?>">
							<?php foreach ( chrysotile_child_get_social_links() as $network => $url ) : ?>
								<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( ucfirst( (string) $network ) ); ?>">
									<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/social/' . $network . '.svg' ); ?>" alt="" width="36" height="36" loading="lazy" decoding="async" />
								</a>
							<?php endforeach; ?>
						</div>
						<?php chrysotile_child_render_header_infobar_html( $chrysotile_header_infobar, 'chrysotile-header-infobar--drawer' ); ?>
						<ul class="chrysotile-nav-drawer-list">
							<li>
								<a class="chrysotile-drawer-link" href="<?php echo esc_url( $chrysotile_nav_v_strane ? $chrysotile_nav_v_strane : $chrysotile_nav_fallback ); ?>"><?php esc_html_e( 'В стране', 'chrysotile-child' ); ?></a>
							</li>
							<li>
								<a class="chrysotile-drawer-link" href="<?php echo esc_url( $chrysotile_nav_v_region ? $chrysotile_nav_v_region : $chrysotile_nav_fallback ); ?>"><?php esc_html_e( 'В регионе', 'chrysotile-child' ); ?></a>
							</li>
							<li>
								<a class="chrysotile-drawer-link" href="<?php echo esc_url( $chrysotile_nav_people ? $chrysotile_nav_people : $chrysotile_nav_fallback ); ?>"><?php esc_html_e( 'Люди', 'chrysotile-child' ); ?></a>
							</li>
							<li>
								<a class="chrysotile-drawer-link" href="<?php echo esc_url( $chrysotile_nav_society ? $chrysotile_nav_society : $chrysotile_nav_fallback ); ?>"><?php esc_html_e( 'Общество', 'chrysotile-child' ); ?></a>
							</li>
							<li>
								<a class="chrysotile-drawer-link" href="<?php echo esc_url( $chrysotile_nav_special ? $chrysotile_nav_special : $chrysotile_nav_fallback ); ?>"><?php esc_html_e( 'Спецпроекты', 'chrysotile-child' ); ?></a>
							</li>
							<li>
								<a class="chrysotile-drawer-link" href="<?php echo esc_url( $chrysotile_nav_kontakty ? $chrysotile_nav_kontakty : $chrysotile_nav_fallback ); ?>"><?php esc_html_e( 'Контакты', 'chrysotile-child' ); ?></a>
							</li>
						</ul>
					</div>
				</div>
			</div>

			<div class="chrysotile-main-nav-desktop">
				<div class="chrysotile-main-nav-links">
					<a class="chrysotile-nav-link" href="<?php echo esc_url( $chrysotile_nav_v_strane ? $chrysotile_nav_v_strane : $chrysotile_nav_fallback ); ?>"><?php esc_html_e( 'В стране', 'chrysotile-child' ); ?></a>
					<a class="chrysotile-nav-link" href="<?php echo esc_url( $chrysotile_nav_v_region ? $chrysotile_nav_v_region : $chrysotile_nav_fallback ); ?>"><?php esc_html_e( 'В регионе', 'chrysotile-child' ); ?></a>
					<a class="chrysotile-nav-link" href="<?php echo esc_url( $chrysotile_nav_kontakty ? $chrysotile_nav_kontakty : $chrysotile_nav_fallback ); ?>"><?php esc_html_e( 'Контакты', 'chrysotile-child' ); ?></a>
					<div class="chrysotile-nav-more-wrap">
						<button
							type="button"
							class="chrysotile-nav-more"
							id="chrysotile-nav-more-btn"
							aria-expanded="false"
							aria-haspopup="true"
							aria-controls="chrysotile-nav-more-dropdown"
						><?php esc_html_e( 'Еще', 'chrysotile-child' ); ?></button>
						<div id="chrysotile-nav-more-dropdown" class="chrysotile-nav-more-dropdown" role="menu" aria-labelledby="chrysotile-nav-more-btn">
							<a role="menuitem" href="<?php echo esc_url( $chrysotile_nav_people ? $chrysotile_nav_people : $chrysotile_nav_fallback ); ?>"><?php esc_html_e( 'Люди', 'chrysotile-child' ); ?></a>
							<a role="menuitem" href="<?php echo esc_url( $chrysotile_nav_society ? $chrysotile_nav_society : $chrysotile_nav_fallback ); ?>"><?php esc_html_e( 'Общество', 'chrysotile-child' ); ?></a>
							<a role="menuitem" href="<?php echo esc_url( $chrysotile_nav_special ? $chrysotile_nav_special : $chrysotile_nav_fallback ); ?>"><?php esc_html_e( 'Спецпроекты', 'chrysotile-child' ); ?></a>
						</div>
					</div>
				</div>
				<div class="chrysotile-main-nav-right">
					<?php chrysotile_child_render_header_infobar_html( $chrysotile_header_infobar ); ?>
					<div class="chrysotile-header-social" aria-label="<?php esc_attr_e( 'Мы в соцсетях', 'chrysotile-child' ); ?>">
						<?php foreach ( chrysotile_child_get_social_links() as $network => $url ) : ?>
							<a class="chrysotile-header-social-link" href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( ucfirst( (string) $network ) ); ?>">
								<img class="chrysotile-header-social-icon" src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/social/' . $network . '.svg' ); ?>" alt="" width="22" height="22" loading="lazy" decoding="async" />
							</a>
						<?php endforeach; ?>
					</div>
					<a class="chrysotile-radio-btn chrysotile-radio-btn--icon-only" href="<?php echo esc_url( $radio_url ); ?>" aria-label="<?php esc_attr_e( 'Радио', 'chrysotile-child' ); ?>">
						<img src="<?php echo esc_url( $chrysotile_assets_uri . 'headphones-icon.svg' ); ?>" alt="" width="22" height="22" class="chrysotile-radio-btn-icon" decoding="async" />
					</a>
					<button class="chrysotile-theme-toggle chrysotile-theme-toggle--icon" type="button" data-theme-toggle aria-pressed="false" data-label-light="<?php echo esc_attr__( 'Включить светлую тему', 'chrysotile-child' ); ?>" data-label-dark="<?php echo esc_attr__( 'Включить тёмную тему', 'chrysotile-child' ); ?>" aria-label="<?php esc_attr_e( 'Включить тёмную тему', 'chrysotile-child' ); ?>">
						<img src="<?php echo esc_url( $chrysotile_assets_uri . 'theme-moon.svg' ); ?>" alt="" width="22" height="22" class="chrysotile-theme-icon chrysotile-theme-icon--moon" data-theme-icon-moon decoding="async" />
						<img src="<?php echo esc_url( $chrysotile_assets_uri . 'theme-sun.svg' ); ?>" alt="" width="22" height="22" class="chrysotile-theme-icon chrysotile-theme-icon--sun" data-theme-icon-sun hidden decoding="async" />
					</button>
					<div class="chrysotile-main-search chrysotile-main-search--toggle">
						<button type="button" class="chrysotile-search-toggle" aria-expanded="false" aria-controls="chrysotile-search-dropdown-panel" id="chrysotile-search-toggle-btn">
							<?php esc_html_e( 'Поиск', 'chrysotile-child' ); ?>
						</button>
						<div class="chrysotile-search-dropdown" id="chrysotile-search-dropdown-panel" role="region" aria-label="<?php esc_attr_e( 'Поиск по сайту', 'chrysotile-child' ); ?>" hidden>
							<form class="chrysotile-search-form chrysotile-search-form--dropdown" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
								<label class="screen-reader-text" for="chrysotile-search-desktop"><?php esc_html_e( 'Поиск:', 'chrysotile-child' ); ?></label>
								<input id="chrysotile-search-desktop" class="chrysotile-search-form--dropdown-input" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Поиск по сайту…', 'chrysotile-child' ); ?>" autocomplete="off" />
								<button type="submit" class="chrysotile-search-form--dropdown-submit"><?php esc_html_e( 'Поиск', 'chrysotile-child' ); ?></button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</nav>
</header>

<main class="chrysotile-wrap">
