<?php
/**
 * Content page in the landing page's style (privacy policy, accessibility statement).
 * The text is the page's own content, edited in the regular WordPress editor.
 */

defined( 'ABSPATH' ) || exit;

$landing = (int) get_option( 'tml_landing_page' );
$home    = $landing ? get_permalink( $landing ) : home_url( '/' );

the_post();
tml_document_head( get_the_title() . ' | The Mews Residences', '', '' );
?>
<body <?php body_class( 'legal-page' ); ?>>
<?php wp_body_open(); ?>

	<header class="legal-header">
		<div class="container header-inner">
			<a href="<?php echo esc_url( $home ); ?>" class="header-logo" aria-label="The Mews Residences – חזרה לדף הבית">
				<img src="<?php echo esc_url( TML_URL . 'assets/img/logo-brown.svg' ); ?>" alt="The Mews Residences">
			</a>
			<a href="<?php echo esc_url( $home ); ?>" class="legal-back">← חזרה לדף הבית</a>
		</div>
	</header>

	<main class="legal">
		<div class="container narrow">
			<h1><?php the_title(); ?></h1>
			<p class="updated">עודכן לאחרונה: <?php echo esc_html( get_the_modified_date( 'd/m/Y' ) ); ?></p>
			<?php the_content(); ?>
		</div>
	</main>

	<?php tml_footer(); ?>

<?php wp_footer(); ?>
</body>
</html>
