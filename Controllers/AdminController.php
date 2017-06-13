<?php

namespace My\Optimized\Controllers;

use My\Optimized\Configurators\ConfiguratorInterface;
use My\Optimized\Helpers\Logger;
use My\Optimized\Helpers\RestoreService;
use My\Optimized\Helpers\PluginOptimizer;
use My\Optimized\Helpers\PluginHelper;
use My\Optimized\Helpers\View;
use My\Optimized\Models\PluginInfo;

/**
 * Class AdminController
 *
 * @package My\Optimized\Controllers
 * @author  Chris Butcher <c.butcher@hotmail.com>
 * @version 0.1.0
 */
class AdminController {
	/**
	 * Displays the introduction page.
	 *
	 * @return void
	 */
	public static function Introduction() {

		wp_enqueue_style( 'my_optimized_admin_css', MY_OPTIMIZED_URL . 'Resources/Styles/admin.css' );
		wp_enqueue_script( 'my_optimized_admin_js', MY_OPTIMIZED_URL . 'Resources/Scripts/admin.js', ['jquery'] );

		View::render( 'Introduction', array(
			'plugins'   => PluginHelper::getInstance(),
			'optimizer' => PluginOptimizer::getInstance(),
		) );
	}

	/**
	 * This action is responsible for displaying and executing the suggested
	 * optimizations of a specific plugin. In order to do that correctly, we
	 * need to detect the server and environment settings, then we need to
	 * find out the best way to optimize the plugin.
	 */
	public static function OptimizePlugin() {
		if ( ! isset( $_REQUEST['my_plugin'] ) ) {
			return null;
		}

		$plugin    = $_REQUEST['my_plugin'];
		$plugins   = PluginHelper::getInstance();
		$optimizer = PluginOptimizer::getInstance();

		/**
		 * We can only optimize plugins that we know about, so we need to find out
		 * whether the selected plugin can be optimized. */
		if ( ! $optimizer->hasConfigurator( $plugin ) ) {
			return self::Introduction();
		}

		/**
		 * Certain plugins are going to be recommended, and when those plugins don't
		 * exist, the we are going to have to ask the user to install them. */
		if ( ! $plugins->hasPlugin( $plugin ) ) {
			return self::Introduction();
		}

		/**
		 * The plugin is already installed but not activated?
		 * Obviously we need to ask the user to activate the plugin. */
		if ( ! $plugins->isPluginActive( $plugin ) ) {
			return self::Introduction();
		}

		$plugin       = $plugins->getPluginInformation( $_REQUEST['my_plugin'] );
		$configurator = $optimizer->getConfigurator( $plugin->name );
		$restore      = new RestoreService();

		/**
		 * There is a button called optimize_plugin, and when the user clicks that button,
		 * they are letting us know that they want to optimize a specific plugin. */
		if ( isset( $_REQUEST['optimize_plugin'] ) ) {

			/**
			 * When the user wants to optimize a plugin, they will send in a list of optimizations
			 * that they want us to make. Those optimizations are based off suggestions made by the
			 * ConfiguratorInterface
			 *
			 * @see ConfiguratorInterface::getSuggestions() */
			if ( isset( $_REQUEST['optimizations'] ) && count( $_REQUEST['optimizations'] ) > 0 ) {

				/**
				 * Before we can optimize the plugin, we need to first create a backup
				 * of the current configuration. This will allow us to restore the old
				 * settings in case something breaks. */
				if ( $restore->create( $plugin, $configurator ) ) {
					if ( $configurator->configure( $_POST['optimizations'] ) ) {
						Logger::getInstance()->success(
							__( 'Successfully configured the {plugin}.', 'a2-optimized' ),
							array( 'plugin' => $plugin->name )
						);
					}

					/**
					 * Sometimes we don't know everything. Sometimes other people know more
					 * than us. Which is why we are giving other people the option to change
					 * the optimized settings.
					 *
					 * @var PluginInfo $plugin
					 * @var ConfiguratorInterface $configurator
					 */
					do_action( MY_OPTIMIZED_ACTION_PLUGIN_OPTIMIZED, $plugin, $configurator );

				} else Logger::getInstance()->error( __( "Unable to create a backup of the {plugin} configuration.", 'a2-optimize' ), array(
					'plugin' => $plugin->name,
				));
			}
		}

		$configurators = $optimizer->getConfigurators();
		$extensions    = get_loaded_extensions();
		$apacheModules = function_exists( 'apache_get_modules' ) ? apache_get_modules() : array();

		/**
		 * This is the part of the plugin that detects the optimal settings. It basically
		 * looks at the current server settings along with other installed plugins, then
		 * gives us a list of the best solutions for optimizing the site. */
		$suggestions = $configurator->getSuggestions(array(
			'plugins'       => $plugins,
			'extensions'    => $extensions,
			'modules'       => $apacheModules,
			'configurators' => $configurators,
		));

		wp_enqueue_style( 'my_optimized_admin_css', MY_OPTIMIZED_URL . 'Resources/Styles/admin.css' );
		wp_enqueue_script( 'my_optimized_admin_js', MY_OPTIMIZED_URL . 'Resources/Scripts/admin.js', ['jquery'] );

		return View::render( 'OptimizePlugin', array(
			'plugin'       => $plugin,
			'plugins'      => $plugins,
			'optimizer'    => $optimizer,
			'suggestions'  => $suggestions,
			'lastRevision' => $restore->getLastRevision( $plugin->name )
		) );
	}

	/**
	 *
	 */
	public static function Backups() {
		if ( ! isset( $_REQUEST['my_plugin'] ) ) {
			return self::Introduction();
		}

		$restore   = new RestoreService();
		$plugins   = PluginHelper::getInstance();
		$optimizer = PluginOptimizer::getInstance();

		if ( ( $plugin = $plugins->getPluginInformation( $_REQUEST['my_plugin'] ) ) === false ) {
			return self::Introduction();
		}

		/**
		 * Normally the end-user wouldn't need to delete a revision, but if they executed an
		 * optimization by accident, they may decided they want to restore, and delete that
		 * revision. Which is why we have opted to create the ability to delete revisions. */
		if ( isset ($_REQUEST['delete_revision'] ) ) {
			if ( $revision = $restore->getRevision( $_REQUEST['delete_revision'] ) ) {
				if ( $restore->delete($revision) ) {
					Logger::getInstance()->success(
						__( 'Deleted the revision for {plugin} from {date} at {time}.' ),
						array(
							'plugin' => $plugin->name,
							'date'   => $revision->datetime->format( 'F jS Y' ),
							'time'   => $revision->datetime->format( 'h:ia' ),
						)
					);

				} else Logger::getInstance()->error(
					__( 'Unable to delete the revision for {plugin} from {date} at {time}.' ),
					array(
						'plugin' => $plugin->name,
						'date'   => $revision->datetime->format( 'F jS Y' ),
						'time'   => $revision->datetime->format( 'h:ia' ),
					)
				);
			}
		}

		/**
		 * The user has decided that something is wrong with their site, and that it may be with a
		 * configuration setting that we changed. So now they want to restore the previous configuration
		 * values from the database. */
		if ( isset( $_REQUEST['restore_revision'] ) ) {
			if ( ( $revision = $restore->getRevision( $_REQUEST['restore_revision'] ) ) !== null ) {
				$configurator = $optimizer->getConfigurator( $plugin->name );
				$configurator->setConfiguration($revision->config);

				if ( $configurator->save() ) {
					Logger::getInstance()->success(
						__( 'Successfully restored the configuration for {plugin} back to {date} at {time}', 'a2-optimized' ),
						array(
							'plugin' => $plugin->name,
							'date'   => $revision->datetime->format( 'F jS, Y' ),
							'time'   => $revision->datetime->format( 'h:ia' )
						)
					);
				} else {
					Logger::getInstance()->error(
						__( 'Failed to restore the configuration for {plugin} back to {date} at {time}', 'a2-optimized' ),
						array(
							'plugin' => $plugin->name,
							'date'   => $revision->datetime->format( 'F jS, Y' ),
							'time'   => $revision->datetime->format( 'h:ia' )
						)
					);
				}
			}
		}

		wp_enqueue_style( 'my_optimized_admin_css', MY_OPTIMIZED_URL . 'Resources/Styles/admin.css' );
		wp_enqueue_script( 'my_optimized_admin_js', MY_OPTIMIZED_URL . 'Resources/Scripts/admin.js', ['jquery'] );

		return View::render( 'Backups', array(
			'plugin'    => $plugin,
			'plugins'   => $plugins,
			'optimizer' => $optimizer,
			'revisions' => $restore->getRevisions( $plugin->name ),
		) );
	}

	/**
	 * This page will display any success, error, warning or notices on the
	 * administrator pages.
	 */
	public static function DisplayNotices() {
		$logger = Logger::getInstance();

		return View::render( 'Notices', array(
			'successes' => $logger->get( 'success' ),
			'errors'    => $logger->get( 'error' ),
			'warnings'  => $logger->get( 'warning' ),
			'notices'   => $logger->get( 'notice' ),
		) );
	}
}
