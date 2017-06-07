<?php
/**
 * The core plugin class.
 *
 * @since  1.0.0
 */
class Toptal_Social_Share {

	/**
	 * Plugin display name.
	 *
	 * @since  1.0.0
	 */
	private $plugin_display_name;

	/**
	 * Default Networks.
	 *
	 * @since  1.0.0
	 */
	private $social_networks;

	/**
	 * The settings options.
	 *
	 * @since  1.0.0
	 */
	private $options;

	/**
	 * The Buttons CSS Classes.
	 *
	 * @since  1.0.0
	 */
	private $button_classes;

	/**
	 * The Icons CSS Classes.
	 *
	 * @since  1.0.0
	 */
	private $icon_classes;

	/**
	 * The Constructor.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {

		$this->options = get_option( 'tss_options' );

		$this->plugin_display_name = __( 'Toptal Social Share', TSS_SLUG );

		$this->social_networks = array( 'Facebook' , 'Twitter', 'Google+', 'Pinterest', 'LinkedIn', 'WhatsApp' );

		$this->button_classes = array(
			'Facebook'  => 'facebook',
			'Twitter'   => 'twitter',
			'Google+'   => 'google-plus',
			'Pinterest' => 'pinterest',
			'LinkedIn'  => 'linkedin',
			'WhatsApp'  => 'whatsapp'
		);

		$this->icon_classes = array(
			'Facebook'  => 'icon-facebook',
			'Twitter'   => 'icon-twitter',
			'Google+'   => 'icon-google-plus',
			'Pinterest' => 'icon-pinterest',
			'LinkedIn'  => 'icon-linkedin',
			'WhatsApp'  => 'icon-whatsapp'
		);
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

		// Enqueue our front-end scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Enqueue our admin scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_admin' ) );

		// Add an action link to the settings page on the plugins page.
		add_filter( 'plugin_action_links_' . TSS_BASENAME, array( $this, 'plugins_page_action_links' ) );

		// Add shortcode
		add_shortcode( 'tss_shortcode', array( $this, 'tss_shortcode' ) );

		// Load our Hooks
		add_action( 'wp', array( $this, 'load_hooks' ) );
	}

	/**
	 * Load plugin text domain.
	 *
	 * @since  1.0.0
	 */
	public function load_text_domain() {

		load_plugin_textdomain( TSS_SLUG, FALSE, dirname( TSS_BASENAME ) . '/languages' );
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
			TSS_SLUG,
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
				<?php do_settings_sections( TSS_SLUG ); ?>
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

		//
		// Add General Settings section and fields
		//
		add_settings_section(
			'tss_general_settings_section',
			__( 'General Settings', TSS_SLUG ),
			array( $this, 'render_settings_section' ),
			TSS_SLUG
		);

		add_settings_field(
			'post_types',
			__( 'Display Social Share bar on', TSS_SLUG ),
			array( $this, 'render_field_post_types' ),
			TSS_SLUG,
			'tss_general_settings_section'
		);

		add_settings_field(
			'activated_networks',
			__( 'Select active Networks', TSS_SLUG ),
			array( $this, 'render_field_activated_networks' ),
			TSS_SLUG,
			'tss_general_settings_section'
		);

		add_settings_field(
			'ordered_networks',
			null,
			array( $this, 'render_field_ordered_networks' ),
			TSS_SLUG,
			'tss_general_settings_section'
		);

		//
		// Add Social Icons Settings section and fields
		//
		add_settings_section(
			'tss_social_icons_settings_section',
			__( 'Social Icons Settings', TSS_SLUG ),
			array( $this, 'render_settings_section' ),
			TSS_SLUG
		);

		add_settings_field(
			'icons_size',
			__( 'Icons size', TSS_SLUG ),
			array( $this, 'render_field_icons_size' ),
			TSS_SLUG,
			'tss_social_icons_settings_section'
		);

		add_settings_field(
			'use_custom_color',
			__( 'Icons color', TSS_SLUG ),
			array( $this, 'render_field_use_custom_color' ),
			TSS_SLUG,
			'tss_social_icons_settings_section'
		);

		add_settings_field(
			'icons_custom_color',
			null,
			array( $this, 'render_field_icons_custom_color' ),
			TSS_SLUG,
			'tss_social_icons_settings_section'
		);

		add_settings_field(
			'icons_custom__background_color',
			null,
			function(){},
			TSS_SLUG,
			'tss_social_icons_settings_section'
		);

		add_settings_field(
			'icons_position',
			__( 'Icons position', TSS_SLUG ),
			array( $this, 'render_field_icons_position' ),
			TSS_SLUG,
			'tss_social_icons_settings_section'
		);

	}

	/**
	 * Output the settings section.
	 *
	 * @since  1.0.0
	 */
	public function render_settings_section() {
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

		// Get selected post types
		$selected_post_types = $options['post_types'];

		// Get list of public Post Types and render checkboxes
		$public_post_types = $this->get_public_post_types();

		?>
		<fieldset>
			<?php foreach ( $public_post_types as $key => $value ) : ?>
				<label style="margin-right: 40px !important;"><input type="checkbox" name="tss_options[post_types][<?php echo $key; ?>]" value="1" <?php checked( 1, $selected_post_types[$key], true ); ?>><?php echo esc_html( $value ); ?></label>
			<?php endforeach; ?>
		</fieldset>
		<?php
	}

	/**
	 * Render field for Activated Networks
	 *
	 * @since  1.0.0
	 */
	public function render_field_activated_networks() {

		// Get current options
		$options = get_option( 'tss_options' );

		// Explode our comma separated values into an array
		$this->social_networks = explode( ',', $options['ordered_networks'] );

		// Get active networks
		$activated_networks = $options['activated_networks'];

		?>
		<fieldset>
			<ul id="tss-sortable-networks">
				<?php foreach ( $this->social_networks as $network ) : ?>
					<li data-network="<?php echo $network; ?>">
						<label>
							<span class="social-icon <?php echo esc_attr( $this->button_classes[$network] ); ?>">
								<i class="<?php echo esc_attr( $this->icon_classes[$network] ); ?>"></i>
							</span>
							<input type="checkbox" name="tss_options[activated_networks][<?php echo $network; ?>]" value="1" <?php checked( 1, $activated_networks[$network], true ); ?>>
							<span>
								<?php echo esc_html( $network ); ?><?php echo $network == "WhatsApp" ? ' ' . __( '(mobile devices only)', TSS_SLUG ) : ''; ?>
							</span>
						</label>
					</li>
				<?php endforeach; ?>
			</ul>
			<p class="description"><?php echo __( 'Drag and drop the items to change the order of appearance.', TSS_SLUG ); ?></p>
		</fieldset>
		<?php
	}

	/**
	 * Render field for Social Icons size
	 *
	 * @since  1.0.0
	 */
	public function render_field_icons_size() {

		// Get current options
		$options = get_option( 'tss_options' );
		$current_size = $options['icons_size'];

		?>
		<fieldset>
			<label><input type="radio" name="tss_options[icons_size]" value="small" <?php checked( 'small', $current_size, true ); ?>><?php echo __( 'Small', TSS_SLUG ); ?></label><br>
			<label><input type="radio" name="tss_options[icons_size]" value="medium" <?php checked( 'medium', $current_size, true ); ?>><?php echo __( 'Medium', TSS_SLUG ); ?></label><br>
			<label><input type="radio" name="tss_options[icons_size]" value="large" <?php checked( 'large', $current_size, true ); ?>><?php echo __( 'Large', TSS_SLUG ); ?></label>
		</fieldset>
		<?php
	}

	/**
	 * Render field for Use Custom Color option
	 *
	 * @since  1.0.0
	 */
	public function render_field_use_custom_color() {

		// Get current options
		$options = get_option( 'tss_options' );
		$current_option = $options['use_custom_color'];

		?>
		<fieldset>
			<label><input type="checkbox" id="tss-use-custom-color-field" name="tss_options[use_custom_color]" value="1" <?php checked( 1, $current_option, true ); ?>><?php echo __( 'Select custom colors', TSS_SLUG ); ?></label>
			<p class="description"><?php echo __( 'Check this option to override default colors with custom ones.', TSS_SLUG ); ?></p>
		</fieldset>
		<?php
	}

	/**
	 * Render Color picker field to store custom color
	 *
	 * @since  1.0.0
	 */
	public function render_field_icons_custom_color() {

		// Get current options
		$options          = get_option( 'tss_options' );
		$use_custom_color = $options['use_custom_color'];
		$custom_color     = $options['icons_custom_color'];
		$custom_bg_color  = $options['icons_custom_background_color'];

		// Show color picker only when option is selected
		$style = ! $use_custom_color ? ' style="display: none;"' : '';

		?>
		<fieldset id="tss-color-picker-wrapper"<?php echo $style; ?>>
			<div>
				<label><?php echo __( 'Icons Color:', TSS_SLUG ); ?></label><input type="text" id="tss-color-picker" name="tss_options[icons_custom_color]" value="<?php echo $custom_color; ?>" />
			</div>
			<div>
				<label><?php echo __( 'Background Color:', TSS_SLUG ); ?></label><input type="text" id="tss-bg-color-picker" name="tss_options[icons_custom_background_color]" value="<?php echo $custom_bg_color; ?>" />
			</div>
		</fieldset>
		<?php
	}

	/**
	 * Render field for Icons positions
	 *
	 * @since  1.0.0
	 */
	public function render_field_icons_position() {

		// Get current options
		$options = get_option( 'tss_options' );
		$current_positions = $options['icons_position'];

		?>
		<fieldset>
			<label><input type="checkbox" name="tss_options[icons_position][below_title]" value="1" <?php checked( 1, $current_positions['below_title'], true ); ?>><?php echo __( 'Display social share icons below the title', TSS_SLUG ); ?></label><br>
			<label><input type="checkbox" name="tss_options[icons_position][floating_left]" value="1" <?php checked( 1, $current_positions['floating_left'], true ); ?>><?php echo __( 'Display social share icons floating on the left of the page', TSS_SLUG ); ?></label><br>
			<label><input type="checkbox" name="tss_options[icons_position][after_content]" value="1" <?php checked( 1, $current_positions['after_content'], true ); ?>><?php echo __( 'Display social share icons after the content', TSS_SLUG ); ?></label><br>
			<label><input type="checkbox" name="tss_options[icons_position][featured_image]" value="1" <?php checked( 1, $current_positions['featured_image'], true ); ?>><?php echo __( 'Display social share icons inside featured image', TSS_SLUG ); ?></label>
		</fieldset>
		<?php
		}

	/**
	 * Render hidden field to store Networks order
	 *
	 * @since  1.0.0
	 */
	public function render_field_ordered_networks() {
		?>
		<input type="hidden" name="tss_options[ordered_networks]" id="tss-ordered-networks">
		<?php
	}

	/**
	 * Load hooks
	 *
	 * @since  1.0.0
	 */
	public function load_hooks() {

		// Bail when browsing WP admin area
		if ( is_admin() ) {
			return;
		}

		// Get current settings
		$options    = get_option( 'tss_options' );
		$post_types = $options['post_types'];
		$networks   = $options['activated_networks'];

		// Bail if no network is active or no post type is selected
		if ( ! $networks || ! $post_types || ( ! is_single() && ! is_page() ) || is_front_page() ) {
			return;
		}

		global $post;

		// Get icons position
		$position = $options['icons_position'];

		// Get current post type
		$post_type = get_post_type( $post->ID );

		// Add hook to display share icons below title
		if ( $position['below_title'] && array_key_exists( $post_type, $post_types ) ) {
			add_filter( 'the_title', array( $this, 'render_buttons_below_title' ), 10, 2 );
		}

		// Add hook to display share icons after content
		if ( $position['after_content'] && array_key_exists( $post_type, $post_types ) ) {
			add_filter( 'the_content', array( $this, 'render_buttons_after_content' ), 10, 1 );
		}

		// Add hook to display share icons inside featured image
		if ( isset( $position['featured_image'] ) && $position['featured_image'] && array_key_exists( $post_type, $post_types ) ) {
			add_filter( 'post_thumbnail_html', array( $this, 'render_buttons_featured_image' ), 10, 1 );
		}

		// Float share bar left side
		if ( isset( $position['floating_left'] ) && $position['floating_left'] && array_key_exists( $post_type, $post_types ) ) {
			add_action( 'wp_head', array( $this, 'render_buttons_floating_left' ) );
		}
	}

	/**
	 * Our main function
	 *
	 * @since  1.0.0
	 */
	public function render_buttons( $extra_classes = '' ) {

		// Get current settings
		$options          = get_option( 'tss_options' );
		$networks         = $options['activated_networks'];
		$icons_size       = $options['icons_size'];
		$use_custom_color = $options['use_custom_color'];
		$custom_color     = $options['icons_custom_color'];
		$custom_bg_color  = $options['icons_custom_background_color'];

		// Bail if no network is active
		if ( ! $networks ) {
			return;
		}

		if ( is_front_page() && ! in_the_loop() ) {

			$permalink = get_home_url();
			$title = get_bloginfo( 'name' );

		} elseif ( is_home() && ! in_the_loop() ) {

			$permalink = get_permalink( get_option( 'page_for_posts' ) ); //get_home_url();
			$title = get_bloginfo( 'name' );

		} elseif ( ! in_the_loop() && ! is_single() ) {

			global $wp;
			$permalink = home_url( add_query_arg( array(), $wp->request ) );
			$title = get_bloginfo( 'name' ) . wp_title( null, false );

		} else {

			global $post;
			$permalink = get_the_permalink( $post->ID );
			$title = $post->post_title;
		}

		// Share buttons style
		$style_classes = $icons_size . ' ';

		// Custom color
		if ( $use_custom_color ) {
			$custom_color_style = 'background-color: ' . $custom_bg_color . '; color: ' . $custom_color;
			$style_classes .= 'custom-colors';
		}

		ob_start();
		?>
		<div class="tss-share-buttons <?php echo esc_attr( $extra_classes ); ?> <?php echo esc_attr( $style_classes ); ?>">
			<?php foreach ( $networks as $network => $value ) : ?>
				<?php
				// Init variables
				$share_link = '';
				$extra_attr = '';

				if ( $network == 'Facebook' ) {

					// https://developers.facebook.com/docs/plugins/share-button/#example
					$share_link = sprintf(
						TSS_FACEBOOK_URL . '?u=%s&t=%s',
						urlencode( $permalink ),
						urlencode( $title )
					);

				} elseif ( $network == 'Twitter' ) {

					// https://dev.twitter.com/web/tweet-button
					$share_link = sprintf(
						TSS_TWITTER_URL . '?url=%s&text=%s',
						urlencode( $permalink ),
						urlencode( $title )
					);

				} elseif ( $network == 'Google+' ) {

					// https://developers.google.com/+/web/share/
					$share_link = sprintf(
						TSS_GOOGLEPLUS_URL . '?url=%s',
						urlencode( $permalink )
					);

				} elseif ( $network == 'Pinterest' ) {

					// https://developers.pinterest.com/docs/widgets/save/?
					$share_link = TSS_PINTEREST_URL;
					$extra_attr = 'data-pin-do="buttonBookmark" data-pin-custom="true"';

				} elseif ( $network == 'LinkedIn' ) {

					// https://developer.linkedin.com/docs/share-on-linkedin
					$share_link = sprintf(
						TSS_LINKEDIN_URL . '?url=%s&mini=true&title=%s&summary=',
						urlencode( $permalink ),
						urlencode( $title )
					);

				} elseif ( $network == 'WhatsApp' && wp_is_mobile() ) {

					// http://stackoverflow.com/questions/21935149/sharing-link-on-whatsapp-from-mobile-website-not-application-for-android
					$share_link = sprintf(
						TSS_WHATSAPP_URL . '?text=%s',
						urlencode( $permalink )
					);
					$extra_attr = 'data-action="'. esc_attr( 'share/whatsapp/share' ) . '"';
				} else {
					continue;
				}

				?>
				<a href="<?php echo $share_link; ?>" <?php echo $extra_attr; ?> rel="nofollow" class="share-button <?php echo esc_attr( $this->button_classes[$network] ); ?>" style="<?php if ( isset( $custom_color_style ) ) echo esc_attr( $custom_color_style ); ?>">
					<i class="<?php echo esc_attr( $this->icon_classes[$network] ); ?>"></i>
				</a>
			<?php endforeach; ?>
		</div>
		<?php

		return ob_get_clean();
	}

	/**
	 * The shortcode
	 *
	 * @since   1.0.0
	 */
	public function tss_shortcode() {

		return $this->render_buttons();
	}

	/**
	 * Render buttons below title
	 *
	 * @since   1.0.0
	 */
	public function render_buttons_below_title( $title, $id ) {

		$backtrace = debug_backtrace();

		// Do not add buttons when filters are called from some known functions
		foreach ( $backtrace as $bt ) {
			if( $bt['function'] == 'comments_template' ||
				$bt['function'] == 'the_post_navigation' ||
				$bt['function'] == 'edit_post_link' ||
				$bt['function'] == 'get_edit_post_link' ||
				$bt['function'] == 'the_title_attribute' ||
				$bt['function'] == 'twentyseventeen_edit_link' ) {
				return $title;
			}
		}

		if ( in_the_loop() && is_main_query() && ! empty( $title ) ) {
			$buttons = $this->render_buttons();
			$title = $title . $buttons;
		}

		return $title;
	}

	/**
	 * Render buttons after content
	 *
	 * @since   1.0.0
	 */
	public function render_buttons_after_content( $content ) {

		$buttons = $this->render_buttons();
		$content = $content . $buttons;

		return $content;
	}

	/**
	 * Render buttons inside featured image
	 *
	 * @since   1.0.0
	 */
	public function render_buttons_featured_image( $html ) {

		if ( ! empty( $html ) ) {
			$buttons = $this->render_buttons();
			$html = '<div class="tss-featured-image-container">' . $html . '<span class="tss-featured-image-toggle">' . __( 'Share', TSS_SLUG ) . '</span>' . $buttons . '</div>';
		}

		return $html;
	}

	/**
	 * Render buttons floating left
	 *
	 * @since   1.0.0
	 */
	public function render_buttons_floating_left() {

		echo $this->render_buttons( 'floating-left' );
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
			'post' => __( 'Posts', TSS_SLUG ),
			'page' => __( 'Pages', TSS_SLUG ),
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
	 * Set default values on activation.
	 *
	 * @since  1.0.0
	 */
	static function set_default_values() {

		$options = get_option( 'tss_options' );

		if ( ! $options ) {

			// Set default values
			$post_types = array(
				'post' => 1
			);

			$activated_networks = array(
				'Facebook'  => 1,
	            'Twitter'   => 1,
	            'Google+'   => 1,
	            'Pinterest' => 1,
	            'LinkedIn'  => 1,
	            'WhatsApp'  => 1
			);

			$ordered_networks = 'Facebook,Twitter,Google+,Pinterest,LinkedIn,WhatsApp';

			$icons_size = 'small';

			$use_custom_color = 0;

			$custom_color    = '#ccc';
			$custom_bg_color = '#565656';

			$icons_position = array(
				'below_title'    => 1,
				'after_content'  => 1
			);

			// Update options table
			update_option(
				'tss_options',
				array(
					'post_types'                    => $post_types,
					'activated_networks'            => $activated_networks,
					'ordered_networks'              => $ordered_networks,
					'icons_size'                    => $icons_size,
					'use_custom_color'              => $use_custom_color,
					'icons_custom_color'            => $custom_color,
					'icons_custom_background_color' => $custom_bg_color,
					'icons_position'                => $icons_position
				)
			);
		}
	}

	/**
	 * Enqueue scripts
	 *
	 * @since  1.0.0
	 */
	public function enqueue_scripts() {

		// Enqueue front-end styles and scripts
		wp_enqueue_style(
			'tss-css',
			TSS_URL . '/css/style.css'
		);

		wp_enqueue_style(
			'tss-icons',
			TSS_URL . '/css/icons.css'
		);

		wp_enqueue_script(
			'tss-js',
			TSS_URL . '/js/front-end.js',
			array( 'jquery' )
		);

		wp_enqueue_script(
			'tss-pinterest-js',
			'//assets.pinterest.com/js/pinit.js',
			array(),
			null,
			true
		);
	}

	/**
	 * Enqueue admin scripts
	 *
	 * @since  1.0.0
	 */
	public function enqueue_scripts_admin( $hook ) {

		// Only enqueue on our settings page
		if ( 'settings_page_' . TSS_SLUG == $hook ) {

			wp_enqueue_style( 'wp-color-picker' );

			wp_enqueue_style(
				'tss-css',
				TSS_URL . '/css/admin.css'
			);

			wp_enqueue_style(
				'tss-icons-admin',
				TSS_URL . '/css/icons.css'
			);

			wp_enqueue_script(
				'tss-admin-js',
				TSS_URL . '/js/admin.js',
				array(
					'jquery',
					'jquery-ui-sortable',
					'wp-color-picker'
				)
			);
		}
	}

	/**
	 * Add an action link to the settings page.
	 *
	 * @since  1.0.0
	 */
	public function plugins_page_action_links( $links ) {

		$links[] = '<a href="'. get_admin_url( null, 'options-general.php?page=' . TSS_SLUG ) .'">' . __( 'Settings', TSS_SLUG ) . '</a>';

		return $links;
	}

}