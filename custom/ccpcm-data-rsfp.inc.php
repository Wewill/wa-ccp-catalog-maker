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
	);

	public $terms = array(
	);

	public $relations = array(
	);

	public $meta_keys_terms = array(
		'geography' => [],
		'production' => [],
		'thematic' => [],
	);

	public $meta_keys = [
		'directory' => [
			'_coblocks_accordion_ie_support' => ['name' => '', 'type' => '', 'uniq' => True ],
			'_coblocks_attr' => ['name' => '', 'type' => '', 'uniq' => True ],
			'_coblocks_dimensions' => ['name' => '', 'type' => '', 'uniq' => True ],
			'_coblocks_responsive_height' => ['name' => '', 'type' => '', 'uniq' => True ],
			'_edit_last' => ['name' => '', 'type' => '', 'uniq' => True ],
			'_edit_lock' => ['name' => '', 'type' => '', 'uniq' => True ],
			'_product_image_gallery' => ['name' => '', 'type' => '', 'uniq' => True ],
			'_thumbnail_id' => ['name' => 'featured_image', 'type' => 'post_picture', 'uniq' => True, 'sizes'=>[
				'r16_9'=>'16:8.38', 
				'r1920_1080'=>'16:9', 
				//'r260_269'=>'260.945:269.14',
				'r4_3'=>'394.03:297.64',
				'r10_6'=>'197.015:115.88',
				'r155_148'=>'155:148.82', //#44
			]],
			'_wp_old_date' => ['name' => '', 'type' => '', 'uniq' => True ],
			'd_farm_address' => ['name' => '', 'type' => '', 'uniq' => True ],
			'd_farm_in_transmission' => ['name' => '', 'type' => '', 'uniq' => True ],
			'd_farm_to_transmit' => ['name' => '', 'type' => '', 'uniq' => True ],
			'd_general_introduction' => ['name' => '', 'type' => '', 'uniq' => True ],
			'd_general_subtitle' => ['name' => '', 'type' => '', 'uniq' => True ],
			'd_identity_area' => ['name' => '', 'type' => '', 'uniq' => True ],
			'd_identity_commercialization' => ['name' => '', 'type' => '', 'uniq' => True ],
			'd_identity_commercializations' => ['name' => '', 'type' => '', 'uniq' => True ],
			'd_identity_diagrams' => ['name' => '', 'type' => '', 'uniq' => True ],
			'd_identity_label' => ['name' => '', 'type' => '', 'uniq' => True ],
			'd_identity_livestock' => ['name' => '', 'type' => '', 'uniq' => True ],
			'd_identity_location' => ['name' => '', 'type' => '', 'uniq' => True ],
			'd_identity_number_of_people' => ['name' => '', 'type' => '', 'uniq' => True ],
			'd_knowledge_acquisitions' => ['name' => '', 'type' => '', 'uniq' => True ],
			'd_knowledge_diagrams' => ['name' => '', 'type' => '', 'uniq' => True ],
			'd_knowledge_installation_period' => ['name' => '', 'type' => '', 'uniq' => True ],
			'd_knowledge_knowledge_ap' => ['name' => '', 'type' => '', 'uniq' => True ],
			'd_knowledge_testimony' => ['name' => '', 'type' => '', 'uniq' => True ],
			'd_knowledge_viabilitys' => ['name' => '', 'type' => '', 'uniq' => True ],
			'd_knowledge_vivabilitys' => ['name' => '', 'type' => '', 'uniq' => True ],
			'd_medias_gallery' => ['name' => '', 'type' => '', 'uniq' => True ],
			'd_medias_video' => ['name' => '', 'type' => '', 'uniq' => True ],
			'd_medias_video_link' => ['name' => '', 'type' => '', 'uniq' => True ],
			'd_relationships_farm' => ['name' => '', 'type' => '', 'uniq' => True ],
			'd_relationships_operation' => ['name' => '', 'type' => '', 'uniq' => True ],
			'd_relationships_structure' => ['name' => '', 'type' => '', 'uniq' => True ],
			'd_stage_opentostage' => ['name' => '', 'type' => '', 'uniq' => True ],
			'd_stage_opentovisit' => ['name' => '', 'type' => '', 'uniq' => True ],
			'footnotes' => ['name' => '', 'type' => '', 'uniq' => True ],
			'hide_page_title' => ['name' => '', 'type' => '', 'uniq' => True ],
			'page_dark_toggle' => ['name' => '', 'type' => '', 'uniq' => True ],
			'page_wide_toggle' => ['name' => '', 'type' => '', 'uniq' => True ],
			'paw_status' => ['name' => '', 'type' => '', 'uniq' => True ],
		],
		'farm' => [

		],
		'operation' => [

		],
		'structure' => [

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
		$edition_id = $this->ccpcm->edition_id;
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

	public function update() {
		$edition_id = $this->ccpcm->edition_id;
		if (!$edition_id) {
			print '<div class="ccpcm_data_notice">Please select "edition" in tool bar</div>';
			return False;
		}

		$this->__fields = $this->get_fields();

//		$this->jsondb->remove_all();
		$c_posts = array();

		$this->ccpcm->log('[ DATA.UPDATE ] update_directory');
		$c_posts_tmp = $this->update_directory();
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