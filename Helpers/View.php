<?php

namespace My\Optimized\Helpers;

class View {
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
