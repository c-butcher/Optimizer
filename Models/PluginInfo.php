<?php

namespace My\Optimized\Models;

class PluginInfo {
	public $name;

	public $description;

	public $version;

	public $author;

	public $pluginUri;

	public function __construct( array $info ) {
		$this->name        = isset( $info['Name'] ) ? $info['Name'] : null;
		$this->description = isset( $info['Description'] ) ? $info['Description'] : null;
		$this->version     = isset( $info['Version'] ) ? $info['Version'] : '0.0.0';
		$this->author      = isset( $info['Author'] ) ? $info['Author'] : null;
		$this->pluginUri   = isset( $info['Plugin URI']) ? $info['Plugin URI'] : null;
		$this->Active      = isset( $info['Active'] ) ? $info['Active'] : false;
	}
}
