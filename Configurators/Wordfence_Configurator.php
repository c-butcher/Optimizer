<?php

namespace My\Optimized\Configurators;

use My\Optimized\Helpers\Logger;

class Wordfence_Configurator implements ConfiguratorInterface {

	/**
	 * Wordfence_Configurator constructor.
	 *
	 * @inheritdoc
	 *
	 * @param null $config
	 */
	public function __construct( $config = null ) { }

	/**
	 * @inheritdoc
	 *
	 * @param string $name
	 * @param null $default
	 *
	 * @return bool|mixed|null|string
	 */
	public function get( $name, $default = null ) {
		return \wfConfig::get( $name, $default );
	}

	/**
	 * @inheritdoc
	 *
	 * @param string $name
	 * @param mixed $value
	 *
	 * @return $this
	 */
	public function set( $name, $value ) {
		\wfConfig::set( $name, $value );

		return $this;
	}

	/**
	 * @inheritdoc
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	public function has( $name ) {
		return \wfConfig::get( $name, null ) !== null ? true : false;
	}

	/**
	 * @inheritdoc
	 *
	 * @return bool
	 */
	public function save() {
		return true;
	}

	/**
	 * @inheritdoc
	 *
	 * @return array
	 */
	public function getConfiguration() {
		return \wfConfig::parseOptions();
	}

	/**
	 * @inheritdoc
	 *
	 * @param array $configuration
	 */
	public function setConfiguration( array $configuration ) {
		\wfConfig::setArray( $configuration );
	}

	/**
	 * @inheritdoc
	 *
	 * @param array $arguments
	 *
	 * @return array
	 */
	public function getSuggestions( array $arguments = array() ) {
		$suggestions = array();

		if ( $this->get('13') === null ) {
			$suggestions[ '13' ] = __( 'We suggest that you disable local file inclusion. (THIS IS A DEMO)', 'a2-optimized' );
		}

		return $suggestions;
	}

	/**
	 * @inheritdoc
	 *
	 * @param array $optimizations
	 *
	 * @return bool
	 */
	public function configure( array $optimizations = array() ) {
		return true;
	}
}
