<?php

class ccpcm_data_custom extends ccpcm_object {
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
		'directory' => [
			'thematic' => 'thematic@term_id',
		],
		'farm' => [

		],
		'operation' => [

		],
		'structure' => [

		],
	);

	public $quick_names = array(
		'directory' => ['post_title'],
		'farm' => ['post_title'],
		'operation' => ['post_title'],
		'structure' => ['post_title'],
	);

	public $terms = array(
		'directory' => [
			'geography' => [],
			'production' => [],
			'thematic' => [],
		],
	);

	public $relations = array(
	);

	public $meta_keys_terms = array(
		'geography' => [
			'g_special_code' => [],
		],
		'production' => [

		],
		'thematic' => [
			't_general_image' => ['name'=>'image', 'type'=>'picture', 'uniq'=>True, 'sizes'=>[
				//'r036_1'=>'0.3620689655:1', 
				//'r16_9'=>'16:8.38', 
				'r1920_1080'=>'16:9', 
				//'r419_136'=>'419.53:136.32', 
				'r419_181'=>'419.53:181.76', 
				//'r419_90' => '419.53:90.88', 
				//'r380_550' => '380:550',
				'r10_15'=>'170.3979:247.64',
				'r4_3'=>'433.7032338099:311.8132338099', //#44
			]],
			't_general_color' => ['name'=>'color', 'type'=>'string', 'uniq'=>True],
			't_general_content' => ['name'=>'content', 'type'=>'string', 'uniq'=>True],
		],
	);

	public $meta_keys = [
		'directory' => [
			'_thumbnail_id' => ['name' => 'featured_image', 'type' => 'post_picture', 'uniq' => True, 'sizes'=>[
				'r16_9'=>'16:8.38', 
				'r1920_1080'=>'16:9', 
				//'r260_269'=>'260.945:269.14',
				'r4_3'=>'394.03:297.64',
				'r10_6'=>'197.015:115.88',
				'r155_148'=>'155:148.82', //#44
			]],
			'd_farm_address' => ['name' => 'farm_address', 'type' => 'string', 'uniq' => True ],
			'd_farm_in_transmission' => ['name' => 'farm_in_transmission', 'type' => 'string', 'uniq' => True ],
			'd_farm_to_transmit' => ['name' => 'farm_to_transmit', 'type' => 'string', 'uniq' => True ],
			'd_general_introduction' => ['name' => 'general_introduction', 'type' => 'string', 'uniq' => True ],
			'd_general_subtitle' => ['name' => 'general_subtitle', 'type' => 'string', 'uniq' => True ],
			'd_identity_area' => ['name' => 'identity_area', 'type' => 'string', 'uniq' => True ],
			'd_identity_commercialization' => ['name' => 'identity_commercialization', 'type' => 'string', 'uniq' => True ],
			'd_identity_commercializations' => ['name' => 'identity_commercializations', 'type' => 'serialize', 'uniq' => True ],
			'd_identity_diagrams' => ['name' => 'identity_diagrams', 'type' => 'serialize', 'uniq' => True ],
			'd_identity_label' => ['name' => 'identity_label', 'type' => 'string', 'uniq' => True ],
			'd_identity_livestock' => ['name' => 'identity_livestock', 'type' => 'string', 'uniq' => True ],
			'd_identity_location' => ['name' => 'identity_location', 'type' => 'string', 'uniq' => True ],
			'd_identity_number_of_people' => ['name' => 'identity_number_of_people', 'type' => 'string', 'uniq' => True ],
			'd_knowledge_acquisitions' => ['name' => 'knowledge_acquisitions', 'type' => 'serialize', 'uniq' => True ],
			'd_knowledge_diagrams' => ['name' => 'knowledge_diagrams', 'type' => 'serialize', 'uniq' => True ],
			'd_knowledge_installation_period' => ['name' => 'knowledge_installation_period', 'type' => 'string', 'uniq' => True ],
			'd_knowledge_knowledge_ap' => ['name' => 'knowledge_knowledge_ap', 'type' => 'string', 'uniq' => True ],
			'd_knowledge_testimony' => ['name' => 'knowledge_testimony', 'type' => 'string', 'uniq' => True ],
			'd_knowledge_viabilitys' => ['name' => 'knowledge_viabilitys', 'type' => 'string', 'uniq' => False ],
			'd_knowledge_vivabilitys' => ['name' => 'knowledge_vivabilitys', 'type' => 'string', 'uniq' => False ],
			'd_medias_gallery' => ['name' => 'medias_gallery', 'type' => 'string', 'uniq' => True ],
			'd_medias_video' => ['name' => 'medias_video', 'type' => 'string', 'uniq' => True ],
			'd_medias_video_link' => ['name' => 'medias_video_link', 'type' => 'string', 'uniq' => True ],
			'd_relationships_farm' => ['name' => 'relationships_farm', 'type' => 'string', 'uniq' => True ],
			'd_relationships_operation' => ['name' => 'relationships_operation', 'type' => 'string', 'uniq' => True ],
			'd_relationships_structure' => ['name' => 'relationships_structure', 'type' => 'string', 'uniq' => True ],
			'd_stage_opentostage' => ['name' => 'stage_opentostage', 'type' => 'string', 'uniq' => True ],
			'd_stage_opentovisit' => ['name' => 'stage_opentovisit', 'type' => 'string', 'uniq' => True ],
			'footnotes' => ['name' => 'footnotes', 'type' => 'serialize', 'uniq' => True ],
			'hide_page_title' => ['name' => 'hide_page_title', 'type' => 'string', 'uniq' => True ],
		],
		'farm' => [
			'_thumbnail_id' => ['name' => 'featured_image', 'type' => 'post_picture', 'uniq' => True, 'sizes'=>[
				'r16_9'=>'16:8.38', 
				'r1920_1080'=>'16:9', 
				//'r260_269'=>'260.945:269.14',
				'r4_3'=>'394.03:297.64',
				'r10_6'=>'197.015:115.88',
				'r155_148'=>'155:148.82', //#44
			]],
			'f_farm_in_transmission' => ['name' => 'farm_in_transmission', 'type' => 'string', 'uniq' => True ],
			'f_farm_to_transmit' => ['name' => 'farm_to_transmit', 'type' => 'string', 'uniq' => True ],
			'f_general_address' => ['name' => 'general_address', 'type' => 'string', 'uniq' => True ],
			'f_general_biography' => ['name' => 'general_biography', 'type' => 'string', 'uniq' => True ],
			'f_general_emails' => ['name' => 'eneral_emails', 'type' => 'serialize', 'uniq' => True ],
			'f_general_farmers' => ['name' => 'general_farmers', 'type' => 'serialize', 'uniq' => True ],
			'f_general_gallery' => ['name' => 'general_gallery', 'type' => 'post_picture', 'sizes'=>[
				'r16_9'=>'16:8.38', 
				'r1920_1080'=>'16:9', 
				//'r260_269'=>'260.945:269.14',
				'r4_3'=>'394.03:297.64',
				'r10_6'=>'197.015:115.88',
				'r155_148'=>'155:148.82', //#44
			]],
			'f_general_legal_entity' => ['name' => 'general_legal_entity', 'type' => 'string', 'uniq' => True ],
			'f_general_links' => ['name' => 'general_links', 'type' => 'serialize', 'uniq' => True ],
			'f_general_phones' => ['name' => 'general_phones', 'type' => 'serialize', 'uniq' => True ],
			'f_geolocation_address' => ['name' => 'geolocation_address', 'type' => 'string', 'uniq' => True ],
			'f_geolocation_lat' => ['name' => 'geolocation_lat', 'type' => 'string', 'uniq' => True ],
			'f_geolocation_lng' => ['name' => 'geolocation_lng', 'type' => 'string', 'uniq' => True ],
			'f_geolocation_map' => ['name' => 'geolocation_map', 'type' => 'string', 'uniq' => True ],
			'f_installation_period' => ['name' => '', 'type' => 'string', 'uniq' => True ],
			'f_more_testimony' => ['name' => 'more_testimony', 'type' => 'string', 'uniq' => True ],
			'f_transmission_farm_in_transmission' => ['name' => 'transmission_farm_in_transmission', 'type' => 'string', 'uniq' => True ],			
		],
		'operation' => [
			'_thumbnail_id' => ['name' => 'featured_image', 'type' => 'post_picture', 'uniq' => True, 'sizes'=>[
				'r16_9'=>'16:8.38', 
				'r1920_1080'=>'16:9', 
				//'r260_269'=>'260.945:269.14',
				'r4_3'=>'394.03:297.64',
				'r10_6'=>'197.015:115.88',
				'r155_148'=>'155:148.82', //#44
			]],
			'o_general_description' => ['name' => 'general_description', 'type' => 'string', 'uniq' => True ],
			'o_general_emails' => ['name' => 'general_emails', 'type' => 'serialize', 'uniq' => True ],
			'o_general_links' => ['name' => 'general_links', 'type' => 'serialize', 'uniq' => True ],
			'o_general_logotype' => ['name' => 'general_logotype', 'type' => 'string', 'uniq' => True ],
			'o_general_phones' => ['name' => 'general_phones', 'type' => 'serialize', 'uniq' => True ],			
		],
		'structure' => [
			'_thumbnail_id' => ['name' => 'featured_image', 'type' => 'post_picture', 'uniq' => True, 'sizes'=>[
				'r16_9'=>'16:8.38', 
				'r1920_1080'=>'16:9', 
				//'r260_269'=>'260.945:269.14',
				'r4_3'=>'394.03:297.64',
				'r10_6'=>'197.015:115.88',
				'r155_148'=>'155:148.82', //#44
			]],
			's_general_address' => ['name' => 'general_address', 'type' => 'string', 'uniq' => True ],
			's_general_description' => ['name' => 'general_description', 'type' => 'string', 'uniq' => True ],
			's_general_email' => ['name' => 'general_email', 'type' => 'string', 'uniq' => True ],
			's_general_emails' => ['name' => 'general_emails', 'type' => 'serialize', 'uniq' => True ],
			's_general_links' => ['name' => 'general_links', 'type' => 'serialize', 'uniq' => True ],
			's_general_referent' => ['name' => 'general_referent', 'type' => 'string', 'uniq' => True ],
			's_internal_notes' => ['name' => 'internal_notes', 'type' => 'serialize', 'uniq' => True ],			
		],
	];

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

	public function update_directory() {
		$this->__fields = $this->get_fields();
		$args = [
			'post_type'=>'directory',
			'orderby'=>'post_title',
			'order'=>'ASC',
			'offset'=>0,
			'posts_per_page'=>-1,
    	];
		$query = new WP_Query($args);
		$query_data = get_posts($args);
		$c_film = 0;
		foreach($query_data as $data) {
			$data = $this->get_post_data($data, 'directory');
			$post_id = $data['id'];
			$data['permalink'] = get_permalink($post_id);
			$this->jsondb->append('directory', $post_id, $data);
			 $c_posts['directory'] ++;
		}
		return $c_posts;
	}

	public function update_farm() {
		$this->__fields = $this->get_fields();
		$args = [
			'post_type'=>'farm',
			'orderby'=>'post_title',
			'order'=>'ASC',
			'offset'=>0,
			'posts_per_page'=>-1,
    	];
		$query = new WP_Query($args);
		$query_data = get_posts($args);
		$c_film = 0;
		foreach($query_data as $data) {
			$data = $this->get_post_data($data, 'farm');
			$post_id = $data['id'];
			$data['permalink'] = get_permalink($post_id);
			$this->jsondb->append('farm', $post_id, $data);
			 $c_posts['farm'] ++;
		}
		return $c_posts;
	}

	public function update_operation() {
		$this->__fields = $this->get_fields();
		$args = [
			'post_type'=>'operation',
			'orderby'=>'post_title',
			'order'=>'ASC',
			'offset'=>0,
			'posts_per_page'=>-1,
    	];
		$query = new WP_Query($args);
		$query_data = get_posts($args);
		$c_film = 0;
		foreach($query_data as $data) {
			$data = $this->get_post_data($data, 'operation');
			$post_id = $data['id'];
			$data['permalink'] = get_permalink($post_id);
			$this->jsondb->append('operation', $post_id, $data);
			 $c_posts['operation'] ++;
		}
		return $c_posts;
	}

	public function update_structure() {
		$this->__fields = $this->get_fields();
		$args = [
			'post_type'=>'structure',
			'orderby'=>'post_title',
			'order'=>'ASC',
			'offset'=>0,
			'posts_per_page'=>-1,
    	];
		$query = new WP_Query($args);
		$query_data = get_posts($args);
		$c_film = 0;
		foreach($query_data as $data) {
			$data = $this->get_post_data($data, 'structure');
			$post_id = $data['id'];
			$data['permalink'] = get_permalink($post_id);
			$this->jsondb->append('structure', $post_id, $data);
			 $c_posts['structure'] ++;
		}
		return $c_posts;
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

	public function update() {
		$this->__fields = $this->get_fields();

//		$this->jsondb->remove_all();
		$c_posts = array();

		$this->ccpcm->log('[ DATA.UPDATE ] update_directory');
		$c_posts_tmp = $this->update_directory();
		$c_posts = array_merge($c_posts, $c_posts_tmp);

		$this->ccpcm->log('[ DATA.UPDATE ] update_farm');
		$c_posts_tmp = $this->update_farm();
		$c_posts = array_merge($c_posts, $c_posts_tmp);

		$this->ccpcm->log('[ DATA.UPDATE ] update_operation');
		$c_posts_tmp = $this->update_operation();
		$c_posts = array_merge($c_posts, $c_posts_tmp);

		$this->ccpcm->log('[ DATA.UPDATE ] update_structure');
		$c_posts_tmp = $this->update_structure();
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
			'directory' => [
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
#									print("$name => ");
#									print_r($v);
#									print ("<br/>");
									if (is_string($v))
										$v = unserialize($v);
									if (is_array($v))
										$vs[] = implode(' ', array_values($v));
									else
										$vs[] = $v;
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
								if ($structure[$header['field']]['uniq'] === False && is_array($element[$name]))
									$value = implode(', ', array_filter($element[$name]));
								else
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