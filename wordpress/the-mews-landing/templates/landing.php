<?php
/**
 * Landing page template. Same markup as the static site, with every value from the settings.
 */

defined( 'ABSPATH' ) || exit;

$r        = new TML_Render();
$a        = 'TML_Render::attr';
$general  = tml_section( 'general' );
$nav      = tml_section( 'header' );
$hero     = tml_section( 'hero' );
$stats    = tml_section( 'stats' );
$project  = tml_section( 'project' );
$invest   = tml_section( 'invest' );
$gallery  = tml_section( 'gallery' );
$location = tml_section( 'location' );
$faq      = tml_section( 'faq' );
$cta      = tml_section( 'cta' );
$form     = tml_section( 'form' );
$img      = TML_URL . 'assets/img/';

$title = $r->raw( 'meta.title', $general['meta_title'] );
$desc  = $r->raw( 'meta.description', $general['meta_description'] );
foreach ( array( 'invalid', 'ok', 'error' ) as $msg ) {
	$r->js( 'form.' . $msg, $form[ $msg ] );
}

/** One lead form; the hero and the bottom section use the same markup. */
$lead_form = function ( $id ) use ( $r, $a, $form ) {
	?>
	<form class="lead-form hero-form" id="<?php echo esc_attr( $id ); ?>-form" novalidate>
		<label class="field">
			<span class="sr-only"><?php echo esc_html( rtrim( tml_he( $form['name_ph'] ), ' *' ) ); ?></span>
			<input type="text" id="<?php echo esc_attr( $id ); ?>-name" name="name" placeholder="<?php echo esc_attr( $r->raw( 'form.namePh', $form['name_ph'] ) ); ?>" data-i18n-ph="form.namePh" autocomplete="name" required>
		</label>
		<label class="field">
			<span class="sr-only"><?php echo esc_html( rtrim( tml_he( $form['phone_ph'] ), ' *' ) ); ?></span>
			<input type="tel" id="<?php echo esc_attr( $id ); ?>-phone" name="phone" placeholder="<?php echo esc_attr( $r->raw( 'form.phonePh', $form['phone_ph'] ) ); ?>" data-i18n-ph="form.phonePh" autocomplete="tel" inputmode="tel" required>
		</label>
		<input type="text" name="website" class="tml-hp" tabindex="-1" autocomplete="off" aria-hidden="true">
		<button type="submit" class="btn-send"<?php echo $a( 'form.send' ); ?>><?php echo $r->tr( 'form.send', $form['send'] ); ?></button>
		<p class="consent"<?php echo $a( 'form.consent' ); ?>><?php echo $r->tr( 'form.consent', $form['consent'] ); ?></p>
		<p class="form-msg" role="status" aria-live="polite"></p>
	</form>
	<?php
};

/** The two logos with the developer line. */
$brands = function () use ( $r, $a, $hero, $img ) {
	?>
	<div class="hero-brands">
		<img src="<?php echo esc_url( $img . 'logo-white.svg' ); ?>" alt="The Mews Residences" class="hero-logo">
		<?php if ( $hero['dev_logo'] ) : ?>
			<span class="brands-divider" aria-hidden="true"></span>
			<div class="hero-dev">
				<img src="<?php echo esc_url( tml_url( $hero['dev_logo'] ) ); ?>" alt="D. Zavos Group – Land &amp; Building Developers" class="dev-logo">
				<p<?php echo $a( 'hero.dev' ); ?>><?php echo $r->tr( 'hero.dev', $hero['dev'], true ); ?></p>
			</div>
		<?php endif; ?>
	</div>
	<?php
};

/** Price box shared by the hero and the bottom section. */
$price_box = function () use ( $r, $a, $hero ) {
	?>
	<div class="price-box">
		<span class="pb-text">
			<span<?php echo $a( 'hero.pay' ); ?>><?php echo $r->tr( 'hero.pay', $hero['pay'] ); ?></span>
			<span<?php echo $a( 'hero.secure' ); ?>><?php echo $r->tr( 'hero.secure', $hero['secure'] ); ?></span>
		</span>
		<b><?php echo esc_html( $hero['price'] ); ?></b>
	</div>
	<?php
};

tml_document_head( $title, $desc, tml_url( $general['og_image'] ) );
?>
<body <?php body_class( 'tml-landing' ); ?>>
<?php wp_body_open(); ?>

	<header class="site-header">
		<div class="container header-inner">
			<a href="#top" class="header-logo" aria-label="The Mews Residences">
				<img src="<?php echo esc_url( $img . 'logo-brown.svg' ); ?>" alt="The Mews Residences">
			</a>
			<nav class="header-nav" aria-label="Main">
				<?php
				$links = array(
					'project'  => 'nav_project',
					'invest'   => 'nav_invest',
					'gallery'  => 'nav_gallery',
					'location' => 'nav_location',
					'faq'      => 'nav_faq',
				);
				foreach ( $links as $anchor => $key ) {
					printf( '<a href="#%s"%s>%s</a>', esc_attr( $anchor ), $a( 'nav.' . $anchor ), $r->tr( 'nav.' . $anchor, $nav[ $key ] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</nav>
			<div class="header-actions">
				<button type="button" class="lang-toggle" data-lang-toggle>EN</button>
				<a href="#contact" class="btn btn-small btn-icon">
					<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 20h4L19 9a2.8 2.8 0 0 0-4-4L4 16z"/><path d="M13.5 6.5l4 4"/></svg>
					<span<?php echo $a( 'nav.cta' ); ?>><?php echo $r->tr( 'nav.cta', $nav['nav_cta'] ); ?></span>
				</a>
			</div>
		</div>
	</header>

	<div class="intro">
		<section class="hero" id="top">
			<img src="<?php echo esc_url( $img . 'icon-sand.svg' ); ?>" alt="" class="hero-mark" aria-hidden="true">
			<div class="container hero-grid">
				<div class="hero-main">
					<div class="hero-topbar">
						<button type="button" class="lang-toggle" data-lang-toggle>EN</button>
					</div>
					<h1>
						<span class="h-light"<?php echo $a( 'hero.l1' ); ?>><?php echo $r->tr( 'hero.l1', $hero['l1'] ); ?></span>
						<span class="h-bold"<?php echo $a( 'hero.l2' ); ?>><?php echo $r->tr( 'hero.l2', $hero['l2'] ); ?></span>
					</h1>
					<?php $price_box(); ?>
					<p class="hero-tag"<?php echo $a( 'hero.tag' ); ?>><?php echo $r->tr( 'hero.tag', $hero['tag'] ); ?></p>
					<p class="hero-cta-label"<?php echo $a( 'hero.formLabel' ); ?>><?php echo $r->tr( 'hero.formLabel', $hero['form_label'] ); ?></p>
					<?php $lead_form( 'hero' ); ?>
					<?php $brands(); ?>
				</div>

				<div class="hero-media">
					<div class="media-badge"><img src="<?php echo esc_url( $img . 'logo-brown.svg' ); ?>" alt=""></div>
					<div class="media-frame">
						<div class="media-box">
							<video class="hero-video" autoplay muted loop playsinline preload="auto"
								poster="<?php echo esc_url( tml_url( $hero['poster'] ) ); ?>"
								data-desktop-src="<?php echo esc_url( tml_url( $hero['video_desktop'] ) ); ?>"
								data-mobile-src="<?php echo esc_url( tml_url( $hero['video_mobile'] ? $hero['video_mobile'] : $hero['video_desktop'] ) ); ?>"
								aria-hidden="true"></video>
						</div>
					</div>
					<p class="hero-disclaimer"<?php echo $a( 'hero.disclaimer' ); ?>><?php echo $r->tr( 'hero.disclaimer', $hero['disclaimer'] ); ?></p>
				</div>
			</div>
		</section>

		<section class="stats">
			<div class="container stats-grid">
				<?php foreach ( $stats['items'] as $i => $item ) : ?>
					<div class="stat">
						<b><?php echo esc_html( $item['value'] ); ?><?php if ( tml_he( $item['unit'] ) ) : ?><small<?php echo $a( "stats.u$i" ); ?>><?php echo $r->tr( "stats.u$i", $item['unit'] ); ?></small><?php endif; ?></b>
						<span<?php echo $a( "stats.l$i" ); ?>><?php echo $r->tr( "stats.l$i", $item['label'] ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		</section>
	</div>

	<section class="project" id="project">
		<img src="<?php echo esc_url( $img . 'elevation.svg' ); ?>" alt="" class="project-sketch" aria-hidden="true">
		<div class="project-media">
			<img src="<?php echo esc_url( tml_url( $project['image'] ) ); ?>" alt="" loading="lazy">
		</div>
		<div class="project-panel">
			<img src="<?php echo esc_url( $img . 'icon-brown.svg' ); ?>" alt="" class="section-icon" aria-hidden="true">
			<?php if ( $project['eyebrow'] ) : ?><span class="eyebrow dark"><?php echo esc_html( $project['eyebrow'] ); ?></span><?php endif; ?>
			<h2<?php echo $a( 'project.title' ); ?>><?php echo $r->tr( 'project.title', $project['title'] ); ?></h2>
			<p<?php echo $a( 'project.p1' ); ?>><?php echo $r->tr( 'project.p1', $project['p1'] ); ?></p>
			<?php if ( tml_he( $project['p2'] ) ) : ?>
				<p<?php echo $a( 'project.p2' ); ?>><?php echo $r->tr( 'project.p2', $project['p2'] ); ?></p>
			<?php endif; ?>
			<a href="#contact" class="btn"<?php echo $a( 'project.cta' ); ?>><?php echo $r->tr( 'project.cta', $project['cta'] ); ?></a>
		</div>
	</section>

	<section class="section section-brown" id="invest">
		<img src="<?php echo esc_url( $img . 'icon-brown.svg' ); ?>" alt="" class="bg-mark" aria-hidden="true">
		<div class="container">
			<div class="section-head">
				<?php if ( $invest['eyebrow'] ) : ?><span class="eyebrow"><?php echo esc_html( $invest['eyebrow'] ); ?></span><?php endif; ?>
				<h2<?php echo $a( 'invest.title' ); ?>><?php echo $r->tr( 'invest.title', $invest['title'] ); ?></h2>
				<p<?php echo $a( 'invest.sub' ); ?>><?php echo $r->tr( 'invest.sub', $invest['sub'] ); ?></p>
			</div>
			<div class="cards">
				<?php foreach ( $invest['cards'] as $i => $card ) : ?>
					<article class="card">
						<span class="card-num"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
						<h3<?php echo $a( "invest.t$i" ); ?>><?php echo $r->tr( "invest.t$i", $card['title'] ); ?></h3>
						<p<?php echo $a( "invest.c$i" ); ?>><?php echo $r->tr( "invest.c$i", $card['text'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="section" id="gallery">
		<div class="container">
			<div class="section-head">
				<?php if ( $gallery['eyebrow'] ) : ?><span class="eyebrow dark"><?php echo esc_html( $gallery['eyebrow'] ); ?></span><?php endif; ?>
				<h2<?php echo $a( 'gallery.title' ); ?>><?php echo $r->tr( 'gallery.title', $gallery['title'] ); ?></h2>
			</div>
			<div class="gallery">
				<?php foreach ( $gallery['images'] as $item ) : ?>
					<?php
					if ( ! $item['image'] ) {
						continue;
					}
					$src = tml_url( $item['image'] );
					?>
					<button class="g-item<?php echo $item['wide'] ? ' g-wide' : ''; ?>" data-src="<?php echo esc_url( $src ); ?>"><img src="<?php echo esc_url( $src ); ?>" alt="<?php echo esc_attr( $item['alt'] ); ?>" loading="lazy"></button>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="section section-olive" id="location">
		<div class="container split split-reverse">
			<div class="split-text">
				<?php if ( $location['eyebrow'] ) : ?><span class="eyebrow dark"><?php echo esc_html( $location['eyebrow'] ); ?></span><?php endif; ?>
				<h2<?php echo $a( 'loc.title' ); ?>><?php echo $r->tr( 'loc.title', $location['title'] ); ?></h2>
				<p<?php echo $a( 'loc.p' ); ?>><?php echo $r->tr( 'loc.p', $location['text'] ); ?></p>
				<ul class="distances">
					<?php foreach ( $location['distances'] as $i => $d ) : ?>
						<li><b<?php echo $a( "loc.v$i" ); ?>><?php echo $r->tr( "loc.v$i", $d['value'] ); ?></b><span<?php echo $a( "loc.l$i" ); ?>><?php echo $r->tr( "loc.l$i", $d['label'] ); ?></span></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<div class="split-media">
				<img src="<?php echo esc_url( tml_url( $location['image'] ) ); ?>" alt="" loading="lazy">
			</div>
		</div>
	</section>

	<section class="section" id="faq">
		<div class="container narrow">
			<div class="section-head">
				<?php if ( $faq['eyebrow'] ) : ?><span class="eyebrow dark"><?php echo esc_html( $faq['eyebrow'] ); ?></span><?php endif; ?>
				<h2<?php echo $a( 'faq.title' ); ?>><?php echo $r->tr( 'faq.title', $faq['title'] ); ?></h2>
			</div>
			<div class="faq">
				<?php foreach ( $faq['items'] as $i => $item ) : ?>
					<details>
						<summary<?php echo $a( "faq.q$i" ); ?>><?php echo $r->tr( "faq.q$i", $item['q'] ); ?></summary>
						<p<?php echo $a( "faq.a$i" ); ?>><?php echo $r->tr( "faq.a$i", $item['a'] ); ?></p>
					</details>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="cta" id="contact">
		<div class="cta-bg" aria-hidden="true" style="background-image:url('<?php echo esc_url( tml_url( $cta['background'] ) ); ?>')"></div>
		<div class="cta-overlay" aria-hidden="true"></div>
		<div class="container cta-inner">
			<h2 class="cta-title">
				<span class="h-light"<?php echo $a( 'cta.l1' ); ?>><?php echo $r->tr( 'cta.l1', $cta['l1'] ); ?></span>
				<span class="h-bold"<?php echo $a( 'cta.l2' ); ?>><?php echo $r->tr( 'cta.l2', $cta['l2'] ); ?></span>
			</h2>
			<?php $price_box(); ?>
			<p class="hero-tag"<?php echo $a( 'hero.tag' ); ?>><?php echo $r->tr( 'hero.tag', $hero['tag'] ); ?></p>
			<p class="hero-cta-label"<?php echo $a( 'hero.formLabel' ); ?>><?php echo $r->tr( 'hero.formLabel', $hero['form_label'] ); ?></p>
			<?php $lead_form( 'contact' ); ?>
			<?php $brands(); ?>
		</div>
	</section>

	<?php tml_footer( $r ); ?>

	<a class="whatsapp" id="whatsapp" href="#" target="_blank" rel="noopener" aria-label="WhatsApp">
		<svg viewBox="0 0 32 32" aria-hidden="true"><path fill="currentColor" d="M16 3C8.8 3 3 8.7 3 15.8c0 2.5.7 4.9 2 7L3 29l6.4-2c2 1.1 4.3 1.7 6.6 1.7 7.2 0 13-5.7 13-12.8S23.2 3 16 3zm0 23.4c-2.1 0-4.1-.6-5.9-1.7l-.4-.3-3.8 1.2 1.2-3.7-.3-.4c-1.2-1.8-1.9-3.9-1.9-6 0-5.9 4.9-10.6 11-10.6s11 4.8 11 10.6-4.9 10.9-10.9 10.9zm6-7.9c-.3-.2-1.9-.9-2.2-1s-.5-.2-.7.2-.8 1-1 1.2-.4.2-.7.1c-.3-.2-1.4-.5-2.6-1.6-1-.9-1.6-1.9-1.8-2.3-.2-.3 0-.5.1-.7l.5-.6c.2-.2.2-.4.3-.6.1-.2.1-.4 0-.6l-1-2.4c-.3-.6-.5-.5-.7-.5h-.6c-.2 0-.6.1-.9.4s-1.2 1.1-1.2 2.7 1.2 3.2 1.4 3.4c.2.2 2.4 3.6 5.8 5 .8.3 1.4.5 1.9.7.8.3 1.6.2 2.2.1.7-.1 1.9-.8 2.2-1.5.3-.7.3-1.4.2-1.5-.1-.2-.3-.3-.6-.5z"/></svg>
	</a>
	<a href="#contact" class="mobile-cta"<?php echo $a( 'mobileCta' ); ?>><?php echo $r->tr( 'mobileCta', $nav['mobile_cta'] ); ?></a>

	<div class="lightbox" id="lightbox" hidden>
		<button class="lb-close" aria-label="Close">×</button>
		<img src="" alt="">
	</div>

<?php
// Strings and settings for main.js. Added now because the template has just collected them.
$config = array(
	'whatsappNumber' => preg_replace( '/\D/', '', $general['whatsapp'] ),
	'whatsappText'   => array(
		'he' => tml_he( $general['whatsapp_text'] ),
		'en' => is_array( $general['whatsapp_text'] ) && $general['whatsapp_text']['en'] ? $general['whatsapp_text']['en'] : tml_he( $general['whatsapp_text'] ),
	),
	'formEndpoint'   => rest_url( 'tml/v1/lead' ),
);
wp_add_inline_script(
	'tml-main',
	'window.TML_CONFIG = ' . wp_json_encode( $config ) . ";\n" .
	'const I18N_EN = ' . wp_json_encode( $r->en ) . ";\n" .
	'const I18N_HE = ' . wp_json_encode( $r->he ) . ';',
	'before'
);
wp_footer();
?>
</body>
</html>
