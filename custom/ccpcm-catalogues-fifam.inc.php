<?php

class ccpcm_catalogues_custom extends ccpcm_object {
    public $types = array(
        'section'=>'Section',
        'section_and_subsections'=>'Section and subsections',
        '2sections'=>'1/2 page par section x 2',
        'film'=>'Film',
        'contact'=>'Contact',
        'jury'=>'Jury',
        'movie-type'=>'Movie types and Jurys',
        'partenaire'=>'Partners',
        'html'=>'Html',
        'htmlx2'=>'2 Html',
        'media'=>'Media',
        'toc'=>'Toc',
        'index'=>'Index',
        'page_break'=>'Page break',
        'planning'=>'Planning',
        'accreditation'=>'Accreditation',
        'partenaire-category'=>'Catégories de Partners',
        'gravityform_winner' => 'Gravityform Winner',
    );

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