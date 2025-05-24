<?php

class ccpcm_data_custom extends ccpppm_object {
    public $picture_max_sizes_available = [
        "9"=>['width'=>52.5, 'height'=>74.5],
        "18"=>['width'=>105, 'height'=>149],
        "36"=>['width'=>210, 'height'=>298],
        "72"=>['width'=>420, 'height'=>596],
        "150"=>['width'=>877, 'height'=>1241],
        "300"=>['width'=>1753, 'height'=>2481],
    ];

    public $strip_tags_allowed = '<br><i><strong><b><img><u><ul><li><em><h1><h2><h3><h4><h5><h6><p><span><aside>'; // ajout wil <h4> + suppression <p> pour <p style="text-align: right;"> align right //

    public $dpi = 9;

    public $picture_max_size = ['width'=>52.5, 'height'=>74.5]; // 9 dpi

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
									$errors[] = sprintf("<font class=\"ccpcm_required\">Required Missing data for %s</font>", $meta_info['name']);
								break;
							case 'important':
								if (!$data[$meta_info['name']])
									$errors[] = sprintf("<font class=\"ccpcm_important\">Important Missing data for %s</font>", $meta_info['name']);
								break;
						}
					}
					if ($type == 'film' and $meta_info['name'] == 'available_formats') {
						if ($data[$meta_info['name']]) {
							$format = $data[$meta_info['name']][0];
							$checks = ['vo', 'format', 'sound_format'];
							foreach($checks as $check)
								if ($format[$check] == 'N/A')
									$errors[] = sprintf("<font class=\"ccpcm_required\">Required Missing FORMAT for %s</font>", $check);
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
		$edition_id = $this->ccpcm->edition_id;
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
		$edition_id = $this->ccpcm->edition_id;
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
		$edition_id = $this->ccpcm->edition_id;
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
		$edition_id = $this->ccpcm->edition_id;
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

	public function update_projection() {
		$this->__fields = $this->get_fields();
		$edition_id = $this->ccpcm->edition_id;
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

	public function update_planning($remove_first_date = False, $errors = True) {
		$this->__fields = $this->get_fields();
		$edition_id = $this->ccpcm->edition_id;
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
					$a_projections[$room_id][$date['isoformat']][$timing] = $this->ccpcm->catalogues->get_catalogue_data_order_by($a_projections[$room_id][$date['isoformat']][$timing], 'projection');
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
		
		$edition_id = $this->ccpcm->edition_id;
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
					print "<div class='ccpcm_data_warning'>Warning: pas de projection pour film $film_id<br/></div>";
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
		$edition_id = $this->ccpcm->edition_id;
		if (!$edition_id) {
			print '<div class="ccpcm_data_notice">Please select "edition" in tool bar</div>';
			return False;
		}

		$this->__fields = $this->get_fields();

//		$this->jsondb->remove_all();
		$c_posts = array();

		$this->ccpcm->log('[ DATA.UPDATE ] update_film_and_partenaire');
		$c_posts_tmp = $this->update_film_and_partenaire();
		$c_posts = array_merge($c_posts, $c_posts_tmp);

		$this->ccpcm->log('[ DATA.UPDATE ] update_jury');
		$c_posts_tmp = $this->update_jury();
		$c_posts = array_merge($c_posts, $c_posts_tmp);

		$this->ccpcm->log('[ DATA.UPDATE ] update_partenaire');
		$c_posts_tmp = $this->update_partenaire();
		$c_posts = array_merge($c_posts, $c_posts_tmp);

		$this->ccpcm->log('[ DATA.UPDATE ] update_projection');
		$c_posts_tmp = $this->update_projection();
		$c_posts = array_merge($c_posts, $c_posts_tmp);

		$this->ccpcm->log('[ DATA.UPDATE ] update_accreditation');
		$c_posts_tmp = $this->update_accreditation();
		$c_posts = array_merge($c_posts, $c_posts_tmp);

		$this->ccpcm->log('[ DATA.UPDATE ] update_terms');
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
		

		$html = "<div class='ccpcm_data_info'><ul>\n";
		foreach($c_posts as $k => $v)
			$html .= "<li>$k : $v</li>\n";
		foreach($c_terms as $k => $v)
			$html .=  "<li>$k : $v</li>\n";
		$html .=  "</ul></div>\n";
		$this->ccpcm->data->update_planning(False, False);
		$html .= "<div class='ccpcm_data_success'>Planning succesfully updated !</div>";
		$this->ccpcm->data->update_sections_planning();
		$html .= "<div class='ccpcm_data_success'>Section w/ planning succesfully updated !</div>";
		print "$html\n";
		$this->ccpcm->log(strip_tags($html));
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
					$e = $this->ccpcm->data->jsondb->get('film', $film_id);
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