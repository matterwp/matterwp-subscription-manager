<?php

namespace MatterWP\WPSubscriptionManager;

/**
 * Class Diagnosis
 * Handles system information gathering and diagnostics for WordPress installations
 */
class Diagnosis {
	/**
	 * SubscriptionManager instance
	 *
	 * @var SubscriptionManager
	 */
	private static $manager;

	/**
	 * Set the SubscriptionManager instance
	 *
	 * @param SubscriptionManager $manager
	 */
	public static function set_manager( SubscriptionManager $manager ) {
		self::$manager = $manager;
	}

	/**
	 * Retrieves comprehensive system information about the WordPress installation
	 *
	 * @return array Filtered array of system information
	 */
	public static function get_system_info() {
		$data = array(
			'plugin_slug'        => self::safely_run( 'get_plugin_slug' ),
			'email'              => self::safely_run( 'get_admin_email' ),
			'website_url'        => self::safely_run( 'get_site_url' ),
			'username'           => self::safely_run( 'get_admin_username' ),
			'hosting'            => self::safely_run( 'get_hosting_info' ),
			'active_theme'       => self::safely_run( 'get_active_theme' ),
			'all_plugins'        => self::safely_run( 'get_all_plugins' ),
			'active_plugins'     => self::safely_run( 'get_active_plugins' ),
			'total_users'        => self::safely_run( 'get_total_users' ),
			'total_posts'        => self::safely_run( 'get_total_posts' ),
			'total_pages'        => self::safely_run( 'get_total_pages' ),
			'is_store'           => self::safely_run( 'is_store' ) ?? '0',
			'php_version'        => PHP_VERSION ?? 'unknown',
			'php_execution_time' => ini_get( 'max_execution_time' ) ?? 'unknown',
			'php_memory_limit'   => ini_get( 'memory_limit' ) ?? 'unknown',
			'upload_max_size'    => ini_get( 'upload_max_filesize' ) ?? 'unknown',
			'post_max_size'      => ini_get( 'post_max_size' ) ?? 'unknown',
		);

		return array_filter(
			$data,
			function( $value ) {
				return $value !== null;
			}
		);
	}

	/**
	 * Safely executes a class method with error handling
	 *
	 * @param string $method Method name to be called
	 * @return mixed|string Returns method result or '0' on failure
	 */
	private static function safely_run( $method ) {
		try {
			return call_user_func( array( __CLASS__, $method ) ) ?? null;
		} catch ( \Exception $e ) {
			return '0';
		}
	}

	/**
	 * Retrieves the admin email address
	 *
	 * @return string Sanitized admin email or empty string on failure
	 */
	private static function get_admin_email() {
		try {
			return sanitize_email( get_option( 'admin_email' ) );
		} catch ( \Exception $e ) {
			return '';
		}
	}

	/**
	 * Retrieves the site URL
	 *
	 * @return string Sanitized site URL or empty string on failure
	 */
	private static function get_site_url() {
		try {
			return esc_url_raw( get_site_url() );
		} catch ( \Exception $e ) {
			return '';
		}
	}

	/**
	 * Retrieves the admin username
	 *
	 * @return string Sanitized admin username or empty string on failure
	 */
	private static function get_admin_username() {
		try {
			$current_user = wp_get_current_user();
			return $current_user instanceof \WP_User ? sanitize_text_field( $current_user->user_login ) : '';
		} catch ( \Exception $e ) {
			return '';
		}
	}

	/**
	 * Detects and returns hosting provider information
	 *
	 * @return string Hosting provider name or server type
	 */
	private static function get_hosting_info() {
		try {
			// Check managed WordPress hosts
			if ( defined( 'WPE_APIKEY' ) ) {
				return 'WP Engine';
			} elseif ( defined( 'KINSTA_VERSION' ) ) {
				return 'Kinsta';
			} elseif ( defined( 'FLYWHEEL_CONFIG_DIR' ) ) {
				return 'Flywheel';
			} elseif ( defined( 'PAGELY_ACCOUNT' ) ) {
				return 'Pagely';
			} elseif ( defined( 'PRESSABLE_API' ) || defined( 'IS_PRESSABLE' ) ) {
				return 'Pressable';
			} elseif ( defined( 'PANTHEON_ENVIRONMENT' ) ) {
				return 'Pantheon';
			} elseif ( defined( 'CLOUDWAYS_APPLICATION_PASSWORD' ) ) {
				return 'Cloudways';
			} elseif ( defined( 'GD_SYSTEM_PLUGIN_DIR' ) || defined( 'GD_TEMP_DIR' ) ) {
				return 'GoDaddy';
			} elseif ( defined( 'MM_BASE_DIR' ) ) {
				return 'Bluehost';
			} elseif ( defined( 'CONVESIO_VER' ) ) {
				return 'Convesio';
			} elseif ( defined( 'WPCOMSH_VERSION' ) ) {
				return 'WordPress.com';
			} elseif ( defined( 'GRIDPANE' ) ) {
				return 'GridPane';
			} elseif ( defined( 'SPINUPWP_CACHE_PATH' ) ) {
				return 'SpinupWP';
			} elseif ( defined( 'CLOSTE_APP_ID' ) ) {
				return 'Closte';
			} elseif ( defined( 'SG_OPTIMIZER_VERSION' ) ) {
				return 'SiteGround';
			} elseif ( defined( 'HOSTGATOR_PLUGIN_VERSION' ) ) {
				return 'HostGator';
			}

			// Check server software if no managed host detected
			$server_software = isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : '';

			if ( ! empty( $server_software ) ) {
				if ( stripos( $server_software, 'nginx' ) !== false ) {
					return 'Nginx Server';
				} elseif ( stripos( $server_software, 'apache' ) !== false ) {
					return 'Apache Server';
				} elseif ( stripos( $server_software, 'litespeed' ) !== false ) {
					return 'LiteSpeed Server';
				}
			}

			return 'Unknown';
		} catch ( \Exception $e ) {
			return 'Unknown';
		}
	}

	/**
	 * Retrieves active theme information
	 *
	 * @return string Theme name or 'Unknown' on failure
	 */
	private static function get_active_theme() {
		try {
			$theme = wp_get_theme();
			return $theme instanceof \WP_Theme ? $theme->get( 'Name' ) : 'Unknown';
		} catch ( \Exception $e ) {
			return 'Unknown';
		}
	}

	/**
	 * Retrieves information about all installed plugins
	 *
	 * @return array List of plugins with their versions
	 */
	private static function get_all_plugins() {
		try {
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			$plugins           = get_plugins();
			$formatted_plugins = array();

			if ( is_array( $plugins ) ) {
				foreach ( $plugins as $plugin_file => $plugin_data ) {
					$name    = isset( $plugin_data['Name'] ) ? sanitize_text_field( $plugin_data['Name'] ) : '';
					$version = isset( $plugin_data['Version'] ) ? sanitize_text_field( $plugin_data['Version'] ) : '';
					if ( $name && $version ) {
						$formatted_plugins[] = $name . ':' . $version;
					}
				}
			}

			return $formatted_plugins;
		} catch ( \Exception $e ) {
			return array();
		}
	}

	/**
	 * Retrieves information about active plugins
	 *
	 * @return array List of active plugins with their versions
	 */
	private static function get_active_plugins() {
		try {
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			$active_plugins    = get_option( 'active_plugins', array() );
			$all_plugins       = get_plugins();
			$formatted_plugins = array();

			if ( is_array( $active_plugins ) && is_array( $all_plugins ) ) {
				foreach ( $active_plugins as $plugin_file ) {
					if ( isset( $all_plugins[ $plugin_file ] ) ) {
						$name    = isset( $all_plugins[ $plugin_file ]['Name'] ) ? sanitize_text_field( $all_plugins[ $plugin_file ]['Name'] ) : '';
						$version = isset( $all_plugins[ $plugin_file ]['Version'] ) ? sanitize_text_field( $all_plugins[ $plugin_file ]['Version'] ) : '';
						if ( $name && $version ) {
							$formatted_plugins[] = $name . ':' . $version;
						}
					}
				}
			}

			return $formatted_plugins;
		} catch ( \Exception $e ) {
			return array();
		}
	}

	/**
	 * Retrieves total number of registered users
	 *
	 * @return int Total user count
	 */
	private static function get_total_users() {
		try {
			$users = count_users();
			return isset( $users['total_users'] ) ? (int) $users['total_users'] : 0;
		} catch ( \Exception $e ) {
			return 0;
		}
	}

	/**
	 * Retrieves total number of published posts
	 *
	 * @return int Total post count
	 */
	private static function get_total_posts() {
		try {
			$count_posts = wp_count_posts();
			return isset( $count_posts->publish ) ? (int) $count_posts->publish : 0;
		} catch ( \Exception $e ) {
			return 0;
		}
	}

	/**
	 * Retrieves total number of published pages
	 *
	 * @return int Total page count
	 */
	private static function get_total_pages() {
		try {
			$count_pages = wp_count_posts( 'page' );
			return isset( $count_pages->publish ) ? (int) $count_pages->publish : 0;
		} catch ( \Exception $e ) {
			return 0;
		}
	}

	/**
	 * Checks if the site is running an e-commerce solution
	 *
	 * @return int 1 if WooCommerce or SureCart is active, 0 otherwise
	 */
	private static function is_store() {
		try {
			if ( ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			return (
				is_plugin_active( 'woocommerce/woocommerce.php' ) ||
				is_plugin_active( 'surecart/surecart.php' )
			) ? 1 : 0;
		} catch ( \Exception $e ) {
			return 0;
		}
	}

	/**
	 * AJAX handler for retrieving system information
	 *
	 * @return void
	 */
	public static function get_system_info_ajax() {
		check_ajax_referer( 'get_diagnosis', 'nonce' );
		wp_send_json_success( self::get_system_info() );
	}

	/**
	 * Gets the plugin slug from the Config instance
	 *
	 * @return string Plugin slug or empty string on failure
	 */
	private static function get_plugin_slug() {
		if (!isset(self::$manager)) {
			return '';
		}

		try {
			$config = self::$manager->get_config();
			return $config ? sanitize_key($config->get('plugin_slug')) : '';
		} catch (\Exception $e) {
			return '';
		}
	}
}
