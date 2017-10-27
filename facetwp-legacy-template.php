<?php
/*
Plugin Name: FacetWP Legacy Templates
Description: Backwards Compatibility for using facet templates without facet filters
Version: 1.0.0
Author: FacetWP, LLC
Author URI: https://facetwp.com/

Copyright 2017 FacetWP, LLC

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, see <http://www.gnu.org/licenses/>.
*/

defined( 'ABSPATH' ) or exit;

class FacetWPLegacy
{

	private static $instance;

	/**
	 * FacetWPLegacy constructor.
	 */
	function __construct() {

		define( 'FACETWPL_VERSION', '1.0.0' );
		define( 'FACETWPL_DIR', dirname( __FILE__ ) );

		// get the gears turning
		include( FACETWPL_DIR . '/class-legacy-shortcode.php' );

		// shortcode for using templates without facets
		add_shortcode( 'facetwplegacy', array( $this, 'shortcode' ) );

	}


	/**
	 * Singleton
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Register shortcode
	 */
	function shortcode( $atts ) {

		if ( isset( $atts['template'] ) ) {

			$template_shortcode = new FacetWPLegacyShortcode();

			return $template_shortcode->render( $atts['template'] );

		} else {

			return '';
		}
	}
}


function FWPLegacy() {
	return FacetWPLegacy::instance();
}


FWPLegacy();
