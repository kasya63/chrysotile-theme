<?php
/**
 * Theme setup for Chrysotile Child.
 *
 * @package Chrysotile_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_enqueue_scripts', 'chrysotile_child_enqueue_assets', 20 );
/**
 * Enqueue parent + child styles.
 */
function chrysotile_child_enqueue_assets() {
	wp_enqueue_style(
		'generatepress-parent',
		get_template_directory_uri() . '/style.css',
		array(),
		wp_get_theme( 'generatepress' )->get( 'Version' )
	);

	wp_enqueue_style(
		'chrysotile-child-style',
		get_stylesheet_uri(),
		array( 'generatepress-parent' ),
		wp_get_theme()->get( 'Version' )
	);

	wp_enqueue_script(
		'chrysotile-child-theme-toggle',
		get_stylesheet_directory_uri() . '/assets/js/theme-toggle.js',
		array(),
		wp_get_theme()->get( 'Version' ),
		true
	);

	wp_enqueue_script(
		'chrysotile-child-mobile-nav',
		get_stylesheet_directory_uri() . '/assets/js/mobile-nav.js',
		array(),
		wp_get_theme()->get( 'Version' ),
		true
	);

	wp_enqueue_script(
		'chrysotile-child-header-search-toggle',
		get_stylesheet_directory_uri() . '/assets/js/header-search-toggle.js',
		array(),
		wp_get_theme()->get( 'Version' ),
		true
	);

	wp_enqueue_script(
		'chrysotile-child-nav-more-dropdown',
		get_stylesheet_directory_uri() . '/assets/js/nav-more-dropdown.js',
		array(),
		wp_get_theme()->get( 'Version' ),
		true
	);
}

/**
 * Permalink for the first existing category among the given slugs.
 *
 * @param string[] $slugs Category slugs in priority order.
 * @return string Archive URL or empty string.
 */
function chrysotile_child_get_category_archive_url_for_slugs( array $slugs ) {
	foreach ( $slugs as $slug ) {
		if ( ! is_string( $slug ) || '' === $slug ) {
			continue;
		}

		$term = get_term_by( 'slug', $slug, 'category' );
		if ( $term instanceof WP_Term ) {
			$link = get_category_link( $term->term_id );
			if ( $link && ! is_wp_error( $link ) ) {
				return $link;
			}
		}
	}

	return '';
}

/**
 * URL for the «Все новости» block (archive of all posts / rubric).
 *
 * @return string
 */
function chrysotile_child_get_all_news_url() {
	$url = chrysotile_child_get_category_archive_url_for_slugs(
		array( 'all_news', 'vse-novosti', 'all-news', 'news' )
	);
	if ( $url ) {
		return $url;
	}

	$page_for_posts = (int) get_option( 'page_for_posts' );
	if ( $page_for_posts > 0 ) {
		$posts_url = get_permalink( $page_for_posts );
		if ( $posts_url ) {
			return $posts_url;
		}
	}

	return home_url( '/' );
}

/**
 * Hide «Главное» rubric from drawer / overflow lists.
 *
 * @param WP_Term|object|null $term Category term.
 * @return bool
 */
function chrysotile_child_header_nav_is_glavnoe_item( $term ) {
	if ( ! is_object( $term ) || empty( $term->slug ) ) {
		return false;
	}

	if ( in_array( $term->slug, array( 'glavnoe', 'glavnye', 'glavnoe-segodnya' ), true ) ) {
		return true;
	}

	if ( ! empty( $term->name ) && function_exists( 'mb_strtolower' ) ) {
		if ( 'главное' === mb_strtolower( (string) $term->name, 'UTF-8' ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Published page URL by path slug (e.g. kontakty).
 *
 * @param string $slug Page slug.
 * @return string Permalink or empty string.
 */
function chrysotile_child_get_page_url_by_slug( $slug ) {
	if ( ! is_string( $slug ) || '' === $slug ) {
		return '';
	}

	$page = get_page_by_path( $slug );
	if ( $page ) {
		$url = get_permalink( $page );
		if ( $url && ! is_wp_error( $url ) ) {
			return $url;
		}
	}

	return '';
}

add_action( 'pre_get_posts', 'chrysotile_child_category_posts_per_page' );
/**
 * Set posts per page for category archives.
 *
 * @param WP_Query $query Main query.
 */
function chrysotile_child_category_posts_per_page( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_category() ) {
		return;
	}

	$term = get_queried_object();
	if ( $term instanceof WP_Term && 'category' === $term->taxonomy && chrysotile_child_category_is_parent_archive_hub( $term ) ) {
		return;
	}

	$query->set( 'posts_per_page', 18 );
}

/**
 * Child categories shown under a parent archive hub (excludes slider rubrics).
 *
 * @param WP_Term $parent Parent category.
 * @return WP_Term[]
 */
function chrysotile_child_category_get_visible_child_terms( WP_Term $parent ) {
	if ( ! $parent instanceof WP_Term || 'category' !== $parent->taxonomy ) {
		return array();
	}

	$terms = get_terms(
		array(
			'taxonomy'   => 'category',
			'parent'     => (int) $parent->term_id,
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC',
		)
	);

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return array();
	}

	$out = array();
	foreach ( $terms as $t ) {
		if ( ! $t instanceof WP_Term ) {
			continue;
		}
		if ( chrysotile_child_is_slider_category( $t ) ) {
			continue;
		}
		$out[] = $t;
	}

	return $out;
}

/**
 * Whether the category archive is a «hub»: has at least one visible child rubric.
 *
 * @param WP_Term $term Queried category.
 * @return bool
 */
function chrysotile_child_category_is_parent_archive_hub( WP_Term $term ) {
	return ! empty( chrysotile_child_category_get_visible_child_terms( $term ) );
}

/**
 * Skip loading posts on parent hub archives (content built from child queries in category.php).
 *
 * @param WP_Query $query Main query.
 */
function chrysotile_child_category_parent_landing_empty_main_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_category() ) {
		return;
	}

	$term = get_queried_object();
	if ( ! $term instanceof WP_Term || 'category' !== $term->taxonomy ) {
		return;
	}

	if ( ! chrysotile_child_category_is_parent_archive_hub( $term ) ) {
		return;
	}

	$query->set(
		'meta_query',
		array(
			array(
				'key'     => '_chrysotile_parent_hub_no_match',
				'compare' => 'EXISTS',
			),
		)
	);
	$query->set( 'posts_per_page', 1 );
	$query->set( 'no_found_rows', true );
}

add_action( 'pre_get_posts', 'chrysotile_child_category_parent_landing_empty_main_query', 11 );

add_filter( 'navigation_markup_template', 'chrysotile_child_pagination_markup', 20, 2 );
/**
 * Wrap posts pagination for styling (GeneratePress strips the default &lt;nav&gt;).
 *
 * @param string $template The navigation template.
 * @param string $class    CSS class on the navigation block.
 * @return string
 */
function chrysotile_child_pagination_markup( $template, $class ) {
	if ( empty( $class ) || false === strpos( $class, 'pagination' ) ) {
		return $template;
	}

	return '<nav class="chrysotile-pagination" role="navigation" aria-label="' . esc_attr__( 'Навигация по страницам', 'chrysotile-child' ) . '"><div class="nav-links">%3$s</div></nav>';
}

add_action( 'after_setup_theme', 'chrysotile_child_setup' );
/**
 * Register theme supports and menus.
 */
function chrysotile_child_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_image_size( 'chrysotile_related', 480, 270, true );

	register_nav_menus(
		array(
			'chrysotile-primary' => __( 'Chrysotile Primary Menu', 'chrysotile-child' ),
			'chrysotile-footer'  => __( 'Chrysotile Footer Menu', 'chrysotile-child' ),
		)
	);
}

add_action( 'widgets_init', 'chrysotile_child_widgets_init' );
/**
 * Register sidebar for right rail blocks.
 */
function chrysotile_child_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Chrysotile Sidebar', 'chrysotile-child' ),
			'id'            => 'chrysotile-sidebar',
			'description'   => __( 'Widgets for the right column in archive and single pages.', 'chrysotile-child' ),
			'before_widget' => '<section class="chrysotile-sidebar-box">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>',
		)
	);
}

/**
 * Helper: output categories as plain links.
 */
function chrysotile_child_primary_categories( $post_id = 0 ) {
	$categories = get_the_category( $post_id );

	if ( empty( $categories ) ) {
		return;
	}

	echo '<div class="chrysotile-meta">';
	foreach ( $categories as $index => $category ) {
		if ( $index > 0 ) {
			echo ' | ';
		}
		printf(
			'<a href="%1$s">%2$s</a>',
			esc_url( get_category_link( $category->term_id ) ),
			esc_html( $category->name )
		);
	}
	echo '</div>';
}

/**
 * Whether a category is the "Слайдер" rubric (exclude from Еще menu).
 *
 * @param WP_Term $category Category term object.
 * @return bool
 */
function chrysotile_child_is_slider_category( $category ) {
	if ( ! $category || ! isset( $category->slug, $category->name ) ) {
		return false;
	}

	$slug = strtolower( (string) $category->slug );
	$name = function_exists( 'mb_strtolower' )
		? mb_strtolower( (string) $category->name, 'UTF-8' )
		: strtolower( (string) $category->name );

	if ( in_array( $slug, array( 'slider', 'slajder', 'slayder' ), true ) ) {
		return true;
	}

	if ( function_exists( 'mb_strpos' ) ) {
		return false !== mb_strpos( $name, 'слайдер', 0, 'UTF-8' );
	}

	return false !== strpos( $name, 'слайдер' );
}

/**
 * Whether a category is «Видео» (exclude from footer rubrics).
 *
 * @param WP_Term $category Category term.
 * @return bool
 */
function chrysotile_child_is_video_category( $category ) {
	if ( ! $category || ! isset( $category->slug, $category->name ) ) {
		return false;
	}

	$slug = strtolower( (string) $category->slug );
	$name = function_exists( 'mb_strtolower' )
		? mb_strtolower( (string) $category->name, 'UTF-8' )
		: strtolower( (string) $category->name );

	if ( in_array( $slug, array( 'video', 'videos', 'video-2' ), true ) ) {
		return true;
	}

	if ( function_exists( 'mb_strpos' ) ) {
		return false !== mb_strpos( $name, 'видео', 0, 'UTF-8' );
	}

	return false !== strpos( $name, 'видео' );
}

/**
 * Whether a category is «Экономика» (placed in footer column 2).
 *
 * @param WP_Term $category Category term.
 * @return bool
 */
function chrysotile_child_is_economy_category( $category ) {
	if ( ! $category || ! isset( $category->slug, $category->name ) ) {
		return false;
	}

	$slug = strtolower( (string) $category->slug );
	$name = function_exists( 'mb_strtolower' )
		? mb_strtolower( (string) $category->name, 'UTF-8' )
		: strtolower( (string) $category->name );

	if ( in_array( $slug, array( 'ekonomika', 'economics', 'economy', 'economic' ), true ) ) {
		return true;
	}

	if ( function_exists( 'mb_strpos' ) ) {
		return false !== mb_strpos( $name, 'экономик', 0, 'UTF-8' );
	}

	return false !== strpos( $name, 'экономик' );
}

/**
 * Whether a page is the Slider page (exclude from Еще menu).
 *
 * @param WP_Post $page Page object.
 * @return bool
 */
function chrysotile_child_is_slider_page( $page ) {
	if ( ! $page || empty( $page->post_title ) ) {
		return false;
	}

	$title = function_exists( 'mb_strtolower' )
		? mb_strtolower( (string) $page->post_title, 'UTF-8' )
		: strtolower( (string) $page->post_title );
	$slug  = strtolower( (string) $page->post_name );

	if ( in_array( $slug, array( 'slider', 'slajder', 'slayder' ), true ) ) {
		return true;
	}

	if ( function_exists( 'mb_strpos' ) ) {
		return false !== mb_strpos( $title, 'слайдер', 0, 'UTF-8' );
	}

	return false !== strpos( $title, 'слайдер' );
}

/**
 * Return radio page URL from template or fallback.
 */
function chrysotile_child_get_radio_url() {
	$radio_pages = get_posts(
		array(
			'post_type'      => 'page',
			'posts_per_page' => 1,
			'meta_key'       => '_wp_page_template',
			'meta_value'     => 'page-radio.php',
		)
	);

	if ( ! empty( $radio_pages ) ) {
		return get_permalink( $radio_pages[0]->ID );
	}

	return home_url( '/radio/' );
}

/**
 * Return social links map.
 */
function chrysotile_child_get_social_links() {
	return array(
		'telegram'  => 'https://t.me/gazetakm',
		'instagram' => 'https://www.instagram.com/gazetakm/',
		'youtube'   => 'https://www.youtube.com/channel/UCAkrsp_48W7tEdthJgzu9aA',
		'ok'        => 'https://ok.ru/profile/572667802596',
	);
}

/**
 * Echo weather + FX row (desktop header or mobile drawer).
 *
 * @param array  $infobar_data   From chrysotile_child_get_header_infobar_data().
 * @param string $extra_classes  Optional CSS classes on wrapper.
 */
function chrysotile_child_render_header_infobar_html( $infobar_data, $extra_classes = '' ) {
	if ( ! is_array( $infobar_data ) ) {
		return;
	}

	$has_weather = isset( $infobar_data['weather']['temp'] ) && null !== $infobar_data['weather']['temp'];
	$has_fx      = false;
	foreach ( array( 'USD', 'EUR', 'RUB' ) as $ccy ) {
		if ( isset( $infobar_data['fx'][ $ccy ]['value'] ) && is_numeric( $infobar_data['fx'][ $ccy ]['value'] ) ) {
			$has_fx = true;
			break;
		}
	}

	if ( ! $has_weather && ! $has_fx ) {
		return;
	}

	$classes = trim( 'chrysotile-header-infobar ' . $extra_classes );
	?>
	<div class="<?php echo esc_attr( $classes ); ?>" role="region" aria-label="<?php esc_attr_e( 'Погода и курсы валют', 'chrysotile-child' ); ?>">
		<?php if ( $has_weather ) : ?>
		<div class="chrysotile-infobar-weather">
			<span class="chrysotile-infobar-weather-icon chrysotile-infobar-weather-icon--<?php echo esc_attr( $infobar_data['weather']['icon'] ); ?>" aria-hidden="true"></span>
			<?php if ( ! empty( $infobar_data['weather']['city'] ) ) : ?>
				<span class="chrysotile-infobar-city"><?php echo esc_html( $infobar_data['weather']['city'] ); ?></span>
			<?php endif; ?>
			<span class="chrysotile-infobar-temp"><?php echo esc_html( $infobar_data['weather']['temp_label'] ); ?></span>
		</div>
		<?php endif; ?>
		<?php if ( $has_fx ) : ?>
		<div class="chrysotile-infobar-fx">
			<?php
			$fx_symbols = array(
				'USD' => '$',
				'EUR' => '€',
				'RUB' => '₽',
			);
			foreach ( $fx_symbols as $ch_code => $ch_sym ) :
				if ( ! isset( $infobar_data['fx'][ $ch_code ]['value'] ) || null === $infobar_data['fx'][ $ch_code ]['value'] ) {
					continue;
				}
				$ch_val   = (float) $infobar_data['fx'][ $ch_code ]['value'];
				$ch_trend = isset( $infobar_data['fx'][ $ch_code ]['trend'] ) ? (string) $infobar_data['fx'][ $ch_code ]['trend'] : 'same';
				?>
			<span class="chrysotile-infobar-pair">
				<span class="chrysotile-infobar-sym" aria-hidden="true"><?php echo esc_html( $ch_sym ); ?></span>
				<span class="chrysotile-infobar-val"><?php echo esc_html( number_format_i18n( $ch_val, 2 ) ); ?></span>
				<?php if ( 'down' === $ch_trend ) : ?>
					<span class="chrysotile-infobar-trend chrysotile-infobar-trend--down" aria-hidden="true"></span>
				<?php elseif ( 'up' === $ch_trend ) : ?>
					<span class="chrysotile-infobar-trend chrysotile-infobar-trend--up" aria-hidden="true"></span>
				<?php endif; ?>
			</span>
				<?php
			endforeach;
			?>
		</div>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Weather + FX for header infobar (cached). No API keys required.
 *
 * @return array{weather: array, fx: array<string, array{value: float, trend: string}>, ok: bool}
 */
function chrysotile_child_get_header_infobar_data() {
	$key    = 'chrysotile_infobar_v5';
	$cached = get_transient( $key );
	if ( false !== $cached && is_array( $cached ) ) {
		return $cached;
	}

	$fresh = chrysotile_child_refresh_header_infobar_data();
	$ttl   = ! empty( $fresh['ok'] ) ? ( 30 * MINUTE_IN_SECONDS ) : ( 10 * MINUTE_IN_SECONDS );
	set_transient( $key, $fresh, $ttl );

	return $fresh;
}

/**
 * @return array
 */
function chrysotile_child_refresh_header_infobar_data() {
	$prev_saved = get_option( 'chrysotile_infobar_prev_fx', array() );
	if ( ! is_array( $prev_saved ) ) {
		$prev_saved = array();
	}
	$out = array(
		'ok'      => false,
		'fetched' => time(),
		'weather' => array(
			'temp'       => null,
			'icon'       => 'cloud',
			'temp_label' => '',
			'city'       => '',
		),
		'fx'      => array(),
	);

	$location = apply_filters(
		'chrysotile_header_infobar_weather_location',
		array(
			'lat'  => 52.191,
			'lon'  => 61.203,
			'city' => __( 'Житикара', 'chrysotile-child' ),
		)
	);
	// Ранее использовался только lat/lon; оставляем имя для совместимости.
	$location = apply_filters( 'chrysotile_header_infobar_weather_coords', $location );
	if ( ! is_array( $location ) ) {
		$location = array();
	}
	if ( empty( $location['city'] ) ) {
		$location['city'] = __( 'Житикара', 'chrysotile-child' );
	}
	$lat         = isset( $location['lat'] ) ? (float) $location['lat'] : 52.191;
	$lon         = isset( $location['lon'] ) ? (float) $location['lon'] : 61.203;
	$city_label  = (string) $location['city'];

	$meteo_url = add_query_arg(
		array(
			'latitude'  => $lat,
			'longitude' => $lon,
			'current'   => 'temperature_2m,weather_code,is_day',
			'timezone'  => 'Asia/Qostanay',
		),
		'https://api.open-meteo.com/v1/forecast'
	);

	$meteo = wp_remote_get(
		$meteo_url,
		array(
			'timeout' => 6,
			'headers' => array( 'Accept' => 'application/json' ),
		)
	);

	if ( ! is_wp_error( $meteo ) && wp_remote_retrieve_response_code( $meteo ) === 200 ) {
		$mj = json_decode( wp_remote_retrieve_body( $meteo ), true );
		if ( is_array( $mj ) && isset( $mj['current']['temperature_2m'] ) ) {
			$t       = (float) $mj['current']['temperature_2m'];
			$code    = isset( $mj['current']['weather_code'] ) ? (int) $mj['current']['weather_code'] : 0;
			$is_day = isset( $mj['current']['is_day'] ) ? (bool) (int) $mj['current']['is_day'] : true;
			$rounded = (int) round( $t );
			$icon    = chrysotile_child_infobar_weather_icon_slug( $code, $is_day );

			$out['weather'] = array(
				'temp'       => $rounded,
				'icon'       => $icon,
				'temp_label' => ( $rounded > 0 ? '+' : '' ) . (string) $rounded . '°',
				'city'       => $city_label,
			);
			$out['ok'] = true;
		}
	}

	$currencies = array( 'USD', 'EUR', 'RUB' );
	foreach ( $currencies as $base ) {
		$val = chrysotile_child_fetch_kzt_per_unit( $base );

		$trend = 'same';
		if ( null !== $val && isset( $prev_saved[ $base ] ) ) {
			$prev = (float) $prev_saved[ $base ];
			if ( $prev > 0 ) {
				if ( abs( $val - $prev ) < 0.005 ) {
					$trend = 'same';
				} elseif ( $val < $prev ) {
					$trend = 'down';
				} else {
					$trend = 'up';
				}
			}
		}

		$out['fx'][ $base ] = array(
			'value' => $val,
			'trend' => $trend,
		);
		if ( null !== $val ) {
			$out['ok'] = true;
		}
	}

	$snap = array();
	foreach ( $out['fx'] as $k => $row ) {
		if ( isset( $row['value'] ) && null !== $row['value'] ) {
			$snap[ $k ] = $row['value'];
		}
	}
	if ( ! empty( $snap ) ) {
		update_option( 'chrysotile_infobar_prev_fx', $snap, false );
	}

	return $out;
}

/**
 * @param int  $wmo_code WMO weather code.
 * @param bool $is_day   Day flag from API.
 * @return string slug: moon|sun|cloud|rain|snow|fog
 */
function chrysotile_child_infobar_weather_icon_slug( $wmo_code, $is_day ) {
	if ( in_array( $wmo_code, array( 0, 1 ), true ) ) {
		return $is_day ? 'sun' : 'moon';
	}
	if ( 2 === $wmo_code ) {
		return $is_day ? 'cloud-sun' : 'moon';
	}
	if ( 3 === $wmo_code ) {
		return 'cloud';
	}
	if ( in_array( $wmo_code, array( 45, 48 ), true ) ) {
		return 'fog';
	}
	if ( $wmo_code >= 71 && $wmo_code <= 77 ) {
		return 'snow';
	}
	if ( ( $wmo_code >= 51 && $wmo_code <= 67 ) || ( $wmo_code >= 80 && $wmo_code <= 82 ) || $wmo_code >= 95 ) {
		return 'rain';
	}
	return 'cloud';
}

/**
 * KZT per 1 unit of base currency (USD / EUR / RUB). Primary: open.er-api; fallback: exchangerate.host.
 *
 * @param string $base Currency code.
 * @return float|null
 */
function chrysotile_child_fetch_kzt_per_unit( $base ) {
	$base = strtoupper( (string) $base );
	$url  = 'https://open.er-api.com/v6/latest/' . rawurlencode( $base );
	$resp = wp_remote_get(
		$url,
		array(
			'timeout' => 8,
			'headers' => array( 'Accept' => 'application/json' ),
		)
	);
	$val = null;
	if ( ! is_wp_error( $resp ) && wp_remote_retrieve_response_code( $resp ) === 200 ) {
		$fj = json_decode( wp_remote_retrieve_body( $resp ), true );
		if ( is_array( $fj ) && ( ! isset( $fj['result'] ) || 'success' === $fj['result'] ) ) {
			$rates = array();
			if ( isset( $fj['conversion_rates'] ) && is_array( $fj['conversion_rates'] ) ) {
				$rates = $fj['conversion_rates'];
			} elseif ( isset( $fj['rates'] ) && is_array( $fj['rates'] ) ) {
				$rates = $fj['rates'];
			}
			if ( isset( $rates['KZT'] ) ) {
				$val = (float) $rates['KZT'];
			}
		}
	}
	if ( null !== $val ) {
		return $val;
	}

	$host_url = add_query_arg(
		array(
			'base'    => $base,
			'symbols' => 'KZT',
		),
		'https://api.exchangerate.host/latest'
	);
	$r2 = wp_remote_get(
		$host_url,
		array(
			'timeout' => 8,
			'headers' => array( 'Accept' => 'application/json' ),
		)
	);
	if ( ! is_wp_error( $r2 ) && wp_remote_retrieve_response_code( $r2 ) === 200 ) {
		$j2 = json_decode( wp_remote_retrieve_body( $r2 ), true );
		if ( is_array( $j2 ) && isset( $j2['rates']['KZT'] ) ) {
			return (float) $j2['rates']['KZT'];
		}
	}

	return null;
}

/**
 * Footer rubrics only: В регионе, В стране, Люди, Общество, Спец проекты (fixed slugs).
 *
 * @return WP_Term[]
 */
function chrysotile_child_get_footer_categories() {
	$slug_order = array( 'region', 'gosudarstvo', 'people', 'society' );
	$out        = array();

	foreach ( $slug_order as $slug ) {
		$term = get_term_by( 'slug', $slug, 'category' );
		if ( $term instanceof WP_Term ) {
			$out[] = $term;
		}
	}

	foreach ( array( 'special_projects', 'special-projects' ) as $slug ) {
		$term = get_term_by( 'slug', $slug, 'category' );
		if ( $term instanceof WP_Term ) {
			$out[] = $term;
			break;
		}
	}

	return $out;
}

/**
 * Label for category in nav/footer (ЖМП shortcut).
 *
 * @param WP_Term $term Category term.
 * @return string
 */
function chrysotile_child_category_nav_label( $term ) {
	if ( 'zhmp' === $term->slug || 'Журналист меняет профессию' === $term->name ) {
		return 'ЖМП';
	}

	return $term->name;
}

/**
 * Short heading for footer mega-links (fixed copy for main rubrics).
 *
 * @param WP_Term $term Category term.
 * @return string
 */
function chrysotile_child_footer_category_label( $term ) {
	if ( ! $term instanceof WP_Term ) {
		return '';
	}

	$labels = array(
		'region'            => __( 'В регионе', 'chrysotile-child' ),
		'gosudarstvo'       => __( 'В стране', 'chrysotile-child' ),
		'people'            => __( 'Люди', 'chrysotile-child' ),
		'society'           => __( 'Общество', 'chrysotile-child' ),
		'special_projects'  => __( 'Спец проекты', 'chrysotile-child' ),
		'special-projects'  => __( 'Спец проекты', 'chrysotile-child' ),
	);

	if ( isset( $labels[ $term->slug ] ) ) {
		return $labels[ $term->slug ];
	}

	return chrysotile_child_category_nav_label( $term );
}

/**
 * Split footer rubrics into columns of two (desktop: 2 + 2 + 1 for five rubrics).
 *
 * @return array<int, WP_Term[]>
 */
function chrysotile_child_footer_category_columns() {
	$cats = chrysotile_child_get_footer_categories();
	if ( empty( $cats ) ) {
		return array();
	}

	$columns = array();
	for ( $i = 0, $n = count( $cats ); $i < $n; $i += 2 ) {
		$columns[] = array_slice( $cats, $i, 2 );
	}

	return $columns;
}

/**
 * Recent posts for footer under a rubric.
 *
 * @param int $category_id Category term ID.
 * @param int $limit       Max posts.
 * @return WP_Post[]
 */
function chrysotile_child_get_footer_category_posts( $category_id, $limit = 4 ) {
	return get_posts(
		array(
			'cat'                 => (int) $category_id,
			'posts_per_page'      => (int) $limit,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
}

/**
 * URL for «Правила использования материалов» page (slug rulse_use_material).
 *
 * @return string
 */
function chrysotile_child_get_rules_material_url() {
	$page = get_page_by_path( 'rulse_use_material' );
	if ( $page instanceof WP_Post && 'publish' === $page->post_status ) {
		return get_permalink( $page );
	}

	return home_url( '/rulse_use_material/' );
}

/**
 * Remove fixed thumbnail dimensions on single posts.
 *
 * @param string       $html              The post thumbnail HTML.
 * @param int          $post_id           Post ID.
 * @param int          $post_thumbnail_id Thumbnail attachment ID.
 * @param string|array $size              Requested size.
 * @param array        $attr              Image attributes.
 * @return string
 */
function chrysotile_child_single_thumbnail_html( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
	if ( ! is_singular( 'post' ) || is_admin() || wp_doing_ajax() ) {
		return $html;
	}

	return (string) preg_replace( '/\s(width|height)="[^"]*"/i', '', $html );
}
add_filter( 'post_thumbnail_html', 'chrysotile_child_single_thumbnail_html', 20, 5 );

/**
 * Force featured image sizes for single post content width.
 *
 * @param array   $attr       Attachment attributes.
 * @param WP_Post $attachment Attachment object.
 * @param string  $size       Requested size.
 * @return array
 */
function chrysotile_child_single_image_attributes( $attr, $attachment, $size ) {
	if ( ! is_singular( 'post' ) || is_admin() ) {
		return $attr;
	}

	$thumbnail_id = (int) get_post_thumbnail_id();
	if ( ! $attachment instanceof WP_Post || (int) $attachment->ID !== $thumbnail_id ) {
		return $attr;
	}

	$attr['sizes'] = '(min-width: 1101px) min(800px, 100vw), 100vw';

	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'chrysotile_child_single_image_attributes', 10, 3 );

/**
 * Estimated reading time in minutes.
 *
 * @param int|null $post_id Post ID.
 * @return int
 */
function chrysotile_single_reading_time_minutes( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	if ( ! $post_id ) {
		return 1;
	}

	$plain = wp_strip_all_tags( (string) get_post_field( 'post_content', $post_id ) );
	$words = preg_split( '/\s+/u', $plain, -1, PREG_SPLIT_NO_EMPTY );
	$count = is_array( $words ) ? count( $words ) : 0;

	return max( 1, (int) ceil( $count / 200 ) );
}

/**
 * Add IDs to H2 headings and build a TOC.
 *
 * @param string $content Filtered content HTML.
 * @return array{content:string,toc:array}
 */
function chrysotile_single_content_with_toc( $content ) {
	$toc = array();
	$i   = 0;

	$content = preg_replace_callback(
		'/<h2([^>]*)>(.*?)<\/h2>/is',
		function ( $matches ) use ( &$toc, &$i ) {
			$i++;
			$attrs = $matches[1];
			$inner = $matches[2];
			$text  = trim( wp_strip_all_tags( $inner ) );

			if ( '' === $text ) {
				return '<h2' . $attrs . '>' . $inner . '</h2>';
			}

			if ( preg_match( '/\bid\s*=\s*["\']([^"\']+)["\']/', $attrs, $id_match ) ) {
				$id = $id_match[1];
			} else {
				$id    = 'toc-heading-' . $i;
				$attrs .= ' id="' . esc_attr( $id ) . '"';
			}

			$toc[] = array(
				'id'   => $id,
				'text' => $text,
			);

			return '<h2' . $attrs . '>' . $inner . '</h2>';
		},
		$content
	);

	return array(
		'content' => $content,
		'toc'     => $toc,
	);
}

/**
 * Stable numeric article identifier.
 *
 * @param int|null $post_id Post ID.
 * @return int
 */
function chrysotile_single_article_identifier( $post_id = null ) {
	$id = $post_id ? (int) $post_id : get_the_ID();

	return $id > 0 ? $id : 0;
}

/**
 * Get post views from post meta.
 *
 * @param int|null $post_id Post ID.
 * @return int
 */
function chrysotile_get_post_views( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	if ( ! $post_id ) {
		return 0;
	}

	return max( 0, (int) get_post_meta( $post_id, 'chrysotile_views', true ) );
}

add_action(
	'init',
	function () {
		register_post_meta(
			'post',
			'chrysotile_subtitle',
			array(
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => 'sanitize_text_field',
				'show_in_rest'      => true,
				'auth_callback'     => static function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
);

add_action(
	'template_redirect',
	function () {
		if ( is_admin() || wp_doing_ajax() || ! is_singular( 'post' ) || is_preview() ) {
			return;
		}

		$post_id = (int) get_queried_object_id();
		if ( $post_id <= 0 ) {
			return;
		}

		$cookie_name = 'chrysotile_pv_' . $post_id;
		if ( ! empty( $_COOKIE[ $cookie_name ] ) ) {
			return;
		}

		$views = chrysotile_get_post_views( $post_id );
		update_post_meta( $post_id, 'chrysotile_views', $views + 1 );

		$path = ( defined( 'COOKIEPATH' ) && COOKIEPATH ) ? COOKIEPATH : '/';
		if ( PHP_VERSION_ID >= 70300 ) {
			setcookie(
				$cookie_name,
				'1',
				array(
					'expires'  => time() + HOUR_IN_SECONDS,
					'path'     => $path,
					'domain'   => COOKIE_DOMAIN,
					'secure'   => is_ssl(),
					'httponly' => true,
					'samesite' => 'Lax',
				)
			);
		} else {
			setcookie( $cookie_name, '1', time() + HOUR_IN_SECONDS, $path, COOKIE_DOMAIN, is_ssl(), true );
		}
	},
	5
);

/**
 * Get single post subtitle from post meta.
 *
 * @param int|null $post_id Post ID.
 * @return string
 */
function chrysotile_get_article_subtitle( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	if ( ! $post_id ) {
		return '';
	}

	$subtitle = get_post_meta( $post_id, 'chrysotile_subtitle', true );

	return is_string( $subtitle ) ? trim( $subtitle ) : '';
}

/**
 * Get most viewed related posts from the last 30 days.
 *
 * @param int $exclude_post_id Excluded current post ID.
 * @param int $limit           Number of posts.
 * @return WP_Query|null
 */
function chrysotile_single_related_most_viewed( $exclude_post_id, $limit = 3 ) {
	$exclude_post_id = (int) $exclude_post_id;
	$limit           = max( 1, (int) $limit );
	$candidate_ids   = get_posts(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 150,
			'post__not_in'   => array( $exclude_post_id ),
			'fields'         => 'ids',
			'orderby'        => 'date',
			'order'          => 'DESC',
			'no_found_rows'  => true,
			'date_query'     => array(
				array(
					'column'    => 'post_date',
					'after'     => '30 days ago',
					'inclusive' => true,
				),
			),
		)
	);

	if ( empty( $candidate_ids ) ) {
		return null;
	}

	usort(
		$candidate_ids,
		static function ( $a, $b ) {
			return chrysotile_get_post_views( $b ) <=> chrysotile_get_post_views( $a );
		}
	);

	$top_ids = array_slice( $candidate_ids, 0, $limit );
	if ( empty( $top_ids ) ) {
		return null;
	}

	return new WP_Query(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'post__in'       => $top_ids,
			'orderby'        => 'post__in',
			'posts_per_page' => $limit,
			'no_found_rows'  => true,
		)
	);
}

/**
 * Social accounts used in the single post floating block.
 *
 * @return array<int,array<string,string>>
 */
function chrysotile_social_items() {
	return array(
		array(
			'url'   => 'https://t.me/+4wuuNdargMpjZTJi',
			'icon'  => 'assets/social/telegram.svg',
			'label' => 'Telegram',
		),
		array(
			'url'   => 'https://www.instagram.com/gazetakm/?hl=ru',
			'icon'  => 'assets/social/instagram.svg',
			'label' => 'Instagram',
		),
		array(
			'url'   => 'https://www.youtube.com/@%D0%93%D0%B0%D0%B7%D0%B5%D1%82%D0%B0%D0%A5%D1%80%D0%B8%D0%B7%D0%BE%D1%82%D0%B8%D0%BB%D0%9A%D0%9C',
			'icon'  => 'assets/social/youtube.svg',
			'label' => 'YouTube',
		),
		array(
			'url'   => 'https://ok.ru/profile/572667802596',
			'icon'  => 'assets/social/ok.svg',
			'label' => 'Ok',
		),
	);
}

add_action(
	'wp_footer',
	function () {
		if ( ! is_single() ) {
			return;
		}
		?>
		<script>
		(function () {
			document.querySelectorAll('.news-single-share-copy').forEach(function (btn) {
				btn.addEventListener('click', function () {
					var url = this.getAttribute('data-url') || '';
					var fb = document.querySelector('.news-single-copy-feedback');
					function done() {
						if (fb) {
							fb.textContent = <?php echo wp_json_encode( __( 'Скопировано', 'chrysotile-child' ) ); ?>;
						}
					}
					if (navigator.clipboard && navigator.clipboard.writeText) {
						navigator.clipboard.writeText(url).then(done).catch(function () {});
					} else {
						var ta = document.createElement('textarea');
						ta.value = url;
						document.body.appendChild(ta);
						ta.select();
						try {
							document.execCommand('copy');
							done();
						} finally {
							document.body.removeChild(ta);
						}
					}
				});
			});
		})();
		</script>
		<?php
	},
	99
);

/**
 * Remove website URL field from default comment form fields.
 *
 * @param array<string,string> $fields Default comment form fields.
 * @return array<string,string>
 */
function chrysotile_child_comment_default_fields_without_url( $fields ) {
	if ( isset( $fields['url'] ) ) {
		unset( $fields['url'] );
	}

	return $fields;
}
add_filter( 'comment_form_default_fields', 'chrysotile_child_comment_default_fields_without_url' );

/**
 * Strip URL field and localize cookies consent (no "website" wording).
 *
 * @param array<string,string> $fields Comment form fields including cookies when opt-in is enabled.
 * @return array<string,string>
 */
function chrysotile_child_comment_form_fields_without_url( $fields ) {
	if ( isset( $fields['url'] ) ) {
		unset( $fields['url'] );
	}

	if ( isset( $fields['cookies'] ) ) {
		$commenter    = wp_get_current_commenter();
		$html5        = current_theme_supports( 'html5', 'comment-form' );
		$checked_attr = ! empty( $commenter['comment_author_email'] )
			? ( $html5 ? ' checked' : ' checked="checked"' )
			: '';

		$fields['cookies'] = '<p class="comment-form-cookies-consent">'
			. '<input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"' . $checked_attr . ' /> '
			. '<label for="wp-comment-cookies-consent">' . esc_html__( 'Сохранить моё имя и email в этом браузере для следующих комментариев.', 'chrysotile-child' ) . '</label>'
			. '</p>';
	}

	return $fields;
}
add_filter( 'comment_form_fields', 'chrysotile_child_comment_form_fields_without_url' );

/*
|--------------------------------------------------------------------------
| Страница «Контакты» (page-kontakty.php) — обработка формы и карта
| (если не определены в другом дочернем шаблоне).
|--------------------------------------------------------------------------
*/

if ( ! function_exists( 'chrysotile_contact_redirect' ) ) {
	/**
	 * Редирект после отправки формы контактов.
	 *
	 * @param 'sent'|'error' $status Статус.
	 * @param string         $reason Короткая причина ошибки (для админ-диагностики).
	 */
	function chrysotile_contact_redirect( $status, $reason = '' ) {
		$url = wp_get_referer();
		if ( ! $url ) {
			$page = get_page_by_path( 'kontakty' );
			$url  = $page ? get_permalink( $page ) : home_url( '/' );
		}
		$args = array( 'contact' => $status );
		if ( '' !== $reason ) {
			$args['contact_reason'] = sanitize_key( $reason );
		}
		wp_safe_redirect( add_query_arg( $args, $url ) );
		exit;
	}
}

if ( ! function_exists( 'chrysotile_contact_set_debug_message' ) ) {
	/**
	 * Save short debug message for admin after redirect.
	 *
	 * @param string $message Debug message.
	 * @return void
	 */
	function chrysotile_contact_set_debug_message( $message ) {
		$message = trim( (string) $message );
		if ( '' === $message ) {
			return;
		}
		set_transient( 'chrysotile_contact_debug_message', $message, 5 * MINUTE_IN_SECONDS );
	}
}

if ( ! function_exists( 'chrysotile_contact_form_handler' ) ) {
	/**
	 * Validate phone: only phone-like chars and 10-15 digits.
	 *
	 * @param string $phone Raw phone.
	 * @return bool
	 */
	function chrysotile_contact_is_valid_phone( $phone ) {
		$phone = trim( (string) $phone );
		if ( '' === $phone ) {
			return false;
		}

		if ( 1 !== preg_match( '/^\+?[0-9\-\s\(\)]+$/', $phone ) ) {
			return false;
		}

		$digits = preg_replace( '/\D+/', '', $phone );
		$len    = strlen( (string) $digits );

		return $len >= 10 && $len <= 15;
	}

	/**
	 * Обработчик формы «Напишите нам» (отправка в Telegram).
	 */
	function chrysotile_contact_form_handler() {
		if ( ! isset( $_POST['chrysotile_contact_nonce'] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['chrysotile_contact_nonce'] ) ), 'chrysotile_contact' ) ) {
			chrysotile_contact_redirect( 'error', 'nonce' );
		}

		$name    = isset( $_POST['chrysotile_contact_name'] ) ? sanitize_text_field( wp_unslash( $_POST['chrysotile_contact_name'] ) ) : '';
		$phone   = isset( $_POST['chrysotile_contact_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['chrysotile_contact_phone'] ) ) : '';
		$email   = isset( $_POST['chrysotile_contact_email'] ) ? sanitize_email( wp_unslash( $_POST['chrysotile_contact_email'] ) ) : '';
		$message = isset( $_POST['chrysotile_contact_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['chrysotile_contact_message'] ) ) : '';
		$consent = isset( $_POST['chrysotile_contact_consent'] ) ? sanitize_text_field( wp_unslash( $_POST['chrysotile_contact_consent'] ) ) : '';

		if ( '' === $name || '' === $phone || '' === $message || ! is_email( $email ) || '1' !== $consent || ! chrysotile_contact_is_valid_phone( $phone ) ) {
			chrysotile_contact_redirect( 'error', 'validation' );
		}

		$bot_token = trim( (string) apply_filters( 'chrysotile_contact_telegram_bot_token', '8722464547:AAGCyJOQRGkD-MNVIxJjyYX-hdM7WIlspFo' ) );
		$chat_id   = trim( (string) apply_filters( 'chrysotile_contact_telegram_chat_id', '-1003999440290' ) );

		if ( '' === $bot_token || '' === $chat_id ) {
			chrysotile_contact_redirect( 'error', 'telegram_config' );
		}

		$text = sprintf(
			"%s: %s\n%s: %s\n%s: %s\n%s: %s",
			__( 'Имя', 'chrysotile-child' ),
			$name,
			__( 'Телефон', 'chrysotile-child' ),
			$phone,
			__( 'Почта', 'chrysotile-child' ),
			$email,
			__( 'Сообщение', 'chrysotile-child' ),
			$message
		);

		$endpoint = sprintf(
			'https://api.telegram.org/bot%s/sendMessage',
			$bot_token
		);

		$response = wp_remote_post(
			$endpoint,
			array(
				'timeout' => 15,
				'httpversion' => '1.1',
				'body'    => array(
					'chat_id' => $chat_id,
					'text'    => $text,
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			$get_url = add_query_arg(
				array(
					'chat_id' => $chat_id,
					'text'    => $text,
				),
				$endpoint
			);
			$get_response = wp_remote_get(
				$get_url,
				array(
					'timeout'     => 15,
					'httpversion' => '1.1',
				)
			);
			if ( ! is_wp_error( $get_response ) && 200 === (int) wp_remote_retrieve_response_code( $get_response ) ) {
				$get_body = json_decode( (string) wp_remote_retrieve_body( $get_response ), true );
				if ( is_array( $get_body ) && ! empty( $get_body['ok'] ) ) {
					chrysotile_contact_redirect( 'sent' );
				}
			}

			// Last-resort fallback for hosts with broken CA bundle.
			$insecure_response = wp_remote_post(
				$endpoint,
				array(
					'timeout'     => 15,
					'httpversion' => '1.1',
					'sslverify'   => false,
					'body'        => array(
						'chat_id' => $chat_id,
						'text'    => $text,
					),
				)
			);
			if ( ! is_wp_error( $insecure_response ) && 200 === (int) wp_remote_retrieve_response_code( $insecure_response ) ) {
				$insecure_body = json_decode( (string) wp_remote_retrieve_body( $insecure_response ), true );
				if ( is_array( $insecure_body ) && ! empty( $insecure_body['ok'] ) ) {
					chrysotile_contact_redirect( 'sent' );
				}
			}

			$get_err      = is_wp_error( $get_response ) ? $get_response->get_error_message() : 'GET non-ok';
			$insecure_err = is_wp_error( $insecure_response ) ? $insecure_response->get_error_message() : 'insecure non-ok';
			$debug_msg    = 'HTTP transport error: POST=' . $response->get_error_message() . '; GET=' . $get_err . '; SSL_OFF=' . $insecure_err;
			chrysotile_contact_set_debug_message( $debug_msg );
			error_log( 'chrysotile_contact telegram wp_error: ' . $debug_msg ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			chrysotile_contact_redirect( 'error', 'telegram_http' );
		}

		$http_code = (int) wp_remote_retrieve_response_code( $response );
		$body = json_decode( (string) wp_remote_retrieve_body( $response ), true );
		if ( 200 !== $http_code || ! is_array( $body ) || empty( $body['ok'] ) ) {
			// Если группа была мигрирована в supergroup, повторяем запрос с новым chat_id.
			if ( is_array( $body )
				&& isset( $body['parameters']['migrate_to_chat_id'] )
				&& '' !== (string) $body['parameters']['migrate_to_chat_id'] ) {
				$migrated_chat_id = (string) $body['parameters']['migrate_to_chat_id'];
				$retry_response   = wp_remote_post(
					$endpoint,
					array(
						'timeout' => 15,
						'httpversion' => '1.1',
						'body'    => array(
							'chat_id' => $migrated_chat_id,
							'text'    => $text,
						),
					)
				);

				if ( ! is_wp_error( $retry_response ) && 200 === (int) wp_remote_retrieve_response_code( $retry_response ) ) {
					$retry_body = json_decode( (string) wp_remote_retrieve_body( $retry_response ), true );
					if ( is_array( $retry_body ) && ! empty( $retry_body['ok'] ) ) {
						chrysotile_contact_redirect( 'sent' );
					}
				}
			}

			$description = is_array( $body ) && ! empty( $body['description'] ) ? (string) $body['description'] : 'unknown';
			chrysotile_contact_set_debug_message( 'Telegram API: HTTP ' . $http_code . ', ' . $description );
			error_log( 'chrysotile_contact telegram fail: HTTP ' . $http_code . ', ' . $description ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			chrysotile_contact_redirect( 'error', 'telegram_api' );
		}

		chrysotile_contact_redirect( 'sent' );
	}

	add_action( 'admin_post_nopriv_chrysotile_contact', 'chrysotile_contact_form_handler' );
	add_action( 'admin_post_chrysotile_contact', 'chrysotile_contact_form_handler' );
}

if ( ! function_exists( 'chrysotile_contacts_yandex_map_embed_url' ) ) {
	/**
	 * URL iframe Яндекс.Карт для страницы контактов.
	 * Замените через фильтр `chrysotile_contacts_yandex_map_embed_url`.
	 *
	 * @return string
	 */
	function chrysotile_contacts_yandex_map_embed_url() {
		$default = 'https://yandex.ru/map-widget/v1/?ll=61.187946%2C52.183115&z=16&l=map&pt=61.187946%2C52.183115%2Cpm2rdm';

		return apply_filters( 'chrysotile_contacts_yandex_map_embed_url', $default );
	}
}

/**
 * Версия правил ЧПУ (сброс после смены логики ссылок).
 */
define( 'CHRYSOTILE_POST_URL_REWRITE_VERSION', 2 );

/**
 * Черновик URL-ярлыка из заголовка: кириллица → латиница, дефисы, без ID и кода.
 *
 * @param string $title Заголовок записи.
 * @return string
 */
function chrysotile_child_transliterate_title_to_slug( $title ) {
	$title = (string) $title;
	if ( '' === $title ) {
		return '';
	}

	$map = array(
		'А' => 'A',
		'а' => 'a',
		'Б' => 'B',
		'б' => 'b',
		'В' => 'V',
		'в' => 'v',
		'Г' => 'G',
		'г' => 'g',
		'Д' => 'D',
		'д' => 'd',
		'Е' => 'E',
		'е' => 'e',
		'Ё' => 'Yo',
		'ё' => 'yo',
		'Ж' => 'Zh',
		'ж' => 'zh',
		'З' => 'Z',
		'з' => 'z',
		'И' => 'I',
		'и' => 'i',
		'Й' => 'Y',
		'й' => 'y',
		'К' => 'K',
		'к' => 'k',
		'Л' => 'L',
		'л' => 'l',
		'М' => 'M',
		'м' => 'm',
		'Н' => 'N',
		'н' => 'n',
		'О' => 'O',
		'о' => 'o',
		'П' => 'P',
		'п' => 'p',
		'Р' => 'R',
		'р' => 'r',
		'С' => 'S',
		'с' => 's',
		'Т' => 'T',
		'т' => 't',
		'У' => 'U',
		'у' => 'u',
		'Ф' => 'F',
		'ф' => 'f',
		'Х' => 'Kh',
		'х' => 'kh',
		'Ц' => 'Ts',
		'ц' => 'ts',
		'Ч' => 'Ch',
		'ч' => 'ch',
		'Ш' => 'Sh',
		'ш' => 'sh',
		'Щ' => 'Sch',
		'щ' => 'sch',
		'Ъ' => '',
		'ъ' => '',
		'Ы' => 'Y',
		'ы' => 'y',
		'Ь' => '',
		'ь' => '',
		'Э' => 'E',
		'э' => 'e',
		'Ю' => 'Yu',
		'ю' => 'yu',
		'Я' => 'Ya',
		'я' => 'ya',
		'Ғ' => 'G',
		'ғ' => 'g',
		'Қ' => 'Q',
		'қ' => 'q',
		'Ң' => 'N',
		'ң' => 'n',
		'Ү' => 'U',
		'ү' => 'u',
		'Ұ' => 'U',
		'ұ' => 'u',
		'Һ' => 'H',
		'һ' => 'h',
		'І' => 'I',
		'і' => 'i',
	);

	$s = strtr( $title, $map );
	$s = remove_accents( $s );
	$s = strtolower( $s );
	$s = preg_replace( '/[^a-z0-9]+/', '-', $s );
	$s = trim( (string) $s, '-' );
	$s = preg_replace( '/-+/', '-', $s );

	return is_string( $s ) ? $s : '';
}

/**
 * При сохранении записи задаёт post_name из транслитерации заголовка (латиница).
 *
 * @param array<string,mixed> $data    Данные для вставки/обновления.
 * @param array<string,mixed> $postarr Исходные данные из формы/REST.
 * @return array<string,mixed>
 */
function chrysotile_child_post_data_latin_slug_from_title( $data, $postarr ) {
	if ( ! is_array( $data ) || 'post' !== ( $data['post_type'] ?? '' ) ) {
		return $data;
	}

	$status = (string) ( $data['post_status'] ?? '' );
	if ( in_array( $status, array( 'inherit', 'trash' ), true ) ) {
		return $data;
	}

	$title = (string) ( $data['post_title'] ?? '' );
	$base  = chrysotile_child_transliterate_title_to_slug( $title );
	$base  = apply_filters( 'chrysotile_child_post_slug_base', $base, $data, $postarr );

	if ( '' === $base ) {
		$base = 'post';
	}

	$post_id = isset( $postarr['ID'] ) ? (int) $postarr['ID'] : (int) ( $data['ID'] ?? 0 );
	$parent  = isset( $data['post_parent'] ) ? (int) $data['post_parent'] : 0;

	$data['post_name'] = wp_unique_post_slug( $base, $post_id, $status, 'post', $parent );

	return $data;
}

add_filter( 'wp_insert_post_data', 'chrysotile_child_post_data_latin_slug_from_title', 99, 2 );

/**
 * Desktop only: remove "Контакты" item from "Ещё" dropdown.
 *
 * Mobile burger menu remains unchanged.
 */
add_action(
	'wp_footer',
	function () {
		?>
		<script>
			(function () {
				if (window.matchMedia && window.matchMedia('(max-width: 1023px)').matches) {
					return;
				}
				var dropdown = document.getElementById('chrysotile-nav-more-dropdown');
				if (!dropdown) {
					return;
				}
				var links = dropdown.querySelectorAll('a[role="menuitem"]');
				links.forEach(function (link) {
					var label = (link.textContent || '').trim().toLowerCase();
					if (label === 'контакты') {
						link.remove();
					}
				});
			})();
		</script>
		<?php
	},
	50
);

add_action(
	'init',
	function () {
		$ver = (int) get_option( 'chrysotile_post_url_rewrite_ver', 0 );
		if ( $ver < CHRYSOTILE_POST_URL_REWRITE_VERSION ) {
			flush_rewrite_rules( false );
			update_option( 'chrysotile_post_url_rewrite_ver', CHRYSOTILE_POST_URL_REWRITE_VERSION );
		}
	},
	999
);
