<?php
/**
 * Template for the meta box
 *
 * @package    WordPress
 * @subpackage Metazoinks
 * @version    1.0.0
 * @license    GPL-3.0+
 * @link       https://github.com/barryceelen/wp-metazoinks
 * @copyright  2017 Barry Ceelen
 */

global $post;

if ( ! empty( $titles ) ) {

	echo '<p>';

	foreach ( $titles as $title ) {

		$value = $post->{$title['meta_key']};

		echo '<label for="metazoinks_titles[' . esc_attr( $title['meta_key'] ) . ']">' . esc_html( $title['label'] ) . '</label><br>';
		echo '<input type="text" name="metazoinks_titles[' . esc_attr( $title['meta_key'] ) . ']" value="' . esc_attr( $value ) . '" style="width:100%"/><br>';
	}

	echo '<p class="howto">' . esc_html__( 'Title tags should be 50-60 characters long, including spaces.', 'metazoinks' ) . '</p>';
	echo '</p>';
}

if ( ! empty( $descriptions ) ) {

	echo '<p>';

	foreach ( $descriptions as $description ) {

		$value = $post->{$description['meta_key']};

		echo '<label for="metazoinks_descriptions[' . esc_attr( $description['meta_key'] ) . ']">' . esc_html( $description['label'] ) . '</label><br>';
		echo '<textarea style="width: 100%;" name="metazoinks_descriptions[' . esc_attr( $description['meta_key'] ) . ']">' . esc_attr( $value ) . '</textarea><br>';
	}

	echo '<p class="howto">' . esc_html__( 'A description should be no longer than 135 – 160 characters.', 'metazoinks' ) . '</p>';
	echo '</p>';
}
