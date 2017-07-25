# WordPress Metazoinks

[![Build Status](https://travis-ci.org/barryceelen/wp-metazoinks.svg?branch=master)](https://travis-ci.org/barryceelen/wp-metazoinks)

A.K.A "Yet Another Meta Description Plugin."
A WordPress plugin for editing the title and description `<meta>` tag for your public post types.

Filter the post types and/or add additional title and description inputs (eg. for sites with multiple languages):

	// Filter metazoinks options.
	add_filter( 'metazoinks_options', 'myprefix_filter_metazoinks_options' );

	/**
	 * Filter metazoinks options.
	 *
	 * @param array $options List of options.
	 * @return array
	 */
	function myprefix_filter_metazoinks_options( $options ) {

		$options['post_types'] = array( 'post', 'my_cool_post_type' );

		$options['title_inputs][] = array(
			'label' => __( 'Title (French)', 'prefix' ),
			'meta_key' => '_meta_title_fr',
		);

		$options['description_inputs][] = array(
			'label' => __( 'Description (French)', 'prefix' ),
			'meta_key' => '_meta_title_fr',
		);

		return $options;
	}

Do something with your additional inputs:

	// Filter document title.
	add_filter( 'metazoinks_title', 'myprefix_filter_metazoinks_title' );

	/**
	 * Filter document title.
	 *
	 * @param string $title Document title.
	 * @return string
	 */
	function myprefix_filter_metazoinks_title( $title ) {

		// Assuming you have some sort of multilanguage setup, stuff like this might work.
		if ( 'fr_FR' === get_locale() ) {

			global $post;

			if ( ! empty( $post->_meta_title_fr ) ) {
				$title = esc_attr( $post->_meta_title_fr );
			}
		}

		return $title;
	}

