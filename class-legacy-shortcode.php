<?php

defined( 'ABSPATH' ) or exit;

class FacetWPLegacyShortcode
{

	/* (string) Template name */
	public $template;

	/* (array) WP_Query arguments */
	public $query_args;

	/* (boolean) Whether search is active */
	public $is_search = false;

	/* (array) The final WP_Query object */
	public $query;

	/**
	 * FacetWPLegacyShortcode constructor.
	 */
	function __construct() {

	}

	/**
	 * render shortcode
	 */
	function render( $template ) {

		if ( isset( $template ) ) {

			$this->template = FWP()->helper->get_template_by_name( $template );
			$query_args = $this->get_query_args();

		} else {

			return '';
		}

		// Detect search string
		if ( ! empty( $query_args['s'] ) ) {
			$this->is_search = true;
		}

		// Run the query once
		if ( empty( $this->query_args ) ) {

			// Get the template "query" field
			$this->query_args = apply_filters( 'facetwp_query_args', $query_args, $this );

			$this->query_args['paged'] = $page;


			// Set the default limit
			if ( empty( $this->query_args['posts_per_page'] ) ) {
				$this->query_args['posts_per_page'] = (int) get_option( 'posts_per_page' );
			}

			// Run the WP_Query
			$this->query = new WP_Query( $this->query_args );
		}

		$output = '';

		if ( $template_html = $this->get_template_html() ) {

			$output = '<div class="facetwp-legacy-template">';
			$output .= $template_html;
			$output .= '</div>';
		}

		$output = apply_filters( 'facetwp_shortcode_html', $output, $atts );

		return $output;
	}

	/**
	 * create the html for the template output
	 *
	 * @return mixed|string|void
	 */
	function get_template_html() {

		global $post, $wp_query;

		$output = apply_filters( 'facetwp_template_html', false, $this );

		if ( false === $output ) {
			ob_start();

			// Preserve globals
			$temp_post = is_object( $post ) ? clone $post : $post;
			$temp_wp_query = is_object( $wp_query ) ? clone $wp_query : $wp_query;

			$query = $this->query;
			$wp_query = $query; // Make $query->blah() optional

			// Remove UTF-8 non-breaking spaces
			$display_code = $this->template['template'];
			$display_code = preg_replace( "/\xC2\xA0/", ' ', $display_code );

			eval( '?>' . $display_code );

			// Reset globals
			$post = $temp_post;
			$wp_query = $temp_wp_query;

			// Store buffered output
			$output = ob_get_clean();
		}

		$output = preg_replace( "/\xC2\xA0/", ' ', $output );

		return $output;

	}


	/**
	 * gets the query args for the template
	 *
	 * @return array|mixed
	 */
	function get_query_args() {

		// remove UTF-8 non-breaking spaces
		$query_args = preg_replace( "/\xC2\xA0/", ' ', $this->template['query'] );
		$query_args = (array) eval( '?>' . $query_args );

		// Merge the two arrays
		return $query_args;
	}
}
