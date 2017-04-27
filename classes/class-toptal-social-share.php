<?php
/**
 * The core plugin class.
 *
 * @since  1.0.0
 */
class Toptal_Social_Share {

	/**
	 * Plugin display name.
	 */
	private $plugin_display_name;

	/**
	 * The settings options.
	 *
	 * @since  1.0.0
	 */
	private $options;

	/**
	 * The Constructor.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {

		$this->options = get_option( 'tss_options' );
		$this->plugin_display_name = __( 'Toptal Social Share', 'toptal-social-share' );
	}

	/**
	 * Initialize the plugin hooks.
	 *
	 * @since  1.0.0
	 */
	public function initialize() {

		// Load the plugin text domain.
		add_action( 'init', array( $this, 'load_text_domain' ) );

		// Set up the admin options page.
		add_action( 'admin_menu', array( $this, 'register_options_page' ) );

		// Register settings
		add_action( 'admin_init', array( $this, 'register_settings_and_fields' ) );

		// Enqueue our JS scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );

		// Add an action link to the settings page on the plugins page.
		add_filter( 'plugin_action_links_' . TSS_SOCIAL_SHARE_BASENAME, array( $this, 'plugins_page_action_links' ) );
	}

	/**
	 * Load plugin text domain.
	 *
	 * @since  1.0.0
	 */
	public function load_text_domain() {

		load_plugin_textdomain( TSS_SOCIAL_SHARE_SLUG, false, TSS_SOCIAL_SHARE_PATH . '/languages' );
	}

	/**
	 * Register our options page.
	 *
	 * @since  1.0.0
	 */
	public function register_options_page() {

		add_options_page(
			$this->plugin_display_name,
			$this->plugin_display_name,
			'manage_options',
			TSS_SOCIAL_SHARE_SLUG,
			array( $this, 'render_admin_page' )
		);
	}

	/**
	 * Output the plugin settings page contents.
	 *
	 * @since  1.0.0
	 */
	public function render_admin_page() {
		?>
		<div class="wrap">
			<h2><?php echo esc_html( $this->plugin_display_name ); ?></h2>
			<form method="post" action="options.php" style="margin-bottom: 40px;">
			    <?php settings_fields( 'tss_option_group' ); ?>
				<?php do_settings_sections( TSS_SOCIAL_SHARE_SLUG ); ?>
			    <?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register Admin Settings.
	 *
	 * @since  1.0.0
	 */
	public function register_settings_and_fields() {

		register_setting(
			'tss_option_group',
			'tss_options',
			array( $this, 'validate_options' )
		);

		add_settings_section(
			'tss_general_settings_section',
			__( 'General Settings', 'toptal-social-share' ),
			array( $this, 'settings_section' ),
			TSS_SOCIAL_SHARE_SLUG
		);

		add_settings_field(
			'post_types',
			__( 'Display Social Share bar on', 'toptal-social-share' ),
			array( $this, 'render_field_post_types' ),
			TSS_SOCIAL_SHARE_SLUG,
			'tss_general_settings_section'
		);
	}

	/**
	 * Output the settings section.
	 *
	 * @since  1.0.0
	 */
	public function settings_section() {
		return;
	}

	/**
	 * Render field for Post Types
	 *
	 * @since  1.0.0
	 */
	public function render_field_post_types() {

		// Get current options
		$options = get_option( 'tss_options' );

		// Check if there is any Post Type already stored
		if ( isset( $options['post_types'] ) ) {
			$selected_post_types = $options['post_types'];
		} else {
			$selected_post_types = array();
		}

		// Get list of public Post Types and render checkboxes
		$public_post_types = $this->get_public_post_types();
		?>

		<fieldset>
			<?php foreach ( $public_post_types as $key => $value ) : ?>
				<label><input type="checkbox" name="tss_options[post_types][<?php echo $key; ?>]" value="1" <?php checked( 1, $selected_post_types[$key], true ); ?>><?php echo esc_html( $value ); ?></label><br>
			<?php endforeach; ?>
		</fieldset>

		<?php
	}

	/**
	 * Return an array of all public post types.
	 *
	 * @since   1.0.0
	 *
	 * @param   string  $context  The context to pass to our filter.
	 *
	 * @return  array             The array of formatted post types.
	 */
	public function get_public_post_types( $context = '' ) {

		$post_type_args = array(
			'public'   => true,
			'_builtin' => false
		);

		$custom_post_types = get_post_types( $post_type_args, 'objects', 'and' );

		$formatted_cpts = array();

		foreach( $custom_post_types as $post_type ) {
			$formatted_cpts[ $post_type->name ] = $post_type->labels->name;
		}

		// Manually add 'post' and 'page' types.
		$default_post_types = array(
			'post' => __( 'Posts', 'toptal-social-share' ),
			'page' => __( 'Pages', 'toptal-social-share' ),
		);

		$post_types = $default_post_types + $formatted_cpts;

		// Allow devs to filter post types
		return apply_filters( 'toptal_social_share_post_types', $post_types, $context );
	}

	/**
	 * Validate our options before saving.
	 *
	 * @since   1.0.0
	 *
	 * @param   array  $input  The options to update.
	 *
	 * @return  array          The updated options.
	 */
	public function validate_options( $input ) {

		// TODO
		return $input;
	}

	/**
	 * Enqueue the admin JS
	 *
	 * @since  1.0.0
	 */
	public function admin_enqueue( $hook ) {

		// Only enqueue on our settings page
		if ( 'settings_page_' . TSS_SOCIAL_SHARE_SLUG == $hook ) {

			wp_enqueue_script(
				'tss-admin-js',
				TSS_SOCIAL_SHARE_URL . '/js/admin.js',
				array( 'jquery', 'jquery-ui-sortable' )
			);
		}
	}

	/**
	 * Add an action link to the settings page.
	 *
	 * @since  1.0.0
	 */
	public function plugins_page_action_links( $links ) {

		$links[] = '<a href="'. get_admin_url( null, 'options-general.php?page=' . TSS_SOCIAL_SHARE_SLUG ) .'">' . __( 'Settings', 'toptal-social-share' ) . '</a>';

		return $links;
	}

}