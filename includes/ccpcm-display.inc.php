<?php

class ccpcm_display extends ccpcm_object {
	function init_menu() {
		add_action('admin_menu', [$this, 'setup_menu']);
	}

	function setup_menu() {
		wp_enqueue_script('ccpcm_ajax', plugin_dir_url(__FILE__).'../js/ccpcm_ajax.js');
		add_menu_page('Catalogue Maker', 'Catalogue Maker', 'manage_options', 'ccpcm', [$this, 'catalogue'], 'dashicons-book', 200);
		add_submenu_page( 'ccpcm', 'Templates', 'Templates', 'manage_options', 'ccpcm-templates', [$this, 'templates']);
		add_submenu_page( 'ccpcm', 'Verify', 'Verify', 'manage_options', 'ccpcm-verify', [$this, 'verify']);
#		add_submenu_page( 'ccpcm', 'Settings', 'Settings', 'manage_options', 'ccpcm-settings', [$this, 'settings']);
		add_submenu_page( 'ccpcm', 'Generate data', 'Generate data', 'manage_options', 'ccpcm-generate-data', [$this, 'generate_data']);
		add_submenu_page( 'ccpcm', 'Exports', 'Exports', 'manage_options', 'ccpcm-export-data', [$this, 'export_data']);
	}

	function catalogue(){
	      echo "<h1>Catalogue</h1>";
				$this->ccpcm->catalogues->display();
	}

	function templates(){
	      echo "<h1>Templates</h1>";
				$this->ccpcm->templates->display();
	}


	function dpi_selector() {
		$dpis = [9,72,150,300];
		$current_dpi = 9;
		if (array_key_exists('dpi_selector', $_COOKIE))
			$current_dpi = $_COOKIE['dpi_selector'];
		print ('<select id="dpi_selector">');
		printf('<option value="">DPI</option>');
		foreach($dpis as $dpi) {
			printf('<option value="%s" %s>%s</option>', $dpi, ($current_dpi == $dpi)?"SELECTED=\"SELECTED\"":"", $dpi);
		}
		print ('</select>');
?>
<script>
var dpi_selector = jQuery('#dpi_selector');
dpi_selector.on('change', function() {
    templates_caches = {};
    catalogue_data_caches = {};
    ccpcm_ajax('set_dpi', {'dpi': dpi_selector.val()}, false, false);
});
</script>
<?php
	}

	function verify(){
		wp_enqueue_style('ccpcm_data', plugin_dir_url(__FILE__).'../css/ccpcm_data.css');

		echo "<h1>Verify</h1>";
		echo "<div class='ccpcm_data_info'><span class='title'>Verifier ici la cohérence de vos données.</span> Verify indique les champs manquants obligatoires, necessaires.</span></div>";
		$this->ccpcm->data->verify();
	}

	function generate_data() {
		wp_enqueue_style('ccpcm_data', plugin_dir_url(__FILE__).'../css/ccpcm_data.css');
		echo "<h1>Generate data</h1>";
		echo "<div class='ccpcm_data_info'><span class='title'>Générer les données afin de fournir les catalogues et templates.</span> A chaque modification du contenu ; il est nécessaire de lancer un generate data. Il est conseillé de travailler en 9dpi ( le plus rapide, les images sont de basse qualité ). La version 72dpi est adaptée pour le téléchargement de la brochure. La version 150dpi est adaptée pour une brochure web à la qualité optimale. La version 300dpi est destinée à l'impression.</span></div>";
		echo "<div class='ccpcm_data_notice'><span class='title'>Même s'il y a une erreur 'time-out' 504, le serveur continue de calculer.</span> Il ne sert à rien de le relancer, cela peut être long et le proxy ne gere pas de tels temps de calcul.</span></div>";
		echo "<p></p>";
		$dpis = array(
			9,
			72,
			150,
			300
		);
		if ($_GET['emptyallcache']) {
			foreach($dpis as $dpi) {
				$this->ccpcm->data->redefine($dpi);
				$this->ccpcm->data->jsondb->remove_all();
			}
			
		}

		if ($_GET['emptyallcachestorage']) {
			foreach($dpis as $dpi) {
				$this->ccpcm->data->redefine($dpi);
				$this->ccpcm->data->jsondb->remove_all(__DIR__.'/'.$this->ccpcm->data->storage_path);
				die('9dpi only for moment');
			}
		}


		if ($_GET['force_recrop'])
			$this->ccpcm->data->force_recrop = True;

		if ($_GET['dpi']) {
			if (in_array($_GET['dpi'], $dpis)) {
				$this->ccpcm->log('[ GENERATE ][ '.$_GET['dpi'].' ] Data Start #'.$this->ccpcm->data->force_recrop);
				$this->ccpcm->data->redefine($_GET['dpi']);
				$this->ccpcm->data->jsondb->remove_all();
				$this->ccpcm->data->update();
				$this->ccpcm->log('[ GENERATE ][ '.$_GET['dpi'].' ] Data End #'.$this->ccpcm->data->force_recrop);
			}
		}
		if ($_GET['generate'] == 'planning') {
			$this->ccpcm->log('[ GENERATE ][ 9 ] Planning Start');
			$this->ccpcm->data->redefine(9);
			$this->ccpcm->data->update_planning(False, False);
			$this->ccpcm->log('[ GENERATE ][ 9 ] Planning End');
		}
		if ($_GET['generate'] == 'planning-section') {
			$this->ccpcm->log('[ GENERATE ][ 9 ] Planning Section Start');
			$this->ccpcm->data->redefine(9);
			$this->ccpcm->data->update_sections_planning();
			$this->ccpcm->log('[ GENERATE ][ 9 ] Planning Section End');
		}
		if ($_GET['generate'] == 'projection') {
			$this->ccpcm->log('[ GENERATE ][ 9 ] Projections Start');
			$this->ccpcm->data->redefine(9);
			$this->ccpcm->data->update_projection(False, False);
			$this->ccpcm->log('[ GENERATE ][ 9 ] Projections End');
		}
		$dpis = array(9, 72, 150, 300);
		echo "<div id='ccpcm_data_container'>";
		printf("<h2>(Re)générer les données et les images <em>manquantes</em></h2>");
		foreach($dpis as $dpi) {
			printf ('<a class="button button-primary" href="?page=ccpcm-generate-data&dpi=%s">Generate at %s dpi%s</a> ', $dpi, $dpi, ($dpi == 9)?" (DEFAULT)":"");
		}
		echo "</div>";
		echo "<div id='ccpcm_data_container'>";
		printf("<h2>(Re)générer les données et les images <em>forcé</em></h2>");
		echo "<p><strong>Cette méthode est plus longue,</strong> elle permet notamment de re-générer une image qui a été modifiée ou recadrée qui resterait persistant</p>";
		foreach($dpis as $dpi) {
			printf ('<a class="button button-primary" href="?page=ccpcm-generate-data&dpi=%s&force_recrop=1">Generate at %s dpi%s</a> ', $dpi, $dpi, ($dpi == 9)?" (DEFAULT)":"");
		}
		echo "</div>";
		echo "<div id='ccpcm_data_container'>";
		if (IS_PLANNING) {
			printf("<h2>Génération du planning</h2>");
			printf ('<a class="button button-primary" href="?page=ccpcm-generate-data&generate=planning">(Re)générer le planning</a> ');
			printf ('<a class="button button-primary" href="?page=ccpcm-generate-data&generate=planning-section">(Re)générer les sections du planning (DEV)</a> ');
			printf ('<a class="button button-primary" href="?page=ccpcm-generate-data&generate=projection">(Re)générer les projections (DEV)</a> ');
		}
		printf("<h2>Vider le cache</h2>");
		echo "<p><strong>En cas de soucis,</strong> cette fonction permet de vider l'ensmeble des données et images et de repartir de zéro.</p>";
		printf ('<a class="button button-primary" href="?page=ccpcm-generate-data&emptyallcache=true">Vider le cache données</a> ');
		printf ('<a class="button button-primary" href="?page=ccpcm-generate-data&emptyallcachestorage=true">Vider le cache images</a> ');
		echo "</div>";
	}

	function export_data() {
		global $ccp_editions_filter;
		$comparators = [
			'==' => [
				'name'=>'Egal',
				'types'=>['boolean', 'integer', 'decimal', 'string', 'date', 'datetime', 'select', 'select_fr'],
			],
			'!=' => [
				'name'=>'Different',
				'types'=>['integer', 'decimal', 'string', 'date', 'datetime', 'select', 'select_fr'],
			],
			'<' => [
				'name'=>'Inférieur',
				'types'=>['integer', 'decimal', 'string'],
			],
			'<=' => [
				'name'=>'Inférieur ou égal',
				'types'=>['integer', 'decimal', 'string'],
			],
			'>' => [
				'name'=>'Supérieur',
				'types'=>['integer', 'decimal', 'string'],
			],
			'>=' => [
				'name'=>'Supérieur ou égal',
				'types'=>['integer', 'decimal', 'string'],
			],
			'contains' => [
				'name'=>'Contient',
				'types'=>['picture', 'post_picture', 'integer', 'decimal', 'wprte', 'string', 'cmjn', 'serialize', 'date', 'datetime', 'url', 'checkbox', 'select', 'select_fr', 'contact', 'language', 'country'],
			],
		];
		$current_type = False;
		$export_all = False;
		if (array_key_exists('type', $_REQUEST))
			$current_type = $_REQUEST['type'];
		if (array_key_exists('all', $_REQUEST))
			$export_all = $_REQUEST['all'];
		if ($current_type && $export_all) 
			$this->ccpcm->export->generate($_GET['type']);
		if (array_key_exists('filter_name', $_REQUEST))
			$filter_name = $_REQUEST['filter_name'];
		else
			$filter_name = '';
		if ($filter_name) {
			$path = sprintf('%s/../export_filters/%s/%s/%s.json', 
				dirname(__FILE__),
				$this->ccpcm->edition_slug,
				$current_type, 
				$filter_name
			);
		}
		if ($filter_name && array_key_exists('filter_delete', $_REQUEST) && $current_type) {
			if (file_exists($path)) {
				unlink($path);
				$filter_name = False;
			}
		}
		if (array_key_exists('mode_export_view', $_REQUEST)) {
			$export_data = $this->ccpcm->export->generate_data_by_filter($path);
		} else {
			$export_data = False;
		}
		if (array_key_exists('mode_export_file', $_REQUEST)) {
			$this->ccpcm->export->generate_file_by_filter($path);
		}
		if (array_key_exists('filter_operator_append', $_REQUEST))
			$filter_operator_append = [
				'filter_type' => 'operator',
				'operator' => $_REQUEST['filter_operator_append']
			];
		else
			$filter_operator_append = False;
		if (array_key_exists('mode_field_append', $_REQUEST))
			$filter_field_append = [
				'filter_type' => 'field',
				'field' => $_REQUEST['filter_field_append'],
				'comparator' => $_REQUEST['filter_comparator_append'],
				'value' => $_REQUEST['filter_value_append'],
			];
		else
			$filter_field_append = False;
		if (array_key_exists('fields_sortable', $_REQUEST) && $_REQUEST['fields_sortable'] != '')
			$fields_sortable = $_REQUEST['fields_sortable'];
		else
			$fields_sortable = False;
		if (array_key_exists('selected_fields', $_REQUEST)) {
			$selected_fields = $_REQUEST['selected_fields'];
		} else 
			$selected_fields = False;
		$filters = [];
		$data = [];
		if ($current_type) {
			$path = sprintf('%s/../export_filters/%s/%s', 
				dirname(__FILE__),
				$this->ccpcm->edition_slug,
				$current_type, 
			);
			if ($filter_name) {
				$file = sprintf('%s/%s.%s',
					$path,
					$filter_name,
					'json'
				);
				if (!is_dir($path))
					mkdir($path, 0700, True);
				if (!file_exists($file)) {
					file_put_contents($file, json_encode(['name'=>$filter_name, 'type'=>$current_type, 'filters' => []], True));
				}
				if (file_exists($file)) {
					// Récupération du contenu
					$data = json_decode(file_get_contents($file), True);;
					$changed = False;
					if ($fields_sortable) {
						$data['filters'] = [];
						$fields_sortable = json_decode(base64_decode($fields_sortable), True);
						foreach($fields_sortable as $field_sortable)
							$data['filters'][] = json_decode(base64_decode($field_sortable), True);
						$changed = True;
					}
					if ($filter_field_append) {
						$data['filters'][] = $filter_field_append;
						$changed = True;
					}
					if ($filter_operator_append) {
						$data['filters'][] = $filter_operator_append;
						$changed = True;
					}	
					if ($selected_fields) {
						$data['fields'] = $selected_fields;
						$changed = True;
					}
					if ($changed) {
						file_put_contents($file, json_encode($data, True));
					}
				}
			}
			if (is_dir($path)) {
				foreach(scandir($path) as $file) {
					if (!in_array($file, ['.', '..']) && substr($file, -strlen('.json')) == '.json') {
						$filters[] = substr($file, 0, -strlen('.json'));
					}
				}
			}
		}

		wp_enqueue_style('ccpcm_export', plugin_dir_url(__FILE__).'../css/ccpcm_export.css');
		wp_enqueue_script('ccpcm_export', plugin_dir_url(__FILE__).'../js/ccpcm_export.js', ['jquery-ui-sortable', 'jquery-ui-droppable']);
		echo "<h1>Exporter toutes les données</h1>";
		foreach(array_keys($this->ccpcm->data->meta_keys) as $type)
			printf ('<a class="button button-primary" href="?page=ccpcm-export-data&type=%s&all=1">Export l\'ensemble "%s"</a> ', $type, $type);

		echo "<h1>Exporter avec des filtres</h1>";
		echo "<form id=\"formular\">\n";
		echo "<div class=\"\"><label>Ensemble de données : </label>";
		echo "<input type=\"hidden\" name=\"page\" value=\"ccpcm-export-data\">\n";
		echo "<select name=\"type\" onchange=\"jQuery('#formular').submit();\">\n";
		printf ('<option value="">-- Choisir un type --</option>'."\n", $type, $type);
		foreach(array_keys($this->ccpcm->data->meta_keys) as $type)
			printf ('<option value="%s"%s>%s</option>'."\n", $type, ($current_type == $type)?' selected="selected"':'', $type);
		echo "</select>";
		echo "</div>\n";
		echo "</form>\n";
		if ($current_type) {	
			if ($filters) {
				echo "<form method=\"GET\">\n";
				echo "<input type=\"hidden\" name=\"page\" value=\"ccpcm-export-data\">\n";
				echo "<input type=\"hidden\" name=\"type\" value=\"$current_type\">\n";
				echo "<fieldset>\n";
				echo "<legend>Filtres existants</legend>\n";
				echo "<select name=\"filter_name\">";
				printf ('<option value="">-- Créer un nouveau filtre --</option>'."\n", $type, $type);
				foreach($filters as $filter) {
					printf("<option value=\"$filter\"%s>$filter</option>\n", ($filter_name == $filter)?' selected="selected"':'');;
				}
				echo "</select>";
				echo "<input class=\"button button-primary\" type=\"submit\" value=\"Sélectionner\">";
				echo "<input class=\"button button-secondary\" type=\"submit\" name=\"filter_delete\" value=\"Supprimer\">";
				echo "</fieldset>\n";
				echo "</form>";
			}
			
			if (!$filter_name) {
				echo "<form method=\"GET\">\n";
				echo "<input type=\"hidden\" name=\"page\" value=\"ccpcm-export-data\">\n";
				echo "<input type=\"hidden\" name=\"type\" value=\"$current_type\">\n";
				echo "<fieldset>\n";
				echo "<legend>Créer un filtre</legend>\n";
				echo "<div>";
				echo "<input type=\"text\" placeholder=\"Nom du filtre\" name=\"filter_name\" id=\"filter_name\" value=\"".$filter_name."\"/>";
				echo "<input class=\"button button-secondary\" type=\"submit\" value=\"Sauvegarder\">";
				echo "</div>";
				echo "</fieldset>\n";
				echo "</form>";
			} else {
				$fields = array_keys($this->ccpcm->data->meta_keys[$current_type]);
				$translates = $this->ccpcm->data->get_icl_field_name_translate($fields);
				echo "<form method=\"POST\" id=\"formular_filter\">\n";
				echo "<h2>Filtre : $filter_name</h2>";
				if ($data) {
					if (!array_key_exists('fields', $data)) {
						$data['fields'] = [];
						foreach ($this->ccpcm->data->meta_keys[$current_type] as $field => $meta) {
							$data['fields'][] = $meta['name'];	
						}
					}
					// affichage du filtre courant
					echo "<fieldset id=\"selected_fields_fieldset\">\n";
					echo "<legend>Champs exportés</legend>\n";
					foreach ($this->ccpcm->data->meta_keys[$current_type] as $field => $meta) {
						if (! array_key_exists('type', $meta))
							$meta['type'] = 'string';
						if (is_array($translates) && array_key_exists($field, $translates))
							$label = $translates[$field];
						else
							$label = $this->ccpcm->export->form_get_label($type, $field);
						echo "<span class=\"selected_fields\"><input type=\"checkbox\" name=\"selected_fields[]\" value=\"".$meta['name']."\" id=\"field_".$meta['name']."\"".((in_array($meta['name'], $data['fields']))?"checked=\"checked\"":"")."><label for=\"field_".$meta['name']."\">$label</label></span>";
					}
					echo "<br/><input type=\"submit\" class=\"button button-primary\" value=\"Sauvegarder\"/>";
					echo " <a class=\"button button-secondary\" id=\"selected_fields_all\">Tout cocher</a> <a class=\"button button-secondary\" id=\"selected_fields_none\">Tout décocher</a>";
					echo "</fieldset>\n";
					// affichage du filtre courant
					echo "<fieldset>\n";
					echo "<legend>Filtre en cours</legend>\n";
					echo "<ul id=\"sortable\">\n";
					foreach($data['filters'] as $filter) {
						$data = base64_encode(json_encode($filter, True));
						switch($filter['filter_type']) {
							case 'operator':
								printf("<li class=\"ui-state-default button button-secondary\" data-json=\"$data\">%s</li>", $filter['operator']); 
								break;
							case 'field':
								list($f, $l) = explode('#', $filter['field']);
								printf("<li class=\"ui-state-default button button-secondary\" data-json=\"$data\">%s %s \"%s\"</li>", $l, $filter['comparator'], $filter['value']); 
								break;
						}
					}
					echo "</ul>\n";
					echo "<div id=\"trash\" class=\"ui-widget-header\">\n";
					echo "<i class=\"fa fa-trash\"></i>";
					echo "</div>\n";
					echo "<input type=\"hidden\" id=\"fields_sortable\" name=\"fields_sortable\" value=\"\"/><br/>";
					echo "<input type=\"button\" class=\"button button-primary\" id=\"fields_sortable_button\" value=\"Sauvegarder\"/>";
					echo "</fieldset>\n";
				}
				echo "<fieldset>\n";
				echo "<legend>Ajouter un operateur</legend>\n";
				foreach(['(', ')', 'ET', 'OU'] as $operator) {
					echo "<input class=\"button button-secondary\" type=\"submit\" name=\"filter_operator_append\" value=\"".$operator."\">";
				}
				echo "</fieldset>\n";
				echo "<fieldset>\n";
				echo "<legend>Ajouter un filtre</legend>\n";
				echo "<select id=\"filter_field_append\" name=\"filter_field_append\">";
				echo "<option value=\"\">-- choisir --</option>\n";
				foreach ($this->ccpcm->data->meta_keys[$current_type] as $field => $meta) {
					if (! array_key_exists('type', $meta))
						$meta['type'] = 'string';
					if (array_key_exists($field, $translates))
						$label = $translates[$field];
					else
						$label = $this->ccpcm->export->form_get_label($type, $field);
					echo "<option value=\"".$meta['name']."#".$label."\" data-type=\"".$meta['type']."\">";
					echo "<label>".$label." (".$meta['type'].")</label>\n";
					echo "</option>";
				}
				echo "</select>\n";
				echo "<select name=\"filter_comparator_append\" id=\"filter_comparator_append\">";
				echo "<option value=\"\">-- choisir --</option>\n";
				foreach($comparators as $comparator => $c_data) {
					echo "<option value=\"".$comparator."\" data-selector=\"".base64_encode(json_encode($c_data['types']))."\">".$c_data['name']."</value>";
				}
				echo "</select>";
				echo "<input type=\"text\" name=\"filter_value_append\" placeholder=\"Valeur de comparaison\"/>";
				echo "<input type=\"submit\" class=\"button button-primary\" name=\"mode_field_append\" value=\"Ajouter\"/>\n";
				echo "</fieldset>\n";
				echo "<fieldset>\n";
				echo "<legend>Voir et exporter</legend>\n";
				echo "<input type=\"submit\" class=\"button button-secondary\" name=\"mode_export_view\" value=\"Voir\"/>\n";
				echo "<input type=\"submit\" class=\"button button-primary\" name=\"mode_export_file\" value=\"Exporter\"/>\n";
				echo "</fieldset>\n";
				echo "</form>\n";
			}
			if ($export_data) {
				echo "<table>\n";
				echo "<tr>";
				for($i=0; $i < count($export_data[0]); $i++) {
					echo "<th>".$export_data[0][$i]."</th>";	
				}
				echo "</tr>\n";
				for($j=1; $j < count($export_data); $j++) {
					echo "<tr>";
					for($i=0; $i < count($export_data[$j]); $i++) {
						echo "<td>".$export_data[$j][$i]."</td>";	
					}
					echo "</tr>\n";
				}
			}
		}
	}
}
