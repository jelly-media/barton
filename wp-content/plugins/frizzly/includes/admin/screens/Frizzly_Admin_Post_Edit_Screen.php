<?php

class Frizzly_Admin_Post_Edit_Screen {

	private $name;
	private $file;
	private $meta_nonce_name;
	/**
	 * @var Frizzly_Meta_Social_Data
	 */
	private $meta;
	private $networks;
	private $version;
	private $screen_hooks;
	/**
	 * @var Frizzly_Share_Options
	 */
	private $share_options;

	/**
	 * @var string[]
	 */
	private $meta_submodules;

	function __construct( $name, $version, $file ) {
		$this->name            = $name;
		$this->version         = $version;
		$this->file            = $file;
		$this->meta_nonce_name = 'frizzly_edit_post';
		$this->meta            = new Frizzly_Meta_Social_Data();
		$this->screen_hooks    = array( 'post.php', 'post-new.php' );
		$this->share_options   = new Frizzly_Share_Options();
		$this->meta_submodules = array(
			'image'   => __( 'Image', 'frizzly' ),
			'content' => __( 'Content', 'frizzly' )
		);

		$this->networks = array(
			'facebook'  => __( 'Facebook', 'frizzly' ),
			'twitter'   => __( 'Twitter', 'frizzly' ),
			'pinterest' => __( 'Pinterest', 'frizzly' ),
		);
	}

	function add_meta_box( $post_type, $post ) {
		add_meta_box(
			'frizzly-post-meta',
			__( 'Frizzly Post Specific Settings', 'frizzly' ),
			array( $this, 'render_meta' ),
			array( 'post', 'page' )
		);
	}

	function init() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ), 10, 2 );
		add_action( 'save_post', array( $this, 'save_meta' ), 10, 3 );
	}

	function enqueue_admin_scripts( $hook ) {
		if ( ! in_array( $hook, $this->screen_hooks ) ) {
			return;
		}
		$plugin_dir_url = plugin_dir_url( $this->file );

		wp_enqueue_script( 'frizzly-meta-js', $plugin_dir_url . 'js/frizzly.meta.js', array( 'jquery' ), $this->version, true );
		$settings = array(
			'i18n' => array(
				'select_image' => array(
					'title' => __( 'Select image', 'frizzly' ),
					'text'  => __( 'Select', 'frizzly' ),
				)
			)
		);
		wp_localize_script( 'frizzly-meta-js', 'frizzly_meta', $settings );
		wp_enqueue_style( 'frizzly-meta-css', $plugin_dir_url . 'css/frizzly.meta.css', array(), $this->version );
	}

	function render_meta() {
		global $post;
		$id                     = $post->ID;
		$meta                   = $this->meta->get( $id );
		$recommended_image_size = array(
			'facebook' => array( 1024, 512 ),
			'twitter'  => array( 1200, 630 )
		);
		?>
		<?php wp_nonce_field( $this->meta_nonce_name, $this->meta_nonce_name ); ?>
        <div class="frizzly-tabs-container">
            <ul class="frizzly-tabs">
                <li class="frizzly-tab-active">
                    <a href="#" data-frizzly-id="frizzly-general"><?php _e( 'General', 'frizzly' ); ?></a>
                </li>
				<?php foreach ( $this->networks as $net_slug => $net_name ): ?>
                    <li>
                        <a href="#" data-frizzly-id="frizzly-<?php echo $net_slug; ?>"><?php echo $net_name; ?></a>
                    </li>
				<?php endforeach; ?>
            </ul>
            <div id="frizzly-general" class="frizzly-tab-panel"><?php $this->render_general_tab( $id ); ?></div>
			<?php foreach ( $this->networks as $net_slug => $net_name ): ?>
                <div id="frizzly-<?php echo $net_slug; ?>" class="frizzly-tab-panel" style="display:none">
					<?php $this->render_network_tab( $net_slug, $net_name, $meta[ $net_slug ], $recommended_image_size[ $net_slug ] ); ?>
                </div>
			<?php endforeach; ?>
        </div>
		<?php
	}

	function render_general_tab( $id ) {
		$share_options      = $this->share_options->get();
		$enabled_submodules = array();
		foreach ( $this->meta_submodules as $sub_slug => $sub_name ) {
			$disabled_list  = $share_options[ $sub_slug ]['disabled_on'];
			$disabled_array = explode( ',', $disabled_list );
			if ( ! in_array( (string) $id, $disabled_array ) ) {
				$enabled_submodules[] = $sub_slug;
			}
		}
		?>
        <table class="form-table">
            <tbody>
            <tr>
                <th><?php _e( 'Active Share Modules', 'frizzly' ); ?></th>
                <td>
					<?php foreach ( $this->meta_submodules as $sub_slug => $sub_name ): ?>
                        <label>
                            <input name="frizzly-disabled-<?php echo $sub_slug; ?>" value="1"
                                   type="checkbox" <?php checked( in_array( $sub_slug, $enabled_submodules ) ); ?>/>
							<?php echo $sub_name; ?>
                        </label><br/>
					<?php endforeach; ?>
                    <p class="description"><?php _e( 'Share icons from these share modules will be active in this entry.', 'frizzly' ); ?></p>
                </td>
            </tr>
            </tbody>
        </table>
		<?php
	}

	function render_network_tab( $network_slug, $network_name, $meta, $featured_image_size = null ) {
		?>
        <p class="description"><?php printf( __( 'If you want to use custom settings for sharing this post on %s, fill the form below. Otherwise, leave it empty.', 'frizzly' ), $network_name ); ?></p>
        <table class="form-table">
            <tbody>
            <tr>
                <th>
                    <label for="frizzly_<?php echo $network_slug; ?>_title"><?php printf( __( '%s title', 'frizzly' ), $network_name ); ?></label>
                </th>
                <td><input class="large-text" id="frizzly_<?php echo $network_slug; ?>_title"
                           name="frizzly_<?php echo $network_slug; ?>_title"
                           value="<?php echo $meta['title']; ?>"/>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="frizzly_<?php echo $network_slug; ?>_description"><?php printf( __( '%s description', 'frizzly' ), $network_name ); ?></label>
                </th>
                <td>
                    <textarea class="large-text" id="frizzly_<?php echo $network_slug; ?>_description"
                              name="frizzly_<?php echo $network_slug; ?>_description"><?php echo $meta['description']; ?></textarea>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="frizzly_<?php echo $network_slug; ?>_image"><?php printf( __( '%s image', 'frizzly' ), $network_name ); ?></label>
                </th>
                <td>
                    <input id="frizzly_<?php echo $network_slug; ?>_image" size="64"
                           name="frizzly_<?php echo $network_slug; ?>_image"
                           value="<?php echo $meta['image']; ?>"/>
                    <input type="button" class="button frizzly-image-selector"
                           data-frizzly-network="<?php echo $network_slug; ?>"
                           value="<?php _e( 'Upload image', 'frizzly' ); ?>"/>
                    <p class="description">
						<?php if ( $featured_image_size ) {
							printf( __( 'Recommended image size for %s is %s by %s pixels.', 'frizzly' ), $network_name, $featured_image_size[0], $featured_image_size[1] );
						} ?>
                    </p>
                </td>
            </tr>
			<?php if ( $network_slug == 'pinterest' ): ?>
				<?php $this->render_pinterest_rows( $network_slug, $network_name, $meta ); ?>
			<?php endif; ?>
            </tbody>
        </table>
        <p><?php printf( __( 'You can edit global %s settings <a href="%s" target="_blank">here</a>.',
				'frizzly' ), $network_name, admin_url( 'admin.php?page=frizzly_settings_general&tab=' . $network_slug ) ); ?>
        </p>
		<?php
	}

	function render_pinterest_rows( $network_slug, $network_name, $meta ) {
		?>
        <tr>
            <th>
                <label for="frizzly_<?php echo $network_slug; ?>_image_title"><?php printf( __( '%s image title', 'frizzly' ), $network_name ); ?></label>
            </th>
            <td><input class="large-text" id="frizzly_<?php echo $network_slug; ?>_image_title"
                       name="frizzly_<?php echo $network_slug; ?>_image_title"
                       value="<?php echo $meta['image_title']; ?>"/>
            </td>
        </tr>
        <tr>
            <th>
                <label for="frizzly_<?php echo $network_slug; ?>_image_alt"><?php printf( __( '%s image alt text', 'frizzly' ), $network_name ); ?></label>
            </th>
            <td><input class="large-text" id="frizzly_<?php echo $network_slug; ?>_image_alt"
                       name="frizzly_<?php echo $network_slug; ?>_image_alt"
                       value="<?php echo $meta['image_alt']; ?>"/>
            </td>
        </tr>
		<?php
	}

	function save_meta( $post_id, $post, $update ) {
		$return_if = ( ! current_user_can( "edit_post", $post_id ) ) ||
		             ( defined( "DOING_AUTOSAVE" ) && DOING_AUTOSAVE ) ||
		             ( ! isset( $_POST[ $this->meta_nonce_name ] ) ) ||
		             ( ! wp_verify_nonce( $_POST[ $this->meta_nonce_name ], $this->meta_nonce_name ) );
		if ( $return_if ) {
			return $post_id;
		}

		$this->save_meta_network_tabs( $post_id );
		$this->save_meta_general( $post_id );
	}

	function save_meta_general( $post_id ) {
		$options = $this->share_options->get();
		foreach ( $this->meta_submodules as $sub_slug => $sub_name ) {
			$should_be_in_array = ! isset( $_POST[ 'frizzly-disabled-' . $sub_slug ] );
			$disabled_str       = trim( $options[ $sub_slug ]['disabled_on'] );
			$disabled_array     = explode( ',', $disabled_str );
			$is_in_array        = in_array( (string) $post_id, $disabled_array );

			if ( $should_be_in_array === $is_in_array ) {
				continue;
			}

			if ( $should_be_in_array ) {
				$disabled_array[] = (string) $post_id;
			} else {
				$disabled_array = array_diff( $disabled_array, array( (string) $post_id ) );
			}
			$disabled_str               = implode( ',', $disabled_array );
			$sub_options['disabled_on'] = $disabled_str;
			$options[ $sub_slug ]       = $sub_options;
		}
		$this->share_options->update( $options );
	}

	function save_meta_network_tabs( $post_id ) {
		$settings = array(
			'facebook'  => $this->get_network_settings( 'facebook' ),
			'twitter'   => $this->get_network_settings( 'twitter' ),
			'pinterest' => $this->get_network_settings( 'pinterest' )
		);
		$this->meta->update( $post_id, $settings );
	}

	function get_network_settings( $network_name ) {
		$title_name       = sprintf( 'frizzly_%s_title', $network_name );
		$description_name = sprintf( 'frizzly_%s_description', $network_name );
		$image_name       = sprintf( 'frizzly_%s_image', $network_name );
		$image_title      = sprintf( 'frizzly_%s_image_title', $network_name );
		$image_alt        = sprintf( 'frizzly_%s_image_alt', $network_name );

		$arr = array(
			'title'       => ! empty( $_POST[ $title_name ] ) ? $_POST[ $title_name ] : '',
			'description' => ! empty( $_POST[ $description_name ] ) ? $_POST[ $description_name ] : '',
			'image'       => ! empty( $_POST[ $image_name ] ) ? $_POST[ $image_name ] : ''
		);
		if ( 'pinterest' != $network_name ) {
			return $arr;
		}
		$arr['image_title'] = ! empty( $_POST[ $image_title ] ) ? $_POST[ $image_title ] : '';
		$arr['image_alt']   = ! empty( $_POST[ $image_alt ] ) ? $_POST[ $image_alt ] : '';

		return $arr;
	}
}