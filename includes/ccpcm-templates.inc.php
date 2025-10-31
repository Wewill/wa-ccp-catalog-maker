<?php
// @ini_set( 'display_errors', 1 );

if (file_exists(__DIR__.'/../custom/ccpcm-templates-'.CCPCM_PROJECT.'.inc.php')) {
	require_once(__DIR__.'/../custom/ccpcm-templates-'.CCPCM_PROJECT.'.inc.php');
} else {
	class ccpcm_templates_custom extends ccpcm_object {

	}
}

class ccpcm_templates extends ccpcm_templates_custom {
  public $indexes = array(
		'templates' => array(),
    'catalogues' => array(),
	);

  public $db_path;
  public $jsondb;

  public $quick_names = array();

  public function __construct($ccpcm) {
    parent::__construct($ccpcm);
		$edition_slug = $this->ccpcm->edition_slug;
		require_once('jsondb.inc.php');
		$this->db_path = sprintf("%s/%s/", CCPCM_RELATIVE_TEMPLATES_PATH."/templates/", $edition_slug);
		if (!is_dir(__DIR__.'/'.$this->db_path))
			if (!mkdir(__DIR__.'/'.$this->db_path, 0755, True))
				printf("Can't create %s<br/>", __DIR__.'/'.$this->db_path);
		$this->jsondb = new jsondb($this->db_path, $this->indexes, $this->quick_names);
	}

	public function display() {
    print ('<script>var edition_slug = "'.$this->ccpcm->edition_slug.'"; var edition_id = "'.$this->ccpcm->edition_id.'";</script>');
		wp_enqueue_style('ccpcm_template', plugin_dir_url(__FILE__).'../css/ccpcm_templates.css');
    wp_enqueue_style('ccpcm_fonts', plugin_dir_url(__FILE__).'../fonts/fonts.css');
//    wp_enqueue_style('ccpcm_fonts2', plugin_dir_url(__FILE__).'../webfontkit-20191007-102815/stylesheet.css');

    wp_enqueue_script('pdfmake_before', plugin_dir_url(__FILE__).'../js/pdfmake_before.js');
		wp_enqueue_script('pdfmake', plugin_dir_url(__FILE__).'../bower_components/pdfmake/build/pdfmake.js', ['pdfmake_before']);
    wp_enqueue_script('pdfmake_after', plugin_dir_url(__FILE__).'../js/pdfmake_after.js', ['pdfmake']);
    wp_enqueue_script('codemirror', plugin_dir_url(__FILE__).'../bower_components/codemirror/lib/codemirror.js');
//    wp_enqueue_script('codemirror_dialog', plugin_dir_url(__FILE__).'../bower_components/codemirror/addon/dialog/dialog.js');
    wp_enqueue_script('codemirror_foldcode', plugin_dir_url(__FILE__).'../bower_components/codemirror/addon/fold/foldcode.js');
    wp_enqueue_script('codemirror_foldgutter', plugin_dir_url(__FILE__).'../bower_components/codemirror/addon/fold/foldgutter.js');
    wp_enqueue_script('codemirror_searchcursor', plugin_dir_url(__FILE__).'../bower_components/codemirror/addon/search/searchcursor.js');
    wp_enqueue_script('codemirror_search', plugin_dir_url(__FILE__).'../bower_components/codemirror/addon/search/search.js');
    wp_enqueue_script('codemirror_annotatescrollbar', plugin_dir_url(__FILE__).'../bower_components/codemirror/addon/scroll/annotatescrollbar.js');
    wp_enqueue_script('codemirror_matchesonscrollbar', plugin_dir_url(__FILE__).'../bower_components/codemirror/addon/search/matchesonscrollbar.js');
    wp_enqueue_script('codemirror_searchjump', plugin_dir_url(__FILE__).'../bower_components/codemirror/addon/search/jump-to-line.js');
    wp_enqueue_script('codemirror_js', plugin_dir_url(__FILE__).'../bower_components/codemirror/mode/javascript/javascript.js');

    wp_enqueue_style('codemirror', plugin_dir_url(__FILE__).'../bower_components/codemirror/lib/codemirror.css');
    wp_enqueue_style('codemirror_matchesonscrollbar_css', plugin_dir_url(__FILE__).'../bower_components/codemirror/addon/search/matchesonscrollbar.css');
    wp_enqueue_style('codemirror_foldglutter_css', plugin_dir_url(__FILE__).'../bower_components/codemirror/addon/fold/foldgutter.css');

    wp_enqueue_script('pdfmake_fonts', plugin_dir_url(__FILE__).'../custom/'.CCPCM_PROJECT.'/js/pdfmake_fonts.js', ['pdfmake']);
    wp_enqueue_script('pdfmake_fonts_files', plugin_dir_url(__FILE__).'../custom/'.CCPCM_PROJECT.'/js/vfs_fonts.js', ['pdfmake_fonts']);

    wp_enqueue_script('canvas2svg', plugin_dir_url(__FILE__).'../js/canvas2svg.js');

    wp_enqueue_script('htmltopdfmake', plugin_dir_url(__FILE__).'../node_modules/html-to-pdfmake/browser.js', ['pdfmake']);

		wp_enqueue_script('jquery-ui-droppable');
    wp_enqueue_script('ccpcm_templates_functions', plugin_dir_url(__FILE__).'../js/ccpcm_templates_functions.js');
		wp_enqueue_script('ccpcm_templates', plugin_dir_url(__FILE__).'../js/ccpcm_templates.js', ['ccpcm_templates_functions']);
    wp_enqueue_script('ccpcm_templates_functions_tmp', plugin_dir_url(__FILE__).'../js/ccpcm_templates_functions_tmp.js', ['ccpcm_templates']);
    print('<div id="ccpcm_templates_container">');
    $this->display_top();
		$this->display_left();
		$this->display_right();
		print('</div>');
    $this->display_help();
	}

  public function display_top() {
    print('<div id="ccpcm_templates_top">');
    print('<div id="ccpcm_templates_top_left"><select id="ccpcm_templates_select"></select>');
    print('<select id="ccpcm_templates_type">');
    $types = $this->ccpcm->catalogues->types;
    $types = array_merge(['master'=>'Master', 'styles'=>'Styles', 'javascript'=>'Javascript'], $types);
    foreach($types as $type => $desc)
      printf('<option value="%s">%s</option>', $type, $desc);
    print('</select>');
    print('<input type="text" placeholder="id,id,.." id="ccpcm_templates_elements_ids">');
    print('<select id="ccpcm_templates_elements_count">');
    foreach([
        0 => 'Multiples, unknown',
        1 => 'Uniq (![])',
        2 => 2,
        3 => 3,
        4 => 4,
        8 => 8,
        12 => 12
      ] as $k => $v) {
      printf('<option value="%s">%s</option>', $k, $v);
    }
    print('</select>');
    print('</div>');
    print('<div id="ccpcm_templates_top_right">');
    print('<a id="ccpcm_template_btn_new" class="button button-primary">New template</a>&nbsp;<span id="ccpcm_template_new"><input id="ccpcm_template_new_name" value="noname">&nbsp;<a id="ccpcm_template_btn_new_append" class="button button-primary">Create</a>&nbsp;<a id="ccpcm_template_btn_new_cancel" class="button button-secondary">Cancel</a></span>');
    print('&nbsp;<a id="ccpcm_template_btn_save" class="button button-primary">Save</a>');
    print('&nbsp;<a id="ccpcm_template_btn_delete" class="button button-secondary">Delete</a>');
    $this->ccpcm->display->dpi_selector();
    print('&nbsp;<a id="ccpcm_template_btn_download" class="button button-primary">Download</a>');
    print('<select id="ccpcm_templates_master_select_render"></select>');
    print('&nbsp;<input type="checkbox" value="1" id="ccpcm_template_add_blank_page" title="add first blank page" alt="add first blank page">');
    print('&nbsp;<input type="checkbox" value="1" id="ccpcm_template_btn_auto_render" checked="checked" title="autorender" alt="autorender">');
    print('&nbsp;<a id="ccpcm_template_btn_render" class="button button-secondary">Render</a>');
    print('</div>');
    print('</div>');
  }

	public function display_left(){
		print ('<div id="ccpcm_template_container_left">');
    print ('<textarea name="ccpcm_template_content" id="ccpcm_template_content"></textarea>');
#@ace    print ('<div name="ccpcm_template_content" id="ccpcm_template_content">');
    print ('</div>');
		print ('</div>');
	}

	public function display_right() {
		print('<iframe id="ccpcm_template_container_right"></iframe>');
	}

  public function display_help() {
?>
<h2>Function javascript</h2>
<ul>
  <li>display_content() : depuis un master, affichage des contenus défini dans le catalogue maker</li>
  <li>display_template(template_name, data) : template_name : nom du template - data : variable générique qui sera remplacée par un film, ou autre</li>
  <li>display_image(image, meta) - meta = {'width':xx(, 'height': yy)}</li>
</ul>
<?php
  }

  public function get_templates() {
    $templates = $this->jsondb->get_ids('templates');
    asort($templates);
    $templates = array_values($templates);
    return $templates;
  }

  public function get_templates_with_infos() {
    $templates = $this->jsondb->get_ids('templates');
    asort($templates);
    $templates = array_values($templates);
    $templates_with_infos = [];
    foreach($templates as $id) {
      $data = $this->jsondb->get('templates', $id);
      $templates_with_infos[] = [
        'id' => $id,
        'type' => (array_key_exists('type', $data))?$data['type']:'',
        'elements_count' => (array_key_exists('elements_count', $data))?$data['elements_count']:0,
      ];
      unset($data);
    }
    return $templates_with_infos;
  }

  public function get_template($id, $options = []) {
    $data = array();
    $data['template'] = $this->jsondb->get('templates', $id);
    $elements_count = (array_key_exists('elements_count', $data['template']))?$data['template']['elements_count']:0;
    $elements_ids = (array_key_exists('elements_ids', $data['template']))?array_filter(explode(',', $data['template']['elements_ids'])):[];
    if (array_key_exists('no_data', $options) and $options['no_data'])
      return $data;
    if (array_key_exists('type', $data['template']))
      $data['data'] = $this->get_random_data($data['template']['type'], $elements_count, $elements_ids);
    else
      $data['data'] = ['nodata'];
    return $data;
  }

  public function set_template($id, $data, $new) {
    if ($new) {
      $t_data = $this->jsondb->get('templates', $id);
      if ($t_data) {
        $t_data['id'] = $id;
        return $t_data;
      } else {
        $this->jsondb->append('templates', $id, $data);
        $data['id'] = $id;
        return $data;
      }
    } else {
      $this->jsondb->append('templates', $id, $data);
      $data['id'] = $id;
      if (array_key_exists('type', $data))
        $data['data'] = $this->get_random_data($data['type'], $data['elements_count'], array_filter(explode(',', $data['elements_ids'])));
      else
        $data['data'] = ['nodata'];
      return $data;
    }
  }

  public function delete_template($id) {
    $this->jsondb->delete('templates', $id);
  }
}
