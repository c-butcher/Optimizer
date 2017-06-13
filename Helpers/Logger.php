<?php

namespace My\Optimized\Helpers;

/**
 * Class Logger
 *
 * @package My\Optimized\Helpers
 * @author  Chris Butcher <c.butcher@hotmail.com>
 * @version 0.1.0
 */
class Logger {

	/**
	 *
	 * @var array[]
	 */
	protected $logs = array(
		'emergency' => array(),
		'alert'     => array(),
		'critical'  => array(),
		'error'     => array(),
		'warning'   => array(),
		'notice'    => array(),
		'info'      => array(),
		'debug'     => array(),
		'success'   => array(),
	);

	/**
	 * Singleton instance of the plugin optimizer.
	 *
	 * @var Logger
	 */
	protected static $instance;

	/**
	 * Returns a singleton instance of the plugin optimizer.
	 *
	 * @return Logger
	 */
	public static function &getInstance() {
		if ( ! self::$instance instanceof self ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * System is unusable.
	 *
	 * @param string $message
	 * @param array $context
	 *
	 * @return void
	 */
	public function emergency( $message, array $context = array() ) {
		$this->log( 'emergency', $message, $context );
	}

	/**
	 * Action must be taken immediately.
	 *
	 * Example: Entire website down, database unavailable, etc. This should
	 * trigger the SMS alerts and wake you up.
	 *
	 * @param string $message
	 * @param array $context
	 *
	 * @return void
	 */
	public function alert( $message, array $context = array() ) {
		$this->log( 'alert', $message, $context );
	}

	/**
	 * Critical conditions.
	 *
	 * Example: Application component unavailable, unexpected exception.
	 *
	 * @param string $message
	 * @param array $context
	 *
	 * @return void
	 */
	public function critical( $message, array $context = array() ) {
		$this->log( 'critical', $message, $context );
	}

	/**
	 * Runtime errors that do not require immediate action but should typically
	 * be logged and monitored.
	 *
	 * @param string $message
	 * @param array $context
	 *
	 * @return void
	 */
	public function error( $message, array $context = array() ) {
		$this->log( 'error', $message, $context );
	}

	/**
	 * Exceptional occurrences that are not errors.
	 *
	 * Example: Use of deprecated APIs, poor use of an API, undesirable things
	 * that are not necessarily wrong.
	 *
	 * @param string $message
	 * @param array $context
	 *
	 * @return void
	 */
	public function warning( $message, array $context = array() ) {
		$this->log( 'warning', $message, $context );
	}

	/**
	 * Normal but significant events.
	 *
	 * @param string $message
	 * @param array $context
	 *
	 * @return void
	 */
	public function notice( $message, array $context = array() ) {
		$this->log( 'notice', $message, $context );
	}

	/**
	 * Interesting events.
	 *
	 * Example: User logs in, SQL logs.
	 *
	 * @param string $message
	 * @param array $context
	 *
	 * @return void
	 */
	public function info( $message, array $context = array() ) {
		$this->log( 'info', $message, $context );
	}

	/**
	 * Detailed debug information.
	 *
	 * @param string $message
	 * @param array $context
	 *
	 * @return void
	 */
	public function debug( $message, array $context = array() ) {
		$this->log( 'debug', $message, $context );
	}

	/**
	 * Heartwarming success messages.
	 *
	 * @param string $message
	 * @param array $context
	 *
	 * @return void
	 */
	public function success( $message, array $context = array() ) {
		$this->log( 'success', $message, $context );
	}

	/**
	 * Returns all of the logged messages for a specific level.
	 *
	 * @param string $level
	 *
	 * @return string[]
	 */
	public function get( $level ) {
		$messages = array();
		if ( isset( $this->logs[ $level ] ) ) {
			$messages = $this->logs[ $level ];
		}

		return $messages;
	}

	/**
	 * Clear out all of the log messages.
	 *
	 * @param null $level
	 */
	public function clear( $level = null ) {

		if ( $level === null ) {
			$this->logs = array(
				'emergency' => array(),
				'alert'     => array(),
				'critical'  => array(),
				'error'     => array(),
				'warning'   => array(),
				'notice'    => array(),
				'info'      => array(),
				'debug'     => array(),
				'success'   => array(),
			);

		} else $this->logs[ $level ] = array();
	}

	/**
	 * Logs with an arbitrary level.
	 *
	 * @param mixed $level
	 * @param string $message
	 * @param array $context
	 *
	 * @return void
	 */
	public function log( $level, $message, array $context = array() ) {
		$replaces = array();
		foreach ( $context as $name => $value ) {
			if ( ! is_array( $value ) && ( ! is_object( $value ) || method_exists( $value, '__toString' ) ) ) {
				$replaces["{{$name}}"] = $value;
			}
		}

		$this->logs[ $level ][] = strtr( $message, $replaces );
	}
}
