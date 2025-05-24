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
                case '2sections':
                $type = 'section';
                $count = 2;
                $ids = $this->ccpcm->data->jsondb->get_ids($type);
    //              $ids = array(221,222);
                break;
                case 'section':
                //$ids = array(231); // Compet LM #40
    //              $ids = array(257); // Compet CM #40
                //$ids = array(230); // Compet #40
                $ids = $this->ccpcm->data->jsondb->get_ids($type);
                break;
                // Wil
                case 'jury':
    //              $ids = array(51969);
                $ids = $this->ccpcm->data->jsondb->get_ids($type);
                break;
                case 'section_and_subsections':
                //$id = 237; // Ouverture cloture #40
                //$id = 233; // Compet CM #40
                $id = 257; // Compet #40
                return $this->ccpcm->catalogues->get_catalogue_data_by_type_and_id(False, $type, [$id], 'name_simplified', [], 1); //182 afrique /193 espagne / 210 UPJV // 192 Competition // 211 MASTERCLASS // 178 Competition CM // 181 Travaie
                break;
                case 'film':
                //$ids = array(52502); // MD > pour test >> Markdown KO
                //$ids = array(52085); // MD Bio > Markdown OK 
                //$ids = array(51616); // MD Bio + punchline > Markdown OK 
                //$ids = array(52611); // justin pour images
                //$ids = array(52400); // Cas sepcial ouvertre
    //              $ids = array(52000); // Film compet Kuessipan
                //$ids = array(50984); // Film pr calage des bordered
                $ids = $this->ccpcm->data->jsondb->get_ids($type);              
                break;
                case 'planning':
                $ids = ['planning'];
                break;
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
    //    $this->ccpcm->data->redefine(150);
    //    if ($type == 'section_and_subsections')
    //      return $this->ccpcm->catalogues->get_catalogue_data_by_type_and_id(False, $type, 210, 'name_simplified', 1); //182 afrique /193 espagne / 210 UPJV // 192 Competition // 211 MASTERCLASS // 178 Competition CM // 181 Travaie
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
            $d = $this->ccpcm->data->jsondb->get($type, $id);
            switch($type) {
                case '2sections':
            case 'section':
                $index = $this->ccpcm->data->jsondb->get_index('film', 'section');
                $films = array();
                if (array_key_exists($id, $index)) {
                $f_ids = $index[$id];
                foreach($f_ids as $f_id) {
                    $film = $this->ccpcm->data->jsondb->get('film', $f_id);
                    $this->ccpcm->custom->append_film_additionnal_fields($film);
                    $films[] = $film;
                }
                }
                $d['films'] = $films;
            unset($films);
                $order = 'order';
            if (array_key_exists('ccppto_film', $d) && array_key_exists('films', $d))
                $d['films'] = $this->ccpcm->custom->apply_post_in_taxonomy_order($d['ccppto_film'], $d['films']);
            else
                $d['films'] = [];
            if (array_key_exists('ccppto_projection', $d) && array_key_exists('projections', $d))
                $d['projections'] = $this->ccpcm->custom->apply_post_in_taxonomy_order($d['ccppto_projection'], $d['projections']);
            else
                $d['projections'] = [];
                $d['films'] = $this->ccpcm->catalogues->get_catalogue_data_order_by($d['films'], $order);
                $d['projections'] = $this->ccpcm->catalogues->get_catalogue_data_order_by($d['projections'], $order);
                if (array_key_exists('featured_film_1', $d) && $d['featured_film_1']) {
                $d['featured_film_1'] = $this->ccpcm->data->jsondb->get('film', $d['featured_film_1']);
                } else {
                $d['featured_film_1'] = False;
                }
                if (array_key_exists('featured_film_2', $d) && $d['featured_film_2']) {
                $d['featured_film_2'] = $this->ccpcm->data->jsondb->get('film', $d['featured_film_2']);
                } else {
                $d['featured_film_2'] = False;
                }
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
    //            $d['partners'] = $partenaire;
                $order = 'order';
                $d['partners'] = $this->ccpcm->catalogues->get_catalogue_data_order_by($partenaire, $order);

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
            case 'film':
                $this->ccpcm->custom->append_film_additionnal_fields($d);
                break;

    /*
                $index = $this->ccpcm->data->jsondb->get_index('projection', 'film');
                $projections = array();
                if (array_key_exists($id, $index)) {
                $p_ids = $index[$id];
                foreach($p_ids as $p_id) {
                    $projection = $this->ccpcm->data->jsondb->get('projection', $p_id);
                    foreach($projection['room'] as $r_idx => $room) {
                    if ($room['parent']) {
                        $projection['room'][$r_idx]['cinema'] = $this->ccpcm->data->jsondb->get('room', $room['parent']);
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
    //    $this->ccpcm->log("get_random_data => ", print_r($data, True));
        if ($count == 1 && count($data) == 1) {
        return $data[0];
        }
        return $data;
    }
}