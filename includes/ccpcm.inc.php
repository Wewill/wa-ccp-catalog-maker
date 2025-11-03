<?php

//@ini_set( 'display_errors', 1 );

class ccpcm {
	private $modules = ['display', 'data', 'catalogues', 'templates', 'integrator', 'custom', 'export'];
	public $edition_slug = False;
	public $edition_id = False;
	private $objects = [];

	function __construct($edition_slug = False, $edition_id = False) {
		global $ccp_editions_filter;
		if ($ccp_editions_filter) {
			$this->edition_slug = $ccp_editions_filter->get_current_edition();
			$this->edition_id = $ccp_editions_filter->get_current_edition_id();
		}
		if ($edition_slug)
			$this->edition_slug = $edition_slug;
		if ($edition_id)
			$this->edition_id = $edition_id;
	}

	public function __set($name, $value) { 
		$this->objects[$name] = $value;
	}

	function __get($name) {
		if (array_key_exists($name, $this->objects))
			return $this->objects[$name];
		elseif (in_array($name, $this->modules)) {
			require_once(__DIR__."/ccpcm-${name}.inc.php");
			$c = "ccpcm_${name}";
			if (class_exists($c)) {
				$this->objects[$name] = new $c($this);
				return $this->objects[$name];
			}
		}
		return False;
	}

	function run() {
		# non ajax
		add_shortcode( 'ccpcm', [$this, 'shortcode'] );
		$this->display->init_menu();
	}

	public function shortcode($attrs) {
		if (!array_key_exists('method', $attrs))
			throw new \Exception('method attribute not defined in ccpcm shortcode');
		$html = '';
		switch($attrs['method']) {
			case 'integrator_button':
				$this->display_integrator(True);
				$attrs = shortcode_atts( [
					'template' => '',
					'master'=>'Master_A4',
					'id' => get_the_ID(),
					'title' => 'Voir le PDF',
					'class' => 'ccpcm-integrator-button',
					'render_dpi' => 9,
					'filename_prefix' => false,
				], $attrs, 'ccpcm');
				$html = sprintf('<a class="%s" onclick="ccpcm_integrator_download_pdf(\'%s\', \'%s\', {\'form_id\' : %s}, %s, \'%s\');">%s</a>', $attrs['class'],  $attrs['master'], $attrs['template'], $attrs['id'], $attrs['render_dpi'], $attrs['filename_prefix'], $attrs['title']);
				break;
		}	
		
		return $html;
	}

	public function log($msg) {
		if (is_array($msg) || is_object($msg))
			$msg = print_r($msg, True);
		$msg = "[ ".date('Y-m-d H:i:s')." ] $msg\n";
		//@todo to comment
		//		$msg .= print_r(debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 4), True)."\n";
		$path = dirname(__FILE__);
		file_put_contents($path."/events.log", $msg, FILE_APPEND);
	}

	public function display_integrator($complete = False) {
//		wp_add_inline_script('pdfmake_extra', 'var edition_slug = "'.$this->edition_slug.'"; var edition_id = "'.$this->edition_id.'";');
//		wp_enqueue_script('pdfmake_extra');
		print ('<script>var edition_slug = "'.$this->edition_slug.'"; var edition_id = "'.$this->edition_id.'"; </script>');
		wp_enqueue_script('pdfmake', plugin_dir_url(__FILE__).'../bower_components/pdfmake/build/pdfmake.js');
		wp_enqueue_style('ccpcm_fonts', plugin_dir_url(__FILE__).'../custom/'.CCPCM_PROJECT.'/fonts/fonts.css');

//    wp_enqueue_style('codemirror', plugin_dir_url(__FILE__).'../bower_components/codemirror/lib/codemirror.css');
//    wp_enqueue_script('codemirror', plugin_dir_url(__FILE__).'../bower_components/codemirror/lib/codemirror.js');
//    wp_enqueue_script('codemirror_js', plugin_dir_url(__FILE__).'../bower_components/codemirror/mode/javascript/javascript.js');

		wp_enqueue_script('pdfmake_fonts', plugin_dir_url(__FILE__).'../custom/'.CCPCM_PROJECT.'/js/pdfmake_fonts.js', ['pdfmake']);
		wp_enqueue_script('pdfmake_fonts_files', plugin_dir_url(__FILE__).'../custom/'.CCPCM_PROJECT.'/js/vfs_fonts.js', ['pdfmake_fonts']);

		wp_enqueue_script('htmltopdfmake', plugin_dir_url(__FILE__).'../node_modules/html-to-pdfmake/browser.js', ['pdfmake']);
		wp_enqueue_script('canvas2svg', plugin_dir_url(__FILE__).'../js/canvas2svg.js');

//		wp_enqueue_script('jquery-ui-droppable');

		wp_enqueue_script('ccpcm_templates_functions', plugin_dir_url(__FILE__).'../js/ccpcm_templates_functions.js'); //, ['pdfmake_extra']);
		wp_enqueue_script('ccpcm_integrator', plugin_dir_url(__FILE__).'../js/ccpcm_integrator.js', ['ccpcm_templates_functions']);
		wp_enqueue_script('ccpcm_ajax', plugin_dir_url(__FILE__).'../js/ccpcm_ajax.js');

  //  wp_enqueue_script('ccpcm_integrator_functions_tmp', plugin_dir_url(__FILE__).'../js/ccpcm_integrator_functions_tmp.js', ['ccpcm_integrator']);
  
	}

	function run_ajax($method, $data = []) {
		switch($method) {
			case 'get_templates':
				return $this->templates->get_templates();
				break;
			case 'get_templates_with_infos':
				return $this->templates->get_templates_with_infos();
				break;
			case 'get_template':
				$id = $data['id'];
				if (array_key_exists('dpi', $data)) {
					$dpi = $data['dpi'];
					$this->data->redefine($dpi);
				}
				if (array_key_exists('options', $data))
					$options = $data['options'];
				else
					$options = [];
				return $this->templates->get_template($id, $options);
				break;
			case 'delete_template':
				$id = $data['id'];
				return $this->templates->delete_template($id);
				break;
			case 'set_template':
				$id = $data['id'];
				$t_data = $data['data'];
				if (array_key_exists('new', $data))
					$new = True;
				else
					$new = False;
				return $this->templates->set_template($id, $t_data, $new);
				break;

			case 'get_catalogues':
				return $this->catalogues->get_catalogues();
				break;
			case 'get_catalogue_masters':
				return $this->catalogues->get_catalogue_masters();
				break;
			case 'get_catalogue':
				$id = $data['id'];
				if (array_key_exists('options', $data))
					$options = $data['options'];
				else
					$options = [];
				return $this->catalogues->get_catalogue($id, $options);
				break;
			case 'delete_catalogue':
				$id = $data['id'];
				return $this->catalogues->delete_catalogue($id);
				break;
			case 'set_catalogue':
				$id = $data['id'];
				if (array_key_exists('data', $data))
					$t_data = $data['data'];
				else
					$t_data = [];
				if (array_key_exists('new', $data))
					$new = True;
				else
					$new = False;
				return $this->catalogues->set_catalogue($id, $t_data, $new);
				break;
			case 'get_catalogue_templates':
				$type = $data['type'];
				return $this->catalogues->get_catalogue_templates($type);
				break;
			case 'get_catalogue_element_data':
				$type = $data['type'];
				return $this->catalogues->get_catalogue_element_data($type);
				break;
			case 'get_catalogue_content':
				$id = $data['id'];
				$dpi = $data['dpi'];
				return $this->catalogues->get_catalogue_content($id, $dpi);
				break;
			case 'get_integrator_content':
				$template = $data['template'];
				if (array_key_exists('data', $data))
					$inputs = $data['data'];
				else
					$inputs = [];
				$dpi = $data['dpi'];
				$this->data->redefine($dpi);
				$template = str_replace('-', '_', $template);
				return $this->integrator->$template($inputs, $dpi);
				break;
			case 'get_catalogue_data_by_type_and_id':
				$template_id = $data['template_id'];
				if (array_key_exists('ids', $data))
					$ids = $data['ids'];
				else
					$ids = False;
				$type = $data['type'];
				if (!array_key_exists('order', $data))
					$order = 'order';
				else
					$order = $data['order'];
				$infos = [];
				if (array_key_exists('display_by', $data))
					$infos['display_by'] = $data['display_by'];;
				$dpi = $data['dpi'];
				$this->data->redefine($dpi);
				return $this->catalogues->get_catalogue_data_by_type_and_id($template_id, $type, $ids, $order, $infos);
			case 'get_file':
				if (array_key_exists('file', $data))
					$file = $data['file'];
				else
					return False;
				$meta = $data['meta'];
				$url = $data['url'];
				$dpi = $data['dpi'];
				$file = str_replace('.', '', $file);
				$file = str_replace('/', '', $file);
				$this->log("DPI ".$dpi);
				$this->data->redefine($dpi);
				return ['base64' => $this->data->store_file_get_final($dpi, $file, $meta, $url)];
				break;
			case 'set_dpi':
				if (array_key_exists('dpi', $data))
					$dpi = $data['dpi'];
				else
					return False;
				setcookie("dpi_selector", $dpi, time()+3600*30, '/');
				return True;
				
		}
	}
}

class ccpcm_object {
	protected $ccpcm = Null;
	public function __construct($ccpcm) {
		$this->ccpcm = $ccpcm;
	}
}

?>
