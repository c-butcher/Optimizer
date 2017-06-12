<?php

namespace My\Optimized\Models;

class ConfigRevision {
	public $id;
	public $plugin;
	public $version;
	public $datetime;
	public $config;

	public function __construct( $revision ) {
		if ( isset( $revision['config'] ) && is_string( $revision['config'] ) ) {
			if ( $config = json_decode( $revision['config'], true ) ) {
				$revision['config'] = $config;
			}
		}

		$this->id       = isset( $revision['id'] ) ? $revision['id'] : 0;
		$this->plugin   = isset( $revision['plugin'] ) ? $revision['plugin'] : null;
		$this->version  = isset( $revision['version'] ) ? $revision['version'] : '0.0.0';
		$this->datetime = isset( $revision['datetime'] ) ? new \DateTime( $revision['datetime'] ) : new \DateTime();
		$this->config   = isset( $revision['config'] ) && is_array( $revision['config'] ) ? $revision['config'] : array();
	}
}
