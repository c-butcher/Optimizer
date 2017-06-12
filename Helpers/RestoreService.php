<?php

namespace My\Optimized\Helpers;

use My\Optimized\Configurators\ConfiguratorInterface;
use My\Optimized\Models\ConfigRevision;
use My\Optimized\Models\PluginInfo;

class RestoreService
{
	protected $db;

	public function __construct() {
		global $wpdb;

		$this->db = $wpdb;
	}

	public function getRevisions( $name ) {
		$revisions = array();

		$query = $this->db->prepare( "SELECT * FROM `{$this->db->prefix}my_config_backups` WHERE `plugin` = %s ORDER BY `datetime` DESC", array( $name ) );
		if ( ( $results = $this->db->get_results( $query, ARRAY_A ) ) !== null ) {
			foreach ( $results as $revision ) {
				$revisions[] = new ConfigRevision($revision);
			}

		} else Logger::getInstance()->alert(
			__( "Unable to load the backups for the {plugin} plugin.", 'a2-optimized' ),
			array( 'plugin' => $name )
		);

		return $revisions;
	}

	public function hasRevisions( $name ) {
		$has = false;

		$query = $this->db->prepare( "SELECT COUNT(*) FROM `{$this->db->prefix}my_config_backups` WHERE `plugin` = %s", array( $name ) );
		if ( ( $results = $this->db->get_results( $query, ARRAY_A ) ) !== null ) {
			if ( is_array($results ) && count( $results ) > 0 ) {
				$has = true;
			}
		} else Logger::getInstance()->alert(
			__( "Unable to load the backups for the {plugin} plugin.", 'a2-optimized' ),
			array( 'plugin' => $name )
		);

		return $has;
	}

	public function getRevision( $id ) {
		$revision = null;

		$query = $this->db->prepare( "SELECT * FROM `{$this->db->prefix}my_config_backups` WHERE `id` = %d", array( $id ) );
		if ( ( $results = $this->db->get_results( $query, ARRAY_A ) ) !== null ) {
			if ( count( $results ) > 0 ) {
				$revision = new ConfigRevision($results[0]);
			}

		} else Logger::getInstance()->alert(
			__( "Unable to load the backups for revision #{id} plugin.", 'a2-optimized' ),
			array( 'id' => $id )
		);

		return $revision;
	}

	public function getLastRevision( $name ) {
		$revision = null;

		$query = $this->db->prepare( "SELECT * FROM `{$this->db->prefix}my_config_backups` WHERE `plugin` = %s LIMIT 0,1", array( $name ) );
		if ( ( $results = $this->db->get_results( $query, ARRAY_A ) ) !== null ) {
			if ( count( $results ) > 0 ) {
				$revision = new ConfigRevision($results[0]);
			}

		} else Logger::getInstance()->debug(
			__( "Unable to load the last revision for the {plugin} plugin.", 'a2-optimized' ),
			array( 'plugin' => $name )
		);

		return $revision;
	}

	public function create( PluginInfo $plugin, ConfiguratorInterface $configurator ) {
		$revision = new ConfigRevision(array(
			'plugin'  => $plugin->name,
			'version' => $plugin->version,
			'config'  => $configurator->getConfiguration(),
		));

		return $this->save( $revision );
	}

	protected function save( ConfigRevision $revision ) {
		$query = $this->db->prepare( "INSERT INTO `{$this->db->prefix}my_config_backups` (`plugin`, `version`, `datetime`, `config`) VALUES (%s, %s, %s, %s)", array(
			$revision->plugin,
			$revision->version,
			$revision->datetime->format('Y-m-d H:i:s'),
			json_encode($revision->config),
		));

		if ( $this->db->query( $query ) === false ) {
			Logger::getInstance()->alert(
				__( "Unable to create a backup of the {plugin} configuration.", 'a2-optimized' ),
				array( 'plugin' => $revision->plugin )
			);
		}

		return true;
	}

	public function delete( ConfigRevision $revision ) {
		$query = $this->db->prepare( "DELETE FROM `{$this->db->prefix}my_config_backups` WHERE `id` = %d", array(
			$revision->id,
		));

		if ( $this->db->query( $query ) === false ) {
			Logger::getInstance()->alert(
				__( "Unable to create a backup of the {plugin} configuration.", 'a2-optimized' ),
				array( 'plugin' => $revision->plugin )
			);

			return false;
		}

		return true;
	}
}
