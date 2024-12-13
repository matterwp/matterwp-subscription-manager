<?php

namespace MatterWP\WPSubscriptionManager;

/**
 * Configuration class for WP Subscription Manager
 * Handles initialization and storage of plugin settings
 */
class Config {
	/**
	 * Stores all plugin settings
	 *
	 * @var array
	 */
	private $settings;

	/**
	 * Unique identifier for the plugin
	 *
	 * @var string
	 */
	private $plugin_slug;

	/**
	 * Display name of the plugin
	 *
	 * @var string
	 */
	private $plugin_name;

	/**
	 * Plugin version number
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Redirect URL after successful subscription
	 *
	 * @var string
	 */
	private $after_subscription_url;

	/**
	 * Path to plugin template files
	 *
	 * @var string
	 */
	private $template_path;

	/**
	 * Required WordPress capability to manage plugin
	 *
	 * @var string
	 */
	private $capability = 'manage_options';

	/**
	 * Constructor - initializes plugin configuration
	 *
	 * @param array $settings Array of plugin settings
	 */
	public function __construct( array $settings ) {
		$this->settings = $settings;
		$this->initialize( $settings );
	}

	/**
	 * Initializes plugin properties with sanitized settings
	 *
	 * @param array $settings Array of plugin settings
	 */
	private function initialize( array $settings ) {
		$this->plugin_slug            = sanitize_key( $settings['plugin_slug'] );
		$this->plugin_name            = sanitize_text_field( $settings['plugin_name'] );
		$this->version                = $settings['version'];
		$this->after_subscription_url = $settings['after_subscription_url'];
		$this->template_path          = dirname( __DIR__ ) . '/templates/';
	}

	/**
	 * Retrieves a specific configuration value
	 *
	 * @param string $key Configuration key to retrieve
	 * @return mixed|null Configuration value or null if not found
	 */
	public function get( $key ) {
		return $this->$key ?? null;
	}

	/**
	 * Returns all plugin settings
	 *
	 * @return array Complete array of plugin settings
	 */
	public function get_settings() {
		return $this->settings;
	}
}
