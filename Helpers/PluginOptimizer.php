<?php

namespace My\Optimized\Helpers;

use My\Optimized\Configurators\EWWW_Configurator;
use My\Optimized\Configurators\W3TC_Configurator;

use My\Optimized\Configurators\ConfiguratorInterface;
use My\Optimized\Configurators\Wordfence_Configurator;
use My\Optimized\Helpers\PluginHelper;
use My\Optimized\Helpers\Logger;

/**
 * Class PluginOptimizer
 *
 * @package My\Optimized\Helpers
 * @author  Chris Butcher <c.butcher@hotmail.com>
 * @version 0.1.0
 */
class PluginOptimizer {

	/**
	 * List of all the available configurators.
	 *
	 * @var ConfiguratorInterface[]
	 */
	protected $configurators;

	/**
	 * Helper that gives us access to plugin information.
	 *
	 * @var \My\Optimized\Helpers\PluginHelper
	 */
	protected $plugins;

	/**
	 * Helper that allows us to log messages.
	 *
	 * @var \My\Optimized\Helpers\Logger
	 */
	protected $logger;

	/**
	 * Singleton instance of the plugin optimizer.
	 *
	 * @var PluginOptimizer
	 */
	protected static $instance;

	/**
	 * Returns a singleton instance of the plugin optimizer.
	 *
	 * @return PluginOptimizer
	 */
	public static function &getInstance() {
		if ( ! self::$instance instanceof self ) {
			$plugins = PluginHelper::getInstance();
			$logger  = Logger::getInstance();

			self::$instance = new self( $plugins, $logger );
		}

		return self::$instance;
	}

	/**
	 * PluginOptimizer constructor.
	 *
	 * @param PluginHelper $plugins Helper that gives us access to plugin information.
	 * @param Logger       $logger  Helper that allows us to log messages
	 */
	public function __construct( PluginHelper $plugins, Logger $logger ) {
		$this->plugins = $plugins;
		$this->logger  = $logger;
	}

	/**
	 * Returns the plugin information for this configurator.
	 *
	 * @param string $name The name of the configurator that you are looking for.
	 *
	 * @return ConfiguratorInterface|mixed|null
	 */
	public function getConfigurator( $name ) {
		if ( ! is_array( $this->configurators ) ) {
			$this->configurators = $this->getConfigurators();
		}

		if ( ! $this->hasConfigurator( $name ) ) {
			return null;
		}

		return $this->configurators[ $name ];
	}

	/**
	 * Check to see if a configurator exists.
	 *
	 * @param string $name The name of the configurator that you are looking for.
	 *
	 * @return bool
	 */
	public function hasConfigurator( $name ) {
		if ( ! is_array( $this->configurators ) ) {
			$this->configurators = $this->getConfigurators();
		}

		return isset( $this->configurators[ $name ] );
	}

	/**
	 * Fetches all of the plugin configurators.
	 *
	 * @return ConfiguratorInterface[]
	 */
	public function getConfigurators() {
		if ( ! is_array( $this->configurators ) || count( $this->configurators ) < 1 ) {
			$defaults = $this->getDefaultConfigurators();

			$this->configurators = apply_filters( MY_OPTIMIZED_FILTER_CONFIGURATORS, $defaults );
			if ( ! is_array( $this->configurators ) || count( $this->configurators ) < 1 ) {
				$this->configurators = $defaults;
			}
		}

		return $this->configurators;
	}

	/**
	 * Fetches all of the built-in configurators.
	 *
	 * @return ConfiguratorInterface[]
	 */
	protected function getDefaultConfigurators() {
		$configurators = array();

		if ( $this->plugins->isPluginActive( 'W3 Total Cache' ) ) {
			$configurators['W3 Total Cache'] = new W3TC_Configurator( new \W3TC\Config() );
		}

		if ( $this->plugins->isPluginActive( 'Wordfence Security' ) ) {
			$configurators['Wordfence Security'] = new Wordfence_Configurator();
		}

		if ( $this->plugins->isPluginActive( 'EWWW Image Optimizer' ) ) {
			$configurators['EWWW Image Optimizer'] = new EWWW_Configurator();
		}

		return $configurators;
	}
}
