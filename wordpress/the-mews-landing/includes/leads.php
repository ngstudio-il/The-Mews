<?php
/**
 * Leads: every form submission is saved in WordPress, emailed, and optionally sent to a webhook.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'tml_register_lead_type' );
add_action( 'rest_api_init', 'tml_register_lead_route' );

function tml_register_lead_type() {
	register_post_type(
		'tml_lead',
		array(
			'labels'          => array(
				'name'          => 'לידים',
				'singular_name' => 'ליד',
				'menu_name'     => 'לידים',
				'all_items'     => 'לידים',
				'search_items'  => 'חיפוש לידים',
				'not_found'     => 'עדיין לא התקבלו לידים',
				'edit_item'     => 'ליד',
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => 'tml',
			'supports'        => array( 'title' ),
			'capability_type' => 'post',
			'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'    => true,
		)
	);
}

function tml_register_lead_route() {
	register_rest_route(
		'tml/v1',
		'/lead',
		array(
			'methods'             => 'POST',
			'callback'            => 'tml_handle_lead',
			'permission_callback' => '__return_true',
		)
	);
}

function tml_handle_lead( WP_REST_Request $request ) {
	$name    = sanitize_text_field( (string) $request->get_param( 'name' ) );
	$phone   = sanitize_text_field( (string) $request->get_param( 'phone' ) );
	$lang    = 'en' === $request->get_param( 'language' ) ? 'en' : 'he';
	$page    = esc_url_raw( (string) $request->get_param( 'page' ) );
	$website = (string) $request->get_param( 'website' );

	// Honeypot: a hidden field people never fill in. Pretend success so bots move on.
	if ( '' !== $website ) {
		return rest_ensure_response( array( 'ok' => true ) );
	}
	if ( mb_strlen( $name ) < 2 || ! preg_match( '/^[+\d][\d\s-]{7,}$/', $phone ) ) {
		return new WP_Error( 'tml_invalid', 'Invalid name or phone', array( 'status' => 400 ) );
	}

	// At most 5 submissions per IP per 10 minutes.
	$ip_key = 'tml_rate_' . md5( isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '' );
	$count  = (int) get_transient( $ip_key );
	if ( $count >= 5 ) {
		return new WP_Error( 'tml_rate', 'Too many requests', array( 'status' => 429 ) );
	}
	set_transient( $ip_key, $count + 1, 10 * MINUTE_IN_SECONDS );

	$id = wp_insert_post(
		array(
			'post_type'   => 'tml_lead',
			'post_status' => 'publish',
			'post_title'  => $name,
		)
	);
	if ( ! $id || is_wp_error( $id ) ) {
		return new WP_Error( 'tml_save', 'Could not save', array( 'status' => 500 ) );
	}
	update_post_meta( $id, '_tml_phone', $phone );
	update_post_meta( $id, '_tml_lang', $lang );
	update_post_meta( $id, '_tml_page', $page );

	$general = tml_section( 'general' );
	$to      = $general['notify_email'] ? $general['notify_email'] : get_option( 'admin_email' );
	wp_mail(
		$to,
		'ליד חדש – The Mews: ' . $name,
		"שם: {$name}\nטלפון: {$phone}\nשפה: " . ( 'en' === $lang ? 'אנגלית' : 'עברית' ) . "\nעמוד: {$page}\n\n" . admin_url( 'edit.php?post_type=tml_lead' )
	);

	if ( $general['webhook'] ) {
		wp_remote_post(
			$general['webhook'],
			array(
				'blocking' => false,
				'timeout'  => 5,
				'headers'  => array( 'Content-Type' => 'application/json' ),
				'body'     => wp_json_encode(
					array(
						'name'     => $name,
						'phone'    => $phone,
						'language' => $lang,
						'page'     => $page,
						'project'  => 'The Mews Residences',
						'date'     => current_time( 'c' ),
					)
				),
			)
		);
	}

	return rest_ensure_response( array( 'ok' => true ) );
}

/* ---------- Leads list in the admin ---------- */

add_filter( 'manage_tml_lead_posts_columns', 'tml_lead_columns' );
add_action( 'manage_tml_lead_posts_custom_column', 'tml_lead_column', 10, 2 );
add_action( 'add_meta_boxes_tml_lead', 'tml_lead_meta_box' );

function tml_lead_columns( $columns ) {
	return array(
		'cb'        => $columns['cb'],
		'title'     => 'שם',
		'tml_phone' => 'טלפון',
		'tml_lang'  => 'שפה',
		'date'      => 'התקבל',
	);
}

function tml_lead_column( $column, $post_id ) {
	if ( 'tml_phone' === $column ) {
		$phone = get_post_meta( $post_id, '_tml_phone', true );
		printf( '<a href="tel:%1$s" dir="ltr">%2$s</a>', esc_attr( preg_replace( '/[^\d+]/', '', $phone ) ), esc_html( $phone ) );
	} elseif ( 'tml_lang' === $column ) {
		echo 'en' === get_post_meta( $post_id, '_tml_lang', true ) ? 'English' : 'עברית';
	}
}

function tml_lead_meta_box() {
	add_meta_box(
		'tml_lead_details',
		'פרטי הליד',
		function ( $post ) {
			$phone = get_post_meta( $post->ID, '_tml_phone', true );
			echo '<p><strong>טלפון:</strong> <a href="tel:' . esc_attr( preg_replace( '/[^\d+]/', '', $phone ) ) . '" dir="ltr">' . esc_html( $phone ) . '</a></p>';
			echo '<p><strong>שפה:</strong> ' . ( 'en' === get_post_meta( $post->ID, '_tml_lang', true ) ? 'English' : 'עברית' ) . '</p>';
			echo '<p><strong>עמוד:</strong> ' . esc_html( get_post_meta( $post->ID, '_tml_page', true ) ) . '</p>';
			echo '<p><strong>התקבל:</strong> ' . esc_html( get_the_date( 'd/m/Y H:i', $post ) ) . '</p>';
		},
		'tml_lead',
		'normal',
		'high'
	);
}

/** Export all leads as CSV (button on the leads list). */
add_action( 'admin_post_tml_export_leads', 'tml_export_leads' );
add_action( 'manage_posts_extra_tablenav', 'tml_export_button' );

function tml_export_button( $which ) {
	if ( 'top' !== $which || 'tml_lead' !== get_current_screen()->post_type ) {
		return;
	}
	$url = wp_nonce_url( admin_url( 'admin-post.php?action=tml_export_leads' ), 'tml_export' );
	echo '<div class="alignleft actions"><a class="button" href="' . esc_url( $url ) . '">ייצוא ל-CSV</a></div>';
}

function tml_export_leads() {
	if ( ! current_user_can( 'edit_posts' ) || ! check_admin_referer( 'tml_export' ) ) {
		wp_die( 'אין הרשאה' );
	}
	$leads = get_posts(
		array(
			'post_type'   => 'tml_lead',
			'numberposts' => -1,
			'post_status' => 'any',
		)
	);
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=the-mews-leads-' . gmdate( 'Y-m-d' ) . '.csv' );
	$out = fopen( 'php://output', 'w' );
	fwrite( $out, "\xEF\xBB\xBF" ); // BOM so Excel opens Hebrew correctly
	fputcsv( $out, array( 'שם', 'טלפון', 'שפה', 'עמוד', 'תאריך' ) );
	foreach ( $leads as $lead ) {
		$row = array(
			$lead->post_title,
			get_post_meta( $lead->ID, '_tml_phone', true ),
			get_post_meta( $lead->ID, '_tml_lang', true ),
			get_post_meta( $lead->ID, '_tml_page', true ),
			get_the_date( 'Y-m-d H:i', $lead ),
		);
		// Keep spreadsheet apps from reading a value as a formula.
		$row = array_map(
			function ( $cell ) {
				return preg_match( '/^[=+\-@]/', (string) $cell ) ? "'" . $cell : $cell;
			},
			$row
		);
		fputcsv( $out, $row );
	}
	fclose( $out );
	exit;
}
