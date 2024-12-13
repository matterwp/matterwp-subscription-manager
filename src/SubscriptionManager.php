<?php

namespace MatterWP\WPSubscriptionManager;

/**
 * Main plugin manager class
 * Coordinates between different components and handles core functionality
 */
class SubscriptionManager {
	/**
	 * Configuration instance
	 *
	 * @var Config
	 */
	private $config;

	/**
	 * Welcome page instance
	 *
	 * @var WelcomePage
	 */
	private $welcome_page;

	/**
	 * Initializes the subscription manager with settings
	 *
	 * @param array $settings Plugin configuration settings
	 */
	public function __construct( array $settings ) {
		$this->config       = new Config( $settings );
		$this->welcome_page = new WelcomePage( $this->config );

		$this->setup_hooks();
	}

	/**
	 * Sets up WordPress action hooks and initializes plugin functionality
	 *
	 * @return void
	 */
	private function setup_hooks() {
		add_action( 'admin_menu', array( $this->welcome_page, 'register' ) );
		add_action( 'admin_init', array( $this, 'handle_redirect' ), 5 );
		add_action( 'wp_ajax_get_diagnosis', array( 'MatterWP\WPSubscriptionManager\Diagnosis', 'get_system_info_ajax' ) );
		$this->maybe_add_welcome_option();
	}

	/**
	 * Creates the welcome page option if it doesn't exist
	 * Used to determine if the welcome page should be shown
	 *
	 * @return void
	 */
	private function maybe_add_welcome_option() {
		$option_name = $this->config->get( 'plugin_slug' ) . '_show_welcome';
		if ( ! get_option( $option_name ) ) {
			add_option( $option_name, 'yes' );
		}
	}

	/**
	 * Handles redirection to welcome page on first plugin activation
	 * Checks user capabilities and welcome page display status
	 *
	 * @return void
	 */
	public function handle_redirect() {
		if ( ! current_user_can( $this->config->get( 'capability' ) ) ) {
			return;
		}

		$option_name     = $this->config->get( 'plugin_slug' ) . '_show_welcome';
		$should_redirect = get_option( $option_name, false );

		if ( $should_redirect === 'yes' ) {
			update_option( $option_name, 'no' );
			wp_safe_redirect( admin_url( 'admin.php?page=' . $this->config->get( 'plugin_slug' ) . '-welcome' ) );
			exit;
		}
	}

	/**
	 * Returns the URL for the welcome page
	 *
	 * @return string Welcome page URL
	 */
	public function get_welcome_url() {
		return admin_url( 'admin.php?page=' . $this->config->get( 'plugin_slug' ) . '-welcome' );
	}
}
