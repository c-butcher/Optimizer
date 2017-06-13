<?php

namespace My\Optimized\Helpers;

class View {

	/**
	 * Supplies the arguments to the HTML within the view, and renders it for display.
	 *
	 * @param string $_view
	 * @param string $_arguments
	 */
	public static function render( $_view, $_arguments ) {
		if ( substr($_view, strlen($_view) - 5) != '.php' ) {
			$_view = $_view . '.php';
		}

		if ( ! file_exists( MY_OPTIMIZED_VIEWS . $_view ) ) {
			return null;
		}

		extract( $_arguments );

		ob_start();
		include( MY_OPTIMIZED_VIEWS . $_view );
		ob_end_flush();
	}
}
