<?php

if (file_exists(__DIR__.'/../custom/ccpcm-data-'.CCPCM_PROJECT.'.inc.php')) {
	require_once(__DIR__.'/../custom/ccpcm-data-'.CCPCM_PROJECT.'.inc.php');
} else {
	class ccpcm_data_custom extends ccpcm_object {

	}
}

class ccpcm_data extends ccpcm_data_custom {
	public $jsondb = False;
	private $db_path = False;
	public $storage_path = False;
	private $lang = 'en_US';
	private $lang_short = 'en';
	private $__icl_cache = array();
	public $pictures_enabled = True;
	public $force_recrop = False;

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

	public function __construct($ccpcm) {
		parent::__construct($ccpcm);
		$this->redefine();
	}

	public function redefine($dpi = false) {
		$edition_slug = $this->ccpcm->edition_slug;
		require_once('jsondb.inc.php');

		if ($dpi)
			$this->dpi = $dpi;

		$this->picture_max_size = $this->picture_max_sizes_available[$this->dpi];

		$this->db_path = sprintf("%s/%s/%s/", CCPCM_RELATIVE_JSONDB_PATH."/jsondb/", $edition_slug, $this->dpi);
		if (!is_dir(__DIR__ .'/'. $this->db_path))
			if (!mkdir(__DIR__ .'/'. $this->db_path, 0755, True))
				printf("Can't create %s<br/>", __DIR__ .'/'. $this->db_path);

		$this->storage_path = sprintf("%s/%s/%s-storage/", CCPCM_RELATIVE_JSONDB_PATH."/jsondb/", $edition_slug, $this->dpi);
		if (!is_dir(__DIR__ .'/'. $this->storage_path))
			if (!mkdir(__DIR__ .'/'. $this->storage_path, 0755, True))
				printf("Can't create %s<br/>", __DIR__ .'/'. $this->storage_path);

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

	private function get_option_values(&$data, $field) {
		foreach($field['data']['options'] as $f_index => $f_option) {
			if ($f_index != 'default')
				$data[$f_option['value']] = $f_option['title'];
		}
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
							$data[$t_infos['name']] = $this->ccpcm->catalogues->convert_html_img_inline($this->dpi, $data[$t_infos['name']]);
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
							if (intval($data[$t_infos['name']]) == $data[$t_infos['name']]) {
								$post_id = $data[$t_infos['name']];
								$url = wp_get_attachment_url( $post_id );
								$data[$t_infos['name']] = ['url'=>$url, 'base64' => $this->convert_file_to_base64($url, (array_key_exists('sizes', $t_infos))?$t_infos['sizes']:False, (array_key_exists('cover', $t_infos))?$t_infos['cover']:False, False, $post_id)];
							} else {
								$url = $data[$t_infos['name']];
								if ($url) {
										$post_id = $this->get_attachment_id($url);
										if ( ! $post_id && array_key_exists('orginalPicture', $t_infos)) {
											$post_id = $this->get_attachment_id($data[$t_infos['orginalPicture']]['url']);
										}
									$data[$t_infos['name']] = ['url'=>$url, 'base64' => $this->convert_file_to_base64($url, (array_key_exists('sizes', $t_infos))?$t_infos['sizes']:False, (array_key_exists('cover', $t_infos))?$t_infos['cover']:False, False, $post_id)];
								} else
									$data[$t_infos['name']] = False;
							}
						} else {
							$pictures = [];
							$metas = $data[$t_infos['name']];
							foreach($metas as $meta) {
								if ($meta) {
									if (intval($meta) == $meta) {
										$post_id = $meta;
										$url = wp_get_attachment_url( $post_id );
										$pictures[] = ['url'=>$url, 'base64' => $this->convert_file_to_base64($url, (array_key_exists('sizes', $t_infos))?$t_infos['sizes']:False, (array_key_exists('cover', $t_infos))?$t_infos['cover']:False, False, $post_id)];		
									} else {
										$url = $meta;
										$post_id = $this->get_attachment_id($url);
										$pictures[] = ['url'=>$url, 'base64' => $this->convert_file_to_base64($url, (array_key_exists('sizes', $t_infos))?$t_infos['sizes']:False, (array_key_exists('cover', $t_infos))?$t_infos['cover']:False, False, $post_id)];
									}
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
			return "[ ccpcm-data.store_file_get_final] ERROR !!!! file name is an ARRAY, should be a string\n";
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
		$path = sprintf("%s/%s/%s/", __DIR__.'/'.$this->storage_path, $m1, $m2);
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
#				$this->ccpcm->log("[ DATA.UPDstore_file_get_final ] $dpi $file ".json_encode($meta));
				$imagick->resizeImage($width, $height, imagick::FILTER_LANCZOS, 1, $bestfit);
				$data = 'data:image/' . $type . ';base64,' . base64_encode($imagick->getimageblob());
				file_put_contents($filename_dst, $data);
#				$this->ccpcm->log("[ DATA.UPDstore_file_get_final END ] $dpi $file ".json_encode($meta));
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
		$path = sprintf("%s/%s/%s/", __DIR__.'/'.$this->storage_path, $m1, $m2);
		$pathRelative = sprintf("%s/%s/%s/", $this->storage_path, $m1, $m2);
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
		$path = __DIR__.'/../../../..';
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
								print "<div class='ccpcm_data_notice'>ATTENTION, impossible de lire ".$url."</div>";
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
		if (! $this->is_gutenberg) {
			$content = str_replace("\r", "", $content);
			$content = str_replace("\n", "<br/>", $content);
		}
//		$content = strip_tags($content, '<br><i><strong><b><u><p><ul><li><em>');
		$content = strip_tags($content, $this->strip_tags_allowed);
		$content = $this->ccpcm->catalogues->convert_html_img_inline($this->dpi, $content);
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
				foreach($terms as $term) {
					$data = get_object_vars($term);
					$data['_order'] = intval($data['term_order']);
					unset($data['term_order']);
					$this->get_terms_metas($key, $data);
					$r_data[$key][] = $data;
				}
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
}
