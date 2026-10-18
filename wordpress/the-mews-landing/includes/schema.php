<?php
/**
 * Editable content of the landing page, grouped into sections (one admin tab each).
 *
 * Field types: text, textarea, html (a few inline tags allowed), url, email, image, video, repeater.
 * 'tr' => true marks a field that has a Hebrew and an English value.
 * Defaults that start with "asset:" point at files bundled in the plugin's assets folder.
 */

defined( 'ABSPATH' ) || exit;

function tml_tr( $he, $en ) {
	return array( 'he' => $he, 'en' => $en );
}

function tml_schema() {
	static $schema = null;
	if ( null !== $schema ) {
		return $schema;
	}

	$schema = array(

		'general' => array(
			'title'  => 'הגדרות כלליות',
			'fields' => array(
				array( 'key' => 'notify_email', 'type' => 'email', 'label' => 'מייל לקבלת לידים', 'help' => 'כל ליד חדש יישלח לכתובת הזו. ריק = מייל מנהל האתר.', 'default' => '' ),
				array( 'key' => 'webhook', 'type' => 'url', 'label' => 'Webhook לליד (לא חובה)', 'help' => 'כתובת CRM / Zapier / Make שתקבל כל ליד כ-JSON.', 'default' => '' ),
				array( 'key' => 'whatsapp', 'type' => 'text', 'label' => 'מספר וואטסאפ', 'help' => 'בפורמט בינלאומי, ספרות בלבד. לדוגמה 972501234567. ריק = הכפתור גולל לטופס.', 'default' => '' ),
				array( 'key' => 'whatsapp_text', 'type' => 'text', 'tr' => true, 'label' => 'נוסח הודעת וואטסאפ', 'default' => tml_tr( 'היי, אשמח לקבל פרטים על פרויקט The Mews בלימסול', "Hi, I'd like details about The Mews project in Limassol" ) ),
				array( 'key' => 'meta_title', 'type' => 'text', 'tr' => true, 'label' => 'כותרת הדף (SEO)', 'default' => tml_tr( 'The Mews Residences | דירות להשקעה בלימסול, קפריסין', 'The Mews Residences | Investment Apartments in Limassol, Cyprus' ) ),
				array( 'key' => 'meta_description', 'type' => 'textarea', 'tr' => true, 'label' => 'תיאור הדף (SEO)', 'default' => tml_tr( 'The Mews Residences – פרויקט בוטיק של 30 דירות 2 חדרי שינה במיקום מרכזי בלימסול, קפריסין. משלמים רק 30,000 € ומבטיחים דירה. השאירו פרטים לקבלת מחירון.', 'The Mews Residences – a boutique project of 30 two-bedroom apartments in central Limassol, Cyprus. Pay only €30,000 to secure your apartment. Leave your details for the price list.' ) ),
				array( 'key' => 'og_image', 'type' => 'image', 'label' => 'תמונה לשיתוף ברשתות', 'default' => 'asset:img/exterior.jpg' ),
			),
		),

		'header' => array(
			'title'  => 'תפריט עליון',
			'fields' => array(
				array( 'key' => 'nav_project', 'type' => 'text', 'tr' => true, 'label' => 'קישור: הפרויקט', 'default' => tml_tr( 'הפרויקט', 'The Project' ) ),
				array( 'key' => 'nav_invest', 'type' => 'text', 'tr' => true, 'label' => 'קישור: למה להשקיע', 'default' => tml_tr( 'למה להשקיע', 'Why Invest' ) ),
				array( 'key' => 'nav_gallery', 'type' => 'text', 'tr' => true, 'label' => 'קישור: גלריה', 'default' => tml_tr( 'גלריה', 'Gallery' ) ),
				array( 'key' => 'nav_location', 'type' => 'text', 'tr' => true, 'label' => 'קישור: מיקום', 'default' => tml_tr( 'מיקום', 'Location' ) ),
				array( 'key' => 'nav_faq', 'type' => 'text', 'tr' => true, 'label' => 'קישור: שאלות נפוצות', 'default' => tml_tr( 'שאלות נפוצות', 'FAQ' ) ),
				array( 'key' => 'nav_cta', 'type' => 'text', 'tr' => true, 'label' => 'כפתור', 'default' => tml_tr( 'להשארת פרטים', 'Leave Your Details' ) ),
				array( 'key' => 'mobile_cta', 'type' => 'text', 'tr' => true, 'label' => 'פס תחתון בנייד', 'default' => tml_tr( 'משלמים רק €30,000 – לפרטים', 'Only €30,000 – Learn More' ) ),
			),
		),

		'hero' => array(
			'title'  => 'פתיח',
			'fields' => array(
				array( 'key' => 'l1', 'type' => 'text', 'tr' => true, 'label' => 'כותרת – שורה ראשונה (דקה)', 'default' => tml_tr( 'להשקיע היום', 'Invest today' ) ),
				array( 'key' => 'l2', 'type' => 'text', 'tr' => true, 'label' => 'כותרת – שורה שנייה (בולטת)', 'default' => tml_tr( 'בלב לימסול, קפריסין', 'in the heart of Limassol, Cyprus' ) ),
				array( 'key' => 'price', 'type' => 'text', 'label' => 'מחיר בתיבה', 'default' => '€30,000' ),
				array( 'key' => 'pay', 'type' => 'text', 'tr' => true, 'label' => 'טקסט מעל המחיר', 'default' => tml_tr( 'משלמים רק', 'Pay only' ) ),
				array( 'key' => 'secure', 'type' => 'text', 'tr' => true, 'label' => 'טקסט מתחת למחיר', 'default' => tml_tr( 'ומבטיחים דירה', 'to secure your apartment' ) ),
				array( 'key' => 'tag', 'type' => 'text', 'tr' => true, 'label' => 'שורת משנה', 'default' => tml_tr( 'מחירי השקה לרוכשים הראשונים · 30 דירות 2 חדרי שינה בלבד', 'Launch prices for first buyers · Only 30 two-bedroom apartments' ) ),
				array( 'key' => 'form_label', 'type' => 'text', 'tr' => true, 'label' => 'כותרת הטופס', 'default' => tml_tr( 'לשיחה עם נציג/ה מטעם הפרויקט השאירו פרטים:', 'Leave your details to speak with a project representative:' ) ),
				array( 'key' => 'dev_logo', 'type' => 'image', 'label' => 'לוגו היזם', 'default' => 'asset:img/zavos-logo.png' ),
				array( 'key' => 'dev', 'type' => 'html', 'tr' => true, 'label' => 'טקסט ליד לוגו היזם', 'help' => 'מותר: <b> ו-<br>', 'default' => tml_tr( 'היזם הגדול בקפריסין<br>למעלה מ-<b>500</b> פרויקטים בלימסול · למעלה מ-<b>45</b> שנות מוניטין', "Cyprus's largest developer<br><b>500+</b> projects in Limassol · <b>45+</b> years of reputation" ) ),
				array( 'key' => 'video_desktop', 'type' => 'video', 'label' => 'סרטון – מחשב (MP4, 16:9)', 'default' => 'asset:video/hero-desktop.mp4' ),
				array( 'key' => 'video_mobile', 'type' => 'video', 'label' => 'סרטון – נייד (MP4, אנכי)', 'default' => 'asset:video/hero-mobile.mp4' ),
				array( 'key' => 'poster', 'type' => 'image', 'label' => 'תמונה לפני טעינת הסרטון', 'default' => 'asset:img/hero-poster.jpg' ),
				array( 'key' => 'disclaimer', 'type' => 'text', 'tr' => true, 'label' => 'שורת הבהרה מתחת לסרטון', 'default' => tml_tr( 'ההדמיות להמחשה בלבד. המחיר והתנאים כפופים להסכם הרכישה. ט.ל.ח.', 'Renders are for illustration only. Price and terms are subject to the purchase agreement. E&OE.' ) ),
			),
		),

		'stats' => array(
			'title'  => 'פס נתונים',
			'fields' => array(
				array(
					'key'     => 'items',
					'type'    => 'repeater',
					'label'   => 'נתונים',
					'add'     => 'הוספת נתון',
					'fields'  => array(
						array( 'key' => 'value', 'type' => 'text', 'label' => 'מספר', 'default' => '' ),
						array( 'key' => 'unit', 'type' => 'text', 'tr' => true, 'label' => 'יחידה קטנה (לא חובה)', 'default' => tml_tr( '', '' ) ),
						array( 'key' => 'label', 'type' => 'text', 'tr' => true, 'label' => 'כיתוב', 'default' => tml_tr( '', '' ) ),
					),
					'default' => array(
						array( 'value' => '30', 'unit' => tml_tr( '', '' ), 'label' => tml_tr( 'דירות בלבד', 'Apartments only' ) ),
						array( 'value' => '5', 'unit' => tml_tr( '', '' ), 'label' => tml_tr( 'קומות', 'Floors' ) ),
						array( 'value' => '2', 'unit' => tml_tr( '', '' ), 'label' => tml_tr( 'חדרי שינה בכל דירה', 'Bedrooms in every apartment' ) ),
						array( 'value' => '1.5', 'unit' => tml_tr( 'ק"מ', 'km' ), 'label' => tml_tr( 'מחוף הים', 'From the beach' ) ),
						array( 'value' => '€30K', 'unit' => tml_tr( '', '' ), 'label' => tml_tr( 'תשלום בלבד לכניסה', 'To get started' ) ),
					),
				),
			),
		),

		'project' => array(
			'title'  => 'הפרויקט',
			'fields' => array(
				array( 'key' => 'image', 'type' => 'image', 'label' => 'תמונה (חצי מסך)', 'default' => 'asset:img/facade.jpg' ),
				array( 'key' => 'eyebrow', 'type' => 'text', 'label' => 'כיתוב קטן (אנגלית)', 'default' => 'The Development' ),
				array( 'key' => 'title', 'type' => 'text', 'tr' => true, 'label' => 'כותרת', 'default' => tml_tr( 'מעוצב לחיים מודרניים', 'Designed for Modern Living' ) ),
				array( 'key' => 'p1', 'type' => 'textarea', 'tr' => true, 'label' => 'פסקה ראשונה', 'default' => tml_tr( 'The Mews Residences הוא פרויקט מגורים עכשווי בן חמש קומות, הכולל 30 דירות של 2 חדרי שינה. קווים אדריכליים נקיים, גימורים אלגנטיים ותכנון פונקציונלי – פרויקט שתוכנן עבור מי שמחפש בית איכותי ועבור משקיעים המחפשים ערך לטווח ארוך.', 'The Mews Residences is a contemporary five-storey residential development comprising 30 two-bedroom apartments. Defined by clean architectural lines, elegant finishes and functional layouts, the project has been designed to meet the needs of both homeowners and investors seeking quality, comfort and long-term value.' ) ),
				array( 'key' => 'p2', 'type' => 'textarea', 'tr' => true, 'label' => 'פסקה שנייה', 'default' => tml_tr( 'כל דירה תוכננה למקסם אור טבעי, נוחות ופרקטיות. חומרים איכותיים, מפרט מודרני וגימורים שנבחרו בקפידה יוצרים חללי מגורים מזמינים – בין אם כבית ובין אם כנכס להשקעה.', 'Every residence has been planned to maximise natural light, practicality and everyday comfort. High-quality materials, modern specifications and carefully selected finishes create welcoming living spaces, whether as a primary residence or an investment property.' ) ),
				array( 'key' => 'cta', 'type' => 'text', 'tr' => true, 'label' => 'כפתור', 'default' => tml_tr( 'אני רוצה לשמוע עוד', 'Tell Me More' ) ),
			),
		),

		'invest' => array(
			'title'  => 'למה דווקא',
			'fields' => array(
				array( 'key' => 'eyebrow', 'type' => 'text', 'label' => 'כיתוב קטן (אנגלית)', 'default' => 'A Smart Investment' ),
				array( 'key' => 'title', 'type' => 'text', 'tr' => true, 'label' => 'כותרת', 'default' => tml_tr( 'למה דווקא The Mews?', 'Why The Mews?' ) ),
				array( 'key' => 'sub', 'type' => 'textarea', 'tr' => true, 'label' => 'טקסט מתחת לכותרת', 'default' => tml_tr( 'מיקום מרכזי, עיצוב עכשווי וביקוש גבוה לדיור באחד האזורים המבוקשים בלימסול.', "A central location, contemporary design and strong demand for housing in one of Limassol's most desirable residential areas." ) ),
				array(
					'key'     => 'cards',
					'type'    => 'repeater',
					'label'   => 'כרטיסים',
					'add'     => 'הוספת כרטיס',
					'fields'  => array(
						array( 'key' => 'title', 'type' => 'text', 'tr' => true, 'label' => 'כותרת', 'default' => tml_tr( '', '' ) ),
						array( 'key' => 'text', 'type' => 'textarea', 'tr' => true, 'label' => 'טקסט', 'default' => tml_tr( '', '' ) ),
					),
					'default' => array(
						array( 'title' => tml_tr( 'כניסה בהון נמוך', 'Low Entry Point' ), 'text' => tml_tr( 'משלמים רק €30,000 ומבטיחים דירה בפרויקט – הדרך הנגישה להיכנס לשוק הנדל"ן בלימסול.', 'Pay only €30,000 to secure an apartment in the project, an accessible way into the Limassol property market.' ) ),
						array( 'title' => tml_tr( 'מיקום מרכזי', 'Central Location' ), 'text' => tml_tr( 'בלב Mesa Geitonia: בתי ספר, סופרמרקטים, בתי קפה ומסעדות במרחק הליכה, והים, הכביש המהיר ומרכז העיר במרחק דקות.', 'In the heart of Mesa Geitonia: schools, supermarkets, cafés and restaurants within walking distance, with the beach, highway and city centre minutes away.' ) ),
						array( 'title' => tml_tr( 'רכישה על הנייר', 'Off-Plan Purchase' ), 'text' => tml_tr( 'רכישה בשלב מוקדם של הפרויקט, עם פוטנציאל עליית ערך עד להשלמת הבנייה ולאורך זמן.', 'Buy at an early stage of the project, with growth potential through completion and beyond.' ) ),
						array( 'title' => tml_tr( 'ביקוש גבוה להשכרה', 'Strong Rental Demand' ), 'text' => tml_tr( 'לימסול היא מרכז עסקי ותיירותי מוביל בקפריסין, עם ביקוש מתמיד לדירות 2 חדרי שינה.', 'Limassol is a leading business and tourism hub in Cyprus, with steady demand for two-bedroom apartments.' ) ),
						array( 'title' => tml_tr( 'מדינה באיחוד האירופי', 'An EU Member State' ), 'text' => tml_tr( 'השקעה ביורו, במדינה חברה באיחוד האירופי, טיסה קצרה מישראל.', 'Invest in euros, in a European Union member state, a short flight from Israel.' ) ),
						array( 'title' => tml_tr( 'היזם הגדול בקפריסין', "Cyprus's Largest Developer" ), 'text' => tml_tr( 'הפרויקט מוקם על ידי D. Zavos Group – היזם הגדול בקפריסין, עם למעלה מ-500 פרויקטים בלימסול ולמעלה מ-45 שנות מוניטין.', 'The project is developed by D. Zavos Group, the largest developer in Cyprus, with more than 500 projects in Limassol and over 45 years of reputation.' ) ),
					),
				),
			),
		),

		'gallery' => array(
			'title'  => 'גלריה',
			'fields' => array(
				array( 'key' => 'eyebrow', 'type' => 'text', 'label' => 'כיתוב קטן (אנגלית)', 'default' => 'Bright Spaces for Everyday Comfort' ),
				array( 'key' => 'title', 'type' => 'text', 'tr' => true, 'label' => 'כותרת', 'default' => tml_tr( 'חללים מוארים לנוחות יומיומית', 'Bright Spaces for Everyday Comfort' ) ),
				array(
					'key'     => 'images',
					'type'    => 'repeater',
					'label'   => 'תמונות',
					'add'     => 'הוספת תמונה',
					'help'    => 'תמונה רחבה תופסת שתי עמודות. הסדר הטוב: רחבה, רגילה, רגילה, רגילה, רגילה, רחבה, רגילה.',
					'fields'  => array(
						array( 'key' => 'image', 'type' => 'image', 'label' => 'תמונה', 'default' => '' ),
						array( 'key' => 'alt', 'type' => 'text', 'label' => 'תיאור התמונה (לנגישות)', 'default' => '' ),
						array( 'key' => 'wide', 'type' => 'checkbox', 'label' => 'תמונה רחבה', 'default' => '' ),
					),
					'default' => array(
						array( 'image' => 'asset:img/exterior.jpg', 'alt' => 'Building exterior render', 'wide' => '1' ),
						array( 'image' => 'asset:img/kitchen.jpg', 'alt' => 'Kitchen and living room render', 'wide' => '' ),
						array( 'image' => 'asset:img/living.jpg', 'alt' => 'Living room with balcony render', 'wide' => '' ),
						array( 'image' => 'asset:img/lounge.jpg', 'alt' => 'Living space render', 'wide' => '' ),
						array( 'image' => 'asset:img/bedroom.jpg', 'alt' => 'Bedroom render', 'wide' => '' ),
						array( 'image' => 'asset:img/night.jpg', 'alt' => 'Building at night render', 'wide' => '1' ),
						array( 'image' => 'asset:img/sunset.jpg', 'alt' => 'Building at sunset render', 'wide' => '' ),
					),
				),
			),
		),

		'location' => array(
			'title'  => 'מיקום',
			'fields' => array(
				array( 'key' => 'image', 'type' => 'image', 'label' => 'תמונה', 'default' => 'asset:img/aerial.jpg' ),
				array( 'key' => 'eyebrow', 'type' => 'text', 'label' => 'כיתוב קטן (אנגלית)', 'default' => 'Everything You Need Within Reach' ),
				array( 'key' => 'title', 'type' => 'text', 'tr' => true, 'label' => 'כותרת', 'default' => tml_tr( 'הכל במרחק נגיעה', 'Everything You Need Within Reach' ) ),
				array( 'key' => 'text', 'type' => 'textarea', 'tr' => true, 'label' => 'טקסט', 'default' => tml_tr( 'The Mews Residences ממוקם בלב שכונת Mesa Geitonia בלימסול – כתובת מרכזית עם כל מה שצריך בקרבת מקום. חיבור מצוין לכל חלקי העיר הופך את שגרת היום-יום לפשוטה.', 'Located in the heart of Mesa Geitonia, The Mews Residences offers a central address with everything close at hand. Excellent connectivity to every part of the city makes daily life effortless.' ) ),
				array(
					'key'     => 'distances',
					'type'    => 'repeater',
					'label'   => 'מרחקים',
					'add'     => 'הוספת מרחק',
					'fields'  => array(
						array( 'key' => 'value', 'type' => 'text', 'tr' => true, 'label' => 'מרחק', 'default' => tml_tr( '', '' ) ),
						array( 'key' => 'label', 'type' => 'text', 'tr' => true, 'label' => 'ממה', 'default' => tml_tr( '', '' ) ),
					),
					'default' => array(
						array( 'value' => tml_tr( "50 מ'", '50 m' ), 'label' => tml_tr( 'משירותים ומסחר', 'From amenities' ) ),
						array( 'value' => tml_tr( "10 מ'", '10 m' ), 'label' => tml_tr( 'מתחבורה ציבורית', 'From public transport' ) ),
						array( 'value' => tml_tr( '1.3 ק"מ', '1.3 km' ), 'label' => tml_tr( 'מבית ספר', 'From school' ) ),
						array( 'value' => tml_tr( '1.5 ק"מ', '1.5 km' ), 'label' => tml_tr( 'מחוף הים', 'From the beach' ) ),
						array( 'value' => tml_tr( '14 ק"מ', '14 km' ), 'label' => tml_tr( 'מאזור הנופש', 'From the resort' ) ),
						array( 'value' => tml_tr( '62 ק"מ', '62 km' ), 'label' => tml_tr( 'משדה התעופה', 'From the airport' ) ),
					),
				),
			),
		),

		'faq' => array(
			'title'  => 'שאלות נפוצות',
			'fields' => array(
				array( 'key' => 'eyebrow', 'type' => 'text', 'label' => 'כיתוב קטן (אנגלית)', 'default' => 'FAQ' ),
				array( 'key' => 'title', 'type' => 'text', 'tr' => true, 'label' => 'כותרת', 'default' => tml_tr( 'שאלות נפוצות', 'Frequently Asked Questions' ) ),
				array(
					'key'     => 'items',
					'type'    => 'repeater',
					'label'   => 'שאלות',
					'add'     => 'הוספת שאלה',
					'fields'  => array(
						array( 'key' => 'q', 'type' => 'text', 'tr' => true, 'label' => 'שאלה', 'default' => tml_tr( '', '' ) ),
						array( 'key' => 'a', 'type' => 'textarea', 'tr' => true, 'label' => 'תשובה', 'default' => tml_tr( '', '' ) ),
					),
					'default' => array(
						array( 'q' => tml_tr( 'היכן ממוקם הפרויקט?', 'Where is the project located?' ), 'a' => tml_tr( 'הפרויקט ממוקם בשכונת Mesa Geitonia בלימסול, קפריסין – 50 מטר משירותים ומסחר ו-1.5 ק"מ בלבד מחוף הים.', 'The project is in Mesa Geitonia, Limassol, Cyprus: 50 metres from amenities and only 1.5 km from the beach.' ) ),
						array( 'q' => tml_tr( 'כמה צריך לשלם כדי להיכנס לפרויקט?', 'How much do I need to pay to get started?' ), 'a' => tml_tr( 'משלמים רק €30,000 ומבטיחים את הדירה. את פרטי פריסת התשלומים המלאה תקבלו מהיועץ שלנו.', 'Pay only €30,000 to secure your apartment. Our advisor will give you the full payment schedule.' ) ),
						array( 'q' => tml_tr( 'אילו דירות יש בפרויקט?', 'What apartments are in the project?' ), 'a' => tml_tr( 'הפרויקט כולל 30 דירות של 2 חדרי שינה בבניין בן חמש קומות, עם מרפסות, אור טבעי וגימורים איכותיים.', 'The project has 30 two-bedroom apartments in a five-storey building, with balconies, natural light and quality finishes.' ) ),
						array( 'q' => tml_tr( 'האם ישראלים יכולים לרכוש דירה בקפריסין?', 'Can foreigners buy property in Cyprus?' ), 'a' => tml_tr( 'כן. קפריסין היא חברה באיחוד האירופי ורוכשים זרים, כולל ישראלים, יכולים לרכוש בה נדל"ן. נשמח ללוות אתכם בכל שלבי התהליך.', 'Yes. Cyprus is an EU member state and foreign buyers, including Israelis, can purchase property there. We will guide you through every step of the process.' ) ),
						array( 'q' => tml_tr( 'האם הדירה מתאימה להשכרה?', 'Is the apartment suitable for renting out?' ), 'a' => tml_tr( 'כן. דירות 2 חדרי שינה במיקום מרכזי בלימסול נהנות מביקוש גבוה להשכרה, הן לטווח ארוך והן לטווח קצר.', 'Yes. Two-bedroom apartments in central Limassol are in high demand for both long-term and short-term rentals.' ) ),
					),
				),
			),
		),

		'cta' => array(
			'title'  => 'אזור תחתון',
			'fields' => array(
				array( 'key' => 'l1', 'type' => 'text', 'tr' => true, 'label' => 'כותרת – שורה ראשונה (דקה)', 'default' => tml_tr( 'הדירות הראשונות', 'The first apartments' ) ),
				array( 'key' => 'l2', 'type' => 'text', 'tr' => true, 'label' => 'כותרת – שורה שנייה (בולטת)', 'default' => tml_tr( 'נמכרות במחירי השקה', 'are selling at launch prices' ) ),
				array( 'key' => 'background', 'type' => 'image', 'label' => 'תמונת רקע (קבועה בגלילה)', 'default' => 'asset:img/night.jpg' ),
			),
		),

		'form' => array(
			'title'  => 'טפסים',
			'fields' => array(
				array( 'key' => 'name_ph', 'type' => 'text', 'tr' => true, 'label' => 'שדה שם', 'default' => tml_tr( 'שם מלא *', 'Full name *' ) ),
				array( 'key' => 'phone_ph', 'type' => 'text', 'tr' => true, 'label' => 'שדה טלפון', 'default' => tml_tr( 'טלפון *', 'Phone *' ) ),
				array( 'key' => 'send', 'type' => 'text', 'tr' => true, 'label' => 'כפתור שליחה', 'default' => tml_tr( 'שליחה', 'Send' ) ),
				array( 'key' => 'consent', 'type' => 'text', 'tr' => true, 'label' => 'שורת הסכמה', 'default' => tml_tr( 'בשליחת הפרטים אני מאשר/ת קבלת פניות ומידע שיווקי', 'By sending your details you agree to be contacted and to receive marketing information' ) ),
				array( 'key' => 'invalid', 'type' => 'text', 'tr' => true, 'label' => 'הודעת שגיאה בשדות', 'default' => tml_tr( 'נא למלא שם וטלפון תקינים', 'Please enter a valid name and phone number' ) ),
				array( 'key' => 'ok', 'type' => 'text', 'tr' => true, 'label' => 'הודעת תודה', 'default' => tml_tr( 'תודה! הפרטים התקבלו ונחזור אליכם בהקדם.', 'Thank you! We received your details and will be in touch shortly.' ) ),
				array( 'key' => 'error', 'type' => 'text', 'tr' => true, 'label' => 'הודעת תקלה', 'default' => tml_tr( 'אירעה שגיאה בשליחה, נסו שוב או פנו אלינו בוואטסאפ.', 'Something went wrong. Please try again or contact us on WhatsApp.' ) ),
			),
		),

		'footer' => array(
			'title'  => 'פוטר',
			'fields' => array(
				array( 'key' => 'dev_line', 'type' => 'text', 'label' => 'שורת יזם', 'default' => 'Developed by D. Zavos Group · Limassol, Cyprus' ),
				array( 'key' => 'privacy_label', 'type' => 'text', 'tr' => true, 'label' => 'קישור: מדיניות פרטיות', 'default' => tml_tr( 'מדיניות פרטיות', 'Privacy Policy' ) ),
				array( 'key' => 'privacy_url', 'type' => 'url', 'label' => 'כתובת עמוד מדיניות פרטיות', 'help' => 'ריק = העמוד שהתוסף יצר.', 'default' => '' ),
				array( 'key' => 'a11y_label', 'type' => 'text', 'tr' => true, 'label' => 'קישור: הצהרת נגישות', 'default' => tml_tr( 'הצהרת נגישות', 'Accessibility Statement' ) ),
				array( 'key' => 'a11y_url', 'type' => 'url', 'label' => 'כתובת עמוד הצהרת נגישות', 'help' => 'ריק = העמוד שהתוסף יצר.', 'default' => '' ),
				array( 'key' => 'disclaimer', 'type' => 'textarea', 'tr' => true, 'label' => 'הבהרה משפטית', 'default' => tml_tr( 'המידע וההדמיות בדף זה הינם להמחשה בלבד ואינם מהווים מסמך מחייב או הצעה. התוצר הסופי עשוי להשתנות. The Mews Residences הוא שם שיווקי.', 'The information and renders on this page are indicative only and do not constitute a contract or offer. The finished product may vary. The Mews Residences is a marketing name.' ) ),
			),
		),
	);

	return $schema;
}

function tml_inline_tags() {
	return array(
		'b'      => array(),
		'strong' => array(),
		'br'     => array(),
		'em'     => array(),
		'small'  => array(),
	);
}

/** Saved content merged over the defaults, for one section. */
function tml_section( $section ) {
	$schema = tml_schema();
	$saved  = get_option( 'tml_content', array() );
	$saved  = isset( $saved[ $section ] ) && is_array( $saved[ $section ] ) ? $saved[ $section ] : array();
	$out    = array();
	foreach ( $schema[ $section ]['fields'] as $field ) {
		$out[ $field['key'] ] = array_key_exists( $field['key'], $saved ) ? $saved[ $field['key'] ] : $field['default'];
	}
	return $out;
}

/** Turns an "asset:..." default into a URL inside the plugin. */
function tml_url( $value ) {
	if ( is_string( $value ) && 0 === strpos( $value, 'asset:' ) ) {
		return TML_URL . 'assets/' . substr( $value, 6 );
	}
	return $value;
}
