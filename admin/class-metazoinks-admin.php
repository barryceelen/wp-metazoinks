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

		$defaults = array(
			'post_types'         => get_post_types(
				array(
					'public' => true,
				)
			),
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
		$this->args = apply_filters( 'metazoinks_options', $defaults );

		$this->add_actions_and_filters();
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
		add_action( 'save_post', array( $this, 'save_meta_box' ), 10, 2 );
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

		add_meta_box(
			'metazoinks',
			esc_html__( 'SEO Title and Description', 'metazoinks' ),
			array( $this, 'render_meta_box' ),
			$this->args['post_types'],
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

		$titles       = empty( $this->args['title_inputs'] ) ? array() : $this->args['title_inputs'];
		$descriptions = empty( $this->args['description_inputs'] ) ? array() : $this->args['description_inputs'];

		wp_nonce_field( 'save-metazoinks', 'metazoinks-nonce', true );

		// Todo: Ok, wait, this path stuff is crazy...
		$path = strpos( __FILE__, basename( WPMU_PLUGIN_DIR ) ) ? WPMU_PLUGIN_DIR : WP_PLUGIN_DIR;
		include trailingslashit( $path ) . dirname( plugin_basename( __DIR__ ) . '/admin/templates/tmpl-meta-box.php' );
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
	public function save_meta_box( $post_id, $post ) {

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

		if ( ! empty( $this->args['title_inputs'] ) ) {
			foreach ( $this->args['title_inputs'] as $input ) { // Todo: Verify $this->args['title_inputs'] for correctness.
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

		if ( ! empty( $this->args['description_inputs'] ) ) { // Todo: Verify $this->args['description_inputs'] for correctness.
			foreach ( $this->args['description_inputs'] as $input ) {
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
