<?php

class ccppm_integrator extends ccppm_object {
  public function Projections_Per_Days($inputs = [], $dpi = 150) {
    return $this->ccppm->data->jsondb->get('planning','planning');
  }
  public function Projections_Per_Cinema($inputs = [], $dpi = 150) {
    return $this->ccppm->data->jsondb->get('planning','planning');
  }
  public function Projections_Per_Room($inputs = [], $dpi = 150) {
    return $this->ccppm->data->jsondb->get('planning','planning');
  }
  public function Projections_Per_Film($inputs = [], $dpi = 150) {
    return $this->ccppm->data->jsondb->get('planning','planning');
  }
  public function Accreditation($inputs = [], $dpi = 150) {
    $data = array();
    if (array_key_exists('ids', $inputs))
      $ids = $inputs['ids'];
    else {
      $ids = $this->ccppm->data->jsondb->get_ids('accreditation');
    }
    foreach($ids as $id) {
      $data[] = $this->ccppm->data->jsondb->get('accreditation',$id);
    }
    return $data;
  }
  public function Fiche_accredite($inputs = [], $dpi = 150) {
    $data = array();
    if (array_key_exists('ids', $inputs))
      $ids = $inputs['ids'];
    else {
      $ids = $this->ccppm->data->jsondb->get_ids('accreditation');
    }
    foreach($ids as $id) {
      $data[] = $this->ccppm->data->jsondb->get('accreditation',$id);
    }
    return $data;
  }
  public function Gravityform_winner($inputs = [], $dpi = 150) {
    if (array_key_exists('form_id' ,$inputs)) {
      return $this->ccppm->data->jsondb->get('gravityform_winner', $inputs['form_id']);
    }
    return ['error' => 'pas de données'];
  }
}
