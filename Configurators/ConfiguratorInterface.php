<?php

namespace My\Optimized\Configurators;

/**
 * Interface ConfiguratorInterface
 *
 * This interface provides a contract for the configuration of other plugins.
 */
interface ConfiguratorInterface {
	/**
	 * The configurator interface
	 *
	 * @param mixed|null $config The configuration values for the plugin.
	 */
	public function __construct( $config = null );

	/**
	 * This is the most difficult part of our plugin. It basically detects the best settings
	 * for a third party plugin based on the server settings and other installed plugins.
	 *
	 * The arguments that this method receives can vary, but are usually the installed PHP Extensions,
	 * Apache Modules, WordPress Plugins, etc. It needs to return an array with the setting name as
	 * the array key, and the description of the optimization as a value.
	 *
	 * Return Example: array( 'maximum_favicon_size' => 'The maximum favicon size should be no bigger than 64px' );
	 *
	 * @param array $arguments
	 *
	 * @return string[]
	 */
	public function getSuggestions( array $arguments = array() );

	/**
	 * This is where we are actually changing the third party plugin. Basically the user has decided
	 * that their website can work better, and it's up to us to make it better. We do that by accepting
	 * arguments which tell us what to do.
	 *
	 * If the arguments tell us to optimize the 'maximum_favicon_size', then we do it here. If they tell
	 * us to 'minimize_our_javascript', then that's what we do.
	 *
	 * The argument keys should tell us what to optimize. If the key exists, then we need to optimize it,
	 * and if the key doesn't exist, then we can skip it.
	 *
	 * @param array $arguments Arguments that are required to optimize the plugin.
	 *
	 * @return boolean
	 */
	public function configure( array $arguments = array() );

	/**
	 * This is nothing more than a wrapper method. It returns an array of values from the plugin that
	 * you are configuring.
	 *
	 * @return mixed[]
	 */
	public function getConfiguration();

	/**
	 * Set multiple configuration values at once.
	 *
	 * @param array $configuration A list of all the configuration values you want to set.
	 *
	 * @return mixed
	 */
	public function setConfiguration( array $configuration );

	/**
	 * Sets a single configuration value.
	 *
	 * @param string $name The name of the configuration you are setting.
	 * @param mixed $value The new value of the configuration setting.
	 *
	 * @return mixed
	 */
	public function set( $name, $value );

	/**
	 * Returns a configuration value.
	 *
	 * @param string $name The name of the configuration value you are getting.
	 * @param mixed $default The default value to return if the configuration value is not found.
	 *
	 * @return mixed
	 */
	public function get( $name, $default = null );

	/**
	 * Check to see if a configuration value exists.
	 *
	 * @param string $name The name of the configuration value you are checking.
	 *
	 * @return boolean
	 */
	public function has( $name );

	/**
	 * Saves the configuration file.
	 *
	 * @return bool
	 */
	public function save();
}
