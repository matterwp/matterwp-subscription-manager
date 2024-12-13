<?php

namespace MatterWP\WPSubscriptionManager;

class SubscriptionManagerFactory {
	/**
	 * Default configuration settings
	 *
	 * @var array
	 */
	private static $default_settings = array(
		'version' => '1.0.0',
	);

	/**
	 * Required settings keys
	 *
	 * @var array
	 */
	private static $required_settings = array(
		'plugin_slug',
		'plugin_name',
	);

	/**
	 * Create a new SubscriptionManager instance
	 *
	 * @param array $settings Configuration settings
	 * @return SubscriptionManager
	 * @throws \InvalidArgumentException If required settings are missing
	 */
	public static function create( array $settings ): SubscriptionManager {
		self::validateRequiredSettings( $settings );
		$settings = self::prepareSettings( $settings );

		$manager = new SubscriptionManager( $settings );
		return $manager;
	}

	/**
	 * Validate that all required settings are present
	 *
	 * @param array $settings
	 * @throws \InvalidArgumentException
	 */
	private static function validateRequiredSettings( array $settings ): void {
		foreach ( self::$required_settings as $required ) {
			if ( ! isset( $settings[ $required ] ) ) {
				throw new \InvalidArgumentException(
					sprintf( 'Missing required setting: %s', sanitize_text_field( $required ) )
				);
			}
		}
	}

	/**
	 * Prepare final settings by merging defaults and computing dynamic values
	 *
	 * @param array $settings
	 * @return array
	 */
	private static function prepareSettings( array $settings ): array {
		$settings['after_subscription_url'] = self::getAfterSubscriptionUrl( $settings );

		return wp_parse_args( $settings, self::$default_settings );
	}

	/**
	 * Get the after subscription URL
	 *
	 * @param array $settings
	 * @return string
	 */
	private static function getAfterSubscriptionUrl( array $settings ): string {
		if ( isset( $settings['after_subscription_url'] ) ) {
			return $settings['after_subscription_url'];
		}

		return admin_url( 'options-general.php?page=' . sanitize_key( $settings['plugin_slug'] ) );
	}
}
