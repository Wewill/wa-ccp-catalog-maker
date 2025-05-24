<?php

class ccppm_custom extends ccppm_object {
    private $cache = [];

    private function get_index($f_type, $f_name) {
        $name = sprintf('index__%s__%s', $f_type, $f_name);
        if (array_key_exists($name, $this->cache))
            return $this->cache[$name];
        $index = $this->ccppm->data->jsondb->get_index($f_type, $f_name);
        $this->cache[$name] = $index;
        return $index;
    }

    private function get_data($f_type, $f_id) {
        $name = sprintf('data__%s__%s', $f_type, $f_id);
        if (array_key_exists($name, $this->cache))
            return $this->cache[$name];
        $data = $this->ccppm->data->jsondb->get($f_type, $f_id);
        $this->cache[$name] = $data;
        return $data;
    }

    public function append_film_additionnal_fields(&$f_film) {
        $index = $this->get_index('projection', 'film');
        $projections = [];
        if (!array_key_exists('id', $f_film))
            return ;
        $id = $f_film['id'];
        $young_public = False;
        $is_guest = False;
        $is_highlighted = False;
	$is_debate = False;
//        $this->ccppm->log("$id");
        if (array_key_exists($id, $index)) {
//          $this->ccppm->log("$id 1");
          $p_ids = $index[$id];
          foreach($p_ids as $p_id) {
//            $this->ccppm->log("$id 2");
            $projection = $this->ccppm->data->jsondb->get('projection', $p_id);
            if ($projection['is_guest'])
                $is_guest = True;
            if (array_key_exists('is_highlighted', $projection) && $projection['is_highlighted'])
                $is_highlighted = True;
            if ($projection['young_public'])
                $young_public = True;
            if ($projection['is_debate'])
                $is_debate = True;
            if ($projection['is_debate'])
                $is_debate = True;
            if (array_key_exists('room', $projection) && is_array($projection['room'])) foreach($projection['room'] as $r_idx => $room) {
              if ($room['parent']) {
                $projection['room'][$r_idx]['cinema'] = $this->get_data('room', $room['parent']);
              }
              if (array_key_exists(0, $projection['room']))
                $projection['room'] = $projection['room'][0];
//              else 
//                $this->ccppm->log('[append_film_additionnal_fields] pb de projection/room '.$p_id.' : '.json_encode($projection, True));
            }
            $projections[] = $projection;
          }
        }
        $f_film['projections'] = $projections;
        $f_film['young_public'] = $young_public;
        $f_film['is_guest'] = $is_guest;
        $f_film['is_highlighted'] = $is_highlighted;
        $f_film['is_debate'] = $is_debate;
    }

        public function apply_post_in_taxonomy_order($order, $data) {
		if (!$order)
			return $data;
                $field_order = '_order';
		$field_id = 'id';
		$order_inv = [];
		foreach($order as $idx => $post_ids) {
			$post_ids = explode(',', $post_ids);
			foreach($post_ids as $post_id) {
				$order_inv[$post_id] = $idx;
			}
		}
		foreach($data as $idx => $entity) {
			if (array_key_exists($field_id, $entity) && array_key_exists($entity[$field_id], $order_inv))
				$data[$idx][$field_order] = $order_inv[$entity[$field_id]];
		}
                return $data;
        }
}

