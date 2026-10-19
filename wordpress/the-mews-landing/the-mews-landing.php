<?php
/**
 * Plugin Name:       The Mews – דף נחיתה
 * Description:       דף הנחיתה של The Mews Residences (לימסול, קפריסין) עם עריכת כל התוכן לפי אזורים, עברית ואנגלית, שמירת לידים, תפריט נגישות ועמודי מדיניות פרטיות והצהרת נגישות.
 * Version:           1.1.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Text Domain:       the-mews-landing
 */

defined( 'ABSPATH' ) || exit;

define( 'TML_VERSION', '1.1.0' );
define( 'TML_DIR', plugin_dir_path( __FILE__ ) );
define( 'TML_URL', plugin_dir_url( __FILE__ ) );
define( 'TML_TEMPLATE_LANDING', 'tml-landing' );
define( 'TML_TEMPLATE_LEGAL', 'tml-legal' );

require_once TML_DIR . 'includes/schema.php';
require_once TML_DIR . 'includes/render.php';
require_once TML_DIR . 'includes/leads.php';
require_once TML_DIR . 'includes/admin.php'; // also holds the sanitizers the content API uses
require_once TML_DIR . 'includes/api.php';

/* ---------- Activation: create the three pages ---------- */

register_activation_hook( __FILE__, 'tml_activate' );

function tml_activate() {
	tml_register_lead_type();
	flush_rewrite_rules();

	$pages = array(
		'tml_landing_page'       => array( 'The Mews Residences', 'the-mews', TML_TEMPLATE_LANDING, '' ),
		'tml_privacy_page'       => array( 'מדיניות פרטיות', 'privacy', TML_TEMPLATE_LEGAL, tml_default_legal( 'privacy' ) ),
		'tml_accessibility_page' => array( 'הצהרת נגישות', 'accessibility', TML_TEMPLATE_LEGAL, tml_default_legal( 'accessibility' ) ),
	);
	foreach ( $pages as $option => $page ) {
		$existing = (int) get_option( $option );
		if ( $existing && get_post( $existing ) && 'trash' !== get_post_status( $existing ) ) {
			continue;
		}
		$id = wp_insert_post(
			array(
				'post_title'   => $page[0],
				'post_name'    => $page[1],
				'post_content' => $page[3],
				'post_status'  => 'publish',
				'post_type'    => 'page',
			)
		);
		if ( $id && ! is_wp_error( $id ) ) {
			update_post_meta( $id, '_wp_page_template', $page[2] );
			update_option( $option, $id );
		}
	}
}

function tml_default_legal( $which ) {
	$file = TML_DIR . 'includes/legal-' . $which . '.html';
	return file_exists( $file ) ? file_get_contents( $file ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
}

/* ---------- Page templates (work with any theme) ---------- */

add_filter( 'theme_page_templates', 'tml_page_templates' );
add_filter( 'template_include', 'tml_template_include', 99 );

function tml_page_templates( $templates ) {
	$templates[ TML_TEMPLATE_LANDING ] = 'The Mews – דף נחיתה';
	$templates[ TML_TEMPLATE_LEGAL ]   = 'The Mews – עמוד תוכן';
	return $templates;
}

function tml_current_template() {
	if ( ! is_singular( 'page' ) ) {
		return '';
	}
	$slug = get_page_template_slug( get_queried_object_id() );
	return in_array( $slug, array( TML_TEMPLATE_LANDING, TML_TEMPLATE_LEGAL ), true ) ? $slug : '';
}

function tml_template_include( $template ) {
	$slug = tml_current_template();
	if ( TML_TEMPLATE_LANDING === $slug ) {
		return TML_DIR . 'templates/landing.php';
	}
	if ( TML_TEMPLATE_LEGAL === $slug ) {
		return TML_DIR . 'templates/legal.php';
	}
	return $template;
}

/* ---------- Assets: ours in, the theme's out (only on our pages) ---------- */

add_action( 'wp_enqueue_scripts', 'tml_enqueue', 999 );

function tml_enqueue() {
	$slug = tml_current_template();
	if ( ! $slug ) {
		return;
	}

	// The landing page carries its own design; theme and block styles would fight it.
	$theme_urls = array( get_template_directory_uri(), get_stylesheet_directory_uri() );
	foreach ( array( wp_styles(), wp_scripts() ) as $deps ) {
		foreach ( $deps->queue as $handle ) {
			$src = isset( $deps->registered[ $handle ] ) ? (string) $deps->registered[ $handle ]->src : '';
			foreach ( $theme_urls as $theme_url ) {
				if ( $src && 0 === strpos( $src, $theme_url ) ) {
					$deps instanceof WP_Styles ? wp_dequeue_style( $handle ) : wp_dequeue_script( $handle );
				}
			}
		}
	}
	foreach ( array( 'global-styles', 'wp-block-library', 'wp-block-library-theme', 'classic-theme-styles', 'core-block-supports' ) as $handle ) {
		wp_dequeue_style( $handle );
	}

	wp_enqueue_style( 'tml-fonts', 'https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Hebrew:wght@300;400;500;600;700&family=Marcellus&display=swap', array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	wp_enqueue_style( 'tml', TML_URL . 'assets/css/styles.css', array( 'tml-fonts' ), TML_VERSION );
	wp_add_inline_style( 'tml', 'body.admin-bar .site-header{top:32px}@media (max-width:782px){body.admin-bar .site-header{top:46px}}' );

	wp_enqueue_script( 'tml-a11y', TML_URL . 'assets/js/a11y.js', array(), TML_VERSION, true );
	wp_add_inline_script( 'tml-a11y', 'window.TML_A11Y_URL = ' . wp_json_encode( tml_page_url( 'accessibility' ) ) . ';', 'before' );

	if ( TML_TEMPLATE_LANDING === $slug ) {
		// main.js reads I18N_EN, I18N_HE and window.TML_CONFIG, which templates/landing.php adds while rendering.
		wp_enqueue_script( 'tml-main', TML_URL . 'assets/js/main.js', array(), TML_VERSION, true );
	}
}

/** URL of the privacy / accessibility page: the one set in the footer tab, or the page the plugin created. */
function tml_page_url( $which ) {
	$footer = tml_section( 'footer' );
	$custom = 'privacy' === $which ? $footer['privacy_url'] : $footer['a11y_url'];
	if ( $custom ) {
		return $custom;
	}
	$id = (int) get_option( 'privacy' === $which ? 'tml_privacy_page' : 'tml_accessibility_page' );
	return $id ? get_permalink( $id ) : '#';
}

/* ---------- Plugin list: quick links ---------- */

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'tml_action_links' );

function tml_action_links( $links ) {
	array_unshift( $links, '<a href="' . esc_url( admin_url( 'admin.php?page=tml' ) ) . '">עריכת תוכן</a>' );
	return $links;
}
