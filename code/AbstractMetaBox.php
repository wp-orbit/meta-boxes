<?php
namespace WPOrbit\MetaBoxes;

/**
 * Class AbstractMetaBox
 *
 * @package WPOrbit\MetaBoxes
 */
abstract class AbstractMetaBox
{
	/**
	 * @var string The meta box ID.
	 */
	protected $id;

	/**
	 * @var string The meta box label.
	 */
	protected $label;

	/**
	 * @var array An array of post types this meta box should be hooked into.
	 */
	protected $post_types = [];

	/**
	 * @var array An array of post slugs that this should be hooked into.
	 */
	protected $post_slugs = [];

	/**
	 * Get meta box (element) ID.
	 *
	 * @return mixed
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get meta box label.
	 *
	 * @return mixed|string
	 */
	public function get_label() {
		return $this->label;
	}

	public function __construct( $args = [] ) {

		$args = wp_parse_args( $args, [
			'id' => 'wp-orbit-meta-box',
			'label' => 'My Metabox',
			'post_slugs' => [],
			'post_types' => []
		]);
		
		if ( is_string( $args['post_slugs'] ) ) {
			$args['post_slugs'] = (array) $args['post_slugs'];
		}

		if ( is_string( $args['post_types'] ) ) {
			$args['post_types'] = (array) $args['post_types'];
		}

		// Set arguments.
		$this->id = $args['id'];
		$this->label = $args['label'];
		$this->post_types = $args['post_types'];
		$this->post_slugs = $args['post_slug'];

		$this->register();
	}

	/**
	 * @param \WP_Post $post
	 */
	public function render( \WP_Post $post ) {
		echo 'Override the render function.';
	}

	public function save( $post_id, $post_type ) {
		// Override save function..
	}

	public function register() {
		foreach ( $this->post_types as $post_type ) {
			add_action( "add_meta_boxes_{$post_type}", function ($post) {
				// Check if there are matching post slugs.
				if ( ! empty( $this->post_slugs ) && ! in_array( $post->post_name, $this->post_slugs ) ) {
					return;
				}
				add_meta_box(
					$this->id,
					$this->label,
					[$this, 'render'],
					null,
					'normal',
					'default'
				);
			} );

			add_action( 'save_post', function ( $postId ) use ( $post_type ) {
				$this->save( $postId, $post_type );
			} );
		}
	}
}