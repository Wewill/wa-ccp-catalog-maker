<?php

class ccpcm_catalogues_custom extends ccpcm_object {
    public $types = array(
        'partner'=>'Partners',
		'partner-category'=>'Catégories de Partners',
        'html'=>'Html',
        'htmlx2'=>'2 Html',
        'media'=>'Media',
        'directory' => 'Répertoire',
        'thematic' => 'Thématique',
        'farm' => 'Ferme',
        'operation' => 'Opération',
        'structure' => 'Structure',
        'toc'=>'Toc',
        'index'=>'Index',
        'page_break'=>'Page break',
    );

	public function get_catalogue_element_data($type) {
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
				$d = $this->ccpcm->data->jsondb->get($type, $id);
				switch($type) {
					case 'thematic':
                        $index = $this->ccpcm->data->jsondb->get_index('directory', 'thematic');
                        $directories = [];
                        if (array_key_exists($id, $index)) {
                            $d_ids = $index[$id];
                            foreach($d_ids as $d_id) {
                                $directory = $this->ccpcm->data->jsondb->get('directory', $d_id);								
								$terms = [
									'relationships_farm' => 'farm',
									'relationships_operation' => 'operation',
									'relationships_structure' => 'structure',
								];
								foreach($terms as $fieldName => $termName) {
									if (array_key_exists($fieldName, $directory)) {
										$termsData = [];
										if (is_string($directory[$fieldName])) {
											$directory[$fieldName] = $this->ccpcm->data->jsondb->get($termName, $directory[$fieldName]);
										} else {
											$termIds = $directory[$fieldName];
											foreach($termIds as $termId)
												$termsData[] = $this->ccpcm->data->jsondb->get($termName, $termId);
											$directory[$fieldName] = $termsData;
										}
									}
								}
                                $directories[] = $directory;
                            }
                        }
                        $d['directories'] = $directories;
                        unset($directories);
                        $order = 'order';
						break;
					case 'directory':
						$terms = [
							'relationships_farm' => 'farm',
							'relationships_operation' => 'operation',
							'relationships_structure' => 'structure',
						];
                        foreach($terms as $fieldName => $termName) {
                            if (array_key_exists($fieldName, $d)) {
                                $termsData = [];
                                if (is_string($d[$fieldName])) {
                                    $d[$fieldName] = $this->ccpcm->data->jsondb->get($termName, $d[$fieldName]);
                                } else {
                                    $termIds = $d[$fieldName];
                                    foreach($termIds as $termId)
                                        $termsData[] = $this->ccpcm->data->jsondb->get($termName, $termId);
                                    $d[$fieldName] = $termsData;
                                }
                            }
                        }
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
				$thematic_ids = $this->ccpcm->data->jsondb->get_ids('thematic');
				$thematics = [];
				foreach($thematic_ids as $id) {
			        $this->ccpcm->data->jsondb->get('thematic', $id);
					$thematics[] = $this->ccpcm->data->jsondb->get('thematic', $id);
				}
				usort($thematics, [$this->ccpcm->catalogues, 'get_catalogue_data_order_by_order']);
				return ['thematics'=>$thematics]; 
			break;
		}
		if ($count == 1) {
			if (count($ids)) $id = $ids[0];
			switch($type) {
			}
			$d = $this->ccpcm->data->jsondb->get($type, $id);
			if ($infos) {
				$d['infos'] = [];
				foreach($infos as $k => $v)
					$d['infos'][$k] = $v;
			}
			switch($type) {
                case 'thematic':
                    $index = $this->ccpcm->data->jsondb->get_index('directory', 'thematic');
                    $directories = [];
                    if (array_key_exists($id, $index)) {
                        $d_ids = $index[$id];
                        foreach($d_ids as $d_id) {
                            $directory = $this->ccpcm->data->jsondb->get('directory', $d_id);
                            $directories[] = $directory;
                        }
                    }
                    $d['directories'] = $directories;
                    unset($directories);
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
					case 'partner-category':
						$index = $this->ccpcm->data->jsondb->get_index('partner', 'partner-category');
						$partner = [];
						if (array_key_exists($id, $index)) {
						  $c_ids = $index[$id];
						  foreach($c_ids as $c_id) {
							$partner[] = $this->ccpcm->data->jsondb->get('partner', $c_id);
						  }
						}
						$d['partners'] = $this->get_catalogue_data_order_by($partner, $order);
						break;
				}				
				$data[] = $d;
			}
			$data = $this->get_catalogue_data_order_by($data, $order);
			return $data;
		}
	}
}