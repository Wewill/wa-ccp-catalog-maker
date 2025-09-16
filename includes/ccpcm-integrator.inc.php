<?php

class ccpcm_integrator extends ccpcm_object {
  # FIFAM
  public function Projections_Per_Days($inputs = [], $dpi = 150) {
    return $this->ccpcm->data->jsondb->get('planning','planning');
  }
  public function Projections_Per_Cinema($inputs = [], $dpi = 150) {
    return $this->ccpcm->data->jsondb->get('planning','planning');
  }
  public function Projections_Per_Room($inputs = [], $dpi = 150) {
    return $this->ccpcm->data->jsondb->get('planning','planning');
  }
  public function Projections_Per_Film($inputs = [], $dpi = 150) {
    return $this->ccpcm->data->jsondb->get('planning','planning');
  }
  public function Accreditation($inputs = [], $dpi = 150) {
    $data = array();
    if (array_key_exists('ids', $inputs))
      $ids = $inputs['ids'];
    else {
      $ids = $this->ccpcm->data->jsondb->get_ids('accreditation');
    }
    foreach($ids as $id) {
      $data[] = $this->ccpcm->data->jsondb->get('accreditation',$id);
    }
    return $data;
  }
  public function Fiche_accredite($inputs = [], $dpi = 150) {
    $data = array();
    if (array_key_exists('ids', $inputs))
      $ids = $inputs['ids'];
    else {
      $ids = $this->ccpcm->data->jsondb->get_ids('accreditation');
    }
    foreach($ids as $id) {
      $data[] = $this->ccpcm->data->jsondb->get('accreditation',$id);
    }
    return $data;
  }
  public function Gravityform_winner($inputs = [], $dpi = 150) {
    if (array_key_exists('form_id' ,$inputs)) {
      return $this->ccpcm->data->jsondb->get('gravityform_winner', $inputs['form_id']);
    }
    return ['error' => 'pas de données'];
  }

  #RSFP
  public function Test_directory($inputs = [], $dpi = 150) {
    if (array_key_exists('form_id' ,$inputs)) {
      $data = $this->ccpcm->data->jsondb->get('directory', $inputs['form_id']);
      return $data;
    }
    return ['error' => 'pas de données'];
  }
}
