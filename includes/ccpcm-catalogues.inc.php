<?php
if (file_exists(__DIR__.'/../custom/ccpcm-catalogues-'.CCPCM_PROJECT.'.inc.php')) {
	require_once(__DIR__.'/../custom/ccpcm-catalogues-'.CCPCM_PROJECT.'.inc.php');
} else {
	class ccpcm_catalogues_custom extends ccpcm_object {
		public $types = array(
			'section'=>'Section',
			'section_and_subsections'=>'Section and subsections',
			'2sections'=>'1/2 page par section x 2',
			'contact'=>'Contact',
			'html'=>'Html',
			'htmlx2'=>'2 Html',
			'media'=>'Media',
			'toc'=>'Toc',
			'index'=>'Index',
			'page_break'=>'Page break',
		);
	}
}

class ccpcm_catalogues extends ccpcm_catalogues_custom {

	public function display() {
		print ('<script>var edition_slug = "'.$this->ccpcm->edition_slug.'"; var edition_id = "'.$this->ccpcm->edition_id.'"; var catalogue_id = false;</script>');
		wp_enqueue_style('ccpcm_catalogue', plugin_dir_url(__FILE__).'../css/ccpcm_catalogues.css');
		wp_enqueue_script('pdfmake', plugin_dir_url(__FILE__).'../bower_components/pdfmake/build/pdfmake.js');
		wp_enqueue_script('trumbowyg', plugin_dir_url(__FILE__).'../bower_components/trumbowyg/dist/trumbowyg.js');
		wp_enqueue_script('trumbowyg_allowtagsfrompaste', plugin_dir_url(__FILE__).'../bower_components/trumbowyg/dist/plugins/allowtagsfrompaste/trumbowyg.allowtagsfrompaste.min.js', ['trumbowyg']);
		wp_enqueue_style('trumbowyg', plugin_dir_url(__FILE__).'../bower_components/trumbowyg/dist/ui/trumbowyg.min.css');

		wp_enqueue_style('ccpcm_fonts', plugin_dir_url(__FILE__).'../fonts/fonts.css');

//    wp_enqueue_style('codemirror', plugin_dir_url(__FILE__).'../bower_components/codemirror/lib/codemirror.css');
//    wp_enqueue_script('codemirror', plugin_dir_url(__FILE__).'../bower_components/codemirror/lib/codemirror.js');
//    wp_enqueue_script('codemirror_js', plugin_dir_url(__FILE__).'../bower_components/codemirror/mode/javascript/javascript.js');

		wp_enqueue_script('pdfmake_fonts', plugin_dir_url(__FILE__).'../js/pdfmake_fonts.js', ['pdfmake']);
		wp_enqueue_script('pdfmake_fonts_files', plugin_dir_url(__FILE__).'../js/vfs_fonts.js', ['pdfmake_fonts']);

		wp_enqueue_script('htmltopdfmake', plugin_dir_url(__FILE__).'../node_modules/html-to-pdfmake/browser.js', ['pdfmake']);
		wp_enqueue_script('canvas2svg', plugin_dir_url(__FILE__).'../js/canvas2svg.js');

		wp_enqueue_script('jquery-ui-droppable');

		wp_enqueue_script('ccpcm_templates_functions', plugin_dir_url(__FILE__).'../js/ccpcm_templates_functions.js');
		wp_enqueue_script('ccpcm_catalogues', plugin_dir_url(__FILE__).'../js/ccpcm_catalogues.js', ['ccpcm_templates_functions']);
    	wp_enqueue_script('ccpcm_catalogues_functions_tmp', plugin_dir_url(__FILE__).'../js/ccpcm_catalogues_functions_tmp.js', ['ccpcm_catalogues']);

		print('<div id="ccpcm_catalogue_container">');
		$this->display_top();
		$this->display_left();
		$this->display_right();
		$this->display_popup();
		print('</div>');
	}

	public function display_top() {
		print('<div id="ccpcm_catalogues_top">');
		print('<div id="ccpcm_catalogues_top_left">Catalogue : <select id="ccpcm_catalogues_select"></select>');
			print('&nbsp;Master : <select id="ccpcm_catalogue_masters_select"></select>');
		print('&nbsp;<a id="ccpcm_catalogue_btn_save" class="button button-primary">Save</a>');
		print('&nbsp;<a id="ccpcm_catalogue_btn_delete" class="button button-secondary">Delete</a>');
			print('</div>');
			print('<div id="ccpcm_catalogues_top_right">');
			print('<a id="ccpcm_catalogue_btn_new" class="button button-primary">New Catalogue</a>&nbsp;<span id="ccpcm_catalogue_new"><input id="ccpcm_catalogue_new_name" value="noname">&nbsp;<a id="ccpcm_catalogue_btn_new_append" class="button button-primary">Create</a>&nbsp;<a id="ccpcm_catalogue_btn_new_cancel" class="button button-secondary">Cancel</a></span>');
			$this->ccpcm->display->dpi_selector();
			print('&nbsp;<a id="ccpcm_catalogue_btn_render" class="button button-primary">Render</a>');
			print('&nbsp;<a id="ccpcm_catalogue_btn_download" class="button button-secondary">Download</a>');
			print('</div>');
		print('</div>');
	}

	public function display_left(){
		print('<div id="ccpcm_catalogue_container_left">');
		print('<ul id="ccpcm_catalogue_items">');
		foreach($this->types as $type => $desc) {
			printf('<li class="ccpcm_catalogue_items_item noselect ccpcm_catalogue_items_item_%s" rel="%s">%s</li>', $type, $type, $desc);
		}
		print('</ul>');
		print('<div id="ccpcm_catalogue_config">');
		print('<ul id="ccpcm_catalogue_config_sortable">');
		print('</ul>');
		print('</div>');

		print('</div>');
	}

	public function display_popup() {
		print('<div id="ccpcm_catalogue_popup_background">');
		print('</div>');
		print('<div id="ccpcm_catalogue_popup">');

		print('<div class="ccpcm_catalogue_popup_left">Template name : </div>');
		print('<div class="ccpcm_catalogue_popup_right"><select id="ccpcm_catalogue_popup_template_select"></select></div>');

		print('<div id="ccpcm_catalogue_popup_html" class="ccpcm_catalogue_popup_type">');
		print('<div class="ccpcm_catalogue_popup_col_left">');
		print('<div class="ccpcm_catalogue_popup_comment"><span class="title">Conseil ! Pensez à cleaner votre texte avant d\'exporter ou de tester la brochure.</span> Il est nécessaire de supprimer les balises inutiles / polluantes :
		Simplement en utilisant par exemple le site : <a href="https://html-cleaner.com" target="_blank">https://html-cleaner.com</a><br/>Copier / coller en totalité le texte en mode "code " (<>) et le nettoyer, puis recoller le texte nettoyé toujours en mode "code" (<>).</div>');
		print('<div class="ccpcm_catalogue_popup_left">Title : </div>');
		print('<div class="ccpcm_catalogue_popup_right"><input id="ccpcm_catalogue_popup_html_title" class="ccpcm_catalogue_popup_input"></div>');
		print('<div class="ccpcm_catalogue_popup_left">Color : </div>');
		print('<div class="ccpcm_catalogue_popup_right"><input id="ccpcm_catalogue_popup_html_color" class="ccpcm_catalogue_popup_input"></div>');
		print('<div id="ccpcm_catalogue_popup_html_rte"></div>');
		print('<p><a id="ccpcm_catalogue_popup_html_save" class="button button-primary">Save</a>&nbsp;<a id="ccpcm_catalogue_popup_html_btn_get_ratio" class="button button-primary">Get image Ratio</a>&nbsp;<a id="ccpcm_catalogue_popup_html_btn_render" class="button button-primary">Render</a></p>');
		print('</div>');
		print('<div class="ccpcm_catalogue_popup_col_right">');
		print('<iframe id="ccpcm_catalogue_popup_html_render"></iframe>');
		print('</div>');
		print('</div>');

		print('<div id="ccpcm_catalogue_popup_htmlx2" class="ccpcm_catalogue_popup_type">');
		print('<div class="ccpcm_catalogue_popup_col_left">');
		print('<div class="ccpcm_catalogue_popup_comment"><span class="title">Conseil ! Pensez à cleaner votre texte avant d\'exporter ou de tester la brochure.</span> Il est nécessaire de supprimer les balises inutiles / polluantes :
		Simplement en utilisant par exemple le site : <a href="https://html-cleaner.com" target="_blank">https://html-cleaner.com</a><br/>Copier / coller en totalité le texte en mode "code " (<>) et le nettoyer, puis recoller le texte nettoyé toujours en mode "code" (<>).</div>');
		print('<div class="ccpcm_catalogue_popup_left">Title : </div>');
		print('<div class="ccpcm_catalogue_popup_right"><input id="ccpcm_catalogue_popup_htmlx2_title" class="ccpcm_catalogue_popup_input"></div>');
		print('<div class="ccpcm_catalogue_popup_left">Color : </div>');
		print('<div class="ccpcm_catalogue_popup_right"><input id="ccpcm_catalogue_popup_htmlx2_color" class="ccpcm_catalogue_popup_input"></div>');
		print('<div id="ccpcm_catalogue_popup_htmlx2_1_rte"></div>');
		print('<div id="ccpcm_catalogue_popup_htmlx2_2_rte"></div>');
		print('<p><a id="ccpcm_catalogue_popup_htmlx2_save" class="button button-primary">Save</a>&nbsp;<a id="ccpcm_catalogue_popup_htmlx2_btn_get_ratio" class="button button-primary">Get image Ratio</a>&nbsp;<a id="ccpcm_catalogue_popup_htmlx2_btn_render" class="button button-primary">Render</a></p>');
		print('</div>');
		print('<div class="ccpcm_catalogue_popup_col_right">');
		print('<iframe id="ccpcm_catalogue_popup_htmlx2_render"></iframe>');
		print('</div>');
		print('</div>');

		print('<div id="ccpcm_catalogue_popup_media" class="ccpcm_catalogue_popup_type">');
		print('<div class="ccpcm_catalogue_popup_comment"><span class="title">Formats acceptés :</span> *.jpg, 300dpi, CMJN uniquement, bords perdus de 5mm donc 10mm de format utile (FU) en plus par rapport au format fini (FF), traits de coupes optionnels.</div>');
		print('<div class="ccpcm_catalogue_popup_left">Media associé : </div>');
		print('<div class="ccpcm_catalogue_popup_right"><input id="ccpcm_catalogue_popup_media_upload" type="button" class="button" value="Select media" /></div>');
		print('<div class=""><img id="ccpcm_catalogue_popup_media_preview"><div id="ccpcm_catalogue_popup_media_url"></div><input type="hidden" value="" id="ccpcm_catalogue_popup_media_id"></div>');
		print('<p><a id="ccpcm_catalogue_popup_media_save" class="button button-primary">Save</a></p>');
		print('</div>');

		print('<div id="ccpcm_catalogue_popup_data" class="ccpcm_catalogue_popup_type">');
		if (IS_PLANNING) print('<div class="ccpcm_catalogue_popup_comment"><span class="title">Planning :</span> attention, le template doit être en data/unique et non en data/multiple</div>');
		print('<div class="ccpcm_catalogue_popup_left">Designation name : </div>');
		print('<div class="ccpcm_catalogue_popup_right"><select id="ccpcm_catalogue_popup_data_select"></select></div>');
		print('<div class="ccpcm_catalogue_popup_left">Order : </div>');
		print('<div class="ccpcm_catalogue_popup_right"><select id="ccpcm_catalogue_popup_data_order">');
		print('<option value="name_simplified">By name simplified</option>');
		print('<option value="order">By order</option>');
		print('<option value="name">By name</option>');
		print('<option value="date">By date</option>');
		print('</select></div>');
		print('<div id="ccpcm_catalogue_popup_data2" class="ccpcm_catalogue_popup_type_inside">');
		print('<div class="ccpcm_catalogue_popup_left">Second designation name : </div>');
		print('<div class="ccpcm_catalogue_popup_right"><select id="ccpcm_catalogue_popup_data2_select"></select></div>');
		print('</div>');
		print('<div id="ccpcm_catalogue_popup_data3" class="ccpcm_catalogue_popup_type_inside">');
		print('<div class="ccpcm_catalogue_popup_left">Display by : </div>');
		print('<div class="ccpcm_catalogue_popup_right"><select id="ccpcm_catalogue_popup_section_display_by_select"><option value="film">Film</option><option value="projection">Projection</option></select></div>');
		print('</div>');
		print('<p><a id="ccpcm_catalogue_popup_data_save" class="button button-primary">Save</a></p>');
		print('</div>');

		print('<div id="ccpcm_catalogue_popup_other" class="ccpcm_catalogue_popup_type">');
		print('<p><a id="ccpcm_catalogue_popup_other_save" class="button button-primary">Save</a></p>');
		print('</div>');

		print('<div id="ccpcm_catalogue_popup_toc" class="ccpcm_catalogue_popup_type">');
		print('<p><a id="ccpcm_catalogue_popup_toc_save" class="button button-primary">Save</a></p>');
		print('</div>');

		print('<div id="ccpcm_catalogue_popup_index" class="ccpcm_catalogue_popup_type">');
		print('<p><a id="ccpcm_catalogue_popup_index_save" class="button button-primary">Save</a></p>');
		print('</div>');

		print('</div>');
	}

	public function display_right() {
		print('<iframe id="ccpcm_catalogue_container_right"></iframe>');
	}

	public function get_catalogues() {
		$catalogues = $this->ccpcm->templates->jsondb->get_ids('catalogues');
		asort($catalogues);
		$catalogues = array_values($catalogues);
		return $catalogues;
	}

	public function get_catalogue_masters() {
		$templates_ids = $this->ccpcm->templates->jsondb->get_ids('templates');
		$templates = array();
		foreach($templates_ids as $template_id) {
			$template = $this->ccpcm->templates->jsondb->get('templates', $template_id);
			if (array_key_exists('type', $template) && $template['type'] == 'master')
				$templates[] = $template_id;
		}
		asort($templates);
		$templates = array_values($templates);
		return $templates;
	}

	public function get_catalogue($id, $options = []) {
		$data = array();
		$data['catalogue'] = $this->ccpcm->templates->jsondb->get('catalogues', $id);
		if (array_key_exists('content', $data['catalogue'])){
			foreach($data['catalogue']['content'] as $idx => $row) {
				if (!in_array($row['type'], ['page_break', 'html', 'htmlx2', 'media', 'toc', 'index'])) {
					$template_id = $row['template'];
					$template_data = $this->ccpcm->templates->jsondb->get('templates', $template_id);
					if (!$template_data) {
						$data['catalogue']['content'][$idx]['_error'] = "Le template n'existe pas";
					}
					$type = $row['type'];
					$is_data2 = False;
					$is_elements_count_unknown = False;
					if (!array_key_exists('elements_count', $template_data))
						$this->ccpcm->log('[ get_catalogue ] Error counts : '.json_encode($row, True));
					if (!array_key_exists('elements_count', $template_data) || !$template_data['elements_count'])
						$is_elements_count_unknown = True;
					if ($type == 'section_and_subsections')
						$type = 'section';
					if ($type == '2sections') {
						$type = 'section';
						$is_data2 = True;
					}
					if ($row['data']) {
						$qn = $this->ccpcm->data->jsondb->get_quick_name($type, $row['data']);
						if ($qn)
							$data['catalogue']['content'][$idx]['_display']['data'] = $qn;
						else
							$data['catalogue']['content'][$idx]['_error'] = "Le contenu rattaché a été supprimé";
					} elseif (!$is_elements_count_unknown && array_key_exists('data', $row)) {
						$data['catalogue']['content'][$idx]['_error'] = "Pas de contenu selectionné";
					}
					if (array_key_exists('data2', $row) && $row['data2']) {
						$qn = $this->ccpcm->data->jsondb->get_quick_name($type, $row['data2']);
						if ($qn)
							$data['catalogue']['content'][$idx]['_display']['data2'] = $qn;
						else
							$data['catalogue']['content'][$idx]['_error'] = "Le contenu rattaché a été supprimé";
					} elseif (!$is_elements_count_unknown && $is_data2 && array_key_exists('data2', $row)) {
						$data['catalogue']['content'][$idx]['_error'] = "Pas de contenu selectionné";
					}
				}
			}
		}
		if (array_key_exists('no_data', $options) and $options['no_data'])
			return $data;
//		$data['data'] = $this->get_data($id);
		return $data;
	}

	public function get_catalogue_templates($type) {
		$templates_ids = $this->ccpcm->templates->jsondb->get_ids('templates');
		$templates = array();
		foreach($templates_ids as $template_id) {
			$template = $this->ccpcm->templates->jsondb->get('templates', $template_id);
			if ($template['type'] == $type)
				$templates[] = $template_id;
		}
		asort($templates);
		$templates = array_values($templates);
		return $templates;
	}

	public function get_catalogue_element_data($type) {
		switch($type) {
			case 'section_and_subsections':
			case '2sections':
				$type = 'section';
				break;
		}
		return $this->ccpcm->data->jsondb->get_quick_names($type);
	}

	public function get_data($id) {
		return [];
		//@todo: a revoir completement
		$data = array();
		$ids = $this->ccpcm->data->jsondb->get_ids($type);
		$t_ids = array();
		if (count($ids)) {
			if ($count === 0)
				$count = count($ids);
			for($n = 0; $n < $count; $n++) {
				$rand = rand(0, count($ids));
				if ($rand > count($ids) - 1)
					$rand = count($ids) - 1;
				$id = $ids[$rand];
				# @todo: justin code à supprimer pour arreter de voir tjrs le meme film
				#if ($type == 'film')
				#  $id = 26585;
				$d = $this->ccpcm->data->jsondb->get($type, $id);
				switch($type) {
					case 'section':
						$index = $this->ccpcm->data->jsondb->get_index('film', 'section');
						$films = array();
						if (array_key_exists($id, $index)) {
							$f_ids = $index[$id];
							foreach($f_ids as $f_id)
								$films[] = $this->ccpcm->data->jsondb->get('film', $f_id);
						}
						$d['films'] = $films;
						break;
					case 'movie-type':
            $index = $this->ccpcm->data->jsondb->get_index('jury', 'movie-type');
            $jurys = array();
            if (array_key_exists($id, $index)) {
              $j_ids = $index[$id];
              foreach($j_ids as $j_id)
                $jurys[] = $this->ccpcm->data->jsondb->get('jury', $j_id);
            }
            $d['jurys'] = $jurys;
            break;
				}
				$data[] = $d;
			}
		}
		if ($count == 1 && count($data) == 1) {
			return $data[0];
		}
		return $data;
	}

	public function set_catalogue($id, $data, $new) {
		$r_data = array();
		if ($new) {
			$r_content = $this->ccpcm->templates->jsondb->get('catalogues', $id);
			if ($r_content) {
				$r_data['id'] = $id;
				$r_data['content'] = $r_content;
				$r_data['data'] = $this->get_data($id);
				return $r_data;
			} else {
				$this->ccpcm->templates->jsondb->append('catalogues', $id, $data);
				$r_data['id'] = $id;
				return $r_data;
			}
		} else {
			$this->ccpcm->templates->jsondb->append('catalogues', $id, $data);
			$r_data['id'] = $id;
			return $r_data;
		}
	}

	public function delete_catalogue($id) {
		$this->ccpcm->templates->jsondb->delete('catalogues', $id);
	}

	/*
		RENDU !!!
	*/
	public function get_catalogue_content($id, $dpi=False) {
		$data = $this->ccpcm->templates->jsondb->get('catalogues', $id);
		if (!array_key_exists('content', $data))
			return null;
		foreach($data['content'] as $idx => $element) {
			switch($element['type']) {
				case 'media':
					$this->ccpcm->data->redefine($dpi);
					$sizes = [
						'r21_28'=>'595.28:793.7066666676',
						'r21_14'=>'595.28:396.8533333338',
						'r23_15'=>'651.969:425,197',
						'r23_30'=>'651.969:850.394',
						'r17_23'=>'168:230',
						'r17_11'=>'168:115',
						'r17_8'=>'168:80',
					];

					$data['content'][$idx]['media']['base64'] = $this->ccpcm->data->convert_file_to_base64($element['media_url'], $sizes, True);
					break;
				case 'html':
					$html = $element['html'];

					$html = $this->convert_html_img_inline($dpi, $html);
					$data['content'][$idx]['html'] = $html;
//					$this->ccpcm->log($data['content'][$idx]['html']);
//					$this->ccpcm->log("DPI : $dpi");
					break;
				case 'htmlx2':
					$html = $element['html'];
					$html2 = $element['html2'];

					$html = $this->convert_html_img_inline($dpi, $html);
					$html2 = $this->convert_html_img_inline($dpi, $html2);
					$data['content'][$idx]['html'] = $html;
					$data['content'][$idx]['html2'] = $html2;
//					$this->ccpcm->log($data['content'][$idx]['html']);
//					$this->ccpcm->log("DPI : $dpi");
					break;
			}
		}
		return $data['content'];
	}

	public function convert_html_img_inline($dpi, $html) {
		preg_match_all('/<img[^>]+>/i',$html, $result);
		foreach($result[0] as $img_tag) {
			preg_match_all('/(alt|title|src|width|height|class)="([^"]*)"/i',$img_tag, $attributs, PREG_SET_ORDER);
			$attrs = [];
			$width = 100;
			$url = False;
			$type = False;
			$img_tag_dst = False;
			foreach($attributs as $i_attribut => $t_attribut) {
				$key = $t_attribut[1];
				$value = $t_attribut[2];
				switch($key) {
					case 'src':
						$url = $value;
						$internal = False;
						$url_parse = parse_url($url);
						$path = '';
						if (!array_key_exists('host', $url_parse)) {
							print ("ERREUR DANS LE FICHIER SUIVANT");
							print_r($url_parse);
							die();
						}
						switch($url_parse['host']) {
							case 'dev.fifam.fr':
								$path = "/home/fifam.fr/vhosts/dev/htdocs";
								$internal = True;
								break;
							case 'www.fifam.fr':
								$path = "/home/fifam.fr/vhosts/www/htdocs";
								$internal = True;
								break;
						}
						if ($internal) {
							$url = sprintf("%s%s", $path, $url_parse['path']);
							$type = pathinfo($url, PATHINFO_EXTENSION);
						} else {
							$type = pathinfo(parse_url($url ,PHP_URL_PATH), PATHINFO_EXTENSION);
						}
						break;
					case 'width':
						$width = $value;
						$attrs[] = sprintf('%s="%s"', $key, $value);
						break;
					default:
						$attrs[] = sprintf('%s="%s"', $key, $value);
						break;
				}
			}
//						$this->ccpcm->log($url);
			if ($url) {
				$content = file_get_contents($url);
				if ($url == 'https://www.concipio.fr/logo.png') $this->ccpcm->log($type);
				if ($content !== False && $type) {
					$width *= $dpi / 72;
					$imagick = new Imagick();
					$imagick->readImageBlob($content);
					$imagick->resizeImage($width, False, imagick::FILTER_LANCZOS, 1, False);
					$img = 'data:image/' . $type . ';base64,' . base64_encode($imagick->getimageblob());
					$attrs[] = sprintf('%s="%s"', 'src', $img);
					$img_tag_dst = sprintf('<img %s/>', implode(' ', $attrs));
					$html = str_replace($img_tag, $img_tag_dst, $html);
				}
			}
			if(!$img_tag_dst) {
				$html = str_replace($img_tag, '', $html);
			}
		}
		return $html;
	}

	public function get_catalogue_data_order_by_order($a, $b) {
		if (is_array($a) && array_key_exists('_order', $a))
			$an = $a['_order'];
		else
			return 0;
		if (is_array($b) && array_key_exists('_order', $b))
			$bn = $b['_order'];
		else
			return 0;
//		$this->ccpcm->log("$an < $bn : ".(($an < $bn) ? -1 : 1));
		if ($an == $bn)
    	    return 0;
		return ($an < $bn) ? -1 : 1;
	}

	public function get_catalogue_data_order_by_date($a, $b) {
		if (is_array($a) && array_key_exists('_date', $a))
			$an = $a['_date'];
		else
			return 0;
		if (is_array($b) && array_key_exists('_date', $b))
			$bn = $b['_date'];
		else
			return 0;
		if ($an == $bn)
			return 0;
		return ($an < $bn) ? -1 : 1;
	}

	public function get_catalogue_data_order_by_projection($a, $b) {
		$adt = sprintf("%s %s:00", $a['date']['isoformat'], $a['start_and_stop_time']['begin']);
		$bdt = sprintf("%s %s:00", $b['date']['isoformat'], $b['start_and_stop_time']['begin']);

		if ($adt == $bdt)
        return 0;
    return ($adt < $bdt) ? -1 : 1;
	}

	public function get_catalogue_data_order_by_name($a, $b) {
		if (is_array($a) && array_key_exists('post_title', $a))
			$an = $a['post_title'];
		else
			return 0;
		if (is_array($b) && array_key_exists('post_title', $b))
			$bn = $b['post_title'];
		else
			return 0;
		if ($an == $bn)
        return 0;
    return ($an < $bn) ? -1 : 1;
	}

	function skip_accents( $str, $charset='utf-8' ) {
	    $str = htmlentities( $str, ENT_NOQUOTES, $charset );
	    $str = preg_replace( '#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str );
	    $str = preg_replace( '#&([A-za-z]{2})(?:lig);#', '\1', $str );
	    $str = preg_replace( '#&[^;]+;#', '', $str );
	    return $str;
	}

	public function name_simplify($txt) {
//		$this->ccpcm->log($txt);
//		$before = $txt;
		$txt = strtolower($txt);
		$txt = str_replace("'", ' ', $txt);
		$txt = str_replace(".", ' ', $txt);
		$txt = str_replace("-", ' ', $txt);
		$txt = str_replace('"', ' ', $txt);
		$txt = $this->skip_accents($txt);
//		$this->ccpcm->log("name_simplify : $before => $txt");
		return $txt;
	}

	public function get_catalogue_data_order_by_name_simplified($a, $b) {
		if (is_array($a) && array_key_exists('_name_simplified', $a) && $a['_name_simplified'])
			$an = $this->name_simplify($a['_name_simplified']);
		elseif (is_array($a) && array_key_exists('french_operating_title', $a) && $a['french_operating_title'])
			$an = $this->name_simplify($a['french_operating_title']);
		elseif (is_array($a) && array_key_exists('post_title', $a))
			$an = $a['post_title'];
		else
			return 0;
			
		if (is_array($b) && array_key_exists('_name_simplified', $b) && $b['_name_simplified'])
			$bn = $this->name_simplify($b['_name_simplified']);
		elseif (is_array($b) && array_key_exists('french_operating_title', $b) && $b['french_operating_title'])
			$bn = $this->name_simplify($b['french_operating_title']);
		elseif (is_array($b) && array_key_exists('post_title', $b))
			$bn = $b['post_title'];
		else
			return 0;
		$an = trim(strtolower($an));
		$bn = trim(strtolower($bn));
		if ($an == $bn)
			return 0;
//		$this->ccpcm->log("[ get_catalogue_data_order_by_name_simplified ] '$an' <=> '$bn' => ".(($an < $bn) ? -1 : 1));
		return ($an < $bn) ? -1 : 1;
	}

	public function get_catalogue_data_order_by($data, $order) {
		switch ($order) {
			case 'order':
				$field = '_order';
				usort($data, [$this, 'get_catalogue_data_order_by_order']);
				break;
			case 'name_simplified':
				$field = 'post_title';
				usort($data, [$this, 'get_catalogue_data_order_by_name_simplified']);
				break;
			case 'date':
				$field = '_date';
				usort($data, [$this, 'get_catalogue_data_order_by_date']);
				break;
			case 'name':
				$field = 'post_title';
				usort($data, [$this, 'get_catalogue_data_order_by_name']);
				break;
			case 'projection':
				$field = 'post_title';
				usort($data, [$this, 'get_catalogue_data_order_by_projection']);
				break;
		}
//		$this->try_log_order($data, $field);
		return $data;
	}

	public function try_log_order($data, $field) {
		foreach($data as $d) {
			$this->ccpcm->log("+ order + ".$d[$field]);
		}
	}

	public function get_catalogue_data_by_type_and_id($template_id, $type, $ids, $order, $infos = [], $count = 1) {
		if ($ids === False)
			$ids = [];
		$ids = array_filter($ids);
		if ($template_id) {
			$template = $this->ccpcm->templates->jsondb->get('templates', $template_id);
			if ($count !== True)
				$count = $template['elements_count'];
			else
				$count = 1;
		}
		switch($type) {
			case 'index':
				$section_ids = $this->ccpcm->data->jsondb->get_ids('section');
				$sections = [];
				foreach($section_ids as $id) {
				  $this->ccpcm->data->jsondb->get('section', $id);
				  if ($id != 220)
					$sections[] = $this->ccpcm->data->jsondb->get('section', $id);
				}
				usort($sections, [$this->ccpcm->catalogues, 'get_catalogue_data_order_by_order']);
				return ['sections'=>$sections]; 
			break;
		}
		if ($count == 1) {
			if (count($ids)) $id = $ids[0];
			switch($type) {
				case '2sections':
					$type = 'section';
					break;
			}
			$d = $this->ccpcm->data->jsondb->get($type, $id);
			if ($infos) {
				$d['infos'] = [];
				foreach($infos as $k => $v)
					$d['infos'][$k] = $v;
			}
			switch($type) {
				case 'section':
					$index = $this->ccpcm->data->jsondb->get_index('film', 'section');
					$films = array();
					if (array_key_exists($id, $index)) {
						$f_ids = $index[$id];
						foreach($f_ids as $f_id) {
							$f_film = $this->ccpcm->data->jsondb->get('film', $f_id);
							$this->ccpcm->custom->append_film_additionnal_fields($f_film);
							$films[] = $f_film;
						}
					}
					$d['films'] = $films;
					$d['films'] = $this->ccpcm->custom->apply_post_in_taxonomy_order($d['ccppto_film'], $d['films']);
					$d['projections'] = $this->ccpcm->custom->apply_post_in_taxonomy_order($d['ccppto_projection'], $d['projections']);
					$d['films'] = $this->get_catalogue_data_order_by($d['films'], $order);
					$d['projections'] = $this->get_catalogue_data_order_by($d['projections'], $order);
					if ($d['featured_film_1']) {
						$d['featured_film_1'] = $this->ccpcm->data->jsondb->get('film', $d['featured_film_1']);
					} else {
						$d['featured_film_1'] = False;
					}
					if ($d['featured_film_2']) {
						$d['featured_film_2'] = $this->ccpcm->data->jsondb->get('film', $d['featured_film_2']);
					} else {
						$d['featured_film_2'] = False;
					}
					break;
				case 'section_and_subsections':
					$d = $this->ccpcm->data->jsondb->get('section', $id);
					$p_index = $this->ccpcm->data->jsondb->get_index('projection', 'film');

					$s_index = $this->ccpcm->data->jsondb->get_index('film', 'section');
					if (array_key_exists($id, $s_index)) {
						$f_ids = $s_index[$id];
						$films = array();
						foreach($f_ids as $f_id) {
							$film = $this->ccpcm->data->jsondb->get('film', $f_id);
							$this->ccpcm->custom->append_film_additionnal_fields($film);
							$films[] = $film;
						}
						$s_section['films'] = $films;
						$d['films'] = $this->get_catalogue_data_order_by($films, $order);
					} else {
						$d['films'] = array();
					}

					$section_index = $this->ccpcm->data->jsondb->get_index('section', 'parent');
					$d['subsections'] = array();
					if (array_key_exists($id, $section_index)) {
						$s_ids = $section_index[$id];
						foreach($s_ids as $s_id) {
							$s_section = $this->ccpcm->data->jsondb->get('section', $s_id);

							$films = array();
							if (array_key_exists($s_id, $s_index)) {
								$f_ids = $s_index[$s_id];
								$films = array();
								foreach($f_ids as $f_id) {
									$film = $this->ccpcm->data->jsondb->get('film', $f_id);
									$this->ccpcm->custom->append_film_additionnal_fields($film);
									$films[] = $film;
								}
//								$s_section['films'] = $films;
								$s_section['films'] = $this->get_catalogue_data_order_by($films, $order);
							}
							$d['subsections'][] = $s_section;
						}
						$d['subsections'] = $this->get_catalogue_data_order_by($d['subsections'], 'order');
					}

					break;
				case 'movie-type':
					$d = $this->ccpcm->data->jsondb->get('movie-type', $id);
					$index = $this->ccpcm->data->jsondb->get_index('jury', 'movie-type');
					$jurys = array();
					if (array_key_exists($id, $index)) {
						$j_ids = $index[$id];
						foreach($j_ids as $j_id)
							$jurys[] = $this->ccpcm->data->jsondb->get('jury', $j_id);
					}
					$d['jurys'] = $this->get_catalogue_data_order_by($jurys, $order);
					break;
				case 'film':
					$this->ccpcm->custom->append_film_additionnal_fields($d);
					break;
				default:
					break;
			}
			return $d;
		} elseif (count($ids) > 1) {
			$data = [];
			foreach($ids as $id) {
				$data[] = $this->get_catalogue_data_by_type_and_id($template_id, $type, [$id], $order, $infos, True);
			}
			return $data;
		} else {
			$data = [];
			if (count($ids) == 1) {
				$id = $ids[0];
				$qns = [$id=>$id];
			} else {
				$qns = $this->ccpcm->data->jsondb->get_quick_names($type);
			}
			//fonction de tri !!
			asort($qns);
			foreach($qns as $id => $qn) {
				$d = $this->ccpcm->data->jsondb->get($type, $id);
				if ($infos) {
					$d['infos'] = [];
					foreach($infos as $k => $v)
						$d['infos'][$k] = $v;
				}
				switch($type) {
					case 'movie-type':
						$index = $this->ccpcm->data->jsondb->get_index('jury', 'movie-type');
						$jurys = array();
						if (array_key_exists($id, $index)) {
							$j_ids = $index[$id];
							foreach($j_ids as $j_id)
								$jurys[] = $this->ccpcm->data->jsondb->get('jury', $j_id);
						}
						$d['jurys'] = $this->get_catalogue_data_order_by($jurys, $order);
						break;
					case 'partenaire-category':
						$index = $this->ccpcm->data->jsondb->get_index('partenaire', 'partenaire-category');
						$partenaire = [];
						if (array_key_exists($id, $index)) {
						  $c_ids = $index[$id];
						  foreach($c_ids as $c_id) {
							$partenaire[] = $this->ccpcm->data->jsondb->get('partenaire', $c_id);
						  }
						}
						$d['partners'] = $this->get_catalogue_data_order_by($partenaire, $order);
						break;
				}

/*				switch($type) {
					case 'section':
						$index = $this->ccpcm->data->jsondb->get_index('film', 'section');
						$films = array();
						if (array_key_exists($id, $index)) {
							$f_ids = $index[$id];
							foreach($f_ids as $f_id)
								$films[] = $this->ccpcm->data->jsondb->get('film', $f_id);
						}
						$d['films'] = $films;
						break;
					case 'section_and_subsections':
					//@todo: a completer
						$index = $this->ccpcm->data->jsondb->get_index('film', 'section');
						$films = array();
						if (array_key_exists($id, $index)) {
							$f_ids = $index[$id];
							foreach($f_ids as $f_id)
								$films[] = $this->ccpcm->data->jsondb->get('film', $f_id);
						}
						$d['films'] = $films;
						break;
				}
*/
				$data[] = $d;
			}
			$data = $this->get_catalogue_data_order_by($data, $order);
			return $data;
		}
	}
}
