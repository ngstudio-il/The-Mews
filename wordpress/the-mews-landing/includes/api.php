<?php
/**
 * Content API for remote editing (for example from an assistant or a deploy script).
 *
 *   GET  /wp-json/tml/v1/content            all sections with their current values
 *   POST /wp-json/tml/v1/content            {"hero": {"l2": {...}}} updates only the fields sent
 *   POST /wp-json/tml/v1/content  {"__reset": "hero"}   back to the default content
 *
 * Only for users who can manage options. Authenticate with an Application Password
 * (Users → Profile → Application Passwords) over HTTPS.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'rest_api_init', 'tml_register_content_route' );

function tml_register_content_route() {
	$can = function () {
		return current_user_can( 'manage_options' );
	};
	register_rest_route(
		'tml/v1',
		'/content',
		array(
			array(
				'methods'             => 'GET',
				'callback'            => 'tml_api_get_content',
				'permission_callback' => $can,
			),
			array(
				'methods'             => 'POST',
				'callback'            => 'tml_api_update_content',
				'permission_callback' => $can,
			),
		)
	);
}

function tml_api_get_content() {
	$out = array();
	foreach ( tml_schema() as $section => $def ) {
		$out[ $section ] = tml_section( $section );
	}
	return rest_ensure_response(
		array(
			'version'  => TML_VERSION,
			'page'     => get_permalink( (int) get_option( 'tml_landing_page' ) ),
			'content'  => $out,
			'sections' => array_map(
				function ( $def ) {
					return $def['title'];
				},
				tml_schema()
			),
		)
	);
}

function tml_api_update_content( WP_REST_Request $request ) {
	$input = $request->get_json_params();
	if ( ! is_array( $input ) || ! $input ) {
		return new WP_Error( 'tml_empty', 'Send a JSON object of sections', array( 'status' => 400 ) );
	}
	$unknown = array_diff( array_keys( $input ), array_merge( array_keys( tml_schema() ), array( '__reset' ) ) );
	if ( $unknown ) {
		return new WP_Error( 'tml_unknown', 'Unknown sections: ' . implode( ', ', $unknown ), array( 'status' => 400 ) );
	}
	// Fields that were not sent keep their current value.
	foreach ( $input as $section => $fields ) {
		if ( '__reset' !== $section && is_array( $fields ) ) {
			$input[ $section ] = array_merge( tml_section( $section ), $fields );
		}
	}
	// The sanitizers unslash (they serve the admin form), so slash the JSON first.
	$clean = tml_sanitize_content( wp_slash( $input ) );
	update_option( 'tml_content', $clean );
	return tml_api_get_content();
}
