<?php

namespace My\Optimized\Configurators;

use My\Optimized\Helpers\Logger;

class EWWW_Configurator implements ConfiguratorInterface {

	protected $config;

	public function __construct( $config = null ) {
		$this->config = $this->getConfiguration();
	}

	public function get( $name, $default = null ) {
		if ( ! $this->has( $name ) ) {
			return $default;
		}

		return $this->config[ $name ];
	}

	public function set( $name, $value ) {
		if ( ewww_image_optimizer_set_option( $name, $value ) ) {
			$this->config[ $name ] = $value;
		}

		return $this;
	}

	public function has( $name ) {
		return isset( $this->config[ $name ] );
	}

	public function save() {
		return true;
	}

	public function getConfiguration() {
		global $wpdb;

		$query = $wpdb->prepare( "SELECT * FROM {$wpdb->options} WHERE `option_name` LIKE %s", array(
			'%ewww_image_%'
		) );

		$config = array();
		if ( ( $results = $wpdb->get_results( $query, ARRAY_A ) ) !== false ) {
			foreach ( $results as $option ) {
				$config[$option['option_name']] = $option['option_value'];
			}
		}

		return $config;
	}

	public function setConfiguration( array $configuration ) {
		foreach ( $configuration as $name => $value ) {
			$this->set( $name, $value );
		}

		return $this;
	}

	public function getSuggestions( array $arguments = array() ) {
		$suggestions = array();

		if ( isset( $this->config[ 'ewww_image_optimizer_jpg_quality' ]) && $this->config[ 'ewww_image_optimizer_jpg_quality' ] != 80 ) {
			$suggestions[ 'ewww_image_optimizer_jpg_quality' ] = __( 'Change your JPG quality to 80%.', 'a2-optimized' );
		}

		if ( isset( $this->config[ 'ewww_image_optimizer_optipng_level' ]) && $this->config[ 'ewww_image_optimizer_optipng_level' ] != 3 ) {
			$suggestions[ 'ewww_image_optimizer_optipng_level' ] = __( 'Optimize your PNGs to level 3.', 'a2-optimized' );
		}

		if ( !isset( $this->config[ 'ewww_image_optimizer_delay' ] ) || $this->config[ 'ewww_image_optimizer_delay' ] < 2 ) {
			$suggestions[ 'ewww_image_optimizer_delay' ] = __( 'Set a 2 second delay between image optimization when adding lots of images.', 'a2-optimized' );
		}


		return $suggestions;
	}

	public function configure( array $optimizations = array() ) {

		if ( isset( $optimizations['ewww_image_optimizer_jpg_quality'] ) ) {
			$this->set( 'ewww_image_optimizer_jpg_quality', 80 );
		}

		if ( isset( $optimizations['ewww_image_optimizer_optipng_level'] ) ) {
			$this->set( 'ewww_image_optimizer_optipng_level', 3 );
		}

		if ( isset( $optimizations['ewww_image_optimizer_delay'] ) ) {
			$this->set( 'ewww_image_optimizer_delay', 2 );
		}

		return true;
	}
}
