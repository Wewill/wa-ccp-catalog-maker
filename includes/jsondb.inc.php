<?php

class jsondb {
	private $db_path = "/tmp/";
	private $indexes;
	private $quick_names;
	private $__quick_names_cache;

	function __construct($db_path, $indexes = [], $quick_names = []) {
		$this->db_path = $db_path;
		$this->indexes = $indexes;
		$this->quick_names = $quick_names;
		$this->__quick_names_cache = [];
	}

	function append($type, $id, $data) {
		$this->append_index($type, $id, $data);
		$pathRelative = sprintf("%s/%s/", $this->db_path, $type);
		$path = sprintf("%s/%s/", __DIR__.'/'.$this->db_path, $type);
		if (!is_dir($path))
			mkdir($path, 0755, True);
		$file = sprintf("%s/%s.json", $path, $id);
		$this->register($type, 'path', $pathRelative);
		file_put_contents($file, json_encode($data, True));
		$this->append_quick_names($type, $id, $data);
	}

	function delete($type, $id) {
		$data = $this->get($type, $id);
		$this->delete_index($type, $id, $data);
		$path = sprintf("%s/%s/", __DIR__.'/'.$this->db_path, $type);
		$file = sprintf("%s/%s.json", $path, $id);
		if (file_exists($file)) {
			unlink($file);
			return True;
		}
		return False;
	}

	function get($type, $id) {
		$path = sprintf("%s/%s/", __DIR__.'/'.$this->db_path, $type);
		$file = sprintf("%s/%s.json", $path, $id);
		if (file_exists($file)) {
			$data = file_get_contents($file);
			return json_decode($data, True);
		}
		return [];
	}

	function append_quick_names($type, $id, $data) {
		if (array_key_exists($type, $this->quick_names)) {
			$fields = $this->quick_names[$type];
				if ($fields) {
				$name = [];
				foreach($fields as $field)
					$name[] = $data[$field];
				$name = implode(' ', $name);
				$file = sprintf("%s/names_%s.json", __DIR__.'/'.$this->db_path, $type);
				$fileRelative = sprintf("%s/names_%s.json", $this->db_path, $type);
				$this->register($type, 'names', $fileRelative);
				if (file_exists($file)) {
					$names = file_get_contents($file);
					$names = json_decode($names, True);
				} else {
					$names = [];
				}
				$names[$id] = $name;
				file_put_contents($file, json_encode($names, True));
			}
		}
	}

	function get_quick_names($type) {
		if (array_key_exists($type, $this->quick_names)) {
			if (!array_key_exists($type, $this->__quick_names_cache)) {
				$file = sprintf("%s/names_%s.json", __DIR__.'/'.$this->db_path, $type);
				if (file_exists($file)) {
					$names = file_get_contents($file);
					$names = json_decode($names, True);
				} else {
					$names = [];
				}
				$this->__quick_names_cache[$type] = $names;
				return $this->__quick_names_cache[$type];
			}
		}
		return False;
	}

	function get_quick_name($type, $id) {
		if (array_key_exists($type, $this->quick_names)) {
			if (!array_key_exists($type, $this->__quick_names_cache)) {
				$file = sprintf("%s/names_%s.json", __DIR__.'/'.$this->db_path, $type);
				if (file_exists($file)) {
					$names = file_get_contents($file);
					$names = json_decode($names, True);
				} else {
					$names = [];
				}
				$this->__quick_names_cache[$type] = $names;
			}
		} else {
//			return false;
		}
		if (array_key_exists($id, $this->__quick_names_cache[$type]))
			return $this->__quick_names_cache[$type][$id];
		else
			return False;
	}

	function get_index($type, $t_name) {
		$file = sprintf("%s/index_%s_%s.json", __DIR__.'/'.$this->db_path, $type, $t_name);
		if (file_exists($file)) {
			$data = file_get_contents($file);
			$data = json_decode($data, True);
			return $data;
		}
		return [];
	}

	function get_ids($type) {
		$file = sprintf("%s/ids_%s.json", __DIR__.'/'.$this->db_path, $type);
		if (file_exists($file)) {
			$data = file_get_contents($file);
			$data = json_decode($data, True);
			return $data;
		}
		return [];
	}

	function append_index_forced($child_type, $child_ids, $parent_type, $parent_id) {
		if (array_key_exists($child_type, $this->indexes) && array_key_exists($parent_type, $this->indexes[$child_type])) {
			$file = sprintf("%s/index_%s_%s.json", __DIR__.'/'.$this->db_path, $child_type, $parent_type);
			$fileRelative = sprintf("%s/index_%s_%s.json", $this->db_path, $child_type, $parent_type);
			$this->register($t_name, 'index', $fileRelative);
			if (file_exists($file)) {
				$index = file_get_contents($file);
				$index = json_decode($index, True);
			} else {
				$index = [];
			}
			if (array_key_exists($parent_id, $index)) {
				$index[$parent_id] = array_unique(array_merge($index[$parent_id], $child_ids));
			} else {
				$index[$parent_id] = $child_ids;
			}
			file_put_contents($file, json_encode($index, True));
		}
	}

	function append_index($type, $id, $data) {
		$t_name = $type;
		if (array_key_exists($type, $this->indexes)) {
			foreach($this->indexes[$type] as $t_name => $i_path) {
				$k_index = $this->get_data_index_for_append($i_path, $data);
				$file = sprintf("%s/index_%s_%s.json", __DIR__.'/'.$this->db_path, $type, $t_name);
				$fileRelative = sprintf("%s/index_%s_%s.json", $this->db_path, $type, $t_name);
				$this->register($t_name, 'index', $fileRelative);
				if (file_exists($file)) {
					$index = file_get_contents($file);
					$index = json_decode($index, True);
				} else {
					$index = [];
				}
				if ($k_index !== False) {
					foreach($k_index as $v_index) {
						if (is_array($v_index)) {
							foreach($v_index as $vv_index) {
								if (!array_key_exists($vv_index, $index)) 
									$index[$vv_index] = [];
								$index[$vv_index][] = $id;
							}
						} else {
							if (!array_key_exists($v_index, $index))
								$index[$v_index] = [];
							$index[$v_index][] = $id;
						}
					}
					file_put_contents($file, json_encode($index, True));
				}
			}
		}
		$file = sprintf("%s/ids_%s.json", __DIR__.'/'.$this->db_path, $type, $t_name);
		$fileRelative = sprintf("%s/ids_%s.json", $this->db_path, $type, $t_name);
		$this->register($type, 'ids', $fileRelative);
		if (file_exists($file)) {
			$index = file_get_contents($file);
			$index = json_decode($index, True);
		} else {
			$index = [];
		}
		if (!in_array($id, $index))
			$index[] = $id;
		file_put_contents($file, json_encode($index, True));
	}

	function delete_index($type, $id, $data) {
		$t_name = $type;
		if (array_key_exists($type, $this->indexes)) {
			foreach($this->indexes[$type] as $t_name => $i_path) {
				$k_index = $this->get_data_index_for_append($i_path, $data);
				$file = sprintf("%s/index_%s_%s.json", __DIR__.'/'.$this->db_path, $type, $t_name);
				$fileRelative = sprintf("%s/index_%s_%s.json", $this->db_path, $type, $t_name);
				$this->register($t_name, 'index', $fileRelative);
				if (file_exists($file)) {
					$index = file_get_contents($file);
					$index = json_decode($index, True);
				} else {
					$index = [];
				}
				if ($k_index !== False) {
					foreach($k_index as $v_index) {
						if (array_key_exists($v_index, $index)) {
							if (in_array($id, $index[$v_index]))
								$index[$v_index] = array_diff($index[$v_index], [$id]);
								$index[$v_index] = array_values($index[$v_index]);
						}
					}
					file_put_contents($file, json_encode($index, True));
				}
			}
		}
		$file = sprintf("%s/ids_%s.json", __DIR__.'/'.$this->db_path, $type, $t_name);
		$fileRelative = sprintf("%s/ids_%s.json", $this->db_path, $type, $t_name);
		$this->register($type, 'ids', $fileRelative);
		if (file_exists($file)) {
			$index = file_get_contents($file);
			$index = json_decode($index, True);
		} else {
			$index = [];
		}
		$index = array_diff($index, [$id]);
		$index = array_values($index);
		file_put_contents($file, json_encode($index, True));
	}


	function register($name, $type, $path_or_file) {
		if (!is_dir(__DIR__.'/'.$this->db_path))
			mkdir(__DIR__.'/'.$this->db_path);
		$file = sprintf("%s/_.json", __DIR__.'/'.$this->db_path);
		if (file_exists($file)) {
			$data = file_get_contents($file);
			$data = json_decode($data, True);
		} else {
			$data = [];
		}
		if (!array_key_exists($name, $data)) {
			$data[$name] = array(
				'type'=>$type,
				$type=>$path_or_file
			);
			file_put_contents($file, json_encode($data, True));
		}
	}

	function get_indexes() {
		// @todo: à completer
	}

	function get_data_index_for_append($a_path, $data) {
		$d = [];
		foreach((array) $a_path as $i_path) {
			$tmp = explode('@', $i_path);
			if (count($tmp) == 1) {
				if (array_key_exists($i_path, $data) and $data[$i_path])
					if (!in_array($data[$i_path], $d))
						$d[] = $data[$i_path];
			} else {
				$k = $tmp[0];
				$i = $tmp[1];
				if (array_key_exists($k, $data) and $data[$k]) {
					if (count($data[$k]) and !array_key_exists(0, $data[$k]))
						$data[$k] = [$data[$k]];
					foreach($data[$k] as $v) {
						if (array_key_exists($i, $v) and $v[$i])
							if (!in_array($v[$i], $d))
								$d[] = $v[$i];
					}
				}
			}
		}
		if ($d)
			return $d;
		return False;
	}

	function remove_all($src = False) {
		if (!$src)
			$src = __DIR__.'/'.$this->db_path;
		$dir = opendir($src);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
			    $full = $src . '/' . $file;
			    if ( is_dir($full) ) {
			        $this->remove_all($full);
			    }
			    else {
			        unlink($full);
			    }
			}
		}
		closedir($dir);
		rmdir($src);
	}
}
