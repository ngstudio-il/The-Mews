<?php
/**
 * Admin screen: one tab per section, Hebrew and English side by side.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_menu', 'tml_admin_menu' );
add_action( 'admin_init', 'tml_register_setting' );
add_action( 'admin_enqueue_scripts', 'tml_admin_assets' );

function tml_admin_menu() {
	add_menu_page( 'The Mews – דף נחיתה', 'The Mews', 'manage_options', 'tml', 'tml_admin_page', 'dashicons-building', 3 );
	add_submenu_page( 'tml', 'עריכת תוכן', 'עריכת תוכן', 'manage_options', 'tml', 'tml_admin_page' );
}

function tml_register_setting() {
	register_setting(
		'tml',
		'tml_content',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'tml_sanitize_content',
			'default'           => array(),
		)
	);
}

function tml_admin_assets( $hook ) {
	if ( 'toplevel_page_tml' !== $hook ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_style( 'tml-admin', TML_URL . 'admin/admin.css', array(), TML_VERSION );
	wp_enqueue_script( 'tml-admin', TML_URL . 'admin/admin.js', array( 'jquery', 'jquery-ui-sortable' ), TML_VERSION, true );
}

function tml_current_tab() {
	$tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'general'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	return array_key_exists( $tab, tml_schema() ) ? $tab : 'general';
}

/* ---------- Saving ---------- */

/**
 * Each tab posts only its own section, so merge it into what is already stored
 * instead of replacing the whole option.
 */
function tml_sanitize_content( $input ) {
	$stored = get_option( 'tml_content', array() );
	if ( ! is_array( $stored ) ) {
		$stored = array();
	}
	if ( ! is_array( $input ) ) {
		return $stored;
	}
	$schema = tml_schema();

	if ( ! empty( $input['__reset'] ) && isset( $schema[ $input['__reset'] ] ) ) {
		unset( $stored[ $input['__reset'] ] );
		return $stored;
	}

	foreach ( $schema as $section => $def ) {
		if ( ! isset( $input[ $section ] ) || ! is_array( $input[ $section ] ) ) {
			continue;
		}
		$clean = array();
		foreach ( $def['fields'] as $field ) {
			$raw                    = isset( $input[ $section ][ $field['key'] ] ) ? $input[ $section ][ $field['key'] ] : null;
			$clean[ $field['key'] ] = tml_sanitize_field( $field, $raw );
		}
		$stored[ $section ] = $clean;
	}
	return $stored;
}

function tml_sanitize_field( $field, $raw ) {
	if ( 'repeater' === $field['type'] ) {
		$rows = array();
		if ( is_array( $raw ) ) {
			foreach ( $raw as $index => $row ) {
				if ( '__i__' === (string) $index || ! is_array( $row ) ) {
					continue; // the hidden row template
				}
				$clean = array();
				foreach ( $field['fields'] as $sub ) {
					$clean[ $sub['key'] ] = tml_sanitize_field( $sub, isset( $row[ $sub['key'] ] ) ? $row[ $sub['key'] ] : null );
				}
				$rows[] = $clean;
			}
		}
		return $rows;
	}

	if ( ! empty( $field['tr'] ) ) {
		$raw = is_array( $raw ) ? $raw : array();
		return array(
			'he' => tml_sanitize_value( $field['type'], isset( $raw['he'] ) ? $raw['he'] : '' ),
			'en' => tml_sanitize_value( $field['type'], isset( $raw['en'] ) ? $raw['en'] : '' ),
		);
	}
	return tml_sanitize_value( $field['type'], $raw );
}

function tml_sanitize_value( $type, $value ) {
	$value = is_string( $value ) ? wp_unslash( $value ) : '';
	switch ( $type ) {
		case 'textarea':
			return sanitize_textarea_field( $value );
		case 'html':
			return wp_kses( $value, tml_inline_tags() );
		case 'email':
			return sanitize_email( $value );
		case 'url':
			return esc_url_raw( $value );
		case 'image':
		case 'video':
			return 0 === strpos( $value, 'asset:' ) ? sanitize_text_field( $value ) : esc_url_raw( $value );
		case 'checkbox':
			return $value ? '1' : '';
		default:
			return sanitize_text_field( $value );
	}
}


/* ---------- Page ---------- */

function tml_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$schema  = tml_schema();
	$tab     = tml_current_tab();
	$values  = tml_section( $tab );
	$landing = (int) get_option( 'tml_landing_page' );
	?>
	<div class="wrap tml-admin">
		<h1 class="tml-title">The Mews – עריכת דף הנחיתה</h1>

		<?php if ( $landing && get_post( $landing ) ) : ?>
			<p class="tml-view">
				<a class="button" href="<?php echo esc_url( get_permalink( $landing ) ); ?>" target="_blank" rel="noopener">צפייה בדף הנחיתה ↗</a>
				<a class="button" href="<?php echo esc_url( admin_url( 'edit.php?post_type=tml_lead' ) ); ?>">לידים שהתקבלו</a>
			</p>
		<?php endif; ?>

		<?php settings_errors(); ?>

		<nav class="nav-tab-wrapper tml-tabs">
			<?php foreach ( $schema as $key => $section ) : ?>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=tml&tab=' . $key ) ); ?>" class="nav-tab <?php echo $key === $tab ? 'nav-tab-active' : ''; ?>"><?php echo esc_html( $section['title'] ); ?></a>
			<?php endforeach; ?>
		</nav>

		<form method="post" action="options.php" class="tml-form">
			<?php settings_fields( 'tml' ); ?>
			<input type="hidden" name="_wp_http_referer" value="<?php echo esc_attr( admin_url( 'admin.php?page=tml&tab=' . $tab ) ); ?>">

			<div class="tml-langs" aria-hidden="true"><span></span><b>עברית</b><b>English</b></div>

			<?php
			foreach ( $schema[ $tab ]['fields'] as $field ) {
				tml_render_field( $field, $values[ $field['key'] ], 'tml_content[' . $tab . '][' . $field['key'] . ']' );
			}
			?>

			<p class="submit">
				<?php submit_button( 'שמירת שינויים', 'primary', 'submit', false ); ?>
				<button type="submit" class="button tml-reset" name="tml_content[__reset]" value="<?php echo esc_attr( $tab ); ?>" formnovalidate>שחזור ברירת מחדל ללשונית</button>
			</p>
		</form>
	</div>
	<?php
}

function tml_render_field( $field, $value, $name ) {
	$help = ! empty( $field['help'] ) ? '<p class="description">' . esc_html( $field['help'] ) . '</p>' : '';

	if ( 'repeater' === $field['type'] ) {
		$rows = is_array( $value ) ? array_values( $value ) : array();
		?>
		<div class="tml-repeater" data-name="<?php echo esc_attr( $name ); ?>">
			<h2 class="tml-rep-title"><?php echo esc_html( $field['label'] ); ?></h2>
			<?php echo $help; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<div class="tml-rows">
				<?php foreach ( $rows as $i => $row ) : ?>
					<?php tml_render_row( $field, $row, $name . '[' . $i . ']' ); ?>
				<?php endforeach; ?>
			</div>
			<template class="tml-row-template"><?php tml_render_row( $field, array(), $name . '[__i__]' ); ?></template>
			<button type="button" class="button tml-add">+ <?php echo esc_html( $field['add'] ); ?></button>
		</div>
		<?php
		return;
	}

	$id = 'f-' . md5( $name );
	?>
	<div class="tml-field tml-type-<?php echo esc_attr( $field['type'] ); ?>">
		<label class="tml-label" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
		<div class="tml-inputs <?php echo empty( $field['tr'] ) ? 'tml-single' : ''; ?>">
			<?php
			if ( ! empty( $field['tr'] ) ) {
				$value = is_array( $value ) ? $value : array( 'he' => '', 'en' => '' );
				tml_render_input( $field['type'], $id, $name . '[he]', isset( $value['he'] ) ? $value['he'] : '', 'rtl' );
				tml_render_input( $field['type'], $id . '-en', $name . '[en]', isset( $value['en'] ) ? $value['en'] : '', 'ltr' );
			} else {
				tml_render_input( $field['type'], $id, $name, is_string( $value ) ? $value : '', 'auto' );
			}
			?>
		</div>
		<?php echo $help; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
	<?php
}

function tml_render_row( $field, $row, $name ) {
	?>
	<div class="tml-row">
		<div class="tml-row-bar">
			<span class="tml-handle dashicons dashicons-move" title="גרירה לשינוי סדר"></span>
			<button type="button" class="button-link tml-remove">הסרה</button>
		</div>
		<?php
		foreach ( $field['fields'] as $sub ) {
			$v = array_key_exists( $sub['key'], $row ) ? $row[ $sub['key'] ] : $sub['default'];
			tml_render_field( $sub, $v, $name . '[' . $sub['key'] . ']' );
		}
		?>
	</div>
	<?php
}

function tml_render_input( $type, $id, $name, $value, $dir ) {
	switch ( $type ) {
		case 'textarea':
		case 'html':
			printf( '<textarea id="%s" name="%s" dir="%s" rows="3">%s</textarea>', esc_attr( $id ), esc_attr( $name ), esc_attr( $dir ), esc_textarea( $value ) );
			break;
		case 'checkbox':
			printf( '<input type="hidden" name="%2$s" value=""><label class="tml-check"><input type="checkbox" id="%1$s" name="%2$s" value="1" %3$s> כן</label>', esc_attr( $id ), esc_attr( $name ), checked( $value, '1', false ) );
			break;
		case 'image':
		case 'video':
			$url = tml_url( $value );
			?>
			<div class="tml-media" data-type="<?php echo esc_attr( $type ); ?>">
				<input type="hidden" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>">
				<div class="tml-preview">
					<?php if ( $url && 'image' === $type ) : ?>
						<img src="<?php echo esc_url( $url ); ?>" alt="">
					<?php elseif ( $url ) : ?>
						<video src="<?php echo esc_url( $url ); ?>" muted preload="metadata"></video>
					<?php endif; ?>
				</div>
				<button type="button" class="button tml-pick"><?php echo 'image' === $type ? 'בחירת תמונה' : 'בחירת סרטון'; ?></button>
				<button type="button" class="button-link tml-clear">ניקוי</button>
			</div>
			<?php
			break;
		default:
			$input_type = in_array( $type, array( 'email', 'url' ), true ) ? $type : 'text';
			printf( '<input type="%s" id="%s" name="%s" value="%s" dir="%s" class="regular-text">', esc_attr( $input_type ), esc_attr( $id ), esc_attr( $name ), esc_attr( $value ), esc_attr( $dir ) );
	}
}
