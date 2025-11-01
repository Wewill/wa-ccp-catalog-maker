<?php

class ccpcm_templates_custom extends ccpcm_object {
    public function get_random_data($type, $count=0, $ids=[]) {
        $data = array();
        switch ($type) {
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
        default:
            if (!count($ids)) {
                switch($type) {
                    default:
                        $ids = $this->ccpcm->data->jsondb->get_ids($type);
                        break;
                }
            } else {
                switch($type) {
                    case 'section_and_subsections':
                        //$id = 237; // Ouverture cloture #40
                        //$id = 233; // Compet CM #40
                        $id = $ids[0]; // Compet #40
                        return $this->ccpcm->catalogues->get_catalogue_data_by_type_and_id(False, $type, [$id], 'name_simplified', [], 1); //182 afrique /193 espagne / 210 UPJV // 192 Competition // 211 MASTERCLASS // 178 Competition CM // 181 Travaie
                        break;
                }          
            }
        }

        $t_ids = array();
        $p_ids = [];
        $random = True;
        if (count($ids)) {
            if ($count == 0) {
                $count = count($ids);
                $random = False;
            }
            for($n = 0; $n < $count; $n++) {
                if ($random) {
                $p_ids = array_diff($ids, $t_ids);
                $p_ids = array_values($p_ids);
                if (count($p_ids) == 0)
                    $p_ids = $ids;
                $rand = rand(0, count($p_ids));
                if ($rand > count($p_ids) - 1)
                    $rand = count($p_ids) - 1;
                if (!array_key_exists($rand, $p_ids)) 
                    throw new Exception(sprintf('[ ERROR ] calcul rand '.$rand.' sur '.json_encode($p_ids)));
                $id = $p_ids[$rand];
                } else {
                $rand = $n;
                if ($rand > count($ids) - 1)
                    $rand = count($ids) - 1;
                $id = $ids[$rand];
                }
                $t_ids[] = $id;
                $d = $this->ccpcm->data->jsondb->get($type, $id);
                switch($type) {
                    case 'thematic':
                        $index = $this->ccpcm->data->jsondb->get_index('directory', 'thematic');
                        $directories = [];
                        if (array_key_exists($id, $index)) {
                            $d_ids = $index[$id];
                            foreach($d_ids as $d_id) {
                                $directory = $this->ccpcm->data->jsondb->get('directory', $d_id);
                                $this->ccpcm->custom->append_film_additionnal_fields($directory);
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
    /*
                        if (array_key_exists('ccppto_film', $d) && array_key_exists('films', $d))
                            $d['films'] = $this->ccpcm->custom->apply_post_in_taxonomy_order($d['ccppto_film'], $d['directories']);
                        else
                            $d['films'] = [];
    */
                        $d['directories'] = $this->ccpcm->catalogues->get_catalogue_data_order_by($d['directories'], $order);
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
}