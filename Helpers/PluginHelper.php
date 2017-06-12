<?php

namespace My\Optimized\Helpers;

use My\Optimized\Models\PluginInfo;

/**
 * Class PluginHelper
 *
 * This class is responsible for checking whether plugins are available and if
 * their version is compatible with our plugin.
 */
class PluginHelper {

	const COMPARE_GREATER_THAN = '>';
	const COMPARE_GREATER_THAN_OR_EQUAL = '>=';
	const COMPARE_LESS_THAN = '<';
	const COMPARE_LESS_THAN_OR_EQUAL = '<=';
	const COMPARE_EQUALS = '=';

	/**
	 * List of all the suggested plugins that can help to optimize the site.
	 *
	 * @var array[]
	 */
	protected $suggestedPlugins = array(
		'W3 Total Cache' => array(
			'name'        => 'W3 Total Cache',
			'description' => '',
			'slug'        => 'w3-total-cache',
		),
		'Wordfence Security' => array(
			'name'        => 'Wordfence Security',
			'description' => '',
			'slug'        => 'wordfence',
		),
		'EWWW Image Optimizer' => array(
			'name'        => 'EWWW Image Optimizer',
			'description' => '',
			'slug'        => 'ewww-image-optimizer',
		),
	);

	/**
	 * List of all the plugins.
	 *
	 * @var PluginInfo[]
	 */
	protected $plugins;

	/**
	 * Singleton instance of the plugin helper.
	 *
	 * @var PluginHelper
	 */
	protected static $instance;

	/**
	 * Returns a singleton instance of the plugin helper.
	 *
	 * @return PluginHelper
	 */
	public static function &getInstance() {
		if ( ! self::$instance instanceof self ) {
			$plugins = array();
			foreach ( get_plugins() as $name => $info ) {
				$info['Active'] = is_plugin_active( $name );
				$plugins[ $info['Name'] ] = new PluginInfo( $info );
			}

			self::$instance = new self( $plugins );
		}

		return self::$instance;
	}

	/**
	 * PluginHelper constructor.
	 *
	 * @param PluginInfo[] $plugins The list of all the plugins (both active and inactive)
	 */
	public function __construct( array $plugins ) {
		foreach ( $plugins as $plugin ) {
			$this->plugins[ $plugin->name ] = $plugin;
		}
	}

	/**
	 * These are the plugins that we are suggesting that the end-user should use. They have been
	 * chosen because they have the best features and performance.
	 *
	 * @return string[]
	 */
	public function getSuggestedPlugins() {
		$suggestedPlugins = $this->suggestedPlugins;
		$suggestedPlugins = apply_filters( MY_OPTIMIZED_FILTER_SUGGESTED_PLUGINS, $suggestedPlugins );
		if ( ! is_array( $suggestedPlugins ) || count( $suggestedPlugins ) < 1 ) {
			$suggested = $this->suggestedPlugins;
		}

		$suggested = array();
		foreach ( $suggestedPlugins as $name => $description ) {
			if ( ! $this->hasPlugin( $name ) ) {
				$suggested[ $name ] = $description;
			}
		}

		return $suggested;
	}

	/**
	 * Returns information about a plugin.
	 *
	 * @param string $name The name of the plugin that you are looking for.
	 *
	 * @return PluginInfo|false
	 */
	public function getPluginInformation( $name ) {
		if ( ! $this->hasPlugin( $name ) ) {
			return false;
		}

		return $this->plugins[ $name ];
	}

	/**
	 * Check whether a plugin is active.
	 *
	 * @param string $name The name of the plugin that you are looking for.
	 *
	 * @return boolean
	 */
	public function isPluginActive( $name ) {
		if ( ! isset( $this->plugins[ $name ] ) ) {
			return false;
		}

		return $this->plugins[ $name ]->Active;
	}

	/**
	 * Returns a list of all the plugins and their info.
	 *
	 * @return PluginInfo[]
	 */
	public function getAll() {
		return $this->plugins;
	}

	/**
	 * Check whether a plugin is installed, regardless of whether it is active or not.
	 *
	 * @param string $name The name of the plugin that you are looking for.
	 *
	 * @return boolean
	 */
	public function hasPlugin( $name ) {
		return isset( $this->plugins[ $name ] );
	}

	/**
	 * Compare a plugins version to make sure that it meets requirements.
	 *
	 * @param string $name The name of the plugin
	 * @param string $expected The version number that you are expecting
	 * @param string $comparison Comparison operator that tells whether you are expecting
	 *                           the version to be greater than, less than, equal to, or
	 *                           a combination.
	 *
	 * @return boolean|null Returns a boolean when the two versions can be compared, null is returned
	 *                      if the plugin cannot be found or the comparison operator is invalid.
	 */
	public function compareVersion( $name, $expected, $comparison = self::COMPARE_GREATER_THAN_OR_EQUAL ) {
		$isGood = null;

		if ( ! $this->hasPlugin( $name ) ) {
			return null;
		}

		$availableComparisonOperators = array(
			self::COMPARE_GREATER_THAN,
			self::COMPARE_GREATER_THAN_OR_EQUAL,
			self::COMPARE_EQUALS,
			self::COMPARE_LESS_THAN_OR_EQUAL,
			self::COMPARE_LESS_THAN,
		);

		if ( in_array( $comparison, $availableComparisonOperators ) ) {
			$plugin = $this->getPluginInformation( $name );

			switch ( $comparison ) {
				case self::COMPARE_EQUALS:
					$isGood = version_compare( $plugin->version, $expected ) == 0;
					break;
				case self::COMPARE_LESS_THAN:
					$isGood = version_compare( $plugin->version, $expected ) < 0;
					break;
				case self::COMPARE_LESS_THAN_OR_EQUAL:
					$isGood = version_compare( $plugin->version, $expected ) <= 0;
					break;
				case self::COMPARE_GREATER_THAN:
					$isGood = version_compare( $plugin->version, $expected ) > 0;
					break;
				case self::COMPARE_GREATER_THAN_OR_EQUAL:
					$isGood = version_compare( $plugin->version, $expected ) >= 0;
					break;
			}
		}

		return $isGood;
	}
}
