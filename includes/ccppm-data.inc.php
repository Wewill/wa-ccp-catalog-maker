<?php

class ccppm_data extends ccppm_object {
	public $jsondb = False;
	private $db_path = False;
	public $storage_path = False;
	private $lang = 'en_US';
	private $lang_short = 'en';
	private $__icl_cache = array();
	public $pictures_enabled = True;
	public $force_recrop = False;
	public $picture_max_sizes_available = [
		"9"=>['width'=>52.5, 'height'=>74.5],
		"18"=>['width'=>105, 'height'=>149],
		"36"=>['width'=>210, 'height'=>298],
		"72"=>['width'=>420, 'height'=>596],
		"150"=>['width'=>877, 'height'=>1241],
		"300"=>['width'=>1753, 'height'=>2481],
	];

	public $strip_tags_allowed = '<br><i><strong><b><img><u><ul><li><em><h1><h2><h3><h4><h5><h6><p><span><aside>'; // ajout wil <h4> + suppression <p> pour <p style="text-align: right;"> align right //

	private $convert_special_char_dict = array(
	        "\u{0300}"=>'grave',
	        "\u{0301}"=>'acute',
	        "\u{0302}"=>'circ',
	        "\u{0303}"=>'tilde',
	        "\u{0308}"=>'uml',
	);

	public function convert_special_char_matches($matches) {
					$c = sprintf("&%s%s;", $matches[1], $this->convert_special_char_dict[$matches[2]]);
					$c = html_entity_decode($c, ENT_COMPAT, 'UTF-8');
					return $c;
	}

	public function convert_special_char($text) {
		if (is_array($text)) {
			foreach($text as $idx => $value) {
				$text[$idx] = $this->convert_special_char($value);
			}
			return $text;
		} else {
			$text = preg_replace_callback("/(\w)(".implode('|', array_keys($this->convert_special_char_dict)).")/", [$this, 'convert_special_char_matches'], $text);
						$text = str_replace("\u{202a}", '', $text);
						$text = str_replace("\u{202c}", '', $text);
			return $text;
		}
	}

	public $dpi = 9;

	public $picture_max_size = ['width'=>52.5, 'height'=>74.5]; // 9 dpi


	public $tmp_contacts = array();

	public $indexes = array(
		'film' => array(
			'director_id' => 'director@id',
			'country' => 'country',
			'movie-type' => 'movie-type',
			'contact_id' => array('producer@id', 'catalog@id', 'director@id'),
			'section' => 'section@term_id',
		),
		'contact' => array(
		),
		'section' => array(
			'parent' => 'parent'
		),
		'room' => array(
			'parent' => 'parent'
		),
		'jury' => array(
			'movie-type' => 'movie-type@term_id',
		),
		'projection'=>array(
			'film' => 'film',
			'guest_id' => 'guest@id',
		),
		'accreditation'=>array(
			'contact_id' => 'contact@id'
		),
		'partenaire' => array(
			'partenaire-category' => 'partenaire-category@term_id',
		),
	);

	public $quick_names = array(
		'film' => array('post_title'),
		'section' => array('name'),
		'contact' => array('name', 'firstname'),
		'jury' => array('post_title'),
		'partenaire' => array('post_title'),
		'movie-type' => array('name'),
		'planning' => array('name'),
		'partenaire-category' => array('name'),
	);

	public $terms = array(
		'film' => array(
			'section'=>array('edition'=>'wpcf-select-edition'),
			'edition'=>array(),
		),
		'jury' => array(
			'movie-type' => array(),
		),
		'projection'=>array(
			'room'=>array(),
			'post_tag'=>array(),
		),
		'partenaire' => array(
			'partenaire-category'=>array(),
		),
		'accreditation' => array(
			'function' => array(),
		),
	);

	public $relations = array(
		'projection'=> array(
			'film'=>array(
				'slug'=>'films-projection',
				'order'=> 'wpcf-f-p-film-order',
			),
		),
	);

	public $meta_keys_terms = array(
		'section' => array(
#			'wpcf-s-image' => array('name'=>'image', 'type'=>'picture', 'uniq'=>True),
			'wpcf-s-image' => array('name'=>'image', 'type'=>'picture', 'uniq'=>True, 'sizes'=>[
				//'r036_1'=>'0.3620689655:1', 
				//'r16_9'=>'16:8.38', 
				'r1920_1080'=>'16:9', 
				//'r419_136'=>'419.53:136.32', 
				'r419_181'=>'419.53:181.76', 
				//'r419_90' => '419.53:90.88', 
				//'r380_550' => '380:550',
				'r10_15'=>'170.3979:247.64',
				'r4_3'=>'433.7032338099:311.8132338099', //#44
			]),
			'wpcf-s-image_colorized_PRINT' => array('name'=>'image_colorized_print', 'orginalPicture' => 'image', 'type'=>'picture', 'uniq'=>True, 'sizes'=>[
				//'r036_1'=>'0.3620689655:1', 
				//'r16_9'=>'16:8.38', 
				'r1920_1080'=>'16:9', 
				//'r419_136'=>'419.53:136.32', 
				'r419_181'=>'419.53:181.76', 
				//'r419_90' => '419.53:90.88', 
				//'r380_550' => '380:550',
				'r10_15'=>'170.3979:247.64',
				'r4_3'=>'433.7032338099:311.8132338099', //#44
			]),
			'wpcf-s-content' => array('name'=>'content', 'type'=>'wprte', 'uniq'=>True),
			'wpcf-s-color' => array('name'=>'color', 'type'=>'string', 'uniq'=>True),
			'wpcf-s-print-color' => array('name'=>'color_print', 'type'=>'cmjn', 'uniq'=>True),

			'wpcf-s-credits-image' => array('name'=>'image_credits', 'type'=>'string', 'uniq'=>True),
			'wpcf-s-featured-film-1' => array('name'=>'featured_film_1', 'uniq'=>True),
			'wpcf-s-featured-film-2' => array('name'=>'featured_film_2', 'uniq'=>True),

			'wpcf-s-filmography-of' => array('name'=>'filmography_of', 'type'=>'string', 'uniq'=>True), // Added #41 (WIL)
			'wpcf-s-filmography'=> array('name'=>'filmography_r', 'type'=>'serialize'), // Added #41 (WIL)
			'ccppto_film'=>array('name'=>'ccppto_film', 'type'=>'json', 'uniq'=>True),
			'ccppto_projection'=>array('name'=>'ccppto_projection', 'type'=>'json', 'uniq'=>True),

		),
		'room' => array(
			'wpcf-r-opening-days-hours' => array('name'=>'opening_days_hours', 'type'=>'json', 'uniq'=>True),
			'wpcf-r-room-adress' => array('name'=>'room_address', 'type'=>'string', 'uniq'=>True),
			'wpcf-r-timing'=>array('name'=>'timing', 'type'=>'json', 'uniq'=>True),
			'wpcf-r-hide-in-planning'=>array('name'=>'hide_in_planning', 'type'=>'boolean', 'uniq'=>True),
		),
		'partenaire-category' => array(
		),
	);

	public $meta_keys = array(
		'film' => array(
			'_thumbnail_id'=>array('name'=>'film_featured_image', 'type'=>'post_picture', 'uniq'=>True, 'sizes'=>[
				'r16_9'=>'16:8.38', 
				'r1920_1080'=>'16:9', 
				//'r260_269'=>'260.945:269.14',
				'r4_3'=>'394.03:297.64',
				'r10_6'=>'197.015:115.88',
				'r155_148'=>'155:148.82', //#44
			]),
			'wpcf-c-e-producer-contact'=>array('type'=>'contact', 'name'=>'producer', 'verify'=>'required'),
			'wpcf-c-e-catalog-contact'=>array('type'=>'contact', 'name'=>'catalog', 'verify'=>'required'),
			'wpcf-c-e-director-contact'=>array('type'=>'contact', 'name'=>'director'),
			'wpcf-c-e-distribution-contact'=>array('type'=>'contact', 'name'=>'distribution', 'verify'=>'required', 'uniq'=>True),
			'wpcf-f-e-contact-copy-location'=>array('type'=>'contact', 'name'=>'contact_copy_location'),
			'wpcf-f-order-title'=>array('type'=>'string', 'name'=>'_name_simplified', 'verify'=>'required', 'uniq'=>True),
			'wpcf-f-author'=>array('type'=>'serialize', 'name'=>'author', 'verify'=>'required'),
			'wpcf-f-screenplay'=>array('type'=>'serialize', 'name'=>'screenplay', 'verify'=>'required'),
			'wpcf-f-photography'=>array('type'=>'serialize', 'name'=>'photography', 'verify'=>'required'),
			'wpcf-f-sound'=>array('type'=>'serialize', 'name'=>'sound', 'verify'=>'required'),
			'wpcf-f-music'=>array('type'=>'serialize', 'name'=>'music', 'verify'=>'required'),
			'wpcf-f-producer-2'=>array('type'=>'serialize', 'name'=>'co_producer'), // To rename... wpcf-f-co-producer
			'wpcf-f-starring'=>array('type'=>'serialize', 'name'=>'starring', 'verify'=>'required'),
			'wpcf-f-editing'=>array('type'=>'serialize', 'name'=>'editing', 'verify'=>'required'),
			'wpcf-f-set-artist'=>array('type'=>'serialize', 'name'=>'set_artist', 'verify'=>'required'),
			'wpcf-f-costumes'=>array('type'=>'serialize', 'name'=>'costumes'),
			'wpcf-f-visual-effects'=>array('type'=>'serialize', 'name'=>'visual_effects'),
			'wpcf-f-voice-over'=>array('type'=>'serialize', 'name'=>'voice_over'),
			'wpcf-f-other'=>array('type'=>'serialize', 'name'=>'other'),
			'wpcf-f-film-poster'=>array('type'=>'picture', 'name'=>'poster', 'uniq'=>True, 'sizes'=>[
				'r10_15'=>'170.3979:247.64',
			]),  // Added #41 (WIL)
//			'wpcf-f-additionnal-pictures'=>array('type'=>'picture', 'name'=>'additionnal_pictures'),
			'wpcf-f-dialogues-files'=>array('type'=>'serialize', 'name'=>'dialogues_files'), // Removed #41
			'wpcf-f-country'=>array('name'=>'country', 'type'=>'country', 'verify'=>'required'),
			'wpcf-f-co-production-country'=>array('name'=>'co_production_country', 'type'=>'country'),
			'wpcf-f-production-year'=>array('name'=>'production_year', 'uniq'=>True, 'verify'=>'required'),
			'wpcf-f-movie-length'=>array('name'=>'movie_length', 'uniq'=>True, 'verify'=>'required'),
			'wpcf-f-movie-length-seconds'=>array('name'=>'movie_length_seconds', 'uniq'=>True),  // Added #41 (WIL)
			'wpcf-f-movie-type'=>array('type'=>'select', 'name'=>'movie_type', 'uniq'=>True, 'verify'=>'required'),
			'wpcf-f-movie-category'=>array('type'=>'select', 'name'=>'movie_category', 'uniq'=>True, 'verify'=>'required'),
			'wpcf-synopsis-french'=>array('name'=>'synopsis_french_alt', 'uniq'=>True), // Depreciated #40  // To rename to wpcf-f-synopsis-french...
			'wpcf-f-synopsis-french'=>array('name'=>'synopsis_french', 'uniq'=>True, 'verify'=>'required'),
			'wpcf-f-synopsis-english'=>array('name'=>'synopsis_english', 'uniq'=>True, 'verify'=>'required'),
			'wpcf-f-film-without-dialogues'=>array('name'=>'without_dialogues', 'type'=>'boolean', 'uniq'=>True),
			//
			'wpcf-f-filmography'=>array('name'=>'filmography', 'type'=>'string', 'uniq'=>True, 'verify'=>'important'), // Depreciated #41
			'wpcf-f-awards'=>array('name'=>'awards', 'type'=>'string', 'uniq'=>True), // Depreciated #41
			'wpcf-f-film-career-french'=>array('name'=>'career_french', 'type'=>'string', 'uniq'=>True), // Depreciated #41
			'wpcf-f-film-career-english'=>array('name'=>'career_english', 'type'=>'string', 'uniq'=>True), // Depreciated #41
			'wpcf-f-selective-film-career'=>array('name'=>'selective_film_career', 'type'=>'string', 'uniq'=>True), // Depreciated #41
			//
			'wpcf-f-filmography-r'=>array('name'=>'filmography_r', 'type'=>'serialize', 'verify'=>'important'), // Added #41 (WIL)
			'wpcf-f-career-r'=>array('name'=>'career_r', 'type'=>'serialize'), // Added #41 (WIL)
			'wpcf-f-awards-r'=>array('name'=>'awards_r', 'type'=>'serialize'), // Added #41 (WIL)
			'wpcf-f-selective-filmography-r'=>array('name'=>'selective_filmography_r', 'type'=>'serialize', 'verify'=>'important'), // Added #41 (WIL)
			'wpcf-f-selective-career-r'=>array('name'=>'selective_career_r', 'type'=>'serialize'), // Added #41 (WIL)
			'wpcf-f-selective-awards-r'=>array('name'=>'selective_awards_r', 'type'=>'serialize'), // Added #41 (WIL)
			'wpcf-f-selective-filmography'=>array('name'=>'selective_filmography', 'type'=>'string', 'uniq'=>True),
			'wpcf-f-selective-awards'=>array('name'=>'selective_awards', 'type'=>'string', 'uniq'=>True),
			'wpcf-f-selective-career'=>array('name'=>'selective_career', 'type'=>'string', 'uniq'=>True), // Added #41 (WIL)
			//
			'wpcf-f-animation'=>array('type'=>'serialize', 'name'=>'animation', 'verify'=>'required'),
			'wpcf-f-animation-type'=>array('type'=>'serialize', 'name'=>'animation_type', 'uniq'=>True),
			'wpcf-f-another-subtitles-languages'=>array('type'=>'language', 'name'=>'another_subtitles_languages', 'uniq'=>True),
			'wpcf-f-as-french-distributor'=>array('type'=>'select', 'name'=>'as_french_distributor', 'uniq'=>True),
			'wpcf-f-as-international-seller'=>array('type'=>'select', 'name'=>'as_international_seller', 'uniq'=>True),
			'wpcf-f-available-formats'=>array('type'=>'serialize', 'name'=>'available_formats'),
			'wpcf-f-catalog-synopsis'=>array('name'=>'catalog_synopsis', 'uniq'=>True),
			'wpcf-f-choose-the-title-to-show'=>array('type'=>'select', 'name'=>'choose_the_title_to_show', 'uniq'=>True),
			'wpcf-f-color-format'=>array('type'=>'select_fr', 'name'=>'color_format', 'uniq'=>True, 'verify'=>'required'),
			'wpcf-f-comments'=>array('name'=>'comments', 'uniq'=>True),
			'wpcf-f-dialogs'=>array('type'=>'select', 'name'=>'dialogs', 'uniq'=>True), // Depreciated #41
			'wpcf-f-diffusion-permission'=>array('type'=>'select', 'name'=>'diffusion_permission', 'uniq'=>True),
			'wpcf-f-directors-pictures'=>array('type'=>'picture', 'name'=>'directors_pictures'),
			'wpcf-f-film-format'=>array('type'=>'checkbox', 'name'=>'film_format', 'verify'=>'required', 'uniq'=>True), // Depreciated #40
			//'wpcf-f-film-gif'=>array('type'=>'picture', 'name'=>'film_gif'), // Removed #41
			'wpcf-f-filming-format'=>array('type'=>'select', 'name'=>'filming_format', 'uniq'=>True), // Depreciated #40
			'wpcf-f-films-made-before'=>array('name'=>'films_made_before', 'uniq'=>True),
			'wpcf-f-french-distributor'=>array('name'=>'french_distributor', 'uniq'=>True),
			'wpcf-f-french-operating-title'=>array('name'=>'french_operating_title', 'uniq'=>True),
			'wpcf-f-french-version-of-the-film'=>array('type'=>'select', 'name'=>'french_version_of_the_film', 'uniq'=>True),  // Depreciated #41
			'wpcf-f-from-adaptation'=>array('name'=>'from_adaptation', 'uniq'=>True),
			'wpcf-f-in-another-language'=>array('type'=>'select', 'name'=>'in_another_language', 'uniq'=>True),
			'wpcf-f-international-sales-contact'=>array('type'=>'contact', 'name'=>'international_sales_contact'),
			'wpcf-f-international-seller'=>array('name'=>'international_seller', 'uniq'=>True),
			'wpcf-f-is-3d'=>array('type'=>'select', 'name'=>'is_3d', 'uniq'=>True),
			'wpcf-f-language'=>array('type'=>'language', 'name'=>'language', 'uniq'=>True),
			'wpcf-f-movie-website'=>array('type'=>'url', 'name'=>'movie_website', 'verify'=>'required'),
			'wpcf-f-notes'=>array('name'=>'notes', 'uniq'=>True),
			'wpcf-f-number-of-reels'=>array('type'=>'integer', 'name'=>'number_of_reels', 'uniq'=>True),
			'wpcf-f-o-contact-copy-location'=>array('name'=>'contact_copy_location'),
			'wpcf-f-o-contact-copy-pick-up'=>array('name'=>'contact_copy_pick_up'),
			'wpcf-f-o-contact-copy-return'=>array('name'=>'contact_copy_return', 'uniq'=>True),
			'wpcf-f-o-contact-payment'=>array('name'=>'contact_payment', 'uniq'=>True),
			'wpcf-f-o-contact-rights'=>array('name'=>'contact_rights', 'uniq'=>True),
			'wpcf-f-o-watched-by'=>array('type'=>'user', 'name'=>'watched_by', 'uniq'=>True),
			'wpcf-f-ope-hauts-de-france'=>array('type'=>'select', 'name'=>'ope_hauts_de_france', 'uniq'=>True),
			//'wpcf-f-other-files'=>array('type'=>'url', 'name'=>'other_files'), // Removed #41
			'wpcf-f-other-film-format'=>array('name'=>'other_film_format', 'uniq'=>True),
			'wpcf-f-other-filming-format'=>array('name'=>'other_filming_format', 'uniq'=>True),
			'wpcf-f-other-movie-category'=>array('name'=>'other_movie_category', 'uniq'=>True),
			'wpcf-f-other-sound-format'=>array('name'=>'other_sound_format', 'uniq'=>True),
			'wpcf-f-other-video-format'=>array('name'=>'other_video_format', 'uniq'=>True),
			'wpcf-f-payment-date'=>array('type'=>'date', 'name'=>'payment_date', 'uniq'=>True),
			'wpcf-f-pick-up-date'=>array('type'=>'date', 'name'=>'pick_up_date', 'uniq'=>True),
			'wpcf-f-pick-up-notes'=>array('name'=>'pick_up_notes', 'uniq'=>True),
			'wpcf-f-pickup-packaging'=>array('type'=>'serialize', 'name'=>'pickup_packaging', 'uniq'=>True),
			'wpcf-f-premiere'=>array('type'=>'select_fr', 'name'=>'premiere', 'uniq'=>True),
			//'wpcf-f-presskit-files'=>array('type'=>'url', 'name'=>'presskit_files'), // Removed #41
			'wpcf-f-production-country'=>array('type'=>'country', 'name'=>'production_country', 'uniq'=>True),
			'wpcf-f-projection-format'=>array('type'=>'select', 'name'=>'projection_format', 'uniq'=>True), // Depreciated #41
			'wpcf-f-punchline-english'=>array('name'=>'punchline_english', 'uniq'=>True, 'verify'=>'required'),
			'wpcf-f-punchline-french'=>array('name'=>'punchline_french', 'uniq'=>True, 'verify'=>'required'),
			'wpcf-f-reference'=>array('type'=>'select', 'name'=>'reference', 'uniq'=>True),
			'wpcf-f-return-date'=>array('type'=>'date', 'name'=>'return_date', 'uniq'=>True),
			'wpcf-f-return-deadline'=>array('type'=>'date', 'name'=>'return_deadline', 'uniq'=>True),
			'wpcf-f-return-packaging'=>array('type'=>'serialize', 'name'=>'return_packaging', 'uniq'=>True),
			'wpcf-f-returns-note'=>array('type'=>'serialize', 'name'=>'returns_note', 'uniq'=>True),
			'wpcf-f-screener'=>array('type'=>'select', 'name'=>'screener', 'uniq'=>True),
			'wpcf-f-screener-dvd'=>array('type'=>'serialize', 'name'=>'screener_dvd', 'uniq'=>True),
			'wpcf-f-screener-url'=>array('type'=>'url', 'name'=>'screener_url'),
			'wpcf-f-sent-dvd'=>array('type'=>'serialize', 'name'=>'sent_dvd', 'uniq'=>True),
			'wpcf-f-sound-format'=>array('type'=>'serialize', 'name'=>'sound_format', 'uniq'=>True),
			'wpcf-f-subtitled-in-english'=>array('type'=>'serialize', 'name'=>'subtitled_in_english', 'uniq'=>True),
			'wpcf-f-subtitled-in-french'=>array('type'=>'serialize', 'name'=>'subtitled_in_french', 'uniq'=>True),
			'wpcf-f-subtitles-state'=>array('type'=>'serialize', 'name'=>'subtitles_state', 'uniq'=>True),
			'wpcf-f-teaser-urls'=>array('type'=>'url', 'name'=>'teaser_urls'),
			'wpcf-f-technical-datas-files'=>array('type'=>'url', 'name'=>'technical_datas_files'),
			'wpcf-f-video-format'=>array('type'=>'checkbox', 'name'=>'video_format', 'uniq'=>True),
			'wpcf-f-short-synopsis-french'=>array('type'=>'string', 'name'=>'short_synopsis_french', 'uniq'=>True, 'verify'=>'required'),
			'wpcf-f-short-synopsis-english'=>array('type'=>'string', 'name'=>'short_synopsis_english', 'uniq'=>True, 'verify'=>'required'),
			'wpcf-f-shot-locations'=>array('type'=>'string', 'name'=>'shot_locations', 'uniq'=>True),
			'wpcf-f-broadcast-licence'=>array('type'=>'string', 'name'=>'broadcast_licence', 'uniq'=>True),
			'wpcf-f-educational-resources'=>array('type'=>'string', 'name'=>'educational_resources', 'uniq'=>True),
			'wpcf-f-public'=>array('type'=>'string', 'name'=>'public', 'verify'=>'required', 'uniq'=>True),
			'wpcf-f-catalog-synopsis-french'=>array('type'=>'string', 'name'=>'catalog_synopsis_french', 'uniq'=>True),
			'wpcf-f-catalog-synopsis-english'=>array('type'=>'string', 'name'=>'catalog_synopsis_english', 'uniq'=>True),
			'wpcf-f-catalog-picture'=>array('type'=>'picture', 'name'=>'catalog_picture', 'uniq'=>True, 'verify'=>'required', 'sizes'=>[
				'r16_9'=>'16:8.38', 
				'r1920_1080'=>'16:9', 
				//'r260_269'=>'260.945:269.14',
				'r4_3'=>'394.03:297.64',
				'r10_6'=>'197.015:115.88',
				'r155_148'=>'155:148.82', //#44
			]),
			'wpcf-f-catalog-filmpartners'=>array('type'=>'picture', 'name'=>'catalog_filmpartners', 'verify'=>'required'),
			'wpcf-f-catalog-tag'=>array('type'=>'select_fr', 'name'=>'catalog_tag', 'uniq'=>True),
			'wpcf-f-photo-credits'=>array('type'=>'string', 'name'=>'film_photo_credits', 'uniq'=>True, 'verify'=>'required'),
			'wpcf-f-long-opinion'=>array('type'=>'string', 'name'=>'long_opinion', 'uniq'=>True),
			'wpcf-f-opinion-author'=>array('type'=>'string', 'name'=>'opinion_author', 'uniq'=>True),
		),
		'contact' => array(
			'wpcf-c-name'=>array('name'=>'name', 'type'=>'string', 'uniq'=>True),
			'wpcf-c-firstname'=>array('name'=>'firstname', 'type'=>'string', 'uniq'=>True),
			'wpcf-c-surname'=>array('name'=>'surname', 'type'=>'string', 'uniq'=>True),
			'wpcf-c-gender'=>array('name'=>'gender', 'type'=>'string', 'uniq'=>True),
			'wpcf-c-language'=>array('name'=>'language', 'type'=>'language', 'uniq'=>True),
			'wpcf-c-organization'=>array('name'=>'organization', 'type'=>'string', 'uniq'=>True),
			'wpcf-c-structure'=>array('name'=>'structure', 'type'=>'string', 'uniq'=>True),
			'wpcf-c-position'=>array('name'=>'position', 'type'=>'string', 'uniq'=>True),
			'wpcf-c-email'=>array('name'=>'email', 'type'=>'string', 'uniq'=>True),
			'wpcf-c-biofilmography-french'=>array('name'=>'biofilmography_french', 'type'=>'string', 'uniq'=>True),
			'wpcf-c-biofilmography-english'=>array('name'=>'biofilmography_english', 'type'=>'string', 'uniq'=>True),
//			'wpcf-f-biofilmography-english'=>array('name'=>'biofilmography_english_alt', 'type'=>'string', 'uniq'=>True),
			'wpcf-c-picture'=>array('name'=>'picture', 'type'=>'picture', 'uniq'=>True, 'sizes'=>['r1_1'=>'1:1', 'r62_115'=>'68.2045:115'], 'black_and_white'=>True),
			'wpcf-c-photo-credits'=>array('name'=>'photo_credits', 'type'=>'string', 'uniq'=>True),
			'wpcf-c-mobile-phone'=>array('name'=>'mobile_phone', 'type'=>'string', 'uniq'=>True),
			'wpcf-c-country'=>array('name'=>'country', 'type'=>'country', 'uniq'=>True),
		),
		'jury' => array(
			'_thumbnail_id'=>array('name'=>'picture', 'type'=>'post_picture', 'verify'=>'required', 'uniq'=>True, 'sizes'=>[
				//'r1_1'=>'1:1',
				//'r036_1'=>'0.36:1', 
				//'r283_266'=>'283.39:266.63', 
				//'r216_841'=>'216.14:841.89', 
				//'r340_163'=>'340.08293523960003:163.003432857', 
				//'r16_9'=>'16:8.38',
				//'r4_3'=>'394.03:297.64',
				//'r8_5' => '8:5',
				'r155_119'=>'155:119.056', //#44
			]),
			'wpcf-j-description'=>array('name'=>'description', 'type'=>'string', 'uniq'=>True),
			'wpcf-j-catalog-content'=>array('name'=>'catalog_content', 'uniq'=>True), // Add w #44
			'wpcf-j-master'=>array('name'=>'master', 'type'=>'boolean', 'uniq'=>True),
		),
		'partenaire' => array(
			'_colorized_URL'=>array('name'=>'colorized_url', 'type'=>'picture', 'uniq'=>True),
			'_colorized_mobile_URL'=>array('name'=>'colorized_mobile_url', 'type'=>'picture', 'uniq'=>True),
			'p-link'=>array('name'=>'link', 'type'=>'string', 'uniq'=>True),
			'_thumbnail_id'=>array('name'=>'picture', 'type'=>'post_picture', 'verify'=>'required', 'uniq'=>True, 'sizes'=>['r1_1'=>'1:1'], 'cover'=>True),
		),
		'projection' => array(
			'_thumbnail_id'=>array('name'=>'projection_featured_image', 'type'=>'post_picture', 'uniq'=>True, 'sizes'=>[
				// 'r16_9'=>'16:8.38',
				// 'r1920_1080'=>'16:9',
				'r260_269'=>'260.945:269.14',
				// 'r4_3'=>'394.03:297.64',
				// 'r10_6'=>'197.015:115.88',
				'r155_148'=>'155:148.82', //#44
			]),
			'_wpcf_belongs_film_id'=>array('name'=>'film', 'type'=>'post_id'),
			'wpcf-p-date'=>array('name'=>'date', 'type'=>'date', 'uniq'=>True),
			'wpcf-p-start-and-stop-time'=>array('name'=>'start_and_stop_time', 'type'=>'serialize', 'uniq'=>True),
			'wpcf-p-note'=>array('name'=>'note', 'type'=>'string', 'uniq'=>True),
			'wpcf-p-is-guest'=>array('name'=>'is_guest', 'type'=>'boolean', 'uniq'=>True),
			'wpcf-p-e-guest-contact'=>array('name'=>'guest', 'type'=>'contact', 'uniq'=>True),
			'wpcf-p-is-debate'=>array('name'=>'is_debate', 'type'=>'boolean', 'uniq'=>True), // ADDEDWIL#43
			'wpcf-p-tag'=>array('name'=>'tag', 'type'=>'string', 'uniq'=>True),
			'wpcf-p-format'=>array('name'=>'format', 'type'=>'string', 'uniq'=>True),
			'wpcf-p-young-public'=>array('name'=>'young_public', 'type'=>'boolean', 'uniq'=>True),
			'wpcf-p-highlights'=>array('name'=>'is_highlighted', 'type'=>'boolean', 'uniq'=>True),
			'wpcf-p-hide-projection-title'=>array('name'=>'is_title_hidden', 'type'=>'boolean', 'uniq'=>True),
		),
		'accreditation' => array(
			'wpcf-a-majority'=>array('name'=>'majority', 'type'=>'boolean', 'uniq'=>True),
			'wpcf-a-accreditation-type'=>array('name'=>'accreditation_type', 'type'=>'string', 'uniq'=>True),
			'wpcf-a-express'=>array('name'=>'express', 'type'=>'boolean', 'uniq'=>True), // ADDWIL#42
			'wpcf-a-express-name'=>array('name'=>'express_name', 'type'=>'string', 'uniq'=>True), // ADDWIL#42
			'wpcf-a-express-image'=>array('name'=>'express_image', 'type'=>'picture', 'uniq'=>True, 'sizes'=>['r1_1'=>'1:1', 'r62_115'=>'68.2045:115'], 'black_and_white'=>True), // ADDWIL#43
//			'wpcf-a-related-contact'=>array('type'=>'contact', 'name'=>'contact', 'uniq'=>True),
			'_wpcf_belongs_contact_id'=>array('type'=>'contact', 'name'=>'contact', 'uniq'=>True),
			'wpcf-a-availability-1'=>array('name'=>'availability_1', 'type'=>'checkbox', 'uniq'=>True),
			'wpcf-a-availability-2'=>array('name'=>'availability_2', 'type'=>'checkbox', 'uniq'=>True),
			'wpcf-a-availability-3'=>array('name'=>'availability_3', 'type'=>'checkbox', 'uniq'=>True),
			'wpcf-a-availability-4'=>array('name'=>'availability_4', 'type'=>'checkbox', 'uniq'=>True),
			'wpcf-a-availability-5'=>array('name'=>'availability_5', 'type'=>'checkbox', 'uniq'=>True),
			'wpcf-a-availability-6'=>array('name'=>'availability_6', 'type'=>'checkbox', 'uniq'=>True),
			'wpcf-a-availability-7'=>array('name'=>'availability_7', 'type'=>'checkbox', 'uniq'=>True),
			'wpcf-a-availability-8'=>array('name'=>'availability_8', 'type'=>'checkbox', 'uniq'=>True),
			'wpcf-a-availability-9'=>array('name'=>'availability_9', 'type'=>'checkbox', 'uniq'=>True),
			'wpcf-a-socio-economic-classification'=>array('name'=>'socio_economic_classification', 'type'=>'select', 'uniq'=>True),
			'wpcf-a-skills'=>array('name'=>'skills', 'type'=>'string', 'uniq'=>True),
			'wpcf-a-job'=>array('name'=>'job', 'type'=>'checkbox', 'uniq'=>True),
			'wpcf-a-vehicle'=>array('name'=>'vehicle', 'type'=>'checkbox', 'uniq'=>True),
			'wpcf-a-address'=>array('name'=>'address', 'type'=>'string', 'uniq'=>True),
			'wpcf-a-emergency-contact'=>array('name'=>'emergency_contact', 'type'=>'string', 'uniq'=>True),
			'wpcf-a-emergency-contact-phone'=>array('name'=>'emergency_contact_phone', 'type'=>'string', 'uniq'=>True),
			'wpcf-a-special-notes'=>array('name'=>'special_notes', 'type'=>'string', 'uniq'=>True),
			'wpcf-a-activities'=>array('name'=>'activities', 'type'=>'serialize'),
			'wpcf-a-activities-notes'=>array('name'=>'activities_notes', 'type'=>'string', 'uniq'=>True),
			'wpcf-a-date-of-arrival'=>array('name'=>'date_of_arrival', 'type'=>'datetime', 'uniq'=>True),
			'wpcf-a-date-of-departure'=>array('name'=>'date_of_departure', 'type'=>'datetime', 'uniq'=>True),
			'wpcf-a-accompanied-list'=>array('name'=>'accompanied_list', 'type'=>'string'),
			'wpcf-a-press-name'=>array('name'=>'press_name', 'type'=>'string', 'uniq'=>True),
			'wpcf-a-press-type'=>array('name'=>'press_type', 'type'=>'checkbox', 'uniq'=>True),
			'wpcf-a-press-website'=>array('name'=>'press_website', 'type'=>'string', 'uniq'=>True),

		),
	);

	public function __construct($ccppm) {
		parent::__construct($ccppm);
		$this->redefine();
	}

	public function redefine($dpi = false) {
		$edition_slug = $this->ccppm->edition_slug;
		require_once('jsondb.inc.php');

		if ($dpi)
			$this->dpi = $dpi;

		$this->picture_max_size = $this->picture_max_sizes_available[$this->dpi];

		$this->db_path = sprintf("%s/%s/%s/%s/", dirname(__FILE__), "../jsondb/", $edition_slug, $this->dpi);
		if (!is_dir($this->db_path))
			if (!mkdir($this->db_path, 0755, True))
				printf("Can't create %s<br/>", $this->db_path);

		$this->storage_path = sprintf("%s/%s/%s/%s-storage/", dirname(__FILE__), "../jsondb/", $edition_slug, $this->dpi);
		if (!is_dir($this->storage_path))
			if (!mkdir($this->storage_path, 0755, True))
				printf("Can't create %s<br/>", $this->storage_path);

		$this->jsondb = new jsondb($this->db_path, $this->indexes, $this->quick_names);
	}

	public function get_fields() {
		$t_fields = get_option('wpcf-fields');
//		$fields = unserialize($fields);
		$d_fields = array();
		foreach($t_fields as $slug => $field) {
			$d_fields[$field['meta_key']] = $field;
		}
		return $d_fields;
	}

	public function get_icl_translate($field, $value, $type) {
		global $wpdb;
		switch($field['type']) {
			case 'radio':
			case 'select':
				switch($type) {
					case 'select':
						$id = False;
						foreach($field['data']['options'] as $o_key => $o_value) {
							if (is_array($o_value) and array_key_exists('value', $o_value))
								if ($o_value['value'] == $value)
									$id = $o_key;
						}
						if ($id === False)
							return False;
						$context = sprintf("%s %s %s %s %s", 'field', $field['id'], 'option', $id, 'title');
						$sql= sprintf("SELECT value FROM %sicl_strings WHERE name = '$context' and language = '".$this->lang_short."'", $wpdb->prefix);
						$value = $wpdb->get_var($sql);
						if (!$value)
							return $field['data']['options'][$id]['title'];
						return $value;
						break;
					case 'select_fr':
						$id = False;
						foreach($field['data']['options'] as $o_key => $o_value) {
							if (is_array($o_value) and array_key_exists('value', $o_value))
								if ($o_value['value'] == $value)
									$id = $o_key;
						}

						if ($id === False)
							return False;
						$context = sprintf("%s %s %s %s %s", 'field', $field['id'], 'option', $id, 'title');
						$sql= sprintf("SELECT %sicl_string_translations.value FROM %sicl_strings, %sicl_string_translations WHERE %sicl_strings.id = %sicl_string_translations.string_id and %sicl_strings.name = '$context' and %sicl_strings.language = 'en' and %sicl_string_translations.language = 'fr'", $wpdb->prefix, $wpdb->prefix, $wpdb->prefix, $wpdb->prefix, $wpdb->prefix, $wpdb->prefix, $wpdb->prefix, $wpdb->prefix);
						$value = $wpdb->get_var($sql);
						if (!$value)
							return $field['data']['options'][$id]['title'];
						return $value;
						break;
				}
				break;
		}
		return False;
	}

	public function get_icl_field_name_translate($fields) {
		global $wpdb;
		$contexts = [];
		$names = [];
		$p = $wpdb->prefix;
		foreach($fields as $field) {
			$contexts[] = sprintf("'%s %s %s'", "field", str_replace('wpcf-', '', $field), "name");
		}
		$sql=sprintf("SELECT %sicl_strings.name, %sicl_strings.id, %sicl_string_translations.value 
			FROM %sicl_strings, %sicl_string_translations 
			WHERE %sicl_string_translations.string_id = %sicl_strings.id 
			AND name IN (%s)
			AND %sicl_strings.language = 'en' AND %sicl_string_translations.language = 'fr'
			",
			$p, $p, $p,
			$p, $p,
			$p, $p,
			implode(', ', $contexts),
			$p, $p);
		$data = $wpdb->get_results( $sql, ARRAY_A );
		foreach($data as $d) {
			$name = $d['name'];
			$value = $d['value'];
			list($t_1, $t_2, $t_3) = explode(' ', $name);
			$n = 'wpcf-'.$t_2;
			$names[$n] = $value;
		}
		return $names;
	}

	public function verify() {
		foreach($this->meta_keys as $type => $meta_keys_value) {
			print ("<h2>$type</h2>");
			$c_errors = 0;
			$post_ids = $this->jsondb->get_ids($type);
			foreach($post_ids as $post_id) {
				$data = $this->jsondb->get($type, $post_id);
				$errors = array();
				foreach($meta_keys_value as $key => $meta_info) {
					if (array_key_exists('verify', $meta_info)) {
						switch ($meta_info['verify']) {
							case 'required':
								if (!$data[$meta_info['name']])
									$errors[] = sprintf("<font class=\"ccppm_required\">Required Missing data for %s</font>", $meta_info['name']);
								break;
							case 'important':
								if (!$data[$meta_info['name']])
									$errors[] = sprintf("<font class=\"ccppm_important\">Important Missing data for %s</font>", $meta_info['name']);
								break;
						}
					}
					if ($type == 'film' and $meta_info['name'] == 'available_formats') {
						if ($data[$meta_info['name']]) {
							$format = $data[$meta_info['name']][0];
							$checks = ['vo', 'format', 'sound_format'];
							foreach($checks as $check)
								if ($format[$check] == 'N/A')
									$errors[] = sprintf("<font class=\"ccppm_required\">Required Missing FORMAT for %s</font>", $check);
						}
					}
				}
				if ($errors) {
					printf ('<a href="/wp-admin/post.php?post=%s&action=edit" target="_blank">Errors found for "%s" (%s)</a>', $post_id, $data['post_title'], $post_id);
					print ("<ul>");
					foreach($errors as $error)
						printf ("<li>%s</li>", $error);
					print ("</ul>");
					$c_errors ++;
				}
			}
			if (!$c_errors)
				print ("Perfect ! no error.<br/>");
		}
	}

	public function update_film_and_partenaire() {
		$this->__fields = $this->get_fields();
		$edition_id = $this->ccppm->edition_id;
		$c_posts['film'] = 0;
		# UPDATE MOVIES
		$args = array(
//			'post__in'=>[43560],
//		  'post__in'=>[43389],
//			'post__in'=>[44260],
//			'post__in'=>[42863], // pour test de projection
			'post_type'=>'film',
			'orderby'=>'post_title',
			'order'=>'ASC',
			'offset'=>0,
			'posts_per_page'=>-1,
//			'posts_per_page'=>1,
			'meta_query' => array(
				array(
					'key' => '_status',
					'value' => array('approved', 'programmed'),
					'compare' => 'IN'
				)
			),
			'tax_query' => array(
				array(
					'taxonomy' => 'edition',
					'field' => 'id',
					'include_children' => false,
					'terms' => array ( $edition_id )
				)
			)
    );
		$query = new WP_Query($args);
		$query_data = get_posts($args);
		$c_film = 0;
		foreach($query_data as $data) {

			$data = $this->get_post_data($data, 'film');
			$post_id = $data['id'];
			$data['permalink'] = get_permalink($post_id);
			$this->jsondb->append('film', $post_id, $data);
			// if ($c > 3)
			// 	break;
			$c_posts['film'] ++;
		}

		$c_posts['contact'] = 0;
		foreach($this->tmp_contacts as $contact_id => $contact) {
			$this->jsondb->append('contact', $contact_id, $contact);
			$c_posts['contact']++;
		}
		return $c_posts;
	}

	public function update_jury() {
		$this->__fields = $this->get_fields();
		$edition_id = $this->ccppm->edition_id;
		$c_posts['jury'] = 0;
		# UPDATE JURY
		$args = array(
			'post_type'=>'jury',
			'orderby'=>'post_title',
			'order'=>'ASC',
			'offset'=>0,
			'posts_per_page'=>-1,
//			'posts_per_page'=>1,
			'tax_query' => array(
				array(
					'taxonomy' => 'edition',
					'field' => 'id',
					'include_children' => false,
					'terms' => array ( $edition_id )
				)
			)
    );
		$query = new WP_Query($args);
		$query_data = get_posts($args);
		$c_film = 0;
		foreach($query_data as $data) {
			$data = $this->get_post_data($data, 'jury');
			$post_id = $data['id'];
			$data['permalink'] = get_permalink($post_id);

			$this->jsondb->append('jury', $post_id, $data);
			$c_posts['jury'] ++;
		}
		return $c_posts;
	}

	public function update_partenaire() {
		$this->__fields = $this->get_fields();
		$edition_id = $this->ccppm->edition_id;
		$c_posts['partenaire'] = 0;
		# UPDATE PARTNERS
		$args = array(
			'post_type'=>'partenaire',
			'orderby'=>'post_title',
			'order'=>'ASC',
			'offset'=>0,
			'posts_per_page'=>-1,
//			'posts_per_page'=>1,
			'tax_query' => array(
				array(
					'taxonomy' => 'edition',
					'field' => 'id',
					'include_children' => false,
					'terms' => array ( $edition_id )
				)
			)
		);
		$query = new WP_Query($args);
		$query_data = get_posts($args);
		$c_film = 0;
		foreach($query_data as $data) {
			$data = $this->get_post_data($data, 'partenaire');
			$post_id = $data['id'];
			$data['permalink'] = get_permalink($post_id);

			$this->jsondb->append('partenaire', $post_id, $data);
			$c_posts['partenaire'] ++;
		}
		return $c_posts;
	}

	public function update_accreditation($post_ids = False) {
		$this->__fields = $this->get_fields();
		$edition_id = $this->ccppm->edition_id;
		$c_posts['accreditation'] = 0;
		# UPDATE PARTNERS
		$args = array(
			'post_type'=>'accreditation',
			'orderby'=>'post_title',
			'order'=>'ASC',
			'offset'=>0,
			'posts_per_page'=>-1,
//			'posts_per_page'=>1,
			'tax_query' => array(
				array(
					'taxonomy' => 'edition',
					'field' => 'id',
					'include_children' => false,
					'terms' => array ( $edition_id )
				)
			)
		);
		if ($post_ids)
			$args['post__in'] = $post_ids;

		$query = new WP_Query($args);
		$query_data = get_posts($args);
		$projection_guest_ids = $this->jsondb->get_index('projection', 'guest_id');
		foreach($query_data as $data) {
			$data = $this->get_post_data($data, 'accreditation');
			$post_id = $data['id'];
			$data['permalink'] = get_permalink($post_id);
			if (!is_string($data['contact']) && array_key_exists('id', $data['contact']))
				$contact_id = $data['contact']['id'];
			else
				$contact_id = null;

			$projections = array();
			//print "contact_id = $contact_id replaced by : 43125 for tests<br/>";
			//$contact_id = 43125;
			if (array_key_exists($contact_id, $projection_guest_ids)) {
				foreach($projection_guest_ids[$contact_id] as $projection_id) {
					$projection = $this->jsondb->get('projection', $projection_id);
					$film_names = [];
					foreach($projection['film'] as $post_id)
						$film_names[] = $this->jsondb->get_quick_name('film', $post_id);
					foreach($projection['films'] as $post_id)
						$film_names[] = $this->jsondb->get_quick_name('film', $post_id);
					$projection['film'] = implode(', ', $film_names); 
					$projection['room'] = $projection['room'][0];
					$cinema_id  = $projection['room']['parent'];
					$projection['cinema'] = $this->jsondb->get('room', $cinema_id);
					$projections[] = $projection;
				}
			}
			$data['projections'] = $projections;
			$this->jsondb->append('accreditation', $post_id, $data);
			$c_posts['accreditation'] ++;
		}
		return $c_posts;
	}

	private function get_option_values(&$data, $field) {
		foreach($field['data']['options'] as $f_index => $f_option) {
			if ($f_index != 'default')
				$data[$f_option['value']] = $f_option['title'];
		}
	}

	public function update_projection() {
		$this->__fields = $this->get_fields();
		$edition_id = $this->ccppm->edition_id;
		$c_posts['projection'] = 0;
		# UPDATE PROJECTIONS
		$args = array(
			'post_type'=>'projection',
			'orderby'=>'post_title',
			'order'=>'ASC',
			'offset'=>0,
			'posts_per_page'=>-1,
//			'posts_per_page'=>1,
			'tax_query' => array(
				array(
					'taxonomy' => 'edition',
					'field' => 'id',
					'include_children' => false,
					'terms' => array ( $edition_id )
				)
			)
		);


		$query = new WP_Query($args);
		$query_data = get_posts($args);
		$c_film = 0;
		foreach($query_data as $data) {
			$data = $this->get_post_data($data, 'projection');
			$post_id = $data['id'];
			$data['permalink'] = get_permalink($post_id);
			$data['films'] = $this->get_toolset_relations($post_id, 'projection', 'film');
#			print "projection : $post_id<br>";
			$this->jsondb->append('projection', $post_id, $data);
#			print_r($data);
#			print "<br/>";
			$c_posts['projection'] ++;
		}
		return $c_posts;
	}

	private function get_toolset_relations($post_id, $father, $son) {
		global $wpdb;
		if (array_key_exists($father, $this->relations) && array_key_exists($son, $this->relations[$father])) {
			$slug = $this->relations[$father][$son]['slug'];
			$order = null;
			if (array_key_exists('order', $this->relations[$father][$son]))
				$order = $this->relations[$father][$son]['order'];
			$sql = sprintf("SELECT rs.id, ttsP.type as parentType, ttsC.type as childType FROM %stoolset_relationships as rs, %stoolset_type_sets as ttsP, %stoolset_type_sets as ttsC WHERE slug = '%s' AND ttsP.set_id = parent_types AND ttsC.set_id = child_types;", $wpdb->prefix, $wpdb->prefix, $wpdb->prefix, $slug);
			$dataInfo = $wpdb->get_results( $sql, ARRAY_A );
			$relationshipId = $dataInfo[0]['id'];
			if ($dataInfo[0]['parentType'] == $father) {
				$sonIdField = 'child_id';
				$fatherIdField = 'parent_id';
			} else {
				$sonIdField = 'parent_id';
				$fatherIdField = 'child_id';
			}
			$sql = sprintf("SELECT %s as son_id, intermediary_id FROM %stoolset_associations WHERE relationship_id = '%s' and %s = '%s';", $sonIdField, $wpdb->prefix, $relationshipId, $fatherIdField, $post_id);
			$results = $wpdb->get_results($sql, ARRAY_A );
			$data = [];
			if ($results) foreach($results as $d) {
				$son_id = $d['son_id'];
				$intermediary_id = $d['intermediary_id'];
				if ($intermediary_id && $order) {
					$data[$son_id] = get_post_meta($intermediary_id, $order, True);
				} else {
					$data[$son_id] = 0;
				}
			}
			if ($order)
				asort($data);
			$son_ids = array_keys($data);
			foreach($son_ids as $son_id)
				$this->jsondb->append_index_forced($father, [$post_id], $son, $son_id);
			return $son_ids;
		}
	}

	public function update_planning($remove_first_date = False, $errors = True) {
		$this->__fields = $this->get_fields();
		$edition_id = $this->ccppm->edition_id;
		$edition_start = date('Y-m-d', get_term_meta( $edition_id, 'wpcf-e-start-date', True ));
		$edition_end = date('Y-m-d', get_term_meta( $edition_id, 'wpcf-e-end-date', True ));
		$first_date = $edition_start;
		$films = $this->jsondb->get_ids('film');
		$projections = $this->jsondb->get_ids('projection');
		$a_projections = array();
		$a_rooms_ids = array();
		$a_dates = array();
		$a_films = array();
		$deltatime = "20";

		$fields = get_option('wpcf-fields');
		$tags = $notices_internal = [];
		$this->get_option_values($tags, $fields['p-tag']);
		$this->get_option_values($notices_internal, $fields['p-notice-internal']);

		foreach($projections as $projection_id) {
			$projection = $this->jsondb->get('projection', $projection_id);
			if (array_key_exists('isoformat', $projection['date'])) {
				$date = $projection['date'];
				if (($date['isoformat'] != $first_date || !$remove_first_date) && $edition_start <= $date['isoformat'] && $date['isoformat'] <= $edition_end) {
					$a_dates[$date['isoformat']] = $date['day'];

					$room_id = False;
					if (is_array($projection['room'])) foreach($projection['room'] as $t_room) {
						$t_room_id = $t_room['term_id'];
						$t_parent_room_id = $t_room['parent'];
						if ($room_id === False) {
							$room_id = $t_room_id;
						} elseif ($t_parent_room_id == $room_id) {
							$room_id = $t_room_id;
						}
					}
					if (!$room_id) {
						$room_id = 'unknown';
						if ($errors) printf ("No room for %s %s %s<br/>", $date['date'], $projection['start_and_stop_time']['begin'], $projection['post_title']);
					} else {
						$room = $this->jsondb->get('room', $room_id);
					}
					if (!in_array($room_id, $a_rooms_ids))
						$a_rooms_ids[]= $room_id;
					$hour = $projection['start_and_stop_time']['begin'];
//					if ($projection['start_and_stop_time']['end'] == '')
//						$projection['start_and_stop_time']['end'] = $projection['start_and_stop_time']['begin'];
					if ($projection['tag'])
						$projection['tag'] = $tags[$projection['tag']];

					if ($projection['notice_internal'])
						$projection['notice_internal'] = $notices_internal[$projection['notice_internal']];

					list($h, $m) = explode(':', $hour);
					$ms = $m + $h * 60;
					$ms += $deltatime;
					$m = $ms % 60;
					$h = intval($ms / 60);
					$hour = sprintf("%02d:%02d", $h, $m);

					$timing = '_';
					$a_hours = $room['timing'];
					if ($a_hours) {
						foreach($a_hours as $a_hour) {
							if (array_key_exists('real_start', $a_hour) and $a_hour['real_start'] <= $hour and $hour < $a_hour['end'])
								$timing = $a_hour['start'];
							elseif ($a_hour['start'] <= $hour and $hour < $a_hour['end'])
								$timing = $a_hour['start'];
						}
					}
/*
					if ($timing == '_') {
						print "Planning problem<br/>";
						print "a_hours : ";
						print_r($a_hours);
						print("<br/>");	
						print_r($projection);
						print("<br/>");	
					}
*/
					if (!array_key_exists($room_id, $a_projections))
						$a_projections[$room_id] = array();
					if (!array_key_exists($date['isoformat'], $a_projections[$room_id]))
						$a_projections[$room_id][$date['isoformat']] = array();
					if (!array_key_exists($timing, $a_projections[$room_id][$date['isoformat']]))
						$a_projections[$room_id][$date['isoformat']][$timing] = array();
						
					if (is_array($projection['film'])) foreach($projection['film'] as $film_id) {
						if (! array_key_exists($film_id, $a_films)) {
							$a_films[$film_id] = $this->jsondb->get('film', $film_id);
							if (is_array($a_films[$film_id]['section'])) foreach($a_films[$film_id]['section'] as $t_section_idx => $t_section) {
								$t_section_id = $t_section['term_id'];
								$section = $this->jsondb->get('section', $t_section_id);
								$a_films[$film_id]['section'][$t_section_idx]['color'] = $section['color_print'];
							}
						}
					}
					if (is_array($projection['films'])) foreach($projection['films'] as $film_id) {
						if (! array_key_exists($film_id, $a_films)) {
							$a_films[$film_id] = $this->jsondb->get('film', $film_id);
							if (is_array($a_films[$film_id]['section'])) foreach($a_films[$film_id]['section'] as $t_section_idx => $t_section) {
								$t_section_id = $t_section['term_id'];
								$section = $this->jsondb->get('section', $t_section_id);
								$a_films[$film_id]['section'][$t_section_idx]['color'] = $section['color_print'];
							}
						}
					}
					$a_projections[$room_id][$date['isoformat']][$timing][] = $projection;
					$a_projections[$room_id][$date['isoformat']][$timing] = $this->ccppm->catalogues->get_catalogue_data_order_by($a_projections[$room_id][$date['isoformat']][$timing], 'projection');
				}
			}
		}

		foreach($a_projections as $room_id => $a_room) {
			foreach($a_room as $a_date_id => $a_date) {
				ksort($a_projections[$room_id][$a_date_id]);
			}
			ksort($a_projections[$room_id]);
		}

		ksort($a_dates);

		$t_rooms = $this->jsondb->get_index('room', 'parent');
//		$o_rooms = [];
		foreach($t_rooms as $t_room_parent_id => $t_room_ids) {
			$room_parent = $this->jsondb->get('room', $t_room_parent_id);
			$rooms = array();
			foreach($t_room_ids as $t_room_id) {
				if (in_array($t_room_id, $a_rooms_ids)) {
					$room = $this->jsondb->get('room', $t_room_id);
					$rooms[] = $room;
				}
			}
			if ($rooms) {
				usort($rooms, function($a, $b) {
					return $a['_order'] <=> $b['_order'];
				});
				$room_parent['rooms'] = $rooms;
				$a_rooms[] = $room_parent;
			} else {
//				$o_rooms[] = $room_parent;
			}
		}
/*
		$a_rooms[] = [
			'term_id'=>'_',
			'slug'=>'_',
			'rooms'=>$o_rooms,
		];
*/
		usort($a_rooms, function($a, $b) {
			return $a['_order'] <=> $b['_order'];
		});

		$data = array(
			'projections'=>$a_projections,
			'rooms'=>$a_rooms,
			'dates'=>$a_dates,
			'films'=>$a_films,
			'name'=>'Planning',
		);
		$this->jsondb->append('planning', 'planning', $data);
		return $c_posts;
	}

	public function get_film_ids_by_section_ids($index_film_section, $section_ids) {
		$film_ids = [];
		foreach($section_ids as $section_id)
			if (array_key_exists($section_id, $index_film_section))
				$film_ids = array_merge($film_ids, $index_film_section[$section_id]);
		return array_unique($film_ids);
	}

	private function update_sections_planning_get_sections_rec($index_section_parent, $parent_id, &$section_ids) {
		if (array_key_exists($parent_id, $index_section_parent)) {
			foreach($index_section_parent[$parent_id] as $section_id) {
				if (!array_key_exists($section_id, $section_ids))
					$section_ids[$section_id] = []; 
				$t_section_ids = [];
				$this->update_sections_planning_get_sections_rec($index_section_parent, $section_id, $t_section_ids);
				foreach($t_section_ids as $t_section_id => $tmp)
					if ($t_section_id != $section_id)
						$section_ids[$section_id][] = $t_section_id;
				if ($t_section_ids)
					foreach($t_section_ids as $child_id => $subchilds)
						if (!array_key_exists($child_id, $section_ids))
							$this->update_sections_planning_get_sections_rec($index_section_parent, $child_id, $section_ids);
			}
		} else {
			$section_ids[$parent_id] = [];
		}
	}

	public function update_sections_planning() {
		print "<h3>Update Sections</h3>";
		
		$edition_id = $this->ccppm->edition_id;
		$edition_start = date('Y-m-d', get_term_meta( $edition_id, 'wpcf-e-start-date', True ));
		$edition_end = date('Y-m-d', get_term_meta( $edition_id, 'wpcf-e-end-date', True ));
		$first_date = $edition_start;
		$film_ids = $this->jsondb->get_ids('film');
		$projection_ids = $this->jsondb->get_ids('projection');
		$index_section_parent = $this->jsondb->get_index('section', 'parent');
		$index_film_section = $this->jsondb->get_index('film', 'section');
		$index_projection_film = $this->jsondb->get_index('projection', 'film');
		$master_id = False;
		$section_ids = [];
		foreach($index_section_parent as $parent_id => $child_ids) {
			$section = $this->jsondb->get('section', $parent_id);
			if ($section['parent'] == 0)
				$master_id = $parent_id;
		}
		if (array_key_exists($master_id, $index_section_parent)) {
			$section_ids = [];
			$this->update_sections_planning_get_sections_rec($index_section_parent, $master_id, $section_ids);
		}
		foreach ($section_ids as $section_id => $child_ids) {
			$film_ids = $this->get_film_ids_by_section_ids($index_film_section, array_merge([$section_id], $child_ids));
			
			$section = $this->jsondb->get('section', $section_id);
			$projections = [];
			$projections_ids = [];
			$projections_md5 = [];
			foreach($film_ids as $film_id) {
				if (array_key_exists($film_id, $index_projection_film)) {
					$projection_ids = $index_projection_film[$film_id];
					foreach($projection_ids as $projection_id) {
						$projection = $this->jsondb->get('projection', $projection_id);
						$isoformat = $projection['date']['isoformat'];
						$time = $projection['start_and_stop_time']['begin'];
						$dt = "$isoformat $time:00";
						if (!array_key_exists($dt, $projections))
							$projections[$dt] = [];
						$md5 = False;

						if ((is_string($projection['film']) || !count($projection['film'])) && !count($projection['films']))
							$md5 = md5($projection['post_title']);
						else
							$md5 = md5(json_encode(['film'=>$projection['film'], 'films'=>$projection['films']], True));
			//			print $projection['post_title']." ".$film_id." ".$md5."<br/>";
//						if (!in_array($projection['id'], $projections_ids)) {
						if (!array_key_exists($md5, $projections_md5)) {
							$projections_md5[$md5] = ['idx' => count($projections[$dt]), 'dt'=>$dt, 'id'=>$projection['id']];
							$projections[$dt][] = $projection;
							$projections_ids[] = $projection['id'];
						} else {
							$meta = $projections_md5[$md5];
							$before_projection = $projections[$meta['dt']][$meta['idx']];
							$before_projection['is_guest'] = $before_projection['is_guest'] || $projection['is_guest'];
							$before_projection['is_debate'] = $before_projection['is_debate'] || $projection['is_debate'];
							$before_projection['is_highlighted'] = $before_projection['is_highlighted'] || $projection['is_highlighted'];
							$projections[$meta['dt']][$meta['idx']] = $before_projection;
						}
					}
				} else {
					print "<div class='ccppm_data_warning'>Warning: pas de projection pour film $film_id<br/></div>";
				}
			}
			ksort($projections);
			$t_projections = [];
			foreach($projections as $dt => $pjs)
				foreach($pjs as $projection)
					$t_projections[] = $projection;
			$section['projections'] = $t_projections;
			$this->jsondb->append('section', $section_id, $section);
		}
	}

	public function update() {
		$edition_id = $this->ccppm->edition_id;
		if (!$edition_id) {
			print '<div class="ccppm_data_notice">Please select "edition" in tool bar</div>';
			return False;
		}

		$this->__fields = $this->get_fields();

//		$this->jsondb->remove_all();
		$c_posts = array();

		$this->ccppm->log('[ DATA.UPDATE ] update_film_and_partenaire');
		$c_posts_tmp = $this->update_film_and_partenaire();
		$c_posts = array_merge($c_posts, $c_posts_tmp);

		$this->ccppm->log('[ DATA.UPDATE ] update_jury');
		$c_posts_tmp = $this->update_jury();
		$c_posts = array_merge($c_posts, $c_posts_tmp);

		$this->ccppm->log('[ DATA.UPDATE ] update_partenaire');
		$c_posts_tmp = $this->update_partenaire();
		$c_posts = array_merge($c_posts, $c_posts_tmp);

		$this->ccppm->log('[ DATA.UPDATE ] update_projection');
		$c_posts_tmp = $this->update_projection();
		$c_posts = array_merge($c_posts, $c_posts_tmp);

		$this->ccppm->log('[ DATA.UPDATE ] update_accreditation');
		$c_posts_tmp = $this->update_accreditation();
		$c_posts = array_merge($c_posts, $c_posts_tmp);

		$this->ccppm->log('[ DATA.UPDATE ] update_terms');
		foreach($this->terms as $type => $t_value)
			foreach($t_value as $k_taxonomy => $v_taxonomy) {
				$args = array(
						'taxonomy' => $k_taxonomy,
			    	'hide_empty' => false,
						'meta_query' => array(),
				);
				if (array_key_exists('edition', $v_taxonomy)) {
					$key = $v_taxonomy['edition'];
					$args['meta_query'][] = array(
            'key'       => $key,
            'value'     => $edition_id,
            'compare'   => '='
			    );
				}
				$terms = get_terms( $args );
				$c_terms[$k_taxonomy] = 0;
				foreach($terms as $term) {
					$data = get_object_vars($term);
					$data['_order'] = intval($data['term_order']);
					unset($data['term_order']);
					$this->get_terms_metas($k_taxonomy, $data);
#					print("<br/><br/>------ $k_taxonomy ------ ".$data['term_id']."<br/>");
#					print_r($
					$this->jsondb->append($k_taxonomy, $data['term_id'], $data);
					$c_terms[$k_taxonomy]++;
				}
			}
		

		$html = "<div class='ccppm_data_info'><ul>\n";
		foreach($c_posts as $k => $v)
			$html .= "<li>$k : $v</li>\n";
		foreach($c_terms as $k => $v)
			$html .=  "<li>$k : $v</li>\n";
		$html .=  "</ul></div>\n";
		$this->ccppm->data->update_planning(False, False);
		$html .= "<div class='ccppm_data_success'>Planning succesfully updated !</div>";
		$this->ccppm->data->update_sections_planning();
		$html .= "<div class='ccppm_data_success'>Section w/ planning succesfully updated !</div>";
		print "$html\n";
		$this->ccppm->log(strip_tags($html));
	}

	public function convert_aside_to_img_table($html) {
//		$html = "aaaa<aside>\n<p>[caption id=\"attachment_58694\" align=\"alignleft\" width=\"250\"]<img class=\"wp-image-58694\" alt=\"\" width=\"250\" height=\"250\" src=\"data:image/jpg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/4QlQaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLwA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/PiA8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJBZG9iZSBYTVAgQ29yZSA1LjYtYzEzOCA3OS4xNTk4MjQsIDIwMTYvMDkvMTQtMDE6MDk6MDEgICAgICAgICI+IDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+IDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiLz4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8P3hwYWNrZXQgZW5kPSJ3Ij8+/9sAQwAGBAQFBAQGBQUFBgYGBwkOCQkICAkSDQ0KDhUSFhYVEhQUFxohHBcYHxkUFB0nHR8iIyUlJRYcKSwoJCshJCUk/8AACwgAHwAfAQERAP/EABkAAQACAwAAAAAAAAAAAAAAAAYDBQQHCP/EACoQAAEEAQMDAwMFAAAAAAAAAAECAwQRBQASIQYxQRMUIgcVYTJRcYGR/9oACAEBAAA/ACMBcn3ltxvcJDtFsqA3n8XpDMgJx7MZafVuW9S0L2qFoURVpNWNx0fzDLok+nF3JC0haglVJA5vVdHfktykpU44bu/keeNI8JufbnSWPQkDHSAtQA3KaBJIc8/EEVYujpb1llImbjYTIsqaEdM9bCCzQSsbUKUpIocbifHjWDAagrzsT7iWG4wj7CVoG0mzW7keTdk+NHHY0aN1GZCUqcguOLKFHkdjt57cjnRjFZORhXjkMa4WVqbW04kGgttQpSdW0rMR5vSuHxaFerIhFx1TibSE7wBso91Cgd3bxz3066VwCeuZuGakb/Yhk+8cAIFJIJTf7mq/vTjq5OHhxnITOPYMZ131VNgcE+P4qqAHYD865jjuW2ttXIIvUqAWSlaf0qAOtr/SbqSTFjtRHUlUVEoBtSVfPcsgFO0/Ej8mq0l+p8lOOsIbdW+hfybBAGyyAQf81//Z\"/> Ceci est une légende[/caption]</p>\n<p>À l’âge de dix-neuf ans, Jacques Perrin débute au théâtre à Paris dans <em>L’année du bac</em>, mise en scène par Yves Boisset (au Théâtre Edouard VII, avec Sami Frey). C’est là que le cinéaste italien Valerio Zurlini le repère et l’invite pour le casting de <em>La fille à la valise</em>. La figure juvénile de l’acteur convient au cinéaste. Aux côtés de Claudia Cardinale et Gian Maria Volonte, Jacques Perrin débute une carrière italienne qui durera une dizaine d’années (3). Carrière évidemment entrecoupée par d’autres rôles dans le cinéma français. À se pencher un peu sur les véritables débuts de Jacques Perrin, il est évident que Valerio Zurlini fut celui qui sut le premier prendre la mesure de son talent, celui qui demande à l’acteur de dire sa nature profonde pour mieux exprimer ce que ressent le personnage. En 1960, le cinéma italien était à un tournant, c’était un cinéma dans lequel la jeunesse voulait vivre, se laisser aller au lendemain de la guerre. Le néo-réalisme commençait à faire partie du passé. Les jeunes des familles aisées cherchaient le soleil des plages de l’Adriatique. Et cela passait dans le cinéma.</aside>aaaaaa";
		$html = str_replace("\n", '', $html);
		$html = str_replace("<p>[caption", '[caption', $html);
		$html = str_replace("[/caption]</p>", "[/caption]", $html);
		//		preg_match_all('/<aside>(((?!<\/aside>).)*)(<img[^>]+>)(((?!<\/aside>).)*)<\/aside>/', $html, $outputs, PREG_SET_ORDER);
		preg_match_all('/<aside>(((?!<\/aside>).)*)(<img[^>]+>)(((?!<\/aside>).)*)<\/aside>/', $html, $outputs, PREG_SET_ORDER);
		foreach($outputs as $output) {
			$all = $output[0];
			if (preg_match('/.*\[caption.*/', $all)) {
				preg_match('/.*((\[caption([^\]]*)\])(<img[^>]+>)(((?!\[\/caption\]).)*)\[\/caption\]).*/', $all, $output2);
				$tmp_all = $all;
				$tmp_all = str_replace('<aside>', '', $tmp_all);
				$tmp_all =  str_replace('</aside>', '', $tmp_all);
				$position = 'left';
				foreach(explode(' ', $output2[3]) as $attributes) {
					if ($attributes) {
						list($attr, $value) = explode('=', $attributes);
						$value = str_replace('"', '', $value);
//						print "attr $attr : $value<br/>";
						if ($attr == 'align') {
							$align = explode(' ', $value);
							if (in_array('aligncenter', $align)) $position = 'center';
							if (in_array('alignright', $align)) $position = 'right';
							if (in_array('alignleft', $align)) $position = 'left';
						}
					}
				}
//				print htmlentities($output2[3])."<br/>";
				$tmp = explode($output2[1], $tmp_all);
				$beforeImg = trim($tmp[0]);
				$afterImg = trim($tmp[1]);
				$img = sprintf('%s<p class="caption">%s</p>', trim($output2[4]), trim($output2[5]));
//				print htmlentities($img)."<br/>";

				$content = '</div><div><table data-pdfmake="{&quot;layout&quot;:&quot;noBorders&quot;}"><tr>';
				switch($position) {
					case 'left':
						$content.= sprintf("<td>%s</td><td>%s</td>", $img, implode(' ', array_filter([$beforeImg, $afterImg])));
						break;
					case 'right':
						$content.= sprintf("<td>%s</td><td>%s</td>", implode(' ', array_filter([$beforeImg, $afterImg])), $img);
						break;
					case 'center':
						$content.= sprintf("<td>%s</td><td>%s</td><td>%s</td>", $beforeImg, $img, $afterImg);
						break;
				}
				$content .= '</tr></table></div><div>';
	//			$content = '';
//				print "position : $position <br/>";
//				print htmlentities($content)."<br/>";
				$html = str_replace($all, $content, $html);				
			} else {
				$beforeImg = trim($output[1]);
				$afterImg = trim($output[4]);
				$img = $output[3];
				$position = 'left';
				preg_match('/^<img.*class="([^"]+)".*>$/', $img, $output_classes);
				$classes = explode(' ', $output_classes[1]);
				if (in_array('aligncenter', $classes)) $position = 'center';
				if (in_array('alignright', $classes)) $position = 'right';
				if (in_array('alignleft', $classes)) $position = 'left';

				$content = '</div><div><table data-pdfmake="{&quot;layout&quot;:&quot;noBorders&quot;}"><tr>';
				switch($position) {
					case 'left':
						$content.= sprintf("<td>%s</td><td>%s</td>", $img, implode(' ', array_filter([$beforeImg, $afterImg])));
						break;
					case 'right':
						$content.= sprintf("<td>%s</td><td>%s</td>", implode(' ', array_filter([$beforeImg, $afterImg])), $img);
						break;
					case 'center':
						$content.= sprintf("<td>%s</td><td>%s</td><td>%s</td>", $beforeImg, $img, $afterImg);
						break;
				}
				$content .= '</tr></table></div><div>';
	//			$content = '';
				$html = str_replace($all, $content, $html);
			}
		}
		return '<div>'.$html.'</div>';
	}

	public function convert_caption_to_p($html) {
//		print "<pre>$html</pre>";
		preg_match_all('/(\[caption([^\]]*)\])(<img[^>]+>)(((?!\[\/caption\]).)*)\[\/caption\]/', $html, $outputs, PREG_SET_ORDER);
//		preg_match_all('/(\[caption[^\]]*\](((?!\[\/caption\]).)*)[\/caption\])', $html, $outputs, PREG_SET_ORDER);
		foreach($outputs as $output) {
			$content = sprintf('%s<p class="caption">%s</p>', trim($output[3]), trim($output[4]));
			$html = str_replace($output[0], $content, $html);
		}
		return $html;
	}

	public function get_terms_metas($taxonomy, &$data) {
		$term_id = $data['term_id'];
//		foreach($this->meta_keys_terms as $t_taxonomy => $t_values) {
		if (array_key_exists($taxonomy, $this->meta_keys_terms)) {
			$t_values = $this->meta_keys_terms[$taxonomy];
			foreach($t_values as $t_key => $t_infos) {
				switch($t_infos['type']) {
					case 'wprte':
						$uniq = (array_key_exists('uniq', $t_infos) and $t_infos['uniq'])?True:False;
						$data[$t_infos['name']] = get_term_meta($term_id, $t_key, $uniq);
						if ($uniq) {
							$value = 
							$data[$t_infos['name']] = str_replace("\r", "", $data[$t_infos['name']]);
							$data[$t_infos['name']] = str_replace("\n", "<br/>", $data[$t_infos['name']]);
							# @todo: justin placer ici la création du tableau
#							$data[$t_infos['name']] = str_replace("<!--", "{!--", $data[$t_infos['name']]);
#							$data[$t_infos['name']] = str_replace("-->", "--}", $data[$t_infos['name']]);
							$data[$t_infos['name']] = strip_tags($data[$t_infos['name']], $this->strip_tags_allowed);
							$data[$t_infos['name']] = wpautop($data[$t_infos['name']]);
							if (preg_match('/.*<aside>.*/', $data[$t_infos['name']])) {
								$data[$t_infos['name']] = $this->convert_aside_to_img_table($data[$t_infos['name']]);
							}
							if (preg_match('/.*\[caption.*/', $data[$t_infos['name']])) {
								$data[$t_infos['name']] = $this->convert_caption_to_p($data[$t_infos['name']]);
							}
							$data[$t_infos['name']] = $this->ccppm->catalogues->convert_html_img_inline($this->dpi, $data[$t_infos['name']]);
						}
						break;
					case 'json':
						$uniq = (array_key_exists('uniq', $t_infos) and $t_infos['uniq'])?True:False;
						$data[$t_infos['name']] = get_term_meta($term_id, $t_key, $uniq);
						if ($uniq)
							$data[$t_infos['name']] = json_decode($data[$t_infos['name']], True);
						break;
					case 'cmjn':
						$uniq = (array_key_exists('uniq', $t_infos) and $t_infos['uniq'])?True:False;
						$data[$t_infos['name']] = get_term_meta($term_id, $t_key, $uniq);
						if ($uniq) {
							$data[$t_infos['name']] = explode(',', $data[$t_infos['name']]);
							if (count($data[$t_infos['name']]) == 1)
								$data[$t_infos['name']] = false;
						} else {
							foreach($data[$t_infos['name']] as $idx => $value) {
								$data[$t_infos['name']][$idx] = explode(',', $value);
								if (count($data[$t_infos['name']][$idx]) == 1)
									$data[$t_infos['name']][$idx] = false;
							}
						}
						break;
					case 'picture':
						$uniq = (array_key_exists('uniq', $t_infos) and $t_infos['uniq'])?True:False;
						$data[$t_infos['name']] = get_term_meta($term_id, $t_key, $uniq);
						if ($uniq) {
							$url = $data[$t_infos['name']];
							if ($url) {
								$post_id = $this->get_attachment_id($url);
								if ( ! $post_id && array_key_exists('orginalPicture', $t_infos)) {
									$post_id = $this->get_attachment_id($data[$t_infos['orginalPicture']]['url']);
								}
								$data[$t_infos['name']] = ['url'=>$url, 'base64' => $this->convert_file_to_base64($url, (array_key_exists('sizes', $t_infos))?$t_infos['sizes']:False, (array_key_exists('cover', $t_infos))?$t_infos['cover']:False, False, $post_id)];
							} else
								$data[$t_infos['name']] = False;
						} else {
							$pictures = array();
							$urls = $data[$t_infos['name']];
							foreach($urls as $url) {
								if ($url) {
									$post_id = $this->get_attachment_id($url);
									$pictures[] = ['url'=>$url, 'base64' => $this->convert_file_to_base64($url, (array_key_exists('sizes', $t_infos))?$t_infos['sizes']:False, (array_key_exists('cover', $t_infos))?$t_infos['cover']:False, False, $post_id)];
								}
							}
							$data[$t_infos['name']] = $pictures;
						}
						break;
					default:
						$uniq = (array_key_exists('uniq', $t_infos) and $t_infos['uniq'])?True:False;
						$data[$t_infos['name']] = get_term_meta($term_id, $t_key, $uniq);
						$data[$t_infos['name']] = $this->convert_special_char($data[$t_infos['name']]);
						break;
				}
			}
		}
/*
		print("<pre>");
		print_r($data);
		print("</pre>");
*/
	}

	public function store_file_get_final($dpi, $file, $meta, $url = false) {
		if ($file == false and $url == false) {
			return "[ store_file_get_final ] no image or url\n";
		}
		if ($file == false and $url) {
			$md5 = md5($url);
			$file = $md5;
		}
		if (is_array($file)) {
			return "[ ccppm-data.store_file_get_final] ERROR !!!! file name is an ARRAY, should be a string\n";
		}
		$add_md5 = md5(json_encode($meta, True));
		$width = False;
		$height = False;
		if (array_key_exists('width', $meta))
			$width = $meta['width'];
		if (array_key_exists('height', $meta))
			$height = $meta['height'];
		$file_dst = sprintf("%s-%s", $file, $add_md5);
		$m1 = substr($file, 0, 1);
		$m2 = substr($file, 1, 2);
		$path = sprintf("%s/%s/%s/", $this->storage_path, $m1, $m2);
		$filename_src = sprintf('%s/%s', $path, $file);
		$filename_dst = sprintf('%s/%s', $path, $file_dst);
		if (!file_exists($filename_dst) or filemtime($filename_src) > filemtime($filename_dst)) {
			$data = explode(';base64,', @file_get_contents($filename_src));
			if (count($data) > 1) {
				list($tmp, $type) = explode('/', $data[0]);
				$data = $data[1];
				$imagick = new Imagick();
				$data = base64_decode($data);
				$imagick->readImageBlob($data);
				$bestfit = True;
				if($width)
					$width *= $dpi / 72;
				else
					$bestfit = False;
				if($height)
					$height *= $dpi / 72;
				else
					$bestfit = False;
#				$this->ccppm->log("[ DATA.UPDstore_file_get_final ] $dpi $file ".json_encode($meta));
				$imagick->resizeImage($width, $height, imagick::FILTER_LANCZOS, 1, $bestfit);
				$data = 'data:image/' . $type . ';base64,' . base64_encode($imagick->getimageblob());
				file_put_contents($filename_dst, $data);
#				$this->ccppm->log("[ DATA.UPDstore_file_get_final END ] $dpi $file ".json_encode($meta));
				return $data;
			}
		} else {
			return file_get_contents($filename_dst);
		}
	}

	public function get_attachment_id($image_url = False) {
		global $wpdb;
		$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ));
			return $attachment[0];
		return False;
	}

	public function store_file_get_data($url, $name = False, $ratio = False) {
		$md5 = md5($url);
		if ($name)
			$file = sprintf("%s-%s", $md5, $name);
		else
			$file = $md5;
		$m1 = substr($md5, 0, 1);
		$m2 = substr($md5, 1, 2);
		$path = sprintf("%s/%s/%s/", $this->storage_path, $m1, $m2);
		if (!is_dir($path))
			if (!mkdir($path, 0755, True))
				printf("Can't create %s<br/>", $path);
		$filename = sprintf("%s%s", $path, $file);
		return ['filename'=>$filename, 'md5'=>$md5, 'file'=>$file, 'path'=>$path];
	}

	public function store_file_set($data, $content) {
		if (!is_dir($data['path']))
			if (!mkdir($data['path'], 0755, True))
				printf("Can't create %s<br/>", $path);
		if (file_put_contents($data['filename'], $content))
			return $data['filename'];
		return False;
	}

	public function autorotate(Imagick $image)
	{
		switch ($image->getImageOrientation()) {
			case Imagick::ORIENTATION_TOPLEFT:
				break;
			case Imagick::ORIENTATION_TOPRIGHT:
				$image->flopImage();
				break;
			case Imagick::ORIENTATION_BOTTOMRIGHT:
				$image->rotateImage("#000", 180);
				break;
			case Imagick::ORIENTATION_BOTTOMLEFT:
				$image->flopImage();
				$image->rotateImage("#000", 180);
				break;
			case Imagick::ORIENTATION_LEFTTOP:
				$image->flopImage();
				$image->rotateImage("#000", -90);
				break;
			case Imagick::ORIENTATION_RIGHTTOP:
				$image->rotateImage("#000", 90);
				break;
			case Imagick::ORIENTATION_RIGHTBOTTOM:
				$image->flopImage();
				$image->rotateImage("#000", 90);
				break;
			case Imagick::ORIENTATION_LEFTBOTTOM:
				$image->rotateImage("#000", -90);
				break;
			default: // Invalid orientation
				break;
		}
		$image->setImageOrientation(Imagick::ORIENTATION_TOPLEFT);
	}

	public function convert_file_to_base64($url, $sizes = False, $cover = False, $black_and_white = False, $post_id = False) {
		if ($url == '')
			return '';
		$url_parse = parse_url($url);
		$path = '';
		switch($url_parse['host']) {
			case 'dev.fifam.fr':
				$path = "/home/fifam.fr/vhosts/dev/htdocs";
				break;
			case 'www.fifam.fr':
				$path = "/home/fifam.fr/vhosts/www/htdocs";
				break;
		}
		$file = sprintf("%s%s", $path, $url_parse['path']);
		if ($sizes === False) {
			$data = $this->store_file_get_data($url);
			if ($this->pictures_enabled) {
				if (!is_file($data['filename'])) {
					$type = pathinfo($file, PATHINFO_EXTENSION);
					if (!in_array(strtolower($type), ['jpeg', 'jpg', 'png'])) {
//@todo : a remettre						print "ERROR FOR $file<br/>";
						//      return "data:image/png;base64,";
						return "";
					}
					$imagick = new \Imagick(realpath($file));
					if ($black_and_white) $imagick->setImageColorSpace(Imagick::COLORSPACE_GRAY);
					$this->autorotate($imagick);
					$imagick->resizeImage($this->picture_max_size['width'], $this->picture_max_size['height'], imagick::FILTER_LANCZOS, 1, TRUE);
					$base64 = 'data:image/' . $type . ';base64,' . base64_encode($imagick->getimageblob());
					$this->store_file_set($data, $base64);
				}
				return $data['file'];
			}
			return False; //data:image/png;base64,";
		} else {
			$type = pathinfo($file, PATHINFO_EXTENSION);
			$base64 = array();
			foreach($sizes as $name => $ratio) {
				if (!in_array(strtolower($type), ['jpeg', 'jpg', 'png'])) {
//@todo: a remettre					print "ERROR FOR $file<br/>";
					$base64[$name] = false; //data:image/png;base64,";
				} else {
					$data = $this->store_file_get_data($url, $name);
					$recrop = $this->get_recrop_data($post_id, $name, $ratio);
					if (!is_file($data['filename']) or ($recrop !== False and $this->force_recrop)) {
						if ($recrop !== False) {
//							print "Je passe ici : get_recrop_data<br/>\n";
//							print $file."<br/>\n";
//							print $data['filename']."<br/>\n";
//							print_r($recrop);
//							print "<br/>";
							try {
								$imagick = new \Imagick(realpath($file));
								$this->autorotate($imagick);
							} catch (Exception $e) {
								$imagick = False;
								$base64[$name] = false;
								break;
							}
							if ($imagick !== False) {
								if ($black_and_white) $imagick->setImageColorSpace(Imagick::COLORSPACE_GRAY);
								$w = $this->picture_max_size['width'];
								$h = $this->picture_max_size['height'];
								if ($ratio === True) {
//									print "==> RATIO = True<br/>";
									$imagick->resizeImage($w, $h, imagick::FILTER_LANCZOS, 1, True);
								} else {
//									print "==> NO COVER<br/>";
									list($rw, $rh) = explode(':', $ratio);
									if ($w > $h) {
										$h = $w * $rh / $rw;
									} else {
										$w = $h * $rw / $rh;
									}
//									print "rw = $rw / rh = $rh / w = $w / h = $h<br/>";
									//( int $width , int $height , int $x , int $y ) 
									//Array ( [original] => Array ( [width] => 2560 [height] => 1605 ) [crop] => Array ( [x] => 516.5999999999997 [y] => 0 [width] => 2066.4 [height] => 1162.4397498262683 ) )
									$imagick->cropImage($recrop['crop']['width'], $recrop['crop']['height'], $recrop['crop']['x'], $recrop['crop']['y']);
									$imagick->cropThumbnailImage($w, $h);
								}
								$content = 'data:image/' . $type . ';base64,' . base64_encode($imagick->getimageblob());
								print "<img src=\"$content\"><br/>";
								$r = $this->store_file_set($data, $content);
/*
								if ($r) {
									print_r($data['filename']);
									print "<br/>";
								}
*/
							}
						} else {
							try {
								$imagick = new \Imagick(realpath($file));
								$this->autorotate($imagick);
							} catch (Exception $e) {
								$imagick = False;
								$base64[$name] = false;
								print "<div class='ccppm_data_notice'>ATTENTION, impossible de lire ".$url."</div>";
								break;
							}
							if ($imagick !== False) {
								if ($black_and_white) $imagick->setImageColorSpace(Imagick::COLORSPACE_GRAY);
								$w = $this->picture_max_size['width'];
								$h = $this->picture_max_size['height'];
								if ($ratio === True) {
									$imagick->resizeImage($w, $h, imagick::FILTER_LANCZOS, 1, True);
								} elseif($cover) {
									list($rw, $rh) = explode(':', $ratio);
									if ($w > $h) {
										$w = $h * $rw / $rh;
									} else {
										$h = $w * $rh / $rw;
									}
									$imagick->thumbnailImage($w, $h, True, True);
								} else {
									list($rw, $rh) = explode(':', $ratio);
									if ($w > $h) {
										$h = $w * $rh / $rw;
									} else {
										$w = $h * $rw / $rh;
									}
									$imagick->cropThumbnailImage($w, $h);
								}
								$content = 'data:image/' . $type . ';base64,' . base64_encode($imagick->getimageblob());
								$this->store_file_set($data, $content);
							}
						}
					}
					$base64[$name] = $data['file'];
				}
			}
			return $base64;
		}
	}

	public function get_recrop_data($post_id = False, $size_name) {
		if ($post_id == False)
			return False;
		global $wpdb;
		$sql = sprintf("SELECT meta_value FROM %spostmeta WHERE post_id='%s' AND meta_key = '__crop_coordinates_full_%s';", $wpdb->prefix, $post_id, $size_name);
		$data = $wpdb->get_col($wpdb->prepare($sql));
		if (count($data) == 0)
			return False;
		$crop = json_decode($data[0], True);
		$crop = $crop['crop'];

		$sql = sprintf("SELECT meta_value FROM %spostmeta WHERE post_id='%s' AND meta_key = '_wp_attachment_metadata';", $wpdb->prefix, $post_id);
		$data = $wpdb->get_col($wpdb->prepare($sql));
		if (count($data) == 0)
			return False;
		$meta = unserialize($data[0]);
		return ['original' => ['width'=>$meta['width'], 'height'=>$meta['height']], 'crop' => $crop];
	}

	public function get_post_data($data, $type) {
		$days = array('DIMANCHE', 'LUNDI', 'MARDI', 'MERCREDI', 'JEUDI', 'VENDREDI', 'SAMEDI');
		$id = $data->ID;
		$content = $data->post_content;
		$content = str_replace("\r", "", $content);
		$content = str_replace("\n", "<br/>", $content);
//		$content = strip_tags($content, '<br><i><strong><b><u><p><ul><li><em>');
		$content = strip_tags($content, $this->strip_tags_allowed);
		$content = $this->ccppm->catalogues->convert_html_img_inline($this->dpi, $content);
		$r_data = array(
			'id'=>$id,
			'post_title'=>$data->post_title,
			'post_content'=>$content,
			'_order'=>intval($data->menu_order),
			'_date'=>$data->post_date,
		);
		$metas = get_post_meta( $id );
		foreach($this->meta_keys[$type] as $key => $meta_info) {
			if (array_key_exists('no_meta', $meta_info) and $meta_info['no_meta'])
				continue;
			if (!array_key_exists('type', $meta_info))
				$meta_info['type'] = 'string';
			$vs = array();
			switch($meta_info['type']) {
				case 'contact':
					$vs_type = 'contact';
					$vs = $metas[$key];
					if (is_array($vs))
						foreach($vs as $k => $vs_id) {
							if ($vs_id) {
								if (array_key_exists($vs_id, $this->tmp_contacts)) {
									$vs[$k] = $this->tmp_contacts[$vs_id];
								} else {
									$vs_data = get_post($vs_id);
									if ($vs_data)
										if ($vs_data->ID)
											$vs[$k] = $this->get_post_data($vs_data, $vs_type);
										else
											unset($vs[$k]);
									$this->tmp_contacts[$vs_id] = $vs[$k];
								}
							} else
								unset($vs[$k]);
						}
					break;
				case 'date':
					$vs = $metas[$key];
					if (is_array($vs)) {
						$vs = array_filter( $vs, 'strlen' );
						foreach($vs as $vsk => $vsv)
							$vs[$vsk] = ['date'=>date('d/m/Y', $vsv), 'isoformat'=>date('Y-m-d', $vsv), 'day'=>$days[date('w', $vsv)].' '.date('d', $vsv)];
					} else {
						$vs = ['date'=>date('d/m/Y', $vs), 'isoformat'=>date('Y-m-d', $vs), 'day'=>$days[date('w', $vs)].' '.date('d', $vs)];
					}
					break;
				case 'datetime':
					$vs = $metas[$key];
					if (is_array($vs)) {
						$vs = array_filter( $vs, 'strlen' );
						foreach($vs as $vsk => $vsv)
							$vs[$vsk] = ['datetime'=>date('d/m/Y H:i', $vsv), 'decomp'=>['date'=>date('d/m/Y', $vsv), 'hour'=>date('H', $vsv), 'min'=>date('i', $vsv)], 'isoformat'=>date('Y-m-d H:i:s', $vsv), 'day'=>$days[date('w', $vsv)].' '.date('d H:i:s', $vsv)];
					} else {
						$vs = ['datetime'=>date('d/m/Y H:i', $vs), 'decomp'=>['date'=>date('d/m/Y', $vs), 'hour'=>date('H', $vs), 'min'=>date('i', $vs)], 'isoformat'=>date('Y-m-d H:i:s', $vs), 'day'=>$days[date('w', $vs)].' '.date('d H:i:s', $vs)];
					}
					break;
				case 'serialize':
					$vst = $metas[$key];
					if (is_array($vst)) {
						foreach($vst as $vsk => $vsv) {
							$vsv = unserialize($vsv);
							$vsvs = False;
							if (is_array($vsv)) {
								foreach($vsv as $vsvk => $vsvv)
									if ($vsvv != '')
										$vsvs = True;
							}
							if ($vsvs)
								$vs[] = $vsv;
						}
					}
					break;
				case 'picture':
					$t_vs = $metas[$key];
					$vs = array();
					if (is_array($t_vs))
						foreach($t_vs as $url) {
							if ($url) {
								$post_id = $this->get_attachment_id($url);
								$vs[] = ['url'=>$url, 'base64' => $this->convert_file_to_base64($url,
										(array_key_exists('sizes', $meta_info))?$meta_info['sizes']:False,
										(array_key_exists('cover', $meta_info))?$meta_info['cover']:False,
										(array_key_exists('black_and_white', $meta_info))?$meta_info['black_and_white']:False),
										$post_id
									];
							}
						}
					break;
				case 'post_picture':
					$t_vs = $metas[$key];
					$vs = array();
					if (is_array($t_vs))
						foreach($t_vs as $pp_post_id) {
							$url = wp_get_attachment_image_url($pp_post_id, 'full');
							if ($url)
								$vs[] = ['url'=>$url, 'base64' => $this->convert_file_to_base64($url,
										(array_key_exists('sizes', $meta_info))?$meta_info['sizes']:False,
										(array_key_exists('cover', $meta_info))?$meta_info['cover']:False,
										(array_key_exists('black_and_white', $meta_info))?$meta_info['black_and_white']:False,
										$pp_post_id)

									];
						}
					break;
				case 'country':
//					$lang = $this->lang;
					$lang = 'fr_FR';
					$file = dirname(__FILE__)."/../../types-custom-fields/fields/".$lang."/country.php";
					$t_data = include($file);
					$vs = $metas[$key];
					$vsv = false;
					if (is_array($vs))
						foreach($vs as $vsk => $vsv) {
							if (array_key_exists($vsv, $t_data))
								$vs[$vsk] = $t_data[$vsv];
						}
					break;
				case 'language':
					$file = dirname(__FILE__)."/../../types-custom-fields/fields/".$this->lang."/language.php";
					$t_data = include($file);
					$vs = $metas[$key];
					$vsv = [];
					if (is_array($vs)) {
						foreach($vs as $vsk => $vsv) {
							if (array_key_exists($vsv, $t_data))
								$vs[$vsk] = $t_data[$vsv];
						}
					}
					break;
				case 'select':
				case 'select_fr':
					$vs = $this->get_field_value($meta_info['type'], $key, $metas[$key]);
					break;
				case 'checkbox':
					$vs[] = $this->get_field_value($meta_info['type'], $key, $metas[$key]);
					break;
				case 'boolean':
					if (strtolower($metas[$key][0]) == 'yes')
						$vs[] = True;
					else
						$vs[] = $metas[$key][0]?True:False;
					break;
				case 'integer':
					$vs = $metas[$key];
					if (is_array($vs)) {
						$vs = array_filter( $vs, 'strlen' );
						foreach($vs as $vsk => $vsv)
							$vs[$vsk] = intval($vsv);
					}
					break;
				case 'decimal':
					$vs = $metas[$key];
					if (is_array($vs)) {
						$vs = array_filter( $vs, 'strlen' );
						foreach($vs as $vsk => $vsv)
							$vs[$vsk] = floatval($vsv);
					}
					break;
				case 'post_id':
					$vs = $metas[$key];
/*
					print "==> post_id, $id : ";
					print_r($vs);
					print "<br/>";
*/
					$t_vs = [];
					if (is_array($vs)) {
						$vs = array_filter( $vs, 'strlen' );
						foreach($vs as $vsk => $vsv)
							if ($vsv)
								$t_vs[$vsk] = intval($vsv);
					}
					$vs = $t_vs;
					unset($t_vs);
					break;
				case 'url':
				case 'user':
				case 'string':
				default:
					$vs = $metas[$key];
					if (is_array($vs)) {
						$vs = array_filter( $vs, 'strlen' );
						foreach($vs as $vsk => $vsv)
							$vs[$vsk] = $this->convert_special_char($vsv);
					}

					break;
			}
//			print ($meta_info['name']." (".$meta_info['type'].") : ");
//			var_dump($vs);
//			print " --- ";
			if (is_array($vs) && count($vs)) {
				if (array_key_exists('uniq', $meta_info) and $meta_info['uniq'])
					$vs = $vs[0];

			} else {
				$vs = '';
			}
//			var_dump($vs);
//			print "<br/>";
			$r_data[$meta_info['name']] = $vs;
		}
		if (array_key_exists($type, $this->terms)) {
			foreach($this->terms[$type] as $key => $term_info) {
				$terms = wp_get_post_terms( $id, $key);
				foreach($terms as $term)
					$r_data[$key][] = get_object_vars($term);
			}
		}
		array_walk_recursive($r_data, function(&$v, $k) {
			if ($v !== false && $v !== true) //  En #43, passage de tous les 0:1 en BDD à false:true dans les datas
				$v = trim($v);
		});
		return $r_data;
	}

	public function get_field_value($type, $key, $value) {
#		error_log ("$type  // $key\n");

		$field = $this->__fields[$key];
		switch ($type) {
			case 'checkbox':
				$return = array();
				$options = $field['data']['options'];
				if (is_array($value)) foreach($value as $v_value) {
					$v_value = unserialize($v_value);
					foreach($v_value as $key => $k_values)
						foreach($k_values as $k_value)
							if (array_key_exists($key, $options)) {
								$return[] = stripslashes($options[$key]['title']);
							}
				}
				return $return;
				break;
			case 'select':
			case 'select_fr':
				$data = array();
				if (array_key_exists('options', $field['data']))
					foreach($field['data']['options'] as $f_values) {
#						error_log(print_r($f_values, True));
						if (is_array($f_values))
							$data[$f_values['value']] = $f_values['title'];
					}
				if (is_array($value)) {
					foreach($value as $k => $v) {
						$icl_string = $this->get_icl_translate($field, $v, $type);
						if ($icl_string !== False)
							$value[$k] = $icl_string;
						elseif(array_key_exists($v, $data))
							$value[$k] = $data[$v];
					}
				}
				return $value;
				break;
		}
	}

	public function export_function_contacts_object($value) {
		$c = [];
		if ($value) {
			if (array_key_exists('name', $value)) {
				$c[] = sprintf('%s %s%s', $value['firstname'], $value['name'], ($value['organization'])?' ('.$value['organization'].')':'');
			} else {
				foreach($value as $contact) {
					$c[] = sprintf('%s %s%s', $contact['firstname'], $contact['name'], ($contact['organization'])?' ('.$contact['organization'].')':'');
				}
			}
		}
		return implode(', ', $c);
	}
	public function export_function_contacts($value) {
		$c = [];
		if ($value) {
			if (is_array($value))
				foreach($value as $contact) {
					$c[] = sprintf('%s %s', $contact['firstname'], $contact['lastname']);
				}
			else
				$c[] = $value;
		}
		return implode(', ', $c);
	}
	public function export_function_awards_r($value) {
		$c = [];
		if ($value) {
			if (is_array($value)) {
				foreach($value as $row)
					$c[] = sprintf('%s - %s (%s)', $row['festival'], $row['film'], $row['year']);
			} else {
				$c[] = $value;
			}
		}
		return implode(', ', $c);
	}
	public function export_function_career_r($value) {
		$c = [];
		if ($value) {
			if (is_array($value)) {
				foreach($value as $row)
					$c[] = sprintf('%s (%s)', $row['festival'], $row['year']);
			} else {
				$c[] = $value;
			}
		}
		return implode(', ', $c);
	}
	public function export_function_filmography_r($value) {
		$c = [];
		if ($value) {
			if (is_array($value)) {
				foreach($value as $row)
					$c[] = sprintf('%s (%s  - %s)', $row['film'], $row['year'], $row['type']);
			} else {
				$c[] = $value;
			}
		}
		return implode(', ', $c);
	}
	public function export_function_available_formats($value) {
		$c = [];
		if ($value) {
			if (is_array($value)) {
				foreach($value as $row)
					$c[] = sprintf('%s - %s  - %s - %s - %s - %s', $row['format'], ($row['kdm'])?'kdm':'#', $row['format_information'], $row['vo'], $row['vostfr'], $row['sound_format']);
			} else {
				$c[] = $value;
			}
		}
		return implode(', ', $c);
	}
	public function export_function_null($value) {
		return '';
	}
	public function export_function_film($value) {
		$c = [];
		if ($value) {
			foreach($value as $film_id) {
					$e = $this->ccppm->data->jsondb->get('film', $film_id);
					$c[] = $e['post_title'];
			}
		}
		return implode(', ', $c);
	}
	public function export_function_date($value) {
		$c = [];
		if ($value) {
			$value = $value['day'];
		}
		return $value;
	}
	public function export_function_datetime($value) {
		$c = [];
		if ($value) {
			$value = $value['day'];
		}
		return $value;
	}
	public function export_function_time_begin_end($value) {
		$c = [];
		if ($value) {
			$value = sprintf('%s => %s', $value['begin'], $value['end']);
		}
		return $value;
	}
	public function export_function_activities($value) {
		$r = unserialize($value);
		$c = [];
		foreach($r as $k => $v)
			$c[] = "$k : $v";
		return implode(', ', $c);
	}

	public function export_function_term($value) {
		$c = [];
		foreach($value as $r) {
			$c[] = $r['name'];
		}
		return implode(', ', $c);
	}

	public function export_function_accompanied_list($value) {
		return implode(', ', $value);
	}

	public function get_data_as_array($type) {
		$data = [];
		$errors = [];

		$fields = [
			'film' => [
				'author' => 'export_function_contacts',
				'photography' => 'export_function_contacts',
				'screenplay' => 'export_function_contacts',
				'costumes' => 'export_function_contacts',
				'sound' => 'export_function_contacts',
				'animation' => 'export_function_contacts',
				'set_artist' => 'export_function_contacts',
				'voice_over' => 'export_function_contacts',
				'visual_effects' => 'export_function_contacts',
				'music' => 'export_function_contacts',
				'screenplay' => 'export_function_contacts',
				'other' => 'export_function_contacts',
				'co_producer' => 'export_function_contacts', //31
				'starring' => 'export_function_contacts', //31
				'editing' => 'export_function_contacts',
				'awards_r' => 'export_function_awards_r', //29
				'career_r' => 'export_function_career_r', //29
				'filmography_r' => 'export_function_filmography_r', //33
				'available_formats' => 'export_function_available_formats', //42
				'animation_type' => 'export_function_null',
			],
			'projection' => [
				'film' => 'export_function_film',
				'date' => 'export_function_date',
				'start_and_stop_time' => 'export_function_time_begin_end',
			],
			'accreditation' => [
				'date_of_arrival' => 'export_function_datetime',
				'date_of_departure' => 'export_function_datetime',
				'activities' => 'export_function_activities',
				'accompanied_list' => 'export_function_accompanied_list'
			],
		];
		if (array_key_exists($type, $this->meta_keys)) {
			$headers=[];
			$data = [['id', 'post_title', 'post_content', '_order'], ['system', 'system', 'system', 'system']];
			foreach($data[0] as $key)
				$headers[] = ['name'=>$key, 'function'=>False];
			if (array_key_exists($type, $this->terms)) {
				$structure = $this->terms[$type];
				foreach($structure as $key => $meta) {
					$function = 'export_function_term';
					$data[0][] = $key;
					$data[1][] = 'term';
					$headers[] = ['name'=>$key, 'function' => $function];
				}				
			}
			$structure = $this->meta_keys[$type];
			foreach($structure as $key => $meta) {
				$name = $meta['name'];
				$function = False;
				if (array_key_exists($type, $fields) && array_key_exists($name, $fields[$type]))
					$function = $fields[$type][$name];
				if (!array_key_exists('type', $meta)) {
					$data[0][] = $name;
					$data[1][] = 'string';
					$headers[] = ['name'=>$name, 'field'=>$key, 'function' => $function];
				} else {
					$data[0][] = $name;
					$data[1][] = $meta['type'];
					$headers[] = ['name'=>$name, 'field'=>$key, 'function' => $function];
				}
			}
			$ids = $this->jsondb->get_ids($type);
			foreach($ids as $id) {
				$element = $this->jsondb->get($type, $id);
				$row = [];
				foreach($headers as $header) {
					$name = $header['name'];
					if (array_key_exists('field', $header)) {
						if (!array_key_exists('type', $structure[$header['field']]))
							$structure[$header['field']]['type'] = 'string';
						switch($structure[$header['field']]['type']) {
							case 'contact':
								$value = $this->export_function_contacts_object($element[$name]);
								break;
							case 'serialize':
								$vs = [];
								if (array_key_exists($name, $element) && is_array($element[$name])) foreach($element[$name] as $v) {
									$vs[] = implode(' ', array_values($v));
								}
								$value = implode(', ', $vs);
								break;
							case 'country':
							case 'checkbox':
							case 'url':
								if (is_array($element[$name]))
									$value = implode(', ', array_filter($element[$name]));
								else
									$value = $element[$name];
								break;
							case 'picture':
							case 'post_picture':
								$value = ($element[$name] && array_key_exists('url', $element[$name]))?$element[$name]['url']:"";
								break;
							default:
								$value = $element[$name];
								break;
						}
					} else {
						$value = $element[$name];
					}
					if ($header['function']) {
						$function = $header['function'];
						$value = $this->$function($value);
					}
					if (is_array($value)) {
						$errors[] =  "ATTENTION => $name / ".$structure[$header['field']]['type']." / ".var_export($value, True)."<br/>\n";
						$row[] = $value;
					} else {
						$row[] = $value;
					}
				}
				$data[] = $row;
			}
		}
		if ($errors) {
			print implode('', $errors);
			die();
		}

		return $data;
	}
}
