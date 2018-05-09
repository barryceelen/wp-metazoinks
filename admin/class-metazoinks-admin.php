<?php
/**
 * Contains admin functionality
 *
 * @package    WordPress
 * @subpackage Metazoinks
 * @version    1.0.0
 * @license    GPL-3.0+
 * @link       https://github.com/barryceelen/wp-metazoinks
 * @copyright  2017 Barry Ceelen
 */

/**
 * Plugin admin class.
 *
 * @package   Abracadabra
 * @author    Barry Ceelen
 */
class Metazoinks_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Initialize.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {

		$this->add_actions_and_filters();
	}

	private function args() {

		$post_types = array_keys(
			get_post_types(
				array(
					'public' => true,
				)
			)
		);

		$defaults = array(
			'post_types'         => $post_types,
			'title_inputs'       => array(
				array(
					'label'    => __( 'Title', 'metazoinks' ),
					'meta_key' => '_metazoinks_title', // WPCS: slow query ok.
				),
			),
			'description_inputs' => array(
				array(
					'label'    => __( 'Description', 'metazoinks' ),
					'meta_key' => '_metazoinks_description', // WPCS: slow query ok.
				),
			),
		);

		/**
		 * Filter the plugin defaults.
		 *
		 * @since 1.0.0
		 *
		 * @param array $defaults An list of plugin defaults.
		 */
		return apply_filters( 'metazoinks_options', $defaults );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @return object A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Add actions and filters.
	 *
	 * @since 0.0.1
	 *
	 * @access private
	 * @return void
	 */
	private function add_actions_and_filters() {

		// Add meta box to the post edit screen.
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ), 10, 2 );

		// Save meta box values.
		add_action( 'save_post', array( $this, 'save_meta_box' ), 10 );
		add_action( 'edit_attachment', array( $this, 'save_meta_box' ), 10 );
	}

	/**
	 * Add meta box to the post edit screen.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $post_type Post type.
	 * @param WP_Post $post      Post object.
	 */
	public function add_meta_box( $post_type, $post ) {

		$args = $this->args();

		if ( ! in_array( $post_type, $args['post_types'] ) ) {
			return;
		}

		add_meta_box(
			'metazoinks',
			esc_html__( 'SEO Title and Description', 'metazoinks' ),
			array( $this, 'render_meta_box' ),
			$post_type,
			'normal',
			'low'
		);
	}

	/**
	 * Meta box content.
	 *
	 * @since 1.0.0
	 * @param WP_Post $post Post object.
	 */
	public function render_meta_box( $post ) {

		$args = $this->args();

		$titles       = empty( $args['title_inputs'] ) ? array() : $args['title_inputs'];
		$descriptions = empty( $args['description_inputs'] ) ? array() : $args['description_inputs'];

		wp_nonce_field( 'save-metazoinks', 'metazoinks-nonce', true );

		include METAZOINKS_PLUGIN_DIR . '/admin/templates/tmpl-meta-box.php';
	}

	/**
	 * Save meta box values.
	 *
	 * @since 1.0.0
	 *
	 * @todo Combine
	 *
	 * @global array $wp_post_types
	 * @param  int     $post_id Post ID.
	 * @param  WP_Post $post    Post object.
	 */
	public function save_meta_box( $post_id ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( empty( $_POST['metazoinks-nonce'] ) ) { // WPCS: input var okay.
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['metazoinks-nonce'] ) ), 'save-metazoinks' ) ) { // WPCS: input var okay.
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Note: not using $post passed along to save_post as this function may be called by `edit_attachment`.
		$post = get_post( $post_id );
		$args = $this->args();

		if ( ! in_array( $post->post_type, $args['post_types'], true ) ) {
			return;
		}

		if ( ! empty( $args['title_inputs'] ) ) {
			foreach ( $args['title_inputs'] as $input ) { // Todo: Verify $args['title_inputs'] for correctness.
				if ( empty( $_POST['metazoinks_titles'][ $input['meta_key'] ] ) ) { // WPCS: input var okay.
					delete_post_meta( $post_id, $input['meta_key'] );
				} else {
					update_post_meta(
						$post_id,
						$input['meta_key'],
						sanitize_text_field( wp_unslash( $_POST['metazoinks_titles'][ $input['meta_key'] ] ) ) // WPCS: input var okay.
					);
				}
			}
		}

		if ( ! empty( $args['description_inputs'] ) ) { // Todo: Verify $args['description_inputs'] for correctness.
			foreach ( $args['description_inputs'] as $input ) {
				if ( empty( $_POST['metazoinks_descriptions'][ $input['meta_key'] ] ) ) { // WPCS: input var okay.
					delete_post_meta( $post_id, $input['meta_key'] );
				} else {
					update_post_meta(
						$post_id,
						$input['meta_key'],
						sanitize_textarea_field( wp_unslash( $_POST['metazoinks_descriptions'][ $input['meta_key'] ] ) ) // WPCS: input var okay.
					);
				}
			}
		}
	}
}

global $metazoinks_admin;
$metazoinks_admin = Metazoinks_Admin::get_instance();
