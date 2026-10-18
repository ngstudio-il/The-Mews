<?php
/**
 * Helpers for the front-end templates.
 *
 * Hebrew is printed into the page; the English value of every translatable field is
 * collected under the same data-i18n key and handed to main.js, which swaps the page
 * to English when the visitor presses EN.
 */

defined( 'ABSPATH' ) || exit;

class TML_Render {

	/** English strings, keyed like the data-i18n attributes. */
	public $en = array();

	/** Hebrew strings main.js needs that are not printed in the markup (form messages). */
	public $he = array();

	/**
	 * Prints the Hebrew value of a translatable field and records its English value.
	 * Returns the data-i18n attribute to place on the element.
	 */
	public function tr( $key, $value, $html = false ) {
		$value = is_array( $value ) ? $value : array( 'he' => (string) $value, 'en' => (string) $value );
		$he    = isset( $value['he'] ) ? $value['he'] : '';
		$en    = isset( $value['en'] ) && '' !== $value['en'] ? $value['en'] : $he;

		$this->en[ $key ] = $html ? wp_kses( $en, tml_inline_tags() ) : esc_html( $en );
		return $html ? wp_kses( $he, tml_inline_tags() ) : esc_html( $he );
	}

	/**
	 * For text the browser treats as plain text (placeholders, the page title): returns the
	 * Hebrew value unescaped (the caller escapes it for its attribute) and records the English.
	 */
	public function raw( $key, $value ) {
		$he               = tml_he( $value );
		$this->en[ $key ] = is_array( $value ) && '' !== $value['en'] ? $value['en'] : $he;
		return $he;
	}

	/** Records a string that only lives in JavaScript (form messages). */
	public function js( $key, $value ) {
		$this->he[ $key ] = isset( $value['he'] ) ? $value['he'] : '';
		$this->en[ $key ] = isset( $value['en'] ) && '' !== $value['en'] ? $value['en'] : $this->he[ $key ];
	}

	/** Attribute for an element whose text was printed with tr(). */
	public static function attr( $key ) {
		return ' data-i18n="' . esc_attr( $key ) . '"';
	}
}

/** Hebrew value of a translatable field, for places that are not swapped (alt text, meta). */
function tml_he( $value ) {
	return is_array( $value ) ? ( isset( $value['he'] ) ? $value['he'] : '' ) : (string) $value;
}

/** Shared page header for the plugin templates. */
function tml_document_head( $title, $description, $og_image ) {
	?>
<!doctype html>
<html lang="he" dir="rtl">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo esc_html( $title ); ?></title>
	<?php if ( $description ) : ?>
		<meta name="description" content="<?php echo esc_attr( $description ); ?>">
		<meta property="og:description" content="<?php echo esc_attr( $description ); ?>">
	<?php endif; ?>
	<meta property="og:title" content="<?php echo esc_attr( $title ); ?>">
	<?php if ( $og_image ) : ?>
		<meta property="og:image" content="<?php echo esc_url( $og_image ); ?>">
	<?php endif; ?>
	<?php if ( ! has_site_icon() ) : ?>
		<link rel="icon" href="<?php echo esc_url( TML_URL . 'assets/img/icon-brown.svg' ); ?>" type="image/svg+xml">
	<?php endif; ?>
	<?php wp_head(); ?>
</head>
	<?php
}

/** Our templates print their own <title>; keep WordPress from printing a second one. */
add_action(
	'wp',
	function () {
		if ( tml_current_template() ) {
			remove_action( 'wp_head', '_wp_render_title_tag', 1 );
		}
	}
);

/** Footer shared by the landing page and the legal pages. */
function tml_footer( TML_Render $r = null ) {
	$f       = tml_section( 'footer' );
	$privacy = $r ? $r->tr( 'footer.privacy', $f['privacy_label'] ) : esc_html( tml_he( $f['privacy_label'] ) );
	$a11y    = $r ? $r->tr( 'footer.a11y', $f['a11y_label'] ) : esc_html( tml_he( $f['a11y_label'] ) );
	?>
	<footer class="site-footer">
		<div class="container">
			<img src="<?php echo esc_url( TML_URL . 'assets/img/logo-white.svg' ); ?>" alt="The Mews Residences" class="footer-logo">
			<?php if ( $f['dev_line'] ) : ?>
				<p class="footer-dev"><?php echo esc_html( $f['dev_line'] ); ?></p>
			<?php endif; ?>
			<ul class="footer-links">
				<li><a href="<?php echo esc_url( tml_page_url( 'privacy' ) ); ?>"<?php echo $r ? TML_Render::attr( 'footer.privacy' ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo $privacy; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
				<li><a href="<?php echo esc_url( tml_page_url( 'accessibility' ) ); ?>"<?php echo $r ? TML_Render::attr( 'footer.a11y' ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo $a11y; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
			</ul>
			<?php if ( $r ) : ?>
				<p class="disclaimer"<?php echo TML_Render::attr( 'footer.disclaimer' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo $r->tr( 'footer.disclaimer', $f['disclaimer'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<?php endif; ?>
			<p class="copy">© <?php echo esc_html( gmdate( 'Y' ) ); ?> The Mews Residences</p>
		</div>
	</footer>
	<?php
}
