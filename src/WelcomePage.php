<?php

namespace MatterWP\WPSubscriptionManager;

use MatterWP\WPSubscriptionManager\Diagnosis;

/**
 * Handles the welcome page functionality
 * Manages display and rendering of the plugin's welcome screen
 */
class WelcomePage {
	/**
	 * Configuration instance
	 *
	 * @var Config
	 */
	private $config;

	/**
	 * Branding settings for the welcome page
	 *
	 * @var array
	 */
	private $branding;

	/**
	 * Initializes the welcome page with configuration
	 *
	 * @param Config $config Configuration instance
	 */
	public function __construct( Config $config ) {
		$this->config   = $config;
		$this->branding = $this->setup_branding();
	}

	/**
	 * Registers the welcome page in WordPress admin menu
	 * Creates a hidden submenu page accessible via direct URL
	 *
	 * @return void
	 */
	public function register() {
		add_submenu_page(
			null,
			esc_html( $this->config->get( 'plugin_name' ) ) . ' Welcome',
			esc_html( $this->config->get( 'plugin_name' ) ) . ' Welcome',
			$this->config->get( 'capability' ),
			$this->config->get( 'plugin_slug' ) . '-welcome',
			array( $this, 'render' )
		);
	}

	/**
	 * Renders the welcome page content
	 * Checks user capabilities and includes the template file
	 *
	 * @return void
	 */
	public function render() {
		if ( ! current_user_can( $this->config->get( 'capability' ) ) ) {
			wp_die( esc_html__( 'Sorry, you are not allowed to access this page.', 'wp-subscription-manager' ) );
		}

		$template_data = array(
			'current_user'           => wp_get_current_user(),
			'plugin_slug'            => $this->config->get( 'plugin_slug' ),
			'plugin_name'            => $this->config->get( 'plugin_name' ),
			'after_subscription_url' => $this->config->get( 'after_subscription_url' ),
			'branding'               => $this->branding,
			'config'                 => $this->config,
			'system_info'            => Diagnosis::get_system_info(),
		);

		extract( $template_data );
		include $this->config->get( 'template_path' ) . 'welcome-page.php';
	}

	/**
	 * Sets up branding configuration for the welcome page
	 * Merges default branding with custom settings if available
	 *
	 * @return array Complete branding configuration
	 */
	private function setup_branding() {
		$default_branding = array(
			'logo'                => plugin_dir_url( dirname( __FILE__ ) ) . 'assets/images/mt-icon.svg',
			'logo_width'          => '56px',
			'heading'             => '',
			'description'         => '',
			'button_color'        => '#b45309',
			'button_hover_color'  => '#92400e',
			'privacy_url'         => 'https://matterwp.com/privacy-policy/',
			'terms_url'           => 'https://matterwp.com/terms-of-service/',
			'button_text'         => 'Proceed & Go to Settings',
			'button_loading_text' => 'Processing...',
			'button_success_text' => 'Subscribed successfully',
		);

		$settings = $this->config->get_settings();
		return isset( $settings['branding'] ) ? array_merge( $default_branding, $settings['branding'] ) : $default_branding;
	}
}
