<?php
/*
   Plugin Name: My Optimizer
   Plugin URI: http://google.com
   Description: Configures a variety of plugins so they perform at their most optimal.
   Version: 0.1.0
   Author: MY Hosting
   Author URI: http://google.com
*/

/** Hey, are you trying to access our plugin without permission? */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MY_OPTIMIZED_VERSION', '0.1.0');
define( 'MY_OPTIMIZED_PATH', plugin_dir_path( __FILE__ ) );
define( 'MY_OPTIMIZED_URL', plugin_dir_url( __FILE__ ) );
define( 'MY_OPTIMIZED_VIEWS', MY_OPTIMIZED_PATH . 'Views/' );

define( 'MY_OPTIMIZED_FILTER_CONFIGURATORS', 'my_optimized_filter_configurators' );
define( 'MY_OPTIMIZED_FILTER_SUGGESTED_PLUGINS', 'my_optimized_filter_suggested_plugins' );
define( 'MY_OPTIMIZED_ACTION_PLUGIN_OPTIMIZED', 'my_optimized_action_plugin_optimized' );

/**
 * Class MY_Optimized
 */
class MY_Optimized {

	/**
	 * We need to add our own custom code to WordPress, and this is where we do it.
	 * Basically we add our Actions, Filters, and other logic that needs to be executed
	 * during the WordPress lifecycle.
	 *
	 * @see https://tommcfarlin.com/wordpress-page-lifecycle/
	 * @see https://codex.wordpress.org/Plugin_API/Action_Reference
	 */
	public function initialize() {

		if ( is_admin() ) {
			$AdminController = new \My\Optimized\Controllers\AdminController();
			add_action( 'admin_notices', array( $AdminController, 'DisplayNotices' ) );
			add_action( 'admin_menu', function () use ($AdminController) {
				add_menu_page( __( 'My Optimizer' ), __( 'My Optimizer' ), 'install_plugins', 'my_optimize', array( $AdminController, 'Introduction' ) );
				add_submenu_page( null, __( 'My Optimize' ), __( 'My Optimizer' ), 'install_plugins', 'my_optimize_plugin', array( $AdminController, 'OptimizePlugin' ) );
				add_submenu_page( null, __( 'My Optimize' ), __( 'My Optimizer' ), 'install_plugins', 'my_optimized_backups', array( $AdminController, 'Backups' ) );
			} );

			$this->upgrade();
		}
	}

	/**
	 * Sometimes a plugin needs to be upgraded, it requires adjustments to the database,
	 * adjusting files, or other changes. In order to detect whether an upgrade is needed,
	 * we cache the previous version number, and compare it against the current version number.
	 */
	protected function upgrade() {
		global $wpdb;

		$version = get_option( 'my_optimized_version', '0.0.0' );

		/**
		 * This function is executed every time the administration section is loaded.
		 * We need to limit the overhead cost, which is why we are checking that the
		 * current version and the stored version match. If they match, then we don't
		 * need to execute anything else. */
		if ( $version == MY_OPTIMIZED_VERSION ) {
			return;
		}

		/**
		 * If we have made it to this point, then this is the first time the plugin has
		 * been installed and we need to create the database table for storing the
		 * configuration backups of all the third-party plugins that we will be configuring. */
		if ( version_compare( $version, '0.1.0' ) == -1 ) {
			$success = $wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}my_config_backups` (
				`id` int unsigned auto_increment,
				`plugin` varchar(255) not null,
				`version` varchar(20) not null default '0.0.0',
				`datetime` datetime not null,
				`config` longtext,
				PRIMARY KEY(`id`)
			)");

			if ( $success === false ) {
				\My\Optimized\Helpers\Logger::getInstance()->error(
					__( "Unable to install the configuration backup database table.", 'a2-optimized' )
				);
			}
		}

		/** We just upgraded the plugin, and now we need to make sure that the system knows it */
		update_option( 'my_optimized_version', MY_OPTIMIZED_VERSION );
	}

	/**
	 * The user decided that they no longer need us. It happens. Basically we need to clean up
	 * our mess. That means that we need to get rid of all our database entries, files, etc.
	 *
	 * @Todo
	 *        How do we log information outside of the SPL Loader scope? We can't use our own Logger,
	 *        because it is outside of the namespace scope. So, should we do a wp_die() statement, or
	 *        perhaps we should write to a file?
	 *
	 *        Perhaps in the future we should require the Logger without using the SPL loader?
	 */
	public static function uninstall() {
		global $wpdb;

		if ( $wpdb->query("DROP TABLE `{$wpdb->prefix}my_config_backups`") === false ) {
			/** @Todo We need to log this problem. */
		}
	}

	/**
	 * Not sure if you're familiar with namespaces, but in order to 'use' namespaces, we need
	 * to register an SPL (Standard PHP Library) loader. This will tell PHP where to look for
	 * our classes.
	 *
	 * @param string $class The fully qualified namespace of the file that we are loading.
	 *
	 * @return void
	 */
	public function autoload( $class ) {
		$base = null;

		if ( substr( $class, 0, 1 ) == "\\" ) {
			$class = substr( $class, 1 );
		}

		if ( substr( $class, 0, 13 ) == 'My\\Optimized\\' ) {
			$filename = MY_OPTIMIZED_PATH . substr( $class, 13 ) . '.php';
			$filename = str_replace( '\\', DIRECTORY_SEPARATOR, $filename );

			if ( file_exists( $filename ) ) {
				require $filename;

			}
		}
	}
}

/**
 * Every WordPress plugin needs to register itself within WordPress. The best way to do that
 * is by using the Activate, Initialize, Deactivate, and Uninstall hooks. These hooks should
 * only be implemented once per plugin, which we are doing below, and they should not be
 * duplicated, which is why these hooks have been registered outside of the main classes scope.
 *
 * If you don't understand, then don't touch or move these around. */

$MY = new MY_Optimized();
spl_autoload_register( array( $MY, 'autoload' ) );
add_action( 'init', array( $MY, 'initialize' ) );
register_activation_hook( __FILE__, array( $MY, 'activate' ) );
register_uninstall_hook( __FILE__, 'MY_Optimized::uninstall' );
