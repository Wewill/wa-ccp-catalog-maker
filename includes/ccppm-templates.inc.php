<?php

class ccppm_templates extends ccppm_object {
  public $indexes = array(
		'templates' => array(),
    'catalogues' => array(),
	);

  public $quick_names = array();

  public function __construct($ccppm) {
    parent::__construct($ccppm);
		$edition_slug = $this->ccppm->edition_slug;
		require_once('jsondb.inc.php');
		$this->db_path = sprintf("%s/%s/%s/", dirname(__FILE__), "../templates/", $edition_slug);
		if (!is_dir($this->db_path))
			if (!mkdir($this->db_path, 0755, True))
				printf("Can't create %s<br/>", $this->db_path);
		$this->jsondb = new jsondb($this->db_path, $this->indexes, $this->quick_names);
	}

	public function display() {
    print ('<script>var edition_slug = "'.$this->ccppm->edition_slug.'"; var edition_id = "'.$this->ccppm->edition_id.'";</script>');
		wp_enqueue_style('ccppm_template', plugin_dir_url(__FILE__).'../css/ccppm_templates.css');
    wp_enqueue_style('ccppm_fonts', plugin_dir_url(__FILE__).'../fonts/fonts.css');
//    wp_enqueue_style('ccppm_fonts2', plugin_dir_url(__FILE__).'../webfontkit-20191007-102815/stylesheet.css');

    wp_enqueue_script('pdfmake_before', plugin_dir_url(__FILE__).'../js/pdfmake_before.js');
		wp_enqueue_script('pdfmake', plugin_dir_url(__FILE__).'../bower_components/pdfmake/build/pdfmake.js', ['pdfmake_before']);
    wp_enqueue_script('pdfmake_after', plugin_dir_url(__FILE__).'../js/pdfmake_after.js', ['pdfmake']);
    wp_enqueue_script('codemirror', plugin_dir_url(__FILE__).'../bower_components/codemirror/lib/codemirror.js');
//    wp_enqueue_script('codemirror_dialog', plugin_dir_url(__FILE__).'../bower_components/codemirror/addon/dialog/dialog.js');
    wp_enqueue_script('codemirror_searchcursor', plugin_dir_url(__FILE__).'../bower_components/codemirror/addon/search/searchcursor.js');
    wp_enqueue_script('codemirror_search', plugin_dir_url(__FILE__).'../bower_components/codemirror/addon/search/search.js');
    wp_enqueue_script('codemirror_annotatescrollbar', plugin_dir_url(__FILE__).'../bower_components/codemirror/addon/scroll/annotatescrollbar.js');
    wp_enqueue_script('codemirror_matchesonscrollbar', plugin_dir_url(__FILE__).'../bower_components/codemirror/addon/search/matchesonscrollbar.js');
    wp_enqueue_script('codemirror_searchjump', plugin_dir_url(__FILE__).'../bower_components/codemirror/addon/search/jump-to-line.js');
    wp_enqueue_script('codemirror_js', plugin_dir_url(__FILE__).'../bower_components/codemirror/mode/javascript/javascript.js');

    wp_enqueue_style('codemirror', plugin_dir_url(__FILE__).'../bower_components/codemirror/lib/codemirror.css');
    wp_enqueue_style('codemirror_matchesonscrollbar_css', plugin_dir_url(__FILE__).'../bower_components/codemirror/addon/search/matchesonscrollbar.css');

//    wp_enqueue_script('pdfmake_fonts', plugin_dir_url(__FILE__).'../bower_components/pdfmake/build/vfs_fonts.js');
    wp_enqueue_script('pdfmake_fonts', plugin_dir_url(__FILE__).'../js/pdfmake_fonts.js', ['pdfmake']);
    wp_enqueue_script('pdfmake_fonts_files', plugin_dir_url(__FILE__).'../js/vfs_fonts.js', ['pdfmake_fonts']);

    wp_enqueue_script('canvas2svg', plugin_dir_url(__FILE__).'../js/canvas2svg.js');

    wp_enqueue_script('htmltopdfmake', plugin_dir_url(__FILE__).'../node_modules/html-to-pdfmake/browser.js', ['pdfmake']);

		wp_enqueue_script('jquery-ui-droppable');
    wp_enqueue_script('ccppm_templates_functions', plugin_dir_url(__FILE__).'../js/ccppm_templates_functions.js');
		wp_enqueue_script('ccppm_templates', plugin_dir_url(__FILE__).'../js/ccppm_templates.js', ['ccppm_templates_functions']);
    wp_enqueue_script('ccppm_templates_functions_tmp', plugin_dir_url(__FILE__).'../js/ccppm_templates_functions_tmp.js', ['ccppm_templates']);
    print('<div id="ccppm_templates_container">');
    $this->display_top();
		$this->display_left();
		$this->display_right();
		print('</div>');
    $this->display_help();
	}

  public function display_top() {
    print('<div id="ccppm_templates_top">');
    print('<div id="ccppm_templates_top_left"><select id="ccppm_templates_select"></select>');
    print('<select id="ccppm_templates_type">');
    $types = $this->ccppm->catalogues->types;
    $types = array_merge(['master'=>'Master', 'styles'=>'Styles', 'javascript'=>'Javascript'], $types);
    foreach($types as $type => $desc)
      printf('<option value="%s">%s</option>', $type, $desc);
    print('</select>');
    print('<input type="text" placeholder="id,id,.." id="ccppm_templates_elements_ids">');
    print('<select id="ccppm_templates_elements_count">');
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
    print('<div id="ccppm_templates_top_right">');
    print('<a id="ccppm_template_btn_new" class="button button-primary">New template</a>&nbsp;<span id="ccppm_template_new"><input id="ccppm_template_new_name" value="noname">&nbsp;<a id="ccppm_template_btn_new_append" class="button button-primary">Create</a>&nbsp;<a id="ccppm_template_btn_new_cancel" class="button button-secondary">Cancel</a></span>');
    print('&nbsp;<a id="ccppm_template_btn_save" class="button button-primary">Save</a>');
    print('&nbsp;<a id="ccppm_template_btn_delete" class="button button-secondary">Delete</a>');
    $this->ccppm->display->dpi_selector();
    print('&nbsp;<a id="ccppm_template_btn_download" class="button button-primary">Download</a>');
    print('<select id="ccppm_templates_master_select_render"></select>');
    print('&nbsp;<input type="checkbox" value="1" id="ccppm_template_add_blank_page" title="add first blank page" alt="add first blank page">');
    print('&nbsp;<input type="checkbox" value="1" id="ccppm_template_btn_auto_render" checked="checked" title="autorender" alt="autorender">');
    print('&nbsp;<a id="ccppm_template_btn_render" class="button button-secondary">Render</a>');
    print('</div>');
    print('</div>');
  }

	public function display_left(){
		print ('<div id="ccppm_template_container_left">');
    print ('<textarea name="ccppm_template_content" id="ccppm_template_content"></textarea>');
#@ace    print ('<div name="ccppm_template_content" id="ccppm_template_content">');
    print ('</div>');
		print ('</div>');
	}

	public function display_right() {
		print('<iframe id="ccppm_template_container_right"></iframe>');
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

  public function get_random_data($type, $count=0, $ids=[]) {
    $data = array();
    switch ($type) {
      case 'index':
        $section_ids = $this->ccppm->data->jsondb->get_ids('section');
        $sections = [];
        foreach($section_ids as $id) {
          $this->ccppm->data->jsondb->get('section', $id);
          if ($id != 220)
            $sections[] = $this->ccppm->data->jsondb->get('section', $id);
        }
        usort($sections, [$this->ccppm->catalogues, 'get_catalogue_data_order_by_order']);
        return ['sections'=>$sections]; 
        break;
      default:
        if (!count($ids)) {
          switch($type) {
            case '2sections':
              $type = 'section';
              $count = 2;
              $ids = $this->ccppm->data->jsondb->get_ids($type);
//              $ids = array(221,222);
              break;
            case 'section':
              //$ids = array(231); // Compet LM #40
//              $ids = array(257); // Compet CM #40
              //$ids = array(230); // Compet #40
              $ids = $this->ccppm->data->jsondb->get_ids($type);
              break;
              // Wil
            case 'jury':
//              $ids = array(51969);
              $ids = $this->ccppm->data->jsondb->get_ids($type);
              break;
            case 'section_and_subsections':
              //$id = 237; // Ouverture cloture #40
              //$id = 233; // Compet CM #40
              $id = 257; // Compet #40
              return $this->ccppm->catalogues->get_catalogue_data_by_type_and_id(False, $type, [$id], 'name_simplified', [], 1); //182 afrique /193 espagne / 210 UPJV // 192 Competition // 211 MASTERCLASS // 178 Competition CM // 181 Travaie
              break;
            case 'film':
              //$ids = array(52502); // MD > pour test >> Markdown KO
              //$ids = array(52085); // MD Bio > Markdown OK 
              //$ids = array(51616); // MD Bio + punchline > Markdown OK 
              //$ids = array(52611); // justin pour images
              //$ids = array(52400); // Cas sepcial ouvertre
//              $ids = array(52000); // Film compet Kuessipan
              //$ids = array(50984); // Film pr calage des bordered
              $ids = $this->ccppm->data->jsondb->get_ids($type);              
            break;
            case 'planning':
              $ids = ['planning'];
              break;
            default:
              $ids = $this->ccppm->data->jsondb->get_ids($type);
              break;
          }
        } else {
          switch($type) {
            case 'section_and_subsections':
              //$id = 237; // Ouverture cloture #40
              //$id = 233; // Compet CM #40
              $id = $ids[0]; // Compet #40
              return $this->ccppm->catalogues->get_catalogue_data_by_type_and_id(False, $type, [$id], 'name_simplified', [], 1); //182 afrique /193 espagne / 210 UPJV // 192 Competition // 211 MASTERCLASS // 178 Competition CM // 181 Travaie
              break;
          }          
        }
    }
//    $this->ccppm->data->redefine(150);
//    if ($type == 'section_and_subsections')
//      return $this->ccppm->catalogues->get_catalogue_data_by_type_and_id(False, $type, 210, 'name_simplified', 1); //182 afrique /193 espagne / 210 UPJV // 192 Competition // 211 MASTERCLASS // 178 Competition CM // 181 Travaie
//    if ($type == 'film')
//         $ids = array(43860); // Bug projections
//        $ids = array(44092); // carte #191
//        $ids = array(44366); // Bug En presence de en 2 cols > a été supprimé
//        $ids = array(38788); // Bug Prmiere mondiale  en 2 cols
//        $ids = array(43367); //Arguments > inf. à 1350 signes ? Passe à la ligen, pourquoi ?
//        $ids = array(43437); //YENTl > passe en 2
//         $ids = array(43364); //Balangiga > tres bien our test
 //         $ids = array(43565); //Dame camelia Bug en presence de
 //         $ids = array(43794); //Pasaran Bug en presence de
 //         $ids = array(43925); //ug en presence de
//        $ids = array(43308);
      //  $ids = array(42948); //Les Orphelins de Sankara
//        $ids=array(44804); // LAS HURDES
//      $ids=array(43389); // Compet + test override catalog + premiere + tag
//      $ids=array(43576); // Test notes
//      $ids=array(44294); // Markdown RED IS DEAD
//      $ids=array(44804); // Markdown LAS HURDES
//      $ids=array(43840); // Dernier loup 3D
//      $ids=array(43570); // Without dialogue

// A tester wil
//      $ids=array(44431); // Programme > on cache la fiche technique   + soucis des accents
//  	$ids=array(38788); // Premiere mondiale
// 		$ids=array(44279); // Apres couleur pas de FORMAT + Apres avec • en trop
// 		$ids=array(44329); // Soucis pas de date + souci fiche tehcnique

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
        # @todo: justin code à supprimer pour arreter de voir tjrs le meme film
        #if ($type == 'film')
        #  $id = 26585;
        $d = $this->ccppm->data->jsondb->get($type, $id);
        switch($type) {
	        case '2sections':
          case 'section':
            $index = $this->ccppm->data->jsondb->get_index('film', 'section');
            $films = array();
            if (array_key_exists($id, $index)) {
              $f_ids = $index[$id];
              foreach($f_ids as $f_id) {
                $film = $this->ccppm->data->jsondb->get('film', $f_id);
                $this->ccppm->custom->append_film_additionnal_fields($film);
                $films[] = $film;
              }
            }
            $d['films'] = $films;
	    unset($films);
            $order = 'order';
	    if (array_key_exists('ccppto_film', $d) && array_key_exists('films', $d))
		    $d['films'] = $this->ccppm->custom->apply_post_in_taxonomy_order($d['ccppto_film'], $d['films']);
	    else
		    $d['films'] = [];
	    if (array_key_exists('ccppto_projection', $d) && array_key_exists('projections', $d))
		    $d['projections'] = $this->ccppm->custom->apply_post_in_taxonomy_order($d['ccppto_projection'], $d['projections']);
	    else
 		    $d['projections'] = [];
            $d['films'] = $this->ccppm->catalogues->get_catalogue_data_order_by($d['films'], $order);
            $d['projections'] = $this->ccppm->catalogues->get_catalogue_data_order_by($d['projections'], $order);
            if (array_key_exists('featured_film_1', $d) && $d['featured_film_1']) {
              $d['featured_film_1'] = $this->ccppm->data->jsondb->get('film', $d['featured_film_1']);
            } else {
              $d['featured_film_1'] = False;
            }
            if (array_key_exists('featured_film_2', $d) && $d['featured_film_2']) {
              $d['featured_film_2'] = $this->ccppm->data->jsondb->get('film', $d['featured_film_2']);
            } else {
              $d['featured_film_2'] = False;
            }
            break;
          case 'partenaire-category':
            $index = $this->ccppm->data->jsondb->get_index('partenaire', 'partenaire-category');
            $partenaire = [];
            if (array_key_exists($id, $index)) {
              $c_ids = $index[$id];
              foreach($c_ids as $c_id) {
                $partenaire[] = $this->ccppm->data->jsondb->get('partenaire', $c_id);
              }
            }
//            $d['partners'] = $partenaire;
            $order = 'order';
            $d['partners'] = $this->ccppm->catalogues->get_catalogue_data_order_by($partenaire, $order);

            break;
          case 'movie-type':
            $index = $this->ccppm->data->jsondb->get_index('jury', 'movie-type');
            $jurys = array();
            if (array_key_exists($id, $index)) {
              $j_ids = $index[$id];
              foreach($j_ids as $j_id)
                $jurys[] = $this->ccppm->data->jsondb->get('jury', $j_id);
            }
            $d['jurys'] = $jurys;
            break;
          case 'film':
            $this->ccppm->custom->append_film_additionnal_fields($d);
            break;

/*
            $index = $this->ccppm->data->jsondb->get_index('projection', 'film');
            $projections = array();
            if (array_key_exists($id, $index)) {
              $p_ids = $index[$id];
              foreach($p_ids as $p_id) {
                $projection = $this->ccppm->data->jsondb->get('projection', $p_id);
                foreach($projection['room'] as $r_idx => $room) {
                  if ($room['parent']) {
                    $projection['room'][$r_idx]['cinema'] = $this->ccppm->data->jsondb->get('room', $room['parent']);
                  }
                }
                $projection['room'] = $projection['room'][0];
                $projections[] = $projection;
              }
            }
            $d['projections'] = $projections;
            */

        }
        $data[] = $d;
      }
    }
//    $this->ccppm->log("get_random_data => ", print_r($data, True));
    if ($count == 1 && count($data) == 1) {
      return $data[0];
    }
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
