<?php
/**
 * Search form template.
 *
 * @package Chrysotile_Child
 */
?>
<form role="search" method="get" class="chrysotile-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="chrysotile-search-field"><?php esc_html_e( 'Поиск:', 'chrysotile-child' ); ?></label>
	<input id="chrysotile-search-field" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Поиск...', 'chrysotile-child' ); ?>">
	<button type="submit"><?php esc_html_e( 'Поиск', 'chrysotile-child' ); ?></button>
</form>
