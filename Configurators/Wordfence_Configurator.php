<?php

namespace My\Optimized\Configurators;

use My\Optimized\Helpers\Logger;

class Wordfence_Configurator implements ConfiguratorInterface {

	public function __construct( $config = null ) { }

	public function get( $name, $default = null ) {
		return \wfConfig::get( $name, $default );
	}

	public function set( $name, $value ) {
		\wfConfig::set( $name, $value );

		return $this;
	}

	public function has( $name ) {
		return \wfConfig::get( $name, null ) !== null ? true : false;
	}

	public function save() {
		return true;
	}

	public function getConfiguration() {
		return \wfConfig::parseOptions();
	}

	public function setConfiguration( array $configuration ) {
		\wfConfig::setArray( $configuration );
	}

	public function getSuggestions( array $arguments = array() ) {
		$suggestions = array();

		if ( $this->get('13') === null ) {
			$suggestions[ '13' ] = __( 'We suggest that you disable local file inclusion. (THIS IS A DEMO)', 'a2-optimized' );
		}

		return $suggestions;
	}

	public function configure( array $optimizations = array() ) {
		return true;
	}
}
