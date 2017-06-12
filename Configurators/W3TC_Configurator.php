<?php

namespace My\Optimized\Configurators;

use My\Optimized\Helpers\Logger;

class W3TC_Configurator implements ConfiguratorInterface {

	/**
	 * Configuration manager.
	 *
	 * @var mixed|\W3TC\Config
	 */
	protected $config;

	public function __construct( $config = null ) {
		if ( ! is_object( $config ) || ! $config instanceof \W3TC\Config ) {
			$config = new \W3TC\Config();
		}

		$this->config = $config;
	}

	public function get( $name, $default = null ) {
		return $this->config->get( $name, $default );
	}

	public function set( $name, $value ) {
		$this->config->set( $name, $value );

		return $this;
	}

	public function has( $name ) {
		return $this->config->get( $name, null ) !== null ? true : false;
	}

	public function save() {
		$this->config->save();

		return true;
	}

	public function getConfiguration() {
		$json = $this->config->export();

		if ( ( $config = json_decode( $json, true ) ) === false ) {
			Logger::getInstance()->critical( __( "Unable to decode the W3TC configuration file.", 'a2-optimized' ) );
		}

		return $config;
	}

	public function setConfiguration( array $configuration ) {
		foreach ( $configuration as $name => $value ) {
			$this->config->set( $name, $value );
		}

		return $this;
	}

	public function getSuggestions( array $arguments = array() ) {
		$suggestions = array();

		if ( ! $this->config->get_boolean( 'pgcache.enabled' ) ) {
			$suggestions['pgcache.enabled'] = __( "Enabling page caching.", 'a2-optimized' );
		}

		if ( ! $this->config->get_boolean( 'browsercache.enabled' ) ) {
			$suggestions['browsercache.enabled'] = __( "Enabling browser caching.", 'a2-optimized' );
		}

		if ( ! $this->config->get_boolean( 'browsercache.html.expires' ) ) {
			$suggestions['browsercache.html.expires'] = __( "Enable browser cache expiration.", 'a2-optimized' );
		}

		if ( ! $this->config->get_boolean( 'dbcache.enabled' ) ) {
			$suggestions['dbcache.enabled'] = __( "Enabling database caching.", 'a2-optimized' );
		}

		if (
			$this->config->get_integer( 'browsercache.cssjs.lifetime' ) < 2592000 ||
			$this->config->get_integer( 'browsercache.cssjs.lifetime' ) > 15724800 ||
			$this->config->get_integer( 'browsercache.other.lifetime' ) < 2592000 ||
			$this->config->get_integer( 'browsercache.other.lifetime' ) > 15724800
		) {
			$suggestions['browsercache.lifetime'] = __( "Cache CSS, JavaScript and other assets for 2 months.", 'a2-optimized' );
		}

		if ( $this->config->get_integer( 'browsercache.html.lifetime' ) > 60 || $this->config->get_integer( 'browsercache.html.lifetime' ) < 30 ) {
			$suggestions['browsercache.html.lifetime'] = __( "Caches HTML pages for 45 seconds.", 'a2-optimized' );
		}

		if ( ! $this->config->get_boolean( 'browsercache.html.compression' ) || ! $this->config->get_boolean( 'browsercache.cssjs.compression' ) ) {
			$suggestions['browsercache.compression'] = __( "Enable compression of CSS, JavaScript and HTML.", 'a2-optimized' );
		}

		if ( $this->config->get_integer( 'dbcache.lifetime' ) < 7200 || $this->config->get_integer( 'dbcache.lifetime' ) > 28800 ) {
			$suggestions['dbcache.lifetime'] = __( "Cache database queries for six hours.", 'a2-optimized' );
		}

		if ( $this->config->get_boolean( 'varnish.enabled' ) === true ) {
			$suggestions['varnish.enabled'] = __( "Disable varnish reverse proxy.", 'a2-optimized' );
		}

		return $suggestions;
	}

	public function configure( array $optimizations = array() ) {
		if ( isset( $optimizations['pgcache.enabled'] ) ) {
			$this->set( 'pgcache.enabled', true );
		}

		if ( isset( $optimizations['browsercache.enabled'] ) ) {
			$this->set( 'browsercache.enabled', true );
		}

		if ( isset( $optimizations['browsercache.html.expires'] ) ) {
			$this->set( 'browsercache.html.expires', true );
		}

		if ( isset( $optimizations['dbcache.enabled'] ) ) {
			$this->set( 'dbcache.enabled', true );
		}

		if ( isset( $optimizations['browsercache.lifetime'] ) ) {
			$this->set( 'browsercache.cssjs.expires', true );
			$this->set( 'browsercache.other.expires', true );
			$this->set( 'browsercache.cssjs.lifetime', 5270400 );
			$this->set( 'browsercache.other.lifetime', 5270400 );
		}

		if ( isset( $optimizations['browsercache.html.lifetime'] ) ) {
			$this->set( 'browsercache.html.expires', true );
			$this->set( 'browsercache.html.lifetime', 45 );
		}

		if ( isset( $optimizations['dbcache.lifetime'] ) ) {
			$this->set( 'dbcache.lifetime', 21600 );
		}

		if ( isset( $optimizations['browsercache.compression'] ) ) {
			$this->set( 'browsercache.cssjs.compression', true );
			$this->set( 'browsercache.other.compression', true );
			$this->set( 'browsercache.html.compression', true );
		}

		$this->save();

		return true;
	}
}
