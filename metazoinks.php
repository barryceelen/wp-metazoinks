<?php
/**
 * Main plugin file
 *
 * @package    WordPress
 * @subpackage Metazoinks
 * @version    1.0.0
 * @license    GPL-3.0+
 * @link       https://github.com/barryceelen/wp-metazoinks
 * @copyright  2017 Barry Ceelen
 */

/*
 * Plugin Name:       Metazoinks
 * Plugin URI:        https://github.com/barryceelen/wp-metazoinks
 * Description:       Edit the title and description &lt;meta&gt; tag for your public post types.
 * Author:            Barry Ceelen
 * Author URI:        https://github.com/barryceelen
 * Version:           1.0.0
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/barryceelen/wp-metazoinks
 */

add_filter( 'pre_get_document_title', 'metazoinks_title', 11 );
add_action( 'wp_head', 'metazoinks_description', 11 );
add_action( 'init', 'metazoinks_load_textdomain' );

/**
 * Filters the page title for posts and pages.
 *
 * @since 1.0.0
 * @param string $title Page title.
 * @return string Filtered page title.
 */
function metazoinks_title( $title ) {

	if ( is_singular() ) {

		global $post;

		if ( ! empty( $post->_metazoinks_title ) ) {
			$title = $post->_metazoinks_title;
		}
	}

	/**
	 * Filter the title.
	 *
	 * @since 1.0.0
	 *
	 * @param string $title Title meta tag string.
	 */
	return apply_filters( 'metazoinks_title', $title );
}

/**
 * Add a description meta tag for posts and pages to the page header.
 *
 * @since 1.0.0
 */
function metazoinks_description() {

	if ( ! is_singular() ) {
		return;
	}

	global $post;

	if ( ! empty( $post->_metazoinks_description ) ) {

		$description = $post->_metazoinks_description;

		/**
		 * Filter the description.
		 *
		 * @since 1.0.0
		 *
		 * @param string $title Description meta tag string.
		 */
		$description = apply_filters( 'metazoinks_description', $post->_metazoinks_description );

		echo '<meta name="description" content="' . esc_attr( $description ) . '">';
	}
}

/**
 * Load plugin textdomain.
 *
 * @since 1.0.0
 */
function metazoinks_load_textdomain() {

	if ( false !== strpos( __FILE__, basename( WPMU_PLUGIN_DIR ) ) ) {
		load_muplugin_textdomain( 'metazoinks', dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	} else {
		load_plugin_textdomain( 'metazoinks', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
}

if ( is_admin() ) {
	include_once 'admin/class-metazoinks-admin.php';
}
